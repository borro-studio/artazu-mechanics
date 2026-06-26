#!/usr/bin/env python3
import os, sys
from http.server import SimpleHTTPRequestHandler, HTTPServer

port = int(os.environ.get('PORT', 4201))
os.chdir(os.path.dirname(os.path.abspath(__file__)))
HTTPServer(('', port), SimpleHTTPRequestHandler).serve_forever()
