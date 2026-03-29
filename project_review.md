# Meme Agent Project Review & Analysis

**Date:** February 23, 2026  
**Project:** Hinglish Meme Generator Agent (OneDollarMeme)

---

## 1. Project Structure Overview

### 1.1 Directory Layout

```
myproject -new/
├── meme_agent/                 # Core Python Meme Agent
│   ├── __init__.py
│   ├── core.py                 # Main agent logic, model config, guardrails
│   ├── api.py                  # API generation logic with retrieval + vision
│   ├── server.py               # FastAPI server (port 8003) with chat/memory
│   ├── data_loader.py          # Dataset loading & search functions
│   ├── vision_analyzer.py      # OpenAI Vision API for image analysis
│   ├── web_search_tool.py      # Reddit & Twitter/X API integration
│   ├── debug_agent.py          # Agent inspection utility
│   ├── main.py                 # DEPRECATED (CLI interface)
│   └── requirements.txt        # Python dependencies
│
├── backend/                    # Alternative Backend Implementation
│   ├── main.py                 # High-traffic API with background tasks
│   ├── server.py               # Server runner
│   ├── direct_api.py           # Direct API endpoint
│   ├── minimal_main.py         # Minimal implementation
│   ├── simple_api.py           # Simple API version
│   ├── clean_main.py           # Cleaned implementation
│   └── requirements.txt        # Backend dependencies
│
├── ImgFlip575K_Dataset/        # Meme Dataset (575K memes)
│   ├── dataset/
│   │   ├── templates/          # 100 template JSON files + img/
│   │   ├── memes/              # 100 meme caption JSON files
│   │   ├── popular_100_memes.csv
│   │   └── statistics.json     # Meme counts per template
│   ├── imgflip_scraper/        # Scrapy scraper code
│   └── requirements.txt
│
├── app/                        # Laravel Application
│   ├── Console/
│   ├── Http/
│   ├── Models/
│   └── Providers/
│
├── data/                       # Shared Data
│   └── conversations.json      # Chat history storage
│
├── storage/
│   └── app/
│       └── meme_agent_feedback.jsonl  # User feedback data
│
├── database/
│   └── database.sqlite         # SQLite database
│
├── config/                     # Laravel Configuration
├── routes/                     # Laravel Routes
├── resources/                  # Laravel Resources (views, assets)
├── public/                     # Public assets
├── tests/                      # Test files
│
├── .venv/                      # Python Virtual Environment
├── node_modules/               # Node.js dependencies
│
├── composer.json               # PHP dependencies
├── package.json                # Node.js dependencies
├── docker-compose.yml
├── Dockerfile
├── nginx.conf
└── .env                        # Environment variables
```

### 1.2 Main Files Summary

| File/Folder | Purpose | Status |
|-------------|---------|--------|
| `meme_agent/core.py` | Agent definition, model config, guardrails, style/tone defs | ✅ Active |
| `meme_agent/api.py` | Meme generation with retrieval, vision, ranking | ✅ Active |
| `meme_agent/server.py` | FastAPI chat server with memory | ✅ Active (port 8003) |
| `backend/main.py` | High-traffic API with background jobs, caching | ✅ Active (port 8003) |
| `meme_agent/data_loader.py` | Template loading, example retrieval, search | ✅ Active |
| `meme_agent/vision_analyzer.py` | Image analysis via OpenAI Vision | ✅ Active |
| `meme_agent/web_search_tool.py` | Reddit & Twitter/X search | ✅ Active |
| `ImgFlip575K_Dataset/` | 575K meme dataset (100 templates loaded) | ✅ Available |
| `meme_agent/main.py` | Original CLI interface | ⚠️ DEPRECATED |

---

## 2. Core Architecture Analysis

