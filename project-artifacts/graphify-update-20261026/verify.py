"""Independent QC of every published graph and preservation of application state."""
import collections
import datetime as dt
import hashlib
import importlib.util
import json
import os
import re
import shutil
import subprocess
from pathlib import Path
from graphify.build import build_merge
from graphify.diagnostics import diagnose_extraction

BASE=Path(__file__).parent
def read(path):return json.loads(path.read_text(encoding='utf-8-sig'))
def sha(path):return hashlib.sha256(path.read_bytes()).hexdigest()
def app_status(text):
    return [line for line in text.splitlines() if 'graphify-out/' not in line and 'project-artifacts/' not in line]
def check(row):
    root=Path(row['root']);art=Path(row['artifacts']);out=root/'graphify-out'
    plan=read(art/'plan.json');data=read(out/'graph.json');manifest=read(out/'manifest.json')
    code={Path(p).relative_to(root).as_posix() for p in plan['code_files']}
    metadata={Path(p).relative_to(root).as_posix() for p in plan['metadata_files']}
    ids={n['id'] for n in data['nodes']};edges=data['links']
    assert len(ids)==len(data['nodes'])>0
    assert all(e['source'] in ids and e['target'] in ids for e in edges)
    sources={n['source_file'] for n in data['nodes'] if n.get('source_file')}
    assert code|metadata <= sources,(row['label'],(code|metadata)-sources)
    assert not set(plan['prune']) & sources
    assert all((root/f).is_file() for f in sources)
    spec=importlib.util.spec_from_file_location('prepared_scope',BASE.parent/'graphify-all-projects-20261020'/'prepare_projects.py')
    scope=importlib.util.module_from_spec(spec);spec.loader.exec_module(scope)
    assert all(scope.safe(f) for f in sources),(row['label'],'excluded source survived')
    assert all(not e.get('source_file') or scope.safe(e['source_file']) for e in edges),(row['label'],'excluded edge provenance')
    assert all(hashlib.md5((root/f).read_bytes()).hexdigest()==manifest[f]['ast_hash'] for f in code)
    assert all(sha(root/f)==value for f,value in read(art/'source-hashes-before.json').items())
    assert all(sha(root/f)==value for f,value in read(out/'metadata-manifest.json').items())
    backup=read(art/'backup-manifest.json')
    assert all(sha(Path(v['backup']))==v['sha256'] for v in backup.values())
    if 'graph.json' in backup:
        old=read(Path(backup['graph.json']['backup']))
        changed={Path(p).relative_to(root).as_posix() for p in plan['changed']+plan['changed_metadata']}
        if plan.get('force_local_per'):changed|=code
        unaffected={n['id'] for n in old['nodes'] if n.get('source_file') and n['source_file'] not in changed|set(plan['prune'])}
        assert unaffected<=ids,(row['label'],'unchanged nodes lost')
    fresh=build_merge([],graph_path=str(out/'graph.json'),root=str(root),directed=data.get('directed',True),dedup=False)
    assert fresh.number_of_nodes()==len(ids) and fresh.number_of_edges()==len(edges)
    before=read(art/'before-git.json')
    if before:
        args=['git','-C',str(root)];env=dict(os.environ,GIT_OPTIONAL_LOCKS='0')
        assert subprocess.check_output(args+['rev-parse','HEAD'],text=True,env=env).strip()==before['head']
        assert sha_bytes(subprocess.check_output(args+['diff','--cached','--binary'],env=env))==before['staged_sha256']
        current=subprocess.check_output(args+['status','--porcelain=v1'],text=True,env=env)
        assert app_status(current)==app_status(before['status']),(row['label'],'application Git status changed')
    html=(out/'graph.html').read_text(encoding='utf-8')
    assert not re.search(r'<script[^>]+src=["\x27]https?://',html)
    assert (out/'vis-network.min.js').is_file()
    health=diagnose_extraction({'nodes':data['nodes'],'edges':edges},directed=data.get('directed',True),root=str(root))
    refs=read(art/'history-coverage-input.json')
    eligible={x['file'] for x in refs if x['file'] in code|metadata}
    assert eligible<=sources
    meta_only={f for f in code if all(n.get('representation')=='file_metadata_only' for n in data['nodes'] if n.get('source_file')==f)}
    result={'ok':True,'label':row['label'],'root':str(root),'nodes':len(ids),'edges':len(edges),'represented_files':len(sources),'code_files':len(code),'metadata_files':len(metadata),'historical_active_file_references_verified':len(eligible),'code_files_metadata_only':sorted(meta_only),'backup_files_verified':len(backup),'source_preserved':True,'head_and_staged_content_preserved':True,'unchanged_nodes_preserved':True,'offline_script':True,'duplicate_ids':0,'dangling_edges':0,'hash_mismatch':0,'confidence':dict(collections.Counter(e.get('confidence','unspecified') for e in edges)),'graph_sha256':sha(out/'graph.json'),'health':health,'runtime_retested':False}
    if root.name=='New AI Script AoE II DE':
        canonical=Path('C:/Users/aruna/Documents/Codex/2026-06-13/chatgpt-saya-ingin-kamu-membuatkan-saya/outputs/New AI Script AoE II DE')
        matches={f:sha(root/f)==sha(canonical/f) for f in code if (canonical/f).is_file()}
        assert len(matches)==len(code) and all(matches.values())
        extraction=read(art/'extraction.json')
        assert {n['id'] for n in extraction['nodes']}<=ids,'PER namespace identity collapsed'
        result['canonical_source_files_matched']=len(matches)
        result['per_namespace_identities_preserved']=True
    if (art/'qc.json').exists():
        shutil.copy2(art/'qc.json',art/('qc.json.bak_'+dt.datetime.now().strftime('%Y%m%d_%H%M%S')+'_final-verification'))
    (art/'qc.json').write_text(json.dumps(result,ensure_ascii=False,indent=2),encoding='utf-8')
    print(json.dumps({k:v for k,v in result.items() if k not in ('health','code_files_metadata_only')},ensure_ascii=False),flush=True)
    return result
def sha_bytes(data):return hashlib.sha256(data).hexdigest()
def main():
    inventory=read(BASE/'inventory.json');results=[];errors=[]
    for row in inventory:
        if row['status']!='planned':continue
        try:results.append(check(row))
        except Exception as error:
            errors.append({'label':row['label'],'error':str(error),'type':type(error).__name__})
            print(json.dumps(errors[-1],ensure_ascii=False),flush=True)
    summary={'results':results,'errors':errors,'skipped':[r for r in inventory if r['status']!='planned']}
    if (BASE/'final-qc.json').exists():
        shutil.copy2(BASE/'final-qc.json',BASE/('final-qc.json.bak_'+dt.datetime.now().strftime('%Y%m%d_%H%M%S')+'_recheck'))
    (BASE/'final-qc.json').write_text(json.dumps(summary,ensure_ascii=False,indent=2),encoding='utf-8')
    assert not errors,errors
if __name__=='__main__':main()
