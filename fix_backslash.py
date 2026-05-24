import codecs

with codecs.open('resources/js/app.js', 'r', 'utf-8', errors='ignore') as f:
    text = f.read()

text = text.replace('showToast(warningMsg, \\'warning\\');', 'showToast(warningMsg, \'warning\');')

with codecs.open('resources/js/app.js', 'w', 'utf-8') as f:
    f.write(text)
