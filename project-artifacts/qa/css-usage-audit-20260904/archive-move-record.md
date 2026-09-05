# CSS Archive Move Record

Tanggal: 2026-09-05  
Permintaan: pindahkan CSS yang sudah diaudit sebagai `UNUSED_HIGH_CONFIDENCE`.  
Mode: pemindahan eksplisit dengan backup SHA-256; tidak ada file lain yang disentuh.

## Destination

`public/assets/archived/css`

## Files moved

| File | Source sebelum move | Destination | Backup SHA-256 cocok |
|---|---|---|---|
| `aruna-admin-v6.css` | `public/assets/css/aruna-admin-v6.css` | `public/assets/archived/css/aruna-admin-v6.css` | Ya |
| `aruna-admin-v7-phoenix-elegant.css` | `public/assets/css/aruna-admin-v7-phoenix-elegant.css` | `public/assets/archived/css/aruna-admin-v7-phoenix-elegant.css` | Ya |
| `aruna-admin-v7-simple-part-2.css` | `public/assets/css/aruna-admin-v7-simple-part-2.css` | `public/assets/archived/css/aruna-admin-v7-simple-part-2.css` | Ya |
| `aruna-admin-v7-simple.css` | `public/assets/css/aruna-admin-v7-simple.css` | `public/assets/archived/css/aruna-admin-v7-simple.css` | Ya |
| `aruna-admin-v7.css` | `public/assets/css/aruna-admin-v7.css` | `public/assets/archived/css/aruna-admin-v7.css` | Ya |
| `aruna-v3.css` | `public/assets/css/aruna-v3.css` | `public/assets/archived/css/aruna-v3.css` | Ya |

## Backup

`project-artifacts/backups/20260905_000000_archive-unused-css/`

Manifest dan SHA-256 tersedia di `manifest.csv`. Backup diverifikasi sebelum pemindahan.

## Post-move verification

- Original paths: 6/6 tidak ada.
- Archive paths: 6/6 ada.
- Archive-to-backup SHA-256: 6/6 cocok.
- CSS aktif di `public/assets/css`: 27 file.
- CSS di `public/assets/archived/css`: 6 file.
- Referensi produksi ke enam basename lama: 0.
- File archive tidak ikut dihitung sebagai CSS aktif pada audit lanjutan.
- Tidak ada database, route, layout, atau konfigurasi yang diubah.
- Tidak ada file CSS kondisional atau theme aktif yang dipindahkan.

## Recovery

Pemindahan dapat dipulihkan dengan menyalin file dari archive atau backup ke path asal setelah dilakukan review. Tidak ada data yang dihapus permanen.

