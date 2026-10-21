"""Incremental local Graphify update with verified backups and no network/LLM."""
import collections
import datetime as dt
import hashlib
import json
import re
import shutil
import subprocess
import sys
from pathlib import Path
from graphify.extract import extract
from graphify.build import build_merge
from graphify.cluster import cluster, score_all
from graphify.analyze import god_nodes, surprising_connections, suggest_questions
from graphify.report import generate
from graphify.export import to_json
from graphify.detect import save_manifest
from aoe_extract import extract_aoe

def sha(path):
    return hashlib.sha256(path.read_bytes()).hexdigest()

def write(path, data):
    path.write_text(json.dumps(data, ensure_ascii=False, indent=2), encoding='utf-8')

def git_state(root):
    result = subprocess.run(['git', '-C', str(root), 'status', '--porcelain=v1', '--untracked-files=no'], capture_output=True, text=True)
    return result.stdout if result.returncode == 0 else None

def update(root):
    root = root.resolve()
    art = root / 'project-artifacts/graphify-all-projects-20261020'
    out = root / 'graphify-out'
    plan = json.loads((art / 'plan.json').read_text(encoding='utf-8'))
    code = [Path(p) for p in plan['code_files']]
    current_manifest_path = out / 'manifest.json'
    current_manifest = json.loads(current_manifest_path.read_text(encoding='utf-8')) if current_manifest_path.exists() else {}
    changed = [p for p in code if hashlib.md5(p.read_bytes()).hexdigest() != current_manifest.get(p.relative_to(root).as_posix(), {}).get('ast_hash')]
    if plan.get('force_local_per',False):changed=code
    source_hashes = json.loads((art/'source-hashes-before.json').read_text(encoding='utf-8'))
    before_git = git_state(root)
    stamp = dt.datetime.now(dt.timezone(dt.timedelta(hours=7))).strftime('%Y%m%d_%H%M%S')
    backup = art / f'backup_{stamp}/graphify-out'
    backup.mkdir(parents=True)
    # Only generated top-level outputs are rewritten. Source, old snapshots and cache are untouched.
    hashes = {}
    if out.exists():
        for p in out.iterdir():
            if p.is_file():
                target = backup / (p.name + f'.bak_{stamp}_cms-history-sync')
                shutil.copy2(p, target)
                assert sha(p) == sha(target), p
                hashes[p.name] = {'backup': str(target), 'sha256': sha(p)}
    write(art / 'backup-manifest.json', hashes)
    print(f'{root.name}: backup verified ({len(hashes)} files); extracting {len(changed)} changed code files', flush=True)
    native = [p for p in changed if p.suffix.lower() not in {'.per','.ai'}]
    extraction = extract(native, cache_root=art, root=root, max_workers=2, resolution_context_nodes=[n for n in json.loads((out/'graph.json').read_text(encoding='utf-8'))['nodes'] if n.get('source_file') not in {p.relative_to(root).as_posix() for p in changed}] if (out/'graph.json').exists() else None) if native else {'nodes': [], 'edges': [], 'hyperedges': []}
    aoe = [p for p in changed if p.suffix.lower() in {'.per','.ai'}]
    if aoe:
        custom = extract_aoe(aoe, [p for p in code if p.suffix.lower() in {'.per','.ai'}], root)
        extraction['nodes'].extend(custom['nodes']); extraction['edges'].extend(custom['edges'])
    fresh_sources = {str(n.get('source_file','')).replace(chr(92),'/') for n in extraction['nodes']}
    for p in changed:
        rel = p.relative_to(root).as_posix()
        if rel not in fresh_sources and str(p).replace(chr(92),'/') not in fresh_sources:
            extraction['nodes'].append({'id':'local_source_file_'+hashlib.sha256(rel.encode()).hexdigest()[:24],'label':rel,'source_file':rel,'file_type':'file','representation':'file_metadata_only','_origin':'local_file_metadata','content_sha256':sha(p)})
    for value in plan.get('changed_metadata',[]):
        p=Path(value);rel=p.relative_to(root).as_posix()
        extraction['nodes'].append({'id':'local_metadata_'+re.sub(r'[^a-zA-Z0-9_]','_',rel),'label':rel,'source_file':rel,'source_location':'L1','file_type':'file','representation':'file_metadata_only','_origin':'local_metadata','content_sha256':sha(p),'bytes':p.stat().st_size})
    write(art / 'extraction.json', extraction)
    graph = build_merge([extraction], graph_path=str(out / 'graph.json') if (out / 'graph.json').exists() and not plan.get('force_local_per',False) else None,
                        prune_sources=plan['prune'] or None, root=str(root), directed=plan['directed'], dedup=False)
    represented = {d.get('source_file') for _,d in graph.nodes(data=True)}
    no_ast_files = [p.relative_to(root).as_posix() for p in code if p.relative_to(root).as_posix() not in represented]
    for f in no_ast_files:
        node_id = 'local_source_file_' + hashlib.sha256(f.encode()).hexdigest()[:24]
        graph.add_node(node_id, label=Path(f).name, source_file=f, file_type='file',
                       representation='file_metadata_only', _origin='local_file_metadata',
                       extraction_note='Native extractor returned no entity; existence/hash only',
                       content_sha256=sha(root/f), bytes=(root/f).stat().st_size)
    # Metadata for active styles/config not parsed by native AST. Never mark metadata as runtime proof.
    metadata = {Path(p).relative_to(root).as_posix() for p in plan.get('metadata_files',[])}
    old_metadata_edges = []
    graph.remove_edges_from([(u,v) for u,v,d in graph.edges(data=True) if d.get('_origin') == 'local_metadata'])
    metadata_nodes = {}
    for f in sorted(metadata):
        matches = [n for n,d in graph.nodes(data=True) if d.get('source_file') == f and d.get('_origin') == 'local_metadata']
        node_id = matches[0] if matches else 'local_metadata_' + re.sub(r'[^a-zA-Z0-9_]', '_', f)
        metadata_nodes[f] = node_id
        graph.add_node(node_id, label=f, source_file=f, source_location='L1', file_type='stylesheet' if f.endswith('.css') else 'configuration',
                       _origin='local_metadata', representation='file_metadata_only', content_sha256=sha(root/f), bytes=(root/f).stat().st_size)
    file_nodes = {}
    for node_id,data in graph.nodes(data=True):
        f = data.get('source_file')
        if f and (data.get('label') in (f, Path(f).name) or data.get('_origin') == 'local_metadata'):
            file_nodes[f] = node_id
    for source,source_id in file_nodes.items():
        p = root / source
        if not p.is_file() or p.suffix not in {'.php', '.js', '.ts', '.tsx', '.vue', '.html', '.py', '.css', '.scss', '.cmd'}:
            continue
        text = p.read_text(encoding='utf-8', errors='replace')
        for target,target_id in metadata_nodes.items():
            token = target.removeprefix('public/') if target.startswith('public/') else target
            if token not in text:
                continue
            for line_num,line in enumerate(text.splitlines(), 1):
                if token in line:
                    graph.add_edge(source_id, target_id, relation='references_stylesheet' if target.endswith('.css') else 'references_configuration',
                                   confidence='EXTRACTED', confidence_score=1.0, weight=1.0, source_file=source, source_location=f'L{line_num}',
                                   _origin='local_metadata', evidence='literal reference in current source; file metadata only; not runtime verification')
                    break
    for source,source_id in file_nodes.items():
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
    for u,v,data in old_metadata_edges:
        if u in graph and v in graph and not graph.has_edge(u,v):
            graph.add_edge(u,v,**data)
    assert graph.number_of_nodes(), 'Empty graph refused'
    communities = cluster(graph)
    cohesion = score_all(graph, communities)
    labels = {}
    for cid,members in communities.items():
        folders = [str(graph.nodes[n].get('source_file','')).replace(chr(92),'/').rsplit('/',1)[0] or 'Root' for n in members if graph.nodes[n].get('source_file')]
        labels[cid] = collections.Counter(folders).most_common(1)[0][0] if folders else 'External symbols'
    gods = god_nodes(graph)
    surprises = surprising_connections(graph, communities)
    questions = suggest_questions(graph, communities, labels)
    stage = art / f'stage_{stamp}'
    stage.mkdir()
    assert to_json(graph, communities, str(stage/'graph.json'), community_labels=labels)
    output = json.loads((stage/'graph.json').read_text(encoding='utf-8'))
    ids = {n['id'] for n in output['nodes']}
    links = output.get('links',output.get('edges',[]))
    assert len(ids) == len(output['nodes'])
    assert all(e['source'] in ids and e['target'] in ids for e in links)
    relative_sources = {n.get('source_file') for n in output['nodes'] if n.get('source_file')}
    assert not set(plan['prune']) & relative_sources
    assert {p.relative_to(root).as_posix() for p in code} <= relative_sources
    old = json.loads((out/'graph.json').read_text(encoding='utf-8')) if (out/'graph.json').exists() else {'nodes':[]}
    changed_rel = {p.relative_to(root).as_posix() for p in changed} | {Path(p).relative_to(root).as_posix() for p in plan.get('changed_metadata',[])}
    unaffected = {n['id'] for n in old['nodes'] if n.get('source_file') and n['source_file'] not in set(plan['prune'])|changed_rel}
    assert unaffected <= ids, f'{len(unaffected-ids)} untouched nodes lost'
    for rel,hash_value in source_hashes.items():
        assert sha(root/rel) == hash_value, f'Source changed during graph run: {rel}'
    report = generate(graph, communities, cohesion, labels, gods, surprises, plan['detection'], {'input':0,'output':0}, str(root), suggested_questions=questions)
    note = f'\n## Verifikasi pembaruan lokal 20 Oktober 2026\n\n{len(changed)} file source diperbarui secara incremental; {len(plan["prune"])} sumber historis/hilang dikeluarkan. Word count not measured during incremental update. {len(metadata)} file stylesheet/config diwakili metadata SHA-256, bukan AST atau bukti runtime. Node aktif yang tidak terdampak dipertahankan. Tidak ada akses LLM eksternal, mutasi aplikasi/database, stage, commit, atau push. Backup dan bukti: {art.as_posix()}.\n'
    (stage/'GRAPH_REPORT.md').write_text(report+note, encoding='utf-8')
    write(stage/'.graphify_analysis.json', {'communities':communities,'cohesion':cohesion,'gods':gods,'surprises':surprises,'questions':questions})
    write(stage/'.graphify_labels.json', labels)
    write(stage/'metadata-manifest.json', {f:sha(root/f) for f in sorted(metadata)})
    write(stage/'.graphify_ast.json', extraction)
    write(stage/'.graphify_extract.json', {'nodes':output['nodes'],'edges':links,'hyperedges':output.get('hyperedges',[])})
    write(stage/'.graphify_detect.json', plan['detection'])
    (stage/'.graphify_python').write_text(sys.executable, encoding='utf-8')
    (stage/'.graphify_root').write_text(str(root), encoding='utf-8')
    if (out/'manifest.json').exists():
        shutil.copy2(out/'manifest.json', stage/'manifest.json')
    save_manifest({'code':[str(p) for p in code]}, str(stage/'manifest.json'), kind='ast', root=root, scan_corpus=[str(p) for p in code]+[str(root/f) for f in plan['retained_non_ast_sources']])
    current_manifest = json.loads((stage/'manifest.json').read_text(encoding='utf-8'))
    assert all(hashlib.md5(p.read_bytes()).hexdigest() == current_manifest[p.relative_to(root).as_posix()]['ast_hash'] for p in code)
    library=Path('D:/Laragon/www/laravel-13-cms-bum/graphify-out/vis-network.min.js')
    if not library.is_file():
        raise RuntimeError('Offline visualization library is unavailable; publication refused')
    shutil.copy2(library,stage/'vis-network.min.js')
    from graphify.exporters.html import to_html
    to_html(graph,communities,str(stage/'graph.html'),community_labels=labels,node_limit=5000)
    html=(stage/'graph.html').read_text(encoding='utf-8')
    html=html.replace('https://unpkg.com/vis-network@9.1.6/standalone/umd/vis-network.min.js','./vis-network.min.js')
    html=re.sub(r'https://(?:unpkg.com|cdn.jsdelivr.net/npm)/vis-network[^\"\s<>]*','./vis-network.min.js',html)
    assert not re.search(r'<script[^>]+src=[\"\\\']https?://',html)
    (stage/'graph.html').write_text(html,encoding='utf-8')
    (stage/'README.md').write_text(f'# Graphify lokal — {root.name}\n\n{note}\nLabel komunitas diturunkan dari folder source secara lokal. INFERRED/AMBIGUOUS tetap harus dicek di source. Update berikutnya menunggu perintah manual pengguna.\n', encoding='utf-8')
    out.mkdir(exist_ok=True)
    for file in stage.iterdir():
        shutil.copy2(file, out/file.name)
    after_git = git_state(root)
    def production_status(text):
        return {line for line in (text or '').splitlines() if 'graphify-out/' not in line and 'project-artifacts/' not in line}
    assert production_status(before_git) == production_status(after_git)
    checks = {'root':str(root),'mode':'incremental-local-AST' if old['nodes'] else 'first-local-structural-build','changed_code_files':len(changed),'pruned_sources':len(plan['prune']),'ast_corpus_files':len(code),'metadata_files':len(metadata),'changed_metadata_files':len(plan.get('changed_metadata',[])),'empty_ast_files_as_metadata':no_ast_files, 'nodes':len(ids),'edges':len(links),'communities':len(communities),'duplicate_ids':0,'dangling_edges':0,'source_hash_mismatch':0,'unaffected_nodes_lost':0,'production_git_status_unchanged':True,'confidence':dict(collections.Counter(e.get('confidence','unspecified') for e in links)),'backup':str(backup),'graph_sha256':sha(out/'graph.json')}
    write(art/'verification.json',checks)
    print(json.dumps(checks),flush=True)
    return checks

if __name__ == '__main__':
    for arg in sys.argv[1:]:
        update(Path(arg))
