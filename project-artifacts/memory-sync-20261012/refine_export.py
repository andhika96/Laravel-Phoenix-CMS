import datetime as dt
import shutil
import re
from pathlib import Path

base = Path(__file__).parent
source_path = base / 'export_memories.py'
src = source_path.read_text(encoding='utf-8')
assert "def prepare():" in src and "def export():" in src
stamp = dt.datetime.now().strftime('%Y%m%d_%H%M%S')
for name in ['export_memories.py','prepared.json','plan.json','review.json']:
    path = base / name
    if path.exists():
        shutil.copy2(path, Path(str(path)+'.bak_'+stamp+'_refine-extraction'))
src = src.replace('import sys\n', 'import sys\nfrom urllib.parse import unquote\n')
old = """                if text.lstrip().startswith(('<environment_context>', '# AGENTS.md', '<INSTRUCTIONS>', '<permissions', '<external_codex', '<user_instructions>', '<turn_aborted>')):
                    continue"""
new = """                text = re.sub(r'<recommended_plugins>.*?</recommended_plugins>', '', text, flags=re.S).strip()
                if '# AGENTS.md' in text or '<INSTRUCTIONS>' in text or text.lstrip().startswith(('<environment_context>', '<permissions', '<external_codex', '<user_instructions>', '<turn_aborted>')):
                    continue"""
assert old in src
src = src.replace(old, new)
old = "        row['previous'] = old.get(row['id'], [])"
new = r"""        previous = set(old.get(row['id'], []))
        for message in row['messages']:
            if message['role'] != 'assistant':
                continue
            for match in re.finditer(r'E:[/\\]+AI[/\\]+Memories[/\\]+[^\r\n' + chr(96) + r'"<>]+?\.md\b', message['text'], re.I):
                candidate = Path(unquote(match[0]).replace('\\', '/'))
                if candidate.is_file() and 'index' not in candidate.name.lower() and candidate.name.lower() not in ('memory.md', 'memory_summary.md', 'readme.md'):
                    previous.add(str(candidate))
        row['previous'] = sorted(previous)
        if row['project'] == 'Tanpa project':
            topic = row['title'] + '\n' + '\n'.join(m['text'][:1500] for m in row['messages'] if m['role']=='user')
            if re.search(r'AoE2? ?II?|Age of Empire|ArunikaAI', topic, re.I):
                row['project'] = 'Age of Empire II DE'
                row['project_attribution'] = 'Topik eksplisit pada chat tanpa project; cwd asli dipertahankan.'
            elif re.search(r'newmg|MG ?Motor', row['title'], re.I):
                row['project'] = 'New MG (chat tanpa project)'
                row['project_attribution'] = 'Topik eksplisit; lokasi laptop dan SSD tidak disamakan.'
        row.setdefault('project_attribution', 'Metadata cwd saat chat.')"""
assert old in src
src = src.replace(old, new)
old = "        newest = finals[-1]['text'] if finals else 'Tidak ada laporan akhir dalam riwayat. Pekerjaan tidak dapat dinyatakan selesai.'"
new = r"""        checkpoint = next((m for m in reversed(finals) if len(m['text']) >= 350 and not re.match(r'(?i)^(?:sudah|saya sudah)\s+(?:saya\s+)?(?:simpan|disimpan)', m['text'])), finals[-1] if finals else None)
        newest = checkpoint['text'] if checkpoint else 'Tidak ada laporan akhir dalam riwayat. Pekerjaan tidak dapat dinyatakan selesai.'"""
assert old in src
src = src.replace(old, new)
src = src.replace("'Project: ' + row['project'], 'Project saat chat: ' + row['cwd'],", "'Project: ' + row['project'], 'Dasar pengelompokan: ' + row['project_attribution'], 'Project saat chat: ' + row['cwd'],")
src = src.replace("'## Checkpoint laporan paling akhir', '',", "'## Checkpoint substantif terakhir', '',\n            'Tanggal checkpoint: ' + (date(checkpoint['timestamp']) if checkpoint else 'Tidak tersedia'), '',")
src = src.replace("Deteksi memori terdahulu memakai ID sumber eksplisit; memori lama yang hanya memakai judul mungkin belum terhubung otomatis.", "Deteksi memori terdahulu memakai ID sumber eksplisit serta path memori pada laporan akhir yang masih ada. Memori lama yang hanya memakai judul mungkin belum terhubung otomatis.")
compile(src, str(source_path), 'exec')
source_path.write_text(src, encoding='utf-8')
print('Script backed up, refined, and syntax checked.')
