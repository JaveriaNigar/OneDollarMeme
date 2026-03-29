import json
import time
import os
import requests

def search_reddit_memes(query: str, limit: int = 3) -> list:
    """
    Search Reddit for memes using the Official Authenticated API.
    Returns a list of strings (Title + URL).
    Requires REDDIT_CLIENT_ID, REDDIT_CLIENT_SECRET, REDDIT_USER_AGENT in .env.
    """
    client_id = os.getenv("REDDIT_CLIENT_ID")
    client_secret = os.getenv("REDDIT_CLIENT_SECRET")
    user_agent = os.getenv("REDDIT_USER_AGENT", "MemeAgent/1.0")

    if not client_id or not client_secret:
        print("Reddit API Credentials missing in .env")
        return []

    # 1. Get Access Token
    auth_url = "https://www.reddit.com/api/v1/access_token"
    auth = requests.auth.HTTPBasicAuth(client_id, client_secret)
    data = {"grant_type": "client_credentials"}
    headers = {"User-Agent": user_agent}

    try:
        token_resp = requests.post(auth_url, auth=auth, data=data, headers=headers, timeout=5)
        if token_resp.status_code != 200:
            print(f"Reddit Auth Failed: {token_resp.status_code}")
            return []
        
        token = token_resp.json().get("access_token")
        if not token:
             print("Reddit Access Token not found")
             return []

        # 2. Search API (Authenticated)
        api_url = "https://oauth.reddit.com/r/memes/search"
        search_headers = {
            "Authorization": f"bearer {token}",
            "User-Agent": user_agent
        }
        params = {
            "q": query,
            "restrict_sr": 1,
            "sort": "top",
            "limit": limit * 2 # fetch more to filter
        }

        response = requests.get(api_url, headers=search_headers, params=params, timeout=5)
        if response.status_code != 200:
            print(f"Reddit API Search Error: {response.status_code}")
            return []
            
        data = response.json()
        posts = data.get("data", {}).get("children", [])
        
        results = []
        for post in posts:
            p_data = post.get("data", {})
            title = p_data.get("title", "")
            url = p_data.get("url", "")
            post_hint = p_data.get("post_hint", "")
            
            # Filter to image posts only
            is_image = post_hint == "image" or url.endswith((".jpg", ".png", ".gif", ".jpeg"))
            if not is_image:
                continue
                
            # NSFW filter
            if p_data.get("over_18", False):
                continue
                
            # Construct result string
            meme_text = f"{title}\n{url}"
            if meme_text not in results:
                results.append(meme_text)
                
            if len(results) >= limit:
                break
                
        return results
        
    except Exception as e:
        print(f"Reddit Authenticated Search Error: {e}")
        return []

def search_twitter_memes(query: str, limit: int = 3) -> list:
    """
    Search X (Twitter) for memes using the Official Twitter API v2.
    Returns a list of strings (Text + Image URL).
    Requires X_BEARER_TOKEN in .env.
    """
    bearer_token = os.getenv("X_BEARER_TOKEN")
    if not bearer_token:
        print("X_BEARER_TOKEN missing in .env")
        return []

    # API Endpoint
    url = "https://api.twitter.com/2/tweets/search/recent"
    
    # Headers
    headers = {
        "Authorization": f"Bearer {bearer_token}",
        "User-Agent": "v2RecentSearchPython"
    }
    
    # Query parameters
    # We want tweets with images, not retweets
    search_query = f"{query} has:images -is:retweet lang:en"
    
    params = {
        "query": search_query,
        "max_results": min(100, max(10, limit * 3)), # API requires min 10
        "expansions": "attachments.media_keys",
        "media.fields": "url,type",
        "tweet.fields": "text"
    }
    
    try:
        response = requests.get(url, headers=headers, params=params, timeout=5)
        if response.status_code != 200:
             print(f"X API Error: {response.status_code} - {response.text}")
             return []
             
        json_response = response.json()
        
        # Parse logic
        # 1. Map media_keys to URLs
        media_map = {}
        if "includes" in json_response and "media" in json_response["includes"]:
            for m in json_response["includes"]["media"]:
                if m.get("type") == "photo" and "url" in m:
                    media_map[m["media_key"]] = m["url"]
        
        # 2. Iterate tweets
        results = []
        if "data" in json_response:
             for tweet in json_response["data"]:
                 text = tweet.get("text", "").replace("\n", " ").strip()
                 
                 # Check attachments
                 if "attachments" in tweet and "media_keys" in tweet["attachments"]:
                     for key in tweet["attachments"]["media_keys"]:
                         if key in media_map:
                             # Found an image!
                             img_url = media_map[key]
                             entry = f"{text}\n{img_url}"
                             
                             if entry not in results:
                                 results.append(entry)
                             # Break after first image per tweet to avoid duplicates
                             break 
                 
                 if len(results) >= limit:
                     break
                     
        return results

    except Exception as e:
        print(f"X Search Error: {e}")
        return []

def search_web_for_memes(query: str, limit: int = 3) -> list:
    """
    Web search tool wrapper.
    Integrates Reddit (Official API) and X/Twitter (Official API).
    """
    # 1. Reddit Search
    reddit_results = search_reddit_memes(query, limit)
    
    # 2. X (Twitter) Search
    twitter_results = search_twitter_memes(query, limit)
    
    # Combine results
    combined_results = reddit_results + twitter_results
    
    # Remove duplicates
    unique_results = list(dict.fromkeys(combined_results))
    
    return unique_results[:limit]
