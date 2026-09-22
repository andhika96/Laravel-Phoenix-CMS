# Revamp Mosaic Classic dan Balanced Card Grid

Tanggal: 22 September 2026
Project: D:\Laragon\www\laravel-13-phoenix

## Hasil dan scope

Mosaic Classic: lead terbuka dengan foto besar dan judul di bawahnya, sidebar cerita bernomor, divider halus, dan grid lanjutan. Balanced Card Grid: kartu sama besar dalam setiap baris, gambar lebih tinggi, footer tanggal/link konsisten. Keduanya mengikuti aksen tema CMS dan mempertahankan route, data, pagination, typography situs, serta opsi thumbnail/frame/shell yang sudah ada.

Custom Classic (`mosaic_classic`):
- `side_count`: 0–24, default 3; 0 meniadakan sidebar, artikel sesudah lead masuk grid.
- `side_position`: right/left; `lead_ratio`: 67:33, 60:40, 50:50.
- `side_layout`: list/stacked; `side_numbers`, `divider`.
- `lead_height`: desktop22rem/tablet20rem/mobile15rem; `side_height`: 7rem per perangkat.
- `gap`: desktop2rem/tablet1.5rem/mobile1.25rem, termasuk jarak antar side stories.
- Show category, excerpt, date, Read article. Read article default nonaktif untuk Classic.
- Grid lanjutan menggunakan Grid columns 3/2/1, dapat dikustomisasi.

Custom Balanced (`balanced_grid`):
- `card_style`: outline/flat/elevated; `text_align`: left/center.
- `card_padding`: 1.25rem; `card_radius`: 0.75rem.
- Gap responsif 2rem/1.5rem/1.25rem; excerpt lines 1–5 (default 2).
- Show category, excerpt, date, Read article; semua default aktif.
- Grid columns memakai kontrol responsif existing.

Gap, padding, radius mendukung px/em/rem/%/pt. Tinggi lead/side memakai px/em/rem/pt; % sengaja tidak ditawarkan karena containing block memiliki tinggi otomatis. UI menjelaskan batas tersebut.

Search/kategori memanfaatkan shared header dan handler Vue existing. Defaults baru kedua template menyediakan search+select. Preference toolbar lama yang sudah tersimpan tetap dihormati. Classic memakai grid biasa tanpa lead/sidebar pada hasil search/category/tag. Clear filters disediakan pada keduanya.

## Backup dan konfigurasi tersimpan

Backup sebelum perubahan, termasuk perubahan Mosaic Magazine yang masih uncommitted pada shared files:
`project-artifacts/backups/20260922_161732-classic-balanced-revamp/`

Sepuluh file lama disalin dengan struktur path + `.bak`; hash source/backup cocok sebelum edit.

Database configuration backup:
`project-artifacts/backups/20260922_161732-classic-balanced-revamp/article-template-settings-20260922_162910.json`

Hanya dua legacy heights yang diubah dari `5.63rem` ke `13.5rem`: Mosaic Classic dan Balanced Card Grid. Update memakai transaction/row lock, backup diverifikasi sebelum write, lalu row dibaca ulang. Semua nilai lain dan active template `editorial-journal` dipertahankan. Bukti: `thumbnail-settings.json`.

Tidak mengganti legacy custom height lain, tidak mengaktifkan template baru sebagai default, tidak commit/push.

## File yang dimodifikasi

1. app/Support/Article/ArticleTemplateOptions.php — normalizer kedua layout, responsive grid Classic, thumbnail defaults.
2. resources/views/article/templates/archive/mosaic-classic.blade.php — partition, custom attributes, visual dan detail toggles.
3. resources/views/article/templates/archive/balanced-card-grid.blade.php — style, spacing, detail toggles dan footer.
4. resources/views/article/templates/partials/archive-header.blade.php — compact toolbar scoped ke tiga template yang sudah direvamp.
5. resources/views/manage_article/templates/index.blade.php — include panel.
6. public/assets/css/article/article-frontend-2026.css — styling scoped, responsive behavior, theme accents; existing Mosaic Magazine toolbar styling dipakai bersama.
7. public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js — section dan normalisasi draft.
8. public/assets/images/article/template-previews/mosaic-classic.svg
9. public/assets/images/article/template-previews/balanced-card-grid.svg
10. tests/Feature/Article/ArticleTemplateOptionsTest.php — assertion default template tidak berubah dipindahkan ke Minimal Reading List (dua template grid sekarang punya default baru).

Partial baru: resources/views/manage_article/templates/partials/options-classic-balanced.blade.php.

Tes baru di project-artifacts/tests/: ClassicBalancedTest.php, ClassicBalancedPersistenceTest.php, classic-balanced.test.mjs.

## Sumber konteks yang dibaca

