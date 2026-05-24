import codecs
import re

with codecs.open('resources/js/app.js', 'r', 'utf-8', errors='ignore') as f:
    text = f.read()

text = re.sub(r'showToast\(warningMsg,\s*\'warning\'\);\s+if\s*\(metaLat\s*&&\s*metaLon\s*&&\s*metaLat\s*!==\s*\'\'\s*&&\s*metaLon\s*!==\s*\'\'\)\s*\{\s*fetchWeatherByCoords\(parseFloat\(metaLat\),\s*parseFloat\(metaLon\),\s*true\);\s*\}\s*return;', r'showToast(warningMsg, \'warning\');\n                return;', text)

text = re.sub(r'showToast\(warningMsg,\s*\'warning\'\);\s+if\s*\(metaLat\s*&&\s*metaLon\)\s*\{\s*fetchWeatherByCoords\(parseFloat\(metaLat\),\s*parseFloat\(metaLon\),\s*true\);\s*\}\s*return;', r'showToast(warningMsg, \'warning\');\n                    return;', text)

with codecs.open('resources/js/app.js', 'w', 'utf-8') as f:
    f.write(text)
