# QA — Template Options compact control density

Tanggal: 2026-09-05

## Scope

Memadatkan seluruh form settings Template Options tanpa mengubah state Vue, normalizer, binding unit, control link/unlink, atau fungsi Apply/Save.

## Design decision

Mengikuti ritme compact Page Builder tanpa menurunkan form modal menjadi ukuran panel editor yang terlalu kecil:

- input, select, unit control, dan link control: **40px**;
- segmented/action/footer button: **38px**;
- unit selector: **76px** (`4.75rem`);
- gap antar option group: **12px** pada source aktif;
- switch dipertahankan pada **56 × 28px** karena merupakan dimensi yang sebelumnya sudah disetujui dan tetap jelas untuk state on/off;
- font body/label tidak diperkecil lagi agar keterbacaan tetap terjaga.

## Production changes

`public/assets/css/article/article-template-manager-2026.css` sekarang memiliki scope token khusus:

- `--article-template-control-height: 2.5rem`;
- `--article-template-action-height: 2.375rem`;
- `--article-template-unit-width: 4.75rem`;
- `--article-template-link-width: 2.375rem`.

Token diterapkan pada Header content, Archive toolbar, Post list, Reading list sidebar, Thumbnail, Pagination, Article title, Archive shell, four-side radius, box spacing, unit selector, Coloris field, segmented control, device control, dan footer actions.

## Mockup synchronization

`project-artifacts/mockups/template-options-20260905/forms-v3/index.html` diselaraskan menggunakan `--control-v3: 40px` dan `--action-v3: 38px`. Padding/gap form group dan conditional disclosure juga dipadatkan.

Semua sembilan screenshot state desktop diregenerasi:

- `01-header-content.png`
- `02-toolbar-category-off.png`
- `03-toolbar-button-list.png`
- `04-toolbar-form-select.png`
- `05-post-list.png`
- `06-reading-list-sidebar.png`
- `07-thumbnail.png`
- `08-pagination.png`
- `09-archive-shell.png`

## Browser evidence

Dengan Bootstrap dan CSS produksi aktif pada fixture yang memakai struktur modal production:

- desktop `1440 × 900`: input/select/link **40px**, position button **38px**, switch **28px**, horizontal overflow `false`;
- mobile `390 × 844`: input/select/link **40px**, position button **38px**, horizontal overflow `false`;
- scroll regression setelah compact pass:
  - desktop `clientHeight=741`, `scrollHeight=1349`, `scrollTop 0 → 608.67`;
  - mobile `clientHeight=723`, `scrollHeight=1356`, `canScroll=true`;
- browser console: **0 error, 0 warning**.

## Automated verification

- Red phase: test compact-density baru gagal ketika source masih memakai `2.75rem` / 44px.
- Green phase: `node --test tests/article-template-presentation.test.mjs` → **22 passed, 0 failed**.
- Full Node suite: `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs` → **84 passed, 0 failed**.
- `php artisan test --compact tests/Feature/Article` → **30 passed, 411 assertions**.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js` → **passed**.
- `php artisan view:cache` → **passed**.
- scoped `git diff --check` → **passed**.

## Runtime boundary

Manager live tetap tidak diuji langsung karena browser QA tidak memiliki sesi login untuk `/manage_article/templates`. Tidak ada kredensial, Apply, atau Save yang dijalankan. Hasil browser di atas memakai CSS produksi aktif dan struktur modal production; validasi live authenticated tetap diperlukan untuk click-through akhir.

## Graphify

Graphify tidak dijalankan dan data graph tidak diubah, sesuai instruksi pengguna.

## Backup

- `project-artifacts/backups/20260905_163356-template-options-compact-density/`
- `project-artifacts/backups/20260905_164007-template-options-compact-density-screens/`
- `project-artifacts/backups/20260905_164211-template-options-compact-density-qa/`