### 2.1 High-Level Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                        USER REQUEST                              │
│  (topic, style, tone, template)                                  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    API LAYER (server.py / main.py)               │
│  - FastAPI endpoints (/chat, /meme-agent, /generate-meme)        │
│  - Rate limiting, caching, background jobs                       │
│  - User history management                                       │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                  GENERATION PIPELINE (api.py)                    │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  1. KEYWORD EXTRACTION                                    │   │
│  │     - OpenAI extracts searchable keyword from topic       │   │
│  └──────────────────────────────────────────────────────────┘   │
│                              │                                   │
│                              ▼                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  2. LOCAL DATASET SEARCH (Priority 1)                     │   │
│  │     - Search ImgFlip575K captions/metadata                │   │
│  │     - Returns: "caption text\nimage_url"                  │   │
│  └──────────────────────────────────────────────────────────┘   │
│                              │                                   │
│              ┌───────────────┴───────────────┐                  │
│              │ Found?                        │ Not Found?       │
│              ▼                               ▼                  │
│  ┌─────────────────────┐       ┌─────────────────────────────┐  │
│  │ Use Local Results   │       │ 3. WEB SEARCH (Priority 2)  │  │
│  │ source="local"      │       │    - Reddit API (r/memes)   │  │
│  └─────────────────────┘       │    - Twitter/X API          │  │
│              │                 │    source="web_search"      │  │
│              │                 └─────────────────────────────┘  │
│              │                              │                   │
│              └───────────────┬──────────────┘                   │
│                              │                                   │
│              ┌───────────────┴───────────────┐                  │
│              │ Items Found?                  │ No Items?        │
│              ▼                               ▼                  │
│  ┌─────────────────────┐       ┌─────────────────────────────┐  │
│  │ 4. VISION ANALYSIS  │       │ 4. FALLBACK GENERATION      │  │
│  │    - OpenAI Vision  │       │    - LLM generates memes    │  │
│  │    - Extract text,  │       │    - Uses template dataset  │  │
│  │      emotion, humor │       │    - Style/tone constraints │  │
│  │    - Enrich items   │       └─────────────────────────────┘  │
│  └─────────────────────┘                   │                   │
│              │                             │                   │
│              └───────────────┬─────────────┘                   │
│                              │                                   │
│                              ▼                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  5. RANKING & FILTERING                                   │   │
│  │     - LLM ranks candidates                                │   │
│  │     - Selects top 3 memes                                 │   │
│  │     - Formats as JSON: {reply, memes[]}                   │   │
│  │     - Safety moderation check                             │   │
│  └──────────────────────────────────────────────────────────┘   │
│                              │                                   │
│                              ▼                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  6. RESPONSE                                              │   │
│  │     - JSON with reply + 3 meme texts                      │   │
│  │     - Saved to history/feedback                           │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Meme Handling Logic

#### Current Capabilities:

| Feature | Status | Description |
|---------|--------|-------------|
| **Finished Meme Search** | ✅ YES | Searches local ImgFlip575K dataset first, then Reddit/Twitter |
| **Caption Generation** | ✅ YES | Generates Hinglish captions for templates |
| **Template Selection** | ✅ YES | AUTO mode or specific template ID |
| **Vision Analysis** | ✅ YES | Analyzes retrieved images for template name, text, emotion |
| **Style/Tone Support** | ✅ YES | 6 styles × 5 tones combinations |
| **Batch Generation** | ⚠️ PARTIAL | Generates pool of 5, ranks to 3 |
| **Feedback Learning** | ⚚️ BASIC | Stores feedback in JSONL, uses for prompt examples |
| **Vector Search** | ❌ NO | Uses simple string matching only |

#### Retrieval vs Generation:

- **Hybrid Approach**: System tries retrieval FIRST (local → web), falls back to LLM generation
- **Local Dataset**: Searches caption text (`boxes` field) and metadata title
- **Web Search**: Reddit (r/memes) + Twitter/X with official APIs
- **Generation**: Uses OpenAI GPT-4o with style/tone/template constraints

### 2.3 Template & Dataset Usage

