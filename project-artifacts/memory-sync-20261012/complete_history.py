import datetime as dt
import shutil
from pathlib import Path
base = Path(__file__).parent
path = base/'export_memories.py'
src = path.read_text(encoding='utf-8')
stamp = dt.datetime.now().strftime('%Y%m%d_%H%M%S')
for name in ['export_memories.py','prepared.json','plan.json','review.json']:
    p=base/name
    if p.exists():
        shutil.copy2(p,Path(str(p)+'.bak_'+stamp+'_paginated-history'))
assert 'def extract(row):' in src
src=src.replace('def extract(row):','def extract_rollout(row):',1)
extra=r'''def extract(row):
    database = CODEX/'thread_history_1.sqlite'
    conn=sqlite3.connect('file:'+database.as_posix()+'?mode=ro',uri=True)
    conn.execute('pragma temp_store=MEMORY')
    conn.row_factory=sqlite3.Row
    final_ids={r[0] for r in conn.execute('SELECT final_agent_item_id FROM thread_turns WHERE thread_id=?',(row['id'],)) if r[0]}
    items=conn.execute("SELECT item_id,item_type,created_at_ms,item_json FROM thread_items WHERE thread_id=? AND item_type IN ('userMessage','agentMessage','fileChange') ORDER BY rollout_ordinal,created_at_ms",(row['id'],)).fetchall()
    if not items:
        conn.close()
        row['history_source']='Rollout fallback; tidak ada item pada database riwayat.'
        return extract_rollout(row)
    row.update(messages=[],patches=[],errors=[],attachment_count=0,history_source=str(database)+' / thread_items + thread_turns')
    for item_row in items:
        item=json.loads(item_row['item_json'])
        typ=item_row['item_type']
        if typ=='fileChange':
            for change in item.get('changes',[]):
                row['patches'].append({'operation':change.get('kind',{}).get('type','unknown')+' (status '+str(item.get('status','unknown'))+')','path':clean(change.get('path',''))})
            continue
        if typ=='agentMessage':
            if item.get('phase') not in ('final_answer','final') and item_row['item_id'] not in final_ids:
                continue
            role,text='assistant',item.get('text','')
        else:
            role='user'
            content=item.get('content',[])
            row['attachment_count']+=sum(c.get('type')!='text' for c in content if isinstance(c,dict))
            text='\n'.join(c.get('text','') for c in content if isinstance(c,dict))
            if '## My request:' in text:
                text=text.split('## My request:',1)[1].strip()
            elif '## My request for Codex:' in text:
                text=text.split('## My request for Codex:',1)[1].strip()
            text=re.sub(r'<recommended_plugins>.*?</recommended_plugins>','',text,flags=re.S).strip()
            if '# AGENTS.md' in text or '<INSTRUCTIONS>' in text or text.lstrip().startswith(('<environment_context>','<permissions','<external_codex','<user_instructions>','<turn_aborted>')):
                continue
            if SENSITIVE_REQUEST.search(text):
                text='[Pesan pemberian kredensial disembunyikan; tujuan login/verifikasi tetap tercatat pada laporan akhir.]'
        text=clean(text)
        if text:
            row['messages'].append({'role':role,'timestamp':dt.datetime.fromtimestamp(item_row['created_at_ms']/1000,TZ).isoformat(),'text':text})
    row['patches']=list({(p['operation'],p['path']):p for p in row['patches']}.values())
    conn.close()
    return row

'''
assert 'def previous_memories():' in src
src=src.replace('def previous_memories():',extra+'def previous_memories():',1)
src=src.replace("'- Riwayat asli: ' + row['rollout_path'],","'- Riwayat asli: ' + row['rollout_path'],\n            '- Sumber riwayat lengkap: ' + row.get('history_source','API read_thread; seluruh pagination selesai'),")
src=src.replace("'messages':sum(len(r['messages']) for r in rows),","'messages':sum(len(r['messages']) for r in rows), 'history_sources':dict(collections.Counter('database-history' if 'thread_items' in r.get('history_source','') else 'rollout-or-cloud' for r in rows)),")
compile(src,str(path),'exec')
path.write_text(src,encoding='utf-8')
print('Complete paginated-history extractor syntax checked.')
