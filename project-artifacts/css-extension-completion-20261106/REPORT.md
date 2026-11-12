# Kelengkapan utility Bootstrap Phoenix — 6 November 2026

Project: Laravel13 Phoenix. Scope source produksi hanya `public/assets/css/shared/extending-css-bootstrap-5.css`. Pengguna menyetujui implementasi hasil audit; rencana penggabungan tiga stylesheet tetap ditunda. Branch main dipertahankan sesuai preferensi pengguna sebelumnya; tidak ada staging, commit, push, deploy atau perubahan database.

## Perubahan

- 70 nilai end-40 sampai end-10 pada base/sembilan breakpoint diperbaiki sesuai angka nama class.
- 10 rule width/height negatif wn-1..5/hn-1..5 dihapus karena browser sebelumnya mengabaikan deklarasinya. Tidak mengarang arti calc baru. Nama utility lama yang valid tetap tersedia melalui extension atau Bootstrap.
- 5 blok text identik dan 9 utility dasar yang sudah dipenuhi Bootstrap dihapus. Margin mx/my yang saling melengkapi tidak dihapus sebagai duplikasi.
- Prioritas utility position/opacity/viewport disamakan dengan Bootstrap. Kasus position-relative position-md-fixed, opacity-100 opacity-md-25 dan vh-100 vh-md-50 kini bekerja pada breakpoint yang dimaksud.
- Semua9breakpoint existing dilengkapi: position static/sticky, offset top/bottom/start/end0/25/50/75/100/auto (rentang horizontal lama tetap), opacity0/25/50/75/100, viewport25/50/75/100, height0/25/50/75/100/auto, dan margin/gap6–10. Width responsive yang sudah ada dipertahankan.
- Helper baru min-w-0/min-h-0, line-clamp-2/3, dvh-100/min-dvh-100 dengan fallback vh, cursor pointer/grab/grabbing.
- Skala spacing lama dipertahankan:6=3.5rem,7=4rem,8=4.5rem,9=5rem,10=6rem. Ukuran width/height rem lama dan font-size eksplisit dipertahankan.
- Header berisi indeks, dependency, breakpoint, skala, konvensi LTR dan penjelasan prioritas. Bagian base dan masing-masing breakpoint memakai START/END perkeluarga utility.

## Ruling cascade spacing

Karena extension dimuat setelah seluruh Bootstrap, menambahkan m-6/gap-6 saja membuat m-6 m-md-1 dan gap-6 gap-md-1 tetap memakai nilai6. Review independen menemukan masalah ini; uji gagal sebelum perbaikan.

Solusi:1091aturan native spacing0–5/auto dari Bootstrap runtime direplay dalam kelompok breakpoint/prefix yang tepat, lalu digabungkan dengan ukuran6–10. Ini duplikasi kompatibilitas yang disengaja agar responsive dan urutan shorthand/axis positif bekerja. Komponen Bootstrap dan utility lain tidak dihasilkan ulang. Same-breakpoint negative margin tetap mengikuti kelompok setelah margin positif seperti Utilities API resmi dan perilaku legacy; breakpoint berikutnya dapat menggantikannya.

Review sempat mengusulkan hasil56px untuk m-n3 mt-6. Pengujian terhadap Bootstrap Utilities API dengan negative margin enabled membuktikan hasil resmi adalah-16px pada breakpoint sama, dan56px untuk m-n3 mt-md-6 pada breakpoint lebih besar. Usulan tersebut diralat dan ditarik oleh reviewer. Source, komentar dan tes mempertahankan kontrak yang sudah diverifikasi.

## Memori, peta source dan backup

