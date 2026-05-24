#!/usr/bin/env python3
"""Fix UTF-8 corruption using escape sequences"""

# Read the file as-is
with open('resources/js/app.js', 'rb') as f:
    content = f.read()

# Convert to string with UTF-8 handling
text = content.decode('utf-8', errors='replace')

# EMOJI FIXES using unicode escapes
fixes = [
    ('\u00e2\u0153\u2026', '✅'),  # corrupted check mark
    ('\u00e2\u008c', '❌'),  # corrupted X
    ('\u00e2\u0084\u00b9\u00ef\u00b8\u0087', 'ℹ️'),  # corrupted info
    ('\u00e2\u0081\u00b3', '⏳'),  # corrupted hourglass
    ('\u00e2\u0098\u0080\u00ef\u00b8\u0087', '☀️'),  # corrupted sun
    ('\u00f0\u009f\u0098\u0099', '🌙'),  # corrupted moon
    ('\u00f0\u009f\u0093\u00a1', '📍'),  # corrupted pin
    ('\u00f0\u009f\u0093\u00a4', '📤'),  # corrupted up arrow
    ('\u00f0\u009f\u008c\u00b1', '🌱'),  # corrupted seedling
]

for corrupted, correct in fixes:
    text = text.replace(corrupted, correct)

# DEGREE SYMBOL
text = text.replace('\u00c2\u00b0', '°')  # Corrupted degree symbol
text = text.replace('Â°', '°')

# Save as UTF-8 without BOM
with open('resources/js/app.js', 'w', encoding='utf-8') as f:
    f.write(text)

print("Fix applied - check file")
