"""Reconcile accessible human chats; publish additive, sanitized E/C handoffs."""
import collections
import datetime as dt
import hashlib
import importlib.util
import json
import re
import sqlite3
import shutil
import sys
from pathlib import Path

BASE = Path(__file__).parent
OLD = BASE.parent / 'memory-sync-20261012'
spec = importlib.util.spec_from_file_location('old_export', OLD / 'export_memories.py')
helper = importlib.util.module_from_spec(spec)
spec.loader.exec_module(helper)
DESTS = helper.DESTS
CURRENT = '01a15802-6823-7731-8e79-8c5a9aa0ee52'
STAMP = '20261020'
TZ = dt.timezone(dt.timedelta(hours=7))

def dump(name, value):
    path=BASE/name
    body=json.dumps(value, ensure_ascii=False, indent=2)
    if path.exists() and path.read_text(encoding='utf-8')!=body:
        backup=path.with_name(path.name+'.bak_'+dt.datetime.now(TZ).strftime('%Y%m%d_%H%M%S_%f')+'_reconcile')
        shutil.copy2(path,backup)
        path.write_text(body,encoding='utf-8')
    else: helper.write_new(path,body)

def sanitized(text):
    text = re.sub(r'<(?:external_codex_apps_open_page|codex_apps_open_page_context)>.*?</(?:external_codex_apps_open_page|codex_apps_open_page_context)>', '', text, flags=re.S)
    text = re.sub(r'(?im)^.*(?:password(?:nya)?|kata\s+sandi|kredensial|credential|DB_PASSWORD)\b.*$', '[Kredensial disamarkan]', text)
    return helper.clean(text)

def digest(messages):
    return hashlib.sha256(json.dumps(messages, ensure_ascii=False, sort_keys=True).encode()).hexdigest()

def note_index():
    found = collections.defaultdict(list)
    paths = list(DESTS[0].glob('*.md')) + list((DESTS[0] / 'extensions/ad_hoc/notes').glob('*.md'))
    for p in paths:
        if '.bak' in p.name or p.name.startswith(STAMP):
            continue
        content = p.read_text(encoding='utf-8-sig', errors='replace')
        ids = set(re.findall(helper.UUID, p.name))
        ids.update(re.findall(r'(?im)^(?:-\s*)?(?:Chat ID|Thread ID|thread_id|Source thread|Source chat)\s*:\s*[` ]*(' + helper.UUID + ')', content[:5000]))
        for ident in ids:
            found[ident].append({'path':str(p),'sha256':helper.sha(p),'evidence':re.findall(r'Evidence SHA-256:\s*([a-f0-9]{64})',content)})
    return found

