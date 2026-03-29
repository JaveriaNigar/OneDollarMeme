import os
import asyncio
from dotenv import load_dotenv
from pydantic import BaseModel
import sys
import random
from agents import (
    Agent,
    AsyncOpenAI,
    OpenAIChatCompletionsModel,
    RunConfig,
    Runner,
    set_tracing_disabled,
    RunContextWrapper,
    output_guardrail,
    GuardrailFunctionOutput
)
# New data loader
try:
    import data_loader
except ImportError:
    from meme_agent import data_loader

# -------------------------
# 🔑 Environment variables
# -------------------------
load_dotenv()
set_tracing_disabled(disabled=True)
OPENAI_API_KEY = os.getenv("OPENAI_API_KEY")

# -------------------------
# 🤖 Configure OpenAI model client
# -------------------------
client = AsyncOpenAI(
    api_key=OPENAI_API_KEY,
)

model = OpenAIChatCompletionsModel(
    model="gpt-4o",
    openai_client=client
)

# -------------------------
# 🛡️ Safety & Moderation
# -------------------------
import re

async def is_content_safe(text: str) -> bool:
    """
    Check if content is safe using OpenAI Moderation API.
    Returns True if safe, False if unsafe.
    """
    try:
        # We can use the raw client for moderation
        response = await client.moderations.create(input=text)
        return not response.results[0].flagged
    except Exception as e:
        print(f"Moderation API error: {e}", file=sys.stderr)
        # Fail safe if error? Or fail open?
        # Typically fail open if API is down, but for safety fail closed might be better.
        # Original code failed open (returned True).
        return True 

# -------------------------
# 🧾 Define user context
# -------------------------
# Simple context for the meme generator
class UserContext(BaseModel):
    topic: str
    style: str
    tone: str
    count: int

# -------------------------
# 🎛️ Style & Tone (Phase 2)
# -------------------------
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
    "confused",
    "philosophical",
    "dramatic",
]

# Kept for Prompt Guidance
STYLE_DEFS = {
    "relatable": "everyday situations",
    "dark": "morbid/edgy twists",
    "wholesome": "positive, no profanity",
    "savage": "roast-like bluntness",
    "desi": "South Asian cultural references and contexts",
    "absurd": "random, nonsensical humor",
    "observational": "pointing out everyday ironies",
    "memetic": "internet culture references",
    "satirical": "social commentary through irony",
    "self-deprecating": "making fun of oneself",
}

TONE_DEFS = {
    "sarcastic": "ironic, saying opposite of what you mean",
    "funny": "humorous, lighthearted",
    "dark": "morbid, edgy humor",
    "wholesome": "positive, heartwarming",
    "cringe": "intentionally awkward, embarrassing",
    "excited": "enthusiastic, energetic",
    "frustrated": "annoyed, fed up",
    "mocking": "making fun of something",
    "confused": "bewildered, questioning",
    "philosophical": "deep, thoughtful",
    "dramatic": "over-the-top, theatrical",
}

