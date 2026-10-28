"""Prepare one local incremental refresh using the existing Phoenix source policy."""
import hashlib
import importlib.util
import json
import os
from pathlib import Path
from graphify.detect import detect

root = Path(__file__).resolve().parents[2]
art = Path(__file__).resolve().parent
previous = root / 'project-artifacts/graphify-all-projects-20261020'
spec = importlib.util.spec_from_file_location('phoenix_graph_scope', previous / 'prepare_projects.py')
scope = importlib.util.module_from_spec(spec)
spec.loader.exec_module(scope)
safe = scope.safe

def sha(path):
    return hashlib.sha256(path.read_bytes()).hexdigest()

def dump(name, data):
    path = art / name
    if path.exists():
        import datetime, shutil
        shutil.copy2(path, path.with_name(path.name + '.bak_' + datetime.datetime.now().strftime('%Y%m%d_%H%M%S')))
    path.write_text(json.dumps(data, ensure_ascii=False, indent=2), encoding='utf-8')

detection = detect(root, google_workspace=False, extra_excludes=scope.EXCLUDES, cache_root=art)
code = {Path(p) for p in detection['files'].get('code', []) if safe(Path(p).relative_to(root).as_posix())}
metadata = set()
for directory, dirs, names in os.walk(root, followlinks=False):
    rel = Path(directory).relative_to(root).as_posix()
    dirs[:] = [name for name in dirs if safe((rel + '/' if rel != '.' else '') + name + '/file.py')]
    for name in names:
        path = Path(directory) / name
        filename = path.relative_to(root).as_posix()
        if not safe(filename) or path.is_symlink():
            continue
        if path.suffix.lower() in {'.css', '.scss', '.sass', '.html', '.xml', '.ini', '.cmd'}:
            metadata.add(path)
        elif filename.startswith('resources/views/vendor/') and path.suffix == '.php':
            code.add(path)

out = root / 'graphify-out'
old = json.loads((out / 'graph.json').read_text(encoding='utf-8'))
manifest = json.loads((out / 'manifest.json').read_text(encoding='utf-8'))
meta_manifest = json.loads((out / 'metadata-manifest.json').read_text(encoding='utf-8'))
sources = {node['source_file'] for node in old['nodes'] if node.get('source_file')}
prune = sorted(filename for filename in sources if not safe(filename) or not (root / filename).is_file())
active = {path.relative_to(root).as_posix() for path in code}
retained = sorted(sources - active - set(prune))
for filename in retained:
    path = root / filename
    if path.suffix.lower() in {'.css', '.scss', '.sass', '.html', '.xml', '.ini', '.cmd'}:
        metadata.add(path)
code = sorted(code)
metadata = sorted(metadata)
changed = [path for path in code if hashlib.md5(path.read_bytes()).hexdigest() != manifest.get(path.relative_to(root).as_posix(), {}).get('ast_hash')]
changed_meta = [path for path in metadata if sha(path) != meta_manifest.get(path.relative_to(root).as_posix())]
all_files = sorted(set(code + metadata + [root / filename for filename in retained]))
assert all(safe(path.relative_to(root).as_posix()) for path in all_files)
detection['files'] = {'code': [str(path) for path in code]}
detection['total_files'] = len(all_files)
plan = {'root': str(root), 'label': 'Laravel 13 Phoenix', 'artifacts': str(art), 'code_files': [str(path) for path in code], 'changed': [str(path) for path in changed], 'metadata_files': [str(path) for path in metadata], 'changed_metadata': [str(path) for path in changed_meta], 'prune': prune, 'retained_non_ast_sources': retained, 'stale_docs': [], 'before_nodes': len(old['nodes']), 'before_edges': len(old['links']), 'directed': old.get('directed', False), 'detection': detection}
dump('plan.json', plan)
dump('source-hashes-before.json', {path.relative_to(root).as_posix(): sha(path) for path in all_files})
dump('before-git.json', scope.state(root))
dump('inventory.json', [{'status': 'planned', 'label': 'Laravel 13 Phoenix', 'root': str(root), 'artifacts': str(art)}])
dump('history-coverage-input.json', [])
print(json.dumps({'codeFiles': len(code), 'changedCodeFiles': len(changed), 'metadataFiles': len(metadata), 'changedMetadataFiles': len(changed_meta), 'prunedSources': prune, 'changed': [path.relative_to(root).as_posix() for path in changed], 'changedMetadata': [path.relative_to(root).as_posix() for path in changed_meta]}, indent=2))
