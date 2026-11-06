# Tabel Manage Event mengikuti Manage Article dan 100 event Demo

Tanggal: 6 November 2026. Project: Laravel13 Phoenix, `D:\Laragon\www\laravel-13-phoenix`.

## Hasil

Manage Event memakai pola tabel dan stylesheet Manage Article: header actions, status dropdown dengan jumlah data, pencarian debounce300ms, panel filter/chip kategori dan visibility, urutan judul/tanggal pembaruan, pilihan5/10/15/25/50 baris, footer rentang dan pagination. Kolom event menampilkan judul+kategori, jumlah sesi, status, next occurrence dan opsi. Thumbnail/ringkasan/visibility/tautan event tersedia pada detail baris.

Detail bisa dibuka dari chevron/judul/baris. Checkbox tetap independen dari disclosure. Mobile mengikuti pilihan pengguna pada Article sebelumnya: kontrol di kiri, status/jadwal ditumpuk, menu tindakan di kanan, mode Pilih, select-all, Escape dan long press700ms. Handler pembatalan gerakan/scroll/blur mengikuti source Article aktual. Bulk status memakai endpoint Event existing dengan confirmation modal. Loader berada di isi tabel; error menyediakan retry, empty state menyediakan reset.

Article tetap memakai view dan JavaScript sebelumnya. Stylesheet yang sama diperluas untuk root/row Event dengan specificity setara; aksi header Event yang hanya dua tombol memakai lebar penuh di mobile. Tidak ada framework/plugin/dependency baru.

## Memori, Graphify dan source

- Memori utama `E:\AI\Memories` diperiksa tetapi drive tidak tersedia. Fallback yang relevan dibaca: `C:\Users\aruna\.codex\memories_v2\rollout_summaries\2026-10-26T07-16-37-IIeG-manage_article_mobile_kontrol_di_kiri_longpress_implementati.md`, terutama keputusan status di toolbar, centered loading dan mobile Kontrol di kiri/long press (baris12–24,39–61). Kondisi source/runtime kemudian diverifikasi langsung.
- `git status --short` diperiksa sebelum edit; perubahan auth/appearance sebelumnya dipertahankan. Baseline status disimpan dalam folder ini.
- Graphify query `manage event article table DataTable` menemukan ManageEventVue3, model Event, Manage Event/Article controllers dan resource Event. Explain `ManageEventVue3` menunjukkan relasi **EXTRACTED contains** ke script Event. Query luas terpotong sehingga tidak dipakai untuk mengasumsikan relasi lain. File aktual dibaca untuk memastikan pemilik perilaku. Tidak ada relasi INFERRED/AMBIGUOUS yang dijadikan bukti.
- Graphify tidak diperbarui karena instruksi manual. SHA256 tetap `0cb6deffe24e3d77dd2d4a59281835dc773a73f6155c3bab9650f4a67056ecb6`.

Source dibaca: view/CSS/JavaScript Manage Article dan partial statusnya; view/JavaScript/controller Manage Event; ManageEventListResource, EventOccurrenceResource; model Event/EventCategory/EventOccurrence; migration event; routes; serta tes Template/HttpFlow Event existing. Penelusuran caller meliputi loadEvents/listData, list-url, kategori, bulkUpdate, pagination, responsive handlers dan CSS selector root.

## Perubahan source dan backup

Empat file existing dibackup sebelum perubahan; path absolut dan SHA256 yang cocok ada dalam `backups.json`:

1. `resources/views/manage_event/manage_event.blade.php`: memuat style tabel yang sama dan include partial list. Modal serta alur kategori existing dipertahankan.
2. `public/assets/js/vue3/manage_event/vueV3-manage-event-2026.js`: state/control tabel, request terbaru menang, error/retry, clamp halaman setelah penghapusan, selection/disclosure/responsive dan bulk status. Metode kategori/delete existing tetap dipakai. ResizeObserver memiliki fallback resize.
3. `app/Http/Controllers/Web/Manage_Event/Manage_Event_Controller.php`: hanya listData ditambah sort whitelist, ukuran halaman whitelist, urutan stabil dengan ID, serta status_counts mengikuti pencarian/kategori/visibility. Route, permission dan endpoint bulk tidak diubah.
4. `public/assets/css/article/manage-article-2026.css`: selector diperluas untuk Event dengan specificity yang sama; memakai tokens/template CMS yang sudah ada.

Dua partial source baru: `resources/views/manage_event/partials/list.blade.php` dan `list-status.blade.php`. Revisi selama QA juga memiliki backup bertimestamp dalam folder ini. Tidak ada commit, push, deploy atau perubahan Graphify.

## Pengujian

