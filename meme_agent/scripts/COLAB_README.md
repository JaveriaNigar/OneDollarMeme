# Google Colab Vector Index Build Guide

## Quick Start

### Option 1: Use the Colab Notebook (Recommended)

1. **Open Google Colab**: https://colab.research.google.com/

2. **Enable GPU**:
   - Click: `Runtime` → `Change runtime type`
   - Select: `Hardware accelerator` → `GPU`
   - Click: `Save`

3. **Upload the Notebook**:
   - Click: `File` → `Upload notebook`
   - Upload: `colab_build_index.ipynb`
   - Click: `Open`

4. **Run All Cells**:
   - Click: `Runtime` → `Run all`
   - Follow the prompts to upload your project zip

5. **Download Results**:
   - The last cell will automatically download `vector_index.zip`
   - Extract and replace your local `storage/vector_index/` folder

---

### Option 2: Manual Commands in Colab

If you prefer to run commands manually:

1. **Open new Colab**: https://colab.research.google.com/#create=true

2. **Enable GPU** (as above)

3. **Run these commands in order**:

```python
# Step 1: Install dependencies
!pip install sentence-transformers faiss-cpu scikit-learn tqdm --quiet

# Step 2: Upload your project
from google.colab import drive
drive.mount('/content/drive')

# Or upload zip:
from google.colab import files
uploaded = files.upload()
# Then extract:
import zipfile
with zipfile.ZipFile('your_project.zip', 'r') as zip_ref:
    zip_ref.extractall('.')

# Step 3: Check GPU
import torch
print(f"GPU: {torch.cuda.get_device_name(0)}" if torch.cuda.is_available() else "No GPU!")

# Step 4: Run build script
!python meme_agent/scripts/build_vector_index.py

# Step 5: Download results
import shutil
from google.colab import files
shutil.make_archive('vector_index', 'zip', 'storage/vector_index')
files.download('vector_index.zip')
```

---

## Expected Output

### Files Created
```
storage/vector_index/
├── index.faiss          (~500-800 MB)
├── id_map.json          (~20-30 MB)
├── metadata.json        (<1 KB)
└── meme_data.json       (~50-100 MB)
```

### Build Time
- **With GPU (T4)**: 2-4 hours
- **With GPU (V100/A100)**: 1-2 hours
- **CPU only**: 24-30 hours (not recommended)

---

## Troubleshooting

### Runtime Disconnected
Colab may disconnect after 12 hours. If this happens:
1. Reconnect runtime
2. Re-run from Step 2 (dependencies are cached)
3. The build will restart from beginning

### Out of Memory
If you get CUDA out of memory error:
1. Edit `build_index_gpu.py`
2. Change `BATCH_SIZE = 128` to `BATCH_SIZE = 64` or `32`
3. Re-run

### Upload Too Large
If your project zip is > 2GB:
1. Upload only required folders:
   - `meme_agent/`
   - `ImgFlip575K_Dataset/dataset/`
2. Create minimal structure in Colab:
```python
!mkdir -p meme_agent storage/vector_index
# Upload just the dataset
```

---

## After Download

1. **Extract the zip**:
   ```bash
   unzip vector_index.zip -d storage/vector_index/
   ```

2. **Verify files**:
   ```bash
   python meme_agent/scripts/test_vector_search.py
   ```

3. **Test search**:
   ```python
   from meme_agent.data_loader import hybrid_search
   results = hybrid_search("exam stress", top_k=5)
   print(f"Found {len(results)} results")
   ```

---

## Cost

- **Google Colab Free**: Limited to ~12 hour sessions, may disconnect
- **Google Colab Pro**: ~$10/month, longer sessions, better GPUs
- **Recommendation**: Free tier should be sufficient for one build

---

## Files Reference

- `colab_build_index.ipynb` - Complete Colab notebook (use this!)
- `build_vector_index.py` - Original build script (CPU)
- `test_vector_search.py` - Test suite

---

## Support

If you encounter issues:
1. Check GPU is enabled: `torch.cuda.is_available()`
2. Check dataset path is correct
3. Check disk space: `!df -h`
4. Try smaller batch size if OOM
