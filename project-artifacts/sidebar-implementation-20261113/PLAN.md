# Sidebar Semua Tema Implementation Plan

> Pelaksanaan langsung dalam sesi ini menggunakan Superpowers Executing Plans dan Ponytail. Pengguna telah menyetujui preview serta implementasi, lalu memperluas scope ke tema lain. Tidak perlu meminta persetujuan ulang.

**Goal:** Terapkan preview terpilih: kategori uppercase, divider saat sidebar dilipat, spacing submenu, dan badan hover tepat pada batas sidebar dengan panah ke kiri.

**Architecture:** Aturan tampilan bersama ditempatkan pada stylesheet CMS existing yang dimuat keenam layout. Empat script Arunika menghitung sisi kiri panel dari `sidebar.getBoundingClientRect().right`. Tema lama memakai offset CSS yang mengikuti ukuran rail dan padding aktualnya. Warna tetap mengikuti token tema. Renderer dan data kategori tidak diubah.

**Tech Stack:** Blade aktif, CSS existing, JavaScript existing, Playwright lokal, PHP CLI Laravel.

**Spec:** Preview terpilih `project-artifacts/sidebar-category-preview-20261113/preview-comparison.png` dan koreksi panah pada REPORT.md di folder yang sama. Alternatif `sidebar-ui-recommendation-20261113` ditolak dan tidak diterapkan.

## Batas

- Gunakan checkout aktif untuk implementasi dan runtime Laragon yang sama. Tidak membuat worktree, mengubah branch, commit, atau push.
- Pertahankan seluruh perubahan lama. Status awal hanya dua folder artifact preview belum ditrack.
- Semua backup, helper test, render Blade, screenshot dan laporan berada di folder ini.
- Jangan mengubah data kategori/menu, pengaturan tema global, auth, frontend, atau aset Page Builder.
- Tidak ada dependensi baru. Graphify dibaca saja dan menunggu perintah update manual.

## Langkah

- [x] Tulis pemeriksaan browser terhadap Blade aktif enam tema; reproduksi kegagalan kategori Lucent dan posisi hover sebelum perubahan.
- [x] Backup seluruh source yang akan berubah dan verifikasi hash backup.
- [x] Terapkan aturan kategori, divider, tooltip dan submenu secara scoped pada `public/assets/css/cms/cms-bootstrap-theme.css`.
- [x] Ubah anchor posisi hover pada `public/assets/js/themes/{arunika_lucent,arunika_aurora,arunika_prism,arunika_equinox}/*.js` dari batas elemen menu ke batas sidebar.
- [x] Jalankan ulang pemeriksaan browser, pengujian regresi sidebar/tema terkait, pemeriksaan sintaks JS serta diff.
- [x] Periksa hasil visual Lucent dan tema lain, tulis laporan dengan batas verifikasi yang nyata.

## Fokus review

- Nama kategori berasal dari renderer aktif; label statis yang sengaja `d-none` tidak dimunculkan.
- Divider hanya tampil saat dilipat; label tampil kembali setelah dibuka.
- Panah menyatu dengan warna panel, dan perpindahan pointer ke submenu tidak memutus hover.
- Layout light/dark dan mobile tetap mempertahankan warna serta perilaku tema.
- Dua tema lama memiliki referensi script 2025 yang tidak tersedia di source aktif. Uji CSS keduanya melalui state class rendered Blade, catat bahwa toggle end-to-end tidak dapat dibuktikan oleh fixture tersebut; jangan memperbaiki aset lama di luar scope.

## Ledger

- Memori CSS/auth/sidebar 3 November diperiksa terfilter. Query Graphify stylesheet Lucent dan bridge CMS menunjukkan consumer layout; source aktual dikonfirmasi.
- Enam tema: Lucent, Aurora, Prism, Equinox, Default, Calm Green.
- Baseline valid: 168 checks, 68 gagal. Sesudah implementasi: 168 checks lulus. Responsif dan fixture kategori tambahan: 20 checks lulus. Regresi Node: 25 + 26 lulus.
- Calm Green memakai rail lebih lebar daripada Default; offset final mengikuti lebar parent dan gutter `.5rem`, bukan konstanta rail.
- Halaman `/account` Lucent diuji langsung tanpa injeksi CSS atau intercept route. Nama kategori, panah, anchor panel, dan gap 12px terverifikasi.
- Diff akhir terbatas pada satu CSS CMS bersama dan empat JS tema; Graphify tidak diperbarui.
