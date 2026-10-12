import datetime as dt,re,shutil
from pathlib import Path
base=Path(__file__).parent
stamp=dt.datetime.now().strftime('%Y%m%d_%H%M%S')
for name in ['update_projects.py','aoe_extract.py']:
    p=base/name;shutil.copy2(p,Path(str(p)+'.bak_'+stamp+'_path-and-markup'))
p=base/'update_projects.py';src=p.read_text(encoding='utf-8')
src=re.sub(r"\.replace\('(?:\\)+','/'\)",".replace(chr(92),'/')",src)
needle="    for u,v,data in old_metadata_edges:\n"
extra=r"""    for source,source_id in file_nodes.items():
        p=root/source
        if p.suffix not in {'.html','.css','.scss'}:continue
        text=p.read_text(encoding='utf-8',errors='replace')
        for match in re.finditer(r'''(?:src|href)\s*=\s*["']([^"']+)["']|@import\s+["']([^"']+)["']''',text):
            token=(match[1] or match[2]).split('?',1)[0].split('#',1)[0]
            if not token or '://' in token:continue
            target_path=(root/token.lstrip('/') if token.startswith('/') else p.parent/token).resolve()
            try:target=target_path.relative_to(root).as_posix()
            except ValueError:continue
            if target not in file_nodes:continue
            line=text.count('\n',0,match.start())+1
            graph.add_edge(source_id,file_nodes[target],relation='references_source_file',confidence='EXTRACTED',confidence_score=1.0,weight=1.0,source_file=source,source_location=f'L{line}',_origin='local_metadata',evidence='Literal current markup/import reference; not runtime proof')
"""
assert needle in src
src=src.replace(needle,extra+needle)
compile(src,str(p),'exec');p.write_text(src,encoding='utf-8')
p=base/'aoe_extract.py';src=p.read_text(encoding='utf-8')
src=src.replace("file_type='aoe_per_file',representation='local_per_structure'","file_type='aoe_ai_marker' if rel.endswith('.ai') else 'aoe_per_file',representation='file_metadata_only' if rel.endswith('.ai') else 'local_per_structure'")
compile(src,str(p),'exec');p.write_text(src,encoding='utf-8')
print('Path normalization and literal markup references checked.')
