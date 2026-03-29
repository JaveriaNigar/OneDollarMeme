import os
import json
import time
import logging
from typing import List, Dict, Optional, Any
from pathlib import Path

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

DATASET_DIR = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "ImgFlip575K_Dataset", "dataset")
TEMPLATES_DIR = os.path.join(DATASET_DIR, "templates")
MEMES_DIR = os.path.join(DATASET_DIR, "memes")

# Vector search configuration
VECTOR_INDEX_PATH = os.path.join(
    os.path.dirname(os.path.dirname(os.path.abspath(__file__))),
    "storage", "vector_index", "index.faiss"
)
VECTOR_ID_MAP_PATH = os.path.join(
    os.path.dirname(os.path.dirname(os.path.abspath(__file__))),
    "storage", "vector_index", "id_map.json"
)
VECTOR_METADATA_PATH = os.path.join(
    os.path.dirname(os.path.dirname(os.path.abspath(__file__))),
    "storage", "vector_index", "metadata.json"
)
MEME_DATA_LOOKUP_PATH = os.path.join(
    os.path.dirname(os.path.dirname(os.path.abspath(__file__))),
    "storage", "vector_index", "meme_data.json"
)

# Score threshold for vector search results
VECTOR_SCORE_THRESHOLD = 0.35

# Global vector store cache
_vector_store = None
_meme_data_lookup = None


def _get_vector_store():
    """
    Load and cache the vector store.
    Returns None if index doesn't exist or fails to load.
    """
    global _vector_store, _meme_data_lookup
    
    if _vector_store is not None:
        return _vector_store
    
    try:
        from vector_store import VectorStore
        
        store = VectorStore(
            index_path=VECTOR_INDEX_PATH,
            id_map_path=VECTOR_ID_MAP_PATH,
            metadata_path=VECTOR_METADATA_PATH
        )
        
        if store.load_index():
            _vector_store = store
            logger.info(f"Vector index loaded: {store.size()} vectors")
            
            # Load meme data lookup
            if os.path.exists(MEME_DATA_LOOKUP_PATH):
                with open(MEME_DATA_LOOKUP_PATH, 'r', encoding='utf-8') as f:
                    _meme_data_lookup = json.load(f)
                logger.info(f"Meme data lookup loaded: {len(_meme_data_lookup)} entries")
            
            return store
        else:
            logger.warning("Vector index not found or failed to load")
            return None
            
    except ImportError as e:
        logger.warning(f"Vector search modules not available: {e}")
        return None
    except Exception as e:
        logger.error(f"Error loading vector store: {e}")
        return None


def _get_meme_by_id(meme_id: str) -> Optional[Dict[str, Any]]:
    """
    Get meme data by ID from the lookup table.
    
    Args:
        meme_id: The meme ID (e.g., "Drake-Hotline-Bling_0").
    
    Returns:
        Meme data dictionary or None if not found.
    """
    global _meme_data_lookup
    
    if _meme_data_lookup is None:
        if os.path.exists(MEME_DATA_LOOKUP_PATH):
            try:
                with open(MEME_DATA_LOOKUP_PATH, 'r', encoding='utf-8') as f:
                    _meme_data_lookup = json.load(f)
            except Exception as e:
                logger.error(f"Error loading meme data lookup: {e}")
                return None
        else:
            return None
    
    # Parse meme_id to get template_id and index
    parts = meme_id.rsplit('_', 1)
    if len(parts) != 2:
        return None
    
    template_id, idx_str = parts
    try:
        idx = int(idx_str)
    except ValueError:
        return None
    
    # Search for matching entry
    for entry in _meme_data_lookup:
        if entry.get('template_id') == template_id and entry.get('meme_idx') == idx:
            return entry
    
    return None


def _format_search_result(
    meme_data: Dict[str, Any],
    score: float,
    source: str
) -> Dict[str, Any]:
    """
    Format a meme search result into standard output format.
    
    Args:
        meme_data: Meme data dictionary.
        score: Similarity score.
        source: Source of the result ("local_vector", "local_keyword", "web").
    
    Returns:
        Formatted result dictionary.
    """
    return {
        "caption": meme_data.get('caption', ''),
        "image_url": meme_data.get('url', ''),
        "post_url": meme_data.get('post_url', ''),
        "template_id": meme_data.get('template_id', ''),
        "title": meme_data.get('title', ''),
        "source": source,
        "score": round(score, 4)
    }


