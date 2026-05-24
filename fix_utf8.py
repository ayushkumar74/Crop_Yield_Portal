import codecs

with codecs.open('resources/js/app.js', 'r', 'utf-8', errors='ignore') as f:
    text = f.read()

# Replace corrupted headers
text = text.replace('?"', '-')
text = text.replace('"?', '-')

# Emojis and Symbols
text = text.replace('\'~?,?\'', '\'☀️\'')
text = text.replace('\'dYOT\'', '\'🌙\'')
text = text.replace('\'o.\'', '\'✅\'')
text = text.replace('\'?O\'', '\'❌\'')
text = text.replace('\',1,?\'', '\'⚠️\'')
text = text.replace('?O', '❌')
text = text.replace('o"', '✅')
text = text.replace('acCEPTED', 'ACCEPTED')
text = text.replace('?3', '⏳')
text = text.replace('dYO', '🌱')
text = text.replace('dY"', '📡')
text = text.replace('dY"', '📡')

# Hindi text restorations
text = text.replace('   r  o? ? ? ,  , Y?     _      _ _  , Y?    ,?  _ "/GPS  ,  ?  r    ؅ , ', 'कम जीपीएस सटीकता। कृपया सटीक स्थान/GPS सक्षम करें।')
text = text.replace(' ,?  _ "   1? , s        1?      _ _  r^ "? _? .   ,   ,  - ؅ , ', 'स्थान प्राप्त करने में विफल। कृपया मैन्युअल रूप से दर्ज करें।')
text = text.replace(' -< o   ?  o _   1?  1^...', 'मौसम प्राप्त कर रहे हैं...')
text = text.replace(' ,?  _ "    _     _   - _ _ _  o _   1 _  1^...', 'स्थान का पता लगा रहे हैं...')
text = text.replace('  r؅   ,?  _ "    _  rO , r  ?  _ ?      ؅ ,', 'मेरे स्थान का मौसम प्राप्त करें')
text = text.replace(' ,?  _ "   1? , s  . ,? 慝?   _       ݅?  - ^      _ _  r^ "? _? .   ,   ,  ݅ ? o    ؅ , ', 'स्थान तक पहुँचने से मना किया गया। कृपया शहर मैन्युअल रूप से दर्ज करें।')
text = text.replace(' ,?  _ "   1? , s        1?      _ _  r^ "? _? .   ,   ,  - ؅ , ', 'स्थान तक पहुँचना विफल रहा। कृपया मौसम मैन्युअल रूप से भरें।')
text = text.replace(' ؅ "     ,?      _< ,       ?   < ^  .  ? _       %  _?  ?     ,   " 1? ,  r  ? ', 'इन स्थितियों के लिए कोई अत्यधिक उपयुक्त फसल नहीं मिली।')

# We can see line 376 has dY"  r؅   ,?  _ "    _  rO , r  ?  _ ?      ؅ ,
# Which translates to 📡 मेरे स्थान का मौसम प्राप्त करें
# This was handled by the dY" -> 📡 and the long string replacement.

with codecs.open('resources/js/app.js', 'w', 'utf-8') as f:
    f.write(text)
