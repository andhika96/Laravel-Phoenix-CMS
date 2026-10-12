# Minimal Reading List: preview revamp UI/UX

Tanggal: 9 Oktober 2026, Asia/Jakarta. Project: Laravel 13 Phoenix.

Status: prototype terpisah selesai dan sudah diuji di browser. Belum diterapkan ke template aktif Laravel.

## Membuka preview

- Viewer Desktop / Tablet / Mobile: http://127.0.0.1:8774/preview.html
- Prototype ukuran browser sebenarnya: http://127.0.0.1:8774/index.html
- `index.html` juga dapat dibuka langsung melalui browser dari filesystem; seluruh font, ikon, dan foto tersedia lokal.
- `qa/current-service.json` mencatat server Node yang aktif. `qa/server.json` dan log Python merupakan diagnostik awal, bukan status layanan terkini.

Untuk menjalankan ulang server lokal, gunakan PowerShell:

```powershell
& 'C:\Users\aruna\.cache\codex-runtimes\codex-primary-runtime\dependencies\node\bin\node.exe' 'D:\Laragon\www\laravel-13-phoenix\project-artifacts\mockups\20261009_minimal-reading-list-revamp\qa\server.cjs'
```

Server hanya bind ke `127.0.0.1` dan hanya menyajikan folder prototype ini.

## Perubahan desain dalam prototype

| Bagian | Ukuran / perilaku |
| --- | --- |
| Search input dan button | Tinggi sama, 48 px; radius 10 px |
| Search button | Lebar 128 px desktop, 104 px mobile, 96 px untuk layar paling sempit |
| Jarak input ke button | 12 px; button memiliki gap ikon ke teks 9 px |
| Search form | Lebar maksimum 680 px dengan input fleksibel |
| Pagination | Target minimal 44 × 44 px; gap 8 px desktop dan 6 px mobile |
| Pagination mobile | Halaman pertama, halaman relevan/aktif, halaman terakhir; ellipsis seperlunya; Previous/Next memakai ikon berlabel aksesibel |
| Daftar artikel | Thumbnail konsisten, judul 22 px desktop / 18 px mobile, jarak row 26 px desktop / 22 px mobile, divider tipis |
| Kategori | Daftar dengan penanda aktif lembut, jumlah artikel, search kategori; mobile memakai native select dekat search utama |
| Popular Posts | Thumbnail dan alignment konsisten, hierarki teks lebih jelas |

Identitas modern sans-serif, aksen hijau, struktur reading list, sidebar, dan copy utama dipertahankan. Menggunakan Nunito dan Font Awesome 6.5.1 yang sudah tersedia di project. Tidak menambah dependency aplikasi.

Catatan source: input/button aktif sudah memiliki `min-height: 48px`. Pada screenshot Manager ukurannya terlihat lebih kecil karena preview diskalakan. Model search `attached` memang memakai `gap: 0`; prototype mengusulkan input dan button terpisah agar proporsi serta jaraknya lebih jelas.

## Memori dan source yang dipakai

`E:\AI\Memories` dicoba terlebih dahulu, tetapi drive E tidak tersedia di sesi ini. Konteks diambil dari salinan C, kemudian diperiksa terhadap source aktif:

- `C:\Users\aruna\.codex\memories\MEMORY.md`, bagian Article Template Options dan Editorial Journal/pagination.
- `C:\Users\aruna\.codex\memories\rollout_summaries\2026-09-06T09-48-48-oqdq-laravel13_phoenix_editorial_journal_template_options.md`, terutama preferensi preview sebelum implementasi dan mempertahankan karakter modern sans-serif.

Source aktif yang dibaca atau ditelusuri:

- `resources/views/article/templates/archive/minimal-reading-list.blade.php`
- `resources/views/article/templates/partials/archive-header.blade.php`
- `resources/views/article/templates/partials/pagination.blade.php`
- `public/assets/css/article/article-frontend-2026.css`, selector reading list, search model, serta pagination.
- `app/Http/Controllers/Web/Manage_Article/ManageArticleTemplateController.php`, alur preview.
- `app/Support/Article/ArticleTemplatePreviewFixture.php`, data dan media contoh.
- `app/Support/Article/ArticleTemplateCatalog.php`, mapping template.
- `project-artifacts/README.md`, aturan penempatan artifact; instruksi pengguna untuk menyimpan aktivitas dalam project-artifacts diikuti.

Fixture mengikuti renderer preview aktif: 12 cerita diulang menjadi 48 item, 6 artikel per halaman. Search dan kategori di prototype bekerja terhadap fixture ini. Data production tidak dibaca atau diubah.

Skill yang dipakai: UI UX Pro Max, Design Taste Frontend, Graphify, Ponytail, dan Playwright. Superpowers tidak ditemukan dalam skill/plugin yang tersedia di sesi ini.

## Graphify

