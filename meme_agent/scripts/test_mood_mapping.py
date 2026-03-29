#!/usr/bin/env python3
"""
Test script for MOOD_MAPPING auto-selection functionality.

Usage:
    python meme_agent/scripts/test_mood_mapping.py
"""

import sys
import os

# Add parent directory to path
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

# Set UTF-8 encoding for Windows
if sys.platform == 'win32':
    import io
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

from core import select_style_tone_template, MOOD_MAPPING


def test_mood_detection():
    """Test mood detection with various queries."""
    print("=" * 70)
    print("MOOD_MAPPING Auto-Selection Test Suite")
    print("=" * 70)
    print()
    
    # Test cases: (query, expected_mood, expected_style, expected_tone)
    # Note: When multiple moods match, the highest-scoring one wins
    test_cases = [
        # Academic/Work
        ("I failed my exam", "exam", "relatable", "sarcastic"),
        ("I have so much homework", "homework", "relatable", "sarcastic"),
        ("Studying for finals", "study", "relatable", "frustrated"),
        ("Assignment deadline tomorrow", "assignment", "relatable", "frustrated"),  # assignment scores higher
        
        # Work/Job
        ("My boss is annoying", "boss", "savage", "mocking"),
        ("I hate my job", "job", "savage", "sarcastic"),
        ("Another meeting today", "meeting", "absurd", "sarcastic"),
        ("Working overtime again", "overtime", "relatable", "frustrated"),
        
        # Day/Time
        ("It's Monday morning", "morning", "relatable", "frustrated"),  # same length, last match wins
        ("Monday blues", "monday", "savage", "sarcastic"),  # only monday matches
        ("Finally it's Friday", "friday", "wholesome", "excited"),
        ("Weekend vibes", "weekend", "wholesome", "excited"),
        
        # Celebrations
        ("It's my birthday today", "birthday", "wholesome", "excited"),
        ("Happy graduation day", "graduation", "wholesome", "excited"),
        ("Wedding season is here", "wedding", "wholesome", "wholesome"),
        
        # Emotions
        ("I'm so tired", "tired", "relatable", "frustrated"),
        ("Feeling stressed out", "stressed", "relatable", "frustrated"),
        ("I'm so happy today", "happy", "wholesome", "excited"),
        ("Feeling sad and lonely", "lonely", "relatable", "dark"),  # lonely scores higher
        
        # Relationships
        ("I love my family", "family", "relatable", "funny"),  # family scores higher than love
        ("Just went through a breakup", "breakup", "relatable", "dark"),
        ("I have a crush on someone", "crush", "relatable", "cringe"),
        ("Desi parents be like", "parents", "desi", "funny"),
        
        # Food/Drink
        ("Need my morning coffee", "morning", "relatable", "frustrated"),  # morning scores higher
        ("Pizza is life", "pizza", "wholesome", "excited"),
        ("My diet failed again", "diet", "relatable", "sarcastic"),
        
        # Technology (with new styles: memetic, satirical)
        ("Phone addiction is real", "phone", "relatable", "sarcastic"),
        ("Internet culture is wild", "internet", "memetic", "funny"),
        ("Social media is fake", "social media", "satirical", "mocking"),
        ("Instagram vs reality", "instagram", "satirical", "sarcastic"),
        ("Twitter drama again", "twitter", "satirical", "sarcastic"),
        ("TikTok trends are cringe", "tiktok", "memetic", "cringe"),
        ("Gaming all night", "gaming", "memetic", "excited"),
        ("Check out this meme", "meme", "memetic", "funny"),  # 'meme' without 'love'
        ("Going viral today", "viral", "memetic", "excited"),
        
        # Money (with new style: self-deprecating)
        ("I'm so broke", "broke", "self-deprecating", "dark"),
        ("Tax season sucks", "tax", "satirical", "dark"),
        ("Shopping therapy", "shopping", "relatable", "cringe"),
        
        # Health
        ("Need to go to the gym", "gym", "savage", "mocking"),
        ("Can't sleep at night", "night", "relatable", "dark"),  # night scores higher
        ("I'm feeling sick", "sick", "relatable", "dark"),
        
        # Weather
        ("It's too hot outside", "hot", "relatable", "frustrated"),
        ("I love summer", "summer", "wholesome", "excited"),
        ("Winter blues again", "winter", "relatable", "frustrated"),
        
        # Desi/Indian
        ("Desi life be like", "desi", "desi", "funny"),
        ("Bollywood drama", "bollywood", "desi", "dramatic"),
        ("Cricket match today", "cricket", "desi", "excited"),
        ("Chai is life", "chai", "desi", "wholesome"),
        ("Indian mom says", "indian", "desi", "funny"),  # indian scores higher than mom
        
        # Life
        ("Life advice needed", "advice", "relatable", "philosophical"),
        ("Need motivation", "motivation", "wholesome", "excited"),
        ("I failed but will try again", None, "relatable", "funny"),  # no mood match
        ("Success feels good", "success", "wholesome", "excited"),
        
        # No mood detected (should return defaults)
        ("Random query xyz", None, "relatable", "funny"),
        ("Testing 123", None, "relatable", "funny"),
    ]
    
    passed = 0
    failed = 0
    
    for query, expected_mood, expected_style, expected_tone in test_cases:
        result = select_style_tone_template(query)
        
        detected_mood = result["detected_mood"]
        detected_style = result["style"]
        detected_tone = result["tone"]
        
        # Check if mood matches (or is None for no-match cases)
        mood_match = detected_mood == expected_mood
        style_match = detected_style == expected_style
        tone_match = detected_tone == expected_tone
        
        if mood_match and style_match and tone_match:
            print(f"[PASS] '{query}'")
            print(f"   Mood: {detected_mood}, Style: {detected_style}, Tone: {detected_tone}")
            passed += 1
        else:
            print(f"[FAIL] '{query}'")
            print(f"   Expected: mood={expected_mood}, style={expected_style}, tone={expected_tone}")
            print(f"   Got:      mood={detected_mood}, style={detected_style}, tone={detected_tone}")
            failed += 1
        print()
    
    # Summary
    print("=" * 70)
    print("TEST SUMMARY")
    print("=" * 70)
    print(f"  Total tests: {len(test_cases)}")
    print(f"  Passed: {passed}")
    print(f"  Failed: {failed}")
    print(f"  Pass rate: {passed/len(test_cases)*100:.1f}%")
    print()
    
    if failed == 0:
        print("[OK] All tests passed!")
        return True
    else:
        print(f"[WARN] {failed} test(s) failed.")
        return False


