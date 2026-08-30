# QA Report 07 — Exact Visual Static HTML Preview

Tanggal: 2026-08-28  
Fixture: `E:\Apps\Laragon\www\ceo-masters\index.html`

## Implementasi

- Modul tersembunyi `static_html` ditambahkan ke katalog v2.4.
- Toolbar import mempunyai mode `Exact Visual` dan `Editable Native`.
- Exact mode menyimpan sanitized source DOM dalam `srcdoc`.
- Iframe menggunakan `sandbox="allow-scripts"` tanpa same-origin, form, popup, atau top-navigation permission.
- CSS framework dan custom CSS berjalan di dalam iframe.
- Tailwind config custom, Google Fonts, Bootstrap 5, Phosphor regular stylesheet, dan URL gambar absolut disaring/diikutkan sesuai allowlist.
- Script sumber arbitrary dihapus.
- ResizeObserver di dalam iframe mengirim tinggi terukur ke parent melalui `postMessage`; runtime parent membatasi tinggi 320–30000 px.
- Mode native tetap tersedia dan service API default tetap `native`.

## Browser preview evidence

- Exact iframe tinggi akhir: **8.525 px**.
- Horizontal overflow parent: **0 px**.
- Phosphor stylesheet terdeteksi: ya.
- Class Phosphor di source DOM: 34.
- Tailwind source loader/config: hadir di srcdoc.
- Source DOM tetap memuat struktur `<main>`, heading, navigasi, CTA, dan section asli.
- Gambar URL absolut: dipertahankan.
- Gambar relatif seperti `ceo-masters-poster.jpg`: tetap gagal tampil karena belum ada Base URL/File Manager; ini sesuai scope.

## Test evidence

- Focused PHPUnit import/CSS/frontend: **25 passed, 139 assertions**.
- Module/catalog/isolation/parser PHPUnit: **36 passed, 8.653 assertions** pada checkpoint sebelumnya; setelah static module final, kontrak terkait tetap lulus.
- Full Node v2.4: **407 passed, 0 failed**.
- Full PHPUnit filter v2.4: **162 passed, 33 failed**; seluruh 33 menerima HTTP 419 CSRF sebelum business assertion, tidak ada failure non-419 baru.
- `git diff --check`: lulus.
- PHP/Blade/definition/runtime syntax checks: lulus.

## Backup dan graph

- Backup bertimestamp dibuat sebelum setiap perubahan existing source/test.
- Graphify diperbarui incremental setelah implementasi static module; backup, QA, dan graph dikecualikan oleh `.graphifyignore`.

## Belum diverifikasi

- Editor authenticated langsung belum diuji karena browser automation tidak memiliki sesi login; tidak ada kredensial, Save, Reset, atau deploy yang dilakukan.
- Base URL resolver untuk relative asset belum dibuat.
