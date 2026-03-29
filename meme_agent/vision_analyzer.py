
import asyncio
from core import client

async def analyze_image(image_url: str) -> dict:
    """
    Analyze an image using OpenAI's Vision model.
    Returns a dictionary with structured analysis:
    - template_name
    - extracted_text
    - emotion
    - short_explanation
    """
    try:
        response = await client.chat.completions.create(
            model="gpt-4o",  # Or compatible vision model
            messages=[
                {
                    "role": "system",
                    "content": (
                        "You are a meme expert and vision analyst. Your task is to analyze the provided meme image."
                        "Return a valid JSON object with the following keys:\n"
                        "- template_name: The name of the meme template (e.g., 'Drake Hotline Bling', 'Distracted Boyfriend'). If unknown, describe the visual layout briefly.\n"
                        "- extracted_text: Any text visible inside the image. If none, return empty string.\n"
                        "- emotion: The primary emotion or reaction conveyed (e.g., 'Sarcasm', 'Joy', 'Frustration').\n"
                        "- short_explanation: A very brief (1 sentence) explanation of why this specific meme usage is funny or what it means.\n"
                        "Do not include markdown formatting (like ```json), just the raw JSON."
                    )
                },
                {
                    "role": "user",
                    "content": [
                        {"type": "text", "text": "Analyze this meme image."},
                        {
                            "type": "image_url",
                            "image_url": {
                                "url": image_url,
                            },
                        },
                    ],
                }
            ],
            max_tokens=300,
        )
        
        content = response.choices[0].message.content.strip()
        
        # Clean up code blocks if present
        if content.startswith("```json"):
            content = content[7:]
        if content.endswith("```"):
            content = content[:-3]
            
        import json
        return json.loads(content.strip())
        
    except Exception as e:
        print(f"Vision Analysis Error for {image_url}: {e}")
        return {}
