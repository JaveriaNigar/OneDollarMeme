#!/usr/bin/env python3
"""Wrapper script to run vector index build with logging."""

import os
import sys
from datetime import datetime

# Redirect stdout/stderr to log file
log_dir = os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(__file__))), 'storage', 'vector_index')
os.makedirs(log_dir, exist_ok=True)
log_path = os.path.join(log_dir, 'build_log.txt')

# Open log file for writing
log_file = open(log_path, 'w', encoding='utf-8')

class Logger:
    def __init__(self, file):
        self.terminal = sys.stdout
        self.log = file
    
    def write(self, message):
        self.terminal.write(message)
        self.terminal.flush()
        self.log.write(message)
        self.log.flush()
    
    def flush(self):
        self.terminal.flush()
        self.log.flush()
    
    def isatty(self):
        return True

sys.stdout = Logger(log_file)
sys.stderr = Logger(log_file)

# Now run the build script
exec(open(os.path.join(os.path.dirname(__file__), 'build_vector_index.py')).read())

log_file.close()
