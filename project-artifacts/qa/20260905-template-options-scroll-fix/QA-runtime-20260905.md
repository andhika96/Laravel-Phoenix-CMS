# QA Runtime — Template Options scroll correction

Tanggal: 2026-09-05

## Scope

Memastikan panel settings pada modal Template Options dapat discroll secara independen setelah density form Pagination, Archive shell, Thumbnail, dan panel lain bertambah.

## Root cause

Markup produksi menempatkan class `article-template-options-modal` pada elemen `.modal-dialog`, sedangkan aturan layout memakai selector descendant seperti `.article-template-options-modal .modal-dialog`. Karena tidak ada `.modal-dialog` descendant di dalam dirinya sendiri, rule tinggi dialog tidak pernah match. Akibatnya `modal-body` dan seluruh settings panel tumbuh mengikuti konten; `overflow-y: auto` pada panel tidak memperoleh area scroll.

Fixture lama tidak menangkap masalah ini karena memakai struktur berbeda dan override `.fixture-*` yang memaksa tinggi parent.

## Perubahan

- `resources/views/manage_article/templates/index.blade.php`
  - memindahkan `article-template-options-modal` ke wrapper `.modal`;
  - mengembalikan `.modal-dialog` ke struktur Bootstrap normal agar selector descendant CSS match;
  - tidak menambah JavaScript, body scroll lock, atau scroll-jacking.
- `tests/article-template-presentation.test.mjs`
  - menambahkan regression test untuk memastikan sizing hook berada pada wrapper dan tidak lagi berada pada `.modal-dialog`.
- `project-artifacts/qa/20260905-template-options-scroll-fix/scroll-fixture.html`
  - disamakan dengan struktur modal produksi dan memuat Bootstrap serta CSS manager aktif.

## Browser evidence

Fixture dijalankan melalui project root sebagai docroot sehingga asset produksi benar-benar termuat.

### Desktop 1440 × 900

- CSS aktif: Bootstrap custom 5.3.6 dan `article-template-manager-2026.css`.
- Settings panel: `clientHeight=741`, `scrollHeight=1362`.
- Programmatic scroll: `scrollTop 0 → 621.33`.
- Mouse wheel pada panel juga mengubah `scrollTop 0 → 621.33`.
- Horizontal overflow: `false`.
- Console: 0 error, 0 warning.
- Screenshot: `scroll-fixture-production-1440.png`.

### Mobile 390 × 844

- Settings panel: `clientHeight=723`, `scrollHeight=1364`.
- Programmatic scroll: `scrollTop 0 → 641.33`.
- Horizontal overflow: `false`.
- Screenshot: `scroll-fixture-production-390.png`.

### A/B root-cause check

- Hook pada `.modal-dialog` seperti kondisi bug: `panelClientHeight=1362`, `panelScrollHeight=1362`, `canScroll=false`.
- Hook pada wrapper `.modal` setelah koreksi: `panelClientHeight=741`, `panelScrollHeight=1362`, `canScroll=true`.

## Automated verification

- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs`: **83 passed, 0 failed**.
- `php artisan test --compact tests/Feature/Article`: **30 passed, 411 assertions**.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`: **passed**.
- `php artisan view:cache`: **passed**.
- `git diff --check` pada file scope: **passed**.

## Runtime boundary

Halaman manager live belum dapat diuji langsung pada sesi browser ini karena route `/manage_article/templates` mengarah ke `/auth/login` tanpa kredensial. Tidak ada kredensial yang dimasukkan dan tidak ada Apply/Save yang dijalankan. Setelah deploy/reload asset, lakukan hard reload pada browser manager lalu buka setiap section untuk memastikan wheel/track scroll bergerak.

## Graphify

Graphify **tidak dijalankan dan tidak diubah** untuk koreksi ini sesuai instruksi eksplisit pengguna agar coding/design tidak memperbarui data graph.

## Backup

- `project-artifacts/backups/20260905_160325-template-options-scroll-runtime/`
- `project-artifacts/backups/20260905_160618-template-options-scroll-runtime-fixture/`
- `project-artifacts/backups/20260905_161047-template-options-scroll-plan-qa/`
