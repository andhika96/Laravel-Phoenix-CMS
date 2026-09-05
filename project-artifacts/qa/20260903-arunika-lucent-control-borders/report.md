# Arunika Lucent Mobile Control Borders — QA Report

Tanggal: 2026-09-03

## Perubahan

- Kontrol `.btn` outline pada content mobile Lucent dipaksa memakai border 1px.
- `.form-check-input` mobile memakai `appearance: none`, ukuran `1em`, radius Bootstrap, dan border 1px agar native border tidak menumpuk.
- `box-shadow` dan outline luar ganda dihapus dari state focus mobile; state fokus keyboard tetap terlihat melalui state visual kontrol.
- Semua aturan tetap dibatasi pada `max-width: 768px` melalui stylesheet mobile Lucent.

## Verifikasi runtime

- Harness viewport `300x844`, `400x844`, dan `500x844`: checkbox `appearance:none`, border `1px solid`, tombol outline border `1px solid`, tanpa outline/box-shadow ganda.
- Pointer focus pada tombol: `focus-visible=false`, outline `none`, box-shadow `none`.
- Keyboard focus pada tombol: `focus-visible=true`, outline `none`, box-shadow `none`, background state tetap terlihat.
- Keyboard focus pada checked checkbox memakai background accent yang sedikit lebih gelap agar fokus tetap terlihat tanpa outer outline.
- Checked checkbox mempertahankan background hijau dan checkmark.
- Horizontal overflow: `scrollWidth === clientWidth` pada 300/400/500px.
- Cached harness run: 0 error, 0 warning. Fresh reload berikutnya mendapat `ERR_CONNECTION_RESET` dari server Python lokal saat memuat Bootstrap/Font Awesome; ini batasan static-server QA, bukan error aplikasi yang terdeteksi.
- Breakpoint `769px`: override mobile tidak aktif; checkbox tetap mengikuti style desktop.

## Test dan pemeriksaan

- Focused Lucent/mobile/theme suite: **31 passed, 0 failed**.
- Broader Arunika suite: **105 passed, 4 failed** pada kontrak Aurora/Prism yang tidak terkait file perubahan ini dan sudah dirty sebelumnya.
- `php artisan view:cache`: pass.
- `node --check` untuk theme scripts, mobile controller, dan dashboard: pass.
- `git diff --check`: pass; hanya warning normalisasi CRLF.
- Graphify: `graphify update . --no-cluster` dan `graphify check-update .` selesai tanpa perubahan graph tertunda.

## Artifacts

- Screenshot QA: [controls-400.png](./controls-400.png)
- Backup sebelum patch appearance/focus: `project-artifacts/backups/20260903_202500_arunika-lucent-control-appearance/`

Live CMS route authenticated belum diverifikasi pada sesi ini. Tidak ada database mutation, Save/Delete/Logout, commit, atau push.
