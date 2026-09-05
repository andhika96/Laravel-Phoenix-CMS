# Minimal Reading List implementation QA

Tanggal: 2026-09-05

## Scope

- Menerapkan desain blog list dua kolom: article stream utama, Categories, dan Popular Posts.
- Menjaga SSR renderer, Vue `listData`, search, category filter, thumbnail fallback, dan SSR/Vue pagination.
- Menyinkronkan live preview Manage Article Templates dengan renderer dan CSS yang sama.
- Menambahkan option Minimal-specific untuk menampilkan sidebar, Categories, dan Popular Posts.
- Memperbaiki search toolbar agar tidak lagi terjebak pada tiga kolom generik.

## Files modified

- `app/Http/Controllers/Web/Article/ArticleFrontendController.php`
- `app/Http/Controllers/Web/Manage_Article/ManageArticleTemplateController.php`
- `app/Support/Article/ArticleTemplateOptions.php`
- `public/assets/css/article/article-frontend-2026.css`
- `public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`
- `resources/views/article/templates/archive/minimal-reading-list.blade.php`
- `resources/views/article/templates/partials/archive-header.blade.php`
- `resources/views/manage_article/templates/index.blade.php`
- `resources/views/manage_article/templates/preview.blade.php`
- `tests/Feature/Article/ArticleTemplateOptionsTest.php`
- `tests/Feature/Article/ArticleMinimalReadingListHeaderRenderTest.php`
- `tests/Feature/Article/ArticleMinimalReadingListRenderTest.php`
- `tests/article-minimal-reading-list-header.test.mjs`
- `tests/article-minimal-reading-list-option-scope.test.mjs`
- `tests/article-minimal-reading-list-sidebar.test.mjs`

## Backup

Backup terverifikasi SHA-256 di `project-artifacts/backups/20260905_article_minimal_reading_list_implementation/`:

- `ArticleFrontendController.php.bak_20260905_033518_minimal_reading_list` — `C9901284EDFFDC9E356AA4E8E374E5343CD4B59810B2C0B5F7EE0A9CD08E9DFC`
- `minimal-reading-list.blade.php.bak_20260905_033518_minimal_reading_list` — `B0DEF7B270D90796DA9A7FF7AFB632EE420817312A281248920BC304B2F1B1C2`
- `manage-article-template-preview.blade.php.bak_20260905_033518_minimal_reading_list` — `E48BB134A1CCC3299CD53C6E34C134EA482CFC6E3D02A538EA7EC1A5D4339B70`
- `article-frontend-2026.css.bak_20260905_033518_minimal_reading_list` — `EA5A96EF68DC6A1AD51E1CCA7F7D971ACAB642D11D18B8EFB65637B2CBE04F95`
- `ManageArticleTemplateController.php.bak_20260905_033924_minimal_reading_list` — `5A90C825AA742CBF0AAB293CA59CE455BDF81176BBCE6ADB02CA346982ABBD14`
- `ArticleTemplateOptions.php.bak_20260905_035858_minimal_reading_list_options` — `E30A78781508A70721E1353602D8C46C579FC3D61A8492905016668A0329E5CA`
- `manage-article-templates-index.blade.php.bak_20260905_035858_minimal_reading_list_options` — `167D750BA4C6926EAAE4784A3C15A7107EA71D186E37ACF34001FD2E7BF88665`
- `vueV3-manage-article-templates-2026.js.bak_20260905_035858_minimal_reading_list_options` — `9276A6AA8F150847804B958A20542CC5DABA394AEC674469D60C9A2CCCC127B1`
- `ArticleTemplateOptionsTest.php.bak_20260905_035858_minimal_reading_list_options` — `6D29F3816C66A673432683D81D2ABBC0C18AF2FC5AFBCF1A5CD803AED82A3B13`
- `ManageArticleTemplateController.php.bak_20260905_041717_minimal_reading_list_scope_preserve` — `DC3370A77566DCE65DFDD71CFA7D651FEE21874C2219E151F696FB9E4AC256BB`
- `archive-header.blade.php.bak_20260905_042033_minimal_reading_list_header` — `BAD785EB939C454BDA9EC10D924743716A03A87B0A81CFD8B2CA8AF1CFB3AFBC`
- `article-frontend-2026.css.bak_20260905_042033_minimal_reading_list_header` — `89AAFC7FD124A2EEB5A212BCB25540AD2882CCA6AF4B981974AACCDB18A934B3`
- `vueV3-manage-article-templates-2026.js.bak_20260905_042920_minimal_reading_list_option_scope` — `79FB21298EEF1583EDEE437EC41F76B6616C88AFB888C922DACC829D55642345`
- `QA.md.bak_20260905_043207_minimal_reading_list_resume` — `284B403DF34AADF9FA824E2D981D75A4EFD4A3C37B44D2D718B7956FE241F4CB`
- `QA.md.bak_20260905_043642_minimal_reading_list_final` — `977F5089179B69D985542DB36F30DE93CFC5A1A4CE4B76A6F7024411198B1101`

## Verification

### Static and PHP

