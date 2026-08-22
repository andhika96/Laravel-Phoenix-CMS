# Arunika File Manager V2 — Interactive Mockup

Mockup terpisah untuk eksplorasi file manager baru bergaya CKBox. Folder ini tidak memakai atau mengubah implementasi file manager lama.

## Menjalankan

```powershell
cd project-artifacts/mockups/file-manager-v2-ckbox
npm install
npm run dev
```

Buka URL yang ditampilkan Vite.

## Membuka build statis

Setelah `npm run build`, buka `dist/index.html`. Konfigurasi Vite menggunakan relative base supaya build dapat dipindahkan bersama folder project.

## Interaksi yang tersedia

- Berpindah antara Local storage dan Cloudflare R2.
- Membuka folder, filter tipe file, pencarian, sorting, serta grid/list view.
- Single atau multi-selection dengan `Ctrl`/`Cmd`.
- Detail drawer dan bulk action toolbar.
- Upload file, upload folder, drag-and-drop, serta simulasi upload queue.
- Pause/resume, cancel, minimize, dan progress per file.
- Pengaturan custom local root, koneksi R2, concurrency, multipart chunk, retry, conflict strategy, resume, dan checksum.

Mockup menggunakan data simulasi; belum ada request ke API Laravel atau storage sebenarnya.
