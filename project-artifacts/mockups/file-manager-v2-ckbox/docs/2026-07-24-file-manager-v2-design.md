# File Manager V2 Interactive Mockup Design

## Goal

Membuat mockup interaktif file manager baru yang terpisah dari implementasi lama, mengadaptasi pola UX CKBox untuk Local storage dan Cloudflare R2.

## Direction

Layout memakai navigation rail, storage switcher, asset toolbar, responsive gallery/list, contextual properties drawer, serta upload queue yang tetap terlihat ketika pengguna menavigasi folder. Gaya visual menggunakan ritme Bootstrap 5 dan bahasa visual Phoenix CMS yang bersih.

## Scope

- Realistic mock data tanpa backend.
- Local dan Cloudflare R2 sebagai dua storage profile.
- Bulk file/folder upload, drag-and-drop, progress queue, pause/resume, retry/cancel, dan settings.
- Search, filters, sorting, folders, multi-selection, bulk toolbar, dan properties drawer.
- Vue 3 SFC yang di-compile oleh Vite.
- Build statis portabel di `dist`.

## Out of Scope

- Laravel routes, controller, database, Flysystem, signed URL, multipart API, dan credential persistence.
- Mengubah atau menghapus file manager lama.
- Upload file sebenarnya ke Local atau R2.

## Future Runtime Architecture

Frontend compiled berkomunikasi dengan API Laravel melalui adapter contract yang sama untuk Local dan S3-compatible storage. File kecil dapat memakai proxied upload, sedangkan objek besar ke R2 memakai presigned multipart upload, bounded concurrency, resumable upload state, retry with exponential backoff, dan server-side finalization.

## Validation

- Vite production build harus berhasil.
- Static contract memverifikasi fitur utama ada pada source.
- Visual smoke test memeriksa desktop layout dan interaksi utama.
