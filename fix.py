import codecs
with codecs.open('resources/js/app.js', 'r', 'utf-8', errors='ignore') as f:
    text = f.read().lstrip('\ufeff')

lines = text.split('\n')
out = []
for i, line in enumerate(lines):
    non_ascii = [c for c in line if ord(c) > 127]
    if non_ascii:
        out.append(f'Line {i+1}: {line.strip()}')

with codecs.open('corrupted.txt', 'w', 'utf-8') as f:
    f.write('\n'.join(out))
