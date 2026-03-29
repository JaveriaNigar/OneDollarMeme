#!/usr/bin/env python3
"""
CPU Vector Index Builder for Meme Agent Dataset
Generates FAISS embeddings for meme TEMPLATES only (100 items).
This is the practical approach for meme search/generator applications.

For the full 575K meme dataset, consider:
- Using GPU (build_index_gpu.py)
- Building embeddings on-the-fly for user queries only
- Using a pre-computed embedding service

Usage:
    python meme_agent/scripts/build_index_cpu.py

Output:
    storage/vector_index/
    ├── index.faiss       - FAISS vector index
    ├── id_map.json       - Meme ID mappings
    ├── metadata.json     - Build metadata
    └── meme_data.json    - Full meme data lookup
"""

import os
import sys
import json
import time
from pathlib import Path
from typing import List, Tuple, Dict, Any
import numpy as np

# Configuration
MODEL_NAME = "all-MiniLM-L6-v2"
BATCH_SIZE = 32  # Conservative for CPU
STORAGE_DIR = os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__)))), "storage", "vector_index")

# Dataset paths
PROJECT_ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
DATASET_ROOT = os.path.join(PROJECT_ROOT, "ImgFlip575K_Dataset", "dataset")
TEMPLATES_DIR = os.path.join(DATASET_ROOT, "templates")
MEMES_DIR = os.path.join(DATASET_ROOT, "memes")


def load_template_data() -> Tuple[List[str], List[str], List[Dict[str, Any]]]:
    """
    Load meme TEMPLATE data only (100 templates).
    Each template represents a meme format with its metadata.
    
    Returns:
        Tuple of (texts, ids, template_data)
    """
    texts = []
    ids = []
    template_data_list = []
    
    if not os.path.exists(TEMPLATES_DIR):
        raise FileNotFoundError(f"Templates directory not found: {TEMPLATES_DIR}")
    
    print(f"Loading templates from {TEMPLATES_DIR}...")
    
    json_files = sorted([f for f in os.listdir(TEMPLATES_DIR) if f.endswith('.json')])
    
    for filename in json_files:
        template_id = os.path.splitext(filename)[0]
        filepath = os.path.join(TEMPLATES_DIR, filename)
        
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                data = json.load(f)
            
            if not isinstance(data, dict):
                continue
            
            # Build searchable text from template metadata
            title = data.get('title', template_id)
            alt_names = data.get('alternative_names', '')
            
            # Combine title and alternative names for rich search
            search_text = f"{title}".strip()
            if alt_names:
                search_text = f"{title} {alt_names}".strip()
            
            template_data = {
                'template_id': template_id,
                'title': title,
                'alternative_names': alt_names,
                'template_url': data.get('template_url', ''),
                'format': data.get('format', 'jpg'),
                'dimensions': data.get('dimensions', ''),
                'file_size': data.get('file_size', ''),
                'search_text': search_text
            }
            
            texts.append(search_text)
            ids.append(template_id)
            template_data_list.append(template_data)
            
        except json.JSONDecodeError as e:
            print(f"  Warning: Could not parse {filename}: {e}")
        except Exception as e:
            print(f"  Warning: Error loading {filename}: {e}")
            continue
    
    return texts, ids, template_data_list


def load_sample_memes(sample_size: int = 500) -> Tuple[List[str], List[str], List[Dict[str, Any]]]:
    """
    Load a SAMPLE of actual memes for demonstration.
    Useful for testing but not production-scale.
    
    Args:
        sample_size: Number of meme samples to load.
        
    Returns:
        Tuple of (texts, ids, meme_data)
    """
    texts = []
    ids = []
    meme_data_list = []
    
    if not os.path.exists(MEMES_DIR):
        return texts, ids, meme_data_list
    
    print(f"Loading up to {sample_size} sample memes from {MEMES_DIR}...")
    
    json_files = sorted([f for f in os.listdir(MEMES_DIR) if f.endswith('.json')])
    memes_loaded = 0
    
    for filename in json_files:
        if memes_loaded >= sample_size:
            break
            
        template_id = os.path.splitext(filename)[0]
        filepath = os.path.join(MEMES_DIR, filename)
        
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                memes = json.load(f)
            
            if not isinstance(memes, list):
                continue
            
            for meme_idx, meme in enumerate(memes):
                if memes_loaded >= sample_size:
                    break
                
                boxes = meme.get('boxes', [])
                if not boxes:
                    continue
                
                caption = ' '.join(str(box) for box in boxes)
                title = meme.get('metadata', {}).get('title', '')
                
                # Combine for search text
                if title and title.lower() not in caption.lower():
                    search_text = f"{caption} {title}"
                else:
                    search_text = caption
                
                meme_id = f"{template_id}_{meme_idx}"
                
                texts.append(search_text.strip())
                ids.append(meme_id)
                meme_data_list.append({
                    'source': 'sample',
                    'template_id': template_id,
                    'meme_idx': meme_idx,
                    'caption': caption,
                    'title': title,
                    'url': meme.get('url', ''),
                    'post_url': meme.get('post', '')
                })
                
                memes_loaded += 1
                
        except Exception as e:
            continue
    
    return texts, ids, meme_data_list


