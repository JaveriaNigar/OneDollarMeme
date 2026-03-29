# Vector Search Implementation Plan
## Semantic Meme Retrieval with FAISS + Sentence Transformers

---

## Overview

Upgrade the current simple string matching search in `data_loader.py` to full **semantic vector search** using **FAISS** + **sentence-transformers**, while maintaining the hybrid approach (vector → keyword → web search fallback).

---

## Files to Create

| File | Purpose |
|------|---------|
| `meme_agent/embeddings.py` | Generate embeddings using `all-MiniLM-L6-v2` model (lightweight, fast) |
| `meme_agent/vector_store.py` | FAISS index management – build/load index, add embeddings, search (cosine similarity) |
| `meme_agent/scripts/build_vector_index.py` | Script to precompute embeddings for all meme captions |
| `storage/vector_index/` | Directory for FAISS index files |

---

## Files to Modify

| File | Changes |
|------|---------|
| `meme_agent/requirements.txt` | Add: `faiss-cpu`, `sentence-transformers`, `scikit-learn`, `torch` |
| `backend/requirements.txt` | Add: `faiss-cpu`, `sentence-transformers`, `scikit-learn`, `torch` |
| `meme_agent/data_loader.py` | Add `semantic_search()` and `hybrid_search()` functions; update search logic |
| `meme_agent/api.py` | Replace keyword-based local search with `hybrid_search()` |

---

## Implementation Steps

### Step 1: Install Dependencies
```bash
pip install faiss-cpu sentence-transformers scikit-learn torch
```

**Estimated effort:** 2-5 minutes (download time depends on connection)

---

### Step 2: Create `meme_agent/embeddings.py`

**Purpose:** Centralized embedding generation using sentence-transformers.

**Key functions:**
- `get_embedding_model()` – Load/cache the `all-MiniLM-L6-v2` model
- `generate_embeddings(texts: List[str], batch_size=32)` – Generate embeddings in batches to avoid OOM
- `generate_query_embedding(query: str)` – Generate single embedding for search queries

**Model choice rationale:**
- `all-MiniLM-L6-v2`: 80MB, fast inference, good quality for semantic search
- Runs on CPU efficiently
- No GPU required

**Estimated effort:** 15 minutes

---

### Step 3: Create `meme_agent/vector_store.py`

**Purpose:** FAISS index management for efficient similarity search.

**Key functions:**
- `VectorStore` class with:
  - `__init__(index_path, id_map_path)` – Initialize with storage paths
  - `load_index()` – Load existing FAISS index and ID mapping
  - `build_index(embeddings, ids)` – Build new index from embeddings
  - `save_index()` – Persist index and ID mapping to disk
  - `search(query_embedding, top_k=10)` – Cosine similarity search, return `(ids, scores)`
  - `is_empty()` – Check if index has any vectors

**Index configuration:**
- Index type: `IndexFlatIP` (inner product = cosine similarity after normalization)
- Dimension: 384 (from `all-MiniLM-L6-v2`)

**Estimated effort:** 20 minutes

---

### Step 4: Create `meme_agent/scripts/build_vector_index.py`

**Purpose:** Precompute embeddings for all meme captions in the dataset.

**Workflow:**
1. Scan `ImgFlip575K_Dataset/dataset/memes/*.json`
2. Extract captions (boxes) + metadata.title for each meme
3. Create unique meme IDs (e.g., `Drake-Hotline-Bling_0`, `Drake-Hotline-Bling_1`)
4. Generate embeddings in batches (batch_size=64)
5. Build FAISS index
6. Save to `storage/vector_index/index.faiss` + `id_map.json`

**Safety features:**
- Progress logging
- Error handling for corrupted JSON files
- Resume capability (skip if index exists)

**Estimated effort:** 30 minutes

**Runtime estimate:** ~10-20 minutes for 575K memes (depends on CPU)

---

### Step 5: Update `meme_agent/data_loader.py`

**New functions:**
- `semantic_search(query: str, top_k=10)` – Pure vector search
- `hybrid_search(query: str, top_k=10)` – Vector first, fallback to keyword, then web search
- `_format_search_result(meme_data, score, source)` – Standardize output format

**Modified functions:**
- `search_memes()` – Now calls `hybrid_search()` internally for backward compatibility

**Output format:**
```python
{
    "caption": "text from boxes",
    "image_url": "https://...",
    "source": "local_vector" | "local_keyword" | "web",
    "score": 0.85,  # cosine similarity score
    "template_id": "Drake-Hotline-Bling"
}
```

**Estimated effort:** 30 minutes

---

### Step 6: Update `meme_agent/api.py`

**Changes:**
- Replace `data_loader.search_memes(keyword, limit=10)` with `data_loader.hybrid_search(keyword, top_k=10)`
- Update item processing to handle new dict format instead of string format
- Maintain vision analysis step (now receives URLs directly)
- Keep ranking pipeline unchanged

**Estimated effort:** 20 minutes

---

### Step 7: Add Safety & Logging

**Features:**
- Handle empty index gracefully (auto-fallback to keyword)
- Handle model loading errors (retry, then fallback)
- Log search time and result counts
- Add configuration for score thresholds

**Estimated effort:** 10 minutes

---

## Total Estimated Effort

| Phase | Time |
|-------|------|
| Dependencies | 5 min |
| embeddings.py | 15 min |
| vector_store.py | 20 min |
| build_vector_index.py | 30 min |
| data_loader.py updates | 30 min |
| api.py updates | 20 min |
| Safety & testing | 20 min |
| **Total** | **~2.5 hours** |

**Note:** Initial index build is a one-time operation (~10-20 min runtime).

---

## Testing Checklist

- [ ] Embeddings generate correctly for sample texts
- [ ] FAISS index builds without errors
- [ ] Vector search returns relevant results
- [ ] Fallback to keyword search works when vector returns nothing
- [ ] Fallback to web search works when both local methods fail
- [ ] Vision analysis still works on retrieved candidates
- [ ] Ranking pipeline produces valid JSON output
- [ ] API endpoint returns correct format
- [ ] Error handling for missing index file
- [ ] Error handling for model loading failures

---

## Questions for User

1. **Index rebuild strategy:** Should the index be rebuilt automatically when new memes are added, or is manual rebuild acceptable for now?

2. **Score threshold:** What minimum cosine similarity score should trigger fallback to keyword search? (Suggested: 0.3-0.4)

3. **Batch size for embedding generation:** Default 64 is safe for most systems. Do you have memory constraints I should account for?

4. **Storage location:** Is `storage/vector_index/` the correct location, or should it be inside `ImgFlip575K_Dataset/`?

---

## Terminal Commands (Ready to Execute)

```bash
# 1. Install dependencies
pip install faiss-cpu sentence-transformers scikit-learn torch

# 2. Create storage directory
mkdir storage\vector_index

# 3. Create scripts directory
mkdir meme_agent\scripts
```

---

## Next Steps After Approval

1. Run pip install commands
2. Create `embeddings.py`
3. Create `vector_store.py`
4. Create `scripts/build_vector_index.py`
5. Update `data_loader.py`
6. Update `api.py`
7. Run index build script
8. Test end-to-end flow

---

**Awaiting your approval to proceed with implementation.**