- Graph: `D:\Laragon\www\laravel-13-phoenix\graphify-out\graph.json`.
- Query: `minimal reading article archive template pagination search` dengan budget 1600.
- Explain: `minimal-reading-list.blade.php`.
- Explain menunjukkan lima relasi `EXTRACTED`: template-shell-attributes, archive-header, media-link, archive-title, dan pagination. Semua jalur yang relevan diperiksa lagi pada source aktif.
- Node generik dari query awal, seperti search File Manager, tidak digunakan sebagai bukti fitur Article.
- `built_at_commit` sama dengan HEAD: `966a8057536d6ddc53c602e68bf62c5ccf336be1`. Worktree awal bersih dan mtime file Blade cocok dengan manifest graph. CSS diperiksa langsung karena tidak memiliki entri manifest yang sama.
- Graph tidak diperbarui: graph masih sesuai untuk source yang ditelusuri dan tidak ada perubahan source aplikasi. Izin pengguna untuk update bila stale dicatat; kondisi tersebut tidak ditemukan dalam scope ini.

## File baru, backup, dan perubahan

Semua aktivitas berada dalam `project-artifacts/mockups/20261009_minimal-reading-list-revamp/`:

- `index.html`: prototype native HTML/CSS/JavaScript.
- `preview.html`: viewer dengan ukuran Desktop 1440, Tablet 768, Mobile 390.
- `assets/`: salinan foto, font, dan ikon dari source aktif. `knowledge-preview.png` identik dengan `canyon-abstract.png`; piksel tidak diedit.
- `qa/verify.js`: satu pemeriksaan browser yang dapat dijalankan ulang.
- `qa/server.cjs`: server lokal memakai Node stdlib.
- `qa/results.json`: hasil 47 pemeriksaan yang lulus.
- `qa/*.png`: screenshot desktop, tablet, mobile, viewer, serta pagination awal/tengah.
- `backups/index.html.bak_20261009_135535_responsive-pagination`: backup sebelum revisi pagination mobile. SHA-256: `08A46928939C0C9B71461D28F04AB463B3E519F70A60B1615250A5A3BB1CF426`.
- `backups/verify.js.bak_20261009_135939_responsive-check` dan `backups/verify.js.bak_20261009_140520_search-fixture-check`: backup sebelum koreksi pemeriksaan. Masing-masing diverifikasi identik dengan file aktif sebelum edit.

Tidak ada file production yang dimodifikasi; karena itu tidak ada backup production. `git diff --name-only` kosong. Git status hanya menunjukkan folder prototype baru ini. Tidak ada commit atau push.

## Pengujian dan hasil

Runtime browser Chrome melalui Playwright CLI: **47 pemeriksaan lulus**, tersimpan dalam `qa/results.json`.

- Search ukuran 48 px, button desktop 128 px, gap 12 px.
- Pagination target minimal 44 px, Previous/Next, halaman awal/akhir/tengah, summary, dan halaman aktif.
- Pagination halaman tengah muat pada 320 px setelah event responsif selesai.
- Search artikel, reset pagination, hasil kosong dengan aksi pemulihan, filter kategori, search kategori, dan native select mobile.
- Dialog artikel contoh, penutupan dengan Escape, reduced motion, viewer Desktop/Mobile.
- Tidak ada horizontal overflow pada 1440, 1024, 768, 390, 375, dan 320 px, serta landscape 844 × 390.
- Semua gambar lokal termuat pada run final.
- Kontras button putih/hijau sekitar 5.03:1; teks sekunder/canvas sekitar 4.73:1, dihitung dari token warna prototype.

Verifikasi statis: syntax inline JavaScript kedua HTML, `node --check` untuk verify/server, dan `git diff --check` lulus. Screenshot desktop penuh, mobile overview, dan pagination desktop/mobile diperiksa visual.

Diagnostik awal mencatat transfer aset terputus pada server Python dan sesi browser yang tertutup setelah environment berubah. Server diganti dengan Node HTTP streaming lokal, lalu seluruh pemeriksaan dijalankan ulang sampai lulus. Keyword uji diganti dari `systems` menjadi `resilient` karena `systems` juga cocok dengan excerpt cerita Design; perilaku search tidak diubah.

Untuk menjalankan ulang QA, buka browser session lalu jalankan file pemeriksaan dari folder `qa`:

```powershell
npx.cmd --yes --package @playwright/cli playwright-cli -s=article-reading-revamp open http://127.0.0.1:8774/index.html --browser chrome --idle-timeout 0
npx.cmd --yes --package @playwright/cli playwright-cli -s=article-reading-revamp --json run-code --filename verify.js
npx.cmd --yes --package @playwright/cli playwright-cli -s=article-reading-revamp --json eval '() => window.__prototypeQA'
```

## Batas verifikasi dan langkah berikutnya

Yang diverifikasi runtime adalah prototype terpisah dan data fixture. Source Laravel diperiksa secara statis. Manager authenticated, penyimpanan Template Options, dan renderer/public endpoint Laravel belum diuji dalam pekerjaan ini karena source aktif belum diubah.

Langkah berikutnya adalah menerima arahan pengguna terhadap preview, lalu menerapkan perubahan yang dipilih secara scoped pada Minimal Reading List dengan backup source aktif serta verifikasi preview/public rendering. Jangan menganggap prototype ini sudah menjadi template production.
