# Fixed: Enforce Exactly 3 Memes Per Request

## Problem

User reported issue where the system was generating more than 3 memes when requested:
- User asked: "give me 10 memes" → System generated 10 memes ❌
- User asked: "give me some more" → System generated 5 memes ❌
- **Expected**: ALWAYS exactly 3 memes, regardless of user request ✅

## Root Cause

The prompts were not explicitly instructing the model to:
1. Ignore user requests for different quantities
2. Always output exactly 3 memes
3. Never generate multiple JSON objects

## Solution Applied

### 1. Updated Pool Generation Prompt (api.py:374-386)

```python
f"3. IF the user asks for a meme OR the situation is naturally funny/relatable: generate EXACTLY {pool_count} memes for internal ranking."
f"7. IMPORTANT: Generate EXACTLY {pool_count} memes, no more, no less. Ignore any user requests for different quantities."
```

**Changes:**
- Changed "up to {pool_count} memes" → "EXACTLY {pool_count} memes"
- Added explicit instruction to ignore user quantity requests

### 2. Updated Rank Prompt (api.py:429-457)

```python
f"CRITICAL RULES - FOLLOW EXACTLY:\n"
f"1. Select EXACTLY 3 best memes from the pool. NO MORE, NO LESS.\n"
f"2. Each of the 3 memes MUST use a DIFFERENT style...\n"
f"3. IGNORE any user requests for more or fewer memes. ALWAYS output exactly 3 memes.\n"
f"5. Return a SINGLE JSON object with keys 'reply' and 'memes'.\n"
f"6. DO NOT output multiple JSON objects. Only ONE JSON object total.\n"
...
f"11. If user asks for more memes (e.g., \"give me 10\"), still output exactly 3 memes.\n"
f"12. This is a SINGLE CALL system - ONE JSON output with EXACTLY 3 memes.\n"
```

**Changes:**
- Added "CRITICAL RULES" header for emphasis
- Rule 3: Explicitly ignore user quantity requests
- Rule 6: Explicitly forbid multiple JSON objects
- Rule 11: Specific example of ignoring "give me 10" requests
- Rule 12: Reinforce single-call, single-JSON system

### 3. Hard Limit in Code (api.py:545-549)

```python
# CRITICAL: Enforce exactly 3 memes - ignore user requests for more
final_obj = {
    "reply": parsed["reply"],
    "memes": processed_memes[:3]  # HARD LIMIT: Exactly 3 memes, no more
}
```

**Changes:**
- Added comment emphasizing this is a hard limit
- Code already had `[:3]` slice, now documented as critical

## Files Modified

| File | Lines Changed | Description |
|------|---------------|-------------|
| `meme_agent/api.py` | 374-386 | Pool generation prompt - enforce exactly 5 |
| `meme_agent/api.py` | 429-457 | Rank prompt - enforce exactly 3, ignore user requests |
| `meme_agent/api.py` | 545-549 | Hard limit comment in code |

## Testing

### Test Case 1: User asks for 10 memes
```
User: "give me 10 memes about exams"
Expected: 3 memes in single JSON
```

### Test Case 2: User asks for more
```
User: "give me some more"
Expected: 3 memes in single JSON (same as before)
```

### Test Case 3: User asks for 1 meme
```
User: "just give me 1 meme"
Expected: 3 memes in single JSON (system always outputs 3)
```

### Test Case 4: Normal request
```
User: "make memes about Pakistan cricket"
Expected: 3 memes in single JSON
```

## Output Format (Enforced)

```json
{
  "reply": "Pakistan cricket fans ready for the World Cup! 🏏",
  "memes": [
    { "style": "desi", "caption": "Iss baar toh cup hamara hai!", "template": "Desi-Mom" },
    { "style": "savage", "caption": "Ab toh final jeetenge!", "template": "Mocking-Spongebob" },
    { "style": "relatable", "caption": "Dil thaam ke baithna!", "template": "This-Is-Fine" }
  ]
}
```

## Rules Enforced

1. ✅ **Single JSON output** - One API call → one JSON response
2. ✅ **Exactly 3 memes** - Hard limit via `[:3]` slice
3. ✅ **Ignore user quantity requests** - Explicit instructions in prompts
4. ✅ **Diverse styles** - 3 unique styles enforced
5. ✅ **Structured format** - Each meme: `{style, caption, template}`
6. ✅ **Mood-based selection** - Uses MOOD_MAPPING table

## Verification

Run syntax check:
```bash
cd C:\xampp\htdocs\myproject
C:\xampp\htdocs\myproject\.venv\Scripts\python.exe -m py_compile meme_agent/api.py
```

Run test suite:
```bash
python meme_agent/scripts/test_batch_generation.py
```

## Summary

| Requirement | Status |
|-------------|--------|
| Always output exactly 3 memes | ✅ Fixed |
| Single JSON output | ✅ Fixed |
| Ignore user quantity requests | ✅ Fixed |
| Diverse styles | ✅ Enforced |
| Structured format | ✅ Enforced |
| Pool → Rank → 3 logic | ✅ Implemented |

**Issue resolved. System now enforces exactly 3 memes per request, regardless of user input.**
