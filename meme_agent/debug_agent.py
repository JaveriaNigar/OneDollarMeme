import asyncio
from core import meme_agent
from agents import Agent

async def inspect_agent():
    print(f"Agent Name: {meme_agent.name}")
    print(f"Input Guardrails count: {len(meme_agent.input_guardrails)}")
    for g in meme_agent.input_guardrails:
        print(f" - Input Guardrail: {g}")
    print(f"Output Guardrails count: {len(meme_agent.output_guardrails)}")
    for g in meme_agent.output_guardrails:
        print(f" - Output Guardrail: {g}")

if __name__ == "__main__":
    asyncio.run(inspect_agent())
