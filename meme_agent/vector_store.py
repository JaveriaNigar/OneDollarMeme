"""
FAISS vector store module for semantic meme search.
Handles index creation, persistence, and similarity search.
"""

import os
import json
import pickle
from typing import List, Tuple, Optional, Dict, Any
import numpy as np


class VectorStore:
    """
    FAISS-based vector store for meme embeddings.
    Uses IndexFlatIP (inner product) which equals cosine similarity
    when vectors are L2-normalized.
    """
    
    def __init__(
        self,
        index_path: str,
        id_map_path: str,
        metadata_path: Optional[str] = None,
        embedding_dim: int = 384
    ):
        """
        Initialize the vector store.
        
        Args:
            index_path: Path to the FAISS index file (.faiss).
            id_map_path: Path to the ID mapping file (.json or .pkl).
            metadata_path: Optional path to metadata file for additional data.
            embedding_dim: Dimension of embeddings (default 384 for all-MiniLM-L6-v2).
        """
        self.index_path = index_path
        self.id_map_path = id_map_path
        self.metadata_path = metadata_path
        self.embedding_dim = embedding_dim
        self.index = None
        self.id_map: Dict[int, str] = {}  # FAISS index -> meme_id
        self.reverse_id_map: Dict[str, int] = {}  # meme_id -> FAISS index
        self.metadata: Dict[str, Any] = {}
    
    def load_index(self) -> bool:
        """
        Load existing FAISS index and ID mapping from disk.
        
        Returns:
            True if index was loaded successfully, False otherwise.
        """
        try:
            import faiss
            
            if not os.path.exists(self.index_path):
                return False
            
            # Load FAISS index
            self.index = faiss.read_index(self.index_path)
            
            # Load ID mapping
            if os.path.exists(self.id_map_path):
                if self.id_map_path.endswith('.pkl'):
                    with open(self.id_map_path, 'rb') as f:
                        self.id_map = pickle.load(f)
                else:
                    with open(self.id_map_path, 'r', encoding='utf-8') as f:
                        self.id_map = json.load(f)
                
                # Convert keys to int if needed
                self.id_map = {int(k): v for k, v in self.id_map.items()}
                self.reverse_id_map = {v: k for k, v in self.id_map.items()}
            
            # Load metadata if available
            if self.metadata_path and os.path.exists(self.metadata_path):
                with open(self.metadata_path, 'r', encoding='utf-8') as f:
                    self.metadata = json.load(f)
            
            return True
            
        except Exception as e:
            print(f"Error loading vector index: {e}")
            self.index = None
            return False
    
    def build_index(self, embeddings: np.ndarray, ids: List[str]) -> None:
        """
        Build a new FAISS index from embeddings.
        
        Args:
            embeddings: numpy array of shape (n_samples, embedding_dim).
            ids: List of string IDs corresponding to each embedding.
        """
        import faiss
        
        if len(embeddings) == 0:
            raise ValueError("Cannot build index with empty embeddings")
        
        if len(embeddings) != len(ids):
            raise ValueError("Embeddings and IDs must have same length")
        
        # Ensure embeddings are float32 and normalized
        embeddings = embeddings.astype(np.float32)
        
        # Build index using Inner Product (equals cosine similarity for normalized vectors)
        self.index = faiss.IndexFlatIP(self.embedding_dim)
        self.index.add(embeddings)
        
        # Build ID mapping
        self.id_map = {i: id_ for i, id_ in enumerate(ids)}
        self.reverse_id_map = {id_: i for i, id_ in enumerate(ids)}
    
    def save_index(self) -> None:
        """
        Save the FAISS index and ID mapping to disk.
        """
        import faiss
        
        if self.index is None:
            raise ValueError("No index to save")
        
        # Ensure directory exists
        os.makedirs(os.path.dirname(self.index_path), exist_ok=True)
        
        # Save FAISS index
        faiss.write_index(self.index, self.index_path)
        
        # Save ID mapping
        os.makedirs(os.path.dirname(self.id_map_path), exist_ok=True)
        if self.id_map_path.endswith('.pkl'):
            with open(self.id_map_path, 'wb') as f:
                pickle.dump(self.id_map, f)
        else:
            with open(self.id_map_path, 'w', encoding='utf-8') as f:
                json.dump(self.id_map, f, indent=2)
        
        # Save metadata if path is set
        if self.metadata_path:
            with open(self.metadata_path, 'w', encoding='utf-8') as f:
                json.dump(self.metadata, f, indent=2)
    
    def search(
        self,
        query_embedding: np.ndarray,
        top_k: int = 10,
        score_threshold: float = 0.0
    ) -> Tuple[List[str], List[float]]:
        """
        Search for similar vectors using cosine similarity.
        
        Args:
            query_embedding: numpy array of shape (1, embedding_dim) or (embedding_dim,).
            top_k: Number of results to return.
            score_threshold: Minimum similarity score to include (0.0 = no filter).
        
        Returns:
            Tuple of (ids, scores) where:
                - ids: List of meme IDs for top-k results.
                - scores: List of cosine similarity scores.
        """
        import faiss
        
        if self.index is None or self.index.ntotal == 0:
            return [], []
        
        # Ensure query is 2D
        if query_embedding.ndim == 1:
            query_embedding = query_embedding.reshape(1, -1)
        
        query_embedding = query_embedding.astype(np.float32)
        
        # Limit k to available vectors
        k = min(top_k, self.index.ntotal)
        
        # Search using inner product (cosine similarity for normalized vectors)
        scores, indices = self.index.search(query_embedding, k)
        
        # Convert to lists
        scores = scores[0].tolist()
        indices = indices[0].tolist()
        
        # Map FAISS indices to meme IDs
        ids = [self.id_map.get(idx, f"unknown_{idx}") for idx in indices]
        
        # Apply score threshold
        if score_threshold > 0:
            filtered_ids = []
            filtered_scores = []
            for id_, score in zip(ids, scores):
                if score >= score_threshold:
                    filtered_ids.append(id_)
                    filtered_scores.append(score)
            ids = filtered_ids
            scores = filtered_scores
        
        return ids, scores
    
    def add_embeddings(
        self,
        embeddings: np.ndarray,
        ids: List[str],
        start_idx: Optional[int] = None
    ) -> None:
        """
        Add new embeddings to the existing index.
        
        Args:
            embeddings: numpy array of shape (n_samples, embedding_dim).
            ids: List of string IDs for new embeddings.
            start_idx: Starting FAISS index (auto-calculated if None).
        """
        import faiss
        
        if self.index is None:
            raise ValueError("No index loaded. Call build_index() or load_index() first.")
        
        embeddings = embeddings.astype(np.float32)
        
        if start_idx is None:
            start_idx = self.index.ntotal
        
        # Add to FAISS index
        self.index.add(embeddings)
        
        # Update ID mapping
        for i, id_ in enumerate(ids):
            faiss_idx = start_idx + i
            self.id_map[faiss_idx] = id_
            self.reverse_id_map[id_] = faiss_idx
    
    def is_empty(self) -> bool:
        """
        Check if the index is empty or not loaded.
        
        Returns:
            True if index is empty or not loaded.
        """
        return self.index is None or self.index.ntotal == 0
    
    def size(self) -> int:
        """
        Get the number of vectors in the index.
        
        Returns:
            Number of vectors.
        """
        if self.index is None:
            return 0
        return self.index.ntotal
    
    def get_metadata(self, key: str, default: Any = None) -> Any:
        """
        Get metadata value by key.
        
        Args:
            key: Metadata key.
            default: Default value if key not found.
        
        Returns:
            Metadata value.
        """
        return self.metadata.get(key, default)
    
    def set_metadata(self, key: str, value: Any) -> None:
        """
        Set metadata value.
        
        Args:
            key: Metadata key.
            value: Metadata value.
        """
        self.metadata[key] = value