#### Dataset Statistics:
- **Total Memes in Dataset**: 575,948
- **Templates Available**: 100 (in `dataset/templates/`)
- **Top Templates by Count**:
  1. Bad Luck Brian: 32,141 memes
  2. Futurama Fry: 17,325 memes
  3. First World Problems: 17,272 memes
  4. Philosoraptor: 19,971 memes
  5. One Does Not Simply: 26,186 memes

#### Template Data Structure:
```json
{
  "title": "Drake Hotline Bling Meme Template",
  "template_url": "https://imgflip.com/s/meme/Drake-Hotline-Bling.jpg",
  "alternative_names": "drakeposting, drakepost, drake hotline approves...",
  "template_id": "181913649",
  "format": "jpg",
  "dimensions": "1200x1200 px",
  "file_size": "95 KB"
}
```

#### Meme Data Structure:
```json
{
  "url": "https://i.imgflip.com/3zbe8v.jpg",
  "post": "https://imgflip.com/i/3zbe8v",
  "metadata": {
    "views": "321",
    "img-votes": "2",
    "title": "straightnt",
    "author": "JasonVanBoening"
  },
  "boxes": ["gay", "straightn't"]
}
```

---

## 3. Dependencies & Libraries

### 3.1 Current Requirements

#### `meme_agent/requirements.txt`:
```
fastapi
uvicorn
openai
python-dotenv
pydantic
openai-agents
requests
beautifulsoup4
```

#### `backend/requirements.txt`:
```
fastapi
uvicorn[standard]
pydantic
python-multipart
python-dotenv
requests
beautifulsoup4
celery
redis
sqlalchemy
openai
openai-agents
google-search-results
```

### 3.2 Installed Packages Analysis

**Currently Installed (verified):**
- `numpy` (2.4.0) ✅
- `fastapi`, `uvicorn` ✅
- `openai`, `openai-agents` ✅
- `pydantic` ✅
- `requests`, `beautifulsoup4` ✅
- `sqlalchemy` ✅
- `celery`, `redis` ✅

**Missing / Not Installed:**
- ❌ `faiss-cpu` or `faiss-gpu` (Facebook AI Similarity Search)
- ❌ `sentence-transformers` (for embeddings)
- ❌ `torch` / `torchvision` (PyTorch)
- ❌ `transformers` (Hugging Face)
- ❌ `scikit-learn` (for cosine similarity, clustering)

### 3.3 Import Analysis

**Currently Used Imports:**
```python
from agents import (
    Agent, AsyncOpenAI, OpenAIChatCompletionsModel,
    RunConfig, Runner, set_tracing_disabled,
    RunContextWrapper, output_guardrail, GuardrailFunctionOutput
)
from openai import AsyncOpenAI  # Direct client for vision/moderation
from pydantic import BaseModel
import json, os, asyncio, re, random
```

**Vector/Embedding Libraries:**
- ❌ **NO** FAISS usage
- ❌ **NO** Sentence Transformers
- ❌ **NO** Any embedding generation
- ❌ **NO** Vector similarity search

**Current Search Method:**
```python
# Simple string matching in data_loader.py
if query in full_text.lower():
    matched = True
elif meme.get("metadata") and query in meme["metadata"].get("title", "").lower():
    matched = True
```

### 3.4 Dependency Recommendations

**Required for Vector Search Enhancement:**
```bash
pip install faiss-cpu sentence-transformers scikit-learn
```

**Optional (for advanced features):**
```bash
pip install transformers torch torchvision
```

**Run existing dependencies:**
```bash
cd meme_agent && pip install -r requirements.txt
cd backend && pip install -r requirements.txt
```

---

## 4. Enhancement Blueprint (Based on OneDollarMeme Documentation)

### 4.1 Planned Features & Required Changes

#### **1. Vector & Vector Search** 🔴 HIGH PRIORITY

**What's Needed:**
- Generate embeddings for all meme captions
- Store embeddings in FAISS index
- Implement semantic similarity search

