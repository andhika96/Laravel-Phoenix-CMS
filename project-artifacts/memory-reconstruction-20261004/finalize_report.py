import collections
import datetime as dt
import hashlib
import json
import re
from pathlib import Path

BASE = Path(__file__).parent
manifest = json.loads((BASE/'memory-manifest.json').read_text(encoding='utf-8'))
roots = [Path('D:/Laragon/www')/name for name in ('laravel-13-phoenix','laravel-13-cms-bum','laravel-13-cms-bum-next','laravel-13-phoenix-standalone')]
results = []
for root in roots:
    art = root/'project-artifacts/graphify-history-sync-20261004'
    result = json.loads((art/'verification.json').read_text(encoding='utf-8'))
    graph = json.loads((root/'graphify-out/graph.json').read_text(encoding='utf-8'))
    ids = {n['id'] for n in graph['nodes']}
    links = graph.get('links',graph.get('edges',[]))
    assert len(ids)==len(graph['nodes'])
    assert all(e['source'] in ids and e['target'] in ids for e in links)
    if result.get('graph_sha256'):
        assert hashlib.sha256((root/'graphify-out/graph.json').read_bytes()).hexdigest()==result['graph_sha256']
    result['evidence'] = str(art/'verification.json')
    result['graph'] = str(root/'graphify-out/graph.json')
    results.append(result)
pairs = manifest['files']+[dict(e=manifest['index_e'],c=manifest['index_c'])]
for pair in pairs:
    assert Path(pair['e']).read_bytes()==Path(pair['c']).read_bytes()
    assert Path(pair['c']).resolve().drive.upper()=='C:'
    assert Path(pair['e']).resolve().drive.upper()=='E:'
    text = Path(pair['e']).read_text(encoding='utf-8')
    if pair.get('id'):
        assert pair['id'] in text
        assert all(section in text for section in ('Tujuan dan konteks','Backup yang tercatat','Hasil pengujian','Masalah belum selesai','Graphify'))
