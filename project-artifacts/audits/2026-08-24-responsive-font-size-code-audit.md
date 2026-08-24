# Audit Kode Responsive Font Size

- Tanggal: 24 Agustus 2026 (Asia/Jakarta)
- Project: `D:\Laragon\www\laravel-13-phoenix`
- Mode: audit dilanjutkan dengan remediasi test-first setelah user menyetujui desain
- Scope final: responsive typography lintas empat tema yang selectable melalui Manage Themes, Site Config, profile menu Prism, dan referensi File Manager V2

## Verdict

**Pass setelah perbaikan dan koreksi scope.** Site Config tetap menjadi sumber global font family, size, dan unit. Empat tema Manage Themes—Mosaic, Aurora, Prism, dan Equinox—menghasilkan variable serta font asset yang sama; responsive adjustment bekerja pada normal, compact, tablet, px, serta rem; dan nilai kecil eksplisit tetap dihormati.

Kode bukan spaghetti. Resolver copy-paste telah diganti satu `SiteTypography`, rule heading berkurang dari delapan menjadi empat, dan JavaScript membaca root font size browser aktual.

## Temuan

### CORRECTED SCOPE — Tema produksi yang selectable berjumlah empat

Audit awal mencampurkan empat tema yang diizinkan `MANAGEABLE_THEME_CODES` dengan dua layout historis, Default dan Calm Green. User mengoreksi scope berdasarkan halaman Manage Themes. Perubahan pada Default dan Calm Green kemudian dikembalikan.

**Perbaikan final:** `App\Support\SiteTypography` menjadi resolver tunggal untuk Mosaic, Aurora, Prism, dan Equinox. Matrix render 15px lulus 4/4. Layout Default dan Calm Green tidak termasuk perubahan final.

### CORRECTED CONTRACT — Nilai Site Config tetap canonical

**Confidence: high; direproduksi di browser CSS engine.**

Rule compact saat ini:

```css
min(var(--ph-font-size), max(12px, calc(var(--ph-font-size) - 1.5px)))
```

User mengklarifikasi bahwa nilai di halaman Config harus tetap menjadi sumber global. Karena itu outer `min()` adalah perilaku yang dipertahankan: nilai 14px menjadi 12.5px pada compact, tetapi nilai eksplisit 10px tetap 10px dan tidak dibesarkan paksa. Komentar CSS sekarang menjelaskan kontrak ini.

### RESOLVED — Test sebelumnya tidak membuktikan perilaku lintas tema

**Confidence: high.**

- Feature test sekarang merender empat layout Manage Themes dengan nilai non-default 15px dan mengharuskan asset, family, size, serta dynamic font link sama.
- Node runtime test menjalankan method Vue melalui VM dan membuktikan konversi memakai root browser aktual.
- Browser CSS-engine QA mengukur 15px normal/compact/tablet, 0.9375rem, dan nilai kecil eksplisit 10px.

### RESOLVED — Konversi px/em/rem sebelumnya mengasumsikan root selalu 16px

**Confidence: high untuk logika source; impact runtime belum diuji pada browser dengan custom root size.**

`siteTypographyRootFontSize()` sekarang membaca `getComputedStyle(document.documentElement).fontSize`; konstanta 16 hanya menjadi fallback bila computed value invalid. Regression test root 20px membuktikan 20px berubah menjadi 1rem, bukan 1.25rem.

## Kualitas dan kerapian kode

Yang sudah baik:

- Satu shared stylesheet menangani content, overlay, sidebar, form control, dan profile hierarchy.
- Selector memakai `:where()` sehingga specificity dasar tetap rendah.
- Breakpoint normal, compact wide-short, dan RFS heading dipisah jelas.
- Validasi backend untuk unit dan range tersedia.
- Runtime 14px dan 0.875rem menghasilkan ukuran yang konsisten.

Perbaikan kerapian:

- Resolver 16 baris yang disalin pada tiga layout dihapus dan diganti satu class dengan satu public method.
- Keempat layout selectable hanya mengonsumsi hasil resolver yang sama.
- Delapan rule heading digabung menjadi empat tanpa mengubah computed size.
- Sembilan `!important` tetap dipertahankan secara terarah untuk menundukkan Bootstrap/theme lama; browser QA membuktikan control dan profile hierarchy tetap benar.

