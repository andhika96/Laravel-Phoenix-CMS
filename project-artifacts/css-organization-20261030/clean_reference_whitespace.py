"""Remove pre-existing trailing blanks only on stylesheet URL lines just updated."""
from datetime import datetime,timezone,timedelta
import hashlib,json,shutil
from pathlib import Path

OUT=Path(__file__).resolve().parent
ROOT=OUT.parents[1]
manifest=OUT/'recovery-manifest.json'
state=json.loads(manifest.read_text())
urls=[m['new'].removeprefix('public/').encode() for m in state['moves'] if m['kind']=='stylesheet']
changes=[]
for ref in state['references']:
    path=ROOT/ref['path']
    original=path.read_bytes()
    assert hashlib.sha256(original).hexdigest()==ref['after_sha256']
    lines=[]
    for line in original.splitlines(keepends=True):
        if any(url in line for url in urls):
            ending=b'\r\n' if line.endswith(b'\r\n') else b'\n' if line.endswith(b'\n') else b''
            line=line.removesuffix(ending) if ending else line
            line=line.rstrip(b' \t')+ending
        lines.append(line)
    revised=b''.join(lines)
    if revised!=original: changes.append((ref,path,revised))
backup=OUT/('backups_'+datetime.now(timezone(timedelta(hours=7))).strftime('%Y%m%d_%H%M%S')+'_reference-whitespace')
assert not backup.exists()
backup.mkdir()
shutil.copy2(manifest,backup/manifest.name)
for ref,path,revised in changes:
    saved=backup/ref['path'];saved.parent.mkdir(parents=True,exist_ok=True);shutil.copy2(path,saved)
    assert saved.read_bytes()==path.read_bytes()
    path.write_bytes(revised)
    ref['after_sha256']=hashlib.sha256(revised).hexdigest()
state['reference_whitespace_backup']=backup.relative_to(ROOT).as_posix()
state['reference_whitespace_files']=[ref['path'] for ref,path,data in changes]
manifest.write_text(json.dumps(state,indent=2),encoding='utf-8')
(OUT/'reference-whitespace.json').write_text(json.dumps({'files':len(changes),'backup':state['reference_whitespace_backup']},indent=2),encoding='utf-8')
print('Trailing whitespace cleaned only on updated URL lines:',len(changes))
