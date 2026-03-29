#!/usr/bin/env python3
"""
Quick test script for vector search functionality.
Run this after building the index to verify everything works.

Usage:
    python meme_agent/scripts/test_vector_search.py
"""

import os
import sys

# Add parent directory to path
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))


def test_imports():
    """Test that all required modules can be imported."""
    print("Testing imports...")
    
    try:
        import faiss
        print("  [OK] faiss")
    except ImportError as e:
        print(f"  [FAIL] faiss: {e}")
        return False
    
    try:
        import sklearn
        print("  [OK] sklearn")
    except ImportError as e:
        print(f"  [FAIL] sklearn: {e}")
        return False
    
    try:
        from sentence_transformers import SentenceTransformer
        print("  [OK] sentence_transformers")
    except ImportError as e:
        print(f"  [FAIL] sentence_transformers: {e}")
        return False
    
    try:
        from embeddings import get_embedding_model, generate_embeddings
        print("  [OK] embeddings module")
    except ImportError as e:
        print(f"  [FAIL] embeddings module: {e}")
        return False
    
    try:
        from vector_store import VectorStore
        print("  [OK] vector_store module")
    except ImportError as e:
        print(f"  [FAIL] vector_store module: {e}")
        return False
    
    try:
        from data_loader import hybrid_search, semantic_search, keyword_search
        print("  [OK] data_loader module")
    except ImportError as e:
        print(f"  [FAIL] data_loader module: {e}")
        return False
    
    return True


def test_embedding_generation():
    """Test that embeddings can be generated."""
    print("\nTesting embedding generation...")
    
    try:
        from embeddings import generate_embeddings
        
        texts = ["exam stress", "monday mood", "happy birthday"]
        embeddings = generate_embeddings(texts, batch_size=4, show_progress=False)
        
        assert embeddings.shape == (3, 384), f"Expected (3, 384), got {embeddings.shape}"
        print(f"  [OK] Generated embeddings: {embeddings.shape}")
        return True
        
    except Exception as e:
        print(f"  [FAIL] Embedding generation failed: {e}")
        return False


def test_vector_store():
    """Test vector store operations."""
    print("\nTesting vector store...")
    
    try:
        from vector_store import VectorStore
        import numpy as np
        
        # Create a small test index
        store = VectorStore(
            index_path="storage/vector_index/test_index.faiss",
            id_map_path="storage/vector_index/test_id_map.json"
        )
        
        # Create test data
        embeddings = np.random.rand(10, 384).astype(np.float32)
        # Normalize for cosine similarity
        embeddings = embeddings / np.linalg.norm(embeddings, axis=1, keepdims=True)
        ids = [f"test_{i}" for i in range(10)]
        
        # Build index
        store.build_index(embeddings, ids)
        assert store.size() == 10
        print(f"  [OK] Built test index with {store.size()} vectors")
        
        # Test search
        from embeddings import generate_query_embedding
        query_emb = generate_query_embedding("test query")
        result_ids, scores = store.search(query_emb, top_k=3)
        
        assert len(result_ids) == 3, f"Expected 3 results, got {len(result_ids)}"
        print(f"  [OK] Search returned {len(result_ids)} results")
        
        # Cleanup
        for f in ["storage/vector_index/test_index.faiss", "storage/vector_index/test_id_map.json"]:
            if os.path.exists(f):
                os.remove(f)
        
        return True
        
    except Exception as e:
        print(f"  [FAIL] Vector store test failed: {e}")
        return False


def test_index_loaded():
    """Test that the built index can be loaded and searched."""
    print("\nTesting index loading...")
    
    try:
        from data_loader import semantic_search, _get_vector_store
        
        store = _get_vector_store()
        
        if store is None or store.is_empty():
            print("  [SKIP] Index not found. Run build_vector_index.py first.")
            return None  # Not a failure, just not built yet
        
        print(f"  [OK] Index loaded with {store.size()} vectors")
        
        # Test search
        results = semantic_search("exam stress", top_k=3)
        print(f"  [OK] Search returned {len(results)} results")
        
        if results:
            print(f"    Top result: {results[0]['caption'][:50]}... (score: {results[0]['score']})")
        
        return True
        
    except Exception as e:
        print(f"  [FAIL] Index test failed: {e}")
        return False


def test_hybrid_search():
    """Test hybrid search functionality."""
    print("\nTesting hybrid search...")
    
    try:
        from data_loader import hybrid_search
        
        results = hybrid_search("monday mood", top_k=5)
        print(f"  [OK] Hybrid search returned {len(results)} results")
        
        if results:
            for i, r in enumerate(results[:3]):
                source = r.get('source', 'unknown')
                score = r.get('score', 0)
                print(f"    {i+1}. [{source}] {r['caption'][:40]}... (score: {score})")
        
        return True
        
    except Exception as e:
        print(f"  [FAIL] Hybrid search failed: {e}")
        return False


def main():
    """Run all tests."""
    print("=" * 60)
    print("Vector Search Test Suite")
    print("=" * 60)
    
    results = {
        "imports": test_imports(),
        "embeddings": False,
        "vector_store": False,
        "index_loaded": None,
        "hybrid_search": False
    }
    
    if results["imports"]:
        results["embeddings"] = test_embedding_generation()
        results["vector_store"] = test_vector_store()
        results["index_loaded"] = test_index_loaded()
        results["hybrid_search"] = test_hybrid_search()
    
    # Summary
    print("\n" + "=" * 60)
    print("Test Summary")
    print("=" * 60)
    
    passed = sum(1 for v in results.values() if v is True)
    failed = sum(1 for v in results.values() if v is False)
    skipped = sum(1 for v in results.values() if v is None)
    
    print(f"  Passed: {passed}")
    print(f"  Failed: {failed}")
    print(f"  Skipped: {skipped}")
    
    if failed > 0:
        print("\n[WARN] Some tests failed. Check the output above.")
        sys.exit(1)
    else:
        print("\n[OK] All tests passed!")
        sys.exit(0)


if __name__ == '__main__':
    main()
