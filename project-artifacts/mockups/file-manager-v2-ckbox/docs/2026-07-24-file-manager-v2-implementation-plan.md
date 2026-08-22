# File Manager V2 Mockup Implementation Plan

**Goal:** Menghasilkan mockup Vue 3 compiled yang portabel dan dapat dilanjutkan di PC lain.

**Architecture:** Vite + Vue SFC dengan komponen terpisah untuk navigation, asset card, properties drawer, upload queue, dan settings. Semua data berasal dari modul mock lokal tanpa koneksi backend.

**Tech Stack:** Vue 3, Vite, Bootstrap 5, Bootstrap Icons.

## Tasks

- [x] Scaffold project terpisah dan konfigurasi relative build.
- [x] Buat mock asset data dan Local/R2 storage profiles.
- [x] Implementasikan file browser, filters, selection, dan detail drawer.
- [x] Implementasikan bulk upload queue dan drag-and-drop simulation.
- [x] Implementasikan storage/upload settings.
- [ ] Install dependency dan hasilkan production build.
- [ ] Jalankan static contract dan visual smoke test.
