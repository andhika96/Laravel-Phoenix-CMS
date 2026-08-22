# Project Artifacts

Folder ini adalah pusat artefak non-runtime Phoenix CMS.

- `backups/` menyimpan rollback lokal dan manifest hash. Isinya tidak dilacak Git.
- `mockups/` menyimpan source mockup yang masih menjadi referensi desain atau test.
- `qa/` menyimpan laporan dan bukti verifikasi berdasarkan fitur.
- `previews/` menyimpan preview visual yang masih dipertahankan.

Direktori teknis `node_modules/` dan `graphify-out/` tetap di root karena dipakai tooling. Final mockup yang harus dapat dibuka melalui browser tetap berada di `public/mockups/`.
