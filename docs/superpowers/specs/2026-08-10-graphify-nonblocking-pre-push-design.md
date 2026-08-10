# Graphify Non-Blocking Pre-Push Design

## Masalah

Hook `.githooks/pre-push` selalu menjalankan `.githooks/graphify-sync` secara sinkron. Pembaruan Graphify yang lambat atau macet membuat `git push` ikut menunggu dan dapat menumpuk proses Git, shell, PowerShell, dan Graphify.

## Perilaku yang disetujui

- `git push origin main` tidak menjalankan Graphify dan tidak menunggu pembaruan graph.
- Sinkronisasi otomatis lain (`post-commit`, `post-checkout`, `post-merge`, dan `post-rewrite`) tetap tidak berubah.
- Pemeriksaan Graphify sebelum push tersedia sebagai opt-in melalui `GRAPHIFY_RUN_PRE_PUSH=1`.
- `GRAPHIFY_SKIP_HOOK=1` tetap tersedia sebagai opt-out umum untuk hook Graphify lainnya.

## Implementasi

Tambahkan guard di awal `.githooks/pre-push` yang langsung keluar kecuali `GRAPHIFY_RUN_PRE_PUSH=1`. Jangan membuat background process, timeout manager, atau dependency baru. Perbarui dokumentasi Git sync dan tambahkan behavior test Node yang menjalankan hook di repository Git sementara dengan bridge Graphify palsu.

## Kriteria penerimaan

1. Menjalankan hook tanpa `GRAPHIFY_RUN_PRE_PUSH` berstatus sukses dan tidak memanggil bridge Graphify.
2. Menjalankan hook dengan `GRAPHIFY_RUN_PRE_PUSH=1` berstatus sukses dan memanggil bridge tepat sekali dengan event `pre-push`.
3. Seluruh shell hook tetap lulus pemeriksaan sintaks.
4. Repository tetap tidak mengikutsertakan `graphify-out` atau backup dalam perubahan.

