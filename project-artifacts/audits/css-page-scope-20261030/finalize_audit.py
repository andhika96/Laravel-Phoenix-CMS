import collections
import csv
import hashlib
import json
from pathlib import Path
from urllib.parse import urlsplit

OUT = Path(__file__).resolve().parent
ROOT = OUT.parents[2]

def result(name):
    payload = json.loads((OUT/name).read_text(encoding='utf-8-sig'))
    assert not payload.get('isError'), (name,payload)
    return json.loads(payload['result'])

cases = []
for name in ['auth-browser-v3.json','cms-batch1.json','cms-batch2.json','cms-batch3.json','cms-batch4.json','edit-samples.json']:
    cases += result(name)
assert len(cases)==102 and len({r['path'] for r in cases})==51
assert all('error' not in r for r in cases)
assert all(not any(token in r['path'] for token in ['pagebuilder','filemanager','file-manager']) for r in cases)
assert all(s['tracked'] for r in cases for s in r['sheets'])
login = [r for r in cases if r['path']=='/auth/login']
assert len(login)==2 and all(r['loginRestoredMatches'] for r in login)
assert all(r['status'] in [200,500] for r in cases)
(OUT/'browser-cases.json').write_text(json.dumps(cases,indent=2),encoding='utf-8')

views = json.loads((OUT/'view-css-map.json').read_text())
assert all(hashlib.sha256((ROOT/v['file']).read_bytes()).hexdigest()==v['sha256'] for v in views)
views = [v for v in views if not v['file'].startswith('resources/views/auth/templates/')]
(OUT/'view-css-map-reviewed.json').write_text(json.dumps(views,indent=2),encoding='utf-8')
inventory = list(csv.DictReader((ROOT/'project-artifacts/audits/css-inventory-20261030/css-inventory.csv').open(encoding='utf-8-sig')))
assert all(hashlib.sha256((ROOT/r['path']).read_bytes()).hexdigest()==r['sha256'] for r in inventory)
custom = [r for r in inventory if r['group']=='custom_public' and not Path(r['path']).name.startswith(('pagebuilder','frontend_elementor'))]
assert len(custom)==25

matrix=[]
for r in cases:
    for s in r['sheets']:
        matrix.append({'page':r['path'],'width':r['width'],'http':r['status'],
                       'css':s['url'],'css_path':urlsplit(s['url']).path,
                       'source_chars':s['chars'],'used_rule_chars':s['usedChars'],'used_rules':s['usedRules'],
                       'observed_percent':round(100*s['usedChars']/s['chars'],2) if s['chars'] else 0,
                       'vue_select_elements':r['components']['vueSelect'],'datepicker_elements':r['components']['datepicker']})
with (OUT/'page-css-matrix.csv').open('w',newline='',encoding='utf-8-sig') as f:
    writer=csv.DictWriter(f,fieldnames=matrix[0].keys());writer.writeheader();writer.writerows(matrix)

by_page={}
for r in cases:
    by_page.setdefault(r['path'],[]).append(r)
vue_absent=sorted({r['path'] for r in cases if r['status']==200 and any('vue-select' in s['url'] for s in r['sheets']) and r['components']['vueSelect']==0})
vue_present=sorted({r['path'] for r in cases if r['components']['vueSelect']>0})
parse=json.loads((OUT/'css-parse-reviewed.json').read_text(encoding='utf-8-sig'))
assert parse['stylesheets']==25 and parse['parsed']==21 and parse['failed']==4
cache=result('cache-browser.json')
assert len(cache)==2 and all(r['status']==200 for r in cache)
cache_map=[{urlsplit(r['url']).path:r for r in page['resources']} for page in cache]
cache_changed=[p for p in cache_map[0].keys()&cache_map[1].keys() if cache_map[0][p]['url']!=cache_map[1][p]['url']]
assert len(cache_changed)==4
assert all(cache_map[1][p]['transferSize']>0 for p in cache_changed)
css_paths={urlsplit(s['url']).path for r in cases for s in r['sheets']}
custom_runtime=[r['path'] for r in custom if '/'+r['path'].removeprefix('public/') in css_paths]
assert len(custom_runtime)==15
theme=json.loads((OUT/'theme-and-routes.json').read_text())['theme']
summary={'date':'2026-10-30','excluded':['all Page Builder pages/assets','new File Manager pages/build sources'],
         'static_blade_views':len(views),'static_get_actions':145,'custom_stylesheets_checked':25,
         'custom_runtime':15,'custom_alternative_theme_source_only':10,
         'browser_paths':51,'browser_viewports':[1440,390],'browser_cases':102,
         'http_counts':dict(collections.Counter(r['status'] for r in cases)),
         'failed_paths':sorted({r['path'] for r in cases if r['status']!=200}),
         'unique_css_urls_without_query':len(css_paths),
         'vue_select_loaded_without_component':vue_absent,'vue_select_component_present':vue_present,
         'parse_passed':21,'parse_failed':4,'changed_cache_urls':cache_changed,
         'source_css_hashes_unchanged':len(inventory),'view_hashes_unchanged':len(views),
         'login_css_restored':True,'active_theme':theme,'source_modified':False,'graph_updated':False}
