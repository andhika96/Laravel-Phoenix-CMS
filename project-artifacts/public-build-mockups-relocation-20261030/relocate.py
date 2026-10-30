"""Archive unused default build and move mockup fixtures, keeping all bytes recoverable."""
import collections,hashlib,json,shutil,subprocess,sys,re
from datetime import datetime,timezone,timedelta
from pathlib import Path

OUT=Path(__file__).resolve().parent
ROOT=OUT.parents[1]
FOLDERS={
 'public/build':'project-artifacts/builds/legacy-public-build-20261030',
 'public/mockups':'project-artifacts/mockups/legacy-public-mockups-20261030',
}
def sha(data):return hashlib.sha256(data).hexdigest()
def local(name):
 p=(ROOT/name).resolve()
 assert p.is_relative_to(ROOT.resolve()),name
 return p
def replacement(data):return data.replace(b'public/mockups',b'project-artifacts/mockups/legacy-public-mockups-20261030')
def refs():
 return [p for p in (ROOT/'tests').rglob('*') if p.is_file() and p.suffix in ['.mjs','.js','.php'] and '.bak' not in p.name and replacement(p.read_bytes())!=p.read_bytes()]
def prepare():
 assert not (OUT/'preflight.json').exists()
 files=[]
 for source,target in FOLDERS.items():
  assert local(source).is_dir() and not local(target).exists()
  assert not local(source).is_symlink()
  for p in local(source).rglob('*'):
   if p.is_file():
    files.append({'old':p.relative_to(ROOT).as_posix(),'new':(local(target)/p.relative_to(local(source))).relative_to(ROOT).as_posix(),'sha256':sha(p.read_bytes())})
 consumers=[{'path':p.relative_to(ROOT).as_posix(),'before_sha256':sha(p.read_bytes()),'after_sha256':sha(replacement(p.read_bytes()))} for p in refs()]
 assert len(files)==18 and len(consumers)==11,(len(files),len(consumers))
 state={'folders':FOLDERS,'files':files,'consumers':consumers,
        'protected':{p.relative_to(ROOT).as_posix():sha(p.read_bytes()) for p in (ROOT/'public/assets/css').rglob('*.css')}}
 (OUT/'preflight.json').write_text(json.dumps(state,indent=2),encoding='utf-8')
 (OUT/'git-status-before.txt').write_text(subprocess.run(['git','status','--short'],cwd=ROOT,capture_output=True,text=True,check=True).stdout,encoding='utf-8')
 print(json.dumps({'files':len(files),'test_consumers':len(consumers),'folder_destinations':FOLDERS},indent=2))
def tests(label):
 assert label in ['baseline','after']
 state=json.loads((OUT/'preflight.json').read_text())
 log=OUT/(label+'-tests.tap');assert not log.exists()
 p=subprocess.run(['node','--test','--test-reporter=tap',*[r['path'] for r in state['consumers'] if r['path'].endswith('.test.mjs')]],cwd=ROOT,capture_output=True,text=True,encoding='utf-8',errors='replace')
 log.write_text(p.stdout+'\n'+p.stderr,encoding='utf-8')
 result={'exit_code':p.returncode,'stats':{k:int(v) for k,v in re.findall(r'^# (tests|pass|fail|cancelled|skipped) (\d+)',p.stdout,re.M)},'failures':re.findall(r'^\s*not ok \d+ - (.*)',p.stdout,re.M)}
 (OUT/(label+'-tests.json')).write_text(json.dumps(result,indent=2),encoding='utf-8')
 print(json.dumps(result,indent=2))
def execute():
 state=json.loads((OUT/'preflight.json').read_text())
 assert json.loads((OUT/'database-check.json').read_text())['hits']==[]
 assert (OUT/'baseline-tests.json').is_file()
 for r in state['files']:assert sha(local(r['old']).read_bytes())==r['sha256'] and not local(r['new']).exists()
 for r in state['consumers']:assert sha(local(r['path']).read_bytes())==r['before_sha256']
 for source,target in FOLDERS.items():assert local(source).is_relative_to(ROOT.resolve()) and local(target).is_relative_to(ROOT.resolve()) and not local(target).exists()
 backup=OUT/('backups_'+datetime.now(timezone(timedelta(hours=7))).strftime('%Y%m%d_%H%M%S'))
 names={r['old'] for r in state['files']}|{r['path'] for r in state['consumers']}
 for name in names:
  p=local(name);saved=backup/name;saved.parent.mkdir(parents=True,exist_ok=True);shutil.copy2(p,saved)
  assert sha(p.read_bytes())==sha(saved.read_bytes())
 state['backup']=backup.relative_to(ROOT).as_posix()
 (OUT/'recovery-manifest.json').write_text(json.dumps(state,indent=2),encoding='utf-8')
 for r in state['consumers']:
  p=local(r['path']);data=replacement(p.read_bytes());assert sha(data)==r['after_sha256'];p.write_bytes(data)
 for source,target in FOLDERS.items():
  local(target).parent.mkdir(parents=True,exist_ok=True)
  shutil.move(str(local(source)),str(local(target)))
 verify()
 print('Backup:',state['backup'])
def verify():
 state=json.loads((OUT/'recovery-manifest.json').read_text())
 for r in state['files']:assert not local(r['old']).exists() and sha(local(r['new']).read_bytes())==r['sha256']
 for r in state['consumers']:assert sha(local(r['path']).read_bytes())==r['after_sha256']
 for name,h in state['protected'].items():assert sha(local(name).read_bytes())==h
 assert not refs()
 for source in FOLDERS:assert not local(source).exists()
 if (OUT/'after-tests.json').exists():
  before=json.loads((OUT/'baseline-tests.json').read_text());after=json.loads((OUT/'after-tests.json').read_text())
  assert before['stats']==after['stats'] and collections.Counter(before['failures'])==collections.Counter(after['failures'])
 print('VERIFIED: 18 moved files byte-identical; 11 test consumers updated; 32 active CSS unchanged; public folders removed.')
mode=sys.argv[1]
if mode=='prepare':prepare()
elif mode in ['baseline','after']:tests(mode)
elif mode=='execute':execute()
elif mode=='verify':verify()
else:raise ValueError(mode)
