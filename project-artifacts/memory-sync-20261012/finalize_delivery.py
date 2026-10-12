"""Create user-facing copies of the verified index and storage report."""
import datetime as dt
import hashlib
import json
import re
import shutil
from pathlib import Path
BASE=Path(__file__).parent
OUT=Path('C:/Users/aruna/Documents/Codex/2026-10-12/ch/outputs')
OUT.mkdir(parents=True,exist_ok=True)
verification=json.loads((BASE/'verification.json').read_text(encoding='utf-8'))
qc=json.loads((BASE/'qc-after.json').read_text(encoding='utf-8'))
assert qc['ok'] and qc['production_git_status_unchanged']
index_e,index_c=Path(verification['index_e']),Path(verification['index_c'])
assert index_e.read_bytes()==index_c.read_bytes()
def new(path,text):
    assert not path.exists(),path
    path.write_text(text,encoding='utf-8')
index=index_e.read_text(encoding='utf-8')
index=re.sub(r'\]\((20261012-all-projects-[^)]+\.md)\)',lambda m:']('+index_e.parent.as_posix()+'/'+m[1]+')',index)
new(OUT/'Indeks_Memori_Semua_Project_20261012.md',index)
report=[
'# Laporan Penyimpanan Memori Seluruh Project',
'',
'Tanggal: 12 Oktober 2026, zona Asia/Jakarta.',
'',
'## Hasil',
'',
'165 chat sebelumnya yang dapat diakses sudah disimpan sebagai 165 rangkuman Markdown. 118 memperbarui konteks chat yang sudah memiliki memori terkait; 47 merupakan catatan tambahan untuk chat yang belum ditemukan memori yang dapat dipastikan hubungan sumbernya. Angka 47 tidak membuktikan tidak pernah ada catatan manual dengan judul berbeda.',
'',
'Setiap rangkuman memiliki pasangan di E dan C. Ditambah satu indeks di setiap lokasi: total 166 file per lokasi, atau 332 file memori. Pembaruan memakai catatan baru dengan kronologi dan rujukan ke catatan lama agar riwayat terdahulu tetap utuh.',
'',
'## Lokasi resmi',
'',
'- Indeks E: '+str(index_e),
'- Indeks C: '+str(index_c),
'- Folder utama: E:/AI/Memories',
'- Salinan Codex: C:/Users/aruna/.codex/memories/extensions/ad_hoc/notes',
'- Artefak pemeriksaan dan transkrip: '+str(BASE),
'',
'[Buka indeks salinan untuk review](Indeks_Memori_Semua_Project_20261012.md)',
'',
'## Cakupan per project',
'',
'| Project / kelompok chat | Jumlah chat |',
'|---|---:|']
for project,count in verification['projects'].items():
    report.append('| '+project+' | '+str(count)+' |')
