# Meme Agent Backend

A FastAPI backend for the Hinglish Meme Generator Agent.

## Features

- RESTful API endpoints for meme generation
- Support for different styles and tones
- Template selection for various meme formats
- Web search integration for trending content
- CORS enabled for web applications

## Endpoints

### GET /
Health check endpoint

### GET /styles
Returns available meme styles

### GET /tones  
Returns available meme tones

### POST /chat
Generate memes based on a topic with style and tone preferences

**Request Body:**
```json
{
  "message": "your topic here",
  "style": "relatable",
  "tone": "funny", 
  "template": "AUTO"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Memes generated successfully",
  "data": {
    "input_topic": "your topic here",
    "style": "relatable",
    "tone": "funny",
    "template": "AUTO",
    "raw_output": "raw agent output",
    "memes": ["parsed meme 1", "parsed meme 2"]
  }
}
```

### POST /generate
Alternative endpoint for meme generation (same as /chat)

## Setup

1. Install dependencies:
```bash
pip install -r backend/requirements.txt
```

2. Set up environment variables:
Create a `.env` file in the root directory with your OpenAI API key:
```
OPENAI_API_KEY=your_openai_api_key_here
```

3. Run the server:
```bash
python backend/server.py --reload
```

Or using uvicorn directly:
```bash
cd backend
uvicorn main:app --reload
```

## Docker

Build and run with Docker:
```bash
docker build -t meme-agent-backend .
docker run -p 8000:8000 meme-agent-backend
```

## Environment Variables

- `OPENAI_API_KEY`: Your OpenAI API key for the agent to work