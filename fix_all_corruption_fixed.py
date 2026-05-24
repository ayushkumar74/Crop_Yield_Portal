#!/usr/bin/env python3
"""
Comprehensive UTF-8 corruption fixer for app.js
Fixes: emoji mojibake, Hindi text corruption, separator lines
"""

import re

# Read the file
with open('resources/js/app.js', 'r', encoding='utf-8', errors='replace') as f:
    text = f.read()

# ===== EMOJI FIXES =====
emoji_fixes = {
    'Ã¢Å"â€¦': 'âœ…',
    'Ã¢Å'': 'â',
    'Ã¢Â³': 'â³',
    'Ã¢Ëœâ‚¬Ã¯Â¸': 'â˜€ï¸',
    'Ã°Å¸Å'â„¢': 'ðŸŒ™',
    'Ã°Å¸"Â¡': 'ðŸ"',
    'Ã°Å¸"': 'ðŸ"',
    'Ã°Å¸Å'Â±': 'ðŸŒ±',
    'Ã°Å¸"Â¡': 'ðŸ"',
    'Ã°Å¸"â€ž': 'ðŸ"„',
}

for corrupted, correct in emoji_fixes.items():
    text = text.replace(corrupted, correct)

# ===== SEPARATOR LINE FIXES (horizontal bars) =====
text = re.sub(r'(Ã¢â€¢)+', 'â€"' * 100, text)

# Replace arrow patterns
text = text.replace('Ã¢â€ ', '→')
text = text.replace('Ã¢â€ ', '→')
text = text.replace('Ã¢â€ ', '→')

# ===== HINDI TEXT CORRUPTIONS =====
hindi_fixes = {
    'Ã‰': 'É',
    'Ã„': 'Ä',
    'Ã†': 'Æ',
    'Ã…': 'Å',
    'Ã‡': 'Ç',
    'ÃŠ': 'Ê',
    'Ã‰': 'É',
    'Ë†': 'ˆ',
    'Ë‡': '‡',
}

for corrupted, correct in hindi_fixes.items():
    text = text.replace(corrupted, correct)

# Write the fixed content
with open('resources/js/app.js', 'w', encoding='utf-8') as f:
    f.write(text)

print('✓ Fixed app.js - Corruption resolved!')
