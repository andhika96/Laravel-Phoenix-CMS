# Arunika Prism mobile profile pointer fix

Tanggal: 2026-09-04

## Root cause

Rule legacy `.ph-top-bar .dropdown-menu:not(.dropdown-menu-end)::before/after` menetapkan `left: 7px`. Profile sheet Prism mobile memakai posisi fixed tanpa class `dropdown-menu-end`, sehingga pointer tertinggal di sisi kiri menu.

## Perbaikan

`public/assets/css/themes/arunika_prism/mobile-v2.css` sekarang mengatur pseudo-element pointer profile mobile ke `left: auto !important` dan `right: 14px !important`. Scope hanya `.ph-prism-mobile-profile-menu`, jadi dropdown desktop tidak berubah.

Regression test ditambahkan pada `tests/arunika-prism-mobile-sidebar-static.test.mjs`.

## Backup

Backup sebelum edit:

`project-artifacts/backups/20260903_235548_arunika-prism-profile-pointer/`

SHA-256 backup source dan test diverifikasi sama dengan source sebelum perubahan.

## Verifikasi

- Prism mobile static regression: passed.
- 400px: menu width 280px, pointer `right: 14px`, center delta terhadap avatar sekitar 3px, overflow 0px.
- 300px: menu width 272px, pointer `right: 14px`, overflow 0px.
- Browser console setelah clean session: 0 error.
- Graphify di-update incremental setelah perubahan source.
