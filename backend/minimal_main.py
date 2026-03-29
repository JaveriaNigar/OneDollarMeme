from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional, List
import subprocess
import sys
import os
import json

# CRITICAL: Exactly 3 memes per request - HARD ENFORCED
EXACT_MEME_COUNT = 3

app = FastAPI(
    title="Meme Agent API",
    description="FastAPI backend for the Hinglish Meme Generator Agent",
    version="1.0.0"
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Define the same constants as in the original agent
STYLES = [
    "relatable",
    "savage",
    "wholesome",
    "desi",
    "absurd",
    "observational",
    "memetic",
    "satirical",
    "self-deprecating",
]

TONES = [
    "sarcastic",
    "funny",
    "dark",
    "wholesome",
    "cringe",
    "excited",
    "frustrated",
    "mocking",
]

class MessageRequest(BaseModel):
    message: str
    style: Optional[str] = "relatable"
    tone: Optional[str] = "funny"
    template: Optional[str] = "AUTO"

class MemeResponse(BaseModel):
    status: str
    message: str
    data: Optional[dict] = None

def call_meme_agent_api(topic: str, style: str, tone: str, template_choice: str):
    """
    Call the MAIN meme_agent/api.py instead of simple_api.py.
    This ensures we get properly structured JSON with exactly 3 memes.
    """
    # Prepare the input data
    input_data = {
        "topic": topic,
        "style": style,
        "tone": tone,
        "template": template_choice
    }

    # Convert to JSON string
    json_input = json.dumps(input_data)

    # CRITICAL: Use the MAIN api.py instead of simple_api.py
    script_path = os.path.join(os.path.dirname(os.path.dirname(__file__)), 'meme_agent', 'api.py')

    try:
        # Execute the api.py script with the JSON input
        result = subprocess.run([
            sys.executable, script_path, json_input
        ], capture_output=True, text=True, timeout=60)

        if result.returncode != 0:
            try:
                return json.loads(result.stdout)
            except:
                raise Exception(f"Agent execution failed: {result.stderr}")

        output = json.loads(result.stdout)
        return output
        
    except subprocess.TimeoutExpired:
        raise Exception("Agent execution timed out")
    except json.JSONDecodeError as e:
        raise Exception(f"Invalid JSON response from agent: {e}")
    except Exception as e:
        raise e

@app.get("/")
async def root():
    """Health check endpoint"""
    return {
        "status": "online",
        "agent": "meme_agent",
        "description": "Hinglish Meme Generator API - Exactly 3 memes per request"
    }

@app.get("/styles")
async def get_available_styles():
    return {"styles": STYLES}

@app.get("/tones")
async def get_available_tones():
    return {"tones": TONES}

@app.post("/chat", response_model=MemeResponse)
async def chat_with_agent(request: MessageRequest):
    """
    Send a message to the meme agent and receive a response.
    CRITICAL: Always returns exactly 3 memes in structured JSON format.
    """
    try:
        # 1. Detect Intent
        meme_intent = any(k in request.message.lower() for k in ["meme", "funny", "joke", "lol", "hasao"]) or \
                      any(k in request.message.lower() for k in ["exam", "wifi", "sibling", "coding"])
        
        if not meme_intent:
            return {
                "status": "success",
                "message": "Normal chat response",
                "data": {
                    "reply": "Hey! 👋 Kese ho? Main Meme Agent hoon, topic batao!",
                    "memes": [],
                    "meme_intent": False
                }
            }

        # 2. Call the MAIN meme agent API
        result = call_meme_agent_api(
            topic=request.message,
            style=request.style,
            tone=request.tone,
            template_choice=request.template
        )

        if result.get("error"):
            raise HTTPException(status_code=500, detail=result["error"])

        if isinstance(result, dict) and "memes" in result:
            memes = result["memes"][:EXACT_MEME_COUNT]
            
            # Ensure reply is topic-aware
            reply = result.get("reply", "")
            if not reply or "meme" in reply.lower():
                reply = f"Bhai {request.message} ke charche toh har jagah hain! 😂 Ye memes dekho."

            return {
                "status": "success",
                "message": "Memes generated successfully (exactly 3)",
                "data": {
                    "input_topic": request.message,
                    "style": request.style,
                    "tone": request.tone,
                    "template": request.template,
                    "raw_output": result.get("raw_output", ""),
                    "reply": reply,
                    "memes": memes,
                    "meme_intent": True
                }
            }
        else:
            raw_output = result.get("data", {}).get("raw_output", "")
            memes = result.get("data", {}).get("memes", [])
            memes = memes[:EXACT_MEME_COUNT]
            
            return {
                "status": "success",
                "message": "Memes generated successfully (exactly 3)",
                "data": {
                    "input_topic": request.message,
                    "style": request.style,
                    "tone": request.tone,
                    "template": request.template,
                    "raw_output": raw_output,
                    "reply": f"{request.message} ki tension khatam! 😂",
                    "memes": memes,
                    "meme_intent": True
                }
            }
            
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error generating memes: {str(e)}")

@app.post("/generate", response_model=MemeResponse)
async def generate_memes_endpoint(request: MessageRequest):
    """
    Alternative endpoint to generate memes.
    CRITICAL: Always returns exactly 3 memes.
    """
    try:
        result = call_meme_agent_api(
            topic=request.message,
            style=request.style,
            tone=request.tone,
            template_choice=request.template
        )

        if result.get("error"):
            raise HTTPException(status_code=500, detail=result["error"])

        if isinstance(result, dict) and "memes" in result:
            memes = result["memes"][:EXACT_MEME_COUNT]
            
            return {
                "status": "success",
                "message": "Memes generated successfully (exactly 3)",
                "data": {
                    "input_topic": request.message,
                    "style": request.style,
                    "tone": request.tone,
                    "template": request.template,
                    "raw_output": result.get("raw_output", ""),
                    "reply": result.get("reply", ""),
                    "memes": memes
                }
            }
        else:
            raw_output = result.get("data", {}).get("raw_output", "")
            memes = result.get("data", {}).get("memes", [])
            memes = memes[:EXACT_MEME_COUNT]
            
            return {
                "status": "success",
                "message": "Memes generated successfully (exactly 3)",
                "data": {
                    "input_topic": request.message,
                    "style": request.style,
                    "tone": request.tone,
                    "template": request.template,
                    "raw_output": raw_output,
                    "memes": memes
                }
            }
            
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error generating memes: {str(e)}")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