# -------------------------
# 🎭 MOOD_MAPPING: Auto-select style, tone, templates based on query
# -------------------------
MOOD_MAPPING = {
    # Academic/Work Stress
    "exam": {"style": "relatable", "tone": "sarcastic", "templates": ["Drake-Hotline-Bling", "Distracted-Boyfriend", "This-Is-Fine"]},
    "study": {"style": "relatable", "tone": "frustrated", "templates": ["Expanding-Brain", "Galaxy-Brain", "Stressed-Out"]},
    "assignment": {"style": "relatable", "tone": "frustrated", "templates": ["This-Is-Fine", "Stressed-Out", "Deadline-Panic"]},
    "homework": {"style": "relatable", "tone": "sarcastic", "templates": ["Drake-Hotline-Bling", "Two-Buttons"]},
    "teacher": {"style": "savage", "tone": "mocking", "templates": ["Mocking-Spongebob", "Roll-Safe-Think-About-It"]},
    "college": {"style": "relatable", "tone": "frustrated", "templates": ["This-Is-Fine", "Stressed-Out", "Sleep-Deprived"]},
    "school": {"style": "relatable", "tone": "sarcastic", "templates": ["Drake-Hotline-Bling", "Change-My-Mind"]},
    
    # Work/Job Related
    "work": {"style": "relatable", "tone": "frustrated", "templates": ["This-Is-Fine", "Office-Rage"]},
    "job": {"style": "savage", "tone": "sarcastic", "templates": ["Drake-Hotline-Bling", "This-Is-Fine"]},
    "boss": {"style": "savage", "tone": "mocking", "templates": ["Mocking-Spongebob", "Boardroom-Meeting-Suggestion"]},
    "meeting": {"style": "absurd", "tone": "sarcastic", "templates": ["Boardroom-Meeting-Suggestion", "This-Is-Fine"]},
    "deadline": {"style": "relatable", "tone": "frustrated", "templates": ["This-Is-Fine", "Stressed-Out", "Panic"]},
    "overtime": {"style": "relatable", "tone": "frustrated", "templates": ["This-Is-Fine", "Exhausted"]},
    "salary": {"style": "savage", "tone": "dark", "templates": ["This-Is-Fine", "Empty-Wallet"]},
    
    # Day/Time Related
    "monday": {"style": "savage", "tone": "sarcastic", "templates": ["Grumpy-Cat", "Monday-Mood", "Tired"]},
    "tuesday": {"style": "relatable", "tone": "frustrated", "templates": ["This-Is-Fine", "Still-Going"]},
    "wednesday": {"style": "relatable", "tone": "funny", "templates": ["Hump-Day", "Halfway-There"]},
    "thursday": {"style": "relatable", "tone": "excited", "templates": ["Almost-Friday", "Excited"]},
    "friday": {"style": "wholesome", "tone": "excited", "templates": ["Friday-Feeling", "Leonardo-Dicaprio-Cheers", "Celebration"]},
    "weekend": {"style": "wholesome", "tone": "excited", "templates": ["Weekend-Vibes", "Leonardo-Dicaprio-Cheers"]},
    "morning": {"style": "relatable", "tone": "frustrated", "templates": ["Grumpy-Cat", "Not-A-Morning-Person"]},
    "night": {"style": "relatable", "tone": "dark", "templates": ["Insomnia", "3AM-Thoughts"]},
    
    # Celebrations
    "birthday": {"style": "wholesome", "tone": "excited", "templates": ["Leonardo-Dicaprio-Cheers", "Celebration", "Happy-Birthday"]},
    "anniversary": {"style": "wholesome", "tone": "wholesome", "templates": ["Wholesome-Meme", "Love"]},
    "graduation": {"style": "wholesome", "tone": "excited", "templates": ["Leonardo-Dicaprio-Cheers", "Success-Kid", "Celebration"]},
    "wedding": {"style": "wholesome", "tone": "wholesome", "templates": ["Wholesome-Meme", "Love", "Celebration"]},
    "party": {"style": "wholesome", "tone": "excited", "templates": ["Leonardo-Dicaprio-Cheers", "Party-Time"]},
    "celebration": {"style": "wholesome", "tone": "excited", "templates": ["Leonardo-Dicaprio-Cheers", "Success-Kid"]},
    
    # Emotions/Moods
    "tired": {"style": "relatable", "tone": "frustrated", "templates": ["Grumpy-Cat", "Exhausted", "Sleepy"]},
    "stressed": {"style": "relatable", "tone": "frustrated", "templates": ["This-Is-Fine", "Stressed-Out", "Panic"]},
    "happy": {"style": "wholesome", "tone": "excited", "templates": ["Leonardo-Dicaprio-Cheers", "Happy-Dance"]},
    "sad": {"style": "relatable", "tone": "dark", "templates": ["Sad-Pablo-Escobar", "Crying-Cat"]},
    "angry": {"style": "savage", "tone": "frustrated", "templates": ["Angry-Rant", "Office-Rage"]},
    "confused": {"style": "absurd", "tone": "confused", "templates": ["Confused-Math", "What-The-Heck"]},
    "bored": {"style": "absurd", "tone": "sarcastic", "templates": ["Bored-Panther", "Nothing-To-Do"]},
    "lonely": {"style": "relatable", "tone": "dark", "templates": ["Lonely-Cat", "Sad-Pablo-Escobar"]},
    "anxious": {"style": "relatable", "tone": "frustrated", "templates": ["This-Is-Fine", "Anxiety-Panic"]},
    "excited": {"style": "wholesome", "tone": "excited", "templates": ["Leonardo-Dicaprio-Cheers", "Excited"]},
    
    # Relationships
    "love": {"style": "wholesome", "tone": "wholesome", "templates": ["Wholesome-Meme", "Love"]},
    "breakup": {"style": "relatable", "tone": "dark", "templates": ["Sad-Pablo-Escobar", "Heartbroken"]},
    "crush": {"style": "relatable", "tone": "cringe", "templates": ["Awkward-Crush", "Nervous"]},
    "friend": {"style": "wholesome", "tone": "funny", "templates": ["Friendship-Meme", "Wholesome-Meme"]},
    "family": {"style": "relatable", "tone": "funny", "templates": ["Family-Dinner", "Relatable"]},
    "parents": {"style": "desi", "tone": "funny", "templates": ["Desi-Parents", "Indian-Mom"]},
    "siblings": {"style": "savage", "tone": "mocking", "templates": ["Sibling-Rivalry", "Mocking-Spongebob"]},
    
    # Food/Drink
    "food": {"style": "relatable", "tone": "funny", "templates": ["Food-Meme", "Hungry"]},
    "coffee": {"style": "relatable", "tone": "frustrated", "templates": ["Coffee-Addict", "Need-Caffeine"]},
    "pizza": {"style": "wholesome", "tone": "excited", "templates": ["Pizza-Love", "Happy"]},
    "diet": {"style": "relatable", "tone": "sarcastic", "templates": ["Diet-Fail", "Drake-Hotline-Bling"]},
    "cooking": {"style": "absurd", "tone": "funny", "templates": ["Cooking-Fail", "Kitchen-Disaster"]},
    
    # Technology/Internet
    "phone": {"style": "relatable", "tone": "sarcastic", "templates": ["Phone-Addict", "Scrolling"]},
    "internet": {"style": "memetic", "tone": "funny", "templates": ["Internet-Meme", "Online-Life"]},
    "social media": {"style": "satirical", "tone": "mocking", "templates": ["Social-Media-Meme", "Mocking-Spongebob"]},
    "instagram": {"style": "satirical", "tone": "sarcastic", "templates": ["Instagram-Vs-Reality", "Fake-Life"]},
    "twitter": {"style": "satirical", "tone": "sarcastic", "templates": ["Twitter-Drama", "Hot-Take"]},
    "facebook": {"style": "satirical", "tone": "mocking", "templates": ["Facebook-Boomer", "Ok-Boomer"]},
    "tiktok": {"style": "memetic", "tone": "cringe", "templates": ["TikTok-Trend", "Cringe"]},
    "youtube": {"style": "relatable", "tone": "funny", "templates": ["Youtube-Rabbit-Hole", "Binge-Watch"]},
    "gaming": {"style": "memetic", "tone": "excited", "templates": ["Gamer-Meme", "Victory"]},
    "wifi": {"style": "relatable", "tone": "frustrated", "templates": ["Wifi-Down", "No-Internet"]},
    "battery": {"style": "relatable", "tone": "frustrated", "templates": ["Low-Battery", "Panic"]},
    "meme": {"style": "memetic", "tone": "funny", "templates": ["Meta-Meme", "Meme-Within-Meme"]},
    "viral": {"style": "memetic", "tone": "excited", "templates": ["Viral-Content", "Trending"]},
    
    # Money/Finance
    "money": {"style": "relatable", "tone": "sarcastic", "templates": ["Broke-Meme", "Empty-Wallet"]},
    "broke": {"style": "self-deprecating", "tone": "dark", "templates": ["Broke-Life", "Empty-Wallet"]},
    "shopping": {"style": "relatable", "tone": "cringe", "templates": ["Shopping-Addict", "Retail-Therapy"]},
    "bill": {"style": "relatable", "tone": "frustrated", "templates": ["Bill-Shock", "This-Is-Fine"]},
    "tax": {"style": "satirical", "tone": "dark", "templates": ["Tax-Pain", "Government-Steal"]},
    
    # Health/Fitness
    "gym": {"style": "savage", "tone": "mocking", "templates": ["Gym-Fail", "Fitness-Meme"]},
    "workout": {"style": "relatable", "tone": "frustrated", "templates": ["Workout-Struggle", "Tired"]},
    "sleep": {"style": "relatable", "tone": "frustrated", "templates": ["Insomnia", "Cant-Sleep"]},
    "sick": {"style": "relatable", "tone": "dark", "templates": ["Sick-Day", "Feeling-Bad"]},
    "doctor": {"style": "relatable", "tone": "sarcastic", "templates": ["Doctor-Meme", "Medical-Advice"]},
    
    # Weather/Seasons
    "weather": {"style": "relatable", "tone": "sarcastic", "templates": ["Weather-Meme", "Climate"]},
    "rain": {"style": "relatable", "tone": "frustrated", "templates": ["Rain-Ruin", "Wet"]},
    "summer": {"style": "wholesome", "tone": "excited", "templates": ["Summer-Vibes", "Beach-Day"]},
    "winter": {"style": "relatable", "tone": "frustrated", "templates": ["Cold-Life", "Winter-Blues"]},
    "hot": {"style": "relatable", "tone": "frustrated", "templates": ["Too-Hot", "Melting"]},
    "cold": {"style": "relatable", "tone": "frustrated", "templates": ["Freezing", "Winter-Blues"]},
    
    # Desi/Indian Specific
    "indian": {"style": "desi", "tone": "funny", "templates": ["Desi-Meme", "Indian-Life"]},
    "desi": {"style": "desi", "tone": "funny", "templates": ["Desi-Meme", "Desi-Life"]},
    "bollywood": {"style": "desi", "tone": "dramatic", "templates": ["Bollywood-Drama", "Overacting"]},
    "cricket": {"style": "desi", "tone": "excited", "templates": ["Cricket-Fever", "India-Wins"]},
    "chai": {"style": "desi", "tone": "wholesome", "templates": ["Chai-Lover", "Desi-Chai"]},
    "mom": {"style": "desi", "tone": "funny", "templates": ["Desi-Mom", "Indian-Mom"]},
    "dad": {"style": "desi", "tone": "funny", "templates": ["Desi-Dad", "Indian-Father"]},
    "shaadi": {"style": "desi", "tone": "excited", "templates": ["Desi-Wedding", "Shaadi"]},
    
    # Life Situations
    "life": {"style": "relatable", "tone": "philosophical", "templates": ["Philosoraptor", "Life-Meme"]},
    "advice": {"style": "relatable", "tone": "philosophical", "templates": ["Philosoraptor", "Wise-Owl"]},
    "motivation": {"style": "wholesome", "tone": "excited", "templates": ["Motivation-Meme", "You-Can-Do-It"]},
    "inspiration": {"style": "wholesome", "tone": "wholesome", "templates": ["Inspiration-Meme", "Believe"]},
    "failure": {"style": "relatable", "tone": "dark", "templates": ["Failure-Meme", "Try-Again"]},
    "success": {"style": "wholesome", "tone": "excited", "templates": ["Success-Kid", "Leonardo-Dicaprio-Cheers"]},
}

