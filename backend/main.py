import sys
import os
import json
import uuid
import datetime
import asyncio
from typing import Optional, List, Dict
from fastapi import FastAPI, HTTPException, BackgroundTasks, Request
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from sqlalchemy import create_engine, Column, Integer, String, Text, DateTime, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, Session

# 🛠️ Path setup: Ensure meme_agent is importable
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.append(os.path.join(BASE_DIR, 'meme_agent'))

try:
    from api import generate_memes as core_generate_memes
except ImportError as e:
    print(f"CRITICAL: Could not import meme_agent. Check path: {e}")
    # Fallback to avoid complete crash during startup
    async def core_generate_memes(*args, **kwargs):
        return "1. [ERROR] Agent not initialized properly."

# 📚 Database Setup (SQLAlchemy)
DB_PATH = os.path.join(BASE_DIR, 'database', 'database.sqlite')
DATABASE_URL = f"sqlite:///{DB_PATH}"

engine = create_engine(DATABASE_URL, connect_args={"check_same_thread": False})
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

class MemeHistory(Base):
    __tablename__ = "meme_agent_history"
    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(String, index=True)
    topic = Column(String)
    style = Column(String)
    tone = Column(String)
    template = Column(String)
    memes_json = Column(Text)  # JSON-encoded list
    timestamp = Column(DateTime, default=datetime.datetime.utcnow)

# Ensure tables exist (In production, use Migrations/Alembic)
try:
    Base.metadata.create_all(bind=engine)
except Exception as e:
    print(f"Database error: {e}")

