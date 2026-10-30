"""Read-only file-level CSS inventory. All output stays beside this script."""
import collections
import csv
import hashlib
import json
import os
from pathlib import Path
import re
import subprocess
from urllib.parse import urlsplit

ROOT = Path(__file__).resolve().parents[3]
OUT = Path(__file__).resolve().parent
TOKEN = re.compile(r"[A-Za-z0-9_@./:+%~-]+\.css\b", re.I)
SKIP = {'.git', 'node_modules', 'vendor', 'project-artifacts', 'graphify-out', 'cache', 'logs', 'renditions'}
TEXT = {'.php', '.js', '.mjs', '.ts', '.vue', '.css', '.scss', '.json', '.html'}

def files(base):
    for folder, dirs, names in os.walk(base, followlinks=False):
        dirs[:] = [d for d in dirs if (d not in SKIP or Path(folder) == ROOT / 'public/assets')
                   and not re.search(r'(?i)bak|backup|archived', d)
                   and not (Path(folder) == ROOT / 'public' and d in {'storage', 'mockups', 'prototypes'})]
        for name in names:
            if not re.search(r'(?i)\.bak|\.old|\.orig', name):
                yield Path(folder) / name

def strip_comments(text):
    return re.sub(r'\{\{--.*?--\}\}|<!--.*?-->|/\*.*?\*/|(?m:^\s*//[^\n]*)',
                  lambda m: '\n' * m.group().count('\n'), text, flags=re.S)

def resolve(token, source):
    if token.startswith(('http://', 'https://', '//')):
        if urlsplit(token).hostname != 'laravel-13-phoenix.aruna':
            return None
        token = urlsplit(token).path
    token = token.lstrip('/')
    if token.startswith('storage/fonts/'):
        return ROOT / 'storage/app/public' / token.removeprefix('storage/')
    if token.startswith(('assets/', 'build/')):
        return ROOT / 'public' / token
    if token.startswith(('resources/', 'public/')):
        return ROOT / token
    return (source.parent / token).resolve()

assert resolve('assets/css/phoenix-cms.css', ROOT/'resources/views/x.blade.php') == ROOT/'public/assets/css/phoenix-cms.css'
assert resolve('./styles.css', ROOT/'resources/js/filemanager_v2/main.js') == ROOT/'resources/js/filemanager_v2/styles.css'
assert not TOKEN.search(strip_comments('{{-- <link href="assets/css/unused.css"> --}}'))
assert TOKEN.search(strip_comments('<link href="https://example.com/live.css">')).group() == 'https://example.com/live.css'

all_files = list(files(ROOT/'public')) + list(files(ROOT/'resources'))
css_files = sorted(p for p in all_files if p.suffix.lower() == '.css')
css_files += sorted((ROOT/'storage/app/public/fonts').rglob('*.css'))
css_set = {p.resolve(): p for p in css_files}
refs = collections.defaultdict(list)
external = []
unresolved = []
source_files = all_files + [p for base in ['app', 'routes', 'config', 'bootstrap', 'scripts'] for p in files(ROOT/base)]
source_files += list(ROOT.glob('*.js'))
read_count = 0
for source in source_files:
    if source.suffix.lower() not in TEXT:
        continue
    raw = source.read_text(encoding='utf-8-sig', errors='replace')
    read_count += 1
    if '.css' not in raw.lower():
        continue
    clean = strip_comments(raw)
    source_name = source.relative_to(ROOT).as_posix()
    kind = 'plugin_internal' if source_name.startswith('public/assets/plugins/') else 'application'
    if any(part.lower() in {'test', 'tests', 'docs', 'doc', 'sample', 'samples', 'example', 'examples'} for part in source.parts):
        kind = 'example_or_test'
    if source_name.startswith('public/build/'):
        kind = 'build_metadata'
    for match in TOKEN.finditer(clean):
        token = match.group()
        target = resolve(token, source)
        entry = {'source': source_name, 'line': clean.count('\n', 0, match.start())+1, 'token': token, 'kind': kind}
        if target is None:
            if kind == 'application':
                external.append(entry)
        elif target.resolve() in css_set and target.resolve() != source.resolve():
            refs[target.resolve()].append(entry)
        elif kind == 'application' and '/' in token and not token.startswith(('http', '//')):
            unresolved.append(entry)