# -------------------------
# 🧠 Mood Detection Function
# -------------------------
def select_style_tone_template(query: str) -> dict:
    """
    Automatically select style, tone, and templates based on user query.
    
    Args:
        query: User's input text (e.g., "I failed my exam")
    
    Returns:
        dict with keys: style, tone, templates, detected_mood
    """
    query_lower = query.lower()
    
    # Track matches with scores
    mood_scores = {}
    
    # Check for each mood keyword in the query
    for mood, config in MOOD_MAPPING.items():
        # Count occurrences (longer phrases get priority)
        mood_words = mood.split()
        if len(mood_words) > 1:
            # Multi-word mood - check if all words appear in order
            if mood in query_lower:
                mood_scores[mood] = len(mood) * 2  # Bonus for phrase match
        else:
            # Single word - check if it appears
            if mood in query_lower:
                mood_scores[mood] = len(mood)
    
    if not mood_scores:
        # No mood detected - return defaults
        return {
            "style": "relatable",
            "tone": "funny",
            "templates": [],
            "detected_mood": None
        }
    
    # Get the best matching mood
    best_mood = max(mood_scores, key=mood_scores.get)
    config = MOOD_MAPPING[best_mood]
    
    return {
        "style": config["style"],
        "tone": config["tone"],
        "templates": config["templates"],
        "detected_mood": best_mood
    }