# 🔥 FastAPI App
app = FastAPI(
    title="Meme Agent High-Traffic API",
    description="Scalable backend with background tasks and polling",
    version="2.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# 🧠 Models
class MessageRequest(BaseModel):
    message: str
    style: Optional[str] = "relatable"
    tone: Optional[str] = "funny"
    template: Optional[str] = "AUTO"
    user_id: Optional[str] = "anonymous"

class JobStatus(BaseModel):
    job_id: str
    status: str
    data: Optional[dict] = None
    error: Optional[str] = None

# 🚄 Global State / Job Store
# For production Horizontal Scaling, move this to REDIS
jobs: Dict[str, JobStatus] = {}

# ⚡ Meme Cache (Topic -> Memes)
# In production, use Redis SETEX
meme_cache: Dict[str, Dict] = {}

# ⚡ Simple Cache for styles/tones
config_cache = {
    "styles": ["relatable", "savage", "wholesome", "desi", "absurd", "observational"],
    "tones": ["sarcastic", "funny", "dark", "wholesome", "cringe"],
}

async def detect_meme_intent(message: str) -> bool:
    """
    Detect if the user wants memes or is just chatting.
    Used to differentiate between normal chat and meme generation.
    """
    meme_keywords = ["meme", "funny", "joke", "lol", "hasao", "comedy", "laugh", "generate", "nikalo"]
    # List of classic meme topics that should trigger even without 'meme' keyword
    topic_triggers = ["exam", "wifi", "sibling", "coding", "salary", "boss", "monday", "desi parents"]
    
    msg_lower = message.lower()
    if any(k in msg_lower for k in meme_keywords):
        return True
    if any(k in msg_lower for k in topic_triggers):
        return True
    
    # Greetings and status checks should NOT trigger memes
    greetings = ["hi", "helo", "hello", "hey", "assalam", "salam", "how are you", "kya haal"]
    if any(msg_lower == g or msg_lower.startswith(g + " ") for g in greetings):
        return False
        
    return False

# --- Background Task Logic ---

async def run_meme_generation_task(job_id: str, request: MessageRequest):
    try:
        jobs[job_id].status = "processing"
        
        # 🧊 Check Cache first
        cache_key = f"{request.message}_{request.style}_{request.tone}"
        if cache_key in meme_cache:
            cache_data = meme_cache[cache_key]
            # Check if cache is fresh (e.g., < 1 hour)
            if (datetime.datetime.now() - cache_data["time"]).total_seconds() < 3600:
                metrics["cache_hits"] += 1
                metrics["completed_jobs"] += 1
                jobs[job_id].status = "completed"
                jobs[job_id].data = cache_data["data"]
                return

        # 🤖 CALL REAL AGENT DIRECTLY
        print(f"[{job_id}] Starting generation for: {request.message}")
        try:
            raw_output = await asyncio.wait_for(
                core_generate_memes(
                    topic=request.message,
                    style=request.style,
                    tone=request.tone,
                    template_choice=request.template
                ),
                timeout=120.0  # 120s timeout to prevent hanging forever
            )
        except asyncio.TimeoutError:
            print(f"[{job_id}] Task timed out after 120s")
            jobs[job_id].status = "failed"
            jobs[job_id].error = "LLM Generation timed out. The agent might be overloaded."
            metrics["failed_jobs"] += 1
            return

        # Parse output
        print(f"[{job_id}] Parsing raw output...")

        # CRITICAL: Check if output is already structured JSON
        try:
            raw_output_clean = raw_output.replace("```json", "").replace("```", "").strip()
            parsed_json = json.loads(raw_output_clean)

            # If it has "memes" key, use structured format
            if "memes" in parsed_json:
                memes_list = parsed_json["memes"]
                # CRITICAL: Hard limit - exactly 3 memes
                parsed_memes = memes_list[:3]

                # Ensure reply is included and topic-aware
                reply = parsed_json.get("reply", "")
                if not reply or "meme" in reply.lower():
                    reply = f"Bhai {request.message} ka scene alag hi hai! 😂 Ye 3 memes check karo."

                result_data = {
                    "input_topic": request.message,
                    "style": request.style,
                    "tone": request.tone,
                    "template": request.template,
                    "raw_output": raw_output,
                    "reply": reply,
                    "memes": parsed_memes,  # EXACTLY 3
                    "meme_intent": True
                }
            else:
                raise ValueError("Not structured JSON")

        except (json.JSONDecodeError, ValueError):
            # Fallback: Parse line-by-line format
            lines = [l.strip() for l in raw_output.splitlines() if l.strip()]
            parsed_memes = []
            for line in lines:
                if "]" in line:
                    parts = line.split("]", 1)
                    parsed_memes.append(parts[1].strip())
                else:
                    import re
                    clean = re.sub(r'^\d+\.\s*', '', line).strip()
                    if clean:
                        parsed_memes.append(clean)

            # CRITICAL: Hard limit - exactly 3 memes
            parsed_memes = parsed_memes[:3]

            # Fallback for parsed_memes if not already objects
            final_memes = []
            for m in parsed_memes:
                final_memes.append({
                    "style": "relatable",
                    "caption": str(m).replace(" - fallback", ""),
                    "template": "auto"
                })

            result_data = {
                "input_topic": request.message,
                "style": request.style,
                "tone": request.tone,
                "template": request.template,
                "raw_output": raw_output,
                "reply": f"{request.message} ki tension khatam! 😂 Ye rahe 3 memes.",
                "memes": final_memes,  # EXACTLY 3
                "meme_intent": True
            }

        # 🧊 Update Cache
        meme_cache[cache_key] = {
            "time": datetime.datetime.now(),
            "data": result_data
        }

        # Save to DB if user_id is provided
        if request.user_id and request.user_id != "anonymous":
            db = SessionLocal()
            print(f"[{job_id}] Saving to history for user: {request.user_id}")
            try:
                new_entry = MemeHistory(
                    user_id=request.user_id,
                    topic=request.message,
                    style=request.style,
                    tone=request.tone,
                    template=request.template,
                    memes_json=json.dumps(parsed_memes)
                )
                db.add(new_entry)
                db.commit()
            except Exception as dbe:
                print(f"DB Error while saving history: {dbe}")
            finally:
                db.close()

        jobs[job_id].status = "completed"
        jobs[job_id].data = result_data
        metrics["completed_jobs"] += 1
        print(f"[{job_id}] Task completed successfully")

    except Exception as e:
        jobs[job_id].status = "failed"
        jobs[job_id].error = str(e)
        metrics["failed_jobs"] += 1
        print(f"Task Failed: {e}")


# --- Monitoring & Metrics ---
# For production, use prometheus_fastapi_instrumentator
metrics = {
    "total_jobs": 0,
    "completed_jobs": 0,
    "failed_jobs": 0,
    "cache_hits": 0
}

@app.get("/metrics")
async def get_metrics():
    return metrics

# --- Rate Limiting (Per User) ---
# Simple in-memory rate limiter. Use Redis for Horizontal Scaling.
user_rate_limits: Dict[str, List[datetime.datetime]] = {}

def check_rate_limit(user_id: str, limit: int = 5, window_seconds: int = 60):
    now = datetime.datetime.now()
    if user_id not in user_rate_limits:
        user_rate_limits[user_id] = []
    
    # Clean old records
    user_rate_limits[user_id] = [t for t in user_rate_limits[user_id] if (now - t).total_seconds() < window_seconds]
    
    if len(user_rate_limits[user_id]) >= limit:
        return False
    
    user_rate_limits[user_id].append(now)
    return True

# --- API Endpoints ---

@app.get("/")
async def root():
    return {"status": "online", "mode": "high-traffic-async", "metrics_ready": True}

@app.post("/meme-agent", response_model=JobStatus)
async def start_generation(request: MessageRequest, background_tasks: BackgroundTasks):
    # Apply Rate Limit
    if not check_rate_limit(request.user_id):
        raise HTTPException(status_code=429, detail="Rate limit exceeded. Please wait a minute.")

    job_id = str(uuid.uuid4())
    jobs[job_id] = JobStatus(job_id=job_id, status="queued")
    
    metrics["total_jobs"] += 1
    
    # 🏃 Start background task
    background_tasks.add_task(run_meme_generation_task, job_id, request)
    
    return jobs[job_id]


@app.get("/meme-agent/status/{job_id}", response_model=JobStatus)
async def check_status(job_id: str):
    if job_id not in jobs:
        print(f"Status check for unknown job: {job_id}")
        raise HTTPException(status_code=404, detail="Job not found")
    return jobs[job_id]

@app.post("/meme-agent/clear")
async def clear_state():
    jobs.clear()
    meme_cache.clear()
    metrics["total_jobs"] = 0
    metrics["completed_jobs"] = 0
    metrics["failed_jobs"] = 0
    metrics["cache_hits"] = 0
    return {"status": "success", "message": "State cleared"}

@app.get("/meme-agent/history/{user_id}")
async def get_user_history(user_id: str):
    db = SessionLocal()
    try:
        history = db.query(MemeHistory).filter(MemeHistory.user_id == user_id).order_by(MemeHistory.timestamp.desc()).limit(20).all()
        return {
            "status": "success",
            "history": [
                {
                    "topic": h.topic,
                    "style": h.style,
                    "tone": h.tone,
                    "template": h.template,
                    "memes": json.loads(h.memes_json),
                    "timestamp": h.timestamp.isoformat()
                } for h in history
            ]
        }
    finally:
        db.close()

# Backward compatibility for the old /chat endpoint
@app.post("/chat")
async def chat_compat(request: MessageRequest):
    # 1. Detect Intent
    is_meme_request = await detect_meme_intent(request.message)
    
    if not is_meme_request:
        # Normal conversation flow
        # Simple heuristic for a friendly reply
        reply = "Hey! 👋 Kese ho? Kuch mazedaar memes dekhne hain toh batao!"
        if "helo" in request.message.lower() or "hi" in request.message.lower():
            reply = "Hello! 👋 Kese ho? Topic batao, main memes bana dunga!"
        
        return {
            "reply": reply,
            "memes": [],
            "meme_intent": False
        }

    # 2. Meme Generation Flow
    raw_output = await core_generate_memes(
        topic=request.message,
        style=request.style,
        tone=request.tone,
        template_choice=request.template
    )

    # CRITICAL: Check if output is already structured JSON with reply+memes
    try:
        raw_output_clean = raw_output.replace("```json", "").replace("```", "").strip()
        parsed_json = json.loads(raw_output_clean)

        if "memes" in parsed_json:
            # CRITICAL: Hard limit - exactly 3 memes
            parsed_memes = parsed_json["memes"][:3]
            
            # Ensure each meme has required fields
            final_memes = []
            for m in parsed_memes:
                if isinstance(m, dict):
                    caption = m.get("caption", "")
                    # Clean up any leftover ' - fallback' strings if they appear
                    caption = caption.replace(" - fallback", "").strip()
                    
                    final_memes.append({
                        "style": m.get("style", "relatable"),
                        "caption": caption,
                        "template": m.get("template", "auto")
                    })
                else:
                    final_memes.append({
                        "style": "relatable",
                        "caption": str(m).replace(" - fallback", "").strip(),
                        "template": "auto"
                    })
            
            # Pad to exactly 3 if needed with topic-aware captions
            while len(final_memes) < 3:
                final_memes.append({
                    "style": "desi",
                    "caption": f"When {request.message} hits different! 😂",
                    "template": "auto"
                })
            
            # Build response with reply and memes at top level
            response_data = {
                "reply": parsed_json.get("reply", f"Bhai {request.message} ke liye ye 3 memes hazir hain! 😂"),
                "memes": final_memes,
                "meme_intent": True
            }
            
            return response_data
        else:
            raise ValueError("Not structured JSON - missing 'memes' key")

    except (json.JSONDecodeError, ValueError):
        # Fallback: Parse line-by-line format
        lines = [l.strip() for l in raw_output.splitlines() if l.strip()]
        parsed_memes = []
        for line in lines:
            if "]" in line:
                parts = line.split("]", 1)
                parsed_memes.append(parts[1].strip())
            else:
                parsed_memes.append(line)

        # CRITICAL: Hard limit - exactly 3 memes
        parsed_memes = parsed_memes[:3]
        
        final_memes = []
        for i, m in enumerate(parsed_memes):
            final_memes.append({
                "style": ["relatable", "savage", "desi"][i % 3],
                "caption": str(m).replace(" - fallback", "").strip(),
                "template": "auto"
            })

        # Pad to exactly 3 if needed
        while len(final_memes) < 3:
            final_memes.append({
                "style": "desi",
                "caption": f"{request.message} ka scene hi kuch aisa hai! 😆",
                "template": "auto"
            })

        response_data = {
            "reply": f"{request.message} ki tension khatam! 😂 Ye rahe 3 memes.",
            "memes": final_memes,
            "meme_intent": True
        }
        
        return response_data

@app.get("/styles")
async def get_styles():
    return {"styles": config_cache["styles"]}

@app.get("/tones")
async def get_tones():
    return {"tones": config_cache["tones"]}

if __name__ == "__main__":
    import uvicorn
    # Use multiple workers if on Linux/Unix for horizontal scaling
    # On Windows, uvicorn only supports 1 worker for loop="asyncio"
    uvicorn.run(app, host="127.0.0.1", port=8003, timeout_keep_alive=300)