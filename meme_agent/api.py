import sys
import json
import asyncio
import os
import random
import logging
from collections import defaultdict
from dotenv import load_dotenv

logger = logging.getLogger(__name__)

# Import components from core
from core import (
    meme_agent,
    config,
    UserContext,
    STYLES,
    TONES,
    STYLE_DEFS,
    Runner,
    is_content_safe,
    select_style_tone_template,  # NEW: Auto-selection function
    get_template_priority_list,  # NEW: Template prioritization
)
try:
    import data_loader
except ImportError:
    from meme_agent import data_loader

try:
    from caption_search import search_templates as semantic_search_captions
except ImportError:
    from meme_agent.caption_search import search_templates as semantic_search_captions

def _get_template_block(selected_id: str, templates: list) -> str:
    if selected_id == "AUTO":
        # Return a summarized list for the agent
        sample_size = min(20, len(templates))
        lines = [f"Refer to {len(templates)} available templates. Here are some options:"]
        for t in templates[:sample_size]:
            lines.append(f"- ID: {t.id} | Name: {t.name}")
        return "\n".join(lines)
    
    found = next((t for t in templates if t.id == selected_id), None)
    if found:
        return f"Selected Template: ID={found.id}, Name={found.name}"
    return "Template not found."

def _feedback_path() -> str:
    base_dir = os.path.dirname(os.path.abspath(__file__))
    return os.path.abspath(os.path.join(base_dir, "..", "storage", "app", "meme_agent_feedback.jsonl"))

def _load_feedback(limit: int = 500) -> list[dict]:
    path = _feedback_path()
    if not os.path.exists(path):
        return []
    records = []
    try:
        with open(path, "r", encoding="utf-8", errors="ignore") as f:
            for line in f:
                line = line.strip()
                if not line:
                    continue
                try:
                    records.append(json.loads(line))
                except json.JSONDecodeError:
                    continue
    except OSError:
        return []
    return records[-limit:]

def _derive_feedback_insights(records: list[dict]):
    high_examples = []
    template_scores = defaultdict(list)
    style_scores = defaultdict(list)

    for r in records:
        rating = r.get("rating")
        feedback = r.get("feedback")
        text = (r.get("meme_text") or "").strip()
        template_id = (r.get("template_id") or "").strip()
        style = (r.get("style") or "").strip()

        if isinstance(rating, int) and rating > 0:
            if template_id:
                template_scores[template_id].append(rating)
            if style:
                style_scores[style].append(rating)

        if text and ((isinstance(rating, int) and rating >= 4) or feedback == "like"):
            high_examples.append(text)

    def _avg(scores: list[int]) -> float:
        return sum(scores) / len(scores) if scores else 0.0

    template_avg = {k: _avg(v) for k, v in template_scores.items()}
    style_avg = {k: _avg(v) for k, v in style_scores.items()}

    preferred_templates = [k for k, v in sorted(template_avg.items(), key=lambda x: x[1], reverse=True) if v >= 4.0]
    preferred_styles = [k for k, v in sorted(style_avg.items(), key=lambda x: x[1], reverse=True) if v >= 4.0]
    deprioritized_templates = [k for k, v in sorted(template_avg.items(), key=lambda x: x[1]) if v > 0 and v <= 2.0]
    deprioritized_styles = [k for k, v in sorted(style_avg.items(), key=lambda x: x[1]) if v > 0 and v <= 2.0]

    # Keep a small, diverse set of examples
    uniq = []
    for ex in high_examples:
        if ex not in uniq:
            uniq.append(ex)
        if len(uniq) >= 3:
            break

    return uniq, preferred_templates, preferred_styles, deprioritized_templates, deprioritized_styles


try:
    import web_search_tool
except ImportError:
    from meme_agent import web_search_tool

from core import client

async def _extract_keyword(topic: str) -> str:
    """
    Extract the core searchable keyword from the user topic using OpenAI.
    """
    try:
        response = await client.chat.completions.create(
            model="gpt-4o",
            messages=[
                {"role": "system", "content": "You are a keyword extractor. Extract the main subject or topic from the user's input for a meme search. Return ONLY the keyword(s). No quotes, no extra text. Example: 'I want a meme about exam stress' -> 'exam stress'. 'Monday mood' -> 'monday'."},
                {"role": "user", "content": topic}
            ],
            temperature=0.3,
            max_tokens=20
        )
        return response.choices[0].message.content.strip()
    except Exception:
        return topic # Fallback to full topic