def prepare():
    old = {r['id']:r for r in json.loads((OLD / 'prepared.json').read_text(encoding='utf-8'))}
    previous = note_index()
    conn = sqlite3.connect((helper.CODEX / 'state_5.sqlite').as_uri() + '?mode=ro', uri=True)
    conn.row_factory = sqlite3.Row
    rows = [dict(r) for r in conn.execute("SELECT id,title,name,cwd,rollout_path,created_at,updated_at,archived,project_id FROM threads WHERE thread_source='user' AND (agent_path IS NULL OR agent_path IN ('','/root')) AND id<>? ORDER BY created_at", (CURRENT,))]
    conn.close()
    api=json.loads((BASE/'app-history-reads.json').read_text(encoding='utf-8'))
    sidebar = json.loads((BASE / 'sidebar.json').read_text(encoding='utf-8'))
    visible = {r['id']:r for r in sidebar['threads'] + sidebar.get('pinnedThreads',[])}
    labels = {p['path'].lower():p['label'] for p in json.loads((BASE/'projects.json').read_text(encoding='utf-8'))}
    names = {}
    for line in (helper.CODEX/'session_index.jsonl').read_text(encoding='utf-8').splitlines():
        item=json.loads(line); names[item['id']]=item.get('thread_name','')
    for row in rows:
        row['cwd']=row['cwd'].removeprefix('\\\\?\\')
        row['title']=visible.get(row['id'],{}).get('title') or row.get('name') or names.get(row['id']) or row['title'].splitlines()[-1][:140]
        row['title']=sanitized(row['title'])
        row['project']=labels.get(row['cwd'].lower()) or old.get(row['id'],{}).get('project') or ('Tanpa project' if '\\Documents\\Codex\\' in row['cwd'] else Path(row['cwd']).name)
        row['kind']='codex-local'
        helper.extract(row)
        if row['id'] in api:
            pages=api[row['id']]
            assert not pages[-1]['page']['hasMore']
            row['history_source']+='; read_thread diverifikasi '+str(len(pages))+' halaman sampai selesai'
        if not row['messages'] and row['id'] in old:
            row['messages']=old[row['id']]['messages']
            row['history_source']='Arsip 12 Oktober; sumber lokal kini tidak tersedia. Tidak mengklaim pembaruan terbaru.'
        if not row['messages']:
            row['needs_app_read']=True
        row['original_evidence']=digest(row['messages'])
        for m in row['messages']:
            m['text']=sanitized(m['text'])
        row['messages']=[m for m in row['messages'] if m['text'] and not m['text'].startswith('<external_codex')]
        prior=old.get(row['id'])
        if prior:
            known={(m['role'],m['text']) for m in row['messages']}
            recovered=0
            for message in prior['messages']:
                message={**message,'text':sanitized(message['text'])}
                if message['text'] and (message['role'],message['text']) not in known:
                    row['messages'].append(message);known.add((message['role'],message['text']));recovered+=1
            def moment(m):
                try:return dt.datetime.fromisoformat(m['timestamp'].replace('Z','+00:00')).timestamp()
                except ValueError:return 0
            row['messages'].sort(key=moment)
            row['history_source']+='; '+str(recovered)+' pesan dipertahankan dari arsip terverifikasi 12 Oktober'
    for remote in json.loads((BASE/'remote-reads.json').read_text(encoding='utf-8')):
        t=remote.get('thread',{})
        if not t.get('id'):
            continue
        assert not remote.get('page',{}).get('hasMore'), 'Remote pagination incomplete'
        messages=[]
        for turn in reversed(remote.get('turns',[])):
            for item in turn.get('items',[]):
                typ=item.get('type')
                if typ=='userMessage':
                    text='\n'.join(x.get('text','') for x in item.get('content',[]) if isinstance(x,dict)); role='user'
                elif typ=='agentMessage' and item.get('phase')!='commentary':
                    text=item.get('text',''); role='assistant'
                else: continue
                if text: messages.append({'role':role,'timestamp':helper.date(turn.get('startedAt') or t.get('createdAt',0)),'text':sanitized(text)})
        if not messages: continue
        rows.append({'id':t['id'],'title':t.get('title') or visible.get(t['id'],{}).get('title') or t['id'],'project':'Tanpa project (cloud)','cwd':t.get('cwd',''),'kind':t.get('kind','cloud'),'created_at':t.get('createdAt',0),'updated_at':t.get('updatedAt',0),'messages':messages,'patches':[],'errors':[],'history_source':'read_thread; seluruh pagination yang tersedia dibaca','rollout_path':'codex://threads/'+t['id'],'attachment_count':0})
    known_ids={r['id'] for r in rows}
    for ident,row in old.items():
        if ident not in known_ids:
            rows.append({**row,'history_source':'Arsip terdahulu; chat tidak muncul pada daftar aplikasi terbaru.'})
    supporting={}
    for path in (helper.CODEX/'memories/rollout_summaries').glob('*.md'):
        head=path.read_text(encoding='utf-8-sig',errors='replace')
        match=re.search(r'(?m)^thread_id:\s*('+helper.UUID+')',head[:1000])
        if match:
            supporting.setdefault(match[1],[]).append({'path':str(path),'text':sanitized(head)[:14000]})
    special={
        '01a12ec1-5987-7351-aacb-6faeca0c2918':('Verdant Vale RPG','D:/Laragon/www/game'),
        '01a13344-052e-77d1-a905-f721ea0fcb6b':('Codex MultiWorkspace','D:/Codex-MultiWorkspace'),
        '01a13861-b815-76c2-a096-8e7e7dc33da5':('Codex MultiWorkspace','D:/Codex-MultiWorkspace'),
        '01a12f1a-35fc-7700-84f8-a05514630319':('Jurassic Park Operation Genesis','D:/Games/JPOG'),
        '01a1145e-6892-7ba1-9397-e867625e99ad':('Arunika Map Studio','D:/Arunika Map Studio'),
        '01a1389a-72a3-71b2-863e-8ee69d321992':('Arunika Map Studio','D:/Arunika Map Studio'),
        '01a1057d-66ec-7431-a0c2-2ed92bb8b0b0':('ArunikaAI v4/Mini','D:/New AI Script AoE II DE')}
    for row in rows:
        ident=row['id']; row['previous']=previous.get(ident,[])
        row['cwd']=row.get('cwd') or ''
        row['supporting_memories']=supporting.get(ident,[])
        if ident in special:
            row['project'],row['project_root']=special[ident]
        elif row['cwd'].lower()=='d:\\laragon\\www\\laravel-13-cms-bum':
            row['project']='Laravel 13 CMS BUM'
            row['project_root']=row['cwd']
        elif row['project'] not in ('Tanpa project','Tanpa project (cloud)'):
            row['project_root']=row['cwd']
        for m in row['messages']:
            if m['role']!='assistant':continue
            for name in re.findall(r'(?:[EC]:[/\\][^\s\n<>\[\]()]*(?:Memories|notes)[/\\])([^\s\n<>\[\]()]+\.md)',m['text'],re.I):
                p=DESTS[0]/name
                if p.is_file() and str(p) not in {x['path'] for x in row['previous']}:
                    row['previous'].append({'path':str(p),'sha256':helper.sha(p),'evidence':[]})
        current=digest(row['messages']); row['evidence_sha256']=current
        prior=old.get(ident)
        same=bool(prior and {(m['role'],sanitized(m['text'])) for m in prior['messages']}=={(m['role'],m['text']) for m in row['messages']})
        exact=any(current in p['evidence'] or row.get('original_evidence') in p['evidence'] for p in row['previous'])
        row['action']='unchanged' if row['previous'] and (same or exact) else ('update' if row['previous'] else 'new')
        row['user_count']=sum(m['role']=='user' for m in row['messages'])
        row['final_count']=sum(m['role']=='assistant' for m in row['messages'])
    assert len(rows)==len({r['id'] for r in rows})
    dump('prepared.json',rows)
    report={'chats':len(rows),'actions':dict(collections.Counter(r['action'] for r in rows)),'projects':dict(collections.Counter(r['project'] for r in rows)),'needs_app_read':[{'id':r['id'],'title':r['title']} for r in rows if r.get('needs_app_read')],'messages':sum(len(r['messages']) for r in rows),'current_chat_separate':CURRENT}
    dump('plan.json',report)
    dump('review.json',[{'id':r['id'],'title':r['title'],'project':r['project'],'action':r['action'],'messages':len(r['messages']),'latest':next((m['text'][:1400] for m in reversed(r['messages']) if m['role']=='assistant'),'Belum ada laporan akhir'),'errors':r['errors']} for r in rows if r['action']!='unchanged'])
    print(json.dumps(report,ensure_ascii=False),flush=True)

