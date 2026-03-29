from fastapi import FastAPI, HTTPException, BackgroundTasks
from pydantic import BaseModel
from typing import Optional, List, Dict, Any
import uvicorn
import sys
import os
import uuid
import time
import asyncio
import re
import json

# Ensure the current directory is in the path for imports
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

# Import the existing generation logic and data from core/api
try:
    from api import generate_memes
    from core import STYLES, TONES, meme_agent, Runner, config, client
except ImportError:
    from meme_agent.api import generate_memes
    from meme_agent.core import STYLES, TONES, meme_agent, Runner, config, client

app = FastAPI(
    title="Meme Agent API",
    description="FastAPI backend for the Hinglish Meme Generator Agent",
    version="1.3.0"
)

# File path for persistent chat history
HISTORY_FILE = os.path.join(os.path.dirname(os.path.abspath(__file__)), "data", "chat_history.json")

# In-memory storage for jobs (In production, use Redis/DB)
jobs: Dict[str, dict] = {}

class MemeRequest(BaseModel):
    message: str  # Topic or Chat Message
    style: Optional[str] = "relatable"
    tone: Optional[str] = "funny"
    template: Optional[str] = "AUTO"
    user_id: Optional[str] = "default_user"

class MemeResponse(BaseModel):
    job_id: str
    status: str

# -------------------------
# 🧠 Memory Management
# -------------------------
def load_history(user_id: str) -> List[Dict[str, str]]:
    """Load conversation history for a specific user."""
    if not os.path.exists(HISTORY_FILE):
        return []
    
    try:
        with open(HISTORY_FILE, "r", encoding="utf-8") as f:
            data = json.load(f)
            return data.get(user_id, {}).get("messages", [])
    except (json.JSONDecodeError, IOError):
        return []

def save_history(user_id: str, role: str, content: str):
    """Save a message to the conversation history."""
    data = {}
    if os.path.exists(HISTORY_FILE):
        try:
            with open(HISTORY_FILE, "r", encoding="utf-8") as f:
                content_file = f.read().strip()
                if content_file:
                    data = json.loads(content_file)
        except (json.JSONDecodeError, IOError):
            data = {}
    
    if user_id not in data:
        data[user_id] = {"messages": []}
    
    data[user_id]["messages"].append({"role": role, "content": content})
    
    try:
        with open(HISTORY_FILE, "w", encoding="utf-8") as f:
            json.dump(data, f, indent=2, ensure_ascii=False)
    except IOError as e:
        print(f"Error saving history: {e}")

# -------------------------
# 🚦 Endpoints
# -------------------------

@app.get("/")
async def root():
    """Health check endpoint"""
    return {"status": "online", "agent": "meme_agent_chat"}

@app.get("/styles")
async def get_styles():
    return {"styles": STYLES}

@app.get("/tones")
async def get_tones():
    return {"tones": TONES}

@app.get("/history")
async def get_chat_history(user_id: str):
    """Return entire conversation history for a user."""
    return {"history": load_history(user_id)}

