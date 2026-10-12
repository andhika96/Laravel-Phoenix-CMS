# Minimal Reading List Revamp Implementation Plan

**Goal:** Terapkan preview yang disetujui ke template aktif Minimal Reading List dengan font family, body size, dan heading scale mengikuti CSS frontend CMS.

**Architecture:** Gunakan Blade serta CSS reading-list yang sudah dipakai preview dan frontend. Pertahankan normalizer/settings, tiga model pagination, custom thumbnail/frame, filter kategori, dan slot pembaruan list Vue. Tidak mengubah data atau pilihan template tersimpan.

**Tech Stack:** Laravel Blade, CSS native, existing Vue frontend, PHP renderer fixture, Playwright CLI.

**Spec:** Preview `../index.html` dan instruksi pengguna untuk langsung menerapkan dengan typography frontend CMS. Scope hanya Minimal Reading List.

## Global Constraints

- Semua backup dan QA dalam folder project-artifacts ini.
- Backup hash-verified sebelum mengedit file yang sudah ada.
- Tidak commit/push, reset/clean, atau menimpa perubahan lain.
- Tidak menyalin font-size px atau font family Nunito dari prototype; gunakan SiteTypography dan token frontend.
- Tidak update Graphify otomatis setelah perubahan source.

## Review Focus

- Search tetap proporsional saat font CMS berubah; gap dari settings berlaku.
- Mobile memakai row kompak, tanpa overflow dan tanpa merusak custom thumbnail height.
- Frame/radius/warna pagination tetap mengikuti model dan settings.
- Header jumlah hasil berada dalam list-content yang diganti oleh Vue.
- Sidebar conditional dan satu mode category filter tetap utuh.

## Task 1: Revamp dan verifikasi

- [x] Buat fixture dari Blade aktif dan browser check; jalankan RED untuk gap search, layout mobile, dan typography CMS.
- [x] Backup CSS, Blade, dan test presentation yang terdampak secara hash-verified.
- [x] Rapikan CSS scoped reading-list; tambahkan heading daftar dalam slot konten. Pertahankan token typography, settings media, dan pagination model bersama.
- [x] Perbarui kontrak presentation untuk layout mobile yang memang berubah.
- [x] Jalankan GREEN browser, Node Article/typography contracts, PHP renderer/Article checks dengan database terisolasi, syntax, dan diff checks.
- [x] Periksa screenshot source renderer, review perubahan, simpan hasil QA dan batas verifikasi.

**Ruling:** Pengguna sudah memberi instruksi eksekusi langsung; plan dilaksanakan inline tanpa approval handoff tambahan. Checkout aktif tetap dipakai agar source/runtime proyek yang diminta dapat diperiksa. Pilihan template/data database tidak diubah.