(OUT/'verification-summary.json').write_text(json.dumps(summary,indent=2),encoding='utf-8')

lines=['# Audit CSS per halaman — Laravel 13 Phoenix — 30 Oktober 2026','',
       'Audit baca-saja. Semua halaman Page Builder dan File Manager baru dikecualikan. Tidak ada source aplikasi, pengaturan tema, halaman, atau data bisnis yang diedit/disimpan oleh audit. Login memakai akun yang telah diotorisasi; efek sesi/login normal aplikasi tetap dapat terjadi.','',
       '## Kesimpulan','',
       'Pemuatan belum sepenuhnya mengikuti kebutuhan minimum tiap halaman. CSS khusus artikel, Header Navigation, Workflow, Spectrum/Croppie/Coloris umumnya memiliki consumer yang tepat. Temuan konkret adalah Vue Select global, Datepicker pada daftar Cover Image, stylesheet tema gabungan auth/frontend/CMS, cache-busting per waktu, dan empat file dengan penulisan CSS tidak valid.','',
       f'- {len(views)} view Blade diperiksa statis; 145 action GET dipetakan tanpa memanggil controller. Action GET mencakup JSON/data, bukan 145 halaman HTML.',
       '- 25 CSS custom di luar Page Builder diperiksa: 15 teramati runtime; 10 milik tema alternatif diperiksa wiring source saja.',
       '- 51 URL pada viewport 1440 dan 390px: 102 kasus; 98 response 200, empat response 500 pada dua URL.',
       '- Seluruh stylesheet yang terdaftar pada halaman sampel terdeteksi oleh engine CSS browser. Tidak ada stylesheet Page Builder/File Manager yang dimasukkan ke pengukuran.',
       '- 173 hash CSS dari inventaris awal dan seluruh hash view audit tetap sama. Tidak ada source aplikasi yang berubah.','',
       '## Temuan terverifikasi','',
       '### 1. Vue Select dimuat terlalu luas','',
       'Layout CMS memuat vue-select.3.20.3.css untuk semua halaman yang memakainya. Dashboard, akun, artikel, event, dan banyak halaman lain tidak memiliki elemen .v-select pada sampel. Source pemakaian <v-select> ditemukan pada delapan view: Site Config; kategori/parent/submenu-detail BE dan FE; serta view Role lama. Sebagian komponen baru tampil ketika modal dibuka. Jangan menghapus plugin secara global; pindahkan pemuatan ke halaman/kelompok consumer tersebut setelah validasi modal. Tiga rule yang tercatat pada Dashboard berasal dari deklarasi :root, bukan bukti komponen select hadir.',
       'Sumber layout: resources/views/themes/arunika_lucent/cms/cms_layout.blade.php:29. Daftar halaman observasi tanpa komponen:','']
lines += ['- `'+p+'`' for p in vue_absent]
lines += ['', '### 2. Datepicker pada daftar Cover Image','',
          'resources/views/manage_coverimage/manage_coverimage.blade.php:9 memuat CSS Datepicker. View daftar dan script vueV3-manage-coverimage-2026.js tidak merender picker; script hanya mendaftarkan VueDatePicker pada components. Kedua viewport daftar memiliki nol picker. Form tambah/edit memiliki komponen picker dan rule CSS-nya terpakai. CSS picker adalah kandidat dipisahkan dari halaman daftar, tetap dipertahankan pada tambah/edit.','',
          '### 3. Lucent dipakai auth, frontend, dan CMS melalui tema global','',
          'Tema database saat audit: '+json.dumps(theme,ensure_ascii=False)+'.',
          'Rantai login: Auth_Controller::login() → auth.default.login → @extends(themes + custom_theme(auth)) → themes/arunika_lucent/auth/auth_layout.blade.php → arunika_lucent.css.',
          'Default pada login adalah template susunan halaman; bukan pilihan tema global Default. custom_theme() membaca theme_settings.id=1, lalu Themes.theme_id, dan memilih folder/layout auth/cms/frontend dari record yang sama.',
          'Layout auth secara eksplisit memuat stylesheet Lucent dan memasang body.ph-theme-arunika-lucent. CSS itu menyediakan token --ph-primary yang dipakai .btn-larapx dari phoenix-cms.css. File Lucent juga berisi sidebar, topbar, dashboard, dan aturan khusus halaman tanpa admin shell.',
          'Uji browser menonaktifkan hanya stylesheet Lucent, tanpa perubahan source/server: latar tombol login hilang dan tautan berubah dari hijau menjadi putih. Sheet dikembalikan dan seluruh computed style yang disampel kembali cocok pada kedua viewport.','',
          '| Viewport login | Panjang source CSS (karakter) | Karakter rule teramati terpakai | Persentase indikatif |',
          '|---:|---:|---:|---:|']