def semantic_search(query: str, top_k: int = 10) -> List[Dict[str, Any]]:
    """
    Search memes using vector similarity search.
    
    Args:
        query: Search query string.
        top_k: Number of results to return.
    
    Returns:
        List of formatted search results with scores.
    """
    start_time = time.time()
    
    store = _get_vector_store()
    if store is None or store.is_empty():
        logger.warning("Vector store not available for semantic search")
        return []
    
    try:
        from embeddings import generate_query_embedding
        
        # Generate query embedding
        query_emb = generate_query_embedding(query)
        
        # Search
        ids, scores = store.search(
            query_emb,
            top_k=top_k,
            score_threshold=VECTOR_SCORE_THRESHOLD
        )
        
        # Format results
        results = []
        for meme_id, score in zip(ids, scores):
            meme_data = _get_meme_by_id(meme_id)
            if meme_data:
                results.append(_format_search_result(meme_data, score, "local_vector"))
        
        elapsed = time.time() - start_time
        logger.info(f"Semantic search: {len(results)} results in {elapsed:.3f}s")
        
        return results
        
    except Exception as e:
        logger.error(f"Semantic search error: {e}")
        return []


def keyword_search(query: str, limit: int = 10) -> List[Dict[str, Any]]:
    """
    Search memes using keyword matching (original functionality).
    
    Args:
        query: Search query string.
        limit: Maximum number of results to return.
    
    Returns:
        List of formatted search results.
    """
    start_time = time.time()
    query = query.lower().strip()
    matches = []
    
    if not os.path.exists(MEMES_DIR):
        return []
    
    files = [f for f in os.listdir(MEMES_DIR) if f.endswith(".json")]
    
    for filename in files:
        template_id = os.path.splitext(filename)[0]
        path = os.path.join(MEMES_DIR, filename)
        
        try:
            with open(path, "r", encoding="utf-8") as f:
                data = json.load(f)
            
            for meme_idx, meme in enumerate(data):
                boxes = meme.get("boxes", [])
                if not boxes:
                    continue
                
                full_text = " ".join(str(box) for box in boxes).lower()
                url = meme.get("url", "")
                title = meme.get("metadata", {}).get("title", "").lower()
                
                matched = False
                if query in full_text:
                    matched = True
                elif query in title:
                    matched = True
                
                if matched:
                    meme_data = {
                        "template_id": template_id,
                        "meme_idx": meme_idx,
                        "caption": " ".join(boxes),
                        "title": meme.get("metadata", {}).get("title", ""),
                        "url": url,
                        "post_url": meme.get("post", "")
                    }
                    # Use high score for keyword matches
                    matches.append(_format_search_result(meme_data, 0.8, "local_keyword"))
                
                if len(matches) >= limit * 2:
                    break
                    
        except Exception as e:
            continue
        
        if len(matches) >= limit * 5:
            break
    
    # Remove duplicates and return
    seen = set()
    unique_matches = []
    for m in matches:
        key = m["caption"]
        if key not in seen:
            seen.add(key)
            unique_matches.append(m)
    
    elapsed = time.time() - start_time
    logger.info(f"Keyword search: {len(unique_matches)} results in {elapsed:.3f}s")
    
    return unique_matches[:limit]


