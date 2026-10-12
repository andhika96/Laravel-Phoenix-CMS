"""Write a compact delivery report; graphs remain in their original projects."""
import json
from pathlib import Path
BASE=Path(__file__).parent
OUT=Path('C:/Users/aruna/Documents/Codex/2026-10-12/ch/outputs')
OUT.mkdir(exist_ok=True)
data=json.loads((BASE/'final-qc.json').read_text(encoding='utf-8'))
inventory=json.loads((BASE/'inventory.json').read_text(encoding='utf-8'))
assert len(data['results'])==7 and not data['errors'] and all(r['ok'] for r in data['results'])
cleanup=json.loads((BASE/'cleanup-incomplete.json').read_text(encoding='utf-8'))
assert cleanup['removed'] and cleanup['root_preserved'] and cleanup['git_preserved']
lines=['LAPORAN PEMBARUAN GRAPHIFY PER PROJECT','Tanggal: 12 Oktober 2026 (Asia/Jakarta)','',
'Pembaruan berhasil pada 7 lokasi source yang masih tersedia dan dapat ditulis. Graph lama diperbarui secara incremental; graph yang belum ada dibuat dari source aktif. Root project yang sudah dihapus tidak dibuat kembali.',
'',
'HASIL PER PROJECT','']
for result in data['results']:
    row=next(r for r in inventory if r['label']==result['label'])
    kind='Incremental' if row['graph_exists'] else 'Graph baru'
    lines += [result['label']+' — '+kind,
        '  Source: '+result['root'],
        '  Graph: '+str(Path(result['root'])/'graphify-out/graph.json'),
        '  Visualisasi: '+str(Path(result['root'])/'graphify-out/graph.html'),
        '  Report: '+str(Path(result['root'])/'graphify-out/GRAPH_REPORT.md'),
        '  Node: '+format(result['nodes'],',')+'; relasi: '+format(result['edges'],','),
        '  Source terwakili: '+str(result['represented_files'])+'; file kode: '+str(result['code_files'])+'; style/config/markup: '+str(result['metadata_files']),
        '  File kode yang hanya diwakili metadata: '+str(len(result['code_files_metadata_only'])),
        '  QC dan backup: '+str(Path(row['artifacts'])),
        '']
    before=json.loads((Path(row['artifacts'])/'before-git.json').read_text(encoding='utf-8'))
    result['git_check_applicable']=before is not None
    if before is None:result['head_and_staged_content_preserved']=None
lines += ['TOTAL',
'  Node: '+format(sum(r['nodes'] for r in data['results']),','),
'  Relasi: '+format(sum(r['edges'] for r in data['results']),','),
'',
'PERUBAHAN UTAMA',
'  Phoenix: 20 file kode berubah diperbarui. 134 sumber artefak/historis/tidak tersedia dikeluarkan dari graph, mencakup 4.938 node. Node source aktif yang tidak terdampak tetap terpelihara.',
'  CMS BUM: hash 1.844 file kode sudah cocok dengan graph terdahulu. Source code tidak diekstrak ulang; metadata 39 file style/config/markup diperbarui.',
'  Standalone: source kode tidak berubah; metadata dan satu rujukan source yang tidak sesuai cakupan diperbarui.',
'  New MG Laptop, helper scenario AoE, script AI v4/Mini, dan Arunika Map Studio: graph pertama dibuat dari source yang masih tersedia.',
'  PER AoE memakai ekstraksi struktur lokal: loader, konstanta, defrule, operator kondisi/aksi, dan rujukan simbol. Identitas namespace v4 dan Mini dipertahankan tanpa penggabungan fuzzy. 25 file paket D cocok dengan source utama C.',
'',
'PROJECT YANG DILEWATI','']
for row in data['skipped']:
    reason={
        'source-root-unavailable':'Folder source tidak tersedia; sesuai instruksi pengguna, dilewati tanpa dibuat ulang.',
        'no-active-source-files':'Folder hanya menyisakan metadata repository, tanpa source aplikasi. Artefak parsial proses ini sudah dibersihkan; folder root dan .git dipertahankan.',
        'artifacts-unavailable':'Pembuatan folder artefak gagal. Untuk New MG SSD Portable, percobaan terakhir mengembalikan Windows error 1392: file or directory is corrupted and unreadable.'
    }.get(row['status'],row['status'])
    lines += ['  '+row['label'],'    '+row['root'],'    '+reason]
