#!/usr/bin/env python3
"""
Build vector index for meme dataset.
Precomputes embeddings for all meme captions and stores in FAISS index.

Usage:
    python meme_agent/scripts/build_vector_index.py

Output:
    - storage/vector_index/index.faiss
    - storage/vector_index/id_map.json
    - storage/vector_index/metadata.json
"""

import os
import sys
import json
import time
import numpy as np
from pathlib import Path
from typing import List, Tuple, Dict, Any

# Force unbuffered output
sys.stdout.reconfigure(line_buffering=True) if hasattr(sys.stdout, 'reconfigure') else None

# Add parent directory to path for imports
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from embeddings import generate_embeddings
from vector_store import VectorStore

# Configuration
PROJECT_ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DATASET_DIR = os.path.join(PROJECT_ROOT, "..", "ImgFlip575K_Dataset", "dataset")
MEMES_DIR = os.path.join(DATASET_DIR, "memes")
STORAGE_DIR = os.path.join(PROJECT_ROOT, "..", "storage", "vector_index")

INDEX_PATH = os.path.join(STORAGE_DIR, "index.faiss")
ID_MAP_PATH = os.path.join(STORAGE_DIR, "id_map.json")
METADATA_PATH = os.path.join(STORAGE_DIR, "metadata.json")

BATCH_SIZE = 64
CHECKPOINT_EVERY = 200  # Save checkpoint every N batches
MODEL_NAME = "all-MiniLM-L6-v2"
SCORE_THRESHOLD = 0.35  # Minimum cosine similarity for valid results


def load_meme_data(memes_dir: str) -> Tuple[List[str], List[str], List[Dict[str, Any]]]:
    """
    Load all meme data from JSON files.
    
    Args:
        memes_dir: Path to the memes directory.
    
    Returns:
        Tuple of (texts, ids, meme_data) where:
            - texts: List of searchable text strings (captions + titles).
            - ids: List of unique meme IDs.
            - meme_data: List of original meme data dictionaries.
    """
    texts = []
    ids = []
    meme_data_list = []
    
    if not os.path.exists(memes_dir):
        print(f"Error: Memes directory not found: {memes_dir}")
        return texts, ids, meme_data_list
    
    # Get all JSON files
    json_files = [f for f in os.listdir(memes_dir) if f.endswith('.json')]
    total_files = len(json_files)
    print(f"Found {total_files} meme template files")
    
    for file_idx, filename in enumerate(json_files, 1):
        template_id = os.path.splitext(filename)[0]
        filepath = os.path.join(memes_dir, filename)
        
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                memes = json.load(f)
            
            if not isinstance(memes, list):
                print(f"  Warning: {filename} does not contain a list, skipping")
                continue
            
            for meme_idx, meme in enumerate(memes):
                # Extract caption boxes
                boxes = meme.get('boxes', [])
                if not boxes:
                    continue
                
                # Create searchable text: combine boxes and title
                caption = ' '.join(str(box) for box in boxes)
                title = meme.get('metadata', {}).get('title', '')
                
                # Combine for search text
                if title and title.lower() not in caption.lower():
                    search_text = f"{caption} {title}"
                else:
                    search_text = caption
                
                # Create unique ID
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
            
            if file_idx % 10 == 0 or file_idx == total_files:
                print(f"  Processed {file_idx}/{total_files} files...")
                
        except json.JSONDecodeError as e:
            print(f"  Error parsing {filename}: {e}")
        except Exception as e:
            print(f"  Error loading {filename}: {e}")
    
    return texts, ids, meme_data_list


