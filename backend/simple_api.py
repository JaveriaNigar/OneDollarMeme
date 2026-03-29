import asyncio
import os
import sys
from dotenv import load_dotenv
import json
import random

# CRITICAL: Exactly 3 memes per request - HARD ENFORCED
EXACT_MEME_COUNT = 3

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

# Template mappings for structured output
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

def generate_memes(topic: str, style: str, tone: str, template_choice: str = "AUTO"):
    """
    Generate EXACTLY 3 memes with structured JSON output.
    
    CRITICAL RULES:
    - Always return exactly 3 memes, no more, no less
    - Ignore user requests for different quantities
    - Return structured JSON with style, caption, template for each meme
    - Ensure diverse styles across the 3 memes
    """
    # Validate style and tone
    if style not in STYLES:
        style = "relatable"
    if tone not in TONES:
        tone = "funny"

    # Meme templates for different styles (Hinglish)
    meme_templates = {
        "relatable": [
            f"When you {topic} and it hits different 😂",
            f"Me trying to {topic}: *insert chaos* 😵‍💫",
            f"{topic} really said: *insert unexpected outcome* 🤔",
            f"My relationship with {topic} be like... 💔",
            f"POV: You thought {topic} would be easy 😅"
        ],
        "savage": [
            f"{topic}? More like {topic} but make it tragic 🗿",
            f"They said {topic} would be fun... they lied 😒",
            f"{topic} really said: *chef's kiss* 👨‍🍳",
            f"If {topic} was a person, I wouldn't trust them 🤭",
            f"{topic} be hitting different today 📈"
        ],
        "wholesome": [
            f"Sometimes {topic} brings joy to the world 🌟",
            f"Enjoying {topic} in peace and harmony 🕊️",
            f"The beauty of {topic} never gets old 💖",
            f"Good vibes only with {topic} ✨",
            f"{topic} makes me feel warm and fuzzy 🥰"
        ],
        "desi": [
            f"{topic} par ek desi nuskha: *insert wisdom* 🧠",
            f"Uncle ji on {topic}: *insert life advice* 👴",
            f"{topic} mein jo hai woh sirf humare yahan 😄",
            f"Mom's take on {topic}: *insert truth bombs* 👩‍👧",
            f"{topic} aur uska rishta hamare culture se 🇮🇳"
        ],
        "absurd": [
            f"If {topic} was a conspiracy theory... 🕵️",
            f"{topic} but in alternate universe 🪐",
            f"Scientists baffled by {topic} phenomenon 🧪",
            f"{topic}: The untold story 📚",
            f"What if {topic} was actually... 🤯"
        ],
        "observational": [
            f"Have you noticed how {topic} always...? 🤔",
            f"The science behind {topic} is fascinating 🔬",
            f"Statistically speaking, {topic} occurs when... 📊",
            f"Experts say {topic} is linked to... 🎓",
            f"Studies show that {topic} correlates with... 📈"
        ],
        "memetic": [
            f"{topic} but make it internet culture 🌐",
            f"When {topic} becomes a vibe check ✅",
            f"{topic} energy: 100% based 💯",
            f"The {topic} lore is getting crazy 📖",
            f"{topic} just dropped and the internet is shook 😱"
        ],
        "satirical": [
            f"Breaking: {topic} shocks everyone, experts confused 📰",
            f"Society on {topic}: *collective confusion* 🤷",
            f"The irony of {topic} is not lost on us 🎭",
            f"{topic}: A masterclass in contradictions 📚",
            f"Capitalism really said {topic} and we all agreed 💰"
        ],
        "self-deprecating": [
            f"Me with {topic}: *existential crisis* 😭",
            f"My {topic} era is not going well 📉",
            f"I said I'd stop {topic} but here we are 🤡",
            f"{topic} is my whole personality at this point 🎪",
            f"Therapist: {topic} is not a personality. Me: 🤨"
        ]
    }

    # Get templates for the requested style
    templates = meme_templates.get(style, meme_templates["relatable"])
    
    # All available styles for diversity
    all_styles = ["relatable", "savage", "desi", "memetic", "satirical", "self-deprecating", "wholesome", "absurd"]

    # CRITICAL: Generate EXACTLY 3 memes with diverse styles
    memes = []
    used_styles = {style}  # Start with the requested style
    
    # First meme uses the requested style
    template1 = random.choice(templates)
    template_id1 = random.choice(TEMPLATES)["id"]
    memes.append({
        "style": style,
        "caption": template1,
        "template": template_id1
    })
    
    # Second and third memes use different styles for diversity
    for i in range(2):  # Generate 2 more memes
        # Pick a different style
        available_styles = [s for s in all_styles if s not in used_styles]
        if not available_styles:
            available_styles = all_styles  # Fallback if all used
        
        new_style = random.choice(available_styles)
        used_styles.add(new_style)
        
        # Get caption for new style
        new_templates = meme_templates.get(new_style, meme_templates["relatable"])
        new_caption = random.choice(new_templates)
        new_template_id = random.choice(TEMPLATES)["id"]
        
        memes.append({
            "style": new_style,
            "caption": new_caption,
            "template": new_template_id
        })

    # CRITICAL: Ensure exactly 3 memes (hard limit)
    memes = memes[:EXACT_MEME_COUNT]
    
    # Pad to exactly 3 if needed
    while len(memes) < EXACT_MEME_COUNT:
        memes.append({
            "style": random.choice(all_styles),
            "caption": f"{topic} but make it memorable ✨",
            "template": random.choice(TEMPLATES)["id"]
        })

    # Create structured JSON output
    output = {
        "reply": f"{topic} ke charche toh har jagah hain! 😂 Ye 3 memes dekho.",
        "memes": memes
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

        # Generate memes with structured output
        result_json = generate_memes(topic, style, tone, template)
        print(result_json)

    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)
