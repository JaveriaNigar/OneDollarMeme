#!/usr/bin/env python3
"""
Backend server for the Meme Agent
"""

import uvicorn
import argparse
import os
from pathlib import Path

def main():
    parser = argparse.ArgumentParser(description='Run the Meme Agent Backend Server')
    parser.add_argument('--host', default='127.0.0.1', help='Host to bind to')
    parser.add_argument('--port', type=int, default=8000, help='Port to bind to')
    parser.add_argument('--reload', action='store_true', help='Enable auto-reload')
    
    args = parser.parse_args()
    
    # Change to the backend directory to ensure proper imports
    backend_dir = Path(__file__).parent
    os.chdir(backend_dir)
    
    # Run the server
    uvicorn.run(
        "main:app",
        host=args.host,
        port=args.port,
        reload=args.reload,
        log_level="info"
    )

if __name__ == "__main__":
    main()