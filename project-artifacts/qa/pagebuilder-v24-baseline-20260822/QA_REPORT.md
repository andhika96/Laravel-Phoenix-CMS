# Page Builder v2.4 Baseline — QA Report

Tanggal: 2026-08-22

## Scope yang diverifikasi

- Clone source aktif Page Builder v2.3 menjadi source milik v2.4.
- Isolasi namespace, route, controller, request, support, model, mail, config, Vue/JavaScript, CSS, Blade, data, migration, mockup, dan tests.
- Dispatcher publik berdasarkan `editor_version`, sehingga halaman v2.3 tetap dirender oleh renderer v2.3 dan halaman v2.4 oleh renderer v2.4.
- Parity statis, automated tests, build, lint, source mapping, checksum source v2.3, dan browser runtime.

## Automated verification

- Node v2.4: 50 file, 243/243 test lulus.
- PHP v2.4: 113 test lulus, 5.817 assertions.
- Node v2.3 regression: 49 file, 241/241 test lulus.
- PHP v2.3 regression terarah: 23 file, 106 test lulus, 3.818 assertions.
- Build Vite: lulus.
- PHP lint source v2.4: lulus.
- Exact transformed-source audit: 185 file terpetakan, 0 missing, 0 mismatch, 0 unexpected.
- Cross-version marker audit: 0 referensi aktif v2.3 di source milik v2.4.
- Checksum source milik v2.3: 258 file, 0 perubahan.
- `git diff --check`: lulus.

## Browser runtime verification

- Root app v2.3 hanya `#pbElementorV23App`; root app v2.4 hanya `#pbElementorV24App`.
- v2.3 hanya memuat asset versioned v2.3; v2.4 hanya memuat asset versioned v2.4.
- Kedua sidebar berisi 47 widget.
- Heading berhasil ditambahkan di kedua versi dengan struktur DOM yang identik.
- Responsive preview Mobile menghasilkan lebar canvas yang sama.
- Klik kiri di luar context menu v2.4 menutup menu.
- Console kedua versi bersih dari error dan warning.

## Batas verifikasi

- Migration v2.4 belum dijalankan ke database agar QA clone tidak mengubah data runtime pengguna.
- Save, Reset, submit Form, dan Apply Dataset tidak dijalankan karena QA browser bersifat read-only.
- Full raw filter suite v2.3 masih memiliki satu test lama `PageBuilderElementorV23ShellTest` yang mengakses route auth tanpa login dan menerima redirect 302. Source/runtime v2.3 tidak diubah untuk memaksa test lama tersebut; shell authenticated v2.3 dan isolasinya sudah dibuktikan oleh baseline isolation test v2.4.
- `php artisan route:list` tidak dapat dipakai sebagai bukti tambahan karena repository memiliki referensi lama ke class yang tidak tersedia, `App\\Http\\Controllers\\Api\\v1\\Testing\\Testing_Controller`. Kegagalan ini tidak berasal dari route v2.4; registrasi dan kontrak route v2.4 sudah lulus Feature tests.

## Graphify

- Update incremental selesai: 274 file code diekstrak ulang, 1.016 tidak berubah, 0 dihapus.
- Graph akhir: 12.277 nodes, 20.350 edges, 1.180 communities.
- Warning non-blocking: JSON shapes v2.4 menghasilkan nol node seperti shapes v2.3; dependency React pada mockup v2.3/v2.4 terdeteksi duplikat pada graph artifact.

## Kesimpulan

Baseline Page Builder v2.4 lulus parity dan isolation QA. Refactor arsitektur widget 100% modular belum dimulai dan tetap menjadi fase terpisah setelah baseline clone disetujui.