for r in login:
    sheet=next(s for s in r['sheets'] if '/themes/arunika_lucent/arunika_lucent.css' in s['url'])
    lines.append(f"| {r['width']} | {sheet['chars']} | {sheet['usedChars']} | {100*sheet['usedChars']/sheet['chars']:.2f}% |")
lines += ['', 'Ini mengindikasikan bundle auth membawa aturan CMS yang tidak diperlukan pada login. Tidak berarti seluruh sisa rule boleh dihapus dari proyek. Pemisahan yang layak adalah fondasi/token/komponen tema bersama dan aturan khusus CMS; auth tetap membutuhkan bagian tema bersama.',
          'Bukti gambar: login-with-lucent-1440.png; login-without-lucent-1440.png; pasangan 390px juga tersedia.','',
          '### 4. Cache CSS umum tidak digunakan kembali antarhalaman','',
          'Empat URL stylesheet memakai ?v=time(). Saat berpindah Dashboard ke Account, query berubah dan browser mengunduh ulang empat file. Transfer terukur pada masing-masing CSS; cms-bootstrap-theme.css memakai versi filemtime dan transfer kedua 0 byte. Ini membuat file yang relevan tetap diunduh berulang. Kandidat perbaikan: versi berdasarkan perubahan file atau build, setelah verifikasi perilaku cache.','',
          '| CSS | Transfer Dashboard | Transfer Account |', '|---|---:|---:|']
for p in sorted(cache_map[0].keys()&cache_map[1].keys()):
    lines.append(f"| `{p}` | {cache_map[0][p]['transferSize']} | {cache_map[1][p]['transferSize']} |")
lines += ['', '### 5. Penulisan CSS tidak valid di empat file','',
          '| File | Baris kegagalan parser pertama | Temuan |','|---|---:|---|']
for f in parse['files']:
    if not f['parsed']:
        lines.append(f"| `{f['path']}` | {f['error']['line']} | {f['error']['reason']} |")
lines += ['', 'phoenix-cms.css:1682/1688 dan aruna-v4.css:325/331 menulis background-color: var(--ph-content-bg); !important;. Browser tetap menerima background-color tetapi mengabaikan token !important yang terpisah. CSSOM browser mengonfirmasi priority kosong untuk .button-collapsed dan .list-group-item. Dua file tema lama menulis position-relative; di dalam deklarasi, yang bukan properti CSS yang valid. Kegagalan parser bukan berarti browser mengabaikan seluruh file; browser melakukan pemulihan atas deklarasi invalid.','',
          '### 6. Mobile CSS dimuat juga pada desktop','',
          'mobile-v2.css Lucent berukuran 21.007 karakter. Pada Dashboard desktop hanya dua rule (273 karakter) tercatat terpakai; pada mobile dengan drawer dibuka tercatat 124 rule. Ini lapisan responsive aktif, bukan file mati. Link CSS tidak memiliki media attribute. Jangan langsung membatasi seluruh file ke max-width karena file masih memiliki guard desktop yang menyembunyikan kontrol mobile.','',
          '## Bagian yang sesuai kebutuhan','',
          '- CSS khusus Manage Article hanya dimuat pada daftar; editor-content pada tambah/edit/detail/preview artikel; template-manager pada pengaturan template.',
          '- Header Navigation memakai dua CSS khusus halaman itu; Workflow memakai stylesheet bersama daftar/editor; frontend dropdown dipakai frontend dan preview konfigurasi parent menu FE.',
          '- SimpleBar dipakai sidebar pada halaman CMS yang berhasil dirender; nol komponen select tidak berarti SimpleBar juga boleh dilepas.',
          '- Spectrum dipakai form Cover Image; Croppie pada halaman pengelolaan foto; Coloris pada pengaturan warna. Nol rule pada keadaan awal Croppie/fullscreen/modal bukan bukti file tidak dibutuhkan.',
          '- Font aktif tetap dibutuhkan. Pelacakan rule Chrome tidak mengukur pemakaian @font-face dengan cara yang sama; font dengan usedRules=0 tidak dianggap unused.','',
          '## Matriks semua URL yang diuji','', '| URL | Desktop HTTP | Mobile HTTP | CSS unik pada sampel |', '|---|---:|---:|---:|']
