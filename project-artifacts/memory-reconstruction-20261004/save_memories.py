"""Create additive, traceable CMS memory notes without changing old memories."""
import collections
import datetime as dt
import hashlib
import json
import re
import sqlite3
from pathlib import Path

BASE = Path(__file__).parent
CODEX = Path('C:/Users/aruna/.codex')
DEST_E = Path('E:/AI/Memories')
DEST_C = CODEX / 'memories/extensions/ad_hoc/notes'
TZ = dt.timezone(dt.timedelta(hours=7))
STAMP = dt.datetime.now(TZ).strftime('%Y%m%d-%H%M%S')
conn = sqlite3.connect(f'file:{CODEX.as_posix()}/state_5.sqlite?mode=ro', uri=True)
main_ids = {r[0] for r in conn.execute("SELECT id FROM threads WHERE thread_source='user' OR source='vscode'")}
all_rows = json.loads((BASE / 'history-evidence.json').read_text(encoding='utf-8'))
rows = [r for r in all_rows if r['id'] in main_ids]
# Clearly unrelated exchanges are inventoried, but not turned into CMS implementation history.
exclude = {
    '019e5518-8a5f-7361-b98d-d28fc31eb25b': 'Unduh arsip memori; bukan perubahan CMS',
    '019e5579-efe3-7701-8133-066b51671797': 'Sinkronisasi memori; bukan perubahan CMS',
    '019e64ca-df63-7ec2-a4a8-d33a1e0b0313': 'Sinkronisasi memori; bukan perubahan CMS',
    '019e7731-ec09-7e90-bce2-6b6f6762c133': 'Instruksi kerja; bukan perubahan CMS',
    '019fa905-bdcd-7f92-95e7-f13299d14a3d': 'Ekstraksi Excel varian mobil; bukan perubahan CMS',
    '01a0ad34-04e5-7e53-96da-b1b4b690453c': 'Saran koneksi internet; bukan perubahan CMS',
    '01a0b4ca-3c5d-7863-88b4-c70c12ce3cce': 'Validasi NPWP Google Cloud; bukan perubahan CMS',
    '01a0cb6f-39eb-7083-b3e7-ca149e24b0bd': 'Perbandingan model AI; bukan perubahan CMS',
    '01a0ad4c-441c-78c1-a0be-e8964110b92f': 'Dokumen MKTI; belum ada implementasi CMS',
}
included = [r for r in rows if r['id'] not in exclude]
dest_files = []

def clean(s):
    s = re.sub(r'<oai-mem-citation>.*?</oai-mem-citation>', '', s, flags=re.S)
    s = re.sub(r'(?im)^(.*(?:password|passwd|api[_ -]?key|secret|access[_ -]?token)\s*[:=]\s*).+$', r'\1[REDACTED]', s)
    s = re.sub(r'(?i)(Bearer\s+)[A-Za-z0-9_.-]{12,}', r'\1[REDACTED]', s)
    return s.strip()

def date(epoch):
    return dt.datetime.fromtimestamp(epoch, TZ).strftime('%Y-%m-%d %H:%M WIB')

def list_lines(lines, limit=40):
    lines = list(dict.fromkeys(clean(x) for x in lines if clean(x)))
    if not lines:
        return '- Tidak tercatat eksplisit dalam laporan akhir yang tersedia.'
    result = '\n'.join('- ' + x[:550] for x in lines[:limit])
    if len(lines) > limit:
        result += f'\n- Ada {len(lines)-limit} rujukan tambahan; lihat arsip laporan akhir.'
    return result

def safe_path(s):
    return not re.search(r'(?i)(\.bak|backup|node_modules|vendor/|\.env|credential|secret)', s)