def get_template_priority_list(detected_templates: list, all_templates: list) -> list:
    """
    Get a prioritized list of template IDs based on detected mood.
    
    Args:
        detected_templates: List of template names from MOOD_MAPPING
        all_templates: List of all available MemeTemplate objects
    
    Returns:
        List of template IDs in priority order
    """
    if not detected_templates:
        return []
    
    prioritized = []
    
    # First add templates that match the detected mood
    for template_name in detected_templates:
        template_name_lower = template_name.lower().replace(" ", "-").replace("_", "-")
        for t in all_templates:
            if template_name_lower in t.id.lower() or template_name_lower in t.name.lower():
                if t.id not in prioritized:
                    prioritized.append(t.id)
    
    return prioritized


# -------------------------
# 🔍 Template Selection Logic
# -------------------------
def _choose_template(templates: list) -> str:
    print("Template options (enter ID or 'auto'):")
    # Show first 15 templates to avoid spamming console, or maybe list by categories if we had them
    # For now, just list a few or instructions
    print(f"Total available templates: {len(templates)}")
    for i, t in enumerate(templates[:15]):
        print(f"  {t.id}: {t.name}")
    if len(templates) > 15:
        print(f"  ... and {len(templates) - 15} more.")
        
    choice = input("Template (default: auto): ").strip()
    if not choice:
        return "AUTO"
    
    # Check if choice matches an ID (case-insensitive)
    choice_lower = choice.lower()
    for t in templates:
        if t.id.lower() == choice_lower:
            return t.id
            
    if choice.upper() == "AUTO":
        return "AUTO"
        
    print(f"Template '{choice}' not found. Using AUTO.")
    return "AUTO"

