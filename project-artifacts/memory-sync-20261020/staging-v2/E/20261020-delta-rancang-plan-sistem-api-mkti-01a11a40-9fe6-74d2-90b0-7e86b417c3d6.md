# Rancang plan sistem API MKTI

Tanggal penyimpanan: 20 Oktober 2026 (WIB)
Project: Laravel 13 CMS BUM
Project saat chat: D:\Laragon\www\laravel-13-cms-bum
Chat ID: 01a11a40-9fe6-74d2-90b0-7e86b417c3d6
Jenis sumber: codex-local
Rentang chat: 2026-10-08 13:44:57 WIB sampai 2026-10-14 09:33:01 WIB
Tindakan: update
Evidence SHA-256: 6078c0cd295725859a56b36b96f359c8859f1390ac571e612a12281be26d8bcc

## Tujuan dan konteks awal

- ChatGPT dari file PDF terbaru dichat ini [@Jelaskan MKTI untuk pemupukan sawit](thread://01a0ad4c-441c-78c1-a0be-e8964110b92f?hostId=local) dan dari file dokumen excel yang saya kirimkan ini, buatkan plan yang matang dan proper untuk sistem APInya
- ChatGPT dari file PDF terbaru dichat ini [@Jelaskan MKTI untuk pemupukan sawit](thread://01a0ad4c-441c-78c1-a0be-e8964110b92f?hostId=local) dan dari file dokumen excel yang saya kirimkan ini, buatkan plan yang matang dan proper untuk sistem APInya di CMS BUM kita ini.

## Keputusan dan implementasi historis

Laporan di bawah merupakan riwayat pekerjaan, bukan pengujian runtime terbaru pada tanggal penyimpanan. Checkpoint terbaru mengungguli pending dari laporan lebih lama.

- Keputusan Anda:** Go MKTI dan Go Blok dikerjakan bersama; payroll bertahap setelah rumus disepakati.
- Arah rancangan sementara, bagian yang belum selesai, dan urutan untuk melanjutkan tanpa mengulang pemeriksaan yang masih valid.
- | Source aplikasi dan database | Tidak diubah |
- | PDF dan Excel sumber | Tidak diubah; hash sumber tersimpan |
- | Rencana API lengkap | Belum selesai; belum ada implementasi |
- Rencana sudah dilanjutkan dari checkpoint, dan API List yang ingin dibuat sudah tersedia.** Scope tetap **Go MKTI Emdek, Mounted Sprayer, dan Go Blok bersama**, dengan payroll bertahap setelah rumus serta kewenangannya disepakati.
- [**Rencana lengkap API Go MKTI dan Go Blok**](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/PLAN_API_GO_MKTI_GO_BLOK_20261009.md) — arsitektur, model data, alur persetujuan, integrasi, aturan laporan, tahapan implementasi, dan skenario pengujian.
- | GET | `/units` | Unit dan implement yang cocok untuk Emdek/Sprayer |
- | PATCH | `/activity-types/{id}` | Perubahan metadata atau penonaktifan kegiatan |
- | GET, PATCH | `/rkb/{id}` | Detail dan perubahan draft RKB |
- | GET, PATCH | `/rkh/{id}` | Detail dan perubahan draft RKH, termasuk kru, cover, target, dan bahan |
- | GET, PATCH | `/block-maps/{id}` | Detail dan perubahan draft peta/lampiran |
- | GET | `/units/{id}/readiness` | Kesiapan unit berdasarkan P2H, PM10, dan keputusan workshop |
- | GET, PATCH | `/work-results/{id}` | Detail dan perubahan draft hasil kerja |
- Rujukan tambahan tersedia di transkrip kronologis.

## Checkpoint substantif terakhir

Tanggal checkpoint: 2026-10-13 09:04:07 WIB

**Konteks Android ini penting. Hasil cross-check saya: alur besar rencana sudah sesuai, tetapi kontraknya belum cukup lengkap untuk langsung dijadikan pegangan implementasi aplikasi Android.** Ada kebutuhan dari Excel dan materi asli yang belum terwakili dengan tegas, serta beberapa aturan yang kita usulkan terlalu kuat dibandingkan bukti sumber.

Saya sudah membaca kembali chat rujukan, memeriksa Excel asli, PDF draft 23 halaman, bagian relevan PDF Preskom asli—terutama halaman 71, 146, 228, dan 229—serta melihat dua foto GoMKTI tentang pekerjaan/rute dan biaya. Hash Excel dan PDF draft masih identik dengan sumber yang dipakai saat membuat rencana.

**Bagian yang sudah sesuai**

| Bagian | Hasil pencocokan |
|---|---|
| RKB → PBLK → RKH | Sesuai urutan dalam PDF dan koreksi operasional Anda. |
| SPMB disetujui EM, lalu diperiksa KTU | Sesuai PDF draft dan konteks chat. |
| Gudang → Mandor → Operator | Sudah terwakili melalui transaksi dan konfirmasi penerimaan bahan. |
| Operator, Aplikator, dan Pemuat | Excel memisahkan kontribusi mereka; rencana sudah menyediakan penugasan dan alokasi hasil. |
| Emdek berbasis kg/pokok, Sprayer berbasis ha | Sudah sesuai, termasuk pencatatan rit, waktu, HM, bahan, dan dokumentasi. |
| Absensi oleh Mandor Rawat dan solar oleh Kepala Tangki | Sesuai catatan workbook. |
| Pemeriksaan hasil dan PBLH | Pembuat serta pihak verifikasi sudah terwakili. |
| Payroll bertahap | Tepat, karena rumus tertulis dan contoh Excel memang bertentangan. |

**Bagian yang perlu diperbaiki sebelum kontrak API Android dibekukan**

| Temuan | Bukti sumber | Perbaikan yang diperlukan |
|---|---|---|
| **Go Blok terlalu dibatasi menjadi pekerjaan manual** | Sheet `14` mencakup Aplikator, Pemuat Pupuk, dan tenaga perawatan manual. Sheet `18!C13:F15` menampilkan Mounted Sprayer bersama pekerjaan lain dalam laporan pekerja Go Blok. | Go Blok harus dapat menampilkan **pekerjaan dan kontribusi seorang pekerja**, termasuk kontribusi pada pekerjaan MKTI. `execution_mode` menjelaskan cara pekerjaan dilakukan; jangan dipakai untuk meniadakan tugas mekanis dari tampilan pekerja Go Blok. |
| **Pendapatan rencana belum terwakili secara jelas** | Sheet `8, 11!C14:H17` menampilkan volume, HK, dan penghasilan sebelum pekerjaan dimulai. Form MKTI pada sheet `9, 12` dan `10, 13` juga menampilkan penghasilan rencana. | Respons tugas Android perlu mendukung **estimasi berdasarkan rencana**, terpisah dari estimasi berdasarkan realisasi dan upah final. `/payroll-previews` sekarang hanya dijelaskan berdasarkan hasil terverifikasi. |
| **Hubungan dengan RKH Panen belum masuk rancangan secara eksplisit** | Sheet `5,6,7!I29` dan `I44` menyebut “Otomatis dari RKH Panen dibagi jumlah WT di afdeling tersebut”. | Tentukan field mana yang dimaksud dan aturan pembagiannya. Catatan ini perlu dikonfirmasi karena belum cukup jelas untuk langsung dijadikan algoritme; jangan diabaikan maupun disalin mentah. |
| **Pemblokiran PM10 terlalu tegas dalam sebagian skenario uji** | PDF draft halaman 8 meminta pemeriksaan rencana/realisasi dan koordinasi dengan workshop. Ketidaklengkapan catatan tidak secara otomatis dinyatakan membuat unit tidak layak. | Bedakan **unit tidak layak**, **catatan belum lengkap**, dan **perlu tindak lanjut**. Aturan yang benar-benar melarang pekerjaan harus berasal dari SOP/keputusan workshop. |
| **Peta dasar dan progres PBLH sebelumnya belum menjadi prasyarat yang jelas** | PDF Preskom halaman 71 meminta PBLK mengacu pada peta kondisi dasar terverifikasi dan progres PBLH berjalan. | PBLK perlu menyimpan referensi **versi peta dasar dan progres hasil sebelumnya**, bukan hanya hubungan dengan item RKB. |
| **Waktu submit API belum tentu sama dengan waktu pengiriman dokumen operasional** | PDF halaman 71 menyebut pengiriman PBLK ke grup pada H-1 pukul 17.00 dan PBLH H-0 pukul 19.00. | Bedakan waktu dibuat di HP, diterima server, diverifikasi, dan benar-benar dikirim/disampaikan. Jangan menghitung insentif ketepatan waktu hanya dari waktu upload. Jam pada materi lama tetap perlu konfirmasi penerapannya. |
| **Rincian kalibrasi dan larutan semprot belum cukup jelas** | PDF Preskom halaman 228–229 menyebut kapasitas alat, kalibrasi, dosis pupuk, herbisida, air, dan kebutuhan per rit. | Bedakan **herbisida pekat, air, dan larutan semprot**, serta referensi kalibrasi alat. Liter larutan tidak boleh dianggap sama dengan liter herbisida. |
| **SIV dan hubungan ke OPS/E-Plantation belum dipetakan secara konkret** | PDF Preskom halaman 228–229 menyebut lampiran Store Issue Voucher dan pencatatan oleh Krani PHM2D, termasuk keseimbangan stok. | Tentukan hubungan SPMB–BPB–SIV dan sistem pencatatan resminya. Jangan menganggap ketiga dokumen identik atau mengklaim saldo real-time sebelum integrasinya tersedia. |

Ada juga detail laporan yang perlu ditulis lebih tegas: **status TM, luas dan populasi blok saat perencanaan, varian rencana–realisasi, rasio material L/ha atau g/ha, serta nomor kontak Operator sesuai kewenangan akses**. Sebagian bisa ditampung oleh struktur saat ini, tetapi belum menjadi daftar field kontrak yang pasti.

**Untuk Android, API sebaiknya disusun mengikuti layar dan tindakan pengguna**

Daftar endpoint yang sudah ada masih berguna. Yang perlu dilengkapi terutama **isi respons, tindakan yang diizinkan, dan perilaku saat offline**, bukan langsung menambah banyak endpoint.

| Kebutuhan Android | API yang dapat digunakan | Kontrak yang perlu dipastikan |
|---|---|---|
| Beranda tugas hari ini | `/my-assignments` | Tugas, peran pengguna, blok, unit, target, status, revisi, dan tindakan yang boleh dilakukan |
| Detail tugas | `/rkh/{id}` dan hasil terkait | Rencana, kontribusi pribadi, kru, bahan, sumber peta, estimasi pendapatan, serta progres |
| Mulai dan selesai pekerjaan | `/work-sessions`, `/work-results`, `/work-trips/{id}` | Perbedaan mulai kerja, menyimpan draft, menyelesaikan rit, submit hasil, dan close sesi |
| Input oleh Operator atau Mandor untuk pekerja lain | Endpoint hasil dan alokasi | Identitas **pencatat**, **pekerja pemilik hasil**, dan dasar kewenangannya |
| Foto saat sinyal buruk | `/attachments` | UUID lokal, status upload, retry, hubungan dengan hasil, dan perilaku ketika upload terputus |
| Sinkronisasi setelah offline | `/sync/changes`, `/sync/submissions` | Urutan dependensi, hasil per item, konflik revisi, pembatalan tugas, dan pemulihan setelah aplikasi ditutup |
| Riwayat hasil dan penghasilan | `/work-results`, `/reports/go-blok`, preview payroll | Kontribusi pekerja, hasil dilaporkan/diterima, estimasi rencana/realisasi, serta status final |
| Perubahan tugas atau cover | Delta sinkronisasi dan penugasan | Apa yang dilakukan Android terhadap draft lokal ketika assignment direvisi atau dicabut |

Contoh yang harus ditangani dengan jelas: **Operator menyimpan rit saat offline, lalu RKH direvisi oleh CMS sebelum HP kembali online.** Server harus menjelaskan apakah rit masih terkait penugasan yang sah, perlu pemeriksaan, atau berkonflik. Android harus mempertahankan draft dan menampilkan tindakan perbaikannya, bukan menghapus hasil atau mengirim ulang sebagai transaksi baru.

Demikian pula, jika **Aplikator mencatat hasilnya sendiri dan Operator juga mencatat total rit unit**, keduanya perlu dihubungkan ke pekerjaan yang sama. Menjumlahkan kedua input sebagai hasil fisik baru akan menggandakan volume.

**Rumus Excel tetap perlu diseleksi**

Temuan sebelumnya masih berlaku:

- Sheet `14` dan `15` berbeda dalam tarif golongan serta dasar premi Operator.
- Ada **12 formula `#REF!`** pada sheet `19` yang disembunyikan dengan `IFERROR`.
- Kolom berlabel **Ltr/HM** pada contoh laporan menghitung arah pembagian yang terbalik.
- Dosis gabungan dijumlahkan pada contoh, padahal perlu total kg dibagi total pokok.
- Pada materi Sprayer asli, uraian menyebut **4,64 liter per rit**, tetapi salah satu baris perhitungan berikutnya menulis **6,64**; hasil akhirnya perlu dihitung ulang sesuai dasar yang disepakati.

Karena itu, **rumus dan nominal authoritative harus dihitung server**. Android boleh menampilkan hasil atau estimasi offline yang diberi status jelas, tetapi tidak menentukan nominal final sendiri.

Penilaian saya sekarang: **rencana layak sebagai dasar arsitektur dan urutan fase, tetapi perlu revisi substansial pada kontrak bisnis dan kontrak Android sebelum implementasi.** Pernyataan sebelumnya bahwa rencana “selesai” berarti dokumennya sudah tersusun; cross-check ini menunjukkan bahwa kelengkapan dokumen belum sama dengan kesesuaian seluruh kebutuhan sumber.

Saya belum mengubah rencana atau source aplikasi pada pemeriksaan ini. Pemeriksaan foto mencakup dua foto GoMKTI yang tersedia dan halaman sumber terkait; belum merupakan audit ulang visual seluruh foto historis maupun pengujian aplikasi Android.

## File dibuat, dimodifikasi, atau dirujuk

- Add: D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/CHECKPOINT_20261008.md
- Add: D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/API_LIST_GO_MKTI_GO_BLOK_20261009.md
- Add: D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/PLAN_API_GO_MKTI_GO_BLOK_20261009.md
- Add: D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/FASE_PEMBUATAN_API_GO_MKTI_GO_BLOK_20261012.md

- [**Buka checkpoint perencanaan API Go MKTI dan Go Blok**](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/CHECKPOINT_20261008.md)
- [**API List Go MKTI dan Go Blok**](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/API_LIST_GO_MKTI_GO_BLOK_20261009.md) — metode HTTP, path, fungsi, tahap, dan ketentuan setiap kelompok API.
- [**Rencana lengkap API Go MKTI dan Go Blok**](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/PLAN_API_GO_MKTI_GO_BLOK_20261009.md) — arsitektur, model data, alur persetujuan, integrasi, aturan laporan, tahapan implementasi, dan skenario pengujian.
- Pemeriksaan terbaru sudah membuktikan daftar metode/path tidak duplikat, hitungan contoh konsisten, dan prefix usulan belum berbenturan dengan route aktif. `php artisan route:list` berhasil dijalankan. Hash PDF/Excel, source yang dicatat dalam baseline, serta 13 perubahan awal Graphify tetap identik. Bukti tersimpan di [hasil verifikasi rencana](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/plan-verification-20261009.json).
- [**Rencana lengkap API Go MKTI dan Go Blok**](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/PLAN_API_GO_MKTI_GO_BLOK_20261009.md) — arsitektur, model data, alur persetujuan, integrasi, validasi, tahapan implementasi, dan skenario pengujian.
- [**API List Go MKTI dan Go Blok**](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/API_LIST_GO_MKTI_GO_BLOK_20261009.md) — **65 baris route yang mencakup 84 operasi HTTP**, lengkap dengan fungsi dan tahap pembuatannya.
- [**Buka rencana fase pembuatan API Go MKTI dan Go Blok**](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/FASE_PEMBUATAN_API_GO_MKTI_GO_BLOK_20261012.md)

## Backup historis

- | Graphify | Graph tidak diperbarui; penanda cache yang berubah akibat query sudah dipulihkan setelah dibackup |

## Pengujian dan verifikasi historis

- File checkpoint sudah diverifikasi berada di folder `project-artifacts` proyek. **Saya tidak melanjutkan pekerjaan sampai Anda meminta lanjut.
- | POST | `/block-maps/{id}/reviews` | Evaluasi PBLK atau verifikasi PBLH sesuai peran |
- PBLK dibuat sebelum RKH. PBLH mengacu pada hasil kerja yang telah diperiksa, dengan verifikasi Audit dan PHM2D tercatat masing-masing.
- | POST | `/work-results/{id}/inspections` | Memverifikasi atau mengembalikan hasil |
- Pencatatan lapangan mendukung retry dengan UUID agar rit, bahan, dan foto tidak tercatat ganda. Approval, posting stok, verifikasi final, serta release payroll tetap dilakukan online. Membuka atau mengekspor laporan bersifat baca-saja.
- | POST | `/payroll-batches/{id}/decisions` | Verifikasi/persetujuan payroll sesuai otorisasi |
- Preview tidak memposting upah.** Payroll final membutuhkan rumus yang disahkan, hasil terverifikasi, kehadiran yang diakui, dan rekonsiliasi lintas modul. `release` menyediakan rekap yang terkunci; pembayaran bank membutuhkan integrasi dan otorisasi tersendiri.
- Pemeriksaan terbaru sudah membuktikan daftar metode/path tidak duplikat, hitungan contoh konsisten, dan prefix usulan belum berbenturan dengan route aktif. `php artisan route:list` berhasil dijalankan. Hash PDF/Excel, source yang dicatat dalam baseline, serta 13 perubahan awal Graphify tetap identik. Bukti tersimpan di [hasil verifikasi rencana](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/plan-verification-20261009.json).
- Endpoint baru masih berupa rancangan.** Pengujian bisnis endpoint terautentikasi, schema database target, stok, dan payroll dilakukan pada tahap implementasi. Graphify dipakai untuk menelusuri hubungan source secara baca-saja dan tidak diperbarui. Daftar source, memori, serta batas verifikasinya tercatat dalam dokumen rencana dan checkpoint.
- | POST | `/block-maps/{id}/reviews` | Mencatat evaluasi PBLK atau verifikasi PBLH sesuai peran |
- PBLK menjadi dasar lokasi rencana sebelum RKH. PBLH mengacu pada realisasi yang telah diperiksa, dengan catatan verifikasi Audit dan PHM2D masing-masing.
- | POST | `/work-results/{id}/inspections` | Memverifikasi atau mengembalikan hasil, termasuk jumlah yang diterima |
- | GET | `/reports/plan-vs-actual` | Perbandingan rencana, realisasi terverifikasi, hasil belum diperiksa, serta pekerjaan tersisa |
- Pencatatan lapangan memakai identitas submission agar retry tidak menggandakan data. Approval, posting stok, verifikasi final, dan release payroll dilakukan online.
- Rujukan tambahan tersedia di transkrip kronologis.

## Masalah belum selesai dan langkah lanjutan

- Konteks Android ini penting. Hasil cross-check saya: alur besar rencana sudah sesuai, tetapi kontraknya belum cukup lengkap untuk langsung dijadikan pegangan implementasi aplikasi Android.** Ada kebutuhan dari Excel dan materi asli yang belum terwakili dengan tegas, serta beberapa aturan yang kita usulkan terlalu kuat dibandingkan bukti sumber.
- Bagian yang perlu diperbaiki sebelum kontrak API Android dibekukan
- | Temuan | Bukti sumber | Perbaikan yang diperlukan |
- | **Go Blok terlalu dibatasi menjadi pekerjaan manual** | Sheet `14` mencakup Aplikator, Pemuat Pupuk, dan tenaga perawatan manual. Sheet `18!C13:F15` menampilkan Mounted Sprayer bersama pekerjaan lain dalam laporan pekerja Go Blok. | Go Blok harus dapat menampilkan **pekerjaan dan kontribusi seorang pekerja**, termasuk kontribusi pada pekerjaan MKTI. `execution_mode` menjelaskan cara pekerjaan dilakukan; jangan dipakai untuk meniadakan tugas mekanis dari tampilan pekerja Go Blok. |
- | **Pendapatan rencana belum terwakili secara jelas** | Sheet `8, 11!C14:H17` menampilkan volume, HK, dan penghasilan sebelum pekerjaan dimulai. Form MKTI pada sheet `9, 12` dan `10, 13` juga menampilkan penghasilan rencana. | Respons tugas Android perlu mendukung **estimasi berdasarkan rencana**, terpisah dari estimasi berdasarkan realisasi dan upah final. `/payroll-previews` sekarang hanya dijelaskan berdasarkan hasil terverifikasi. |
- | **Hubungan dengan RKH Panen belum masuk rancangan secara eksplisit** | Sheet `5,6,7!I29` dan `I44` menyebut “Otomatis dari RKH Panen dibagi jumlah WT di afdeling tersebut”. | Tentukan field mana yang dimaksud dan aturan pembagiannya. Catatan ini perlu dikonfirmasi karena belum cukup jelas untuk langsung dijadikan algoritme; jangan diabaikan maupun disalin mentah. |
- | **Pemblokiran PM10 terlalu tegas dalam sebagian skenario uji** | PDF draft halaman 8 meminta pemeriksaan rencana/realisasi dan koordinasi dengan workshop. Ketidaklengkapan catatan tidak secara otomatis dinyatakan membuat unit tidak layak. | Bedakan **unit tidak layak**, **catatan belum lengkap**, dan **perlu tindak lanjut**. Aturan yang benar-benar melarang pekerjaan harus berasal dari SOP/keputusan workshop. |
- | **Peta dasar dan progres PBLH sebelumnya belum menjadi prasyarat yang jelas** | PDF Preskom halaman 71 meminta PBLK mengacu pada peta kondisi dasar terverifikasi dan progres PBLH berjalan. | PBLK perlu menyimpan referensi **versi peta dasar dan progres hasil sebelumnya**, bukan hanya hubungan dengan item RKB. |
- | **Waktu submit API belum tentu sama dengan waktu pengiriman dokumen operasional** | PDF halaman 71 menyebut pengiriman PBLK ke grup pada H-1 pukul 17.00 dan PBLH H-0 pukul 19.00. | Bedakan waktu dibuat di HP, diterima server, diverifikasi, dan benar-benar dikirim/disampaikan. Jangan menghitung insentif ketepatan waktu hanya dari waktu upload. Jam pada materi lama tetap perlu konfirmasi penerapannya. |
- | **Rincian kalibrasi dan larutan semprot belum cukup jelas** | PDF Preskom halaman 228–229 menyebut kapasitas alat, kalibrasi, dosis pupuk, herbisida, air, dan kebutuhan per rit. | Bedakan **herbisida pekat, air, dan larutan semprot**, serta referensi kalibrasi alat. Liter larutan tidak boleh dianggap sama dengan liter herbisida. |
- | **SIV dan hubungan ke OPS/E-Plantation belum dipetakan secara konkret** | PDF Preskom halaman 228–229 menyebut lampiran Store Issue Voucher dan pencatatan oleh Krani PHM2D, termasuk keseimbangan stok. | Tentukan hubungan SPMB–BPB–SIV dan sistem pencatatan resminya. Jangan menganggap ketiga dokumen identik atau mengklaim saldo real-time sebelum integrasinya tersedia. |
- Ada juga detail laporan yang perlu ditulis lebih tegas: **status TM, luas dan populasi blok saat perencanaan, varian rencana–realisasi, rasio material L/ha atau g/ha, serta nomor kontak Operator sesuai kewenangan akses**. Sebagian bisa ditampung oleh struktur saat ini, tetapi belum menjadi daftar field kontrak yang pasti.
- Daftar endpoint yang sudah ada masih berguna. Yang perlu dilengkapi terutama **isi respons, tindakan yang diizinkan, dan perilaku saat offline**, bukan langsung menambah banyak endpoint.
- | Kebutuhan Android | API yang dapat digunakan | Kontrak yang perlu dipastikan |
- Contoh yang harus ditangani dengan jelas: **Operator menyimpan rit saat offline, lalu RKH direvisi oleh CMS sebelum HP kembali online.** Server harus menjelaskan apakah rit masih terkait penugasan yang sah, perlu pemeriksaan, atau berkonflik. Android harus mempertahankan draft dan menampilkan tindakan perbaikannya, bukan menghapus hasil atau mengirim ulang sebagai transaksi baru.
- Demikian pula, jika **Aplikator mencatat hasilnya sendiri dan Operator juga mencatat total rit unit**, keduanya perlu dihubungkan ke pekerjaan yang sama. Menjumlahkan kedua input sebagai hasil fisik baru akan menggandakan volume.
- Rujukan tambahan tersedia di transkrip kronologis.

Untuk melanjutkan, verifikasi kembali Git/source/runtime aktif, gunakan Graphify sebagai peta teknis, dan backup sebelum perubahan. Jangan mengulangi mutasi database/deploy hanya berdasarkan arsip.

## Laporan terbaru lain yang relevan

### 2026-10-09 14:32:45 WIB

**Ya, tahap perencanaan dan API List sudah selesai.** Hasilnya tersimpan dalam dua dokumen:

- [**Rencana lengkap API Go MKTI dan Go Blok**](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/PLAN_API_GO_MKTI_GO_BLOK_20261009.md) — arsitektur, model data, alur persetujuan, integrasi, validasi, tahapan implementasi, dan skenario pengujian.
- [**API List Go MKTI dan Go Blok**](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/API_LIST_GO_MKTI_GO_BLOK_20261009.md) — **65 baris route yang mencakup 84 operasi HTTP**, lengkap dengan fungsi dan tahap pembuatannya.

Konsistensi daftar endpoint, hitungan contoh, dan ketersediaan prefix route sudah diperiksa. Source aplikasi serta perubahan awal repository tetap dipertahankan.

**Implementasi API belum dimulai.** Dokumen sudah siap untuk review, sementara keputusan bisnis seperti approver RKB/RKH, sumber stok resmi, dan rumus upah tetap ditandai sebagai syarat sebelum fitur terkait diaktifkan. Scope mengikuti pilihan Anda: **Go MKTI + Go Blok bersama, payroll bertahap setelah rumus disepakati.**

### 2026-10-09 16:03:28 WIB

Berikut **API List yang direncanakan untuk Go MKTI dan Go Blok**. Satu kelompok API melayani **Emdek Fertilizer, Mounted Sprayer, dan pekerjaan manual Go Blok**.

**Base path usulan:**

```text
/api/v1/agronomy
```

Semua path pada tabel di bawah mengikuti base path tersebut. Contohnya, `/rkh` berarti `/api/v1/agronomy/rkh`. Jika satu baris mencantumkan `GET, POST`, berarti tersedia dua operasi pada path yang sama.

**Status: seluruh endpoint berikut masih dalam rancangan, belum diimplementasikan.**

**1. Akses pengguna dan master data**

| Metode | Endpoint | Fungsi |
|---|---|---|
| GET | `/me` | Mendapatkan identitas pengguna, peran, hak akses, dan scope estate/afdeling |
| GET | `/reference-data` | Mendapatkan referensi lokasi, blok, warung, material, dan satuan berdasarkan jenis referensi |
| GET | `/employees` | Mendapatkan pegawai, kontrak, golongan, serta kandidat Operator, Aplikator, Pemuat, Mandor, dan cover |
| GET | `/units` | Mendapatkan unit dan implement yang sesuai untuk Emdek atau Sprayer |
| GET, POST | `/activity-types` | Melihat daftar dan membuat kode kegiatan agronomi beserta satuan serta jenis basis/borong |
| PATCH | `/activity-types/{id}` | Mengubah metadata atau menonaktifkan kegiatan |

Login menggunakan autentikasi akun BUM yang sudah tersedia. Pemilihan pegawai dan unit mengikuti tanggal penugasan serta scope pengguna.

**2. RKB, RKH, dan penugasan pekerja**

| Metode | Endpoint | Fungsi |
|---|---|---|
| GET, POST | `/rkb` | Melihat daftar dan membuat RKB agronomi |
| GET, PATCH | `/rkb/{id}` | Melihat detail dan mengubah draft RKB |
| POST | `/rkb/{id}/submit` | Mengajukan RKB untuk diperiksa |
| POST | `/rkb/{id}/decisions` | Menyetujui atau mengembalikan RKB untuk perbaikan |
| GET, POST | `/rkh` | Melihat daftar dan membuat RKH berdasar

[Cuplikan; lihat riwayat sumber untuk laporan lengkap.]

### 2026-10-12 16:30:45 WIB

**Sudah ada fase A–F pada bagian 11 rencana sebelumnya. Sekarang saya sudah merincinya menjadi rencana pengerjaan yang lebih konkret**, lengkap dengan urutan, hasil tiap fase, dependensi, dan syarat selesai.

[**Buka rencana fase pembuatan API Go MKTI dan Go Blok**](D:/Laragon/www/laravel-13-cms-bum/project-artifacts/mkti-goblok-api-plan-20261008/FASE_PEMBUATAN_API_GO_MKTI_GO_BLOK_20261012.md)

Cakupannya tetap **Emdek Fertilizer, Mounted Sprayer, dan Go Blok manual bersama**. Fase A dibagi menjadi **A1–A2**, fase C menjadi **C1–C2**, dan ditambahkan **fase 0** untuk memastikan integrasi sebelum coding.

| Fase | Yang dikerjakan | Hasil yang harus tersedia sebelum lanjut |
|---|---|---|
| **0 — Pemeriksaan fondasi dan integrasi** | Periksa source/schema database, mapping master pegawai/lokasi/unit/material, sumber stok dan absensi, formulir P2H/PM10, serta kewenangan approval. Siapkan kontrak dasar API dan lingkungan uji. | Sumber data dan batas integrasi jelas. Keputusan yang belum tersedia mempunyai pemilik dan tidak diisi berdasarkan asumsi. |
| **A1 — Master data dan keamanan API** | `/me`, `/reference-data`, `/employees`, `/units`, `/activity-types`. Terapkan autentikasi BUM, permission, scope estate/afdeling, validasi, pencegahan submission ganda, dan kontrol versi. | Client dapat memilih kegiatan, lokasi, pegawai, dan unit yang sah. Pengguna tidak dapat mengakses data di luar scope-nya. |
| **A2 — RKB, PBLK, RKH, dan penugasan** | RKB bulanan → PBLK → RKH harian. Masukkan target, bahan, unit, Operator, Aplikator, Pemuat, pekerja manual, serta cover. Implementasikan submit, keputusan, returned, dan revisi. | Rencana ketiga mode kerja dapat disusun dan ditelusuri. Approval mengikat revisi tertentu dan mengikuti pejabat yang ditetapkan. |
| **B — Bahan, absensi, dan

[Cuplikan; lihat riwayat sumber untuk laporan lengkap.]

## Hubungan dengan memori sebelumnya

- E:\AI\Memories\20261012-all-projects-rancang-plan-sistem-api-mkti-01a11a40-9fe6-74d2-90b0-7e86b417c3d6.md

## Sumber dan keterbatasan

- Riwayat: \\?\C:\Users\aruna\.codex\sessions\2026\10\08\rollout-2026-10-08T13-44-57-01a11a40-9fe6-74d2-90b0-7e86b417c3d6_01a11a42-09ac-7652-9d4c-5fa05a1619e9.jsonl
- Metode: C:\Users\aruna\.codex\thread_history_1.sqlite / thread_items + thread_turns + rollout comparison (18 missing text messages recovered); 0 pesan dipertahankan dari arsip terverifikasi 12 Oktober
- Pesan pengguna: 13; laporan akhir: 6
- Kesalahan sumber: Tidak terdeteksi.
[Kredensial disamarkan]
- Memori lama dipertahankan; catatan ini menambahkan pembaruan per chat.
- Graphify diproses terpisah berdasarkan source aktif setelah arsip memori tersimpan.
- Verifikasi terbaru hanya mencakup integritas penyimpanan E/C; hasil uji aplikasi di atas bersifat historis.

## Memori pendukung dari sesi terdahulu

Bagian ini adalah handoff historis tambahan, berguna ketika proyeksi pesan aplikasi terpotong. Hasil uji tetap mengikuti tanggal sesi sumber; bukan verifikasi runtime hari ini.

Sumber: C:\Users\aruna\.codex\memories\rollout_summaries\2026-10-08T06-43-25-tIOO-mkti_goblok_api_plan_android_cross_check.md

thread_id: 01a11a40-9fe6-74d2-90b0-7e86b417c3d6
updated_at: 2026-10-14T02:33:01+00:00
rollout_path: \\?\C:\Users\aruna\.codex\sessions\2026\10\08\rollout-2026-10-08T13-44-57-01a11a40-9fe6-74d2-90b0-7e86b417c3d6_01a11a42-09ac-7652-9d4c-5fa05a1619e9.jsonl
cwd: \\?\D:\Laragon\www\laravel-13-cms-bum
git_branch: main

# API Go MKTI dan Go Blok direncanakan dengan payroll ditunda, lalu dihentikan aman dan kemudian di-cross-check untuk kesiapan integrasi Android

Rollout context: Project utama `D:\Laragon\www\laravel-13-cms-bum`. Pengguna meminta rencana API yang matang berdasarkan thread rujukan `01a0ad4c-441c-78c1-a0be-e8964110b92f` (“Jelaskan MKTI untuk pemupukan sawit”), PDF terbaru di Downloads, dan workbook `FORM GO MKTI DAN GO BLOK (1).xlsx`. Pengguna memilih scope **Go MKTI + Go Blok bersama; payroll bertahap setelah rumus disepakati**, lalu meminta checkpoint aman sebelum rencana lengkap selesai. Pada kelanjutan berikutnya, source/hash diverifikasi dan rencana API dibuat di artifact lokal. Cross-check lanjutan setelah pengguna menjelaskan integrasi Android menemukan beberapa revisi penting pada kontrak API; belum ada implementasi source aplikasi.

## Task 1: Pemeriksaan sumber dan penetapan scope API

Outcome: success

Preference signals:

- Saat diminta memilih fokus tahap pertama, pengguna memilih: “Go MKTI + Go Blok bersama; payroll bertahap setelah rumus disepakati” -> pada pekerjaan serupa, perlakukan ini sebagai keputusan scope yang mengutamakan alur operasional dan pencatatan hasil terlebih dahulu, bukan izin untuk langsung mengaktifkan payroll final.
- Pengguna meminta plan yang “matang dan proper” berdasarkan PDF dan Excel, dan sebelumnya meminta agar materi dibandingkan agar “nggak asal masukin aja” -> setiap aturan bisnis dari dokumen harus diberi status sumber, konflik, atau usulan; jangan mengubah contoh historis menjadi SOP final tanpa konfirmasi.

Key steps:

- Thread rujukan dibaca melalui `read_thread`; PDF draft 23 halaman dan workbook Excel 17 sheet, termasuk dua sheet tersembunyi, dianalisis.
- Source aktif ditelusuri menggunakan Graphify sebagai petunjuk, tetapi graph diketahui stale sebagian karena beberapa source/routes lebih baru; source aktual kemudian dibaca langsung.
- Ditemukan bahwa CMS sudah memiliki RKB/RKH Upkeep, P2H/PM10, aktivitas harian dan laporan rate, tetapi belum ditemukan route API khusus MKTI/agronomy pada pemeriksaan tersebut.
- Arah sementara yang dipilih adalah modul Agronomi khusus untuk Emdek, Mounted Sprayer, dan Go Blok, dengan master data BUM tetap dipakai bersama serta integrasi eksplisit ke modul lama.

Reusable knowledge:

- PDF menggambarkan alur `RKB -> PBLK -> RKH -> SPMB -> persetujuan Estate Manager -> pemeriksaan KTU -> Gudang/BPB -> Mandor -> Operator -> P2H/PM10/solar -> LHO -> evaluasi -> PBLH/verifikasi -> rekap`.
- PBLK harus mendahului RKH; PBLH dibuat GIS bersama Ka. Ur. Afdeling dan diverifikasi Audit/PHM2D. Materi belum menetapkan secara tegas pejabat approval formal RKB/RKH, jadi jangan menganggap Admin CMS atau Estate Manager otomatis sebagai approver RKB/RKH.
- Excel memisahkan peran Operator, Aplikator, tenaga muat, dan Mandor. Realisasi lapangan mencakup rit, volume kg/ha, pokok, meter lari, waktu, HM, foto/dokumentasi, material, dan hasil per pekerja.
- Absensi upkeep diarahkan ke akun Mandor Rawat; pengisian solar diarahkan diisi Kepala Tangki. Pelaksana pekerjaan, penerima bahan, dan pencatat pengisian harus dibedakan.
- Model Upkeep lama memiliki observer yang dapat memanggil `GoHarvest_ActivitySyncService` dan mengantrekan sinkronisasi aktivitas harvester ketika RKH approved. Memaksa transaksi MKTI langsung ke jalur lama berisiko salah alur atau duplikasi.

Failures and how to do differently:

- Graphify tidak boleh dianggap sebagai snapshot source terkini: `graphify-out/graph.json` berasal dari 4 Oktober 2026 dan source/routes lebih baru. Gunakan graph untuk orientasi, lalu verifikasi file aktif langsung.
- Query Graphify mengubah cache/tracked metadata. Pada checkpoint, perubahan `graphify-out/cache/last_query_stamp` dipulihkan dari HEAD dan status Git kembali bersih; pada sesi berikutnya jangan memperbarui Graphify tanpa instruksi pengguna.

References:

- Thread rujukan: `01a0ad4c-441c-78c1-a0be-e8964110b92f`
- PDF: `C:\Users\aruna\Downloads\MKTI_Flow_Aktivitas_Terhubung.pdf`
- Excel: `C:\Users\aruna\Downloads\FORM GO MKTI DAN GO BLOK (1).xlsx`
- Existing models/services: `app/Models/Transaction/MaintenanceRKB.php`, `app/Models/Transaction/MaintenanceRKH.php`, `app/Models/Transaction/MaintenanceRKHDetail.php`, `app/Observers/MaintenanceRKHObserver.php`, `app/Observers/MaintenanceRKHDetailObserver.php`, `app/Services/Transaction/GoHarvest_ActivitySyncService.php`.

## Task 2: Audit formula dan batas payroll

Outcome: partial

Preference signals:

- Pengguna ingin materi PDF, foto, dan Excel diselaraskan tanpa memasukkan angka secara asal -> pertahankan angka historis, contoh, dan aturan perusahaan sebagai lapisan berbeda; konflik harus diekspos dan dikonfirmasi.
- Pengguna memilih payroll bertahap setelah rumus disepakati -> API boleh merekam hasil dan menampilkan preview/estimasi berstatus, tetapi tidak boleh menerbitkan upah final dari rumus yang belum authoritative.

Key steps:

- Sheet `14` dan `15` dibandingkan; rumus dan contoh dihitung ulang secara mandiri.
- Formula laporan dan referensi eksternal diperiksa dengan `openpyxl` secara read-only.
- Checkpoint menyimpan hash sumber, inventory sheet, ekstraksi PDF, preview halaman, dan daftar formula bermasalah.

Reusable knowledge:

- Sheet `14` menyatakan PHT Rp125.221/hari dan PHL Rp150.266/hari; Operator Emdek memakai premi `(hasil kerja - basis 4.000 kg/HK) x Rp45/kg`; Operator MKTI berbasis hektare memakai `(hasil kerja - basis 5 ha/HK) x Rp37.566/ha`.
- Sheet `15` bertentangan dengan sheet `14`: beberapa pekerja berlabel PHT memakai tarif PHL, dan contoh premi memakai dasar/tarif berbeda. Ini bukan sekadar pembulatan.
- Aplikator berbasis mengikuti `hasil / basis x tarif HK`; pekerjaan borongan mengikuti `hasil x tarif borong`. Perlakuan hasil di bawah basis, multi-blok/multi-shift, cover worker, pembulatan, perubahan golongan, dan alokasi biaya masih memerlukan keputusan bisnis.
- Ditemukan 12 formula `#REF!` pada sheet `19`, disamarkan dengan `IFERROR(...,0)`; nol hasilnya tidak membuktikan perhitungan benar.
- Contoh formula `20!AA13` dan `21!Y13` menghitung arah `HM/liter` meskipun labelnya `Ltr/HM`; dosis gabungan di sheet `20` dijumlahkan per baris, padahal kandidat perhitungan yang benar adalah total kg dibagi total pokok.
- `9, 12!AC54` dan `AC56` merujuk workbook eksternal `[1]5,6,7`, yang tidak tersedia saat diperiksa.

Failures and how to do differently:

- Jangan memilih sheet `14` atau `15` sebagai payroll final hanya karena salah satunya tampak lebih jelas. Simpan `rule_version`, tanggal berlaku, sumber keputusan, dan status `draft/approved` sebelum payroll final.
- Jangan menyembunyikan formula error menjadi nol dalam API/report. Server harus mengembalikan status missing/invalid dan sumber masalah.

References:

- Workbook sheet/range: `14!B2:K24`, `15!D25:O35`, `19!J15:J26`, `20!AA13`, `21!Y13`, `9, 12!AC54:AC56`, `List!C6:G7`, `PENGISIAN SOLAR!B2:I9`.
- Checkpoint: `D:\Laragon\www\laravel-13-cms-bum\project-artifacts\mkti-goblok-api-plan-20261008\CHECKPOINT_20261008.md`
- Source verification: `D:\Laragon\www\laravel-13-cms-bum\project-artifacts\mkti-goblok-api-plan-20261008\source-verification.json`

## Task 3: Safe checkpoint and pause

Outcome: success

Preference signals:

- Pengguna menghentikan proses dengan: “Coba hentikan dulu dan buat check point yang aman” -> ketika pengguna meminta berhenti, simpan status, bukti, pending items, dan batas verifikasi sebelum melakukan pekerjaan lanjutan; jangan melanjutkan analisis atau implementasi dalam turn yang sama.
- Pengguna menginginkan perubahan aman dan tidak merusak source -> backup hanya perubahan milik sesi, jangan menggunakan reset/clean/checkout yang dapat menghapus perubahan pengguna.

Key steps:

- Created `CHECKPOINT_20261008.md` with scope decision, findings, artifacts, uncompleted work, safe continuation sequence, and verification boundaries.
- Saved `source-verification.json`, `pdf-source-extract.txt`, and PDF page previews under `project-artifacts/mkti-goblok-api-plan-20261008`.
- Restored the Graphify cache stamp changed by the session, backed up the pre-restore value, and verified `git status --short` was clean.
- Explicitly recorded that no API implementation, database migration, PDF/Excel modification, deployment, commit, or Graphify rebuild occurred.

References:

- Checkpoint path: `D:\Laragon\www\laravel-13-cms-bum\project-artifacts\mkti-goblok-api-plan-20261008\CHECKPOINT_20261008.md`
- Git status after restoration: clean.
- Source hashes recorded in checkpoint and later rechecked unchanged.

## Task 4: Continue plan and cross-check for Android integration

Outcome: partial

Preference signals:

- User clarified: “Soalnya API ini akan diintegrasikan dengan aplikasi mobile untuk android” -> future API planning must be mobile-first at the contract level: offline drafts, idempotent sync, conflict handling, upload retry, assignment revisions, role-aware responses, and explicit distinction between reported, accepted, estimated, and final values.
- User’s prior emphasis on not inserting material “asal” implies Android contracts should expose source/status/validation metadata rather than only final-looking values.

Key steps:

- Checkpoint and source hashes were revalidated; PDF and Excel hashes remained unchanged.
- API planning artifacts were expanded, including `API_LIST_GO_MKTI_GO_BLOK_20261009.md` and `FASE_PEMBUATAN_API_GO_MKTI_GO_BLOK_20261012.md`.
- Cross-check found that the initial plan was directionally sound but not ready to freeze as an Android implementation contract.

Reusable knowledge:

- Proposed base path in the artifact: `/api/v1/agronomy`, serving Emdek, Mounted Sprayer, and Go Blok with `execution_mode: emdek | sprayer | manual`; this remains a proposal, not an implemented route.
- Android needs API contracts organized around screens/actions: assignments, RKH detail, work sessions, trips/rit, results, attachments, sync changes/submissions, unit readiness, fuel fillings, reports, and payroll previews.
- Offline flow must preserve a local UUID/client submission ID, retryable attachments, dependency ordering, server idempotency, `expected_version` conflict checks, and a recoverable local draft if the app closes or the assignment changes while offline.
- Distinguish `reported_quantity`, `accepted_quantity`, and `rejected_quantity`/reason. A reported result is not automatically accepted, and an accepted result is not automatically payroll-final.
- Go Blok cannot be restricted to “manual” only. Excel `sheet 18!C13:F15` includes Mounted Sprayer alongside other worker activities, and sheet `14` includes Aplikator, Pemuat Pupuk, and manual maintenance. `execution_mode` should describe execution mechanics, not hide a worker’s contribution from Go Blok views.
- Android responses must include planned income/estimated income separately from realized estimates and final payroll. Excel `sheet 8, 11!C14:H17` and MKTI forms display planned volume/HK/income before work starts.
- The relation to harvest RKH must be explicit or marked unresolved: Excel `sheet 5,6,7!I29` and `I44` say some operator/volume data is “otomatis dari RKH Panen dibagi jumlah WT di afdeling tersebut”. Do not silently implement this algorithm without confirming its exact source fields and allocation rule.
- PM10 readiness must distinguish `not_ready`, `incomplete_record`, and `follow_up_required`; incomplete documentation is not automatically a work prohibition unless an authoritative workshop/SOP rule says so.
- PBLK should retain references to the verified base map version and prior PBLH progress, not only the RKB relation.
- Track separate timestamps for device-created, uploaded/received by server, reviewed/verified, and operationally delivered/submitted. Do not infer incentive punctuality solely from upload time.
- Sprayer material fields must distinguish concentrate/herbicide, water, and final spray solution; liters of solution are not automatically liters of herbicide. Calibration and per-rit dosage need explicit fields.
- SPMB, BPB, SIV, warehouse stock, and OPS/E-Plantation references need explicit integration mapping; do not assume these documents are interchangeable or claim real-time stock without a verified ledger source.
- Server-side rules must calculate authoritative payroll and cost values. Android may display offline estimates only with a visible provisional status; it must not determine final payroll locally.
- Prevent double counting when Operator records a unit-level rit total and Aplikator records individual contribution: both must reference the same work unit/rit and allocation grain, rather than being summed as two physical outputs.

Failures and how to do differently:

- The initial API plan was too optimistic about Android readiness: it needed more explicit response fields, action permissions, offline state transitions, conflict semantics, attachment lifecycle, planned-income previews, and worker/contribution grain.
- Do not claim the plan is complete merely because files exist. The later cross-check explicitly found “rencana layak sebagai dasar arsitektur dan urutan fase, tetapi perlu revisi substansial pada kontrak bisnis dan kontrak Android sebelum implementasi.”
- Do not make PM10 a blanket blocker, do not collapse created/received/verified/delivered timestamps, and do not treat Go Blok as manual-only.

References:

- API artifact: `D:\Laragon\www\laravel-13-cms-bum\project-artifacts\mkti-goblok-api-plan-20261008\API_LIST_GO_MKTI_GO_BLOK_20261009.md`
- Phase artifact: `D:\Laragon\www\laravel-13-cms-bum\project-artifacts\mkti-goblok-api-plan-20261008\FASE_PEMBUATAN_API_GO_MKTI_GO_BLOK_20261012.md`
- Android
