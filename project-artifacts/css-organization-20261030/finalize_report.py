import collections,csv,hashlib,json
from pathlib import Path
import xml.etree.ElementTree as ET

OUT=Path(__file__).resolve().parent
ROOT=OUT.parents[1]
state=json.loads((OUT/'recovery-manifest.json').read_text())
mapping={m['old']:m['new'] for m in state['moves']}
inventory=list(csv.DictReader((ROOT/'project-artifacts/audits/css-inventory-20261030/css-inventory.csv').open(encoding='utf-8-sig')))
active=[r for r in inventory if r['group']=='custom_public']
assert len(active)==32
for r in active:
    path=ROOT/mapping.get(r['path'],r['path'])
    assert hashlib.sha256(path.read_bytes()).hexdigest()==r['sha256'],str(path)
for r in state['references']:
    assert hashlib.sha256((ROOT/r['path']).read_bytes()).hexdigest()==r['after_sha256'],r['path']
backup=ROOT/state['backup']
for m in state['moves']:
    assert hashlib.sha256((backup/m['old']).read_bytes()).hexdigest()==m['sha256']
for r in state['references']:
    assert hashlib.sha256((backup/r['path']).read_bytes()).hexdigest()==r['before_sha256']
for name,expected in state['protected'].items():
    assert hashlib.sha256((ROOT/name).read_bytes()).hexdigest()==expected
assert not [p for p in (ROOT/'public/assets/css').iterdir() if p.is_file()]

def result(name):
    payload=json.loads((OUT/name).read_text(encoding='utf-8-sig'))
    if payload.get('isError'):raise AssertionError(payload['error'].splitlines()[0])
    return json.loads(payload['result'])
cases=result('browser-auth-verified.json')+result('browser-pages-verified.json')
assets=result('browser-new-css-verified.json')
assert len(cases)==21 and len({r['path'] for r in cases})==18
assert all(r['status']==200 and all(s['status']==200 for s in r['css']) for r in cases)
assert len(assets)==16 and all(a['status']==200 and a['sha256']==a['expected'] for a in assets)
node=json.loads((OUT/'final-node-tests.json').read_text())
assert node['exit_code']==0 and node['stats']['pass']==256 and node['stats']['fail']==0
php=ET.parse(OUT/'final-php-assets.xml').getroot()
suite=php.find('testsuite') if php.tag=='testsuites' else php
assert int(suite.attrib['tests'])==5 and int(suite.attrib.get('failures','0'))==0 and int(suite.attrib.get('errors','0'))==0
with (OUT/'relocations.csv').open('w',newline='',encoding='utf-8-sig') as f:
    w=csv.DictWriter(f,fieldnames=['old','new','kind','sha256']);w.writeheader();w.writerows(state['moves'])
with (OUT/'updated-consumers.csv').open('w',newline='',encoding='utf-8-sig') as f:
    w=csv.DictWriter(f,fieldnames=['path','before_sha256','after_sha256']);w.writeheader();w.writerows(state['references'])
summary={'date':'2026-10-30','root_css_loose_files':0,'active_css_total':32,'active_css_payloads_unchanged':32,
         'relocated_stylesheets':16,'historical_backups_relocated':18,'color_references_relocated':2,
         'updated_views':35,'updated_test_files':53,'backed_up_original_files':len(list(backup.rglob('*')))-sum(p.is_dir() for p in backup.rglob('*')),
         'backup':state['backup'],'node_passed':256,'php_tests_passed':5,'php_assertions':int(suite.attrib['assertions']),
         'php_syntax_files':json.loads((OUT/'php-syntax-check.json').read_text())['files'],
         'browser_cases':21,'browser_urls':18,'new_css_http_and_hash_passed':16,'user_changes_preserved':True,
         'graph_updated':False,'committed':False,'pushed':False,'deployed':False}
(OUT/'verification-summary.json').write_text(json.dumps(summary,indent=2),encoding='utf-8')
lines=['# Penataan folder CSS Laravel 13 Phoenix — 30 Oktober 2026','',
       'Selesai secara lokal. Enam belas CSS yang semula berada di root public/assets/css telah dikelompokkan menurut fungsi. Semua 32 CSS aktif mempertahankan isi byte identik. Seluruh consumer aktif yang ditemukan diperbarui ke lokasi baru.','',
       '## Struktur akhir','', '```text', 'public/assets/css/',
       '├─ shared/                  CSS dasar, tambahan Bootstrap, typography bersama',
       '├─ cms/                     penghubung tema Bootstrap CMS',
       '├─ article/                 CSS artikel yang sudah terpisah',
       '├─ frontend/                menu frontend',
       '├─ header-navigation/       pengaturan Header Navigation',
       '├─ pagebuilder/',
       '│  ├─ legacy/               Page Builder lama',
       '│  ├─ elementor/            editor/frontend Elementor awal',
       '│  ├─ elementor-v23/        editor/frontend Elementor v2.3',
       '│  └─ elementor-v24/        editor/frontend Elementor v2.4',
       '├─ setup/                   stylesheet instalasi',
       '├─ themes/                  masing-masing tema dan mobile',
       '└─ workflow/                daftar/editor Workflow','```','',
       '## Relokasi CSS aktif','', '| Lokasi lama relatif terhadap public/assets/css | Lokasi baru |', '|---|---|']
for m in state['moves']:
    if m['kind']=='stylesheet':lines.append(f"| `{Path(m['old']).name}` | `{m['new'].removeprefix('public/assets/css/')}` |")
