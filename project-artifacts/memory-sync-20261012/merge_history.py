import datetime as dt
import shutil
from pathlib import Path
base=Path(__file__).parent
path=base/'export_memories.py'
src=path.read_text(encoding='utf-8')
stamp=dt.datetime.now().strftime('%Y%m%d_%H%M%S')
for name in ['export_memories.py','prepared.json','plan.json','review.json']:
    p=base/name
    if p.exists():shutil.copy2(p,Path(str(p)+'.bak_'+stamp+'_merge-history'))
old="    conn.close()\n    return row\n"
new=r"""    conn.close()
    # SQLite projection can be incomplete; retain missing messages from the rollout.
    rollout=extract_rollout(dict(row))
    known={(m['role'],m['text']) for m in row['messages']}
    added=0
    for message in rollout['messages']:
        key=(message['role'],message['text'])
        if key not in known:
            row['messages'].append(message)
            known.add(key)
            added+=1
    def chronological(message):
        try:return dt.datetime.fromisoformat(message['timestamp'].replace('Z','+00:00')).timestamp()
        except ValueError:return 0
    row['messages'].sort(key=chronological)
    patch_paths={p['path'] for p in row['patches']}
    row['patches']+=[p for p in rollout['patches'] if p['path'] not in patch_paths]
    row['errors']+=rollout['errors']
    row['history_source']+=' + rollout comparison ('+str(added)+' missing text messages recovered)'
    row['messages_recovered_from_rollout']=added
    return row
"""
assert src.count(old)==1
src=src.replace(old,new)
compile(src,str(path),'exec')
path.write_text(src,encoding='utf-8')
print('Database/rollout reconciliation syntax checked.')
