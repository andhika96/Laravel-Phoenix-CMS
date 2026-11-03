# Laravel 13 Phoenix — pemeriksaan semua tema dan isolasi auth

3 November 2026 (WIB). Permintaan: periksa tema lain setelah teks sidebar menjadi putih; pastikan Primary Color dan CSS auth tidak mengganggu CMS.

## Kesimpulan

Auth dan CMS terpisah pada source dan hasil browser saat ini. Rule pemaksa putih berasal dari CSS bridge Bootstrap milik CMS, bukan dari Primary Color auth.

Rule lama sudah terdapat dalam Git commit `0e8065faf`, tanggal **30 Oktober 2026 pukul 15:08:31 +07:00**, sedangkan backup sebelum perubahan Primary Color auth bertimestamp **16:30:29** hari yang sama. Bukti histori ada di `git-origin.txt` dan `git-before-auth-bridge.css`; backup auth sebelumnya tercatat pada `project-artifacts/auth-primary-20261030/backup-manifest.json`.

Browser mereproduksi rule pemaksa putih pada enam tema: Prism, Aurora, Lucent, Equinox, Default, Calm Green. Perbaikan awal mengecualikan navigasi `.ph-sidebar` sehingga mencakup empat tema Arunika. Pemeriksaan lanjutan menemukan dua tema lama menggunakan wrapper `arv7-menu`, `arv7-menu-mobile`, dan `arv7-menu-footer`. Ketiga wrapper ini sekarang juga dikecualikan.

Tidak ada perubahan tambahan pada CSS auth, controller auth, warna tersimpan, primary-color form, atau pengaturan tema global.

## Perubahan source

Satu file: `public/assets/css/cms/cms-bootstrap-theme.css`.

Rule `list-group` aktif tetap digunakan untuk komponen Bootstrap biasa. Rule tersebut mengecualikan item navigasi dalam:

- `.ph-sidebar`
- `.arv7-menu`
- `.arv7-menu-mobile`
- `.arv7-menu-footer`

Warna aktif kembali mengikuti masing-masing tema; tidak menetapkan satu warna teks untuk seluruh tema atau mengubah palet tema.

