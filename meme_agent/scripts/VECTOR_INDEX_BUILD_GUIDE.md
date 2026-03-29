# Vector Index Build - Google Colab GPU Instructions

## Overview

Build the FAISS vector index on Google Colab GPU for **fast embedding generation** (2-4 hours vs 24-30 hours on CPU).

---

## Files Provided

| File | Purpose |
|------|---------|
| `colab_build_index.ipynb` | Complete Colab notebook - **USE THIS** |
| `build_index_gpu.py` | Standalone GPU build script |
| `COLAB_README.md` | Detailed instructions |
| `test_vector_search.py` | Test suite to verify after download |

---

## Quick Start (5 Minutes Setup)

### Step 1: Open Google Colab
1. Go to: https://colab.research.google.com/
2. Sign in with Google account (if not already)

### Step 2: Upload Notebook
1. Click: **File** → **Upload notebook**
2. Select: `colab_build_index.ipynb`
3. Click: **Open**

### Step 3: Enable GPU
1. Click: **Runtime** → **Change runtime type**
2. Select: **Hardware accelerator** → **GPU**
3. Click: **Save**

### Step 4: Run All Cells
1. Click: **Runtime** → **Run all**
2. Follow prompts to upload your project zip
3. Wait for build to complete (2-4 hours)
4. Download will start automatically

---

## What You Need to Upload

Create a zip file containing:

```
myproject.zip
├── meme_agent/
│   ├── scripts/
│   ├── embeddings.py
│   ├── vector_store.py
│   └── data_loader.py
├── ImgFlip575K_Dataset/
│   └── dataset/
│       └── memes/          # All JSON files
└── storage/
    └── vector_index/       # Can be empty
```

**Note:** You can exclude `.git/`, `__pycache__/`, `node_modules/`, etc. to reduce size.

---

## Expected Output

### Files Created (in `storage/vector_index/`)

| File | Size | Purpose |
|------|------|---------|
| `index.faiss` | ~500-800 MB | FAISS vector index |
| `id_map.json` | ~20-30 MB | Index to meme ID mapping |
| `meme_data.json` | ~50-100 MB | Meme metadata lookup |
| `metadata.json` | <1 KB | Build information |

### Build Time

| GPU Type | Estimated Time |
|----------|----------------|
| Tesla T4 (Free) | 2-4 hours |
| Tesla V100 (Pro) | 1-2 hours |
| A100 (Pro+) | 30-60 minutes |
| CPU only | 24-30 hours ⚠️ |

---

## During the Build

The notebook will show progress:

```
Loading memes: 100%|██████████| 99/99 [00:30<00:00]
Generating embeddings:  63%|██████3   | 5670/9000 [2:30:00<1:45:00]
```

### Progress Milestones

| Progress | Time Elapsed | Status |
|----------|--------------|--------|
| 0-10% | 15-30 min | Loading data, starting embeddings |
| 10-50% | 1-2 hours | Embedding generation |
| 50-90% | 1-2 hours | Embedding generation |
| 90-100% | 15-30 min | Building index, saving files |

---

## After Build Completes

### Automatic Download
The last cell will automatically download `vector_index.zip`.

### Manual Download (if needed)
```python
from google.colab import files
files.download('vector_index.zip')
```

### Extract Locally
```bash
# Windows
Extract all files to: C:\xampp\htdocs\myproject\storage\vector_index\

# Or via command line
cd C:\xampp\htdocs\myproject
tar -xf vector_index.zip -C storage/vector_index/
```

---

## Verify Installation

### Step 1: Check Files
```bash
cd C:\xampp\htdocs\myproject
dir storage\vector_index
```

Should show:
- `index.faiss`
- `id_map.json`
- `metadata.json`
- `meme_data.json`

### Step 2: Run Tests
```bash
python meme_agent/scripts/test_vector_search.py
```

Expected output:
```
============================================================
Test Summary
============================================================
  Passed: 5
  Failed: 0
  Skipped: 0

[OK] All tests passed!
```

### Step 3: Test Search
```bash
python -c "from meme_agent.data_loader import hybrid_search; r = hybrid_search('exam stress', 5); print(f'Found {len(r)} results')"
```

---

## Troubleshooting

### Problem: Runtime Disconnected
**Cause:** Colab free tier has 12-hour session limit

**Solution:**
1. Reconnect runtime
2. Re-run from cell after dependency installation
3. Dependencies are cached, so it's faster

### Problem: Out of Memory (OOM)
**Error:** `RuntimeError: CUDA out of memory`

**Solution:**
1. Edit cell with `build_index_gpu.py`
2. Change: `BATCH_SIZE = 128` → `BATCH_SIZE = 64` or `32`
3. Re-run all cells after that point

### Problem: Upload Too Large
**Error:** Zip file > 2GB fails to upload

**Solution:** Upload only essentials:
```
minimal.zip
├── meme_agent/scripts/build_index_gpu.py
└── ImgFlip575K_Dataset/dataset/memes/
```

Then in Colab:
```python
# Install dependencies first
!pip install sentence-transformers faiss-cpu tqdm

# Run the GPU build script directly
!python meme_agent/scripts/build_index_gpu.py
```

### Problem: Download Fails
**Cause:** Large file download timeout

**Solution:** Use Google Drive:
```python
from google.colab import drive
drive.mount('/content/drive')

# Copy to Drive
!cp -r storage/vector_index /content/drive/MyDrive/

# Then download from Drive web interface
```

---

## Cost

| Plan | Price | Session Limit | GPU Options |
|------|-------|---------------|-------------|
| Free | $0 | ~12 hours | T4, sometimes K80 |
| Pro | ~$10/month | ~24 hours | T4, V100, P100 |
| Pro+ | ~$50/month | ~24 hours | A100, V100 |

**Recommendation:** Free tier is sufficient for one complete build.

---

## Alternative: Run Locally (Slow)

If Colab doesn't work, you can run locally:

```bash
cd C:\xampp\htdocs\myproject
python meme_agent/scripts/build_vector_index.py
```

**Warning:** This will take 24-30 hours on CPU!

---

## Next Steps After Download

1. ✅ Extract `vector_index.zip` to `storage/vector_index/`
2. ✅ Run tests: `python meme_agent/scripts/test_vector_search.py`
3. ✅ Test meme generation through your API
4. ✅ Monitor search performance

---

## Support Files

All files are in `meme_agent/scripts/`:
- `colab_build_index.ipynb` - Main Colab notebook
- `build_index_gpu.py` - Standalone GPU script
- `build_vector_index.py` - Original CPU script
- `test_vector_search.py` - Test suite
- `COLAB_README.md` - Extended documentation

---

## Summary

1. **Upload** `colab_build_index.ipynb` to Colab
2. **Enable GPU** in Runtime settings
3. **Run all cells** and upload project zip
4. **Wait 2-4 hours** for build to complete
5. **Download** `vector_index.zip`
6. **Extract** to local `storage/vector_index/`
7. **Test** with `test_vector_search.py`

**That's it!** Your semantic search will now work with vector embeddings.