- `node --test tests/article-minimal-reading-list-header.test.mjs tests/article-minimal-reading-list-option-scope.test.mjs tests/article-minimal-reading-list-sidebar.test.mjs tests/article-template-presentation.test.mjs tests/article-frontend-pagination.test.mjs tests/article-template-preview-fixture.test.mjs tests/manage-article-template-manager.test.mjs` — 52 passed.
- `php artisan test tests/Feature/Article/ArticleMinimalReadingListHeaderRenderTest.php tests/Feature/Article/ArticleMinimalReadingListRenderTest.php tests/Feature/Article/ArticleTemplateOptionsTest.php tests/Feature/Article/ArticleTemplateRenderTest.php tests/Feature/Article/ArticleTemplatePreviewControllerTest.php` — 14 passed, 232 assertions.
- `php artisan test tests/Feature/Article/ArticleTemplateRenderTest.php tests/Feature/Article/ArticleTemplatePreviewControllerTest.php` — 8 passed, 163 assertions.
- `php artisan test tests/Feature/Article/ArticleFrontendRouteTest.php --filter=guest_can_open_archive_detail_and_vue_listdata_while_only_eligible_articles_are_exposed` — 1 passed, 34 assertions.
- `php -l` untuk tiga controller/normalizer aktif — lulus.
- `node --check` untuk Article frontend dan Manage Template JS — lulus.
- `php artisan view:cache` — lulus.
- `git diff --check` — lulus; warning CRLF hanya berasal dari perubahan pre-existing pada file theme lain.

Full `php artisan test tests/Feature/Article --testdox` menghasilkan 26 passed dan 1 failure pada test password unlock yang menerima HTTP 419 CSRF, bukan jalur Article archive/template yang diubah. Jalur archive, render, preview, option, pagination, dan query seluruhnya lulus.

Header render test juga memastikan ketika tiga toggle Header content aktif, `Eyebrow`, `Title`, dan `Description` muncul dalam shared header sebelum `article-template-toolbar`. Runtime public saat ini memiliki ketiga toggle tersebut dalam keadaan tersimpan `false`, sehingga header tetap tersembunyi sesuai kontrak option; tidak ada setting yang diubah otomatis.

### Runtime public archive

Read-only browser QA pada `https://laravel-13-phoenix.aruna/article`:

- Desktop 1280x720: layout `796px + 320px`, gap `64px`, 12 rows, 8 category links, 4 popular items.
- Search control final saat setting Search berada di `center`: input tetap `569.45px`, tombol `94.55px`, tinggi keduanya `48px`; saat posisi `right` juga sudah diverifikasi tetap proporsional.
- Pagination active: background transparan, text `rgb(23, 32, 51)`, tinggi `44px`, `aria-current="page"`.
- Page 2 berubah ke `?page=2` dan menampilkan item 13–24.
- Category link berubah ke `?category=11` dan menghasilkan 715 artikel yang sesuai.
- Search + category berubah ke `?search=Performance+Insight+%2300022&category=11` dan menghasilkan 1 artikel; total heading ikut berubah.
- Body `overflow-y: auto`, document scroll height sekitar 3330px, horizontal overflow `false`.
- Console errors/warnings: tidak ada.

Manager live preview read-only diarahkan ke `/auth/login` pada in-app browser karena tidak ada session manager yang tersedia. Tidak ada credential yang dimasukkan dan tidak ada Save/Apply yang dijalankan.

### Runtime mobile

Read-only viewport override 390x844 lalu dikembalikan ke desktop:

- Layout menjadi satu kolom `342.667px`.
- Row memakai `96px + 232.271px` dan thumbnail `96px x 96px`.
- Sidebar berubah dari `sticky` menjadi `static`.
- Pagination active tetap 44px dan ber-`aria-current`.
- Horizontal overflow `false`.
- Console errors/warnings: tidak ada.

### Manager preview

- Controller preview memakai fixture articles dan fixture categories untuk sidebar Minimal; renderer preview memuat `article-frontend-2026.css` yang sama dengan public archive.
- `ArticleTemplateOptions` normalizer dan manager draft mengenali `sidebar.enabled`, `sidebar.categories.enabled`, dan `sidebar.popular.enabled`.
- Manager hanya mematerialisasi option sidebar untuk `minimal-reading-list`; archive template lain tetap memakai option contract sebelumnya.
- Preview fixture dan render test lulus.
- Live authenticated Manager UI tidak dibuka ulang melalui browser QA karena session manager tidak tersedia pada in-app browser; parity diverifikasi melalui controller/view/fixture tests dan shared renderer/CSS path.

## Data decision

Tabel `articles` belum memiliki popularity metric. Panel `Popular Posts` saat ini memakai empat artikel publish terbaru sebagai fallback deterministic. Ranking popularitas nyata ditunda sampai ada product metric yang resmi.

## Graphify

Graphify di-update incremental setelah setiap batch source berubah. Kondisi terakhir: 21.678 nodes, 37.562 edges, 1.590 communities. `graph.html` dilewati otomatis karena graph melebihi batas visualisasi 5.000 nodes.
