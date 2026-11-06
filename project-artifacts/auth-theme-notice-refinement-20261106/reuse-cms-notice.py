from pathlib import Path
import re

root=Path(__file__).resolve().parents[2]
count=0
for theme in ('default','card','split_left','split_right'):
    for view in (root/'resources/views/auth'/theme).glob('*.blade.php'):
        source=view.read_bytes().decode('utf-8')
        marker='<div class="ph-notice" v-cloak>'
        assert source.count(marker)==1, str(view)
        start=source.index(marker)
        depth=0
        end=None
        for token in re.finditer(r'<div\b[^>]*>|</div>',source[start:]):
            depth += -1 if token.group().startswith('</') else 1
            if depth==0:
                end=start+token.end()
                break
        assert end is not None
        updated=source[:start]+"@include('components.notice-toast')"+source[end:]
        assert updated.count('ref="formHTML"')==source.count('ref="formHTML"')==1
        assert updated.count('@csrf')==source.count('@csrf')
        assert updated.count('@include(\'components.notice-toast\')')==1
        view.write_bytes(updated.encode('utf-8'))
        count+=1
assert count==16
print(f'{count} legacy auth views now reuse the existing CMS notice markup.')