lines+=['','## File pendamping','',
        '- 18 file .bak lama dipindahkan ke `project-artifacts/css-organization-20261030/historical-css-backups/`; file tidak dihapus permanen atau dipakai sebagai source analisis.',
        '- base_color.txt dan base_color_from_heroui_theme.txt dipindahkan ke `project-artifacts/references/css-color-notes-20261030/`.',
        '- Folder article dan themes dipertahankan. File Manager V2 tetap pada struktur source/build yang sudah ada.',
        '- Root public/assets/css kini hanya berisi sembilan folder; tidak ada CSS, catatan TXT, atau .bak yang lepas di root.','',
        '## Consumer dan batas perubahan','',
        '- Path diperbarui pada 35 view aktif dan 53 file test/helper. Daftar lengkap: updated-consumers.csv.',
        '- Pembaruan mencakup asset/url, public_path/filemtime, pembacaan file pada test, serta assertion path isolasi versi Page Builder.',
        '- Seluruh URL aset di dalam 16 CSS diperiksa; tidak ada URL relatif yang perlu direbase. Payload CSS tidak diubah.',
        '- Tidak ada referensi aktif ke path flat lama yang ditemukan setelah perubahan pada scope source yang diperiksa. Database 272 kolom teks/JSON diperiksa dalam transaksi READ ONLY; nol referensi path lama yang perlu dimigrasikan.',
        '- Spasi/tab sisa di akhir 13 file hanya dirapikan pada baris URL CSS yang baru diperbarui, agar git diff --check lulus.','',
        '## Backup dan pemulihan','',
        '- Backup utama: `'+state['backup']+'`.',
        '- 127 file asli dicadangkan sebelum relokasi/perubahan consumer; hash setiap backup cocok.',
        '- Manifest pemulihan: recovery-manifest.json. Berisi lokasi asal/tujuan, hash, backup, consumer, dan hash tiga file perubahan lama user.',
        '- Snapshot perubahan lama user: pre-existing-working-tree.patch. Tidak melakukan reset, checkout, stash, atau stage.',
        '- Backup sebelum merapikan spasi URL: `'+state['reference_whitespace_backup']+'`.',
        '- Recovery dapat dilakukan per file dengan lokasi asal di manifest dan salinan asli di backup utama; jangan menimpa perubahan baru tanpa membandingkan source aktif.','',
        '## Verifikasi','',
        '- 32/32 CSS aktif tetap identik berdasarkan SHA-256; 16/16 CSS hasil relokasi juga teruji melalui HTTP dengan isi sesuai hash asli.',
        '- Node baseline dan sesudah perubahan: 256 lulus, 0 gagal, dari 40 file test terarah.',
        '- PHP asset isolation/baseline Page Builder v2.4: 5 lulus, 8.619 assertion, sebelum dan sesudah perubahan.',
        '- Pemeriksaan syntax PHP: 59 file lulus.',
        '- git diff --check: lulus; Git masih memberi peringatan normalisasi line ending pada beberapa file, tanpa whitespace error.',
        '- Browser: 21 kasus pada 18 URL, seluruh halaman dan CSS lokal yang diperiksa berstatus HTTP 200. Tidak ada link CSS flat lama yang terdeteksi.',
        '- Screenshot login, Dashboard, dan Page Builder v2.4 disimpan di folder task ini. Login dan Dashboard diperiksa visual setelah relokasi.',
        '- Tiga file perubahan lama user tetap cocok hash-nya: manage-article-2026.css, vueV3-manage-article-2026.js, dan manage_article.blade.php.','',
        '| Halaman | Lebar viewport | HTTP |','|---|---:|---:|']
lines += [f"| `{r['path']}` | {r['width']} | {r['status']} |" for r in cases]
lines+=['','## Memori dan Graphify','',
        '- Memori digunakan: E:/AI/Memories/20260905-laravel13-phoenix-css-archive-and-equinox-mobile-handoff.md; E:/AI/Memories/20260825-laravel13-phoenix-public-resources-views-cleanup-handoff.md.',
        '- Graphify explain dipakai pada phoenix-cms.css dan pagebuilder_elementor_v24.css untuk menemukan layout dan test pemanggil. Relasi references_stylesheet EXTRACTED kemudian diperiksa terhadap source aktual.',
        '- Metadata hash seluruh 16 CSS sesuai sebelum relokasi. Graphify tidak diperbarui karena pengguna meminta update manual; graph kini masih menunjuk sebagian path lama. Gunakan manifest relokasi ini sampai update manual dilakukan.','',
        '## Batas verifikasi','',
        '- Ini penataan file dan path. Isi bundle auth/CMS, kesalahan sintaks CSS lama, pemuatan Vue Select/Datepicker, dan cache versioning yang dibahas pada audit sebelumnya tidak diubah.',
        '- Runtime diuji pada browser lokal dan viewport sampel, bukan seluruh browser/perangkat/role/keadaan interaksi atau production.',
        '- Tidak menjalankan seluruh suite aplikasi atau build ulang. Pemeriksaan difokuskan pada consumer aset dan halaman yang memuat kelompok CSS yang dipindahkan.',
        '- Tidak ada staging, commit, push, atau deployment. Pemindahan dan perubahan consumer perlu dibawa bersama jika nanti di-deploy.','',
        '## Bukti utama','',
        '- relocations.csv / recovery-manifest.json: asal, tujuan, kategori, dan hash file.',
        '- updated-consumers.csv: 88 file consumer/test yang diperbarui.',
        '- final-node-tests.tap / final-php-assets.xml / php-syntax-check.json: hasil pengujian.',
        '- browser-auth-verified.json / browser-pages-verified.json / browser-new-css-verified.json: bukti runtime yang berhasil.',
        '- verification-summary.json: ringkasan verifikasi.']
(OUT/'report.md').write_text('\n'.join(lines)+'\n',encoding='utf-8')
print(json.dumps(summary,indent=2))