for path,items in sorted(by_page.items()):
    d=next(r for r in items if r['width']==1440);m=next(r for r in items if r['width']==390)
    lines.append(f"| `{path}` | {d['status']} | {m['status']} | {len({urlsplit(s['url']).path for r in items for s in r['sheets']})} |")
lines += ['', '## CSS custom dalam scope (25)','', '| File | Bukti audit |', '|---|---|']
for r in custom:
    lines.append(f"| `{r['path']}` | {'Runtime aktif' if r['path'] in custom_runtime else 'Tema alternatif; source saja'} |")
lines += ['', '## Halaman yang belum dapat dinilai penuh','',
          '- /notification/filled: HTTP 500 pada desktop/mobile; controller menunjuk notification.notification_filled yang file view-nya tidak tersedia.',
          '- /awesome_admin/user/edit/1: HTTP 500 pada desktop/mobile; penyebab server belum didiagnosis karena di luar audit pemuatan CSS.',
          '- /auth/recoveryaccount: observasi awal menghasilkan 419 tanpa sesi pemulihan valid; tidak mengirim email/reset password untuk membuat sesi.',
          '- Tema alternatif diperiksa melalui source enam layout CMS/auth/frontend; tidak mengubah tema global untuk menguji semuanya secara runtime.',
          '- Sampel edit memakai record yang sudah tersedia. Tidak semua record, isi modal, hover/focus, dark mode, role non-admin, dan template auth alternatif diuji secara menyeluruh.',
          '- Persentase coverage hanya karakter rule yang teramati pada keadaan sampel, bukan bukti seluruh deklarasi di rule terpakai atau instruksi menghapus bagian lain.','',
          '## Source, memori, Graphify, backup, dan perubahan','',
          '- Memori E: yang relevan: 20260903-arunika-lucent-mobile-final-handoff.md; 20260905-laravel13-phoenix-css-archive-and-equinox-mobile-handoff.md. Konteks theme juga ditemukan pada handoff migrasi 13 Agustus, lalu diverifikasi kembali ke source/record aktif.',
          '- Graphify explain: custom_theme(); Auth_Controller web; arunika_lucent/auth/auth_layout.blade.php. Relasi stylesheet EXTRACTED dicocokkan source. Panggilan model yang dilabeli INFERRED diverifikasi langsung pada Common.php; tidak diperlakukan sebagai fakta dari graph saja.',
          '- Source utama: app/Helper/Common.php; Auth_Controller.php; auth/default/login.blade.php; enam layout tema CMS/auth/frontend; 127 view dalam view-css-map-reviewed.json; controller/action dalam route-view-map.json; script daftar Cover Image; 25 stylesheet custom.',
          '- Source aplikasi tidak diubah. Tidak ada backup source baru. Backup revisi alat audit ada di folder audit ini dengan suffix .bak_20261030_*. Tidak memakai backup sebagai source.',
          '- Tiga perubahan user di Manage Article tetap dipertahankan. Tidak staging, commit, push, deploy, atau build ulang.',
          '- Graphify tidak diperbarui, sesuai instruksi manual.','',
          '## Berkas bukti','',
          '- page-css-matrix.csv: stylesheet per URL/viewport, penggunaan rule indikatif, dan keberadaan komponen.',
          '- browser-cases.json: 102 hasil browser, pemulihan sheet login, dan CSS yang dimuat.',
          '- view-css-map-reviewed.json / route-view-map.json / theme-and-routes.json: jalur source, layout, dan tema aktif.',
          '- css-parse-reviewed.json / syntax-browser.json: hasil parser dan pemulihan browser atas deklarasi invalid.',
          '- cache-browser.json: permintaan/transfer CSS antarhalaman.',
          '- verification-summary.json: ringkasan pemeriksaan dan batas scope.',
          '- auth-browser.json dan auth-browser-v2.json berisi percobaan alat yang gagal; kesimpulan memakai auth-browser-v3.json yang berhasil.']
(OUT/'report.md').write_text('\n'.join(lines)+'\n',encoding='utf-8')
print(json.dumps({k:v for k,v in summary.items() if k not in ['vue_select_loaded_without_component','vue_select_component_present']},indent=2))