for row in included:
    reports = [clean(f['text']) for f in row['finals']]
    report_text = '\n\n'.join(reports)
    lines = report_text.splitlines()
    filenames = [p.strip().replace('\\', '/') for p in row['patch_files'] if safe_path(p)]
    # Add concrete file references from final reports, not inferred file names.
    file_refs = re.findall(r'(?:[A-Za-z]:[/\\])?[^\s`<>"()\[\]]+\.(?:php|blade\.php|vue|js|mjs|css|json|sql|md|tsx|ts|yml|yaml)', report_text)
    filenames += [p.rstrip(':.,').replace('\\', '/') for p in file_refs if safe_path(p) and ('/' in p or '\\' in p)]
    filenames = sorted(set(p for p in filenames if len(p) < 250))
    checks = [line for line in lines if re.search(r'(?i)(\btest|phpunit|assertion|lulus|passed|npm run build|syntax|lint|\bverifik|\bQC\b|\bQA\b)', line)]
    pending = [line for line in lines if re.search(r'(?i)(belum|pending|terkendala|dihentikan|tidak.*diverifikasi|tidak.*terverifikasi|perlu.*(?:uji|verifik)|lanjut)', line)]
    backup_lines = [line for line in lines if re.search(r'(?i)(backup|\.bak_|manifest.*(?:sha|hash))', line)]
    changes = [line for line in lines if re.search(r'(?i)(sudah.*(?:ubah|perbaik|tambah|terap|selesai)|perubahan|implementasi|diterapkan|ditambahkan|diubah|diperbaiki|diselesaikan|berhasil.*(?:pasang|sinkron|pulih)|cleanup|migration|migrasi)', line)]
    nonprod = all('project-artifacts' in f or 'graphify' in f or '/tests/' in f or f.startswith('tests/') for f in row['patch_files'])
    if row['patch_files'] and not nonprod:
        status = 'Ada perubahan source yang tercatat pada panggilan patch historis; keberhasilan dan batas verifikasi mengikuti laporan bertanggal di bawah.'
        category = 'perubahan-source-tercatat'
    elif changes:
        status = 'Ada laporan perubahan/artefak/operasional dalam chat; jangan menganggap seluruh permintaan atau saran di chat sudah dieksekusi.'
        category = 'perubahan-atau-operasional-dilaporkan'
    else:
        status = 'Konteks pendukung/diagnosis; perubahan produksi belum terbukti dari laporan yang tersedia.'
        category = 'pendukung-atau-belum-terbukti'
    if not reports:
        status = 'Tidak ada laporan akhir; jangan mengklaim pekerjaan selesai. Periksa ulang source dan riwayat tool jika melanjutkan.'
        category = 'tanpa-laporan-akhir'
    slug = re.sub(r'[^a-z0-9]+', '-', row['title'].lower()).strip('-')[:65] or 'cms-history'
    name = f"{STAMP}-{row['project']}-{slug}-{row['id'][:8]}.md"
    archive = BASE / 'final-reports' / f"{row['id']}.md"
    archive.parent.mkdir(parents=True, exist_ok=True)
    archive.write_text('# Arsip laporan akhir historis\n\n' + '\n\n---\n\n'.join(f"## {f['timestamp']}\n\n{clean(f['text'])}" for f in row['finals']), encoding='utf-8')
    goals = [clean(g) for g in row['goals'] if not g.startswith(('<', '# Skill'))]
    chosen_goals = list(dict.fromkeys(goals[:2] + goals[-2:]))
    timeline = []
    for f in row['finals']:
        text = clean(f['text'])
        # A dated extract keeps historical claims and later corrections distinguishable.
        paragraphs = [p for p in text.split('\n\n') if p.strip() and not p.lstrip().startswith(('# ', '## ', '### ', '<'))]
        first = paragraphs[0] if paragraphs else text
        selected = [line for line in text.splitlines() if re.search(r'(?i)(sudah.*(?:ubah|perbaik|tambah|terap)|belum.*(?:uji|selesai|verifik)|\d+.*(?:tests|tes|assertions)|pending)', line)]
        excerpt = first[:650]
        if selected:
            excerpt += '\n' + '\n'.join(dict.fromkeys(selected[:2]))[:450]
        timeline.append(f"### {f['timestamp']}\n\n{excerpt}")
    previous = []
    topic_words = set(re.findall(r'[a-z0-9]+', row['title'].lower())) - {'lanjutkan', 'perbaiki', 'tinjau', 'update', 'pengembangan', 'chatgpt', 'bisa', 'kamu', 'saya', 'memori'}
    for p in DEST_E.glob('*.md'):
        if p.name.startswith(STAMP):
            continue
        if any(w in p.stem.lower() for w in topic_words if len(w) >= 5):
            previous.append(str(p))
    body = f"""# {row['title']} — {row['project']}

Tanggal penyimpanan: {date(dt.datetime.now(TZ).timestamp())}
Rentang chat: {date(row['created_at'])} sampai {date(row['updated_at'])}
Project saat chat: `{row['cwd']}`
Chat ID: `{row['id']}`
Kategori: `{category}`

## Tujuan dan konteks awal

{list_lines(chosen_goals, 4)}

## Status dan keputusan penting

{status}

Catatan ini merekonstruksi riwayat dari laporan akhir dan rujukan tool lokal. Hasil uji di bawah adalah hasil historis, bukan pengujian ulang pada 4 Oktober 2026. Source aktif harus dicek ulang sebelum perubahan lanjutan. Kutipan laporan dapat memuat rencana/saran; hanya tindakan yang eksplisit dilaporkan telah dilaksanakan boleh dianggap checkpoint implementasi.

{list_lines(changes, 18)}

## File dibuat, dimodifikasi, atau dirujuk

Daftar ini berasal dari patch dan laporan akhir historis; bukan daftar file yang diubah dalam sesi penyimpanan memori ini. Rujukan dapat mencakup file yang hanya dibaca atau diuji.

{list_lines(filenames, 60)}

## Backup yang tercatat

{list_lines(backup_lines, 12)}

## Hasil pengujian dan verifikasi historis

{list_lines(checks, 25)}

## Masalah belum selesai dan langkah lanjutan

Daftar berikut memuat keterbatasan pada checkpoint masing-masing, termasuk yang mungkin diperbaiki pada checkpoint lebih baru. Baca kronologi agar tidak menghidupkan kembali status pending yang sudah selesai.

{list_lines(pending, 25)}

Langkah lanjut: baca laporan paling akhir yang relevan, periksa status Git dan source aktif, query Graphify sesuai fitur, lalu backup sebelum perubahan. Jangan mengulangi mutasi database atau Save UI hanya untuk memverifikasi riwayat.

## Kronologi ringkas hasil akhir

{chr(10).join(timeline) if timeline else 'Tidak ada laporan akhir yang tersedia.'}

## Sumber dan memori terdahulu

- Riwayat lokal: `{row['rollout_path']}`
- Arsip laporan lengkap, tanpa reasoning atau output tool mentah: [{archive.name}]({archive.as_posix()})
- Rujukan awal memori: `E:\\AI\\Memories\\INDEX.md`, `E:\\AI\\Memories\\README.md`, `C:\\Users\\aruna\\.codex\\memories\\MEMORY.md`.
- Berkas memori yang namanya terkait topik (hanya penunjuk; isinya tidak dibaca ulang seluruhnya):
{list_lines(previous[:8], 8)}

## Graphify

Penyimpanan catatan ini belum memperbarui graph. Pembaruan graph dikerjakan setelah seluruh memori tersimpan, berdasarkan source aktif, bukan berdasarkan klaim percakapan saja.
"""
    for dest in (DEST_E, DEST_C):
        dest.mkdir(parents=True, exist_ok=True)
        out = dest / name
        if out.exists():
            raise FileExistsError(out)
        out.write_text(body, encoding='utf-8')
    assert hashlib.sha256((DEST_E / name).read_bytes()).digest() == hashlib.sha256((DEST_C / name).read_bytes()).digest()
    dest_files.append({'id': row['id'], 'title': row['title'], 'project': row['project'], 'cwd': row['cwd'], 'category': category, 'e': str(DEST_E / name), 'c': str(DEST_C / name), 'sha256': hashlib.sha256(body.encode('utf-8')).hexdigest(), 'finals': len(row['finals'])})

