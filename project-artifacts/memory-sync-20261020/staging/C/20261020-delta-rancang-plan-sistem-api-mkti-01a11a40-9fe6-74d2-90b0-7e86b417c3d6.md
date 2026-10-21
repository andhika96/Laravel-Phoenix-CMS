# Rancang plan sistem API MKTI

Tanggal penyimpanan: 20 Oktober 2026 (WIB)
Project: Laravel 11 CMS BUM
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