- Tes list baru memakai **SQLite in-memory**, seperti pola EventHttpFlow existing. Sebelum implementasi, pagination5 baris gagal (masih15) dan status_counts belum ada. Sesudah implementasi: **14 tests,123 assertions lulus**, meliputi tes baru dan Template/HttpFlow existing. Sort/page-size, filter, counts dan bulk hanya baris terpilih diuji tanpa mengubah database CMS.
- Binding request pada harness diperbaiki agar resolver pagination memakai request uji aktual. Source pagination aplikasi tidak diubah untuk masalah harness ini.
- QA browser mengungkap dua kesalahan markup baru: nested interpolasi Blade/Vue dan penutup option yang hilang. Keduanya diperbaiki sebelum hasil final. Diagnostik dan backup disimpan di folder ini.
- **18 kelompok QA Chrome lulus** (`browser-checks.json`): live initial load;10 kombinasi viewport320/390/768/1024/1440px × light/dark; detail dengan keyboard, checkbox, confirmation/bulk payload; search/per-page/pagination/sort/status; centered loading/error/retry/empty; mode pilih/select-all/Escape/long press; serta kompatibilitas Article pada390/820/1440px.
- Cek kompatibilitas Article membandingkan geometry/font/padding/display/warna memakai stylesheet saat ini versus backup stylesheet asli yang diinjeksi sementara. Pengukuran identik setelah font dan resize stabil. View/JS Article tidak dimodifikasi.
- QA tabel memakai data fixture31 baris untuk mencakup semua state. Bulk response dimock; mutasi Event diblokir. Hasil final: nol Vue/page errors dan nol mutasi Event/Article dari QA browser. HTTP500 yang disengaja hanya untuk menguji retry.
- Setelah seeding, **10 pemeriksaan data live lulus** (`live-demo-checks.json`):100 ID unik dari7 halaman,199 sesi, lima ukuran halaman, kategori20 event, visibility private20 event, pencarian #100 satu hasil, dan detail event. Request list live tidak dimock; tidak ada mutasi dari QA ini.
- Screenshot hasil data nyata: `live-event-desktop.png`, `live-event-mobile.png`. Screenshot fixture/light/dark tersimpan terpisah.
- Node syntax check, PHP lint controller dan `git diff --check` lulus. Tidak menjalankan suite seluruh repository atau view-cache global yang tidak diperlukan.

## Data dummy yang diminta pengguna

Setelah pekerjaan tabel dan QA selesai, dibuat tepat **100 event**, **199 sesi** (1–3/event) dan **5 kategori Demo**. Sebelum pembuatan, tabel events, event_occurrences dan event_categories kosong. Tiga tabel berprefix `lr_` diverifikasi memakai InnoDB. Snapshot ketiganya diambil sebelum insert dan disimpan sebagai `events-before-20261106_145853.json`, `occurrences-before-20261106_145853.json`, `categories-before-20261106_145853.json`.

- Seluruh event berjudul `[Demo] ... #001` sampai `#100`, berstatus **Draft**, tag `demo,dummy,event-table-20261106`, dan slug prefix `demo-event-table-20261106-`.
- Ada variasi topik/kota, ringkasan/konten, visibility80 public/20 private, kapasitas dan lokasi online/offline. Public visibility tidak mempublikasikan event karena status tetap Draft.
- Lima kategori `[Demo] Workshop/Webinar/Komunitas/Pelatihan/Seminar` dibuat karena belum ada kategori aktif, masing-masing dipakai20 event. Tidak membuat kategori tambahan jika kategori aktif sudah tersedia.
- Sesi dijadwalkan mulai7 November2026, timezone Asia/Jakarta. URL online menggunakan domain contoh example.com. Tidak ada registrasi, booking, reminder atau pengiriman email dibuat.
- Insert dalam satu transaksi. Fingerprint event lama dipastikan tetap sama. Seeder tidak menimpa data existing; percobaan ulang mendeteksi100 slug batch dan tidak membuat duplikasi.
- Manifest pemulihan `demo-batch.json` mencatat seluruh ID event, ID sesi, slug dan ID kategori yang dibuat. Jika kelak diminta menghapus data dummy, gunakan manifest ini dan periksa relasi/registrasi terbaru lebih dahulu; jangan restore seluruh tabel karena dapat menimpa data baru pengguna.
- Script `create-demo-events.php` defaultnya preflight read-only; penulisan hanya ketika `--create` diberikan. Preflight awal diperbaiki untuk mengenali prefix tabel sebelum ada data yang ditulis.

## Batas verifikasi

Responsive diperiksa melalui viewport Chrome, belum perangkat fisik atau browser lain. Bulk status diuji pada database in-memory dan mock browser, tidak diterapkan ke event CMS nyata saat QA. Penghapusan event/kategori nyata tidak dijalankan. Source dan data dummy hanya diterapkan pada instalasi lokal ini.