**Files to Modify:**
| File | Changes |
|------|---------|
| `meme_agent/data_loader.py` | Add `generate_embeddings()`, `build_faiss_index()`, `semantic_search()` |
| `meme_agent/requirements.txt` | Add `faiss-cpu`, `sentence-transformers`, `scikit-learn` |
| `meme_agent/api.py` | Replace `search_memes()` with vector search in pipeline |

**New Files to Create:**
| File | Purpose |
|------|---------|
| `meme_agent/vector_store.py` | FAISS index management (save/load, add, search) |
| `meme_agent/embeddings.py` | Embedding generation using sentence-transformers |
| `storage/vector_index/` | Directory for FAISS index files |

**High-Level Steps:**
1. Install vector dependencies
2. Precompute embeddings for all 575K meme captions
3. Build FAISS index (IVF Flat or HNSW for speed)
4. Replace string matching with cosine similarity search
5. Add hybrid search (keyword + semantic)

---

#### **2. Template Dataset Enhancement** 🟡 MEDIUM PRIORITY

**What's Needed:**
- Expand beyond 100 templates to full 575K
- Add template categories/tags
- Precompute template embeddings for AUTO selection

**Files to Modify:**
| File | Changes |
|------|---------|
| `meme_agent/data_loader.py` | Add `get_template_categories()`, `find_similar_templates()` |
| `ImgFlip575K_Dataset/` | Load all templates (currently only 100 JSON files exist) |

**New Files to Create:**
| File | Purpose |
|------|---------|
| `meme_agent/template_index.py` | Template categorization, similarity matching |
| `data/template_categories.json` | Mapping of templates to categories |

**High-Level Steps:**
1. Scrape/load remaining templates from ImgFlip API
2. Categorize templates (reaction, comparison, emotion, etc.)
3. Add template metadata embeddings for smarter AUTO selection
4. Implement template recommendation based on topic

---

#### **3. Humor Styles, Tone Modes, Mapping Table** 🟡 MEDIUM PRIORITY

**Current State:**
```python
STYLES = ["relatable", "savage", "wholesome", "desi", "absurd", "observational"]
TONES = ["sarcastic", "funny", "dark", "wholesome", "cringe"]
STYLE_DEFS = {...}  # Simple definitions
```

**What's Needed:**
- Detailed style/tone definitions with examples
- Style-tone compatibility matrix
- Few-shot examples per style-tone combination

**Files to Modify:**
| File | Changes |
|------|---------|
| `meme_agent/core.py` | Expand `STYLE_DEFS`, add `TONE_DEFS`, create `STYLE_TONE_MATRIX` |
| `meme_agent/api.py` | Use matrix for better prompt guidance |

**New Files to Create:**
| File | Purpose |
|------|---------|
| `data/humor_styles.json` | Comprehensive style/tone definitions + examples |
| `data/style_tone_mapping.json` | Which tones work best with which styles |

**High-Level Steps:**
1. Define detailed style/tone descriptions with Hinglish examples
2. Create compatibility matrix (e.g., "dark" + "savage" = ✅, "dark" + "wholesome" = ❌)
3. Add style-specific few-shot examples to prompts
4. Implement style transfer for caption rewriting

---

#### **4. Batch Generation** 🟢 LOW PRIORITY (Already Partially Implemented)

**Current State:**
- Generates pool of 5, ranks to 3

**What's Needed:**
- Configurable batch sizes
- Parallel generation for speed
- Diversity scoring to avoid similar outputs

**Files to Modify:**
| File | Changes |
|------|---------|
| `meme_agent/api.py` | Add `batch_size` parameter, parallel generation |
| `meme_agent/core.py` | Add diversity ranking in guardrails |

**New Files to Create:**
| File | Purpose |
|------|---------|
| `meme_agent/batch_generator.py` | Parallel batch processing with diversity |