def hybrid_search(
    query: str,
    top_k: int = 10,
    vector_threshold: int = 3
) -> List[Dict[str, Any]]:
    """
    Hybrid search: Try vector search first, fallback to keyword if needed.
    
    Search priority:
    1. Vector search (semantic similarity)
    2. Keyword search (if vector returns fewer than threshold results)
    
    Args:
        query: Search query string.
        top_k: Maximum number of results to return.
        vector_threshold: Minimum vector results before fallback.
    
    Returns:
        List of formatted search results, deduplicated and ranked.
    """
    start_time = time.time()
    
    # Step 1: Try vector search
    vector_results = semantic_search(query, top_k=top_k)
    
    if len(vector_results) >= vector_threshold:
        # Good results from vector search
        elapsed = time.time() - start_time
        logger.info(f"Hybrid search (vector): {len(vector_results)} results in {elapsed:.3f}s")
        return vector_results[:top_k]
    
    # Step 2: Fallback to keyword search
    logger.info(f"Vector search returned {len(vector_results)} results, trying keyword fallback...")
    keyword_results = keyword_search(query, limit=top_k)
    
    # Combine results (vector first, then keyword)
    # Deduplicate by caption
    seen = set()
    combined = []
    
    for result in vector_results:
        key = result["caption"]
        if key not in seen:
            seen.add(key)
            combined.append(result)
    
    for result in keyword_results:
        key = result["caption"]
        if key not in seen:
            seen.add(key)
            combined.append(result)
    
    # Sort by score (descending)
    combined.sort(key=lambda x: x["score"], reverse=True)
    
    elapsed = time.time() - start_time
    logger.info(f"Hybrid search (combined): {len(combined)} results in {elapsed:.3f}s")
    
    return combined[:top_k]


class MemeTemplate:
    def __init__(self, id: str, name: str, url: str, dimensions: str):
        self.id = id
        self.name = name
        self.url = url
        self.dimensions = dimensions

    def to_dict(self):
        return {
            "id": self.id,
            "name": self.name,
            "url": self.url,
            "dimensions": self.dimensions
        }


def load_templates() -> List[MemeTemplate]:
    """Load all meme templates from the dataset."""
    templates = []
    if not os.path.exists(TEMPLATES_DIR):
        print(f"Warning: Templates directory not found at {TEMPLATES_DIR}")
        return []

    for filename in os.listdir(TEMPLATES_DIR):
        if filename.endswith(".json"):
            try:
                path = os.path.join(TEMPLATES_DIR, filename)
                with open(path, "r", encoding="utf-8") as f:
                    data = json.load(f)
                    # Use filename without extension as ID
                    template_id = os.path.splitext(filename)[0]
                    templates.append(MemeTemplate(
                        id=template_id,
                        name=data.get("title", template_id),
                        url=data.get("template_url", ""),
                        dimensions=data.get("dimensions", "")
                    ))
            except Exception as e:
                print(f"Error loading template {filename}: {e}")

    # Sort by name for consistent display
    templates.sort(key=lambda x: x.name)
    return templates


def get_template_examples(template_id: str, limit: int = 3) -> List[str]:
    """Get example captions for a specific template."""
    examples = []
    meme_path = os.path.join(MEMES_DIR, f"{template_id}.json")

    if os.path.exists(meme_path):
        try:
            with open(meme_path, "r", encoding="utf-8") as f:
                data = json.load(f)
                # data is a list of meme objects
                for meme in data[:limit]:
                    boxes = meme.get("boxes", [])
                    if boxes:
                        examples.append(" | ".join(boxes))
        except Exception as e:
            print(f"Error loading examples for {template_id}: {e}")

    return examples


def get_all_template_ids() -> List[str]:
    return [os.path.splitext(f)[0] for f in os.listdir(TEMPLATES_DIR) if f.endswith(".json")]


def search_memes(query: str, limit: int = 3) -> List[str]:
    """
    Search the local dataset for memes matching the query.
    Uses hybrid search (vector + keyword fallback).
    Returns formatted strings for backward compatibility.
    
    Args:
        query: Search query string.
        limit: Maximum number of results to return.
    
    Returns:
        List of formatted strings: "caption\\nimage_url"
    """
    # Use hybrid search
    results = hybrid_search(query, top_k=limit * 2)
    
    # Format for backward compatibility
    formatted = []
    for result in results:
        caption = result.get("caption", "")
        url = result.get("image_url", "")
        
        if url:
            formatted.append(f"{caption}\n{url}")
        else:
            formatted.append(caption)
        
        if len(formatted) >= limit:
            break
    
    return formatted


def search_memes_structured(query: str, limit: int = 10) -> List[Dict[str, Any]]:
    """
    Search memes and return structured data (new API).
    
    Args:
        query: Search query string.
        limit: Maximum number of results to return.
    
    Returns:
        List of result dictionaries with caption, url, source, score, etc.
    """
    results = hybrid_search(query, top_k=limit)
    return results
