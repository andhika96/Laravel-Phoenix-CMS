"""Reuse the existing verified updater, adapted to today's per-project plans."""
from pathlib import Path
base=Path(__file__).parent
old=Path('D:/Laragon/www/laravel-13-phoenix/project-artifacts/memory-reconstruction-20261004/update_graph.py')
src=old.read_text(encoding='utf-8')
src=src.replace('from graphify.detect import save_manifest','from graphify.detect import save_manifest\nfrom aoe_extract import extract_aoe')
src=src.replace("art = root / 'project-artifacts/graphify-history-sync-20261004'","art = root / 'project-artifacts/graphify-all-projects-20261012'")
src=src.replace('4 Oktober 2026','12 Oktober 2026')
old_extract="    extraction = extract(changed, cache_root=art, root=root, max_workers=2) if changed else {'nodes': [], 'edges': [], 'hyperedges': []}"
new_extract="""    native = [p for p in changed if p.suffix.lower() not in {'.per','.ai'}]
    extraction = extract(native, cache_root=art, root=root, max_workers=2) if native else {'nodes': [], 'edges': [], 'hyperedges': []}
    aoe = [p for p in changed if p.suffix.lower() in {'.per','.ai'}]
    if aoe:
        custom = extract_aoe(aoe, [p for p in code if p.suffix.lower() in {'.per','.ai'}], root)
        extraction['nodes'].extend(custom['nodes']); extraction['edges'].extend(custom['edges'])
    fresh_sources = {str(n.get('source_file','')).replace('\\\\\\\\','/') for n in extraction['nodes']}
    for p in changed:
        rel = p.relative_to(root).as_posix()
        if rel not in fresh_sources and str(p).replace('\\\\\\\\','/') not in fresh_sources:
            extraction['nodes'].append({'id':'local_source_file_'+hashlib.sha256(rel.encode()).hexdigest()[:24],'label':rel,'source_file':rel,'file_type':'file','representation':'file_metadata_only','_origin':'local_file_metadata','content_sha256':sha(p)})
    for value in plan.get('changed_metadata',[]):
        p=Path(value);rel=p.relative_to(root).as_posix()
        extraction['nodes'].append({'id':'local_metadata_'+re.sub(r'[^a-zA-Z0-9_]','_',rel),'label':rel,'source_file':rel,'source_location':'L1','file_type':'file','representation':'file_metadata_only','_origin':'local_metadata','content_sha256':sha(p),'bytes':p.stat().st_size})"""
assert old_extract in src
src=src.replace(old_extract,new_extract)
begin=src.index("    metadata = {f for f in plan['retained_non_ast_sources']")
end=src.index('    old_metadata_edges = ',begin)
src=src[:begin]+"""    metadata = {Path(p).relative_to(root).as_posix() for p in plan.get('metadata_files',[])}
"""+src[end:]
src=src.replace("    old_metadata_edges = [(u,v,dict(d)) for u,v,d in graph.edges(data=True) if d.get('_origin') == 'local_metadata']","    old_metadata_edges = []\n    graph.remove_edges_from([(u,v) for u,v,d in graph.edges(data=True) if d.get('_origin') == 'local_metadata'])")
src=src.replace("p.suffix not in {'.php', '.js', '.ts', '.tsx', '.vue'}","p.suffix not in {'.php', '.js', '.ts', '.tsx', '.vue', '.html', '.py', '.css', '.scss', '.cmd'}")
src=src.replace("    changed_rel = {p.relative_to(root).as_posix() for p in changed}","    changed_rel = {p.relative_to(root).as_posix() for p in changed} | {Path(p).relative_to(root).as_posix() for p in plan.get('changed_metadata',[])}")
src=src.replace("    source_hashes = {str(p.relative_to(root)): sha(p) for p in code}","    source_hashes = json.loads((art/'source-hashes-before.json').read_text(encoding='utf-8'))")
begin=src.index("    if (out/'graph.html').exists():")
end=src.index("    (stage/'README.md')",begin)
html="""    library=Path('D:/Laragon/www/laravel-13-cms-bum/graphify-out/vis-network.min.js')
    if not library.is_file():
        raise RuntimeError('Offline visualization library is unavailable; publication refused')
    shutil.copy2(library,stage/'vis-network.min.js')
    from graphify.exporters.html import to_html
    to_html(graph,communities,str(stage/'graph.html'),community_labels=labels,node_limit=5000)
    html=(stage/'graph.html').read_text(encoding='utf-8')
    html=html.replace('https://unpkg.com/vis-network@9.1.6/standalone/umd/vis-network.min.js','./vis-network.min.js')
    assert not re.search(r'<script[^>]+src=[\\\"\\\\\\']https?://',html)
    (stage/'graph.html').write_text(html,encoding='utf-8')
"""
src=src[:begin]+html+src[end:]
src=src.replace("'mode':'incremental-local-AST'","'mode':'incremental-local-AST' if old['nodes'] else 'first-local-structural-build'")
src=src.replace("'metadata_files':len(metadata),","'metadata_files':len(metadata),'changed_metadata_files':len(plan.get('changed_metadata',[])),")
compile(src,str(base/'update_projects.py'),'exec')
assert not (base/'update_projects.py').exists()
(base/'update_projects.py').write_text(src,encoding='utf-8')
print('Existing updater adapted and syntax checked.')
