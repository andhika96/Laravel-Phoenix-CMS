import json
from pathlib import Path

artifact = Path(__file__).resolve().parent
root = artifact.parents[1]
out = root / 'graphify-out'
graph = json.loads((out / 'graph.json').read_text(encoding='utf-8'))
nodes = {node['id']: node for node in graph['nodes']}
by_source = {}
for node in nodes.values():
    by_source.setdefault(node.get('source_file'), []).append(node)

layout = 'resources/views/auth/auth_layout.blade.php'
base = 'public/assets/css/auth/auth-base.css'
bridge = 'public/assets/css/cms/cms-bootstrap-theme.css'
auth_sources = {
    f'resources/views/auth/{template}/{page}.blade.php'
    for template in ('default', 'card', 'split_left', 'split_right')
    for page in ('login', 'signup', 'forgotpassword', 'resetpassword')
}
theme_layouts = {
    f'resources/views/themes/{theme}/cms/cms_layout.blade.php'
    for theme in ('arunika_prism', 'arunika_aurora', 'arunika_lucent', 'arunika_equinox', 'default', 'calm_green')
}
assert auth_sources | theme_layouts | {layout, base, bridge, 'app/Support/AuthAppearance.php'} <= by_source.keys()
literal_styles = {
    (edge['source_file'], nodes[edge['target']].get('source_file'))
    for edge in graph['links'] if edge.get('relation') == 'references_stylesheet'
}
assert (layout, base) in literal_styles
assert all((view, bridge) in literal_styles for view in theme_layouts)
assert not any(view in theme_layouts and asset and asset.startswith('public/assets/css/auth/') for view, asset in literal_styles)
auth_edges = [edge for edge in graph['links'] if edge.get('relation') == 'extends' and edge.get('source_file') in auth_sources]
assert {edge['source_file'] for edge in auth_edges} == auth_sources
assert all('auth_layout' in nodes[edge['target']]['label'] for edge in auth_edges)
result = {
    'auth_views_extending_independent_layout': len(auth_edges),
    'cms_layouts_referencing_cms_bridge': len(theme_layouts),
    'auth_base_literal_reference': True,
    'auth_helper_nodes': [node['label'] for node in by_source['app/Support/AuthAppearance.php']],
    'auth_stylesheet_metadata': sorted(path for path in by_source if path and path.startswith('public/assets/css/auth/')),
    'css_selector_runtime_checked': False,
}
(artifact / 'conversation-scope-verification.json').write_text(json.dumps(result, indent=2), encoding='utf-8')
print(json.dumps(result, indent=2))
