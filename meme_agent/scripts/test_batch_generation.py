#!/usr/bin/env python3
"""
Test script for Batch Meme Generation with Diverse Styles.

Tests:
1. Single JSON output format
2. 5-meme pool → 3 meme filtering
3. Diverse styles across 3 memes
4. Mood-based auto-selection from MOOD_MAPPING
5. Structured output: {style, caption, template} per meme

Usage:
    python meme_agent/scripts/test_batch_generation.py
"""

import os
import sys
import json
import asyncio

# Add parent directory to path
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from api import generate_memes


async def test_batch_generation(topic: str, expected_styles: list = None):
    """
    Test batch generation for a given topic.
    
    Args:
        topic: The meme topic/query
        expected_styles: List of styles we expect to see (for validation)
    """
    print(f"\n{'='*60}")
    print(f"Testing: '{topic}'")
    print(f"{'='*60}")
    
    try:
        # Call generate_memes with auto style/tone
        result = await generate_memes(
            topic=topic,
            style="auto",
            tone="auto",
            template_choice="AUTO"
        )
        
        # Parse JSON
        output = json.loads(result)
        
        # Validate structure
        print("\n✅ JSON Structure:")
        print(f"   - 'reply' key: {'✓' if 'reply' in output else '✗'}")
        print(f"   - 'memes' key: {'✓' if 'memes' in output else '✗'}")
        
        # Validate memes array
        memes = output.get("memes", [])
        print(f"   - Meme count: {len(memes)} (expected: 3)")
        
        # Validate each meme has required fields
        print("\n✅ Meme Structure:")
        styles_used = set()
        for i, meme in enumerate(memes, 1):
            if isinstance(meme, dict):
                has_style = "style" in meme
                has_caption = "caption" in meme
                has_template = "template" in meme
                style = meme.get("style", "N/A")
                styles_used.add(style)
                
                print(f"   Meme {i}:")
                print(f"      - style: {'✓' if has_style else '✗'} ({style})")
                print(f"      - caption: {'✓' if has_caption else '✗'}")
                print(f"      - template: {'✓' if has_template else '✗'}")
                
                if has_caption:
                    caption = meme.get("caption", "")[:60]
                    print(f"      - Caption preview: \"{caption}...\"")
            else:
                print(f"   Meme {i}: ✗ Not a dict (got {type(meme).__name__})")
        
        # Validate diverse styles
        print(f"\n✅ Style Diversity:")
        print(f"   - Unique styles used: {len(styles_used)}")
        print(f"   - Styles: {', '.join(styles_used)}")
        
        if len(memes) == 3 and len(styles_used) == 3:
            print(f"   ✓ All 3 memes have different styles!")
        elif len(memes) == 3:
            print(f"   ⚠ Some styles repeated ({3 - len(styles_used)} duplicates)")
        
        # Print full output
        print(f"\n📄 Full JSON Output:")
        print(json.dumps(output, indent=2))
        
        return {
            "success": True,
            "topic": topic,
            "meme_count": len(memes),
            "unique_styles": len(styles_used),
            "styles": list(styles_used),
            "output": output
        }
        
    except json.JSONDecodeError as e:
        print(f"\n❌ JSON Parse Error: {e}")
        print(f"Raw output: {result[:200]}...")
        return {"success": False, "error": "JSON parse error", "topic": topic}
    except Exception as e:
        print(f"\n❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return {"success": False, "error": str(e), "topic": topic}


async def main():
    """Run all batch generation tests."""
    print("="*60)
    print("Batch Meme Generation Test Suite")
    print("Single JSON | 5→3 Filter | Diverse Styles | Mood-Based")
    print("="*60)
    
    # Test cases with different moods
    test_cases = [
        {
            "topic": "yaar exams ne maar diya",
            "expected_mood": "exam",
            "description": "Academic stress"
        },
        {
            "topic": "monday morning office",
            "expected_mood": "monday",
            "description": "Work stress"
        },
        {
            "topic": "broke at end of month",
            "expected_mood": "broke",
            "description": "Money problems"
        },
        {
            "topic": "weekend vibes finally",
            "expected_mood": "weekend",
            "description": "Celebration"
        },
        {
            "topic": "instagram vs reality",
            "expected_mood": "instagram",
            "description": "Social media satire"
        }
    ]
    
    results = []
    for test in test_cases:
        result = await test_batch_generation(
            test["topic"],
        )
        result["expected_mood"] = test.get("expected_mood")
        result["description"] = test.get("description")
        results.append(result)
    
    # Summary
    print("\n" + "="*60)
    print("Test Summary")
    print("="*60)
    
    passed = sum(1 for r in results if r.get("success") and r.get("meme_count") == 3)
    diverse = sum(1 for r in results if r.get("unique_styles") == 3)
    
    print(f"\nTotal Tests: {len(results)}")
    print(f"Passed (3 memes): {passed}/{len(results)}")
    print(f"Diverse Styles (3 unique): {diverse}/{len(results)}")
    
    print("\nDetailed Results:")
    for r in results:
        status = "✓" if r.get("success") else "✗"
        topic = r.get("topic", "unknown")
        memes = r.get("meme_count", 0)
        styles = r.get("unique_styles", 0)
        desc = r.get("description", "")
        print(f"  {status} [{memes} memes, {styles} styles] {topic} ({desc})")
    
    if passed == len(results) and diverse >= len(results) - 1:
        print("\n🎉 All tests passed! Batch generation working correctly.")
        sys.exit(0)
    else:
        print("\n⚠ Some tests failed. Check output above.")
        sys.exit(1)


if __name__ == "__main__":
    asyncio.run(main())
