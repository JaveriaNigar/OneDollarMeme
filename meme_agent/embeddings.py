"""
Embedding generation module for semantic meme search.
Uses sentence-transformers with the lightweight all-MiniLM-L6-v2 model.
"""

from typing import List, Optional
import numpy as np
import os

# Suppress HuggingFace warnings
os.environ['TRANSFORMERS_VERBOSITY'] = 'error'
os.environ['HF_HUB_DISABLE_PROGRESS_BARS'] = '1'


def get_embedding_model(model_name: str = "all-MiniLM-L6-v2"):
    """
    Load the sentence-transformers embedding model.
    Model is cached globally for reuse.

    Args:
        model_name: Name of the sentence-transformers model to use.
                   Default is 'all-MiniLM-L6-v2' (80MB, fast, good quality).

    Returns:
        The loaded SentenceTransformer model.
    """
    from sentence_transformers import SentenceTransformer
    import logging
    
    # Set logging levels
    logging.getLogger('sentence_transformers').setLevel(logging.ERROR)
    logging.getLogger('transformers').setLevel(logging.ERROR)
    logging.getLogger('huggingface_hub').setLevel(logging.ERROR)
    
    return SentenceTransformer(model_name, trust_remote_code=True)


def generate_embeddings(
    texts: List[str],
    batch_size: int = 64,
    model_name: Optional[str] = None,
    show_progress: bool = False
) -> np.ndarray:
    """
    Generate embeddings for a list of texts in batches.
    
    Args:
        texts: List of text strings to embed.
        batch_size: Number of texts to process per batch (default 64).
        model_name: Optional model name override.
        show_progress: Whether to show progress bar.
    
    Returns:
        numpy array of shape (len(texts), embedding_dim).
    """
    if not texts:
        return np.array([]).reshape(0, 384)
    
    model_name = model_name or "all-MiniLM-L6-v2"
    model = get_embedding_model(model_name)
    
    # Filter out empty strings but keep track of indices
    valid_texts = []
    valid_indices = []
    for i, text in enumerate(texts):
        if text and isinstance(text, str) and text.strip():
            valid_texts.append(text.strip())
            valid_indices.append(i)
    
    if not valid_texts:
        # Return zeros for all inputs
        return np.zeros((len(texts), 384))
    
    # Generate embeddings in batches
    embeddings = model.encode(
        valid_texts,
        batch_size=batch_size,
        show_progress_bar=show_progress,
        convert_to_numpy=True,
        normalize_embeddings=True  # L2 normalize for cosine similarity
    )
    
    # Create full array with zeros for invalid inputs
    full_embeddings = np.zeros((len(texts), embeddings.shape[1]))
    for idx, emb_idx in enumerate(valid_indices):
        full_embeddings[emb_idx] = embeddings[idx]
    
    return full_embeddings


def generate_query_embedding(query: str, model_name: Optional[str] = None) -> np.ndarray:
    """
    Generate a single embedding for a search query.
    
    Args:
        query: The search query string.
        model_name: Optional model name override.
    
    Returns:
        numpy array of shape (1, embedding_dim).
    """
    if not query or not isinstance(query, str) or not query.strip():
        return np.zeros((1, 384))
    
    model_name = model_name or "all-MiniLM-L6-v2"
    model = get_embedding_model(model_name)
    embedding = model.encode(
        [query.strip()],
        convert_to_numpy=True,
        normalize_embeddings=True
    )
    return embedding


def get_embedding_dimension(model_name: Optional[str] = None) -> int:
    """
    Get the embedding dimension for the model.
    
    Returns:
        int: The dimension of embeddings (384 for all-MiniLM-L6-v2).
    """
    model_name = model_name or "all-MiniLM-L6-v2"
    model = get_embedding_model(model_name)
    return model.get_sentence_embedding_dimension()