- Memori: C:\Users\aruna\.codex\memories\MEMORY.md, konteks normalizer tunggal untuk preview/save/public. E:\AI\Memories tidak tersedia.
- Graphify query `mosaic-classic ArticleTemplateOptions` budget900; graph mengarahkan ke renderer, normalizer, dan controller. Hasil terkait diverifikasi terhadap source aktif; node mockup/unrelated diabaikan. Graphify tidak diperbarui sesuai instruksi manual pengguna.
- Source aktif: dua renderer, normalizer, manager JS/Blade, media-link, archive-header, options-styling, options-mosaic-magazine, ArticleTemplateSetting, ArticleTemplatePreviewFixture, preview view, frontend Vue filter loader, CSS Article, thumbnail SVG, existing Article tests.
- UI UX Pro Max query `editorial content hierarchy --domain ux`: rekomendasi hierarchy/type scale; rekomendasi breadcrumbs tidak relevan untuk perubahan ini dan tidak diterapkan. Design Taste Frontend dipakai untuk mempertahankan warna/typography CMS dan membedakan komposisi kedua template.
- QA harness Mosaic sebelumnya dibaca hanya sebagai alat pemeriksaan, bukan source aplikasi.

## Hasil pengujian akhir

```powershell
php vendor/bin/phpunit tests/Feature/Article project-artifacts/tests/MosaicMagazineTest.php project-artifacts/tests/MosaicMagazinePersistenceTest.php project-artifacts/tests/ClassicBalancedTest.php project-artifacts/tests/ClassicBalancedPersistenceTest.php --cache-directory=project-artifacts/tests/.phpunit-cache
```

**60 tests, 849 assertions, PASS.** Ini mencakup regresi template lain dan persistence melalui SQLite in-memory, bukan database aplikasi. Test baru sebelumnya gagal untuk opsi/markup yang belum ada lalu lulus setelah implementasi.

Node: tests/article*.test.mjs, tests/manage-article-template-manager.test.mjs, project-artifacts/tests/mosaic-magazine.test.mjs, project-artifacts/tests/classic-balanced.test.mjs: **114 tests, PASS**.

PHP lint, Node syntax check, `php artisan view:cache`, dan `git diff --check`: PASS. Public CSS/JS dimuat langsung, tidak ada perubahan bundle Vite/dependency.

Log: phpunit.txt, node.txt.

## Browser runtime

Harness lokal `router.php` merender Blade/CSS/JS aktif dan membaca konfigurasi tersimpan aktual. Data artikel memakai fixture aplikasi; POST ditolak. Preview manager/main/modal serta thumbnail terlihat berubah.

- Classic default desktop1440: kolom765.33px/382.67px, lead352px, sidebar112px, grid thumbnail216px; tidak overflow horizontal.
- Classic mobile390: layout/grid satu kolom, lead240px; tidak overflow.
- Classic tablet768 custom stacked, gap3rem, lead18rem: satu kolom, lead288px; side gap24px + padding24px menghasilkan jarak48px; tidak overflow.
- UI Classic: side_count2, posisi kiri, rasio60:40, lead25rem dan excerpt off diteruskan ke iframe. Computed columns459.20px/688.80px, lead400px, excerpt nodes0. Apply meneruskan konfigurasi ke preview utama.
- Balanced default mobile390: satu kolom, thumbnail216px, excerpt2 lines, tidak overflow.
- Balanced tablet768: dua track348.33px, tidak overflow.
- Balanced desktop1440: tiga track372px; tiga kartu pertama sama tinggi458.99px, tidak overflow.
- UI Balanced: elevated, center, padding2rem, excerpt4 lines diteruskan ke preview. Shadow dan alignment/padding terukur di modal; setelah Apply, perubahan terlihat di preview utama. Dua pembacaan frame via browser tool timeout sementara, bukan console error; render langsung mengonfirmasi clamp4, padding32px, alignmentcenter, tanpa overflow.
- Console manager dan preview diperiksa: tidak ada error/warning.
- Viewport override direset dan tab QA ditutup setelah verifikasi.

## Review dan batas

Reviewer terpisah (GPT-5.6 Terra High) memeriksa diff terhadap backup awal turn. Dua temuan diperbaiki: unit persen untuk gap/padding/radius dan gap side-story responsif. Verifikasi reviewer akhir tidak menemukan isu tambahan.

Browser QA menggunakan harness lokal, bukan sesi Firefox terautentikasi pengguna. Database Save diuji pada SQLite. Tidak seluruh kombinasi font/theme/custom units diperiksa visual. Legacy preference toolbar tetap berlaku, sehingga search/kategori yang sebelumnya disimpan disabled tetap disabled sampai diubah di Archive toolbar.

Graph tidak diperbarui. Tidak ada reset/clean/stage/commit/push. Perubahan Mosaic Magazine dan artifact sebelumnya dipertahankan.

## Cara melihat

Ctrl+F5 pada Manage Article Templates, pilih Mosaic Classic atau Balanced Card Grid. Template Options menampilkan section bernama sesuai template; Grid columns dan Thumbnail tetap tersedia. Apply changes memperbarui draft preview, Save Template mengikuti alur aplikasi existing.
