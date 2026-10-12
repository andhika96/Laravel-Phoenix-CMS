# Balanced Card Grid — revamp source dan preview

Tanggal: 11 Oktober 2026. Project: `D:\Laragon\www\laravel-13-phoenix`.

## Hasil

Revamp diterapkan ke source aktif sesuai permintaan untuk melanjutkan pola Minimal Reading List, Editorial Journal, dan kedua Mosaic. Struktur Blade Balanced Card Grid tetap digunakan. Typography mengikuti CSS variables frontend CMS; tidak memasang font atau dependency baru.

Preview: `http://127.0.0.1:8777/preview.html`, dengan mode Desktop 1440px, Tablet 768px, dan Mobile 390px. Tampilan ukuran penuh ada di `index.html`. Server hanya melayani artifact ini pada localhost; untuk menjalankannya kembali gunakan `node project-artifacts/mockups/20261011_balanced-card-grid-revamp/qa/server.cjs` dari root project.

Screenshot: `qa/desktop-full.png`, `qa/tablet-full.png`, `qa/mobile.png`, `qa/mobile-full.png`, dan `qa/viewer.png`. Preview memakai Blade, normalizer, dan CSS source aktif, dengan enam fixture articles. Helper search/category/dialog hanya berada di artifact preview dan tidak dimasukkan ke aplikasi; pagination demo bersifat ilustratif.

## Perubahan

- Header dan toolbar mengikuti pola revamp lain. Search input, button, dan category select sejajar pada tinggi 48px; search default memiliki gap 12px dan area yang lebih lapang di desktop.
- Label Search terlihat. Posisi search/category, gap, radius, warna, serta asynchronous category attribute tetap mengikuti setting.
- Kartu mempertahankan grid seimbang, thumbnail di atas body, serta kategori, judul, excerpt, tanggal, dan Read article dengan hierarchy typography CMS. Tinggi kartu dan footer sejajar dalam setiap baris.
- Default grid tetap 3 kolom desktop, 2 tablet, dan 1 mobile. Grid custom dua kolom pada mobile tetap berfungsi; CSS tidak memaksa jumlah kolom.
- Card style flat/outline/elevated, alignment left/center, card padding/radius, excerpt lines, visibility toggles, dan story gap per device tetap dipertahankan.
- Background thumbnail regular cards memakai height dari setting Thumbnail. Asset mode memakai gambar aktual; fit dan custom frame tetap dihormati.
- Default pagination frame Balanced menjadi OFF, sehingga kelima template yang direvamp memiliki default yang sama. Explicit frame ON/OFF yang sudah tersimpan tetap berlaku. Tiga model shared boxed/underline/soft tetap memakai kontrol 44px.

Default frame Balanced sebelumnya ON pada checkpoint pekerjaan Mosaic. Perubahan default OFF dicatat pada tahap Balanced ini; tidak mereset nilai yang sudah disimpan dalam database.

## Source yang dibaca dan diubah

Lima file source yang diubah:

1. `public/assets/css/article/article-frontend-2026.css`: memperluas selector toolbar shared agar mencakup Balanced dan menambah styling khusus kartu Balanced, tanpa menimpa CSS variables setting.
2. `app/Support/Article/ArticleTemplateOptions.php`: default frame Balanced OFF.
3. `public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`: menyamakan fallback frame form dengan normalizer.
4. `resources/views/article/templates/partials/archive-header.blade.php`: label Search Balanced terlihat; aria-label dan filtering existing dipertahankan.
5. `tests/Feature/Article/ArticleTemplateOptionsTest.php`: default/explicit frame diuji untuk seluruh lima archive templates.

Source yang juga dibaca: `balanced-card-grid.blade.php`, `options-classic-balanced.blade.php`, `ArticleTemplateCatalog.php`, media-link, pagination, shell attributes, archive-title, stylesheet typography CMS, serta fixture dan QA sebelumnya. Blade Balanced tidak perlu diubah. `qa/source-checkpoint-delta.json` membuktikan hanya lima file di atas yang berubah dari checkpoint sebelas file; enam file lain tetap identik. Perubahan lama yang tidak terkait dipertahankan.

