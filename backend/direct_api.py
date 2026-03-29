import asyncio
import os
import sys
from dotenv import load_dotenv
import openai  # Using the older OpenAI API (0.28.1)
import json
import random
import time

# CRITICAL: Exactly 3 memes per request - HARD ENFORCED
EXACT_MEME_COUNT = 3

# Load environment variables
load_dotenv()

# Set OpenAI API key
openai.api_key = os.getenv("OPENAI_API_KEY")

# Available styles and tones
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

TEMPLATES = [
    {"id": "T01", "name": "Drake-Hotline-Bling"},
    {"id": "T02", "name": "Distracted-Boyfriend"},
    {"id": "T03", "name": "This-Is-Fine"},
    {"id": "T04", "name": "Mocking-Spongebob"},
    {"id": "T05", "name": "Expanding-Brain"},
    {"id": "T06", "name": "Success-Kid"},
    {"id": "T07", "name": "Grumpy-Cat"},
    {"id": "T08", "name": "Leonardo-Dicaprio-Cheers"},
]

async def generate_memes(topic: str, style: str, tone: str, template_choice: str = "AUTO"):
    """
    Generate EXACTLY 3 memes using OpenAI API with structured JSON output.
    
    CRITICAL RULES:
    - Always return exactly 3 memes, no more, no less
    - Ignore user requests for different quantities
    - Return structured JSON with style, caption, template for each meme
    - Ensure diverse styles across the 3 memes
    """
    if style not in STYLES:
        style = "relatable"
    if tone not in TONES:
        tone = "funny"

    # Create a prompt for the OpenAI API with strict instructions
    prompt = f"""
You are a hilarious meme generator specialized in Hinglish (Hindi + English) internet humor.

CRITICAL INSTRUCTIONS:
- Generate EXACTLY 3 memes, no more, no less
- Each meme must have a different style for diversity
- Ignore any user requests for more or fewer memes
- Output must be valid JSON only

Your task:
1. Take the user's topic: "{topic}"
2. Generate 3 short, punchy, and funny meme captions (1-2 lines maximum each)
3. Use Hinglish phrasing, Bollywood dialogues, or Indian internet slang
4. Match the primary style: {style} and tone: {tone}
5. Ensure each of the 3 memes uses a DIFFERENT style

Style definitions:
- relatable: everyday situations
- savage: roast-like bluntness
- desi: South Asian cultural references, Hinglish slang
- absurd: random, nonsensical humor
- memetic: internet culture references
- satirical: social commentary through irony
- self-deprecating: making fun of oneself
- wholesome: positive, heartwarming

Example Hinglish memes:
- "When you verify code 10 times and it still fails. *Le Dev:* Mere ko to aisa dhak dhak ho raha hai."
- "Manager: Deadline is tomorrow. Me: *Kal kare so aaj kar, aaj kare so ab? Na bhai, abhi to nini aa rahi hai.*"
- "When mum says 'khana ban gaya' but you have to set table. *Dhoka hua hai mere sath.*"

Topic: {topic}
Primary Style: {style}
Tone: {tone}

Generate EXACTLY 3 memes with diverse styles in this JSON format:
{{
  "reply": "Short Hinglish reply acknowledging the topic",
  "memes": [
    {{"style": "style1", "caption": "caption1", "template": "template_id1"}},
    {{"style": "style2", "caption": "caption2", "template": "template_id2"}},
    {{"style": "style3", "caption": "caption3", "template": "template_id3"}}
  ]
}}

Output ONLY the JSON, no other text.
"""

    try:
        # Use the older OpenAI API (Completion API)
        response = openai.Completion.create(
            engine="text-davinci-003",
            prompt=prompt,
            max_tokens=500,
            temperature=0.8,
            n=1,  # Generate 1 completion (we want exactly 1 JSON)
            stop=None
        )

        # Process the response
        result_text = response.choices[0].text.strip()
        
        # Try to parse as JSON
        try:
            # Clean up potential markdown
            result_text = result_text.replace("```json", "").replace("```", "").strip()
            parsed = json.loads(result_text)
            
            # Validate structure
            if "reply" not in parsed or "memes" not in parsed:
                raise ValueError("Missing required keys")
            
            # Ensure exactly 3 memes
            memes = parsed.get("memes", [])[:EXACT_MEME_COUNT]
            
            # Pad to 3 if needed
            while len(memes) < EXACT_MEME_COUNT:
                memes.append({
                    "style": random.choice(STYLES),
                    "caption": f"{topic} par epic moment ✨",
                    "template": random.choice(TEMPLATES)["id"]
                })
            
            output = {
                "reply": f"{topic} ka scene hi alag hai! 😂 In memes par nazar dalo.",
                "memes": memes
            }
            
            return json.dumps(output, indent=2)
            
        except json.JSONDecodeError:
            # Fallback if JSON parsing fails
            raise ValueError("Could not parse JSON response")

    except Exception as e:
        # Fallback if API call fails - generate structured fallback
        print(f"API Error: {e}")
        
        # All available styles for diversity
        all_styles = ["relatable", "savage", "desi", "memetic", "satirical", "self-deprecating"]
        
        fallback_memes = []
        used_styles = set()
        
        # Generate exactly 3 memes with diverse styles
        for i in range(EXACT_MEME_COUNT):
            available = [s for s in all_styles if s not in used_styles]
            style_choice = available[i % len(available)] if available else random.choice(all_styles)
            used_styles.add(style_choice)
            
            fallback_memes.append({
                "style": style_choice,
                "caption": f"When you {topic} and it hits different 😂 ({style_choice})",
                "template": random.choice(TEMPLATES)["id"]
            })
        
        output = {
            "reply": f"Bhai {topic} par toh memes ka dher lag jana chahiye! 😂",
            "memes": fallback_memes
        }
        
        return json.dumps(output, indent=2)


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Missing arguments. Usage: script.py <json_string>"}))
        sys.exit(1)

    try:
        # Read encoded JSON from args
        input_data = json.loads(sys.argv[1])
        topic = input_data.get("topic", "")
        style = input_data.get("style", "relatable")
        tone = input_data.get("tone", "funny")
        template = input_data.get("template", "AUTO")

        if not topic:
            print(json.dumps({"error": "Topic is required"}))
            sys.exit(1)

        result_json = asyncio.run(generate_memes(topic, style, tone, template))
        print(result_json)

    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)