- E:\AI\Memories diperiksa, tidak tersedia. Konteks dibaca dari `project-artifacts/css-extension-audit-20261106/REPORT.md` dan laporan penataan CSS sebelumnya; source aktif menjadi acuan.
- Status Git diperiksa sebelum edit; tracked tree bersih saat itu. Output kosong tidak membuat file status-before. Pemeriksaan akhir mengonfirmasi satu-satunya tracked change adalah target CSS (`final-state.json`).
- Graphify explain `extending-css-bootstrap-5.css` menunjukkan8pemanggil **EXTRACTED**; seluruh pemanggil/urutan Bootstrap diperiksa langsung. Tidak memakai relasi INFERRED/AMBIGUOUS sebagai bukti. Graphify tidak diperbarui sesuai instruksi manual; SHA256 tetap `0cb6deffe24e3d77dd2d4a59281835dc773a73f6155c3bab9650f4a67056ecb6`.
- Backup original dibuat sebelum perubahan; path absolut dan hash tercatat di `backup.json` dan diverifikasi ulang. Backup tahap implementasi dan komentar review juga disimpan bertimestamp dalam folder ini. Pemulihan dapat dilakukan per file memakai backup original setelah membandingkan perubahan terbaru, tanpa reset repository.
- Source dibaca: target CSS lengkap melalui PostCSS, Bootstrap5.3.6_custom CSS aktual,8layout pemanggil, renderer/regresi isolasi tema sebelumnya, serta source Sass Utilities API Bootstrap untuk membuktikan urutan negative margin.
- File produksi yang dimodifikasi hanya target CSS. phoenix-cms.css/theme-responsive-typography.css, layout, PHP, JS aplikasi, file framework, dan konfigurasi server tidak diubah. Script transform/review/test berada di project-artifacts, bukan pipeline runtime baru.

## Verifikasi terbaru

1. **8/8 tes utility lulus** (`final-utility-tests.log`). Memeriksa seluruh nilai offset, CSS.supports seluruh deklarasi, interaksi dengan Bootstrap, batas bawah dan tepat setiap breakpoint, skala spacing, campuran native/extension dan shorthand/axis, negative margin, clamp/dvh/cursor/min-size, serta ketersediaan semua nama utility lama yang valid. Sebelum implementasi6tes gagal dan1lulus; tes spacing tambahan juga RED sebelum compatibility fix.
2. **28/28 perbandingan consumer lulus** (`consumer-checks.json`):7layout ×390/1440px ×light/dark. Dua CMS default/calm_green menggunakan dashboard aktif; lima frontend calm_green/Aurora/Equinox/Prism/Lucent menggunakan layout aktif, font asli, menu dan komponen fixture. CSS backup versus source baru dibandingkan untuk geometry/font/warna/padding/display. Semua nilai yang diukur identik. Transisi dinonaktifkan hanya pada fixture agar pengukuran akhir stabil.
3. **8/8 cek referensi negative margin lulus** (`negative-margin-reference.json`), membandingkan output Sass Utilities API terpasang dengan extension. Sass hanya menghasilkan referensi di memori, bukan mengganti Bootstrap runtime atau menambah build/dependency.
4. **4GETlive baseline comparisons lulus** (`live-smoke.json`):/home dan/article HTTP200 memakai extension, /auth/login HTTP200 tetap tidak memakainya, /event anonim tetap redirect ke/auth/login sesuaibaseline. Tidak ada pageerror baru atau request tulis. GET aset CSS HTTP200; hash respons sama dengan file source aktif.
5. Parser CSS, seluruh deklarasi browser, keunikan selector per konteks dan `git diff --check` lulus. Sourcebackuphashvalid;branchmain danstagingdipertahankan.
6. Junction QA sementara diverifikasi targetnya dan dipindahkan dari public ke `retained-preview-junction`. URL sementara ditutup. Semua fixture/compiled/screenshot tidak termasuk source atau cakupan Graphify.

## Ukuran dan batas

Source berubah dari69805byte menjadi174154byte karena varian lengkap dan compatibility spacing. Pengukuran gzip dari5966 menjadi16280byte; ini ukuran kompresi terukur, bukan klaim server sudah mengirimgzip. Server lokal pada smoke test mengirim encoding identity; pengaturan kompresi tidak diubah dalam scope ini.

Setup hanya diperiksa pemanggilan stylesheet secara statis; wizard instalasi tidak dijalankan. Fixture consumer adalah pengujian CSS tanpa script/CRUD aplikasi. Belum menguji semua interaksi CMS/frontend, selector dinamis dari database, browser selain Chrome, perangkat fisik atau RTL. Perubahan hanya berlaku pada8layout yang sudah memuat extension; pemuatan ke tema CMS lain tidak diperluas.