def render(row):
    finals=[m for m in row['messages'] if m['role']=='assistant']
    users=[m for m in row['messages'] if m['role']=='user']
    newest=next((m for m in reversed(finals) if len(m['text'])>=300 and not re.match(r'(?i)^(?:sudah|saya sudah)\s+(?:saya\s+)?(?:simpan|disimpan)',m['text'])),finals[-1] if finals else None)
    lines=[l for m in finals for l in m['text'].splitlines()]
    selected=lambda pattern:helper.bullets([l for l in lines if re.search(pattern,l,re.I)],14,900)
    tail='\n\n'.join('### '+helper.date(m['timestamp'])+'\n\n'+m['text'][:1800]+('\n\n[Cuplikan; lihat riwayat sumber untuk laporan lengkap.]' if len(m['text'])>1800 else '') for m in finals[-4:] if m is not newest)
    body=['# '+row['title'],'','Tanggal penyimpanan: 20 Oktober 2026 (WIB)','Project: '+row['project'],'Project saat chat: '+row['cwd'],'Chat ID: '+row['id'],'Jenis sumber: '+row['kind'],'Rentang chat: '+helper.date(row['created_at'])+' sampai '+helper.date(row['updated_at']),'Tindakan: '+row['action'],'Evidence SHA-256: '+row['evidence_sha256'],'','## Tujuan dan konteks awal','',helper.bullets([m['text'] for m in users[:2]],2,1600),'','## Keputusan dan implementasi historis','','Laporan di bawah merupakan riwayat pekerjaan, bukan pengujian runtime terbaru pada tanggal penyimpanan. Checkpoint terbaru mengungguli pending dari laporan lebih lama.','',selected(r'keputusan|disepakati|implement|diubah|diperbaiki|ditambahkan|perubahan|selesai'),'','## Checkpoint substantif terakhir','',('Tanggal checkpoint: '+helper.date(newest['timestamp'])+'\n\n'+newest['text']) if newest else 'Tidak ada laporan akhir yang tersedia; pekerjaan belum dapat dinyatakan selesai.','','## File dibuat, dimodifikasi, atau dirujuk','',helper.bullets([p['operation']+': '+p['path'] for p in row['patches']],35,700),'',selected(r'\.(?:php|vue|mjs|js|tsx|ts|css|sql|md|xlsx|pdf|per|ai|json)\b'),'','## Backup historis','',selected(r'backup|\.bak_|cadangan|recovery branch'),'','## Pengujian dan verifikasi historis','',selected(r'test|assertion|lulus|passed|verifik|QC\b|QA\b|npm run build|syntax|lint'),'','## Masalah belum selesai dan langkah lanjutan','',helper.bullets([l for l in (newest['text'] if newest else '').splitlines() if re.search(r'belum|pending|batas|kendala|risiko|lanjut|perlu',l,re.I)],16,900),'','Untuk melanjutkan, verifikasi kembali Git/source/runtime aktif, gunakan Graphify sebagai peta teknis, dan backup sebelum perubahan. Jangan mengulangi mutasi database/deploy hanya berdasarkan arsip.','','## Laporan terbaru lain yang relevan','',tail or 'Tidak ada laporan tambahan.','','## Hubungan dengan memori sebelumnya','',helper.bullets([p['path'] for p in row['previous']],15,700),'','## Sumber dan keterbatasan','','- Riwayat: '+row['rollout_path'],'- Metode: '+row.get('history_source','Tidak tercatat'),'- Pesan pengguna: '+str(row['user_count'])+'; laporan akhir: '+str(row['final_count']),'- Kesalahan sumber: '+('; '.join(row['errors']) if row['errors'] else 'Tidak terdeteksi.'),'- Lampiran biner tidak diekspor; kredensial disamarkan.','- Memori lama dipertahankan; catatan ini menambahkan pembaruan per chat.','- Graphify diproses terpisah berdasarkan source aktif setelah arsip memori tersimpan.','- Verifikasi terbaru hanya mencakup integritas penyimpanan E/C; hasil uji aplikasi di atas bersifat historis.','']
    if row.get('supporting_memories'):
        body+=['## Memori pendukung dari sesi terdahulu','','Bagian ini adalah handoff historis tambahan, berguna ketika proyeksi pesan aplikasi terpotong. Hasil uji tetap mengikuti tanggal sesi sumber; bukan verifikasi runtime hari ini.','']
        for item in row['supporting_memories']:
            body+=['Sumber: '+item['path'],'',item['text'],'']
    return sanitized('\n'.join(body))+'\n'