def build_index(
    texts: List[str],
    ids: List[str],
    meme_data: List[Dict[str, Any]],
    batch_size: int = BATCH_SIZE,
    model_name: str = MODEL_NAME
) -> VectorStore:
    """
    Build FAISS index from meme texts with periodic checkpointing.

    Args:
        texts: List of searchable texts.
        ids: List of meme IDs.
        meme_data: List of meme metadata.
        batch_size: Batch size for embedding generation.
        model_name: Name of embedding model.

    Returns:
        VectorStore with built index.
    """
    import faiss
    from sentence_transformers import SentenceTransformer

    # Create vector store
    store = VectorStore(
        index_path=INDEX_PATH,
        id_map_path=ID_MAP_PATH,
        metadata_path=METADATA_PATH
    )

    # Check for existing index to resume
    existing_count = 0
    if store.load_index():
        existing_count = store.size()
        print(f"\n[RESUME] Found existing index with {existing_count} vectors")
        print(f"  Continuing from vector {existing_count}...")

    remaining_texts = texts[existing_count:]
    remaining_ids = ids[existing_count:]
    remaining_meme_data = meme_data[existing_count:]

    if not remaining_texts:
        print(f"\nIndex already complete with {existing_count} vectors!")
        return store

    total_batches = (len(remaining_texts) + batch_size - 1) // batch_size
    start_batch = existing_count // batch_size

    print(f"\nGenerating embeddings for {len(remaining_texts)} remaining memes...")
    print(f"Model: {model_name}")
    print(f"Batch size: {batch_size}")
    print(f"Checkpoint interval: every {CHECKPOINT_EVERY} batches")
    print(f"Progress: {existing_count}/{len(texts)} already done ({100*existing_count/len(texts):.1f}%)")
    print(f"Starting from batch {start_batch}/{total_batches}")

    # Load model
    print("\nLoading embedding model...")
    model = SentenceTransformer(model_name, trust_remote_code=True)

    # Initialize index if starting fresh
    if existing_count == 0:
        print("Building new FAISS index...")
        store.index = faiss.IndexFlatIP(store.embedding_dim)
        store.id_map = {}
        store.reverse_id_map = {}
    else:
        print(f"Continuing with existing index ({store.index.ntotal} vectors)...")

    # Process in batches with checkpointing
    current_idx = existing_count
    batch_num = 0
    start_time = time.time()

    for i in range(0, len(remaining_texts), batch_size):
        batch_texts = remaining_texts[i:i + batch_size]
        batch_ids = remaining_ids[i:i + batch_size]

        # Generate embeddings for this batch
        embeddings = model.encode(
            batch_texts,
            batch_size=batch_size,
            show_progress_bar=False,
            convert_to_numpy=True,
            normalize_embeddings=True
        ).astype(np.float32)

        # Add to index
        store.index.add(embeddings)

        # Update ID mapping
        for j, id_ in enumerate(batch_ids):
            faiss_idx = current_idx + j
            store.id_map[faiss_idx] = id_
            store.reverse_id_map[id_] = faiss_idx

        current_idx += len(batch_texts)
        batch_num += 1

        # Progress display
        progress = 100 * batch_num / total_batches
        if batch_num % 10 == 0 or batch_num == total_batches:
            print(f"  Batch {batch_num}/{total_batches} ({progress:.1f}%) - {current_idx} vectors indexed")

        # Checkpoint save
        if batch_num % CHECKPOINT_EVERY == 0:
            print(f"\n[CHECKPOINT] Saving at batch {batch_num} ({current_idx} vectors)...")
            store.set_metadata('total_memes', current_idx)
            store.set_metadata('total_templates', len(set(d['template_id'] for d in meme_data[:current_idx])))
            store.set_metadata('model_name', model_name)
            store.set_metadata('embedding_dim', store.embedding_dim)
            store.set_metadata('built_at', time.strftime('%Y-%m-%d %H:%M:%S'))
            store.set_metadata('last_updated', time.strftime('%Y-%m-%d %H:%M:%S'))
            store.set_metadata('last_batch', batch_num)
            store.set_metadata('total_batches', total_batches)
            store.save_index()
            print(f"[CHECKPOINT] Saved successfully at batch {batch_num}\n")

    elapsed = time.time() - start_time
    print(f"\nEmbedding generation completed in {elapsed:.1f}s")

    # Final save
    store.set_metadata('total_memes', store.size())
    store.set_metadata('total_templates', len(set(d['template_id'] for d in meme_data)))
    store.set_metadata('model_name', model_name)
    store.set_metadata('embedding_dim', embeddings.shape[1])
    store.set_metadata('built_at', time.strftime('%Y-%m-%d %H:%M:%S'))
    store.set_metadata('last_updated', time.strftime('%Y-%m-%d %H:%M:%S'))
    store.set_metadata('last_batch', batch_num)
    store.set_metadata('total_batches', total_batches)
    store.set_metadata('complete', True)

    print(f"Saving final index to {INDEX_PATH}...")
    store.save_index()

    # Save meme data lookup (full data)
    lookup_path = os.path.join(STORAGE_DIR, 'meme_data.json')
    print(f"Saving meme data lookup to {lookup_path}...")
    with open(lookup_path, 'w', encoding='utf-8') as f:
        json.dump(meme_data, f, indent=2)

    print(f"\nIndex built successfully!")
    print(f"  Total vectors: {store.size()}")
    print(f"  Index file: {INDEX_PATH}")
    print(f"  ID map: {ID_MAP_PATH}")

    return store


def verify_index(store: VectorStore) -> None:
    """
    Verify the built index with a test query.
    
    Args:
        store: The VectorStore to verify.
    """
    print("\nVerifying index with test queries...")
    
    test_queries = [
        "exam stress",
        "monday mood",
        "happy birthday"
    ]
    
    from embeddings import generate_query_embedding
    
    for query in test_queries:
        query_emb = generate_query_embedding(query)
        ids, scores = store.search(query_emb, top_k=3)
        
        print(f"\n  Query: '{query}'")
        if ids:
            for id_, score in zip(ids, scores):
                print(f"    {id_}: {score:.4f}")
        else:
            print("    No results found")


def main():
    """Main entry point."""
    print("=" * 60)
    print("Meme Vector Index Builder")
    print("=" * 60)

    # Ensure storage directory exists
    os.makedirs(STORAGE_DIR, exist_ok=True)

    # Load meme data
    print(f"\nLoading meme data from {MEMES_DIR}...")
    texts, ids, meme_data = load_meme_data(MEMES_DIR)

    if not texts:
        print("\nError: No meme data found. Check the dataset path.")
        return

    print(f"Loaded {len(texts)} memes from {len(set(ids))} templates")

    # Check for existing index
    store = VectorStore(
        index_path=INDEX_PATH,
        id_map_path=ID_MAP_PATH,
        metadata_path=METADATA_PATH
    )

    if store.load_index():
        existing_count = store.size()
        print(f"\n[RESUME] Found existing index with {existing_count}/{len(texts)} vectors")
        if existing_count >= len(texts):
            print("Index is already complete!")
            verify_index(store)
            return
        print(f"Will resume from vector {existing_count}...")
    else:
        print("\nNo existing index found. Starting fresh build.")

    # Build/continue index
    store = build_index(texts, ids, meme_data)

    # Verify
    verify_index(store)

    print("\n" + "=" * 60)
    print("Index build complete!")
    print("=" * 60)


if __name__ == '__main__':
    main()
