import asyncio
import os
import sys

# Ensure current dir is in path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from api import generate_memes

async def test():
    print("Testing 'school' topic...")
    try:
        result = await generate_memes("school", "relatable", "funny", "AUTO")
        print("\nSuccess! Output:")
        print(result)
    except Exception as e:
        print(f"\nCaught Exception: {type(e).__name__}: {e}")
        import traceback
        traceback.print_exc()

if __name__ == "__main__":
    asyncio.run(test())