def generate_embeddings_cpu(texts: List[str]) -> np.ndarray:
    """
    Generate embeddings using sentence-transformers on CPU.
    
    Args:
        texts: List of text strings to embed.
        
    Returns:
        numpy array of shape (len(texts), embedding_dim)
    """
    from sentence_transformers import SentenceTransformer
    from tqdm import tqdm
    
    print(f"\nLoading embedding model: {MODEL_NAME}")
    print("(First run will download the model ~80MB)")
    
    # Load model on CPU
    model = SentenceTransformer(MODEL_NAME, device='cpu', trust_remote_code=True)
    
    print(f"\nGenerating embeddings for {len(texts)} items...")
    print(f"Batch size: {BATCH_SIZE}")
    
    # Estimate time
    items_per_second = ~40  # Conservative CPU estimate
    eta_seconds = len(texts) / items_per_second
    print(f"Estimated time: ~{eta_seconds/60:.1f} minutes\n")
    
    # Generate embeddings with progress bar
    embeddings = model.encode(
        texts,
        batch_size=BATCH_SIZE,
        show_progress_bar=True,
        convert_to_numpy=True,
        normalize_embeddings=True  # L2 normalize for cosine similarity
    )
    
    print(f"\nEmbeddings shape: {embeddings.shape}")
    print(f"Memory size: {embeddings.nbytes / (1024**2):.1f} MB")
    
    return embeddings


def build_faiss_index(embeddings: np.ndarray, ids: List[str], meme_data_list: List[Dict[str, Any]], storage_dir: str):
    """
    Build and save FAISS index.
    """
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
    index_size = os.path.getsize(index_path) / (1024**2)
    print(f"Index size: {index_size:.2f} MB")
    
    # Save ID mapping
    id_map = {i: id_ for i, id_ in enumerate(ids)}
    id_map_path = os.path.join(storage_dir, "id_map.json")
    print(f"Saving ID map to {id_map_path}...")
    with open(id_map_path, 'w', encoding='utf-8') as f:
        json.dump(id_map, f, indent=2)
    
    # Save metadata
    metadata = {
        'total_items': len(ids),
        'embedding_dim': dimension,
        'model_name': MODEL_NAME,
        'batch_size': BATCH_SIZE,
        'built_at': time.strftime('%Y-%m-%d %H:%M:%S'),
        'built_on': 'CPU',
        'index_type': 'templates_only'
    }
    metadata_path = os.path.join(storage_dir, "metadata.json")
    with open(metadata_path, 'w') as f:
        json.dump(metadata, f, indent=2)
    print(f"Saved metadata to {metadata_path}")
    
    # Save meme data lookup
    meme_data_path = os.path.join(storage_dir, "meme_data.json")
    print(f"Saving data to {meme_data_path}...")
    with open(meme_data_path, 'w', encoding='utf-8') as f:
        json.dump(meme_data_list, f, indent=2)
    
    print("\nIndex built successfully!")
    return index


def main():
    """Main entry point."""
    print("="*60)
    print("Meme Vector Index Builder - CPU (Templates Only)")
    print("="*60)
    print()
    print("NOTE: Building index for 100 meme TEMPLATES only.")
    print("      This is the practical approach for meme search.")
    print()
    print("      For full 575K dataset, use build_index_gpu.py")
    print("="*60)
    print()
    
    start_time = time.time()
    
    try:
        # Load template data (100 items)
        texts, ids, template_data = load_template_data()
        
        if not texts:
            print("\nError: No template data found!")
            sys.exit(1)
            
        print(f"\nLoaded {len(texts)} templates")
        
        # Generate embeddings
        embeddings = generate_embeddings_cpu(texts)
        
        # Build index
        build_faiss_index(embeddings, ids, template_data, STORAGE_DIR)
        
        # Final summary
        elapsed = time.time() - start_time
        minutes, seconds = divmod(int(elapsed), 60)
        
        print("\n" + "="*60)
        print("BUILD COMPLETE!")
        print("="*60)
        print(f"Total time: {minutes}m {seconds}s")
        print(f"\nOutput files in {STORAGE_DIR}/:")
        for f in sorted(os.listdir(STORAGE_DIR)):
            filepath = os.path.join(STORAGE_DIR, f)
            size = os.path.getsize(filepath)
            if size > 1024*1024*1024:
                print(f"  - {f}: {size / (1024**3):.1f} GB")
            elif size > 1024*1024:
                print(f"  - {f}: {size / (1024**2):.1f} MB")
            else:
                print(f"  - {f}: {size / 1024:.1f} KB")
        
        print("\nNext steps:")
        print("1. Test search: python meme_agent/scripts/test_vector_search.py")
        print("2. Start server: python meme_agent/server.py")
        
    except Exception as e:
        print(f"\nBuild failed with error: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)


if __name__ == "__main__":
    main()
