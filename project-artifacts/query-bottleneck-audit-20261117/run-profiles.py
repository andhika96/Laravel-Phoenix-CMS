"""Run bounded, isolated read-only probes; record raw evidence without response data."""
import json
import subprocess
from pathlib import Path

art = Path(__file__).resolve().parent
root = art.parents[1]
routes = json.loads((art/'route-inventory.json').read_text())
skip_parts = ('logout','setvertical','removevertical','image-rendition','/testing','testurl','/role/test',
              'benchmark','/checkdata','/checkdataforupdate','/getdata','/getlanguage','getisvertical',
              'filefonts','listdata/fonts','/config/logo','/preview-data','/listdata/routes')
skip_uris = {'notification','setup','setup/success','auth/recoveryaccount','arunika','arunika/v1','arunika/v1/gemini'}
cases = []
for row in routes:
    uri = row['uri']
    if 'GET' not in row['methods'] or '{' in uri or not row.get('class_exists'):
        continue
    if not row['action'].startswith('App\\Http\\Controllers\\Web\\'):
        continue
    if uri in skip_uris or uri.startswith('api/') or any(s in uri for s in skip_parts):
        continue
    cases.append({'uri': '/'+uri.lstrip('/'), 'mode': 'guest' if uri in {'/','home','home/listdata','article','article/listdata','auth/login','auth/signup','auth/forgotpassword'} else 'admin'})
assert any(c['uri']=='/dashboard' for c in cases)
assert not any(c['uri']=='/notification' for c in cases)
(art/'profile-cases.json').write_text(json.dumps(cases,indent=2),encoding='utf-8')
results = []
for case in cases:
    try:
        run = subprocess.run(['php',str(art/'profile-request.php'),case['uri'],case['mode']],cwd=root,capture_output=True,text=True,encoding='utf-8',errors='replace',timeout=30)
        result = json.loads(run.stdout)
    except Exception as error:
        result = {**case,'probe_error':str(error),'stdout_prefix':run.stdout[:150] if 'run' in locals() else ''}
    results.append(result)
    with (art/'request-profiles.jsonl').open('a',encoding='utf-8') as stream:
        stream.write(json.dumps(result,ensure_ascii=False)+'\n')
    print(json.dumps({k:v for k,v in result.items() if k not in {'queries','by_table'}},ensure_ascii=False),flush=True)
(art/'profile-summary.json').write_text(json.dumps([{k:v for k,v in row.items() if k!='queries'} for row in results],indent=2),encoding='utf-8')
