"""Keep file-only coverage explicit and flag unresolved receiver targets locally."""
import collections
import datetime
import hashlib
import json
import re
import shutil
from pathlib import Path
from graphify.paths import load_node_link_graph
from graphify.exporters.html import to_html

root = Path(__file__).resolve().parents[2]
art = Path(__file__).resolve().parent
out = root / 'graphify-out'
stamp = datetime.datetime.now(datetime.timezone(datetime.timedelta(hours=7))).strftime('%Y%m%d_%H%M%S')
backup = art / ('backup_' + stamp + '_before_quality_annotations')
backup.mkdir()
for name in ['graph.json', '.graphify_extract.json', 'graph.html', 'GRAPH_REPORT.md', 'README.md', '.vocab.txt']:
    file = out / name
    if file.exists():
        target = backup / (name + '.bak_' + stamp)
        shutil.copy2(file, target)
        assert file.read_bytes() == target.read_bytes()
for name in ['verification.json', 'qc.json', 'final-qc.json']:
    file = art / name
    if file.exists():
        shutil.copy2(file, backup / (name + '.bak_' + stamp))

data = json.loads((out / 'graph.json').read_text(encoding='utf-8'))
fresh = json.loads((art / 'extraction.json').read_text(encoding='utf-8'))
groups = collections.defaultdict(list)
for node in fresh['nodes']:
    groups[node.get('source_file', '')].append(node)
metadata_only = sorted(filename for filename, nodes in groups.items() if filename and len(nodes) == 1 and nodes[0].get('label') == Path(filename).name and not nodes[0].get('_callable') and nodes[0].get('source_location') is None)
for node in data['nodes']:
    if node.get('source_file') in metadata_only:
        file = root / node['source_file']
        node.update(representation='file_metadata_only', extraction_note='Native extractor returned no symbols; this file node records existence and current hash only.', content_sha256=hashlib.sha256(file.read_bytes()).hexdigest())

nodes = {node['id']: node for node in data['nodes']}
ambiguous = []
for edge in data['links']:
    if edge['source'] != edge['target']:
        continue
    node = nodes[edge['source']]
    name = node.get('label', '').lstrip('.').removesuffix('()')
    filename = edge.get('source_file') or node.get('source_file')
    location = edge.get('source_location') or node.get('source_location', '')
    match = re.search(r'L(\d+)', str(location))
    if not filename or not match:
        continue
    file = (root / filename).resolve()
    assert file.is_relative_to(root)
    lines = file.read_text(encoding='utf-8').splitlines()
    line = lines[int(match[1]) - 1] if int(match[1]) <= len(lines) else ''
    pattern = r'((?:\$?[A-Za-z_]\w*)(?:(?:->|\.)[A-Za-z_]\w*)*)(->|\.)' + re.escape(name) + r'\s*\('
    if not any(call[1] not in {'$this', 'this', 'self'} for call in re.finditer(pattern, line)):
        continue
    edge.update(confidence='AMBIGUOUS', previous_confidence=edge.get('confidence'), resolution_note='Literal call uses a different receiver; the parser target is not established. Verify active source before treating this edge as recursion.')
    if 'confidence_score' in edge:
        edge['previous_confidence_score'] = edge.pop('confidence_score')
    ambiguous.append({'source_file': filename, 'source_location': location, 'label': name, 'evidence': line.strip()})

(out / 'graph.json').write_text(json.dumps(data, ensure_ascii=False, indent=2), encoding='utf-8')
mirror = json.loads((out / '.graphify_extract.json').read_text(encoding='utf-8'))
mirror['nodes'] = data['nodes']
mirror['edges'] = data['links']
(out / '.graphify_extract.json').write_text(json.dumps(mirror, ensure_ascii=False, indent=2), encoding='utf-8')
communities = collections.defaultdict(list)
for node in data['nodes']:
    communities[node.get('community', 0)].append(node['id'])
labels = {int(key): value for key, value in json.loads((out / '.graphify_labels.json').read_text(encoding='utf-8')).items()}
to_html(load_node_link_graph(data), dict(communities), str(out / 'graph.html'), community_labels=labels, node_limit=5000)
html = (out / 'graph.html').read_text(encoding='utf-8')
html = re.sub(r'https://(?:unpkg.com|cdn.jsdelivr.net/npm)/vis-network[^"\s<>]*', './vis-network.min.js', html)
assert not re.search(r'<script[^>]+src=["\x27]https?://', html)
(out / 'graph.html').write_text(html, encoding='utf-8')
note = '\n## Cakupan parser dan batas receiver, 26 Oktober 2026\n\n' + str(len(metadata_only)) + ' template yang tidak menghasilkan simbol ditandai file_metadata_only dengan hash source. ' + str(len(ambiguous)) + ' relasi same-name pada receiver lain ditandai AMBIGUOUS; tidak ada target alternatif yang dikarang. Metadata dan graph ini bukan bukti runtime aplikasi.\n'
for name in ['README.md', 'GRAPH_REPORT.md']:
    file = out / name
    file.write_text(file.read_text(encoding='utf-8') + note, encoding='utf-8')
vocab = set()
for node in data['nodes']:
    for word in re.findall(r'[^\W\d_]+', node.get('label', '') or '', re.UNICODE):
        for term in re.findall(r'[A-Z]+(?=[A-Z][a-z])|[A-Z]?[a-z]+|[A-Z]+', word) or [word]:
            if 3 <= len(term) <= 30:
                vocab.add(term.lower())
(out / '.vocab.txt').write_text('\n'.join(sorted(vocab)), encoding='utf-8')
verification = json.loads((art / 'verification.json').read_text(encoding='utf-8'))
verification.update(empty_ast_files_as_metadata=metadata_only, receiver_bindings_marked_ambiguous=len(ambiguous), confidence=dict(collections.Counter(edge.get('confidence', 'unspecified') for edge in data['links'])), graph_sha256=hashlib.sha256((out / 'graph.json').read_bytes()).hexdigest())
(art / 'verification.json').write_text(json.dumps(verification, ensure_ascii=False, indent=2), encoding='utf-8')
(art / 'quality-annotations.json').write_text(json.dumps({'metadata_only': metadata_only, 'ambiguous_receiver_bindings': ambiguous, 'backup': str(backup)}, ensure_ascii=False, indent=2), encoding='utf-8')
print(json.dumps({'fileOnlyTemplates': len(metadata_only), 'ambiguousReceiverBindings': ambiguous, 'vocabularyTerms': len(vocab)}, indent=2))
