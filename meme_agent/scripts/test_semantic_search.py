#!/usr/bin/env python3
"""
Test script for Semantic Caption Search.

Usage:
    python meme_agent/scripts/test_semantic_search.py
"""

import sys
import os

# Add parent directory to path
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

# Set UTF-8 encoding for Windows
if sys.platform == 'win32':
    import io
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

from caption_search import search_templates, build_caption_index, CaptionSearchEngine


def test_semantic_search():
    """Test semantic search with various queries."""
    print("=" * 70)
    print("Semantic Caption Search Test Suite")
    print("=" * 70)
    print()
    
    # Build index
    print("Building caption index...")
    count = build_caption_index()
    print(f"Indexed {count} captions")
    print()
    
    # Test cases: (query, expected_keywords)
    test_cases = [
        # Academic/Exam stress
        ("I failed my exam", ["exam", "test", "study", "fail", "stress"]),
        ("Homework is due tomorrow", ["homework", "assignment", "deadline", "due"]),
        
        # Celebrations
        ("It's my birthday today", ["birthday", "celebration", "party", "happy"]),
        ("Happy New Year", ["year", "celebration", "happy", "new"]),
        
        # Emotions
        ("I'm so tired", ["tired", "sleep", "exhausted", "wake"]),
        ("Feeling stressed", ["stress", "anxious", "worry", "pressure"]),
        ("I'm so happy", ["happy", "joy", "smile", "laugh"]),
        
        # Work/Job
        ("I hate my job", ["work", "job", "boss", "tired"]),
        ("Monday morning", ["monday", "morning", "tired", "wake"]),
        
        # Technology
        ("My phone battery is dead", ["phone", "battery", "dead", "charge"]),
        ("WiFi is not working", ["wifi", "internet", "connection", "network"]),
        
        # Relationships
        ("I have a crush", ["crush", "love", "like", "heart"]),
        ("Missing my friend", ["friend", "miss", "love", "together"]),
        
        # Food
        ("I need coffee", ["coffee", "caffeine", "tired", "morning"]),
        ("Pizza is life", ["pizza", "food", "love", "eat"]),
    ]
    
    passed = 0
    failed = 0
    
    for query, expected_keywords in test_cases:
        print(f"Query: '{query}'")
        print(f"Expected keywords: {expected_keywords}")
        
        results = search_templates(query, top_k=3, min_score=0.25)
        
        if results:
            print(f"Found {len(results)} results:")
            for i, r in enumerate(results, 1):
                caption_preview = r['text'][:60] + "..." if len(r['text']) > 60 else r['text']
                print(f"  {i}. (score: {r['score']:.3f}) [{r['template_id']}] {caption_preview}")
                
                # Check if any expected keyword appears in results
                found_keyword = False
                for keyword in expected_keywords:
                    if keyword.lower() in r['text'].lower() or keyword.lower() in r.get('title', '').lower():
                        found_keyword = True
                        break
                
                if found_keyword:
                    passed += 1
                else:
                    failed += 1
        else:
            print("  No results found")
            failed += 1
        
        print()
    
    # Summary
    print("=" * 70)
    print("TEST SUMMARY")
    print("=" * 70)
    total_tests = len(test_cases) * 3  # 3 results per query
    print(f"  Total checks: {total_tests}")
    print(f"  Passed: {passed}")
    print(f"  Failed: {failed}")
    if total_tests > 0:
        print(f"  Pass rate: {passed/total_tests*100:.1f}%")
    print()
    
    return failed == 0


def test_specific_queries():
    """Test specific example queries from requirements."""
    print("=" * 70)
    print("Required Example Queries Test")
    print("=" * 70)
    print()
    
    examples = [
        ("I failed my exam", "exam/stress memes"),
        ("Birthday party vibes", "birthday/celebration memes"),
    ]
    
    for query, expected_type in examples:
        print(f"Query: '{query}' (expecting {expected_type})")
        
        results = search_templates(query, top_k=5, min_score=0.25)
        
        if results:
            print(f"  Found {len(results)} relevant templates:")
            for r in results[:3]:
                caption_preview = r['text'][:50] + "..." if len(r['text']) > 50 else r['text']
                print(f"    - [{r['template_id']}] {caption_preview}")
            print("  [PASS] Results found")
        else:
            print("  [FAIL] No results found")
        
        print()
    
    return True


if __name__ == "__main__":
    # Run specific examples first
    test_specific_queries()
    
    # Run full test suite
    success = test_semantic_search()
    
    # Exit with appropriate code
    sys.exit(0 if success else 1)
