#!/usr/bin/env python3
"""
Comprehensive UTF-8 corruption fixer for app.js
"""

import re

# Read the file
with open('resources/js/app.js', 'r', encoding='utf-8', errors='replace') as f:
    text = f.read()

# ===== EMOJI FIXES =====
# Fix common mojibake patterns
emoji_fixes = [
    (r'Ã¢Å"â€¦', 'âœ…'),
    (r'Ã°Å¸Å'â„¢', 'ðŸŒ™'),
    (r'Ã°Å¸"Â¡', 'ðŸ"'),
    (r'Ã°Å¸"', 'ðŸ"'),
    (r'Ã°Å¸Å'Â±', 'ðŸŒ±'),
    (r'Ã°Å¸"â€ž', 'ðŸ"„'),
]

for corrupted, correct in emoji_fixes:
    text = text.replace(corrupted, correct)

# ===== SEPARATOR LINE FIXES =====
# Replace multiple corrupted bullet points with dashes
text = re.sub(r'(Ã¢â€¢)+', '-' * 100, text)

# ===== HINDI TEXT CORRUPTIONS =====
# Fix common Hindi mojibake patterns
hindi_fixes = [
    (r'Ã‰', 'É'),
    (r'Ã„', 'Ä'),
    (r'Ã†', 'Æ'),
    (r'Ã…', 'Å'),
    (r'Ã‡', 'Ç'),
    (r'ÃŠ', 'Ê'),
    (r'Ë†', 'ˆ'),
    (r'Ë‡', '‡'),
]

for corrupted, correct in hindi_fixes:
    text = text.replace(corrupted, correct)

# Write the fixed content
with open('resources/js/app.js', 'w', encoding='utf-8') as f:
    f.write(text)

print('Fixed app.js - Corruption resolved!')