rows = []
for path in css_files:
    name = path.relative_to(ROOT).as_posix()
    entries = refs[path.resolve()]
    app = [r for r in entries if r['kind'] == 'application']
    plugin = [r for r in entries if r['kind'] == 'plugin_internal']
    if name.startswith('public/assets/css/'):
        group = 'custom_public'
    elif name.startswith('public/assets/beta_css/'):
        group = 'beta'
    elif name.startswith('resources/pagebuilder_elementor_v24/'):
        group = 'pagebuilder_module_source'
    elif name.startswith('resources/'):
        group = 'build_source'
    elif name.startswith('storage/'):
        group = 'dynamic_font'
    elif name.startswith('public/build/') or name == 'public/assets/plugins/filemanager_v2/filemanager-v2.css':
        group = 'build_output'
    elif any(x in path.parts for x in ['test', 'tests', 'docs', 'sample', 'samples']):
        group = 'plugin_example_test'
    else:
        group = 'plugin_distribution'
    status = 'REFERENCED_APPLICATION' if app else 'REFERENCED_PLUGIN_INTERNAL' if plugin else 'NO_RESOLVED_APPLICATION_REFERENCE'
    if group == 'dynamic_font':
        status = 'DYNAMIC_FONT_OPTION'
    if group == 'plugin_example_test':
        status = 'PLUGIN_EXAMPLE_TEST'
    payload = path.read_bytes()
    rows.append({'path': name, 'group': group, 'status': status, 'bytes': len(payload),
                 'sha256': hashlib.sha256(payload).hexdigest(), 'application_refs': len(app),
                 'plugin_refs': len(plugin), 'evidence': '; '.join(f"{r['source']}:{r['line']}" for r in app[:8])})

with (OUT/'css-inventory.csv').open('w', newline='', encoding='utf-8-sig') as f:
    writer = csv.DictWriter(f, fieldnames=rows[0].keys())
    writer.writeheader()
    writer.writerows(rows)
(OUT/'references.json').write_text(json.dumps({str(css_set[k].relative_to(ROOT)).replace('\\', '/'):v for k,v in refs.items()}, indent=2), encoding='utf-8')
(OUT/'external-css.json').write_text(json.dumps(external, indent=2), encoding='utf-8')
(OUT/'unresolved-css-tokens.json').write_text(json.dumps(unresolved, indent=2), encoding='utf-8')
hashes = collections.defaultdict(list)
for row in rows:
    hashes[row['sha256']].append(row['path'])
duplicates = [v for v in hashes.values() if len(v)>1]
(OUT/'exact-duplicates.json').write_text(json.dumps(duplicates, indent=2), encoding='utf-8')
summary = {'total_css':len(rows), 'source_files_read':read_count,
           'groups':dict(collections.Counter(r['group'] for r in rows)),
           'statuses':dict(collections.Counter(r['status'] for r in rows)),
           'external_urls':len({r['token'] for r in external}), 'exact_duplicate_groups':len(duplicates),
           'custom_css':[r for r in rows if r['group'] in {'custom_public','beta'}]}
(OUT/'summary.json').write_text(json.dumps(summary, indent=2), encoding='utf-8')
assert len(rows) == len({r['path'] for r in rows})
assert all((ROOT/r['path']).is_file() for r in rows)
print(json.dumps({k:v for k,v in summary.items() if k!='custom_css'}, indent=2))
for row in summary['custom_css']:
    print(f"{row['status']:36} {row['path']} ({row['application_refs']} refs)")
