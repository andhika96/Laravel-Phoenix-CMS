# Bootstrap mengikuti tema CMS

Tanggal: 28 Oktober 2026. Project: `D:/Laragon/www/laravel-13-phoenix`.

Status: diterapkan, diverifikasi pada CMS aktif dan pada harness stylesheet enam layout CMS. Permintaan pengguna mencakup background focus/active dropdown dan konsistensi komponen Bootstrap dengan warna/template CMS.

## Akar masalah

Bootstrap mendefinisikan nilai lokal warna komponen. Pada pemeriksaan sebelum koreksi, accent CMS adalah `rgb(31,166,117)`, tetapi active dropdown memakai `rgb(13,110,253)` dan aturan link tema memaksa teks tetap hijau. Native nav-pills, pagination, list-group, progress, badge dan accordion juga masih membawa warna Bootstrap. Mengubah hanya font dropdown tidak menghubungkan state warna tersebut.

`phoenix-cms.css` juga dipakai frontend/auth, sehingga menambahkan pemetaan global di sana akan melampaui scope CMS. Solusi memakai stylesheet khusus CMS yang dimuat setelah tema pada masing-masing layout CMS.

## Perubahan source

File baru: `public/assets/css/cms-bootstrap-theme.css`.

- Memetakan warna primary, surface, teks, subtle, border, dan focus ke token Phoenix aktif, termasuk palette legacy `--ph-primary` dan palette Arunika `--ph-theme-primary`.
- Menghitung token pada root, body, dan nested `data-bs-theme`, karena sebagian tema memiliki override pada body.
- Dropdown hover/focus/active memakai subtle surface dan teks tema; state disabled tetap disabled dan explicit semantic text seperti Delete tetap merah.
- Native primary/outline/link buttons, navigation, pagination, list-group, progress, accordion, form controls, checked/radio/switch, primary utilities/alerts, serta overlay surfaces menggunakan token tema. Semantic danger/success/warning/info tetap independen dari primary.
- Ikon accordion memakai asset SVG Bootstrap yang sama melalui CSS mask/currentColor, sehingga warna biru statis pada icon dark mode tidak muncul. Focus thumb switch dan active range memakai accent; validasi dan disabled native dipertahankan.
- Dropdown mewarisi ukuran teks template. Tidak mengunci warna accent atau font ke warna/ukuran satu tema.

Satu link stylesheet ditambahkan pada enam file:

1. `resources/views/themes/default/cms/cms_layout.blade.php`
2. `resources/views/themes/calm_green/cms/cms_layout.blade.php`
3. `resources/views/themes/arunika_aurora/cms/cms_layout.blade.php`
4. `resources/views/themes/arunika_prism/cms/cms_layout.blade.php`
5. `resources/views/themes/arunika_equinox/cms/cms_layout.blade.php`
6. `resources/views/themes/arunika_lucent/cms/cms_layout.blade.php`

Satu rule dropdown lokal di `public/assets/css/article/manage-article-2026.css` dihapus karena font/background/border kini dimiliki lapisan bersama. Perilaku serta layout daftar artikel tetap dipertahankan. JavaScript/Blade Manage Article, stylesheet core, dan seluruh frontend/auth layout tidak berubah pada pekerjaan ini.

## Source, memori, dan Graphify yang diperiksa

Source aktif yang diperiksa: Bootstrap lokal 5.3.6_custom, `phoenix-cms.css`, token/semantic rules stylesheet Lucent/Prism/Aurora/Equinox, palette Default/Calm Green, keenam CMS layout, dan stylesheet/markup dropdown Manage Article. Kontrak Lucent, Prism, Equinox, Aurora, responsive typography dan frontend link isolation juga dibaca/dijalankan sesuai scope.

Drive E: tidak tersedia. Memori relevan berasal dari `C:/Users/aruna/.codex/memories/MEMORY.md`, bagian Lucent semantic Bootstrap dan cross-theme typography/profile. Memori dipakai untuk menjaga peran semantic colors, shared-theme seam, dan batas bukti harness versus runtime.

Graphify query untuk `theme-responsive-typography.css` menemukan kontrak tema dan semantic tests; query `public/assets/css/phoenix-cms.css` menemukan metadata file. Query path dari layout Lucent ke stylesheet core tidak menemukan jalur, sehingga dependensi include diperiksa langsung pada Blade. Tidak mengarang relasi graph yang tidak ada. Graphify hanya dipakai sebagai peta source; graph belum diperbarui sesuai arahan manual pengguna.

