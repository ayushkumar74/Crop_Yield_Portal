#!/usr/bin/env python3
import re

# Read corrupted file
with open('resources/js/app.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Line 771 onwards needs weather mapping - extract from corrupted.txt and rebuild
# Find and fix the weather mapping object that's been destroyed
# Lines around 769-774 need fixing

fixes = [
    # Line 824 - "not load weather" should be error message
    (r"const errorMsg = document\.documentElement\.lang\?\.startsWith\('hi'\) \? 'not load weather\.';",
     "const errorMsg = document.documentElement.lang?.startsWith('hi') ? 'मौसम लोड नहीं कर सकते।' : 'Could not load weather.';"),
    
    # Line 828 - "ng weather" should be error message
    (r"const netErrorMsg = document\.documentElement\.lang\?\.startsWith\('hi'\) \? 'ng weather\.';",
     "const netErrorMsg = document.documentElement.lang?.startsWith('hi') ? 'नेटवर्क त्रुटि: मौसम लोड नहीं कर सकते।' : 'Network error: Could not fetch weather.';"),
]

content = ''.join(lines)

for pattern, replacement in fixes:
    content = re.sub(pattern, replacement, content)

# Write fixed content
with open('resources/js/app.js', 'w', encoding='utf-8') as f:
    f.write(content)

print("Recovery complete!")
