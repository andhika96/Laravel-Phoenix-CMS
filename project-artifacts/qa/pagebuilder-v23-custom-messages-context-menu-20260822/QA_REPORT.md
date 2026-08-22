# QA Page Builder v2.3 — Custom Messages dan Context Menu

- Tanggal: 2026-08-22
- URL: `https://laravel-13-phoenix.aruna/pagebuilder-elementor/v2.3/create`
- Browser: Chrome dengan sesi pengguna yang sudah login
- Dataset: Form baru di canvas, hanya state editor sementara
- Batas keselamatan: tidak menekan **Save**, **Reset**, **Apply dataset**, atau melakukan submit form nyata

## Hasil umum

Status: **LULUS** untuk scope runtime yang diuji.

- Tidak ada error atau warning pada console browser setelah seluruh alur QA.
- Semua variasi Custom Messages yang diuji tampil pada posisi, warna status, dan ukuran yang sesuai.
- Modal tetap muat pada canvas mobile 390 px.
- Context menu menutup saat klik kiri dilakukan di luar menu, baik pada canvas, sidebar, maupun topbar.
- Klik kiri di dalam menu tidak menutup menu sebelum sebuah action dipilih.
- Action `Edit Name` tetap berjalan dan menutup menu.
- Tombol `Escape` tetap menutup menu.

## Matriks Custom Messages

| Display type | Success | Error | Interaksi yang diverifikasi |
| --- | --- | --- | --- |
| Basic | Lulus | Lulus | Pesan tampil setelah tombol submit |
| Above Form | Lulus | Lulus | Pesan tampil di atas fields; dismiss menghapus pesan |
| Toast | Lulus | Lulus | Toast tampil di kanan atas; tombol close menghapus toast |
| Modal | Lulus | Lulus | `role=alertdialog`, close dan backdrop dismiss bekerja |

Kontrol kondisional yang juga diverifikasi:

- `Show Icon = false`: icon tidak dirender.
- `Dismissible = false`: tombol dismiss tidak dirender dan klik backdrop tidak menutup modal.
- `Dismissible = true`: tombol close dan backdrop dapat menutup modal.

## Matriks Context Menu

| Skenario | Sebelum | Sesudah | Hasil |
| --- | ---: | ---: | --- |
| Klik area kosong di dalam menu | 1 menu | 1 menu | Lulus |
| Klik kiri area canvas di luar menu | 1 menu | 0 menu | Lulus |
| Klik kiri sidebar di luar canvas | 1 menu | 0 menu | Lulus |
| Klik kiri topbar di luar canvas | 1 menu | 0 menu | Lulus |
| Pilih `Edit Name` | 1 menu | 0 menu | Lulus; action tetap aktif |
| Tekan `Escape` | 1 menu | 0 menu | Lulus |

## Bukti screenshot

- `01-basic-success.png`
- `02-basic-error.png`
- `03-above-form-error.png`
- `04-above-form-success.png`
- `05-toast-success.png`
- `06-toast-error.png`
- `07-modal-error.png`
- `08-modal-success.png`
- `09-mobile-modal-success.png`
- `10-context-menu-open.png`
- `11-context-menu-closed-outside.png`

## Catatan visual

- Hierarki success/error konsisten: icon, title, message, dan dismiss mudah dikenali.
- Toast tidak menutupi panel pengaturan dan tetap berada di sudut kanan canvas.
- Modal memiliki kontras backdrop yang cukup dan tetap proporsional pada mode desktop maupun mobile.
- Context menu sesuai target field dan tidak lagi bertahan setelah interaksi di luar menu.

## Batas verifikasi

- QA ini memverifikasi editor/canvas runtime pada state sementara, bukan persistence setelah Save atau reload.
- Submit backend nyata tidak dilakukan; success/error dipicu melalui fasilitas preview message di editor.
- Audit modularitas widget/layout/grid dicatat terpisah pada `project-artifacts/qa/pagebuilder-v23-modularity-audit-20260822/MODULARITY_AUDIT.md`.
