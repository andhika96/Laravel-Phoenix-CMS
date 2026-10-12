# Editorial Journal: preview revamp responsive

Tanggal: 11 Oktober 2026, Asia/Jakarta. Project: Laravel 13 Phoenix.

Status: preview terpisah selesai dan diperiksa visual/browser. Source serta settings aplikasi belum diubah.

## Membuka hasil

- Viewer Desktop / Tablet / Mobile: http://127.0.0.1:8775/preview.html
- Ukuran browser sebenarnya: http://127.0.0.1:8775/index.html
- `index.html` dapat dibuka dari file lokal, dengan seluruh aset yang tersedia dalam folder ini. Viewer iframe paling tepat dibuka melalui server lokal.
- Screenshot: `qa/desktop-full.png`, `qa/mobile.png`, `qa/mobile-full.png`, `qa/mobile-card.png`, dan `qa/tablet.png`.

Server native Node hanya bind ke 127.0.0.1 dan menyajikan folder preview ini. Untuk menjalankan ulang:

```powershell
node 'D:\Laragon\www\laravel-13-phoenix\project-artifacts\mockups\20261011_editorial-journal-revamp\qa\server.cjs'
```

## Arah desain

Mempertahankan Editorial Journal modern sans-serif: artikel unggulan dengan teks di kiri dan gambar besar di kanan, diikuti grid artikel. Revamp merapikan whitespace, ukuran/proposi thumbnail, hierarki judul, chip kategori, excerpt, tanggal, dan Read more.

- Desktop: artikel unggulan split layout, grid tiga kolom.
- Tablet: gambar unggulan di atas teks, grid dua kolom.
- Mobile: gambar unggulan di atas teks, grid satu kolom dengan thumbnail penuh dan isi di bawahnya.
- Typography memakai kontrak frontend CMS. Font yang terbaca dari runtime adalah Noto Sans 14 px; compact density CMS tetap berlaku. Body/control menggunakan ukuran inherit; heading mengikuti token RFS CMS.
- Search input/button minimal 48 px dan gap default 12 px; field kategori menyesuaikan lebar mobile.
- Pagination memperlihatkan model Boxed yang sudah tersedia, target 44 px, dan footer frame OFF untuk preview ini.

Untuk memperlihatkan desain kontrol yang memang sudah didukung template, profil preview mengaktifkan search, category select, dan Read more pada lead/grid. Ini pilihan lokal prototype; tidak mengubah default atau nilai tersimpan aplikasi. Aksen hijau dalam preview memakai tone lebih gelap untuk keterbacaan.

Search/filter bekerja terhadap enam cerita contoh yang tampil. Read more membuka dialog native berisi ringkasan contoh; Escape menutupnya. Pagination merupakan contoh visual halaman pertama, tanpa backend navigation. Data berasal dari fixture editorial yang sudah ada.

## Source dan memori

Source aktif yang dibaca/dipakai:

- `resources/views/article/templates/archive/editorial-journal.blade.php`
- Shared archive-header, media-link, archive-title, template-shell-attributes, dan pagination yang di-include renderer.
- `public/assets/css/article/article-frontend-2026.css`
- `public/assets/css/theme-responsive-typography.css`
- `app/Support/Article/ArticleTemplateOptions.php`
- `app/Support/Article/ArticleTemplatePreviewFixture.php`
- Renderer fixture dan aset lokal pada pekerjaan Minimal Reading List sebagai pola workflow, bukan source pengganti aplikasi.

Drive E tidak tersedia. Registry `C:\Users\aruna\.codex\memories\MEMORY.md:2343-2356` digunakan untuk konteks preview-before-source, mempertahankan modern sans-serif, dan kontrak settings Editorial Journal. Seluruh konteks tersebut diperiksa lagi terhadap source aktif.

Graphify query: `editorial journal article template`, budget 1400; explain `editorial-journal.blade.php`. Lima relasi include `EXTRACTED` menunjukkan shell/header/media/title/pagination. Node query generik yang tidak relevan tidak dipakai. Graph tidak diperbarui; detail renderer dan CSS diverifikasi langsung pada file aktif.

## File yang dibuat

- `render.php`: merender Blade/normalizer aktif dengan profil preview dan CMS typography. Helper translation terisolasi agar preview tidak menambah record bahasa.
- `preview.css`: style revamp scoped Editorial Journal, hanya dimuat pada prototype.
- `preview.js`: interaksi search/filter dan native dialog contoh.
- `index.html`: hasil renderer lokal dengan snapshot CSS frontend aktif.
- `preview.html`: viewer pergantian lebar Desktop / Tablet / Mobile.
- `assets/`: salinan Bootstrap, Font Awesome, font Noto Sans, dan enam foto fixture lokal.
- `qa/server.cjs`, `qa/verify.js`, `qa/results.json`, screenshot, dan snapshot CLI.

Tidak ada source aplikasi yang dimodifikasi dan tidak memerlukan backup source baru. Ketujuh file source yang sebelumnya sudah berubah diverifikasi SHA-256 sama dengan checkpoint awal di `qa/source-checkpoint.json`; pekerjaan Minimal Reading List tetap dipertahankan.

## Verifikasi

**31 check browser lulus** pada Chrome melalui Playwright CLI; hasil di `qa/results.json`.

- Tidak ada horizontal overflow pada 1440, 1024, 768, 390, 375, 320 px.
- Grid 3/3/2/1/1/1 kolom pada lebar tersebut.
- Desktop lead split; tablet/mobile lead image di atas teks.
- Search alignment, target 48 px, gap 12 px, typography Noto Sans/CMS, dan perubahan font CMS diperiksa.
- Search, hasil kosong, category filter, dialog native, Escape, reduced motion, serta viewer device switch diperiksa.
- Seluruh resource lokal termuat tanpa failure.
- PHP render syntax, JS syntax, dan git diff --check lulus.
- Screenshot desktop penuh, mobile overview, dan kartu mobile diperiksa visual.

Keterbatasan: ini preview desain dengan fixture lokal, bukan pengujian authenticated Manager Apply/Save atau public navigation aplikasi. Tidak ada perubahan database/settings, dependency aplikasi, commit/push, atau update Graphify.
