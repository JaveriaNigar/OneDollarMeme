#!/usr/bin/env python3
"""
Semantic Caption Search for Meme Templates.

This module provides semantic search functionality to match user queries
to relevant meme captions/templates using vector embeddings and cosine similarity.

Usage:
    from caption_search import search_templates, CaptionSearchEngine
    
    # Simple search
    results = search_templates("I failed my exam", top_k=5)
    
    # Using the engine directly
    engine = CaptionSearchEngine()
    results = engine.search("birthday celebration", top_k=3)
"""

import os
import json
import numpy as np
from typing import List, Dict, Optional, Any, Tuple
from pathlib import Path
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Configuration
DATASET_DIR = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "ImgFlip575K_Dataset", "dataset")
MEMES_DIR = os.path.join(DATASET_DIR, "memes")
TEMPLATES_DIR = os.path.join(DATASET_DIR, "templates")
CACHE_DIR = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "storage", "caption_cache")

EMBEDDING_MODEL = "all-MiniLM-L6-v2"
CAPTION_CACHE_PATH = os.path.join(CACHE_DIR, "caption_embeddings.npy")
CAPTION_DATA_CACHE_PATH = os.path.join(CACHE_DIR, "caption_data.json")


class CaptionSearchEngine:
    """
    Semantic search engine for meme captions.
    Uses sentence-transformers for embeddings and cosine similarity for matching.
    """
    
    def __init__(self, model_name: str = EMBEDDING_MODEL):
        """
        Initialize the search engine.
        
        Args:
            model_name: Name of the sentence-transformers model to use.
        """
        self.model_name = model_name
        self.model = None
        self.caption_embeddings = None
        self.caption_data = []
        self._initialized = False
    
    def _load_model(self):
        """Load the embedding model."""
        if self.model is None:
            from sentence_transformers import SentenceTransformer
            self.model = SentenceTransformer(self.model_name, device='cpu', trust_remote_code=True)
            logger.info(f"Loaded embedding model: {self.model_name}")
    
    def _load_captions(self, sample_size: int = None) -> List[Dict[str, Any]]:
        """
        Load all meme captions from the dataset.
        
        Args:
            sample_size: Optional limit on number of captions to load.
        
        Returns:
            List of caption dictionaries with text and metadata.
        """
        captions = []
        
        if not os.path.exists(MEMES_DIR):
            logger.warning(f"Memes directory not found: {MEMES_DIR}")
            return captions
        
        # Load captions from meme files
        json_files = sorted([f for f in os.listdir(MEMES_DIR) if f.endswith('.json')])
        logger.info(f"Loading captions from {len(json_files)} files...")
        
        files_loaded = 0
        for filename in json_files:
            if sample_size and len(captions) >= sample_size:
                break
                
            template_id = os.path.splitext(filename)[0]
            filepath = os.path.join(MEMES_DIR, filename)
            
            try:
                with open(filepath, 'r', encoding='utf-8') as f:
                    memes = json.load(f)
                
                if not isinstance(memes, list):
                    continue
                
                # Load up to 10 memes per template for sampling
                for meme_idx, meme in enumerate(memes[:10]):
                    if sample_size and len(captions) >= sample_size:
                        break
                        
                    boxes = meme.get('boxes', [])
                    if not boxes:
                        continue
                    
                    # Create caption text
                    caption_text = ' | '.join(str(box) for box in boxes)
                    
                    # Get metadata
                    title = meme.get('metadata', {}).get('title', '')
                    url = meme.get('url', '')
                    post_url = meme.get('post', '')
                    
                    captions.append({
                        'caption_id': f"{template_id}_{meme_idx}",
                        'template_id': template_id,
                        'meme_idx': meme_idx,
                        'text': caption_text,
                        'title': title,
                        'url': url,
                        'post_url': post_url,
                        'boxes': boxes
                    })
                
                files_loaded += 1
                if files_loaded % 10 == 0:
                    logger.info(f"Loaded {len(captions)} captions from {files_loaded} files...")
                
            except Exception as e:
                logger.warning(f"Error loading {filename}: {e}")
                continue
        
        logger.info(f"Loaded {len(captions)} captions from {files_loaded} files")
        return captions
    
    def _embed_captions(self, captions: List[str]) -> np.ndarray:
        """
        Generate embeddings for a list of captions.
        
        Args:
            captions: List of caption texts.
            
        Returns:
            numpy array of embeddings.
        """
        self._load_model()
        
        embeddings = self.model.encode(
            captions,
            batch_size=64,
            show_progress_bar=True,
            convert_to_numpy=True,
            normalize_embeddings=True  # L2 normalize for cosine similarity
        )
        
        return embeddings
    
    def build_index(self, force_rebuild: bool = False, sample_size: int = 1000) -> int:
        """
        Build the caption embedding index.
        
        Args:
            force_rebuild: If True, rebuild even if cache exists.
            sample_size: Number of captions to sample (None for all).
            
        Returns:
            Number of captions indexed.
        """
        # Check cache
        if not force_rebuild and os.path.exists(CAPTION_CACHE_PATH) and os.path.exists(CAPTION_DATA_CACHE_PATH):
            logger.info("Loading cached caption embeddings...")
            try:
                self.caption_embeddings = np.load(CAPTION_CACHE_PATH)
                with open(CAPTION_DATA_CACHE_PATH, 'r', encoding='utf-8') as f:
                    self.caption_data = json.load(f)
                logger.info(f"Loaded {len(self.caption_data)} cached captions")
                self._initialized = True
                return len(self.caption_data)
            except Exception as e:
                logger.warning(f"Cache load failed, rebuilding: {e}")
        
        # Build new index with sampling
        logger.info(f"Building caption index (sample_size={sample_size})...")
        self.caption_data = self._load_captions(sample_size=sample_size)
        
        if not self.caption_data:
            logger.error("No captions found to index")
            return 0
        
        # Extract texts
        texts = [c['text'] for c in self.caption_data]
        
        # Generate embeddings
        logger.info(f"Generating embeddings for {len(texts)} captions...")
        self.caption_embeddings = self._embed_captions(texts)
        
        # Save cache
        os.makedirs(CACHE_DIR, exist_ok=True)
        np.save(CAPTION_CACHE_PATH, self.caption_embeddings)
        with open(CAPTION_DATA_CACHE_PATH, 'w', encoding='utf-8') as f:
            json.dump(self.caption_data, f, indent=2, ensure_ascii=False)
        
        logger.info(f"Saved caption index: {self.caption_embeddings.shape}")
        self._initialized = True
        return len(self.caption_data)
    
    def search(self, query: str, top_k: int = 5, min_score: float = 0.3) -> List[Dict[str, Any]]:
        """
        Search for captions semantically similar to the query.
        
        Args:
            query: Search query string.
            top_k: Number of results to return.
            min_score: Minimum cosine similarity threshold.
            
        Returns:
            List of matching captions with scores.
        """
        if not self._initialized:
            self.build_index()
        
        if self.caption_embeddings is None or len(self.caption_data) == 0:
            logger.warning("Caption index not built")
            return []
        
        self._load_model()
        
        # Embed query
        query_embedding = self.model.encode(
            [query],
            convert_to_numpy=True,
            normalize_embeddings=True
        )[0]
        
        # Compute cosine similarity (dot product for normalized vectors)
        scores = np.dot(self.caption_embeddings, query_embedding)
        
        # Get top-k indices
        top_indices = np.argsort(scores)[::-1][:top_k * 3]  # Get more for filtering
        
        # Filter by min_score and format results
        results = []
        for idx in top_indices:
            if scores[idx] >= min_score:
                result = self.caption_data[idx].copy()
                result['score'] = float(scores[idx])
                results.append(result)
            
            if len(results) >= top_k:
                break
        
        logger.info(f"Search '{query}': found {len(results)} results")
        return results
    
    def search_by_template(self, template_id: str, top_k: int = 5) -> List[Dict[str, Any]]:
        """
        Find captions similar to those in a specific template.
        
        Args:
            template_id: The template ID to find similar captions for.
            top_k: Number of results to return.
            
        Returns:
            List of similar captions from other templates.
        """
        # Get captions for this template
        template_captions = [c for c in self.caption_data if c['template_id'] == template_id]
        
        if not template_captions:
            logger.warning(f"No captions found for template: {template_id}")
            return []
        
        # Use first caption as query
        query_text = template_captions[0]['text']
        
        # Search
        results = self.search(query_text, top_k=top_k * 2)
        
        # Filter out same template
        results = [r for r in results if r['template_id'] != template_id][:top_k]
        
        return results


