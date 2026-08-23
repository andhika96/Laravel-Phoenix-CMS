# Product Lead Form — Hover and Selected Preview

Tanggal: 2026-08-23

## Perubahan

- Settings state tabs Normal/Hover/Selected sekarang mengirim preview event ke Canvas.
- Canvas menampilkan state Hover/Selected secara langsung saat tab terkait dipilih.
- Hover dan Selected memiliki border width four-sides tersendiri, selain border color/background/shadow.
- State preview juga diterapkan ke media wrapper ketika label berada di luar thumbnail.

## Verification

- Product Lead Form Node suite: 10 passed.
- Full v2.4 Node suites: 388 passed.
- Full v2.4 PHP Feature+Unit suites: 154 passed, 10,307 assertions.
- Control audit: 50 modules, 1,795 controls, 0 consumerless.
- Vite build: 58 modules passed.
- PHP/JS syntax and `git diff --check`: passed.

Graphify incremental: 20,045 nodes and 36,997 links; diagnostic 0 missing endpoints, 0 self-loops, 2,068 dangling external-reference endpoints.