## Backup

Backup lima file sebelum perubahan:

`project-artifacts/backups/20261011_022351-balanced-card-grid-revamp/manifest.json`

Setiap backup dibandingkan dengan source memakai SHA-256 sebelum perubahan. Semua source backup, mockup, screenshot, compiled views, logs, dan QA artifacts berada di `project-artifacts`. Tidak dilakukan reset, clean, commit, push, atau perubahan pilihan template/setting database.

## Verifikasi terbaru

| Pemeriksaan | Hasil | Bukti |
| --- | --- | --- |
| Node Article dan typography | 128 pass, 0 fail | `qa/node-final.log` |
| PHP Article | 43 pass, 626 assertions | `qa/php-final.log` |
| Browser Balanced: layout, setting, states, viewer | 76 pass | `qa/results.json` |
| Browser parity 5 archive templates × 3 pagination | 27 pass | `implementation/pagination-results.json` |
| PHP/JS syntax | Pass | pemeriksaan terminal |
| `git diff --check` | Pass | pemeriksaan terminal |
| Frontend/CSS HTTP | 200, CSS cocok dengan source | `qa/runtime-http.json` |

Viewport Balanced yang diuji: 1440, 1024, 768, 390, 375, dan 320px. Pengujian memastikan tidak ada horizontal overflow, kartu/footer sejajar per baris, foto di atas teks pada mobile, serta ukuran search mengikuti perubahan font CMS.

Settings yang diuji: flat/outline/elevated, center alignment, padding 24px, radius 20px, excerpt 4 lines, heading tag h3, gap per device 40/28/24px, grid custom, shell padding, background thumbnail 180px/contain/custom frame, asset mode, detail toggles OFF, pagination total OFF, item gap, explicit frame 3px/radius 18px, search position/gap/radius/color, dan ketiga model pagination. Search, empty state, category filter, dialog, dan Escape juga diuji pada prototype.

`render.php` menghasilkan 12 profil dari source aktif. PHP berjalan dengan SQLite `:memory:` untuk tests, array cache/session, compiled views di artifact, dan helper translation identity pada fixture agar tidak menulis data terjemahan CMS. `implementation/pagination-render.php` menghasilkan 15 fixture dari semua Blade archive aktif; pengujian membandingkan height, border, radius, background, color, serta font size untuk tiap model.

## Memori dan Graphify

`E:\AI\Memories` tidak tersedia pada host sesi ini. Konteks memakai registry C `C:\Users\aruna\.codex\memories\MEMORY.md`, terutama preferensi desain modern dan normalizer/settings sebagai kontrak, kemudian diverifikasi ulang pada source. Artifact implementasi Mosaic dipakai untuk melanjutkan pola UI dan QA yang sudah selesai.

Graph tersedia di `graphify-out/graph.json`. Explain `balanced-card-grid.blade.php` menunjukkan lima hubungan include EXTRACTED: archive-header, archive-title, media-link, pagination, dan template-shell-attributes. Semua jalur tersebut diperiksa terhadap source aktif. Graph hanya digunakan sebagai peta; graph tidak di-update sesuai instruksi pengguna untuk update manual dan kini tertinggal terhadap perubahan source revamp.

Skill UI UX Pro Max, Design Taste Frontend, Graphify, Playwright, dan Ponytail digunakan. Superpowers tidak tersedia dalam daftar skill sesi ini.

## Batas verifikasi

Source dan CSS runtime sudah diperiksa; browser QA menggunakan Blade aktif dengan fixture terisolasi. Apply/Save pada form admin authenticated belum diuji. Frontend `/article` masih memilih Minimal Reading List pada checkpoint akhir; pilihan ini dipertahankan. Tidak mengklaim fixture filtering/dialog atau pagination ilustratif sebagai pengujian data bisnis maupun persistensi UI admin.