async def generate_memes(topic: str, style: str, tone: str, template_choice: str):
    """
    Generate memes using a single LLM call and dataset context.
    
    Rules:
    - Semantic search first.
    - Fallback to keyword search if similarity < 0.6.
    - Exactly 3 memes.
    - JSON output.
    """
    # ========================================
    # 🔍 DATASET SEARCH
    # ========================================
    results = []
    search_source = "semantic"
    
    try:
        # Try semantic search first
        semantic_results = await asyncio.to_thread(data_loader.semantic_search, topic, top_k=10)
        
        # Check similarity threshold (0.6)
        if semantic_results and semantic_results[0].get('score', 0) >= 0.6:
            results = semantic_results
            logger.info(f"Semantic search successful (score: {results[0]['score']:.3f})")
        else:
            # Fallback to keyword search if semantic score is low or no results
            logger.info("Semantic search score low or no results, falling back to keyword search")
            results = await asyncio.to_thread(data_loader.keyword_search, topic, limit=10)
            search_source = "keyword"
            
    except Exception as e:
        logger.error(f"Search failed: {e}")
        # Fallback to keyword search on error
        results = await asyncio.to_thread(data_loader.keyword_search, topic, limit=10)
        search_source = "fallback_keyword"

    # ========================================
    # 🧠 PROMPT CONSTRUCTION
    # ========================================
    # Load templates for context
    templates = await asyncio.to_thread(data_loader.load_templates)
    
    # Filter by user choice if provided
    if template_choice != "AUTO":
        # Prioritize the chosen template if it exists
        user_template_found = next((t for t in templates if t.id == template_choice), None)
        if not user_template_found:
             template_choice = "AUTO" # Revert to auto if not found
             
    # Prepare dataset context
    dataset_context = []
    for r in results[:10]:
        dataset_context.append(f"Template: {r.get('template_id')} | Caption: {r.get('caption')}")
    
    context_str = "\n".join(dataset_context) if dataset_context else "No direct matches found in dataset."
    
    # Mood/Auto-selection context
    auto_selection = select_style_tone_template(topic)
    detected_mood = auto_selection.get('detected_mood', 'general')
    
    prompt = (
        f"Generate exactly 3 Hinglish/English memes for the topic: '{topic}'.\n\n"
        f"DETECTION:\n"
        f"- Detected Mood: {detected_mood}\n"
        f"- Target Style: {style}\n"
        f"- Target Tone: {tone}\n\n"
        f"DATASET CONTEXT (PRIORITY):\n"
        f"{context_str}\n\n"
        f"INSTRUCTIONS:\n"
        f"1. Use the dataset captions if they are relevant (high similarity).\n"
        f"2. If the dataset results are low quality or irrelevant, you may be creative but keep the topic consistent.\n"
        f"3. Follow the target style: {style} and tone: {tone}.\n"
        f"4. If template_choice is '{template_choice}', use that ID if it's not 'AUTO'. Otherwise, pick appropriate templates.\n"
        f"5. Output ONLY a valid JSON object with keys: 'reply', 'memes', 'meme_intent'.\n"
        f"6. 'reply' should be a natural, conversational Hinglish/English punchline (e.g., 'Ye memes dekho!'). Do NOT mention the quantity '3'.\n"
        f"7. 'memes' must contain EXACTLY 3 objects with 'style', 'caption', 'template'.\n"
        f"8. 'meme_intent' must be true.\n"
    )

    # ========================================
    # 🚀 SINGLE LLM CALL
    # ========================================
    try:
        context = UserContext(topic=topic, style=style, tone=tone, count=3)
        final_result = await Runner.run(
            meme_agent,
            prompt,
            context=context,
            run_config=config
        )
        
        output_text = final_result.final_output or ""
        # Clean output string to ensure it's valid JSON
        output_text = output_text.replace("```json", "").replace("```", "").strip()
        
        # Validate JSON
        try:
            parsed = json.loads(output_text)
            # Ensure quantity and format
            if "memes" in parsed:
                # Enforce exactly 3
                parsed["memes"] = parsed["memes"][:3]
                while len(parsed["memes"]) < 3:
                    parsed["memes"].append({
                        "style": style,
                        "caption": f"Dealing with {topic} like a pro.",
                        "template": "auto"
                    })
                # Ensure meme_intent
                parsed["meme_intent"] = True
                return json.dumps(parsed)
            else:
                raise ValueError("Missing 'memes' key")
        except (json.JSONDecodeError, ValueError) as e:
            logger.warning(f"JSON Parsing failed: {e}. Output was: {output_text}")
            raise # Fallthrough to outer except
            
    except Exception as e:
        logger.error(f"Generation failed: {e}")
        # Fallback response
        fallback = {
            "reply": f"{topic} ka scene dekho!",
            "memes": [
                {"style": style, "caption": f"Me trying to explain {topic}.", "template": "auto"},
                {"style": style, "caption": f"The moment you realize {topic} is life.", "template": "auto"},
                {"style": style, "caption": f"When {topic} hits different.", "template": "auto"}
            ],
            "meme_intent": True
        }
        return json.dumps(fallback)

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Missing arguments. Usage: api.py <json_string>"}))
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

        result_text = asyncio.run(generate_memes(topic, style, tone, template))
        
        # Simple parsing for JSON output
        lines = result_text.strip().split('\n')
        parsed_memes = []
        for line in lines:
            line = line.strip()
            if not line: continue
            # Try to extract ID and Text. Format: "1. [T01] Text..."
            parsed_memes.append(line)

        # STRIKE RULE: Exactly 3 memes
        parsed_memes = parsed_memes[:3]

        print(json.dumps({
            "status": "success",
            "data": {
                "raw_output": result_text,
                "memes": parsed_memes
            }
        }))

    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)
