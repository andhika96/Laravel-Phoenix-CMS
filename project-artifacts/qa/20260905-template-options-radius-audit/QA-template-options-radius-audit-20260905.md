# Template Options preview radius removal and Minimal Reading List audit

Tanggal: 2026-09-05  
Scope: Menghapus radius pada dua wrapper live preview dan mengaudit aliran seluruh form setting Minimal Reading List.

## Hasil utama

- `article-template-manager__device-stage` pada halaman Manage Article Templates tidak lagi memiliki `border-radius`.
- `article-template-options-preview__stage` pada modal Template Options tidak lagi memiliki `border-radius`.
- Kedua iframe preview juga tidak lagi mewarisi radius wrapper.
- Radius modal `.article-template-options-modal .modal-content` dan radius frame milik template tetap dipertahankan. Jadi yang hilang hanya radius container preview, bukan styling konten/template.
- Tidak ada perubahan data, endpoint baru, atau update Graphify.

## Audit alur opsi Minimal Reading List

| Panel | Input manager | Normalizer / preview | Frontend renderer | Status |
|---|---|---|---|---|
| Header content | `header.*.enabled`, `header.*.text` | `ArticleTemplateOptions::archive()` → `archive-header` | Copy eyebrow/title/description dan fallback H1 | Terverifikasi |
| Archive toolbar | Search/category enabled + position | `toolbar.*` + `category.mode` | Zone left/center/right; Button list atau Form select | Terverifikasi |
| Post list | `post_list.item_gap` + unit | normalized dimension | `--article-reading-list-post-gap` → CSS `gap` | Terverifikasi |
| Reading list sidebar | Sidebar, Categories, Popular Posts, Static/Sticky | `sidebar.*` | Panel visibility + `data-article-sidebar-position` → sticky/static CSS | Terverifikasi |
| Thumbnail | Mode, fit, background, frame, Background Height | normalized mode/dimension/frame | Background CSS path atau `<img>` asset path; height hanya background | Terverifikasi |
| Pagination | Total, position, frame, color/width/radius, padding/margin | normalized pagination/frame/box | SSR pagination dan Vue pagination memakai class/style contract yang sama | Terverifikasi |
| Article title | `article_title.tag` | validated H1–H6 | shared `archive-title` partial | Terverifikasi |
| Archive shell | Padding, margin, frame | normalized responsive shell/frame | data attributes + CSS custom properties pada `.article-shell` | Terverifikasi |

## Dua live preview

Page preview dan modal preview tidak memiliki renderer terpisah:

1. Manager page menggunakan `previewUrl`.
2. Modal menggunakan `modalPreviewUrl`.
3. Keduanya dibuat oleh `buildPreviewUrl(surface, template, templateOptions)`.
4. Keduanya menuju `/manage_article/templates/preview/archive/minimal-reading-list`.
5. `ManageArticleTemplateController::preview()` membaca `template_options`, menormalkan melalui `ArticleTemplateOptions`, lalu mengirim `templateView` yang sama ke `manage_article.templates.preview`.

Regression test juga merender hasil normalized `templateView` dengan matrix opsi penuh dan memeriksa dua mode category filter:

- `button-list`: category links/sidebar muncul, toolbar category select tidak muncul.
- `select`: toolbar category select muncul, sidebar category panel tidak muncul.

## Evidence runtime dan fixture

### Preview stage

Fixture: [stage-fixture.html](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-radius-audit/stage-fixture.html)  
Screenshot: [stage-fixture.png](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-radius-audit/stage-fixture.png)

Playwright Chromium pada viewport `900 × 700` mengukur:

- manager stage: `borderRadius: 0px`
- modal stage: `borderRadius: 0px`
- manager iframe: `borderRadius: 0px`
- modal iframe: `borderRadius: 0px`
- document overflow horizontal/vertical: `0`
- console error/warning: `0`

Uji tambahan pada viewport `390 × 844` dan `768 × 900` juga menghasilkan `borderRadius: 0px` untuk kedua stage dan iframe, horizontal/vertical overflow `0`, serta console/page errors `0`.

### Source and integration

- Test stage wrapper radius lulus.
- Test page/modal memakai endpoint renderer yang sama lulus.
- Test PHP matrix Minimal Reading List lulus dengan `72 assertions`.
- `ArticleTemplatePreviewController` tetap memakai fixture read-only; tidak ada Save/Apply dari browser.

## Verification

- `node --test tests/manage-article-template-manager.test.mjs` — `28 passed`.
- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs` — `98 passed`.
- `php artisan test --compact tests/Feature/Article` — `32 passed`, `476 assertions`.
- `php artisan test --compact tests/Feature/Article/ArticleTemplatePreviewControllerTest.php` — `2 passed`, `72 assertions`.
- Browser fixture computed-style check — stage dan iframe `0px`, overflow `0`, console bersih.

## Batas verifikasi

- Session browser authenticated untuk halaman manager tidak tersedia, sehingga click-through langsung pada modal production dan tombol Save/Apply tidak dilakukan.
- Parity source/runtime dibuktikan melalui controller, normalized options, shared Blade renderer, CSS fixture, dan regression tests. Verifikasi authenticated UI langsung tetap perlu dilakukan pada session manager milik pengguna.

## Backup

Backup perubahan sesi ini berada di:

`project-artifacts/backups/20260905_210000-template-options-preview-radius-audit/`

File yang dibackup sebelum perubahan:

- `article-template-manager-2026.css`
- `tests/article-template-presentation.test.mjs`
- `tests/manage-article-template-manager.test.mjs`
- `tests/Feature/Article/ArticleTemplatePreviewControllerTest.php`
- `project-artifacts/plans/20260905-template-options-revamp-plan.md`

## Graphify

Graphify tidak diperbarui sesuai instruksi eksplisit pengguna untuk tidak update data graph selama coding/design.
