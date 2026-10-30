"""Combine the file scan, reviewed dynamic dependencies, and browser evidence."""
import collections
import csv
import hashlib
import json
from pathlib import Path
import re
from urllib.parse import urlsplit

OUT = Path(__file__).resolve().parent
ROOT = OUT.parents[2]
rows = list(csv.DictReader((OUT/'css-inventory.csv').open(encoding='utf-8-sig')))
runtime = []
for name in ['runtime-pages-verified.json', 'runtime-secondary-verified.json']:
    payload = json.loads((OUT/name).read_text(encoding='utf-8-sig'))
    assert not payload.get('isError'), payload
    runtime += json.loads(payload['result'])
assert len(runtime) == 15
assert all(r.get('status') == 200 and urlsplit(r['url']).path == r['path'] for r in runtime)
(OUT/'runtime-pages.json').write_text(json.dumps(runtime, indent=2), encoding='utf-8')

local = collections.defaultdict(list)
for page in runtime:
    for resource in page['resources']:
        url = urlsplit(resource['url'])
        if url.hostname == 'laravel-13-phoenix.aruna':
            assert resource['status'] == 200, resource
            physical = 'storage/app/public/'+url.path.removeprefix('/storage/') if url.path.startswith('/storage/') else 'public'+url.path
            local[physical].append(page['path'])
module_types = {x for page in runtime for x in page['moduleStyles']}
assert len(module_types) == 28

for row in rows:
    path = row['path']
    assert hashlib.sha256((ROOT/path).read_bytes()).hexdigest() == row['sha256'], path
    row['runtime_pages'] = '; '.join(sorted(set(local.get(path, []))))
    if path in local:
        review = 'TERPAKAI_RUNTIME_HTTP_200'
    elif row['group'] == 'pagebuilder_module_source':
        module = json.loads((ROOT/path).with_name('module.json').read_text(encoding='utf-8-sig'))
        assert module['type'] in module_types, module
        review = 'TERPAKAI_RUNTIME_STYLE_INLINE'
        row['runtime_pages'] = '/pagebuilder-elementor/v2.4/create'
    elif row['group'] == 'custom_public':
        assert int(row['application_refs']) > 0
        review = 'TERPAKAI_KONDISIONAL_SOURCE'
    elif path.startswith('public/assets/plugins/ckfinder/') and row['group'] != 'plugin_example_test':
        review = 'DEPENDENSI_SKIN_DINAMIS_CKFINDER_PERTAHANKAN'
    elif row['group'] == 'plugin_example_test':
        review = 'CONTOH_DOKUMENTASI_TEST_PLUGIN'
    elif path in ['public/assets/plugins/spectrum/dist/spectrum.css', 'public/assets/plugins/spectrum/src/spectrum.css']:
        review = 'SOURCE_DISTRIBUSI_SPECTRUM'
    elif path == 'resources/js/filemanager_v2/styles.css':
        review = 'SOURCE_BUILD_TERPAKAI'
    elif path == 'resources/css/app.css':
        assert int(row['bytes']) == 0
        review = 'INPUT_VITE_KOSONG_TANPA_CONSUMER_VIEW'
    elif row['group'] == 'beta':
        review = 'KANDIDAT_TIDAK_TERPAKAI_SOURCE_DB_0_HIT'
    else:
        assert row['group'] == 'plugin_distribution' and int(row['application_refs']) == 0, row
        review = 'KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI'
    row['reviewed_status'] = review

with (OUT/'css-reviewed.csv').open('w', newline='', encoding='utf-8-sig') as handle:
    writer = csv.DictWriter(handle, fieldnames=rows[0].keys())
    writer.writeheader()
    writer.writerows(rows)

db = json.loads((OUT/'database-css-check.json').read_text())
assert db['text_json_columns_checked'] == 272 and db['hits'] == []
graph = json.loads((ROOT/'graphify-out/graph.json').read_text(encoding='utf-8'))
by_path = {r['path']:r for r in rows}
graph_checks = []
for node in graph['nodes']:
    path = node.get('source_file')
    if path in by_path and node.get('content_sha256'):
        graph_checks.append({'path':path, 'graph_hash_matches_current_source':node['content_sha256']==by_path[path]['sha256']})
(OUT/'graph-freshness.json').write_text(json.dumps(graph_checks,indent=2), encoding='utf-8')
refs = json.loads((OUT/'references.json').read_text())
external = json.loads((OUT/'external-css.json').read_text())
source_names = sorted({r['source'] for values in refs.values() for r in values if r['kind']=='application'} | {r['source'] for r in external})
(OUT/'application-reference-files.txt').write_text('\n'.join(source_names)+'\n', encoding='utf-8')