projects = collections.Counter(x['project'] for x in manifest['files'])
stamp = dt.datetime.now(dt.timezone(dt.timedelta(hours=7))).strftime('%Y%m%d-%H%M%S')
body = f'''# Hasil Rekonstruksi Memori CMS dan Pembaruan Graphify

Tanggal: 4 Oktober 2026, WIB.
Workspace pelaksanaan: D:\\Laragon\\www\\laravel-13-phoenix.

## Tujuan dan konteks awal

Pengguna meminta membuat memori dari seluruh chat terdahulu yang melakukan update CMS, menyimpannya di E:\\AI\\Memories dan drive C, kemudian memperbarui Graphify setelah memori selesai. Scope mencakup riwayat CMS lokal yang dapat diakses, bukan seluruh akun ChatGPT cloud atau percakapan yang sudah dihapus.

## Keputusan penting

- Menginventarisasi database metadata chat lokal secara read-only, lalu membaca rollout yang berada pada project Laravel/CMS terkait.
- Daftar UI hanya memuat 50 chat terbaru; riwayat lokal dipakai untuk mencakup chat lama.
- Memeriksa 117 chat utama setelah chat subagent/guardian dipisahkan. Membuat 108 catatan CMS, 1 indeks, dan laporan akhir ini. Sembilan chat di luar perubahan CMS dicantumkan sebagai dikecualikan dalam indeks.
- Catatan pendukung/diagnosis dipisahkan secara eksplisit dari bukti implementasi. Satu chat tanpa laporan akhir tidak diklaim selesai.
- Memori lama dipertahankan. Semua catatan baru bersifat tambahan; klaim pengujian aplikasi dalam catatan tetap diberi label historis.
- Graph diperbarui lokal secara incremental dari source aktif. Tidak ada LLM/API eksternal, watcher, atau jadwal otomatis.
- Source Android CMS BUM yang terkait riwayat disertakan meskipun diabaikan Git. Dependency, Font Awesome vendored, .next, node_modules, vendor, cache, backup, histori source, secret, dan project-artifacts dikecualikan.
- File konfigurasi/stylesheet yang tidak memiliki AST diberi representation=file_metadata_only dan hash SHA-256. Ini tidak membuktikan perilaku runtime.

## Memori dan source yang dibaca

- E:\\AI\\Memories\\INDEX.md dan README.md: lokasi resmi, struktur, dan memori terdahulu.
- C:\\Users\\aruna\\.codex\\memories\\MEMORY.md: registry konteks CMS lintas chat.
- rollout_summaries/2026-09-23T06-14-51-vARB-laravel_cms_api_nextjs_lucent_phoenix_migration.md: konteks API additive, fallback Blade/Vue/Android, dan batas pengujian terisolasi.
- E:\\AI\\Memories\\20260801-015434-laravel13-phoenix-graphify-pagebuilder-ui-handoff.md dan 20260913-191348-laravel13-cms-bum-gis-import-geometry-backfill-handoff.md: alur graph manual, backup, dan batas source.
- Source verifikasi terarah: app/Support/Article/ArticleTemplateOptions.php, resources/views/article/templates/partials/classic-meta.blade.php, public/assets/js/gokut-route-worker.js pada CMS BUM, serta app/Support/CmsIdempotency.php pada CMS BUM NextJS.
- Konfigurasi .graphifyignore pada empat project bergraph, scripts/graphify/update-from-git.ps1, serta updater lokal terdahulu CMS BUM. Library Graphify yang terpasang diperiksa untuk API detect/extract/build_merge/export.

## File dibuat atau dimodifikasi

- 108 catatan per chat dan 1 indeks: lihat [{Path(manifest['index_e']).name}]({Path(manifest['index_e']).as_posix()}).
- Salinan drive C berada di C:\\Users\\aruna\\.codex\\memories\\extensions\\ad_hoc\\notes, dengan nama yang sama.
- Utilitas pelaksanaan dan bukti ada di project-artifacts/memory-reconstruction-20261004 pada Phoenix: collect_history.py, save_memories.py, plan_graphs.py, update_graph.py, finalize_report.py, history-evidence.json, memory-manifest.json, dan arsip final-reports/.
- Output graph yang diperbarui mencakup graph.json, manifest.json, metadata-manifest.json, GRAPH_REPORT.md, README.md, interpreter/root pointer, serta sidecar analysis/labels/extraction. Visualisasi CMS BUM dan NextJS diregenerasi dengan library offline yang sudah tersedia.
- Source aplikasi, database, stage Git, commit, dan remote tidak diubah.

## Distribusi catatan per project

| Project | Catatan |
|---|---:|
'''
for project,count in sorted(projects.items()):
    body += f'| {project} | {count} |\n'
body += '\n## Hasil Graphify dan backup\n\n| Project | Status | Node | Relasi |\n|---|---|---:|---:|\n'
for result in results:
    project = Path(result['root']).name
    status = 'Manifest/source tetap cocok; graph tidak perlu ditulis ulang' if result.get('status') else 'Diperbarui dan diverifikasi'
    body += f"| {project} | {status} | {result['nodes']} | {result['edges']} |\n"
body += '\nBackup dibuat dan hash diperiksa sebelum setiap penulisan graph. Backup awal yang penting:\n\n'
for root in roots[:3]:
    art = root/'project-artifacts/graphify-history-sync-20261004'
    for backup in sorted(art.glob('backup_*/graphify-out')):
        body += f'- `{backup}`\n'