**High-Level Steps:**
1. Add `batch_size` and `final_count` as API parameters
2. Implement parallel LLM calls for pool generation
3. Add diversity scoring (semantic similarity between candidates)
4. Select top-N diverse memes instead of just top-N quality

---

#### **5. Performance Optimizations** 🟡 MEDIUM PRIORITY

##### 5.1 Caching (✅ Already Implemented)
```python
# Current: In-memory cache with 1-hour TTL
meme_cache: Dict[str, Dict] = {}
```

**Enhancements Needed:**
- Redis-based distributed caching
- Embedding cache (avoid recomputing)
- Template embedding precomputation

##### 5.2 Async Processing (✅ Already Implemented)
```python
# Current: asyncio + BackgroundTasks
background_tasks.add_task(run_meme_generation_task, job_id, request)
```

**Enhancements Needed:**
- Celery for distributed task queue (✅ installed, not fully used)
- Request queuing with priority

##### 5.3 Precomputation
**What's Needed:**
- Precompute all template embeddings
- Precompute style-tone example embeddings
- Cache vision analysis results

**Files to Modify:**
| File | Changes |
|------|---------|
| `backend/main.py` | Integrate Redis cache properly |
| `meme_agent/data_loader.py` | Add precomputation scripts |

**New Files to Create:**
| File | Purpose |
|------|---------|
| `scripts/precompute_embeddings.py` | Batch embedding generation |
| `scripts/warm_cache.py` | Cache warming on startup |

---

#### **6. Limitations Fixes** 🔴 HIGH PRIORITY

| Current Limitation | Solution | Files to Modify |
|-------------------|----------|-----------------|
| **Simple string search** | Vector semantic search | `data_loader.py`, new `vector_store.py` |
| **Only 100 templates** | Load full 575K dataset | `data_loader.py`, dataset expansion |
| **No template categories** | Add categorization | New `template_index.py` |
| **Basic feedback system** | Implement learning loop | `api.py`, `core.py` |
| **No diversity in batch** | Add diversity scoring | New `batch_generator.py` |
| **In-memory state** | Redis for production | `backend/main.py`, `server.py` |
| **No rate limit in Redis** | Redis-based rate limiting | `backend/main.py` |
| **Single LLM call per rank** | Cache ranking results | `api.py` |

---

## 5. Summary & Next Steps

### 5.1 Current State Assessment

| Component | Status | Notes |
|-----------|--------|-------|
| **Basic Meme Generation** | ✅ Working | LLM generates Hinglish memes |
| **Retrieval (Local)** | ✅ Working | Simple string search in 100 templates |
| **Retrieval (Web)** | ✅ Working | Reddit + Twitter APIs |
| **Vision Analysis** | ✅ Working | OpenAI Vision for image understanding |
| **Style/Tone System** | ✅ Working | 6×5 combinations |
| **Vector Search** | ❌ Missing | Requires FAISS + sentence-transformers |
| **Full Dataset** | ⚠️ Partial | Only 100/575K templates loaded |
| **Batch Generation** | ⚠️ Basic | Pool of 5 → rank to 3 |
| **Caching** | ⚠️ Basic | In-memory, needs Redis |
| **Feedback Learning** | ⚠️ Basic | JSONL storage, simple examples |

### 5.2 Priority Order for Enhancements

1. **🔴 Vector Search Implementation** (Highest Priority)
   - Install dependencies
   - Build embedding pipeline
   - Replace string search with semantic search

2. **🔴 Dataset Expansion**
   - Load all 575K memes
   - Add template categories

3. **🟡 Style/Tone Enhancement**
   - Detailed definitions
   - Mapping table
   - Better few-shot examples

4. **🟡 Performance Optimization**
   - Redis integration
   - Precomputation scripts
   - Celery task queue

5. **🟢 Batch Generation Enhancement**
   - Diversity scoring
   - Parallel generation

6. **🟢 Advanced Features**
   - Template recommendation
   - Style transfer
   - Learning loop

### 5.3 Installation Commands Needed

