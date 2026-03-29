#!/usr/bin/env python3
"""
GPU-Optimized Vector Index Builder for Meme Dataset
Designed for Google Colab with GPU acceleration

Usage:
    python build_index_gpu.py
"""

import os
import sys
import json
import time
from pathlib import Path
from typing import List, Tuple, Dict, Any
import numpy as np
from tqdm import tqdm

# Configuration - Adjust based on GPU memory
BATCH_SIZE = 128  # Reduce to 64 or 32 if OOM
MODEL_NAME = "all-MiniLM-L6-v2"
STORAGE_DIR = "storage/vector_index"
MEMES_DIR = "ImgFlip575K_Dataset/dataset/memes"


def check_gpu():
    """Check GPU availability and print info."""
    try:
        import torch
        print("="*60)
        print("GPU Check")
        print("="*60)
        print(f"PyTorch version: {torch.__version__}")
        print(f"CUDA available: {torch.cuda.is_available()}")
        
        if torch.cuda.is_available():
            print(f"GPU: {torch.cuda.get_device_name(0)}")
            print(f"CUDA version: {torch.version.cuda}")
            print(f"GPU Memory: {torch.cuda.get_device_properties(0).total_memory / 1e9:.1f} GB")
        else:
            print("WARNING: GPU not available! Build will be very slow.")
            print("Please enable GPU in Colab: Runtime -> Change runtime type -> GPU")
        
        print()
        return torch.cuda.is_available()
    except Exception as e:
        print(f"Error checking GPU: {e}")
        return False


def load_meme_data(memes_dir: str) -> Tuple[List[str], List[str], List[Dict[str, Any]]]:
    """Load all meme data from JSON files."""
    texts = []
    ids = []
    meme_data_list = []
    
    if not os.path.exists(memes_dir):
        raise FileNotFoundError(f"Memes directory not found: {memes_dir}")
    
    json_files = sorted([f for f in os.listdir(memes_dir) if f.endswith('.json')])
    total_files = len(json_files)
    
    print(f"Loading memes from {total_files} template files...")
    
    for filename in tqdm(json_files, desc="Loading memes", unit="file"):
        template_id = os.path.splitext(filename)[0]
        filepath = os.path.join(memes_dir, filename)
        
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                memes = json.load(f)
            
            if not isinstance(memes, list):
                continue
            
            for meme_idx, meme in enumerate(memes):
                boxes = meme.get('boxes', [])
                if not boxes:
                    continue
                
                caption = ' '.join(str(box) for box in boxes)
                title = meme.get('metadata', {}).get('title', '')
                
                # Combine caption and title for search text
                if title and title.lower() not in caption.lower():
                    search_text = f"{caption} {title}"
                else:
                    search_text = caption
                
                meme_id = f"{template_id}_{meme_idx}"
                
                texts.append(search_text.strip())
                ids.append(meme_id)
                meme_data_list.append({
                    'template_id': template_id,
                    'meme_idx': meme_idx,
                    'caption': caption,
                    'title': title,
                    'url': meme.get('url', ''),
                    'post_url': meme.get('post', '')
                })
                
        except Exception as e:
            print(f"Error loading {filename}: {e}")
            continue
    
    return texts, ids, meme_data_list


def generate_embeddings_gpu(texts: List[str], batch_size: int = 128) -> np.ndarray:
    """Generate embeddings using GPU-accelerated sentence-transformers."""
    from sentence_transformers import SentenceTransformer
    
    print(f"\nLoading model: {MODEL_NAME}")
    print("This may take a minute on first run...")
    
    # Load model on GPU
    device = 'cuda' if torch.cuda.is_available() else 'cpu'
    model = SentenceTransformer(MODEL_NAME, device=device)
    print(f"Model loaded on: {model.device}")
    
    print(f"\nGenerating embeddings for {len(texts)} memes...")
    print(f"Batch size: {batch_size}")
    print(f"Estimated time: ~{len(texts) / batch_size / 3600 * 0.5:.1f} hours\n")
    
    # Generate embeddings with progress bar
    embeddings = model.encode(
        texts,
        batch_size=batch_size,
        show_progress_bar=True,
        convert_to_numpy=True,
        normalize_embeddings=True  # L2 normalize for cosine similarity
    )
    
    print(f"\nEmbeddings shape: {embeddings.shape}")
    print(f"Embedding dimension: {embeddings.shape[1]}")
    print(f"Memory size: {embeddings.nbytes / (1024**3):.1f} GB")
    
    return embeddings