index_name = f'{STAMP}-cms-history-index.md'
index = ['# Indeks Rekonstruksi Memori Perubahan CMS', '', f'Tanggal: {date(dt.datetime.now(TZ).timestamp())}', '', f'Diperiksa: {len(rows)} chat utama lokal. Dibuat: {len(dest_files)} catatan CMS. Dikecualikan: {len(exclude)} chat di luar perubahan CMS. Chat subagent/guardian tidak dihitung sebagai chat utama.', '', 'Cakupan mencakup riwayat lokal Codex yang tersedia; tidak mengklaim seluruh percakapan ChatGPT cloud atau chat yang sudah dihapus.', '', 'Seluruh catatan baru bersifat tambahan. Memori lama, source aplikasi, database, dan Git tidak diubah oleh proses penyimpanan. Hash SHA-256 salinan E dan C diverifikasi identik.', '', '## Catatan per project', '']
for project, notes in collections.defaultdict(list).items():
    pass
for project in sorted({n['project'] for n in dest_files}):
    index += [f'### {project}', '']
    for note in [n for n in dest_files if n['project'] == project]:
        index.append(f"- [{note['title']}]({note['e'].replace(chr(92), '/')}) — `{note['id']}`; {note['category']}; {note['finals']} laporan akhir.")
    index.append('')
index += ['## Chat di luar scope', '']
for row in rows:
    if row['id'] in exclude:
        index.append(f"- {row['title']} (`{row['id']}`): {exclude[row['id']]}")
index += ['', '## Keterbatasan dan langkah berikut', '', '- Teks hasil akhir adalah bukti pelaporan historis. Pengujian aplikasi tidak dijalankan ulang dalam proses ini.', '- Daftar file dalam catatan menggabungkan file patch dan rujukan laporan; tidak semuanya pasti dimodifikasi.', '- Memori berbasis judul bukan sumber kondisi terbaru. Lihat kronologi dan koreksi terakhir.', '- Graphify diperbarui setelah catatan E/C selesai, dengan backup dan pengecualian artefak/backup/secret.', f'- Manifest validasi lokal: `{BASE / "memory-manifest.json"}`.', '']
index_body = '\n'.join(index)
for dest in (DEST_E, DEST_C):
    (dest / index_name).write_text(index_body, encoding='utf-8')
assert (DEST_E / index_name).read_bytes() == (DEST_C / index_name).read_bytes()
manifest = {'saved_at': date(dt.datetime.now(TZ).timestamp()), 'stamp': STAMP, 'examined_main_chats': len(rows), 'excluded': exclude, 'files': dest_files, 'index_e': str(DEST_E / index_name), 'index_c': str(DEST_C / index_name)}
(BASE / 'memory-manifest.json').write_text(json.dumps(manifest, ensure_ascii=False, indent=2), encoding='utf-8')
print(json.dumps({'examined': len(rows), 'saved_notes': len(dest_files), 'projects': dict(collections.Counter(n['project'] for n in dest_files)), 'categories': dict(collections.Counter(n['category'] for n in dest_files)), 'index_e': manifest['index_e'], 'index_c': manifest['index_c'], 'max_note_chars': max(len(Path(n['e']).read_text(encoding='utf-8')) for n in dest_files)}, ensure_ascii=False))
