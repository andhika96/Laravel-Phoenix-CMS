"""Scoped relocation with byte-preserving references, backups, and recovery manifest."""
import collections
import hashlib
import json
from pathlib import Path
import re
import shutil
import subprocess
import sys
from datetime import datetime, timezone, timedelta

OUT = Path(__file__).resolve().parent
ROOT = OUT.parents[1]
CSS = ROOT/'public/assets/css'
DEST = {
    'phoenix-cms.css':'shared/phoenix-cms.css',
    'extending-css-bootstrap-5.css':'shared/extending-css-bootstrap-5.css',
    'theme-responsive-typography.css':'shared/theme-responsive-typography.css',
    'cms-bootstrap-theme.css':'cms/cms-bootstrap-theme.css',
    'awesome-admin-header-navigation.css':'header-navigation/awesome-admin-header-navigation.css',
    'awesome-admin-header-navigation-fullscreen.css':'header-navigation/awesome-admin-header-navigation-fullscreen.css',
    'frontend-menu-dropdown.css':'frontend/frontend-menu-dropdown.css',
    'workflow-editor.css':'workflow/workflow-editor.css',
    'aruna-v4.css':'setup/aruna-v4.css',
    'pagebuilder.css':'pagebuilder/legacy/pagebuilder.css',
    'pagebuilder_elementor.css':'pagebuilder/elementor/pagebuilder_elementor.css',
    'frontend_elementor.css':'pagebuilder/elementor/frontend_elementor.css',
    'pagebuilder_elementor_v23.css':'pagebuilder/elementor-v23/pagebuilder_elementor_v23.css',
    'frontend_elementor_v23.css':'pagebuilder/elementor-v23/frontend_elementor_v23.css',
    'pagebuilder_elementor_v24.css':'pagebuilder/elementor-v24/pagebuilder_elementor_v24.css',
    'frontend_elementor_v24.css':'pagebuilder/elementor-v24/frontend_elementor_v24.css',
}
ROOTS = ['app','resources','routes','config','tests','scripts','public/js','public/assets/js','public/assets/css']

def digest(data): return hashlib.sha256(data).hexdigest()
def local(relative):
    path=(ROOT/relative).resolve()
    if not path.is_relative_to(ROOT.resolve()): raise ValueError('Outside project: '+relative)
    return path
def variants():
    result=[]
    for old,new in DEST.items():
        old='assets/css/'+old;new='assets/css/'+new
        for a,b in [(old,new),(old.replace('/','\\/').replace('.css','\\.css'),new.replace('/','\\/').replace('.css','\\.css')),
                    (old.replace('/','\\'),new.replace('/','\\')),(old.replace('/','\\\\'),new.replace('/','\\\\'))]:
            result.append((a.encode(),b.encode()))
    return result
def updated(data):
    for a,b in variants(): data=data.replace(a,b)
    return data
def sources():
    cmd=['rg','--files','--hidden',*ROOTS,'-g','!*.bak*','-g','!**/backup*/**','-g','!**/archived/**','-g','!**/cache/**','-g','!**/vendor/**']
    proc=subprocess.run(cmd,cwd=ROOT,capture_output=True,text=True,check=True)
    for name in proc.stdout.splitlines():
        p=local(name)
        if p.suffix.lower() in {'.php','.js','.mjs','.ts','.vue','.json','.css','.scss','.html'}: yield p
def prepare():
    if (OUT/'preflight.json').exists(): raise RuntimeError('Preflight already exists; inspect it before rerunning')
    assert {p.name for p in CSS.glob('*.css')}==set(DEST)
    moves=[]
    for old,new in DEST.items():
        p=CSS/old
        assert not (CSS/new).exists()
        text=p.read_text(encoding='utf-8-sig')
        urls=re.findall(r'url\(\s*[\'"]?([^\)\'"\s]+)',text)
        assert not [u for u in urls if not re.match(r'(?:[a-z]+:|/|#)',u,re.I)], 'Relative CSS URL needs explicit rebasing: '+old
        moves.append({'old':p.relative_to(ROOT).as_posix(),'new':(CSS/new).relative_to(ROOT).as_posix(),'kind':'stylesheet','sha256':digest(p.read_bytes())})
    for p in CSS.iterdir():
        if not p.is_file() or p.name in DEST: continue
        if '.bak' in p.name:
            target=OUT/'historical-css-backups'/p.name;kind='historical_backup'
        elif p.name in ['base_color.txt','base_color_from_heroui_theme.txt']:
            target=ROOT/'project-artifacts/references/css-color-notes-20261030'/p.name;kind='color_reference'
        else: raise RuntimeError('Unexpected loose file: '+str(p))
        assert not target.exists()
        moves.append({'old':p.relative_to(ROOT).as_posix(),'new':target.relative_to(ROOT).as_posix(),'kind':kind,'sha256':digest(p.read_bytes())})
    refs=[]
    for p in sources():
        raw=p.read_bytes();new=updated(raw)
        if new!=raw:
            refs.append({'path':p.relative_to(ROOT).as_posix(),'before_sha256':digest(raw),'after_sha256':digest(new)})
    # CSS payloads must remain exact; only consumer URL strings should change.
    assert not any(r['path'].endswith('.css') for r in refs)
    direct_node={r['path'] for r in refs if r['path'].endswith('.test.mjs')}
    helpers={r['path'] for r in refs if r['path'].startswith('tests/helpers/')}
    for p in (ROOT/'tests').glob('*.test.mjs'):
        data=p.read_text(encoding='utf-8-sig')
        if any(Path(h).name in data for h in helpers): direct_node.add(p.relative_to(ROOT).as_posix())
    state={'root':str(ROOT),'moves':moves,'references':refs,'node_tests':sorted(direct_node),
           'git_status_before':subprocess.run(['git','status','--short'],cwd=ROOT,capture_output=True,text=True,check=True).stdout,
           'protected':{p:digest(local(p).read_bytes()) for p in ['public/assets/css/article/manage-article-2026.css','public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js','resources/views/manage_article/manage_article.blade.php']}}
    (OUT/'preflight.json').write_text(json.dumps(state,indent=2),encoding='utf-8')
    (OUT/'pre-existing-working-tree.patch').write_bytes(subprocess.run(['git','diff','--binary'],cwd=ROOT,capture_output=True,check=True).stdout)
    print(json.dumps({'moves':dict(collections.Counter(m['kind'] for m in moves)),'reference_files':len(refs),'node_test_files':len(direct_node)},indent=2))