lines += ['',
'VERIFIKASI TERBARU',
'  Semua 7 graph lulus QC independen.',
'  ID duplikat: 0. Endpoint relasi hilang: 0. Self-loop: 0. Edge collapse terdeteksi pada graph terbit: 0. Hash source/manifest mismatch: 0.',
'  Source aktif yang dipindai tetap memiliki SHA-256 yang sama dengan snapshot sebelum pembaruan.',
'  Seluruh backup graph yang dibuat cocok dengan SHA-256 manifest. Node source yang tidak terdampak tetap ada.',
'  Graph dapat dimuat ulang tanpa perubahan jumlah node/relasi. Uji query graph pada Phoenix, New MG, AoE, dan Map Studio berhasil menelusuri node source.',
'  Pada repository dengan HEAD yang tersedia, HEAD, isi staged diff, dan status perubahan aplikasi tetap sama. Tidak ada commit/push/checkout/reset.',
'  Script visualisasi memakai vis-network lokal. Tidak ada script CDN yang dijalankan bersama data graph. Graph besar memakai tampilan agregat komunitas.',
'',
'BACKUP DAN ARTEFAK',
'  Artefak setiap project berada di <root>/project-artifacts/graphify-all-projects-20261012.',
'  Backup output lama berada di backup_YYYYMMDD_HHMMSS/graphify-out dengan backup-manifest.json.',
'  Source aplikasi tidak dimodifikasi. Script updater/QC yang disempurnakan memiliki backup .bak bertimestamp.',
'  Script bersama, inventory, hasil QC, dan catatan cleanup berada di '+str(BASE),
'',
'MEMORI DAN SOURCE YANG DIPAKAI',
'  Indeks resmi: E:/AI/Memories/20261012-all-projects-index.md',
'  Rangkuman chat AoE v4 dan Arunika Map Studio bertanggal 12 Oktober; corpus prepared.json/riwayat patch digunakan untuk menguji rujukan file aktif.',
'  Panduan C:/Users/aruna/.codex/memories/MEMORY.md dan script Graphify 4 serta 8 Oktober menjadi konteks prosedur; kondisi terbaru diverifikasi lewat source/hash, bukan klaim historis.',
'  Contoh source yang dibuka langsung: ArticleTemplateOptions.php; engine.py Map Studio; build_preview3_scenario.py; loader ArunikaAI_v4.per dan 07-defense-communication.per.',
'',
'BATAS VERIFIKASI',
'  Ini verifikasi statis graph, source, backup, dan Git. Pengujian runtime aplikasi, database bisnis, deployment, browser CMS, dan gameplay AoE tidak dijalankan ulang.',
'  INFERRED/AMBIGUOUS tetap harus diperiksa pada source saat dipakai untuk perubahan kode. Referensi PER bersifat sintaksis; tidak membuktikan perilaku engine/game.',
'  Sebagian JSON serta stylesheet/markup hanya diwakili metadata file/hash. Keberadaan node file tidak berarti seluruh isi konfigurasi telah dianalisis secara semantik.',
'  New MG SSD Portable belum diperbarui karena folder artefaknya gagal ditulis. Tidak menjalankan perbaikan filesystem atau mengubah source pada drive E.',
'  Graphify diperbarui atas perintah manual ini; tidak menambah watcher, schedule, atau hook otomatis.',
'']
data['cleanup']=cleanup
for name,content in [('Laporan_Update_Graphify_20261012.txt','\n'.join(lines)),('Verifikasi_Graphify_Semua_Project_20261012.json',json.dumps(data,ensure_ascii=False,indent=2))]:
    path=OUT/name
    assert not path.exists(),path
    path.write_text(content,encoding='utf-8')
    assert path.stat().st_size>0
print(json.dumps({'outputs':str(OUT),'verified_projects':7,'skipped_projects':len(data['skipped']),'incomplete_artifact_removed':cleanup['target']},ensure_ascii=False))