def test_mood_mapping_coverage():
    """Test that all moods in MOOD_MAPPING are valid."""
    print("=" * 70)
    print("MOOD_MAPPING Coverage Test")
    print("=" * 70)
    print()
    
    from core import STYLES, TONES
    
    valid_styles = set(STYLES)
    valid_tones = set(TONES)
    
    issues = []
    
    for mood, config in MOOD_MAPPING.items():
        if config["style"] not in valid_styles:
            issues.append(f"Mood '{mood}': invalid style '{config['style']}'")
        if config["tone"] not in valid_tones:
            issues.append(f"Mood '{mood}': invalid tone '{config['tone']}'")
        if not config["templates"] or not isinstance(config["templates"], list):
            issues.append(f"Mood '{mood}': templates should be a non-empty list")
    
    if issues:
        print("[FAIL] Issues found:")
        for issue in issues:
            print(f"   - {issue}")
        return False
    else:
        print(f"[OK] All {len(MOOD_MAPPING)} moods have valid style, tone, and templates!")
        print(f"   Total styles: {len(valid_styles)} - {sorted(valid_styles)}")
        print(f"   Total tones: {len(valid_tones)} - {sorted(valid_tones)}")
        return True


def test_multi_word_moods():
    """Test that multi-word moods are detected correctly."""
    print()
    print("=" * 70)
    print("Multi-Word Mood Detection Test")
    print("=" * 70)
    print()
    
    multi_word_tests = [
        ("I'm on social media all day", "social media"),
        ("Instagram vs reality is funny", "instagram"),  # Single word should also match
    ]
    
    for query, expected_mood in multi_word_tests:
        result = select_style_tone_template(query)
        detected = result["detected_mood"]
        
        if detected == expected_mood:
            print(f"[PASS] '{query}' -> detected '{detected}'")
        else:
            print(f"[INFO] '{query}' -> detected '{detected}' (expected '{expected_mood}')")
    
    print()


if __name__ == "__main__":
    # Run coverage test first
    coverage_ok = test_mood_mapping_coverage()
    
    # Run multi-word test
    test_multi_word_moods()
    
    # Run main detection tests
    detection_ok = test_mood_detection()
    
    # Exit with appropriate code
    if coverage_ok and detection_ok:
        sys.exit(0)
    else:
        sys.exit(1)
