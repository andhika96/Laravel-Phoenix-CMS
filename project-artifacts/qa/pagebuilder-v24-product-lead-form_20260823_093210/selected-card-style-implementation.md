# Product Lead Form — Selected Card Style

Tanggal: 2026-08-23

## Scope

Menambahkan kontrol visual pada `Style → Product Level Cards` untuk ukuran kartu, spacing, alignment, media border, dan state `Selected`.

State `Selected` sekarang mengatur:

- border dan background kartu;
- label color dan shadow;
- tampil/sembunyi checklist;
- posisi checklist (top/bottom + left/right);
- ukuran, offset, radius, warna ikon, dan warna background ikon.

## Verification

- Product Lead Form Node suite: 8 passed.
- Full v2.4 Node suites: 386 passed, 0 failed.
- Product Lead Form renderer test: 1 passed, 10 assertions.
- Full v2.4 PHP Feature+Unit suites: 154 passed, 10,300 assertions.
- SFC compile: passed through Product Lead Form suite.
- PHP/JS syntax and `git diff --check`: passed.
- Vite build: passed, 58 modules transformed.
- Control binding audit: 50 modules, 1,805 controls, 0 consumerless controls.

## Graphify

Incremental update selesai tanpa clustering: 20,040 nodes dan 36,980 links. Diagnostic Graphify mencatat 0 missing endpoints, 0 self-loops, dan 2,068 dangling external-reference endpoints; ini adalah referensi eksternal yang tidak direpresentasikan sebagai node project, bukan endpoint source yang hilang.

Editor browser QA authenticated belum diulang karena sesi browser editor tidak tersedia; verifikasi renderer publik dan test/fake tetap lulus.
