import sys
import os

# Add parent directory to path
sys.path.append(os.path.abspath(os.path.join(os.getcwd(), 'meme_agent')))

try:
    import api
    print("Import successful")
    print("Generate memes function available:", callable(api.generate_memes))
except Exception as e:
    print("Import failed:", e)