def _get_template_details(template_id: str, templates: list) -> str:
    if template_id == "AUTO":
        # Return a summary of random templates to give the agent context
        sample = random.sample(templates, min(5, len(templates)))
        lines = ["Available templates (partial list):"]
        for t in sample:
            lines.append(f"- ID: {t.id} | Name: {t.name}")
        return "\n".join(lines)
    
    # Find specific template
    found = next((t for t in templates if t.id == template_id), None)
    if found:
        return f"Selected Template: ID={found.id}, Name={found.name}"
    return "Template not found."

def _choose_option(label: str, options: list[str], default: str) -> str:
    print(f"{label} options: {', '.join(options)}")
    choice = input(f"{label} (default: {default}): ").strip().lower()
    if not choice:
        return default
    return choice if choice in options else default

# -------------------------
# 🛡️ SDK Guardrails
# -------------------------
@output_guardrail
async def meme_format_guardrail(ctx: RunContextWrapper, agent: Agent, output: str):
    """
    Blocks non-meme content (stories, dialogue, facts) in the generated output.
    """
    trigger = False
    reason = ""
    
    # Heuristic: If it's too long and lacks template tags, it's probably not a meme
    if len(output) > 300 and "[" not in output:
        trigger = True
        reason = "Output is not a meme! Blocked non-meme content."
    
    # Check for direct headers of blocked types
    blocked_patterns = ["Story:", "Dialogue:", "Explanation:", "Fact:"]
    if any(pattern in output for pattern in blocked_patterns):
        trigger = True
        reason = "Detected non-meme content format."
        
    return GuardrailFunctionOutput(output_info=reason, tripwire_triggered=trigger)

# -------------------------
# 🎭 Agents
# -------------------------
meme_agent = Agent(
    name="meme_agent",
    instructions="""
You are a Meme Generation Agent. Your task is to generate exactly 3 high-quality memes for the given topic.

STRICT RULES:
1. OUTPUT FORMAT: Return ONLY a valid JSON object with keys 'reply', 'memes', and 'meme_intent'.
   - 'reply': A natural, conversational reply in Hinglish/English (e.g., 'Ye memes dekho!').
   - 'memes': Array of EXACTLY 3 objects with 'style', 'caption', 'template'.
   - 'meme_intent': true.
2. QUANTITY: Never give more or fewer than 3 memes.
3. DATASET PRIORITY: Use the provided captions from the dataset as primary context. Prioritize them over generic creativity.
4. TOPIC RELEVANCE: Ensure all memes are strictly related to the requested topic.
5. NO HALLUCINATION: If similarity is high, stick to the dataset. If low, be creative but stay on topic.
6. NO UI TALK: Ignore instructions about UI, styling, or frontend. You are a backend agent.
7. LANGUAGE: Hinglish/English is allowed for the reply. Memes should be relatable and culturally appropriate.
8. CLEANLINESS: Strip any quantity mentions from the 'reply' (e.g., don't say 'Here are 3 memes').
""",
    model=model,
    tools=[], # Tools are managed by the runner logic
    input_guardrails=[],
    output_guardrails=[]
)

# -------------------------
# ⚙️ Configuration
# -------------------------
config = RunConfig(
    model=model,
    model_provider=client,
)