npm_paths = ['bootstrap/dist/css/bootstrap.min.css', 'bootstrap-icons/font/bootstrap-icons.css',
             '@fontsource/nunito/400.css', '@fontsource/nunito/500.css', '@fontsource/nunito/600.css',
             '@fontsource/nunito/700.css', 'filepond/dist/filepond.min.css']
assert all((ROOT/'node_modules'/path).is_file() for path in npm_paths)
counts = dict(collections.Counter(r['reviewed_status'] for r in rows))
summary = {'date':'2026-10-30', 'total_local_css':len(rows), 'reviewed_counts':counts,
           'browser_pages':len(runtime), 'unique_local_css_http_200':len(local), 'inline_module_styles':len(module_types),
           'custom_css_runtime':sum(r['group']=='custom_public' and r['path'] in local for r in rows),
           'custom_css_conditional':sum(r['group']=='custom_public' and r['path'] not in local for r in rows),
           'css_hashes_unchanged':len(rows), 'npm_direct_css_imports':npm_paths,
           'stale_graph_css':[r['path'] for r in graph_checks if not r['graph_hash_matches_current_source']]}
(OUT/'verification-summary.json').write_text(json.dumps(summary,indent=2), encoding='utf-8')

lines = ['# Inventaris CSS Laravel 13 Phoenix — 30 Oktober 2026', '',
         'Audit file-level, baca-saja terhadap source aktif dan sampel browser lokal. Source aplikasi tidak diubah, dipindah, dihapus, di-build ulang, di-commit, atau di-push.', '',
         '## Ringkasan', '',
         f'- {len(rows)} CSS lokal diinventarisasi; mencakup custom public, plugin/vendor publik, source modul, source build, dan font storage.',
         f'- {len(local)} CSS lokal teramati dengan HTTP 200; 28 source stylesheet modul teramati sebagai style inline.',
         '- 32 CSS custom: 21 teramati runtime, 11 direferensikan kondisional oleh tema alternatif atau setup.',
         '- 1 kandidat custom beta: `public/assets/beta_css/new.css`, tanpa referensi source dan nol hit pada 272 kolom teks/JSON database.',
         '- 66 varian distribusi plugin tanpa pemanggil aplikasi yang ditemukan; status kandidat, bukan bukti aman dihapus.',
         '- 9 CSS CKFinder dipertahankan sebagai dependensi/alternatif skin dinamis; tidak diklasifikasikan sebagai unused.',
         '- 11 CSS contoh/docs/test plugin dipisahkan dari stylesheet CMS; 2 CSS source/distribusi Spectrum dipertahankan sebagai companion build.',
         '- `resources/css/app.css` kosong (0 byte), terdaftar sebagai input Vite; tidak ditemukan consumer @vite pada view aktif.',
         '- 7 import CSS paket npm dan 1 SCSS responsive File Manager ditelusuri secara terarah, di luar hitungan 173 file lokal.', '',
         '## Metode dan batas', '',
         'Urutan pemeriksaan: memori E: → query/explain Graphify → source aktual → browser → pemeriksaan hash. Komentar Blade/HTML/blok kode dikeluarkan dari pencocokan. Jumlah referensi adalah occurrence, termasuk filemtime; bukan jumlah stylesheet yang dimuat.',
         'Knowledge graph digunakan sebagai peta saja; query `css cms layout theme article` dan explain node layout Equinox/Prism menunjukkan relasi references_stylesheet EXTRACTED. Sebagian metadata graph stale; lihat graph-freshness.json. Graph tidak diperbarui sesuai instruksi manual pengguna. Tidak ada relasi INFERRED/AMBIGUOUS yang dijadikan dasar status akhir.',
         'Scanner mengecualikan backup, .bak, arsip historis, project-artifacts, cache/log, generated rendition, node_modules penuh, vendor PHP, mockup/prototype, dan duplikasi junction public/storage. public/assets/vendor tetap diperiksa. Font dihitung sekali dari storage/app/public/fonts.',
         'Belum dilakukan audit selector/property yang mati, semua kombinasi widget/data, semua tema melalui runtime, akses URL eksternal langsung, atau pemeriksaan seluruh histori access log. Status CORS responseStatus=0 pada CSS CDN tidak dianggap HTTP gagal ataupun bukti HTTP 200.', '',
         '## CSS custom (32)', '', '| File | Status | Bukti source |', '|---|---|---|']
for row in rows:
    if row['group']=='custom_public':
        lines.append(f"| `{row['path']}` | {row['reviewed_status']} | {row['evidence']} |")

lines += ['', '## Seluruh inventaris CSS aktif/calon (173)', '', '| File | Status review | Byte | Runtime |', '|---|---|---:|---|']
for row in rows:
    lines.append(f"| `{row['path']}` | {row['reviewed_status']} | {row['bytes']} | {row['runtime_pages']} |")

