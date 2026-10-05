"""Read local CMS chat history, retaining human goals and final reports only."""
import collections
import json
import re
import sqlite3
from pathlib import Path

BASE = Path(__file__).parent
CODEX = Path('C:/Users/aruna/.codex')
CURRENT = '01a1057d-cb49-79d2-a5fb-1d7fbeebfcaf'
conn = sqlite3.connect(f'file:{CODEX.as_posix()}/state_5.sqlite?mode=ro', uri=True)
conn.row_factory = sqlite3.Row
titles = {}
for line in (CODEX / 'session_index.jsonl').read_text(encoding='utf-8').splitlines():
    row = json.loads(line)
    titles[row['id']] = row.get('thread_name', '')
rows = [dict(r) for r in conn.execute(
    "SELECT id,title,cwd,rollout_path,created_at,updated_at FROM threads "
    "WHERE cwd LIKE '%Laragon%' AND title NOT LIKE 'Guardian review%' "
    "AND (agent_path IS NULL OR agent_path IN ('','/root')) ORDER BY created_at")]
records = []
for row in rows:
    if row['id'] == CURRENT or row['id'] == '01a1057f-a966-72d2-8a6e-a21319914fe1':
        continue
    row['cwd'] = row['cwd'].removeprefix('\\\\?\\')
    row['project'] = Path(row['cwd']).name
    row['title'] = titles.get(row['id']) or row['title'].splitlines()[-1][:140]
    row['finals'] = []
    row['goals'] = []
    row['patch_files'] = []
    row['errors'] = []
    p = Path(row['rollout_path'].removeprefix('\\\\?\\'))
    if not p.exists():
        row['errors'].append('Rollout tidak ditemukan')
        records.append(row)
        continue
    for line in p.open(encoding='utf-8'):
        try:
            event = json.loads(line)
        except json.JSONDecodeError:
            row['errors'].append('Baris JSON tidak valid')
            continue
        payload = event.get('payload', {})
        if event.get('type') == 'event_msg' and payload.get('type') == 'task_complete':
            msg = payload.get('last_agent_message', '')
            if msg:
                row['finals'].append({'timestamp': event.get('timestamp'), 'text': msg})
        if event.get('type') != 'response_item':
            continue
        if payload.get('type') == 'message':
            msg = '\n'.join(c.get('text', '') for c in payload.get('content', []) if isinstance(c, dict))
            if payload.get('role') == 'assistant' and payload.get('channel') == 'final' and msg:
                row['finals'].append({'timestamp': event.get('timestamp'), 'text': msg})
            elif payload.get('role') == 'user' and msg and not msg.lstrip().startswith(('<environment_context>', '# AGENTS.md', '<INSTRUCTIONS>', '<permissions', '<external_codex', '<user_instructions>')):
                if '## My request:' in msg:
                    msg = msg.split('## My request:', 1)[1]
                row['goals'].append(msg.strip())
        if payload.get('type') in ('function_call', 'custom_tool_call'):
            args = payload.get('arguments', payload.get('input', ''))
            if not isinstance(args, str):
                args = json.dumps(args)
            args = args.replace('\\n', '\n').replace('\\r', '')
            row['patch_files'].extend(re.findall(r'\*\*\* (?:Update|Add|Delete) File: ([^\r\n";]+)', args))
    row['finals'] = list({f['text']: f for f in row['finals']}.values())
    row['goals'] = list(dict.fromkeys(row['goals']))
    row['patch_files'] = sorted(set(row['patch_files']))
    records.append(row)
(BASE / 'history-evidence.json').write_text(json.dumps(records, ensure_ascii=False, indent=2), encoding='utf-8')
print(json.dumps({'chats': len(records), 'projects': dict(collections.Counter(r['project'] for r in records)), 'no_final': [r['id'] for r in records if not r['finals']], 'errors': [(r['id'], r['errors']) for r in records if r['errors']]}, ensure_ascii=False))
