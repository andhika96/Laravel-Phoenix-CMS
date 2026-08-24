# Article Template Visual Correction QA

Tanggal: 2026-08-24  
Scope: Manage Article Templates dan frontend Article saja. Manage Event tidak disentuh.

## Defect yang direproduksi

1. Katalog Archive hanya mendaftarkan empat template.
2. Kartu template di panel kiri hanya memakai icon, bukan thumbnail preview.
3. Preview Archive dan Detail mengosongkan media ketika artikel tidak memiliki thumbnail.
4. CSS Article memakai `Georgia` dan ukuran heading independen, sehingga tidak mengikuti Site Config typography CMS.

## Perbaikan yang diverifikasi

- Archive sekarang berurutan: Minimal Reading List, Editorial Journal, Mosaic Magazine, Mosaic Classic, Balanced Card Grid.
- Mosaic Classic memiliki Blade Archive sendiri dan terdaftar dalam allowlist katalog.
- Sembilan aset SVG lokal ditambahkan: delapan thumbnail desain template dan satu placeholder artikel netral tanpa konten/watermark eksternal.
- Kartu kiri merender thumbnail desain; event `v-on:error` mengganti aset gagal dengan placeholder lokal. Bentuk `@error` sengaja tidak digunakan karena itu adalah directive Blade.
- Seluruh Archive dan Detail template memakai fallback placeholder saat file thumbnail Article tidak ada.
- Frontend Article dan iframe preview memuat font aktif Site Config serta `theme-responsive-typography.css`; heading memakai token RFS CMS, bukan ukuran `Georgia`/`clamp()` sendiri.

## Follow-up: device preview yang skalabel

- Root cause: device button sebelumnya hanya memberi `max-width` pada iframe. Iframe tetap memakai ukuran panel, sehingga Desktop/Detail tampak seperti konten besar biasa dan pengguna tidak tahu ukuran device yang sedang disimulasikan.
- Preview sekarang memakai viewport virtual eksplisit: Desktop `1440×900`, Tablet `834×1112`, Mobile `390×844`.
- Stage menghitung scale dari lebar panel dan tinggi maksimum `640px`, lalu menampilkan seluruh frame dengan transform. Ini menjaga dimensi internal iframe tetap benar sekaligus memuat preview di panel.
- Label aktif menampilkan nama device, resolusi, dan `Scaled to fit`; tombol memakai `aria-pressed`.
- Shell yang sama dipakai Archive dan Detail, sehingga perubahan device berlaku konsisten pada keduanya.

## Automated QA

- RED pertama: katalog/placeholder/token typography/kartu thumbnail belum tersedia; test gagal sesuai ekspektasi.
- Focused PHP: `php artisan test tests/Feature/Article` — 8 passed, 70 assertions.
- Focused Node: Article frontend/template manager/thumbnail checks — 14 passed.
- PHP lint untuk `ArticleTemplateCatalog` dan `ManageArticleTemplateController` — passed.
- `node --check` untuk Vue template manager — passed.
- `php artisan view:clear` dan `php artisan view:cache` — passed.
- `git diff --check` — passed.
- Full PHP: 706 passed, 1 failure historis di `Tests\Feature\PageBuilderElementorV23ShellTest` (expected 200, runtime mendapat 302 pada shell v2.3), tidak terkait file Article yang diubah.
- Full Node dijalankan; suite global masih memiliki kegagalan statis historis Aurora/Page Builder di luar scope. Seluruh 11 check Article terkait lulus.
- Independent read-only review: tidak ada temuan P1/P2. Temuan P3 tentang Mosaic Classic yang hanya dicek sebagai view terdaftar telah ditutup dengan render test nyata memakai placeholder tanpa thumbnail.

## Browser QA read-only

URL: `https://laravel-13-phoenix.aruna/manage_article/templates`

- Hard reload berhasil tanpa console error pada tab akhir.
- DOM runtime menampilkan lima kartu Archive sesuai urutan dan lima thumbnail desain dengan `loaded: true`.
- Preview Minimal Reading List memuat enam gambar; artikel tanpa thumbnail memakai `article-image-placeholder.svg` dan gambar tersebut `loaded: true`.
- Preview Mosaic Classic dipilih tanpa Save, merender heading `Mosaic Classic`, enam gambar, dan fallback placeholder termuat.
- Preview Detail Focused Reader menampilkan cover `<img>` untuk Article yang tidak memiliki thumbnail.
- Computed CSS manager menggunakan font aktif Site Config (`Noto Sans` pada runtime saat QA) dan ukuran adaptif 12.5px; iframe heading menggunakan font yang sama.
- Device runtime: Desktop memiliki layout iframe `1440×900` dan visual stage diskalakan untuk muat; Detail mempertahankan Tablet `834×1112`; Detail Mobile memiliki iframe `390×844` dan visual stage `296×640` pada panel QA.

Keterbatasan capture: API screenshot in-app browser gagal meng-capture halaman sesudah perubahan dan fallback CDP melebihi batas waktu. Karena itu bukti browser akhir berupa DOM runtime, asset-load, computed-style, dan console log read-only; screenshot baseline tersimpan di `project-artifacts/qa/20260824_article-template-manager-before-visual-correction.png`.

## Graphify

Incremental update berhasil dengan `--code-only` karena extraction docs/images memerlukan LLM key yang tidak tersedia. Graph aktif diperbarui kembali setelah test follow-up terakhir.