```bash
# Navigate to project
cd "C:\xampp\htdocs\myproject -new"

# Install vector search dependencies
pip install faiss-cpu sentence-transformers scikit-learn

# Install existing requirements
cd meme_agent && pip install -r requirements.txt
cd ../backend && pip install -r requirements.txt

# Verify installations
pip list | findstr "faiss sentence scikit"
```

---

## 6. Architecture Diagram (Enhanced)

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         ENHANCED MEME AGENT                              │
└─────────────────────────────────────────────────────────────────────────┘

┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│   USER       │────▶│   FastAPI    │────▶│  Background  │
│   Request    │     │   Server     │     │   Job Queue  │
└──────────────┘     └──────────────┘     └──────────────┘
                            │                    │
                            │              ┌─────┴─────┐
                            │              │           │
                            ▼              ▼           ▼
                     ┌─────────────────────────────────────┐
                     │        GENERATION PIPELINE          │
                     │                                     │
                     │  ┌─────────────────────────────┐   │
                     │  │ 1. Keyword Extraction       │   │
                     │  └─────────────────────────────┘   │
                     │              │                      │
                     │              ▼                      │
                     │  ┌─────────────────────────────┐   │
                     │  │ 2. HYBRID SEARCH            │   │
                     │  │    ┌────────────┐           │   │
                     │  │    │ Vector     │           │   │
                     │  │    │ Search     │◀────┐     │   │
                     │  │    │ (FAISS)    │     │     │   │
                     │  │    └────────────┘     │     │   │
                     │  │    ┌────────────┐     │     │   │
                     │  │    │ Keyword    │     │     │   │
                     │  │    │ Search     │     │     │   │
                     │  │    └────────────┘     │     │   │
                     │  │         │             │     │   │
                     │  │         └──────┬──────┘     │   │
                     │  └────────────────┼────────────┘   │
                     │                   │                │
                     │              ┌────┴────┐          │
                     │              │ Found?  │          │
                     │              └────┬────┘          │
                     │          ┌────────┴────────┐      │
                     │          │                 │      │
                     │          ▼                 ▼      │
                     │    ┌──────────┐     ┌──────────┐  │
                     │    │ Vision   │     │ Fallback │  │
                     │    │ Analysis │     │ LLM Gen  │  │
                     │    └──────────┘     └──────────┘  │
                     │          │                 │      │
                     │          └────────┬────────┘      │
                     │                   │                │
                     │                   ▼                │
                     │  ┌─────────────────────────────┐   │
                     │  │ 3. Diversity Ranking        │   │
                     │  └─────────────────────────────┘   │
                     │                   │                │
                     │                   ▼                │
                     │  ┌─────────────────────────────┐   │
                     │  │ 4. Safety Moderation        │   │
                     │  └─────────────────────────────┘   │
                     └─────────────────────────────────────┘
                                      │
                                      ▼
                     ┌─────────────────────────────────────┐
                     │           CACHING LAYER             │
                     │  ┌─────────────┐  ┌──────────────┐  │
                     │  │ Redis Cache │  │ Embedding    │  │
                     │  │ (Responses) │  │ Cache        │  │
                     │  └─────────────┘  └──────────────┘  │
                     └─────────────────────────────────────┘
                                      │
                                      ▼
                     ┌─────────────────────────────────────┐
                     │          DATA LAYER                 │
                     │  ┌─────────────┐  ┌──────────────┐  │
                     │  │ FAISS Index │  │ SQLite/Redis │  │
                     │  │ (Vectors)   │  │ (History)    │  │
                     │  └─────────────┘  └──────────────┘  │
                     │  ┌─────────────────────────────┐   │
                     │  │ ImgFlip575K Dataset         │   │
                     │  │ (575K memes, 100 templates) │   │
                     │  └─────────────────────────────┘   │
                     └─────────────────────────────────────┘
```

---

**Ready for Next Phase:** Awaiting OneDollarMeme documentation to finalize enhancement specifications.
