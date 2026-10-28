"""Check the requested CMS source coverage against literal current-source references."""
import hashlib
import json
from pathlib import Path

root = Path(__file__).resolve().parents[2]
art = Path(__file__).resolve().parent
data = json.loads((root / 'graphify-out/graph.json').read_text(encoding='utf-8'))
plan = json.loads((art / 'plan.json').read_text(encoding='utf-8'))
nodes = {node['id']: node for node in data['nodes']}
sources = {node.get('source_file') for node in data['nodes']}
changed = {Path(path).relative_to(root).as_posix() for path in plan['changed'] + plan['changed_metadata']}
assert changed <= sources, changed - sources
stylesheet = 'public/assets/css/cms-bootstrap-theme.css'
target_ids = {key for key, node in nodes.items() if node.get('source_file') == stylesheet}
assert target_ids
assert all(nodes[key].get('representation') == 'file_metadata_only' for key in target_ids)
assert all(nodes[key].get('content_sha256') == hashlib.sha256((root / stylesheet).read_bytes()).hexdigest() for key in target_ids)

references = []
for layout in sorted((root / 'resources/views/themes').glob('*/cms/cms_layout.blade.php')):
    source = layout.relative_to(root).as_posix()
    lines = layout.read_text(encoding='utf-8').splitlines()
    line = next(index for index, text in enumerate(lines, 1) if 'assets/css/cms-bootstrap-theme.css' in text)
    matching = [edge for edge in data['links'] if edge['target'] in target_ids
                and nodes[edge['source']].get('source_file') == source
                and edge.get('relation') == 'references_stylesheet'
                and edge.get('confidence') == 'EXTRACTED'
                and edge.get('source_file') == source
                and edge.get('source_location') == f'L{line}']
    assert len(matching) == 1, f'Missing/duplicate verified stylesheet reference: {source}:L{line}'
    references.append({'source_file': source, 'source_location': f'L{line}', 'target': stylesheet, 'confidence': 'EXTRACTED'})
assert references
article_view = 'resources/views/manage_article/manage_article.blade.php'
article_style = 'public/assets/css/article/manage-article-2026.css'
assert any(nodes[edge['source']].get('source_file') == article_view
           and nodes[edge['target']].get('source_file') == article_style
           and edge.get('relation') == 'references_stylesheet'
           and edge.get('confidence') == 'EXTRACTED' for edge in data['links'])
assert any(node.get('label') == 'ManageArticleVue3' for node in nodes.values())
mirror = json.loads((root / 'graphify-out/.graphify_extract.json').read_text(encoding='utf-8'))
assert mirror['nodes'] == data['nodes'] and mirror['edges'] == data['links']
result = {'ok': True, 'changed_sources_verified': len(changed), 'cms_layout_references_verified': len(references),
          'references': references, 'article_view_stylesheet_verified': True, 'graph_mirror_matches': True,
          'graph_sha256': hashlib.sha256((root / 'graphify-out/graph.json').read_bytes()).hexdigest()}
(art / 'scope-check.json').write_text(json.dumps(result, ensure_ascii=False, indent=2), encoding='utf-8')
print(json.dumps({key: value for key, value in result.items() if key != 'references'}))