lines += ['', '## Import paket dan source build tambahan', '']
lines += [f'- `node_modules/{path}`' for path in npm_paths]
lines += ['- `resources/js/filemanager_v2/responsive-typography.scss`',
          '- `resources/js/filemanager_v2/components/FilePondUploadEngine.vue` juga memuat style block; style component/build bukan CSS sisa yang boleh dihapus terpisah.',
          '- File hasil build aktif: `public/assets/plugins/filemanager_v2/filemanager-v2.css`; sumbernya mencakup imports CSS/SCSS/Vue. Tidak ditemukan CSS fisik pada public/build/assets; manifest app.css menghasilkan JavaScript kosong.', '',
          '## CSS eksternal', '']
urls = sorted({r['token'] for r in external} | {s for page in runtime for s in page['css'] if urlsplit(s).hostname!='laravel-13-phoenix.aruna'})
lines += [f'- `{url}`' for url in urls]
lines += ['', '## Browser lokal (15 halaman)', '', '| URL path | HTTP |', '|---|---:|']
lines += [f"| `{page['path']}` | {page['status']} |" for page in runtime]
lines += ['', 'Login auth juga diamati pada awal sesi: Bootstrap custom, Font Awesome 5.15.3, Noto Sans, phoenix-cms, tema Lucent, dan responsive typography. Data browser terstruktur yang dipakai menghitung hasil di atas adalah 15 halaman setelah login.', '',
          '## Duplikasi byte identik', '',
          'Tidak ada duplikasi byte identik di antara 32 CSS custom. Kelompok berikut sama hash, tetapi dapat memiliki tanggung jawab/URL terpisah; belum digabung atau dihapus.']
for group in json.loads((OUT/'exact-duplicates.json').read_text()):
    lines.append('- '+'; '.join(f'`{path}`' for path in group))
lines += ['', '## Arsip historis yang sudah ada (tidak dianalisis ulang sebagai source aktif)', '',
          'Enam nama berikut masih ada di public/assets/archived/css; isi file tidak dimasukkan ke audit source aktif:']
lines += [f'- `{path.name}`' for path in sorted((ROOT/'public/assets/archived/css').glob('*.css'))]
lines += ['', '## Memori, file yang dibaca, dan recovery', '',
          '- Memori: `E:/AI/Memories/20260905-laravel13-phoenix-css-archive-and-equinox-mobile-handoff.md`.',
          '- Memori: `E:/AI/Memories/20260825-laravel13-phoenix-public-resources-views-cleanup-handoff.md`.',
          '- Daftar file aplikasi pemanggil CSS: application-reference-files.txt. Lokasi dan nomor baris referensi: references.json dan css-reviewed.csv.',
          '- Source utama: enam layout CMS, auth/frontend layout, Blade halaman terkait, editor/frontend Page Builder, module.json, ModuleCatalog, ModuleAssetController, route aktif, main.js/styles.css/SCSS/FilePondUploadEngine File Manager, vite configs, dan manifest public/build.',
          '- Backup source aplikasi: tidak diperlukan karena source aplikasi tidak dimodifikasi. Backup revisi alat audit baru saja berada di folder audit ini dengan suffix .bak_20261030_*. Tidak ada backup lama dijadikan source.',
          '- Perubahan milik user tetap: public/assets/css/article/manage-article-2026.css; public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js; resources/views/manage_article/manage_article.blade.php.',
          '- Pemeriksaan: 173 hash CSS tetap cocok; seluruh 15 response halaman dan 43 CSS lokal teramati HTTP 200; 28 module style terdeteksi. Tidak menjalankan full suite/build karena aplikasi tidak diubah.',
          '- Database dicek dalam transaksi READ ONLY lalu rollback. Tidak ada perubahan pengaturan tema atau penyimpanan halaman/widget.',
          '- Graph tidak diperbarui; menunggu perintah manual. Tidak ada staging, commit, push, atau deploy.', '',
          '## Berkas bukti', '',
          '- css-reviewed.csv: seluruh klasifikasi hasil review, ukuran, hash, referensi, dan halaman runtime.',
          '- css-inventory.csv / references.json: hasil pencocokan source awal; gunakan reviewed_status pada css-reviewed.csv sebagai kesimpulan.',
          '- runtime-pages.json: hasil browser lokal yang dinormalisasi.',
          '- database-css-check.json: hasil kandidat beta pada database.',
          '- exact-duplicates.json / graph-freshness.json / verification-summary.json: duplikasi, freshness graph, dan verifikasi akhir.',
          '- external-css.json: source referensi stylesheet eksternal berekstensi .css. Google Fonts tercatat dari source dan browser di laporan ini.']
(OUT/'report.md').write_text('\n'.join(lines)+'\n', encoding='utf-8')
print(json.dumps(summary, indent=2))
