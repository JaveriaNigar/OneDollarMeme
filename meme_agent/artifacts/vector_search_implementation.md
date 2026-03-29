# Vector Search Implementation - Complete

## Overview

Successfully implemented semantic vector search for meme retrieval using **FAISS** + **sentence-transformers**, with a hybrid approach (vector → keyword → web search fallback).

---

## Files Created

### Core Modules

| File | Purpose |
|------|---------|
| `meme_agent/embeddings.py` | Embedding generation using `all-MiniLM-L6-v2` model |
| `meme_agent/vector_store.py` | FAISS index management (build, load, save, search) |
| `meme_agent/scripts/build_vector_index.py` | Script to precompute embeddings for all memes |
| `meme_agent/scripts/test_vector_search.py` | Test suite for vector search functionality |

### Directories

| Directory | Purpose |
|-----------|---------|
| `storage/vector_index/` | FAISS index and metadata storage |
| `meme_agent/scripts/` | Utility scripts |

---

## Files Modified

| File | Changes |
|------|---------|
| `meme_agent/requirements.txt` | Added: `faiss-cpu`, `sentence-transformers`, `scikit-learn`, `torch` |
| `backend/requirements.txt` | Added: `faiss-cpu`, `sentence-transformers`, `scikit-learn`, `torch` |
| `meme_agent/data_loader.py` | Added `semantic_search()`, `hybrid_search()`, `search_memes_structured()`; updated `search_memes()` to use hybrid |
| `meme_agent/api.py` | Updated to use `search_memes_structured()`; enhanced vision analysis to handle dict format |

---

## Key Features

### 1. Embedding Generation (`embeddings.py`)

```python
from embeddings import generate_embeddings, generate_query_embedding

# Batch embedding generation
embeddings = generate_embeddings(texts, batch_size=64, show_progress=True)

# Single query embedding
query_emb = generate_query_embedding("exam stress")
```

**Model:** `all-MiniLM-L6-v2`
- 384 dimensions
- ~80MB size
- Fast CPU inference
- L2-normalized for cosine similarity

### 2. Vector Store (`vector_store.py`)

```python
from vector_store import VectorStore

store = VectorStore(
    index_path="storage/vector_index/index.faiss",
    id_map_path="storage/vector_index/id_map.json"
)

# Load existing index
if store.load_index():
    ids, scores = store.search(query_emb, top_k=10, score_threshold=0.35)
```

**Features:**
- FAISS `IndexFlatIP` (inner product = cosine similarity for normalized vectors)
- ID mapping (FAISS index ↔ meme ID)
- Metadata storage
- Incremental add support

### 3. Hybrid Search (`data_loader.py`)

**Search Priority:**
1. **Vector Search** - Semantic similarity on captions + titles
2. **Keyword Search** - Fallback if vector returns < 3 results
3. **Web Search** - (In api.py) Fallback if both local methods fail

```python
from data_loader import hybrid_search, semantic_search, keyword_search

# Hybrid search (recommended)
results = hybrid_search("exam stress", top_k=10)

# Pure vector search
results = semantic_search("monday mood", top_k=5)

# Pure keyword search
results = keyword_search("birthday", limit=10)
```

**Output Format:**
```python
{
    "caption": "text from boxes",
    "image_url": "https://...",
    "post_url": "https://...",
    "template_id": "Drake-Hotline-Bling",
    "title": "meme title",
    "source": "local_vector" | "local_keyword" | "web",
    "score": 0.85
}
```

### 4. API Integration (`api.py`)

- Uses `search_memes_structured()` for structured output
- Vision analysis enhanced to handle both dict and string formats
- Backward compatible with existing code

---

## Usage

### Step 1: Install Dependencies

```bash
pip install faiss-cpu sentence-transformers scikit-learn torch
```

### Step 2: Build Vector Index

```bash
cd meme_agent
python scripts/build_vector_index.py
```

**Expected output:**
```
============================================================
Meme Vector Index Builder
============================================================

Loading meme data from .../ImgFlip575K_Dataset/dataset/memes...
Found 97 meme template files
  Processed 10/97 files...
  ...
Loaded XXXXX memes from 97 templates

Generating embeddings for XXXXX memes...
Model: all-MiniLM-L6-v2
Batch size: 64
Embedding generation completed in XXX.Xs
...
Index built successfully!
  Total vectors: XXXXX
```

**Runtime:** ~10-20 minutes for 575K memes (CPU-dependent)

### Step 3: Test Vector Search

```bash
python scripts/test_vector_search.py
```

### Step 4: Use in Application

The API automatically uses hybrid search when available:

```python
# In api.py, this now uses vector search:
from data_loader import search_memes_structured

results = search_memes_structured("exam stress", limit=10)
```

---

## Configuration

### Score Threshold

In `data_loader.py`:
```python
VECTOR_SCORE_THRESHOLD = 0.35  # Minimum cosine similarity for valid results
```

Adjust based on your needs:
- **Higher (0.5+)**: More precise, fewer results
- **Lower (0.2)**: More results, may include less relevant matches

### Batch Size

In `scripts/build_vector_index.py`:
```python
BATCH_SIZE = 64  # Adjust based on available RAM
```

### Fallback Threshold

In `data_loader.py`:
```python
def hybrid_search(query, top_k=10, vector_threshold=3):
    # If vector returns < 3 results, try keyword fallback
```

---

## Output Files

After building the index:

| File | Description |
|------|-------------|
| `storage/vector_index/index.faiss` | FAISS index file |
| `storage/vector_index/id_map.json` | FAISS index → meme ID mapping |
| `storage/vector_index/metadata.json` | Index metadata (count, model, build time) |
| `storage/vector_index/meme_data.json` | Meme data lookup table |

---

## Troubleshooting

### Module Import Errors

```bash
# Verify installations
python -c "import faiss; import sklearn; from sentence_transformers import SentenceTransformer"
```

### Index Not Found

```
Warning: Vector index not found or failed to load
```

**Solution:** Run `python scripts/build_vector_index.py`

### Out of Memory

Reduce batch size in `build_vector_index.py`:
```python
BATCH_SIZE = 32  # or 16 for low-memory systems
```

### Slow Search

- Ensure index is loaded (not rebuilding each time)
- Vector search should be < 100ms for 575K vectors
- First search includes model load (~5 seconds)

---

## Performance

| Operation | Expected Time |
|-----------|---------------|
| Index build (575K memes) | 10-20 min |
| First search (with model load) | ~5 sec |
| Subsequent searches | < 100 ms |
| Embedding generation | ~500 samples/sec |

---

## Next Steps

1. **Run index build:** `python scripts/build_vector_index.py`
2. **Test search:** `python scripts/test_vector_search.py`
3. **Test API:** Start your server and test meme generation
4. **Monitor:** Check `storage/vector_index/metadata.json` for index stats

---

## Architecture Diagram

```
User Query
    │
    ▼
┌─────────────────┐
│  hybrid_search  │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
    ▼         │
┌─────────┐   │
│ Vector  │   │
│ Search  │   │
│ (FAISS) │   │
└────┬────┘   │
     │        │
     │ < 3 results?
     │        │
     └───Yes──┘
         │
         ▼
    ┌─────────┐
    │ Keyword │
    │ Search  │
    └────┬────┘
         │
         ▼
    ┌─────────┐
    │  Rank & │
    │ Return  │
    └─────────┘
```

---

## Implementation Complete ✓

All code is ready. Run the index build script to enable semantic search.