## Backup dan preservasi

Manifest: `project-artifacts/backups/cms-bootstrap-theme-20261028/manifest.json`.

Tujuh source lama dibackup sebelum perubahan: enam CMS layout dan CSS Manage Article. Nama backup memiliki timestamp serta penanda pekerjaan. Hash SHA-256 ketujuh backup original sesuai source awal. Revisi CSS/test selama pemeriksaan memiliki backup tambahan di folder yang sama.

`preservation-verification.json` membuktikan 15 source yang harus dipertahankan tetap identik, termasuk core stylesheet, seluruh frontend/auth layout, dan Blade/Vue artikel. Git HEAD, index, serta graph tetap sama. Tidak ada stage, commit, push, modifikasi Bootstrap vendor, perubahan database artikel, atau penyimpanan preferensi tema pengguna.

## Verifikasi terbaru

### Harness browser stylesheet, terisolasi

`project-artifacts/tests/cms-bootstrap-theme-20261028/components.mjs`: **24 kasus lolos**, memakai CSS produksi aktual untuk enam tema × light/dark × native/purple accent.

Pemeriksaan mencakup active/focus dropdown, foreground item aktif, primary components, semantic danger/success/warning/info, disabled item, opacity utility, focus switch, dan validation state. Regression awal gagal pada background biru; review visual menambahkan pemeriksaan foreground list-group serta icon accordion sebelum koreksinya. Tidak ada runtime error pada harness.

Ini merupakan bukti stylesheet di browser, bukan klaim bahwa database tema aplikasi telah diganti ke keenam template.

### CMS aktif, terautentikasi

`project-artifacts/tests/cms-bootstrap-theme-20261028/live-browser.mjs`: **12 kasus Manage Article** pada 1440/390 px × light/dark × native/purple/gray accent. Memeriksa hover, focus keyboard, mouse sedang ditekan, `.active`, Escape, dan warna Delete.

Pemeriksaan menu profil juga lolos pada **empat halaman lain**: Dashboard, Account, Manage Cover Image, Manage Event. Runtime errors **0**, Vue warnings **0**, request perubahan artikel **0**. Login tidak dihitung sebagai penulisan artikel. Pergantian warna hanya pada style DOM browser pengujian; tidak disimpan ke database/localStorage pengguna.

Halaman publik `/article` merespons **HTTP 200** dan **tidak memuat stylesheet CMS baru**.

Regression header/font Manage Article dijalankan ulang: **10 kasus lolos** pada 320/390/768/1024/1440 px dan light/dark. Kedua tombol header tetap langsung, ukuran menu mengikuti template, kategori/detail/selection tetap berfungsi. Salinan hasil: `article-menu-regression.json`.

### Kontrak source

**38 test lolos**: kontrak Lucent semantic/mobile/theme, Prism, Equinox, Aurora palette, responsive typography, serta dua guard pemuatan CMS dan isolasi frontend/auth. Log: `contracts.log`.

`git diff --check` lolos; hanya ada peringatan normalisasi CRLF pada file yang memang sudah memakai CRLF.

### Temuan test lama di luar scope

`tests/article-theme-link-isolation.test.mjs` masih gagal pada assertion statis warna `#344054` untuk frontend pagination. Test hanya membaca `article-frontend-2026.css`; source dan test ini identik byte Git HEAD, sehingga failure tidak dipicu adapter CMS. File/test tersebut tidak diubah. Bukti kesamaan hash: `baseline-frontend.json`. Halaman frontend runtime tetap tidak memuat CSS baru.

## Artifact dan keterbatasan

Hasil terstruktur: `components-verification.json`, `live-verification.json`, `article-menu-regression.json`, dan `preservation-verification.json`. Screenshot: `dropdown-pressed-1440.png`, `dropdown-pressed-390.png`, `components-light.png`, `components-dark.png`.

Runtime aplikasi diverifikasi pada tema aktif Lucent. Template lain diverifikasi memakai stylesheet produksi di harness terisolasi dan guard layout, tanpa mengganti konfigurasi tema database. Browser adalah Chrome; Firefox, Safari, dan perangkat fisik belum diuji. Seluruh suite backend/repository tidak dijalankan. Graphify tetap menunggu perintah pembaruan manual.