def build_faiss_index(embeddings: np.ndarray, ids: List[str], storage_dir: str):
    """Build and save FAISS index."""
    import faiss
    
    print("\n" + "="*60)
    print("Building FAISS Index")
    print("="*60)
    
    # Ensure embeddings are float32
    embeddings = embeddings.astype(np.float32)
    
    # Create index (Inner Product = Cosine Similarity for normalized vectors)
    dimension = embeddings.shape[1]
    print(f"Creating IndexFlatIP index (dimension: {dimension})...")
    index = faiss.IndexFlatIP(dimension)
    index.add(embeddings)
    
    # Create storage directory
    os.makedirs(storage_dir, exist_ok=True)
    
    # Save index
    index_path = os.path.join(storage_dir, "index.faiss")
    print(f"Saving index to {index_path}...")
    faiss.write_index(index, index_path)
    index_size = os.path.getsize(index_path) / (1024**3)
    print(f"Index size: {index_size:.2f} GB")
    
    # Save ID mapping
    id_map = {i: id_ for i, id_ in enumerate(ids)}
    id_map_path = os.path.join(storage_dir, "id_map.json")
    print(f"Saving ID map to {id_map_path}...")
    with open(id_map_path, 'w', encoding='utf-8') as f:
        json.dump(id_map, f)
    
    # Save metadata
    metadata = {
        'total_memes': len(ids),
        'total_templates': len(set(id_.rsplit('_', 1)[0] for id_ in ids)),
        'embedding_dim': dimension,
        'model_name': MODEL_NAME,
        'batch_size': BATCH_SIZE,
        'built_at': time.strftime('%Y-%m-%d %H:%M:%S'),
        'built_on': 'Google Colab GPU' if torch.cuda.is_available() else 'CPU'
    }
    metadata_path = os.path.join(storage_dir, "metadata.json")
    with open(metadata_path, 'w') as f:
        json.dump(metadata, f, indent=2)
    print(f"Saved metadata to {metadata_path}")
    
    print("\nIndex built successfully!")
    return index


def main():
    """Main entry point."""
    print("="*60)
    print("Meme Vector Index Builder - GPU Accelerated")
    print("="*60)
    print()
    
    # Check GPU
    has_gpu = check_gpu()
    
    # Adjust batch size if no GPU
    global BATCH_SIZE
    if not has_gpu and BATCH_SIZE > 32:
        print("No GPU detected. Reducing batch size to 32.")
        BATCH_SIZE = 32
    
    start_time = time.time()
    
    try:
        # Load data
        texts, ids, meme_data = load_meme_data(MEMES_DIR)
        print(f"\nLoaded {len(texts)} memes")
        
        # Generate embeddings
        embeddings = generate_embeddings_gpu(texts, batch_size=BATCH_SIZE)
        
        # Build index
        build_faiss_index(embeddings, ids, STORAGE_DIR)
        
        # Save meme data lookup
        meme_data_path = os.path.join(STORAGE_DIR, "meme_data.json")
        print(f"\nSaving meme data to {meme_data_path}...")
        with open(meme_data_path, 'w', encoding='utf-8') as f:
            json.dump(meme_data, f)
        
        # Final summary
        elapsed = time.time() - start_time
        hours, remainder = divmod(int(elapsed), 3600)
        minutes, seconds = divmod(remainder, 60)
        
        print("\n" + "="*60)
        print("BUILD COMPLETE!")
        print("="*60)
        print(f"Total time: {hours}h {minutes}m {seconds}s")
        print(f"\nOutput files in {STORAGE_DIR}/:")
        for f in sorted(os.listdir(STORAGE_DIR)):
            filepath = os.path.join(STORAGE_DIR, f)
            size = os.path.getsize(filepath)
            if size > 1024*1024*1024:  # > 1GB
                print(f"  - {f}: {size / (1024**3):.1f} GB")
            elif size > 1024*1024:  # > 1MB
                print(f"  - {f}: {size / (1024**2):.1f} MB")
            else:
                print(f"  - {f}: {size / 1024:.1f} KB")
        
        print("\nNext steps:")
        print("1. Download the storage/vector_index/ folder")
        print("2. Replace your local storage/vector_index/ folder")
        print("3. Run: python meme_agent/scripts/test_vector_search.py")
        
    except Exception as e:
        print(f"\nBuild failed with error: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)


if __name__ == "__main__":
    # Import torch here to avoid issues if not installed
    try:
        import torch
    except ImportError:
        print("Error: torch not installed. Run: pip install torch")
        sys.exit(1)
    
    main()
