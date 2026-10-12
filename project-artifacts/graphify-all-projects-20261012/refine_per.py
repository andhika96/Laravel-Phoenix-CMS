import datetime as dt,json,shutil
from pathlib import Path
base=Path(__file__).parent
stamp=dt.datetime.now().strftime('%Y%m%d_%H%M%S')
p=base/'aoe_extract.py';src=p.read_text(encoding='utf-8')
shutil.copy2(p,Path(str(p)+'.bak_'+stamp+'_external-operators'))
src=src.replace("nodes[ident]={'id':ident,'label':label,'_origin':'local_aoe_structure',**data}","nodes[ident]={'id':ident,'label':label,'source_file':'','source_location':'','_origin':'local_aoe_structure',**data}")
compile(src,str(p),'exec');p.write_text(src,encoding='utf-8')
p=base/'update_projects.py';src=p.read_text(encoding='utf-8')
shutil.copy2(p,Path(str(p)+'.bak_'+stamp+'_per-identities'))
needle="    source_hashes = json.loads"
src=src.replace(needle,"    if plan.get('force_local_per',False):changed=code\n"+needle,1)
src=src.replace("graph_path=str(out / 'graph.json') if (out / 'graph.json').exists() else None,","graph_path=str(out / 'graph.json') if (out / 'graph.json').exists() and not plan.get('force_local_per',False) else None,")
src=src.replace("prune_sources=plan['prune'] or None, root=str(root), directed=plan['directed'])","prune_sources=plan['prune'] or None, root=str(root), directed=plan['directed'], dedup=not any(p.suffix.lower() in {'.per','.ai'} for p in code))")
compile(src,str(p),'exec');p.write_text(src,encoding='utf-8')
p=Path('D:/New AI Script AoE II DE/project-artifacts/graphify-all-projects-20261012/plan.json')
shutil.copy2(p,Path(str(p)+'.bak_'+stamp+'_per-identity-refresh'))
plan=json.loads(p.read_text(encoding='utf-8'));plan['force_local_per']=True
plan['refresh_reason']='Local PER namespace identities must remain distinct; disable fuzzy merging. No source files changed.'
p.write_text(json.dumps(plan,ensure_ascii=False,indent=2),encoding='utf-8')
print('PER operator provenance and namespace identity checks refined.')