def run_tests(label):
    assert label in ['baseline','after']
    state=json.loads((OUT/'preflight.json').read_text())
    log=OUT/(label+'-node-tests.tap')
    if log.exists(): raise RuntimeError('Test evidence already exists')
    proc=subprocess.run(['node','--test','--test-reporter=tap',*state['node_tests']],cwd=ROOT,capture_output=True,text=True,encoding='utf-8',errors='replace')
    log.write_text(proc.stdout+'\n'+proc.stderr,encoding='utf-8')
    stats={key:int(value) for key,value in re.findall(r'^# (tests|pass|fail|cancelled|skipped) (\d+)',proc.stdout,re.M)}
    failures=re.findall(r'^\s*not ok \d+ - (.*)',proc.stdout,re.M)
    result={'exit_code':proc.returncode,'stats':stats,'failures':failures}
    (OUT/(label+'-node-tests.json')).write_text(json.dumps(result,indent=2),encoding='utf-8')
    print(json.dumps(result,indent=2))
def execute():
    state=json.loads((OUT/'preflight.json').read_text())
    assert (OUT/'baseline-node-tests.json').is_file()
    db=json.loads((OUT/'database-path-references.json').read_text())
    assert db['hits']==[], 'Persisted URL references need an explicit migration decision'
    for m in state['moves']: assert digest(local(m['old']).read_bytes())==m['sha256'] and not local(m['new']).exists()
    for r in state['references']: assert digest(local(r['path']).read_bytes())==r['before_sha256']
    stamp=datetime.now(timezone(timedelta(hours=7))).strftime('%Y%m%d_%H%M%S')
    backup=OUT/('backups_'+stamp)
    files={m['old'] for m in state['moves']}|{r['path'] for r in state['references']}|set(state['protected'])
    for name in sorted(files):
        p=local(name);dst=backup/name;dst.parent.mkdir(parents=True,exist_ok=True);shutil.copy2(p,dst)
        assert digest(p.read_bytes())==digest(dst.read_bytes()), name
    state['backup']=backup.relative_to(ROOT).as_posix()
    (OUT/'recovery-manifest.json').write_text(json.dumps(state,indent=2),encoding='utf-8')
    for r in state['references']:
        p=local(r['path']);new=updated(p.read_bytes());assert digest(new)==r['after_sha256'];p.write_bytes(new)
    for m in state['moves']:
        p=local(m['old']);dst=local(m['new']);dst.parent.mkdir(parents=True,exist_ok=True);shutil.move(str(p),str(dst))
        assert not p.exists() and digest(dst.read_bytes())==m['sha256']
    assert not [p for p in CSS.iterdir() if p.is_file()]
    for p,h in state['protected'].items(): assert digest(local(p).read_bytes())==h
    print(json.dumps({'backup':state['backup'],'backed_up_files':len(files),'relocated_files':len(state['moves']),'updated_reference_files':len(state['references'])},indent=2))
def verify():
    state=json.loads((OUT/'recovery-manifest.json').read_text())
    for m in state['moves']: assert not local(m['old']).exists() and digest(local(m['new']).read_bytes())==m['sha256']
    for r in state['references']: assert digest(local(r['path']).read_bytes())==r['after_sha256']
    for p,h in state['protected'].items(): assert digest(local(p).read_bytes())==h
    assert not [p for p in CSS.iterdir() if p.is_file()]
    leftovers=[]
    for p in sources():
        if updated(p.read_bytes())!=p.read_bytes(): leftovers.append(p.relative_to(ROOT).as_posix())
    assert not leftovers,leftovers
    before=json.loads((OUT/'baseline-node-tests.json').read_text());after=json.loads((OUT/'after-node-tests.json').read_text())
    assert collections.Counter(before['failures'])==collections.Counter(after['failures']), 'Regression changed test failures'
    assert before['stats']==after['stats'], 'Test totals changed'
    print('VERIFIED: all CSS payload hashes unchanged, consumers updated, root clean, protected user changes unchanged, test results match baseline.')

assert len(DEST)==16 and len(set(DEST.values()))==16
assert updated(b"assets/css/phoenix-cms.css") == b"assets/css/shared/phoenix-cms.css"
assert local('public/assets/css').is_dir()
mode=sys.argv[1]
if mode=='prepare':prepare()
elif mode in ['baseline','after']:run_tests(mode)
elif mode=='execute':execute()
elif mode=='verify':verify()
else:raise ValueError(mode)