@app.post("/chat")
async def chat(request: MemeRequest):
    """
    Main Chat Endpoint with Memory and Feature-gated Meme Generation.
    """
    user_id = request.user_id or "default_user"
    user_msg = request.message
    match = None
    
    # 1. Load History
    history = load_history(user_id)
    
    # 2. Construct Messages for LLM
    # We inject instructions to trigger meme generation via a special token
    start_system_prompt = (
        "You are a Meme Generation Backend Agent. Your objective is to extract meme topics from user input and trigger generation.\n\n"
        "RULES:\n"
        "- Trigger generation using exactly this tag: [GENERATE_MEME: <topic>]\n"
        "- Reply naturally in Hinglish or English (e.g., 'Ye memes dekho!', 'Meme ka scene on hai!').\n"
        "- Keep the reply short and conversational (1 sentence).\n"
        "- STRIKE RULE: Never mention the number of memes (e.g., don't say '3 memes').\n"
        "- STRIKE RULE: Do NOT generate meme captions or numbered lists yourself in the chat response.\n"
        "- If the user just greets you, reply naturally without triggering a meme unless they ask for one.\n"
    )
    
    messages = [{"role": "system", "content": start_system_prompt}]
    
    # Add context (use full history)
    relevant_history = history if history else []
    for msg in relevant_history:
        messages.append({"role": msg["role"], "content": msg["content"]})
        
    messages.append({"role": "user", "content": user_msg})
    
    
    # 3. Get LLM Response
    response_text = ""
    try:
        completion = await client.chat.completions.create(
            model="gpt-4o",
            messages=messages,
            temperature=0.7
        )
        response_text = completion.choices[0].message.content or ""
        
    except Exception as e:
        print(f"LLM Error: {e}")
        return {"success": False, "error": str(e)}

    # Initialize match early to avoid UnboundLocalError
    match = re.search(r"\[GENERATE_MEME:\s*(.*?)\]", response_text)

    # 🛡️ GLOBAL CLEANING: Remove any lines that look like memes (numbered lists or [TXX] tags)
    # from the entire response to prevent hallucinations in NORMAL CHAT mode.
    lines = response_text.split('\n')
    clean_lines = []
    found_hallucination = False
    for line in lines:
        line_strip = line.strip()
        # If line starts with "1. ", "2. " etc or contains "[T" (likely template tag)
        if re.match(r'^\d+\.', line_strip) or "[" in line_strip and "]" in line_strip:
            found_hallucination = True
            continue
        clean_lines.append(line)
    
    response_text = "\n".join(clean_lines).strip()
    
    # If we found hallucinations and NO meme tag, let's force a trigger if it looks like they wanted memes
    if found_hallucination and not match:
        if any(word in user_msg.lower() for word in ["meme", "dikhao", "bana", "laugh", "funny"]):
            # Clean up user_msg to get a better topic
            clean_topic = re.sub(r'(?i)^(give me |i want |show me |make |generate )?memes? (about|on|for)?\s*', '', user_msg).strip()
            if not clean_topic: clean_topic = user_msg[:50]
            
            # Inject the tag ourselves to trigger the actual generation
            response_text += f"\n\n[GENERATE_MEME: {clean_topic[:50]}]"
            # Re-run search
            match = re.search(r"\[GENERATE_MEME:\s*(.*?)\]", response_text)

    # 4. Check for Meme Trigger
    final_response_text = response_text
    meme_data = []
    meme_intent = False
    
    if match:
        meme_intent = True
        meme_topic = match.group(1).strip()
        
        # Remove the tag from the conversational part
        conversational_part = response_text.replace(match.group(0), "").strip()
        
        # Final scrub of conversational part: remove hallucinated lists, debug info, vision analysis
        conversational_part = re.sub(r'(?i)\|?\s*\[Vision Analysis\].*', '', conversational_part)
        conversational_part = re.sub(r'(?i)Template:.*', '', conversational_part)
        conversational_part = " ".join([l.strip() for l in conversational_part.split('\n') if not (re.match(r'^\d+\.', l.strip()) or "[" in l.strip() and "]" in l.strip())])
        
        # Remove any lingering quantity mentions (e.g., "3 memes", "three memes", "here are 3")
        conversational_part = re.sub(r'(?i)\b\d+\s+memes?\b', 'memes', conversational_part)
        conversational_part = re.sub(r'(?i)\b(one|two|three|four|five|six|seven|eight|nine|ten)\s+memes?\b', 'memes', conversational_part)
        conversational_part = re.sub(r'(?i)here (are|is) (\d+|some|your|three|3) memes?\b', '', conversational_part).strip()

        # Ensure conversational part remains clean or empty if it was just fluff
        if not conversational_part or len(conversational_part) < 3:
            conversational_part = ""
            
        final_response_text = conversational_part
            
        try:
            # Generate Meme
            print(f"[SERVER] Calling generate_memes for topic: {meme_topic}")
            meme_output = await generate_memes(
                topic=meme_topic,
                style=request.style or "relatable",
                tone=request.tone or "funny",
                template_choice=request.template or "AUTO"
            )
            print(f"[SERVER] generate_memes output type: {type(meme_output)}, length: {len(meme_output) if meme_output else 0}")
            print(f"[SERVER] First 200 chars: {meme_output[:200] if meme_output else 'None'}")

            if not meme_output:
                meme_output = "[]" 

            # Parse structured JSON from generate_memes if possible
            try:
                parsed_memes = json.loads(meme_output)
                print(f"[SERVER] Parsed JSON: {parsed_memes}")
                if isinstance(parsed_memes, dict) and "memes" in parsed_memes:
                    final_response_text = parsed_memes.get("reply", conversational_part)
                    meme_data = parsed_memes["memes"][:3] # Fail-safe slice
                    print(f"[SERVER] Successfully extracted memes: {len(meme_data)}")
                else:
                    print(f"[SERVER] JSON parsed but not in expected format")
                    final_response_text = conversational_part
                    meme_data = [{"style": "relatable", "caption": m, "template": "auto"} for m in (meme_output or "").splitlines() if m.strip()][:3]
            except Exception as parse_err:
                print(f"[SERVER] JSON parse error: {parse_err}")
                final_response_text = conversational_part
                meme_data = [{"style": "relatable", "caption": m, "template": "auto"} for m in (meme_output or "").splitlines() if m.strip()][:3]

        except Exception as e:
            print(f"Meme Generation Error: {e}")
            import traceback
            traceback.print_exc()
            final_response_text = f"{conversational_part}\n\n(Oops, meme generation failed: {str(e)})"
            
    else:
        # Just chat
        final_response_text = final_response_text.strip()
        meme_intent = False

    # 5. Save Context
    save_history(user_id, "user", user_msg)
    save_history(user_id, "assistant", final_response_text)
    
    return {
        "success": True,
        "reply": final_response_text,
        "memes": meme_data,
        "meme_intent": meme_intent,
        "history": load_history(user_id)
    }

# Keep legacy endpoint for direct tool access if needed
@app.post("/generate-meme")
async def generate_meme_sync(request: MemeRequest):
    try:
        result_text = await generate_memes(
            topic=request.message,
            style=request.style or "relatable",
            tone=request.tone or "funny",
            template_choice=request.template or "AUTO"
        )
        return {"success": True, "response": result_text}
    except Exception as e:
        return {"success": False, "error": str(e)}

# -------------------------
#   🚀 Run
# -------------------------
if __name__ == "__main__":
    uvicorn.run("server:app", host="127.0.0.1", port=8003, reload=True)
