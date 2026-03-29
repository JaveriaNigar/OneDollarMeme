#!/usr/bin/env python
"""Test script to verify topic-aware meme replies"""

import requests
import json
import sys

# Ensure UTF-8 encoding
sys.stdout.reconfigure(encoding='utf-8')

def test_meme_request(topic):
    """Test a meme request and check if reply is topic-aware"""
    url = "http://127.0.0.1:8003/chat"
    
    payload = {
        "message": topic,
        "style": "relatable",
        "tone": "funny",
        "template": "AUTO",
        "user_id": "test_user"
    }
    
    print(f"\n{'='*60}")
    print(f"Testing topic: {topic}")
    print(f"{'='*60}")
    
    try:
        response = requests.post(url, json=payload, timeout=120)
        response.raise_for_status()
        
        data = response.json()
        print(f"\n[OK] Backend Response:")
        print(json.dumps(data, indent=2, ensure_ascii=False))
        
        # Check structure
        if "reply" in data:
            reply = data["reply"]
            print(f"\n[REPLY] {reply}")
            
            # Check if reply is topic-aware
            topic_words = topic.lower().split()
            reply_lower = reply.lower()
            
            # Check for generic placeholders
            generic_phrases = [
                "here are your memes",
                "meme hazir",
                "ye lo memes",
                "ye rahe memes",
                "here are memes"
            ]
            
            is_generic = any(phrase in reply_lower for phrase in generic_phrases)
            
            # Check if topic is referenced
            topic_referenced = any(word in reply_lower for word in topic_words if len(word) > 3)
            
            print(f"\n[ANALYSIS]")
            print(f"   - Contains generic placeholder: {'YES (BAD)' if is_generic else 'NO (GOOD)'}")
            print(f"   - Topic referenced in reply: {'YES' if topic_referenced else 'NO (but may be okay if witty)'}")
        
        # Check memes
        if "memes" in data:
            memes = data["memes"]
            print(f"\n[MEME COUNT] {len(memes)}")
            
            if len(memes) != 3:
                print(f"   [WARNING] Expected exactly 3 memes, got {len(memes)}")
            else:
                print(f"   [OK] Exactly 3 memes returned")
            
            for i, meme in enumerate(memes, 1):
                if isinstance(meme, dict):
                    print(f"\n   [MEME {i}]")
                    print(f"      Style: {meme.get('style', 'N/A')}")
                    print(f"      Caption: {meme.get('caption', 'N/A')}")
                    print(f"      Template: {meme.get('template', 'N/A')}")
        
        return data
        
    except requests.exceptions.Timeout:
        print("[ERROR] Request timed out (120s)")
        return None
    except requests.exceptions.RequestException as e:
        print(f"[ERROR] Request failed: {e}")
        return None

if __name__ == "__main__":
    # Test cases
    test_topics = [
        "desi siblings fighting for wifi",
        "exam stress",
        "monday morning"
    ]
    
    for topic in test_topics:
        test_meme_request(topic)
        print("\n")
