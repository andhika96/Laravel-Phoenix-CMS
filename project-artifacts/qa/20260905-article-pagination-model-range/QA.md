# Article pagination model and responsive range — 2026-09-05

## Scope

- Menambahkan tiga model pagination bernama: `Minimal Underline`, `Classic Boxed`, dan `Soft Highlight`.
- Menambahkan range pagination terpisah untuk Desktop, Tablet, dan Mobile.
- Menjaga pilihan model dan range tetap konsisten pada manager preview, modal preview, SSR, Vue pagination, dan frontend archive.

## Model contract

| Label UI | Value | Visual behavior |
|---|---|---|
| Minimal Underline | `underline` | Link ringan, active page memakai underline theme accent |
| Classic Boxed | `boxed` | Setiap control memiliki border; active page memakai filled accent |
| Soft Highlight | `soft` | Tanpa border penuh; active page memakai accent surface |

Normalized option:

```json
{
  "pagination": {
    "type": "boxed",
    "range": {
      "desktop": 3,
      "tablet": 3,
      "mobile": 2
    }
  }
}
```

Range dibatasi `1–9` dan model invalid kembali ke `boxed`.

## Runtime behavior

- Vue Article frontend memilih `pagination.range.desktop/tablet/mobile` berdasarkan viewport `desktop >= 992`, `tablet 576–991`, dan `mobile <= 575`.
- Resize viewport memperbarui range tanpa mengubah URL atau filter.
- Manager page preview dan modal preview menambahkan `preview_device` ke preview URL ketika device diganti, sehingga SSR fixture memakai range device yang sedang dipreview.
- SSR pagination mengeluarkan model class dan semua range sebagai data attributes.
- Vue pagination memakai model class dan `page-range` responsive dari option yang sama.

## Visual QA

Fixture: [pagination-fixture.html](./pagination-fixture.html)

- [Desktop 1440](./desktop-1440.png)
- [Tablet 768](./tablet-768.png)
- [Mobile 390](./mobile-390.png)

Playwright Chromium mengukur ketiga model pada semua viewport:

- `underline`: transparent active background + theme underline.
- `boxed`: bordered links + filled active background.
- `soft`: transparent links + accent-surface active background.
- Horizontal/vertical overflow: `0` pada seluruh viewport.
- Console errors/warnings: `0`.

## Tests

- `node --test tests/article-frontend-pagination.test.mjs tests/manage-article-template-manager.test.mjs` — `45 passed`.
- `php artisan test --compact tests/Feature/Article/ArticleTemplateOptionsTest.php tests/Feature/Article/ArticleTemplatePreviewControllerTest.php` — `9 passed`, `143 assertions`.
- `node --check` untuk Vue manager dan Article frontend — lulus.
- Preview controller test memastikan `preview_device=mobile` memakai range mobile dan model yang dipilih.

## Graphify

Graphify tidak di-update dalam task ini. Pembaruan Graphify tetap manual dan menunggu instruksi eksplisit pengguna.