report += [
'',
'Chat AoE yang awalnya tanpa project ikut dikelompokkan berdasarkan rujukan topik eksplisit; cwd aslinya tetap disimpan di masing-masing catatan. Lokasi New MG Laptop, SSD Portable, dan konteks New MG tanpa project dibedakan.',
'',
'## Isi catatan',
'',
'Tujuan, konteks awal, keputusan, implementasi yang dilaporkan, daftar file dari patch/laporan, backup historis, hasil pengujian historis, checkpoint substantif terakhir, pending, langkah lanjut, serta rujukan ke transkrip kronologis.',
'',
'Transkrip memuat 5.292 pesan pengguna dan laporan akhir assistant yang diperoleh dari database riwayat dan file sesi. Reasoning, instruksi lingkungan, output tool mentah, dan lampiran biner tidak disalin. Kredensial yang terdeteksi disamarkan.',
'',
'## Bukti verifikasi terbaru',
'',
'- 165 pasangan catatan E/C serta satu pasangan indeks identik byte-for-byte dan SHA-256.',
'- Semua file catatan ada dan tidak kosong; semua ID chat memiliki entri manifest/transkrip.',
'- 222 file memori lama yang dirujuk tetap memiliki SHA-256 yang sama.',
'- Tidak ada error sumber JSON/riwayat pada 165 chat yang diproses.',
'- Pemeriksaan cakupan empat project yang diminta, heading wajib, penyamaran label kredensial, dan self-check sanitizer lulus.',
'- 162 chat memakai database thread_history_1.sqlite yang dicocokkan dengan rollout; satu chat lokal memakai fallback rollout dan dua chat cloud memakai read_thread. Rekonsiliasi mengembalikan 1.387 pesan yang belum tercatat pada proyeksi database.',
'- Inventarisasi 450 file sesi tidak menemukan ID riwayat orphan terhadap 402 thread database. Chat subagent/guardian dipisahkan dari chat utama.',
'- Status Git Phoenix sama dengan snapshot awal setelah menormalkan folder artefak pekerjaan ini. Tidak ada staging, commit, atau push.',
'',
'[Lihat verifikasi JSON](Verifikasi_Memori_20261012.json)',
'',
'## File dibaca, dibuat, dan backup',
'',
'Dibaca: E:/AI/Memories/INDEX.md, README.md, header catatan memori yang relevan, C:/Users/aruna/.codex/memories/MEMORY.md, ringkasan rollout pekerjaan memori 4 Oktober, collect_history.py/save_memories.py lama, state_5.sqlite dan thread_history_1.sqlite secara read-only, session_index.jsonl, rollout lokal, serta daftar/read_thread yang diizinkan aplikasi.',
'',
'Dibuat pada folder artefak: export_memories.py, qc_export.py, script penyempurnaan/finalisasi, snapshot metadata sidebar/project/cloud, prepared.json, review.json, plan.json, manifest.json, previous-hashes.json, verification.json, qc-before.json, qc-after.json, dan 165 transkrip.',
'',
'Script dan artefak yang diperbaiki dibackup sebelum perubahan dengan pola .bak_YYYYMMDD_HHMMSS_refine-extraction, _paginated-history, _merge-history, atau _git-comparison. Memori lama tidak ditimpa.',
'',
'## Keterbatasan dan langkah berikut',
'',
'- Cakupan terdiri dari 163 chat lokal sebelumnya dan dua chat cloud yang tersedia di aplikasi. Ini tidak menjamin seluruh chat akun ChatGPT web, perangkat lain, atau chat yang telah dihapus sudah terjangkau.',
'- Satu chat tidak memiliki laporan akhir: '+verification['no_final'][0][0]+'. Catatannya menyebutkan batas ini, sehingga tidak dianggap pekerjaan yang selesai.',
'- Hasil uji aplikasi dalam catatan adalah laporan historis. Runtime aplikasi, database bisnis, deployment, dan game tidak diuji ulang pada sesi penyimpanan ini.',
'- Lampiran biner dan artefak source tidak digandakan; rujukan tetap menunjuk lokasi asal.',
'- Graphify tidak dikueri maupun diperbarui karena sesi ini mengarsipkan riwayat, tidak mengubah source aplikasi. Pembaruan graph menunggu perintah manual pengguna.',
'- Untuk kelanjutan pekerjaan, baca catatan terkait dari indeks baru lalu periksa source/runtime aktual.',
'']
new(OUT/'Laporan_Penyimpanan_Memori_20261012.md','\n'.join(report))
new(OUT/'Verifikasi_Memori_20261012.json',json.dumps({'storage':verification,'qc':qc},ensure_ascii=False,indent=2))
for name in ['Indeks_Memori_Semua_Project_20261012.md','Laporan_Penyimpanan_Memori_20261012.md','Verifikasi_Memori_20261012.json']:
    path=OUT/name
    assert path.is_file() and path.stat().st_size>0
print(json.dumps({'outputs':str(OUT),'files':3,'index_pair_reverified':True},ensure_ascii=False))