# Global engine instance
_engine: Optional[CaptionSearchEngine] = None


def _get_engine() -> CaptionSearchEngine:
    """Get or create the global search engine."""
    global _engine
    if _engine is None:
        _engine = CaptionSearchEngine()
    return _engine


def search_templates(query: str, top_k: int = 5, min_score: float = 0.3) -> List[Dict[str, Any]]:
    """
    Search for meme templates/captions semantically similar to the query.
    
    Args:
        query: Search query (e.g., "I failed my exam", "birthday party")
        top_k: Number of results to return.
        min_score: Minimum cosine similarity threshold (0.0-1.0).
        
    Returns:
        List of matching captions with metadata and scores.
        
    Example:
        >>> results = search_templates("I failed my exam", top_k=3)
        >>> for r in results:
        ...     print(f"{r['score']:.3f}: {r['text']}")
    """
    engine = _get_engine()
    return engine.search(query, top_k=top_k, min_score=min_score)


def build_caption_index(force_rebuild: bool = False, sample_size: int = 1000) -> int:
    """
    Build the caption embedding index.
    
    Args:
        force_rebuild: If True, rebuild even if cache exists.
        sample_size: Number of captions to sample (None for all).
        
    Returns:
        Number of captions indexed.
    """
    engine = _get_engine()
    return engine.build_index(force_rebuild=force_rebuild, sample_size=sample_size)


def get_template_examples(template_id: str, limit: int = 3) -> List[str]:
    """
    Get example captions for a specific template.
    
    Args:
        template_id: The template ID.
        limit: Maximum number of examples to return.
        
    Returns:
        List of caption texts.
    """
    engine = _get_engine()
    if not engine._initialized:
        engine.build_index()
    
    examples = [c for c in engine.caption_data if c['template_id'] == template_id]
    return [c['text'] for c in examples[:limit]]


if __name__ == "__main__":
    # Test the search engine
    print("=" * 60)
    print("Caption Search Engine Test")
    print("=" * 60)
    
    # Build index
    print("\nBuilding index...")
    count = build_caption_index()
    print(f"Indexed {count} captions")
    
    # Test queries
    test_queries = [
        "I failed my exam",
        "birthday party celebration",
        "monday morning tired",
        "when you finally understand",
        "me pretending to work"
    ]
    
    print("\n" + "=" * 60)
    print("Search Results")
    print("=" * 60)
    
    for query in test_queries:
        print(f"\nQuery: '{query}'")
        results = search_templates(query, top_k=3)
        
        if results:
            for i, r in enumerate(results, 1):
                print(f"  {i}. (score: {r['score']:.3f}) [{r['template_id']}] {r['text'][:80]}...")
        else:
            print("  No results found")
