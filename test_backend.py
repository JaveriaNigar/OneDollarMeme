import sys
import os
sys.path.append(os.path.join(os.path.dirname(__file__), 'meme_agent'))

# Test import of required modules
try:
    from meme_agent.main import meme_agent, config, Runner
    from meme_agent.api import generate_memes
    print("✓ All required modules imported successfully")
except ImportError as e:
    print(f"✗ Import error: {e}")
    sys.exit(1)

# Test basic functionality
async def test_generation():
    try:
        result = await generate_memes(
            topic="coding",
            style="relatable",
            tone="funny", 
            template_choice="AUTO"
        )
        print("✓ Basic generation test passed")
        print(f"Sample result: {result[:100]}...")
    except Exception as e:
        print(f"✗ Generation test failed: {e}")

if __name__ == "__main__":
    import asyncio
    asyncio.run(test_generation())