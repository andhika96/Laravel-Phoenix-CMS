import collections
import hashlib
import json
import sys
from pathlib import Path
from graphify.detect import detect

BASE = Path(__file__).parent
EXCLUDES = ['project-artifacts/', '**/project-artifacts/**', '.env*', '**/.env*', '*.bak*', '**/backup*/**', '**/Backup*/**', '**/node_modules/**', '**/vendor/**', '**/fontawesome/**', '**/graphify-out/**', '**/storage/**', '**/bootstrap/cache/**', '**/build/**', '**/dist/**', '**/.next/**', '**/.gradle/**', '**/*.log', '**/*credential*', '**/*secret*']

def plan(root):
    root = root.resolve()
    out = root / 'graphify-out'
    art = root / 'project-artifacts' / 'graphify-history-sync-20261004'
    if not (root / 'project-artifacts').is_dir():
        raise RuntimeError(f'Folder project-artifacts belum disetujui: {root}')
    art.mkdir(exist_ok=True)
    detection = detect(root, google_workspace=False, extra_excludes=EXCLUDES, cache_root=art)
    code = [Path(p) for p in detection['files'].get('code', [])]
    # Android app source is explicitly part of earlier CMS chats, despite a local Git ignore.
    mobile = root / 'mobile/android'
    if mobile.is_dir():
        mobile_source = mobile / 'app/src'
        extra = [p for p in mobile_source.rglob('*') if p.is_file() and p.suffix in {'.kt', '.kts', '.java', '.js', '.json'} and not any(x in p.parts for x in ('build', 'node_modules', 'backup', 'backups'))]
        extra += list(mobile.glob('*.kts')) + list((mobile / 'app').glob('*.kts'))
        code = sorted(set(code + extra))
        detection['files']['code'] = [str(p) for p in code]
    manifest = json.loads((out / 'manifest.json').read_text(encoding='utf-8')) if (out / 'manifest.json').exists() else {}
    graph = json.loads((out / 'graph.json').read_text(encoding='utf-8')) if (out / 'graph.json').exists() else {'nodes': [], 'links': [], 'directed': True}
    def relative(p):
        try:
            return Path(str(p).replace('\\', '/')).relative_to(root).as_posix()
        except ValueError:
            return str(p).replace('\\', '/')
    normalized_manifest = {relative(p): v for p, v in manifest.items()}
    changed = [p for p in code if hashlib.md5(p.read_bytes()).hexdigest() != normalized_manifest.get(p.relative_to(root).as_posix(), {}).get('ast_hash')]
    graph_files = {relative(n['source_file']) for n in graph['nodes'] if n.get('source_file')}
    # Preserve existing semantic/source coverage when it remains active. Refresh code locally.
    active = {p.relative_to(root).as_posix() for p in code}
    missing = sorted(f for f in graph_files if not (root / f).is_file())
    excluded = sorted(f for f in graph_files if any(t in f.lower() for t in ('project-artifacts/', '.bak', '/backup', 'backups/', 'node_modules/', 'vendor/', 'fontawesome/', 'graphify-out/', 'storage/', 'bootstrap/cache/')) or Path(f).name.startswith('.env'))
    change_meta = [f for f in graph_files if f not in active and f not in excluded and f not in missing]
    data = {'root': str(root), 'artifacts': str(art), 'code_files': [str(p) for p in code], 'changed': [str(p) for p in changed], 'prune': sorted(set(missing + excluded)), 'retained_non_ast_sources': sorted(change_meta), 'before_nodes': len(graph['nodes']), 'before_edges': len(graph.get('links', graph.get('edges', []))), 'directed': graph.get('directed', False), 'detection': detection}
    (art / 'plan.json').write_text(json.dumps(data, ensure_ascii=False, indent=2), encoding='utf-8')
    print(json.dumps({'root': str(root), 'code': len(code), 'changed': len(changed), 'prune': len(data['prune']), 'metadata_sources': len(change_meta), 'before_nodes': data['before_nodes'], 'extensions': dict(collections.Counter(p.suffix for p in code)), 'changed_sample': [p.relative_to(root).as_posix() for p in changed[:12]]}), flush=True)
    return data

if __name__ == '__main__':
    for name in sys.argv[1:]:
        plan(Path(name))
