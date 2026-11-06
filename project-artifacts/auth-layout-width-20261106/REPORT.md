# Lebar layout Ocean Split dan Verdant Split — 6 November 2026

## Hasil implementasi

Pengaturan **Layout width** tersedia pada kedua tema, untuk Login, Signup, Forgot Password dan Reset Password. Nilai **Constrained** (`bounded`) menjadi default dan mempertahankan ukuran saat ini. **Full width (adaptive)** (`fluid`) mengisi lebar yang tersedia dengan padding tepi responsive yang sudah ada. Isi form tetap maksimal355px desktop dan390px mobile; teks panel pada mode penuh dibatasi560px agar baris tidak memanjang berlebihan di layar lebar.

Kelompok **Layout** menggunakan accordion dan kontrol CMS yang sudah ada, di antara Page content dan pengaturan visual. Urutannya: Layout width, Card width (Ocean, hanya Constrained), Panel position, Photo / form proportion (Ocean). Nilai Card width tetap tersimpan dalam draft ketika kontrol disembunyikan, dan dipakai kembali ketika kembali ke Constrained.

Tidak ada endpoint, tabel, migration, dependency atau plugin baru. Save memakai validasi serta penyimpanan JSON per halaman/per tema yang sudah ada. Default thumbnail tetap sesuai mode Constrained yang tidak berubah. Tema aktif dan opsi pengguna tidak diganti untuk keperluan QA.

## Memori dan Graphify

- `E:\AI\Memories` diperiksa lebih dahulu dan tidak tersedia (Test-Path false). Konteks terakhir dibaca dari `project-artifacts/auth-brand-single-20261106/REPORT.md`, termasuk pointer keputusan auth sebelumnya.
- Status repository diperiksa dan disimpan sebelum perubahan. Perubahan sebelumnya dipertahankan.
- Graphify `explain resources_views_auth_auth_layout_blade` digunakan sebagai konteks: relasi **EXTRACTED** ke auth-base.css. Graph belum mencakup shared modern view/Catalog baru, sehingga schema, caller, Blade, CSS dan alur Save diverifikasi langsung pada source aktif. Tidak ada temuan INFERRED/AMBIGUOUS yang dijadikan bukti.
- Graphify tidak diperbarui sesuai instruksi manual; SHA256 tetap `0cb6deffe24e3d77dd2d4a59281835dc773a73f6155c3bab9650f4a67056ecb6`.

## File dan backup

Empat file source dibackup sebelum perubahan; path lengkap dan hash ada di `backups.json`:

1. `app/Support/AuthThemeCatalog.php`: schema/default/validasi mode; conditional Card width; grouping Layout pada kedua tema.
2. `public/assets/js/vue3/manage_appearance/vueV3-manage-appearance-2026.js`: kelompok Layout dan label mode; memakai evaluator `when` dan mekanisme Save existing.
3. `resources/views/auth/modern/page.blade.php`: atribut data untuk menerapkan mode tersimpan.
4. `public/assets/css/auth/auth-modern.css`: lebar penuh dan batas teks panel, hanya untuk Ocean/Verdant dengan mode fluid. Aturan ditempatkan sesudah preset Card width sehingga semua preset tetap menghasilkan layout penuh ketika fluid aktif.

Source lain yang dibaca: controller Appearance index/update, view Appearance generik, CSS Appearance, tes Catalog/Render/Images existing dan script QA Appearance sebelumnya. Caller dicari melalui `layout_width`, `card_width`, `when`, `optionGroups`, `schema` serta atribut shared view.

Artefak baru di folder ini: PLAN, tes fitur, render fixture, dua script QA browser, snapshot pengaturan, log tes, JSON hasil browser dan screenshot. Perbaikan awal harness tes (environment testing untuk request HTTP) juga dibackup sebelum diedit. Tidak ada commit, push atau deployment.

## Pengujian dan bukti

- Sebelum implementasi, tiga tes fitur gagal karena mode/default belum ada, field baru belum diterima endpoint Save (422), dan atribut mode belum dirender. Log: `before-tests.log`. Percobaan awal harness HTTP sempat419; environment testing kemudian disamakan dengan tes Images existing, tanpa mengubah middleware aplikasi.
- Sesudah implementasi, tes fitur baru dan tes Catalog/Render existing lulus: **8 tests, 212 assertions** (`after-tests.log`). Mencakup default untuk opsi lama, mode valid/invalid, pengelompokan, Save dan reload dua mode pada dua tema × empat halaman, retensi Card width dan pilihan tema, serta render atribut pada seluruh kombinasi tema/mode/halaman.
- Endpoint Save diuji sungguhan dalam **DatabaseTransactions**, kemudian rollback. Site Config tidak dimutasi. Tes tidak mengirim email dan tidak mengupload gambar.
- **112 pemeriksaan layout Chrome lulus** (`layout-browser-checks.json`):96 kombinasi dua tema × dua mode × empat halaman × enam viewport (320,390,820,1440,1920,3840px);12 variasi Ocean untuk tiga Card width, dua posisi panel dan dua proporsi; serta empat variasi tambahan tablet stack dan mobile visual above.
- Pengukuran browser memastikan lebar surface sesuai mode, form tetap dalam batas lebarnya, satu brand, dan tanpa overflow horizontal. Proporsi panel Ocean juga dicek pada mode penuh.
- **8 kasus UI Manage Appearance lulus** (`appearance-browser-checks.json`):dua tema × empat tab halaman, label, urutan field, conditional visibility, retensi ukuran kartu, payload Save dan notice CMS. Mode baru tidak muncul pada Clean Peach/Midnight. Kontrol Layout diperiksa pada tablet820px dan mobile390px. Tidak ada pageerror. **Save pada browser diintersep**, sehingga draft QA tidak mengganti opsi pengguna.
-16 fixture HTML dibuat dari Blade aktual melalui override model **di memori**; CSS dan font disajikan dari origin aplikasi. Screenshot desktop1920px, ultrawide3840px, mobile390px dan panel setting diperiksa. Semua screenshot berada dalam folder ini.
- `settings-before.json` dan `settings-after.json` identik byte per byte setelah QA. Pengaturan auth tersimpan tetap sama.
- PHP lint Catalog, Node syntax check Vue dan `git diff --check` lulus.
- Target junction preview diperiksa sebelum dipindahkan dari public ke `retained-preview-junction`. URL sementara telah ditutup. Fixture dan junction arsip dikecualikan dari analisis source/Graphify.

## Keterbatasan

Responsive diuji memakai viewport Chrome, belum perangkat fisik100inci, Safari atau Firefox. Tidak mengulang suite backend yang tidak terkait. Pada mode penuh, gambar Ocean memakai perilaku cover/focus existing sehingga potongan gambar mengikuti rasio panel; form tetap dibatasi lebarnya.