Backup sebelum perluasan perbaikan, terverifikasi SHA-256:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\theme-auth-isolation-20261103\backups_20261103_095530\cms-bootstrap-theme.css`

SHA-256: `C0645DCB1B472F7DD2891EB2BBD2E6F532540E0442249D7B63355BD5D3678247`. Ini snapshot source aktif setelah perbaikan pertama, bukan pengganti source utama. Manifest di `backup-manifest.json`.

## Pemisahan yang diperiksa

1. Enam layout CMS tidak memuat `assets/css/auth/`, `AuthAppearance`, atau variabel `--auth-primary`.
2. Seluruh selector pada lima file CSS auth dibatasi ke `.ph-auth-page` / `body.ph-auth-page`.
3. Seluruh 16 view auth mewarisi `auth.auth_layout`, tanpa `custom_theme('auth')`.
4. Layout auth tidak memuat CSS tema CMS, bridge CMS, atau shared `phoenix-cms.css`.
5. Variabel primary auth dideklarasikan pada body auth, bukan selector root yang berlaku untuk dokumen CMS.
6. Penyimpanan primary melalui Appearance diuji dalam transaksi rollback: warna login dapat berubah ke kuning sementara seluruh row `Theme_Settings` tetap sama. Tidak ada perubahan pengaturan yang dipertahankan setelah tes.
7. Auth masih berbagi Bootstrap/Font Awesome dan font dari Site Config sebagai fondasi. Tidak menyatakan bahwa fondasi bersama tersebut merupakan CSS tema.

## Verifikasi browser

Enam tema dirender dari Blade aktif dengan pemilihan layout hanya pada string fixture. Tidak mengganti tema aplikasi dalam database, tidak menyimpan theme-color, dan tidak mengubah file Blade aktif. Tiap tema dirender dalam proses PHP terpisah sehingga fungsi renderer menu tidak terbawa dari tema sebelumnya. Fixture dibuka pada origin lokal dengan asset CSS/JS aktual.

### Desktop, enam tema × dua mode

Browser membandingkan tiga kondisi: bridge lama, bridge dengan rule pemaksa warna dihapus sebagai pembanding warna asli tema, dan source hasil perbaikan. Total **36 kondisi**. Sesudah perbaikan, warna teks, label, dan background cocok dengan pembanding tema pada semua 12 kombinasi tema/mode. Animasi dinonaktifkan hanya dalam browser QA untuk menghindari pengambilan warna saat transisi.

Warna desktop pada aksen ungu dalam fixture:

| Tema | Light | Dark |
|---|---|---|
| Prism | #9D00FF | #9D00FF |
| Aurora | #9D00FF | #9D00FF |
| Lucent | #171717 | #F4F7F5 |
| Equinox | #9D00FF | #F5FFFC |
| Default | #E01D24 | #EF6250 |
| Calm Green | #024023 | #1FA675 |

Ini membuktikan pemulihan kontrak warna masing-masing tema, bukan audit kontras seluruh palet semua tema. Default dan Calm Green tetap memakai palet bawaannya.

Bukti: `browser-theme-audit.json` sebelum perluasan legacy, `browser-theme-audit-final.json` sesudahnya, serta screenshot `*-desktop.png`.

### Mobile

Enam tema pada viewport 390×844, mode light/dark, masing-masing dibandingkan antara warna asli tema dan source hasil perbaikan: **24 kondisi**, seluruhnya cocok. Struktur mobile legacy juga diperiksa. Ini pengujian computed style menu mobile; tidak mengklaim seluruh interaksi navigasi semua template sudah diuji.

Bukti: `browser-mobile-audit.json`.

### Uji batas CSS auth terhadap CMS

Untuk setiap enam tema, warna body, menu aktif, tombol primary dan background diukur. Kelima stylesheet auth kemudian sengaja dimuat ke fixture CMS dan variabel `--auth-primary` diberi kuning pada DOM QA. Hasil pengukuran tetap sama pada enam tema. Dokumen CMS aslinya tidak memuat stylesheet atau nilai primary auth tersebut.

Seluruh 16 variasi auth juga dimuat; masing-masing hanya memuat CSS auth yang sesuai, tidak memperoleh variabel bridge CMS, dan tombol tetap menggunakan primary auth hijau yang tersimpan.

Bukti: `browser-isolation.json`. Ada tiga `grecaptcha is not defined` pada signup, konsisten dengan temuan lama sebelum pemisahan auth; tidak memengaruhi kesimpulan cascade CSS. Tidak menguji submit signup/recovery.

## Pengujian lain

- `verify-source.mjs`: lulus untuk enam layout CMS, seluruh selector CSS auth, serta 16 view auth.
- `verify-results.mjs`: lulus, perbandingan hasil tema dan isolasi browser.
- PHPUnit `AuthCmsIsolationTest.php`: **1 test, 3 assertions lulus**, memakai DatabaseTransactions/rollback.
- Node regression suite Lucent/Prism/Aurora/Equinox: **30 tests lulus, 0 gagal**, `regression-node.log`.
- `git diff --check` pada perubahan CSS: lulus.

## Memori, Graphify, dan source

Memori relevan: `E:\AI\Memories\20260901-arunika-lucent-handoff.md` dan `E:\AI\Memories\20260905-laravel13-phoenix-css-archive-and-equinox-mobile-handoff.md`; laporan perbaikan pertama dan laporan auth 30 Oktober dipakai untuk konteks terbaru. Memori September mendeskripsikan auth lama, jadi kondisi pemisahan diverifikasi ulang dari source sekarang.

Graphify lokal dipakai dengan query `Theme_Settings` dan `Awesome_Admin_Themes_Controller cms_layout Theme_Settings custom_theme`; sumber peta mengarahkan ke model tema, Common.php, dan controller pengaturan tema. Bukti penyebab berasal dari source, Git, dan browser; tidak menganggap relasi INFERRED sebagai bukti. Hasil query terpilih dicatat di `graph-query.txt`. Graph belum mencakup seluruh perubahan auth terbaru; tidak di-update sesuai instruksi manual.

Source dibaca: controller Themes dan Appearance, model Themes/Theme_Settings/Page_Theme_Setting, Common.php pemilih tema, enam layout dan komponen menu CMS, dashboard Blade, layout dan 16 view auth, AuthAppearance, lima stylesheet auth, stylesheet sidebar tema dan bridge CMS, serta tes terkait. Source yang dimodifikasi hanya bridge CMS tersebut.

## Status dan batas

Pekerjaan scope ini selesai. Perubahan auth/preview/artifact lama dalam worktree dipertahankan. Tidak reset, commit, push, deploy, atau update Graphify. Tidak menyimpan percakapan ke memori karena tidak diminta.

Verifikasi browser memakai Chromium lokal dan fixture dari view aktif. Tidak mengaktifkan keenam tema secara global. Firefox, perangkat fisik, produksi, seluruh palet/mode/komponen tiap tema, dan alur submit auth sungguhan belum diuji.