body += '''
## Pengujian dan verifikasi terbaru

- 109 pasangan file memori awal (108 catatan + indeks) diverifikasi identik byte-for-byte; salinan E benar-benar berada di drive E dan salinan C di drive C. Laporan ini juga dibandingkan setelah penulisan.
- Struktur wajib seluruh catatan dan keberadaan Chat ID diperiksa.
- Empat graph lolos pemeriksaan ID unik dan seluruh endpoint relasi tersedia.
- Tiga graph yang diperbarui lolos pemeriksaan hash source, hash graph akhir, preservasi node source yang tidak terdampak, dan status Git produksi yang tetap sama.
- Query smoke test: Mosaic Classic/ArticleTemplateOptions/classic-meta, GOKUT snapping worker, dan CmsIdempotency/ResourceController/AuthController. Node aktual yang diharapkan ditemukan; query diberi budget kecil dan tidak dimaksudkan sebagai pemeriksaan semua relasi.
- Source hasil query dibuka untuk pemeriksaan terarah. Tidak menjalankan pengujian aplikasi/browser/database; hasil ini adalah verifikasi statis dan operasional tooling lokal.

## Masalah belum selesai dan keterbatasan

- NEWMG, Gen21AI CMS, dan Laravel 13 CRM belum memiliki graph dan folder project-artifacts. Pembuatan folder menunggu jawaban pengguna sesuai aturan eksplisit pengguna untuk project tanpa project-artifacts. Jangan membuat folder atau graph tersebut sebelum jawabannya diterima.
- Folder historis D:\\Laragon\\www\\laravel-12-phoenix dan D:\\Laragon\\www\\laravel-11-cms-bum tidak tersedia. Memorinya tetap disimpan; graph baru untuk folder backup tidak dibuat.
- Rangkuman adalah rekonstruksi hasil akhir bertanggal, bukan pembuktian bahwa seluruh fitur masih bekerja sekarang. Riwayat yang hanya memuat saran, rencana, atau diagnosis tidak otomatis berarti tindakan telah dilakukan.
- Parser Graphify memiliki keterbatasan atribusi untuk sejumlah referensi Blade bersama. INFERRED masih memerlukan pemeriksaan source dan tidak membuktikan runtime. Tidak mengubah source untuk mengatasi keterbatasan parser.
- Beberapa JSON konfigurasi hanya memiliki metadata file/hash karena extractor tidak menghasilkan entity. Relasi semantik JSON belum dipetakan penuh.
- Graph source lebih tepat untuk penelusuran teknis; keputusan bisnis, alasan implementasi, dan status pending tetap dibaca dari memori/kronologi chat.

## Langkah lanjutan untuk chat berikutnya

1. Baca indeks CMS baru dan catatan fitur yang relevan; periksa checkpoint paling akhir agar tidak memakai status pending lama.
2. Jika pengguna menyetujui folder artefak, buat folder NEWMG/Gen21AI/CRM yang disetujui, tentukan scope dari catatan perubahan, lalu bangun graph lokal terbatas dengan backup dan QC.
3. Sebelum melanjutkan feature work, periksa Git/source/runtime terbaru. Jangan mengulang Save, import, atau kalkulasi database untuk sekadar menguji riwayat.
4. Update Graphify selanjutnya tetap manual dan menunggu perintah pengguna.
'''
report_name = f'{stamp}-cms-memory-graph-sync-results.md'
dest_e = Path('E:/AI/Memories')/report_name
dest_c = Path('C:/Users/aruna/.codex/memories/extensions/ad_hoc/notes')/report_name
for destination in (dest_e,dest_c):
    assert not destination.exists()
    destination.write_text(body,encoding='utf-8')
assert dest_e.read_bytes()==dest_c.read_bytes()
verification = {'examined_chats':117,'cms_notes':108,'excluded_chats':9,'memory_files_per_drive':110,'verified_memory_pairs':110,'notes_by_project':dict(projects),'index_e':manifest['index_e'],'index_c':manifest['index_c'],'report_e':str(dest_e),'report_c':str(dest_c),'graphs':results,'pending_graph_projects':['newmg','laravel-13-gen21ai-cms','laravel-13-crm'],'unavailable_historical_projects':['laravel-12-phoenix','laravel-11-cms-bum']}
(BASE/'final-verification.json').write_text(json.dumps(verification,ensure_ascii=False,indent=2),encoding='utf-8')
print(json.dumps({'memory_pairs_verified':110,'index_e':manifest['index_e'],'report_e':str(dest_e),'report_c':str(dest_c),'graphs':[{k:r[k] for k in ('root','nodes','edges')} for r in results]},ensure_ascii=False))
