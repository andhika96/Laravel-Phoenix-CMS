"""Conservatively flag same-name receiver collisions without inventing target nodes."""
import collections
import hashlib
import json
import re
import shutil
from pathlib import Path
from graphify.paths import load_node_link_graph
from graphify.exporters.html import to_html

BASE=Path(__file__).parent
def read(p):return json.loads(p.read_text(encoding='utf-8'))
def sha(p):return hashlib.sha256(p.read_bytes()).hexdigest()
def foreign_receiver(line,name):
    pattern=r'((?:\$?[A-Za-z_]\w*)(?:(?:->|\.)[A-Za-z_]\w*)*)(->|\.)'+re.escape(name)+r'\s*\('
    return any(m[1] not in {'$this','this','self'} for m in re.finditer(pattern,line))

assert foreign_receiver('$this->s3->putObject($x);','putObject')
assert foreign_receiver('service.photo(uuid);','photo')
assert not foreign_receiver('$this->update($x);','update')
assert not foreign_receiver('walk(folder);','walk')

def main():
    reviewed=[]
    for row in read(BASE/'inventory.json'):
        if row['status']!='planned':continue
        root=Path(row['root']);out=root/'graphify-out';art=Path(row['artifacts'])
        data=read(out/'graph.json');nodes={n['id']:n for n in data['nodes']};flagged=[]
        for edge in data['links']:
            if edge['source']!=edge['target']:continue
            node=nodes[edge['source']];name=node.get('label','').lstrip('.').removesuffix('()')
            f=edge.get('source_file') or node.get('source_file');loc=edge.get('source_location') or node.get('source_location','')
            match=re.search(r'L(\d+)',str(loc))
            if not f or not match:continue
            p=(root/f).resolve();assert p.is_relative_to(root.resolve())
            lines=p.read_text(encoding='utf-8',errors='replace').splitlines();line=lines[int(match[1])-1] if int(match[1])<=len(lines) else ''
            if not foreign_receiver(line,name):continue
            edge.update(confidence='AMBIGUOUS',previous_confidence=edge.get('confidence'),resolution_note='Same-name call on a different receiver; AST call exists but target binding is not proven. Recheck source before treating as recursion.')
            flagged.append({'source':edge['source'],'target':edge['target'],'source_file':f,'source_location':loc,'label':name})
        if not flagged:continue
        before={f:sha(root/f) for f in {x['source_file'] for x in flagged}}
        for name in ['graph.json','graph.html','GRAPH_REPORT.md','README.md','.graphify_extract.json','verification.json','qc.json']:
            p=(art if name in {'verification.json','qc.json'} else out)/name
            if p.exists():shutil.copy2(p,art/(name+'.bak_20261020_receiver_ambiguity'))
        (out/'graph.json').write_text(json.dumps(data,ensure_ascii=False,indent=2),encoding='utf-8')
        extract=read(out/'.graphify_extract.json')
        targets={x['source'] for x in flagged}
        for e in extract.get('edges',[]):
            if e['source']==e['target'] and e['source'] in targets:
                e.update(confidence='AMBIGUOUS',resolution_note='Same-name receiver target is unresolved; verify against active source.')
        (out/'.graphify_extract.json').write_text(json.dumps(extract,ensure_ascii=False,indent=2),encoding='utf-8')
        graph=load_node_link_graph(data);communities=collections.defaultdict(list)
        for n in data['nodes']:communities[n.get('community',0)].append(n['id'])
        labels={k:'Community '+str(k) for k in communities}
        to_html(graph,dict(communities),str(out/'graph.html'),community_labels=labels,node_limit=5000)
        html=(out/'graph.html').read_text(encoding='utf-8');html=re.sub(r'https://(?:unpkg.com|cdn.jsdelivr.net/npm)/vis-network[^\"\s<>]*','./vis-network.min.js',html)
        assert not re.search(r'<script[^>]+src=[\"\x27]https?://',html)
        (out/'graph.html').write_text(html,encoding='utf-8')
        for name in ['GRAPH_REPORT.md','README.md']:
            p=out/name;p.write_text(p.read_text(encoding='utf-8')+'\n## Batas resolusi receiver — 20 Oktober 2026\n\n'+str(len(flagged))+' relasi same-name pada receiver lain ditandai AMBIGUOUS. Pemanggilan literal ditemukan, tetapi target method belum terbukti. Node/relasi dipertahankan agar tidak mengarang target alternatif. Self-loop lain dapat berupa recursion yang sah; graph tidak membuktikan perilaku runtime.\n',encoding='utf-8')
        for name in ['verification.json','qc.json']:
            p=art/name
            if not p.exists():continue
            value=read(p);value['confidence']=dict(collections.Counter(e.get('confidence','unspecified') for e in data['links']));value['graph_sha256']=sha(out/'graph.json');value['receiver_bindings_marked_ambiguous']=len(flagged);p.write_text(json.dumps(value,ensure_ascii=False,indent=2),encoding='utf-8')
        assert before=={f:sha(root/f) for f in before}
        record={'root':str(root),'flagged_count':len(flagged),'details':flagged}
        (art/'receiver-ambiguity-review.json').write_text(json.dumps(record,ensure_ascii=False,indent=2),encoding='utf-8');reviewed.append(record)
    (BASE/'receiver-ambiguity-review.json').write_text(json.dumps(reviewed,ensure_ascii=False,indent=2),encoding='utf-8')
    print(json.dumps({'projects':len(reviewed),'ambiguous_receiver_links':sum(x['flagged_count'] for x in reviewed)}),flush=True)
if __name__=='__main__':main()