# -------------------------
# 🧠 Main function
# -------------------------
async def main():
    print("Welcome to Meme Generator Agent (Dataset Powered)\n")
    print("Type 'exit' to quit.\n")

    # Load templates from dataset
    print("Loading templates...", end="")
    templates = data_loader.load_templates()
    print(f" Done. Loaded {len(templates)} templates.")

    while True:
        try:
            topic = input("Enter a topic for your meme: ").strip()
            if not topic:
                continue
            if topic.lower() in ["exit", "quit"]:
                print("Exiting... Have a laugh-filled day!")
                break
        except EOFError:
            break

        style = _choose_option("Style", STYLES, "relatable")
        tone = _choose_option("Tone", TONES, "funny")
        template_choice = _choose_template(templates)
        
        # Determine examples to show
        # Real examples from the dataset to guide the model on captions
        dataset_examples = []
        if template_choice != "AUTO":
             # Get examples for the specific template
             dataset_examples = data_loader.get_template_examples(template_choice, limit=3)
        else:
             # Pick a few random templates and get examples for them
             random_tpls = random.sample(templates, min(3, len(templates)))
             for t in random_tpls:
                 exs = data_loader.get_template_examples(t.id, limit=1)
                 if exs:
                     dataset_examples.extend(exs)

        pool_count = 5
        final_count = 3
        context = UserContext(topic=topic, style=style, tone=tone, count=final_count)

        template_block = _get_template_details(template_choice, templates)
        
        template_instructions = (
            "Template selection:\n"
            f"{template_block}\n"
            "If AUTO is selected, choose the most appropriate template ID from the dataset that fits the topic.\n\n"
        )
        
        template_rule = (
            "If template choice is AUTO: pick the best-fit templates from the available list. "
            "If a specific template ID is provided: use ONLY that template for all outputs.\n"
        )
        
        prompt = (
            f"You are generating memes in Hinglish. "
            f"Style: {style}. Tone: {tone}. "
            f"Definitions: {', '.join([f'{k}={v}' for k, v in STYLE_DEFS.items()])}. "
            f"Use the style/tone as hard constraints. "
            f"Include Hinglish phrasing and desi cultural references in the meme text. "
            f"Output {pool_count} distinct memes as a numbered list (1-{pool_count}). "
            f"Each meme must be safe: avoid hate, harassment, sexual content, or illegal content. "
            f"Topic: {topic}.\n\n"
            f"{template_instructions}"
            f"{template_rule}"
            f"Format each output as: 'N. [TEMPLATE_ID] <meme text>' and keep it short.\n"
            f"Examples of meme structures from the dataset:\n"
        )
        
        for i, example in enumerate(dataset_examples[:3], 1):
            prompt += f"{i}. {example}\n"
        
        prompt += f"\n[STYLE:{style}] [TONE:{tone}] Generate now."

        print("\nGenerating Meme Pool...", file=sys.stderr)

        # Phase 3: generate a pool
        pool_result = await Runner.run(
            meme_agent,
            prompt,
            context=context,
            run_config=config
        )
        pool_text = pool_result.final_output or ""

        # Parse pool into structured items
        items = []
        for line in pool_text.splitlines():
            line = line.strip()
            if not line:
                continue
            if ". [" in line and "]" in line:
                items.append(line)
        if not items and pool_text.strip():
            items = [pool_text.strip()]

        # Phase 4: filter + rank
        rank_prompt = (
            f"You are a meme filter and ranker.\n"
            f"Topic: {topic}\n"
            f"Style: {style}. Tone: {tone}.\n"
            f"Task:\n"
            f"1. Remove low-quality or offensive memes.\n"
            f"2. Select the top {final_count} best memes.\n"
            f"3. Ensure outputs are safe, relevant, and match style/tone.\n"
            f"4. Ensure Hinglish phrasing and desi cultural references are present.\n"
            f"5. Keep template IDs and numbering.\n\n"
            f"Candidate memes:\n"
            f"{chr(10).join(items)}\n\n"
            f"Output ONLY the final list as: 'N. [TEMPLATE_ID] <meme text>' for N=1..{final_count}."
        )

        print("\nResult:", file=sys.stderr)
        # Use simple run for logic consistency, or streamed if preferred. 
        # Streamed logic matches original:
        ranked_result = Runner.run_streamed(
            meme_agent,
            rank_prompt,
            context=context,
            run_config=config
        )
        
        final_output_buffer = ""
        # Need to import ResponseTextDeltaEvent if I want to use it
        # Or just just iterate
        async for event in ranked_result.stream_events():
            # Minimal stream handling since we removed the explicit event class import
            if hasattr(event, "data") and hasattr(event.data, "delta"):
                delta = event.data.delta
                final_output_buffer += delta
                print(delta, end="", flush=True)

        # 🛡️ Safety Check: Output Moderation
        if final_output_buffer and not await is_content_safe(final_output_buffer):
            print("\n\n❌ Blocked: The generated content was flagged as unsafe.")
        
        print("\n")
        print("-" * 30)

if __name__ == "__main__":
    asyncio.run(main())