### Ponytail complexity review

- `resolved`: copy-paste resolver diganti `App\Support\SiteTypography`.
- `resolved`: delapan rule heading menjadi empat mapping H1-H4.
- `resolved`: konversi unit memakai computed root font size.

`net: source aktif lebih pendek dan satu jalur konfigurasi menggantikan tiga salinan.`

## Evidence matrix

| Requirement | Evidence | Result |
| --- | --- | --- |
| Shared asset dimuat empat tema selectable | Source + static test | Pass |
| Site Config mengontrol empat tema selectable | Render empat layout dengan 15px | Pass; 4/4 |
| Desktop normal 1440×1100 | Browser CSS engine | Pass: body/control 15px, H1 40px |
| Desktop compact 1440×900 | Browser CSS engine | Pass: body/control 13.5px, H1 32px |
| Tablet 1024×768 | Browser CSS engine | Pass: body/control 15px, H1 37.36px |
| Unit rem | Browser CSS engine, 0.9375rem | Pass: 13.5px compact |
| Nilai kecil eksplisit | Browser CSS engine, configured 10px | Pass: body/control tetap 10px |
| Profile hierarchy compact | Browser CSS engine | Pass: item 12.5px pada base 15px compact |
| Tidak ada console error harness | Browser log | Pass |

## Checks yang dijalankan

- `node --test tests\theme-responsive-typography-static.test.mjs tests\site-typography-config-runtime.test.mjs`: 10/10 pass.
- `php artisan test tests/Feature/SiteTypographyPreviewSettingsTest.php --compact`: 4 pass, 70 assertions.
- Theme Manager + Site Config + Site Logo regression: 11 pass, 131 assertions.
- Full PHP: 674 pass, 1 baseline fail, 19.151 assertions. Failure tetap `PageBuilderElementorV23ShellTest` yang mengharapkan 200 tanpa authenticated user tetapi menerima 302.
- Full active Node: 790 total, 784 pass, 6 baseline fail pada dua test Aurora dan empat test Prism; tidak ada failure typography baru.
- `php artisan view:cache`: pass.
- PHP lint `SiteTypography.php` dan feature test: pass; `git diff --check`: pass dengan warning normalisasi CRLF saja.
- Graphify BFS query untuk responsive typography/theme/layout/config/sidebar/profile; graph fresh dan dipakai untuk mempersempit source.
- Render Blade empat tema Manage Themes dengan nilai non-default 15px, lalu verifikasi output variable.
- Browser harness setelah perbaikan pada 1440×1100, 1440×900, 1024×768, 15px, explicit 10px, dan 0.9375rem; 0 console error/warning.
- Independent read-only review dan re-review koreksi scope: ready, tanpa Critical/Important. Coverage minor `em` dan invalid-root fallback kemudian ditambahkan dan lulus; Default/Calm Green dikonfirmasi byte-identical terhadap HEAD.

## Batasan dan state recovery

- Browser in-app tidak mempunyai authenticated CMS session; `/awesome_admin/config` mengarah ke login. Karena itu QA browser memakai shared CSS yang benar-benar disajikan host pada harness terisolasi, sementara wiring layout diverifikasi melalui render Laravel authenticated di proses lokal.
- Probe render mencoba memakai transaction rollback, tetapi perubahan `site_config.font_size` sementara tidak ikut rollback. Nilai langsung dikembalikan dan diverifikasi menjadi `noto_sans`, `14px`; theme aktif tetap `arunika_prism`.
- Source responsive typography dan empat layout Arunika dimodifikasi setelah backup `responsive-typography-global-fix-20260824_164317`: 10 file baseline, 0 SHA-256 mismatch. Koreksi scope Default/Calm Green dibackup lagi di `responsive-typography-four-theme-scope-20260824_171208`: 6 file, 0 mismatch.
- Metrics sebelum dan sesudah perbaikan berada di `project-artifacts/qa/responsive-typography-audit-20260824/`.
- Graphify incremental final setelah koreksi scope: 19.923 nodes, 34.358 edges, 1.440 communities; 0 missing/dangling endpoints, self-loop, duplicate, atau collapsed edge.