def publish():
    rows=json.loads((BASE/'prepared.json').read_text(encoding='utf-8'))
    appfile=BASE/'app-fallbacks.json'
    if appfile.exists():
        app=json.loads(appfile.read_text(encoding='utf-8'))
        for row in rows:
            if row.get('needs_app_read'):
                row.update(app[row['id']]); row['evidence_sha256']=digest(row['messages'])
                row['user_count']=sum(m['role']=='user' for m in row['messages']);row['final_count']=sum(m['role']=='assistant' for m in row['messages'])
    assert all(r['messages'] or r['action']=='unchanged' for r in rows),'Unread chat requires fallback'
    preserved={p['path']:p['sha256'] for r in rows for p in r['previous']}
    manifest=[]
    for row in rows:
        if row['action']=='unchanged': continue
        slug=re.sub('[^a-z0-9]+','-',row['title'].lower()).strip('-')[:55] or 'chat'
        name=STAMP+'-delta-'+slug+'-'+row['id']+'.md'
        body=render(row)
        for target in [BASE/'staging-v2/E',BASE/'staging-v2/C']:
            helper.write_new(target/name,body)
        item={'id':row['id'],'title':row['title'],'project':row['project'],'action':row['action'],'name':name,'sha256':helper.sha(BASE/'staging-v2/E'/name)}
        assert item['sha256']==helper.sha(BASE/'staging-v2/C'/name)
        manifest.append(item)
    for item in manifest:
        for dest in DESTS:
            helper.write_new(dest/item['name'],(BASE/'staging-v2/E'/item['name']).read_text(encoding='utf-8'))
        assert all(helper.sha(dest/item['name'])==item['sha256'] for dest in DESTS)
    assert all(helper.sha(Path(p))==h for p,h in preserved.items()),'Existing memory changed'
    dump('manifest.json',manifest)
    report={'inventoried_chats':len(rows),'new_notes':sum(x['action']=='new' for x in manifest),'updated_notes':sum(x['action']=='update' for x in manifest),'unchanged_chats':sum(r['action']=='unchanged' for r in rows),'note_pairs_verified':len(manifest),'hash_mismatch':0,'old_memories_unchanged':len(preserved),'empty_notes':0,'projects':dict(collections.Counter(x['project'] for x in manifest)),'messages_read':sum(len(r['messages']) for r in rows),'application_runtime_retested':False,'artifacts':str(BASE),'destinations':[str(p) for p in DESTS]}
    dump('verification.json',report)
    print(json.dumps(report,ensure_ascii=False),flush=True)

assert 'sample-secret' not in sanitized('passwordnya ini sample-secret')
assert 'test123' not in sanitized('password: test123')
assert sanitized('Runtime belum diverifikasi.')=='Runtime belum diverifikasi.'
assert digest([])==digest([])
if __name__=='__main__':
    publish() if '--publish' in sys.argv else prepare()
