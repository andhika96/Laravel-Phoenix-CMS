# Laravel 13 Phoenix — teks menu aktif sidebar putih

Tanggal: 3 November 2026 (WIB). Scope: teks menu sidebar aktif yang berubah putih pada latar ungu muda dalam screenshot pengguna.

## Penyebab dan perbaikan

Runtime menggunakan `ph-theme-arunika-lucent`. Rule global pada `public/assets/css/cms/cms-bootstrap-theme.css`:

`.list-group .list-group-item.active { color: var(--bs-list-group-active-color) !important; }`

Rule tersebut memakai `--cms-bs-on-primary`, yaitu putih untuk latar primary solid, dan mengalahkan aturan warna navigasi Lucent. Sidebar menggunakan latar tinted terang sehingga teks putih tidak tepat. Hasil browser sebelum perbaikan: foreground dan label Dashboard `rgb(255,255,255)`.

Perubahan hanya pada satu file source: rule tersebut kini mengecualikan `.list-group-item` di dalam `.ph-sidebar` melalui `:not(.ph-sidebar .list-group-item)`. Warna navigasi kembali ditentukan oleh tema. Aturan daftar Bootstrap di luar sidebar tetap berlaku. Tidak menambah override putih/hitam global atau mengganti warna tema, data, font, auth, Page Builder, atau File Manager.

## Backup dan recovery

Backup terverifikasi SHA-256 sebelum perubahan:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\sidebar-active-contrast-20261103\backups_20261103_094515\cms-bootstrap-theme.css`

Manifest: `backup-manifest.json`. Source SHA-256 awal: `19AF32E8A98F3CC422C6C46BD18157C386A3658DF9FEF7748BA4D4A1E55C2A15`.

Status repository sebelum perubahan dicatat di `git-status-before.txt`. Perubahan auth sebelumnya dan artifact preview Manage Article yang belum di-commit dipertahankan. Tidak ada reset, staging, commit, push, deploy, atau mutasi pengaturan database.

## Konteks dan source yang diperiksa

- Memori relevan: `E:\AI\Memories\20260901-arunika-lucent-handoff.md`, `E:\AI\Memories\20260903-arunika-lucent-mobile-final-handoff.md`, `E:\AI\Memories\20260905-laravel13-phoenix-css-archive-and-equinox-mobile-handoff.md`. Catatan lama V2 tentang sidebar hanya sebagai konteks migrasi tema; source aktif Lucent menjadi acuan.
- Graphify query lokal: `Arunika Lucent sidebar active menu color ph-nav-text list-group-item active` (`graph-query.txt`). Graph mengarahkan ke menu dan script Lucent. `explain sidebar` ambigu; diselesaikan memakai ID `public_assets_js_themes_arunika_lucent_arunika_lucent_sidebar`. Relasi `contains` ke script pada L109 berlabel EXTRACTED. Tidak memakai relasi INFERRED sebagai bukti penyebab.
- Graph bertanggal 28 Oktober; jalur Lucent yang ditunjuk diverifikasi pada source aktual. CSSOM browser menjadi bukti rule pemenang; graph tidak dipakai untuk menyimpulkan cascade. Tidak memperbarui graph sesuai instruksi manual pengguna. Tidak mengirim graph/source ke layanan eksternal.
- Dibaca: layout dan komponen menu Lucent; `arunika_lucent.css` (khususnya kontrak shell terakhir, warna aktif, teks dan link); `mobile-v2.css` (state warna drawer); script `arunika_lucent.js` (applyMainColor); `shared/phoenix-cms.css`; `cms/cms-bootstrap-theme.css`; tes terkait Lucent, Prism, Aurora, Equinox; helper konfigurasi tema pada konteks sebelumnya.
- Skills: Ponytail untuk perubahan sempit, UI UX Pro Max untuk kontras/state, Design Taste Frontend dibaca dan tidak menerapkan redesign karena scope perbaikan CMS, Playwright untuk browser, Graphify untuk peta source. Paket Superpowers tidak ditemukan pada sesi ini; alur reproduksi → backup → perubahan minimal → verifikasi tetap dijalankan tanpa dependency baru.

## Verifikasi terbaru

1. Masuk ke CMS lokal, membuka `/dashboard`, dan mereproduksi putih pada aksen ungu `#9D00FF`. `browser-before.json` memuat CSSOM rule yang mengenai anchor aktif; screenshot `dashboard-before.png`.
2. Setelah perubahan, desktop mode light memakai teks `rgb(23,23,23)` dan dark memakai `rgb(244,247,245)`. Delapan pilihan palet masing-masing diuji pada dua mode (16 kombinasi). Kontras desktop light 14.44–16.87:1 dan dark 11.46:1. Palet diubah hanya di DOM browser QA, tanpa menyimpan pilihan tema.
3. Hover dan focus desktop tetap gelap pada tinted background.
4. Drawer mobile 390×844 tetap memakai aksen ungu `rgb(157,0,255)` sesuai rule mobile Lucent, bukan putih dari bridge; tidak ada overflow horizontal. Kontras seluruh palet mobile tidak diaudit dalam scope ini.
5. Fixture daftar Bootstrap biasa di luar sidebar tetap memakai teks putih dan background primary, pada semua 16 kombinasi. Fixture dihapus dan DOM dikembalikan/reload setelah QA.
6. JavaScript page errors selama pengujian: `[]`.
7. Node tests eksplisit: **30 passed, 0 failed**, dari Lucent theme/mobile semantics/mobile v2, Prism theme, Aurora theme color, Equinox theme. `regression-node.log`.
8. `git diff --check`: lulus. Patch satu file CSS tidak memerlukan PHP/JS build maupun migration. Layout sudah memberi cache key `filemtime()` untuk CSS bridge.

Bukti: `browser-verify.json`, `dashboard-after.png`, `sidebar-mobile-after.png`. Screenshot sesudahnya diperiksa secara visual.

## Batas verifikasi

Runtime diuji di Chromium lokal. Firefox yang tampak pada screenshot pengguna dan perangkat fisik belum diuji. Tema lain hanya dites statis; tidak mengganti tema CMS global atau menyatakan semua tema telah lulus runtime. Alur auth dan primary color tombol bukan scope follow-up ini. Tidak ada perubahan source auth tambahan.

Semua pekerjaan dalam scope selesai. Bila browser pengguna masih memperlihatkan resource lama, lakukan hard reload. Tidak ada penyimpanan memori percakapan karena tidak diminta.
