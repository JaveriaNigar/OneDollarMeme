import requests
import json

BASE_URL = "http://127.0.0.1:8000"

def test_endpoints():
    print("Testing Meme Agent Backend API...\n")
    
    # Test root endpoint
    print("1. Testing root endpoint...")
    response = requests.get(f"{BASE_URL}/")
    print(f"Status: {response.status_code}")
    print(f"Response: {response.json()}\n")
    
    # Test styles endpoint
    print("2. Testing styles endpoint...")
    response = requests.get(f"{BASE_URL}/styles")
    print(f"Status: {response.status_code}")
    print(f"Styles: {response.json()['styles']}\n")
    
    # Test tones endpoint
    print("3. Testing tones endpoint...")
    response = requests.get(f"{BASE_URL}/tones")
    print(f"Status: {response.status_code}")
    print(f"Tones: {response.json()['tones']}\n")
    
    # Test chat endpoint
    print("4. Testing chat endpoint...")
    payload = {
        "message": "working from home",
        "style": "relatable",
        "tone": "funny"
    }
    response = requests.post(f"{BASE_URL}/chat", json=payload)
    print(f"Status: {response.status_code}")
    data = response.json()
    print(f"Memes generated: {len(data['data']['memes'])}")
    print(f"Sample meme: {data['data']['memes'][0] if data['data']['memes'] else 'None'}\n")
    
    # Test generate endpoint
    print("5. Testing generate endpoint...")
    payload = {
        "message": "weekend",
        "style": "desi",
        "tone": "funny"
    }
    response = requests.post(f"{BASE_URL}/generate", json=payload)
    print(f"Status: {response.status_code}")
    data = response.json()
    print(f"Memes generated: {len(data['data']['memes'])}")
    print(f"Sample meme: {data['data']['memes'][0] if data['data']['memes'] else 'None'}\n")
    
    print("All tests completed successfully! 🎉")

if __name__ == "__main__":
    test_endpoints()