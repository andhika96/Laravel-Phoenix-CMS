# Buat preview halaman Pre-book

Tanggal penyimpanan: 20 Oktober 2026 (WIB)
Project: New MG [Laptop]
Project saat chat: D:\Laragon\www\newmg
Chat ID: 01a13562-b6a4-7332-b010-91bfda6c01f8
Jenis sumber: codex-local
Rentang chat: 2026-10-13 20:10:24 WIB sampai 2026-10-15 13:12:33 WIB
Tindakan: update
Evidence SHA-256: ca3ba9a9413074428ca34f7920f8890b2e58881c3af91089303f8b6c6d7c244e

## Tujuan dan konteks awal

- ChatGPT saya ingin membuat halaman baru diwebsite MG Motor kita ini, halaman baru ini namanya pre-book dan isi halamannya ini form submit gitu, nah saya ingin form submitnya itu sama seperti form submit test drive, formnya aja tapi nanti kan dihalaman baru ini isinya hanya form submit aja nah saya ingin UI UXnya itu bagus dengan formnya persis berada ditengah.

Dan ketika user submit datanya, datanya ke record ke database nanti datanya bisa ditampilkan di CMS persis seperti dihalaman manage test drive, tapi data pre-book ini punya halaman tersendiri, jadi nanti kamu ambil aja copy paste halaman manage test drive lalu kamu sesuaikan untuk data pre-book ini, bisa di filter by date range dan bisa diexport menggunakan excel yang dimana sudah dibuat format table dan auto width.

Nanti setelah user selesai submit ada notice message data berhasil dikirim dan ada teks sedang mendownload flyer atau brosur otomatis, tapi jika tidak terdownload otomatis klik tombol dibawah untuk mendownloadnya.

Sebelum kamu eksekusi, saya ingin kamu buat previewnya disini halaman pre-booknya dulu, entah langsung halaman prototype juga boleh.
- Fon size dan familynya juga disama dong dengan website MG Motornya jadi preview prototypenya benar-benar mirip lah

## Keputusan dan implementasi historis

Laporan di bawah merupakan riwayat pekerjaan, bukan pengujian runtime terbaru pada tanggal penyimpanan. Checkpoint terbaru mengungguli pending dari laporan lebih lama.

- | Bagian | Implementasi aktif |
- Nanti implementasi pre-book akan mengikuti Vue 2 dan pola JavaScript yang sudah ada tersebut**, termasuk Axios, `FormData`, komponen dropdown, serta state loading dan respons submit. Perilaku khusus pre-book—pesan berhasil dan unduhan brosur—akan disesuaikan agar tidak mengubah perilaku form test drive maupun form lain yang memakai script bersama.
- Pemeriksaan kali ini hanya membaca source dan memverifikasi script yang dimuat browser; belum ada perubahan source atau pengujian kirim data/OTP. Graphify hanya dipakai untuk penelusuran, tanpa pembaruan graph.
- Tidak ada source yang diubah pada pemeriksaan tadi.
- Untuk pre-book nanti saya akan mengikuti pola ini:** Vue 2 melalui script lokal, struktur `data` dan `methods`, binding Vue pada form, serta Axios dan `FormData` yang sudah digunakan proyek. JavaScript simulasi pada prototype akan disesuaikan saat implementasi.
- Tidak ada source yang diubah. Pemeriksaan ini memastikan konfigurasi dan hubungan kode secara statis; submit data serta pengiriman OTP tidak dijalankan.
- Saat implementasi nanti, saya akan **menyalin struktur tabelnya saja**. Data pelanggan test drive di dalam dump tetap berada pada test drive; tabel pre-book mulai kosong dengan ID baru. Halaman CMS pre-book akan mengikuti Manage Test Drive, dengan ekspor Excel berbentuk tabel dan lebar kolom otomatis seperti permintaan Anda.
- Belum ada import SQL, pembuatan tabel, atau perubahan source.** Hasil ini sudah diverifikasi dari dump dan source aktif; struktur database yang sedang berjalan belum diperiksa langsung.
- ![Halaman pre-book dengan field dan tombol rounded pill](D:/Laragon/www/newmg/project-artifacts/prebook-implementation-20261014/qc/prebook-rounded-desktop.png)
- Revisi ini ada di [view pre-book](D:/Laragon/www/newmg/modules/prebook/views/index.php), dengan backup sebelum perubahan di [folder backups](D:/Laragon/www/newmg/project-artifacts/prebook-implementation-20261014/backups). Pemeriksaan PHP lulus, dan tampilan desktop serta mobile sudah diperiksa—tidak ada overflow horizontal pada lebar 390px. Graphify tidak diperbarui.
- Implementasi form, penyimpanan database, **[Manage Pre-book](https://newmg.aruna/manage_prebook)**, filter tanggal, ekspor Excel, dan modal Manage Files juga sudah tersedia secara lokal. Rincian perubahan dan pengujiannya ada pada [laporan implementasi](D:/Laragon/www/newmg/project-artifacts/prebook-implementation-20261014/implementation-report.json).
- Sudah saya perbaiki ketiganya. Silakan **reload halaman pre-book dan Manage Pre-book**; versi script juga sudah diperbarui agar browser mengambil perubahan terbaru.
- ![Dropdown Dealer setelah diperbaiki](D:/Laragon/www/newmg/project-artifacts/prebook-fix-20261014/qc/pb-dealer-final.png)
- Penyebab Manage Pre-book blank juga sudah diperbaiki.** Sebelumnya saya mendaftarkan JavaScript setelah footer selesai dirender. Akibatnya, `manage-prebook.js` tidak dimuat dan atribut `v-cloak` terus menyembunyikan konten. Sekarang registrasi script dilakukan sebelum pemuatan layout, mengikuti pola Manage Test Drive.
- Rujukan tambahan tersedia di transkrip kronologis.

## Checkpoint substantif terakhir

Tanggal checkpoint: 2026-10-15 10:44:53 WIB

Sudah saya ubah: **Model MG pada form pre-book sekarang hanya mengambil status Only Pre-book (`7`), terpilih otomatis, dan dropdown-nya disabled** sehingga pengguna tidak bisa menggantinya.

- Hanya **satu mobil** yang ditampilkan. Jika ada beberapa model berstatus Only Pre-book, sistem mengambil yang pertama sesuai urutan model di CMS.
- Nilai model tetap ikut terkirim meskipun dropdown disabled.
- Server juga mengunci model yang sama, sehingga request yang dimanipulasi untuk memilih mobil lain ditolak.
- Pengaturan OTP tetap seperti sebelumnya: **nonaktif secara default**.

**Catatan penting:** saat diperiksa, database lokal belum memiliki model berstatus Only Pre-book. Karena itu, halaman lokal akan menampilkan **“Belum ada model pre-book tersedia”** dan tombol submit dinonaktifkan. Silakan ubah status **MGS6 EV → Only Pre-book** di CMS agar otomatis muncul.

Pengujian lulus untuk model yang benar, penolakan model Publish/model lain, kondisi tanpa model, serta reCAPTCHA. HTML hasil render juga sudah diperiksa: satu opsi terpilih, dropdown disabled, dan nilai model tetap tersedia untuk submit. Data uji di-rollback.

Perubahan hanya pada [controller pre-book](D:/Laragon/www/newmg/modules/prebook/prebook.php) dan [tampilan form](D:/Laragon/www/newmg/modules/prebook/views/index.php). Backup dan hasil pengujian ada di [folder pekerjaan](D:/Laragon/www/newmg/project-artifacts/prebook-locked-model-20261015).

Belum dilakukan push/pull atau pengujian di AWS; status model pada database server perlu dipastikan sudah `Only Pre-book`.

## File dibuat, dimodifikasi, atau dirujuk

- add (status completed): D:\Laragon\www\newmg\project-artifacts\pre-book-preview-20261013\index.html
- Add: D:/Laragon/www/newmg/project-artifacts/pre-book-preview-20261013/index.html

- Sudah saya cek source dan halaman test drive yang aktif. **Website MG Motor ini memakai Vue.js 2, tepatnya versi 2.7.14.
- Vue dimuat langsung melalui tag `<script>` dari **aset lokal website**, yaitu `assets/js/vuejs-2.7.14.min.js`. Jadi cara pemakaiannya seperti versi CDN tanpa proses build, tetapi file Vue-nya disimpan di proyek sendiri.
- | JavaScript utama | `assets/js/aruna-vue2022.js` |
- [page.tpl.php](D:/Laragon/www/newmg/themes/default/page.tpl.php:618): pemuatan Vue dan dependensinya.
- [aruna-vue2022.js](D:/Laragon/www/newmg/assets/js/aruna-vue2022.js:31): instance Vue form, state, dan methods.
- [index.php form test drive](D:/Laragon/www/newmg/modules/testdrive/views/index.php:141): binding form ke JavaScript.
- Betul, **untuk memastikan versi Vue dan pola JavaScript, pemeriksaan langsung di folder proyek sudah cukup**. Dari `themes/default/page.tpl.php` sudah jelas bahwa website memuat `vuejs-2.7.14.min.js`, dan `assets/js/aruna-vue2022.js` menunjukkan penggunaan `new Vue(...)`.
- Sudah saya cek ulang **langsung dari file aktif di `D:\Laragon\www\newmg`, tanpa membuka browser**. Hasilnya tetap: **Vue.js 2.7.14**, dengan bukti berikut.
- Di [themes/default/page.tpl.php:622](D:/Laragon/www/newmg/themes/default/page.tpl.php:622):
- <script src="'.base_url('assets/js/vuejs-2.7.14.min.js').'"></script>
- Header [assets/js/vuejs-2.7.14.min.js](D:/Laragon/www/newmg/assets/js/vuejs-2.7.14.min.js:1):
- Vue.js v2.7.14
- Di [assets/js/aruna-vue2022.js:31](D:/Laragon/www/newmg/assets/js/aruna-vue2022.js:31):
- Sedangkan [modules/testdrive/views/index.php:141](D:/Laragon/www/newmg/modules/testdrive/views/index.php:141) menghubungkan submit dengan:
- Rujukan tambahan tersedia di transkrip kronologis.

## Backup historis

- File yang diperbarui: [index.html](D:/Laragon/www/newmg/project-artifacts/pre-book-preview-20261013/index.html). Backup sebelum revisi: [index.html.bak_20261013_201946_font_notice](D:/Laragon/www/newmg/project-artifacts/pre-book-preview-20261013/index.html.bak_20261013_201946_font_notice).
- Revisi ini ada di [view pre-book](D:/Laragon/www/newmg/modules/prebook/views/index.php), dengan backup sebelum perubahan di [folder backups](D:/Laragon/www/newmg/project-artifacts/prebook-implementation-20261014/backups). Pemeriksaan PHP lulus, dan tampilan desktop serta mobile sudah diperiksa—tidak ada overflow horizontal pada lebar 390px. Graphify tidak diperbarui.
- Backup tersedia di [folder backups](D:/Laragon/www/newmg/project-artifacts/prebook-fix-20261014/backups), dan rincian pengujian ada di [laporan perbaikan](D:/Laragon/www/newmg/project-artifacts/prebook-fix-20261014/report.json). Pemeriksaan sintaks PHP/JS, pencarian Dealer, dropdown bertingkat, dan layout mobile lulus. Graphify tidak diperbarui.
- Penguncian nomor selama countdown, verifikasi kode salah/benar, dan tampilan mobile sudah diuji kembali. Backup dibuat sebelum perubahan.
- Perubahan utama berada pada [controller pre-book](D:/Laragon/www/newmg/modules/prebook/prebook.php), [view form](D:/Laragon/www/newmg/modules/prebook/views/index.php), [JavaScript pre-book](D:/Laragon/www/newmg/assets/js/prebook.js), dan service baru [Prebook_otp.php](D:/Laragon/www/newmg/application/libraries/Prebook_otp.php). Backup dan bukti pengujian tersimpan di [folder artefak OTP](D:/Laragon/www/newmg/project-artifacts/prebook-otp-live-20261014), dengan rincian pada [laporan verifikasi](D:/Laragon/www/newmg/project-artifacts/prebook-otp-live-20261014/report.json). Graphify tidak diperbarui.
- Perubahan hanya pada [Prebook_otp.php](D:/Laragon/www/newmg/application/libraries/Prebook_otp.php), dengan backup sebelumnya tersimpan di [folder backup](D:/Laragon/www/newmg/project-artifacts/prebook-otp-live-20261014/backups). **24 pemeriksaan logika lulus**, termasuk kondisi gagal, error koneksi, percobaan ulang, dan countdown saat berhasil. Pemeriksaan browser dengan simulasi respons gagal juga memastikan input dan tombol kembali aktif, tanpa countdown, sementara submit tetap terkunci. Graphify tidak diperbarui sesuai instruksi Anda.
- File yang diubah: [validasi server](D:/Laragon/www/newmg/application/libraries/Prebook_store.php), [JavaScript CMS](D:/Laragon/www/newmg/assets/js/manage-prebook.js), [tampilan modal](D:/Laragon/www/newmg/modules/manage_prebook/views/index.php), dan [versi aset CMS](D:/Laragon/www/newmg/modules/manage_prebook/manage_prebook.php). Backup serta pengujian tersimpan di [folder pekerjaan](D:/Laragon/www/newmg/project-artifacts/prebook-upload-30mb-20261014). Graphify tidak diperbarui sesuai instruksi Anda.
- Perubahan hanya pada [tampilan pre-book](D:/Laragon/www/newmg/modules/prebook/views/index.php). Backup dan screenshot pemeriksaan tersimpan di [folder pekerjaan](D:/Laragon/www/newmg/project-artifacts/prebook-select-arrow-20261014). Silakan refresh halaman pre-book untuk melihat hasilnya.
- Perubahan ada pada [JavaScript pre-book](D:/Laragon/www/newmg/assets/js/prebook.js), [tampilan form](D:/Laragon/www/newmg/modules/prebook/views/index.php), dan [versi aset halaman](D:/Laragon/www/newmg/modules/prebook/prebook.php). Backup serta hasil pengujian tersimpan di [folder pekerjaan](D:/Laragon/www/newmg/project-artifacts/prebook-nearby-20261014). Graphify tidak diperbarui.
- Perubahan ada pada [JavaScript pre-book](D:/Laragon/www/newmg/assets/js/prebook.js), [versi aset halaman](D:/Laragon/www/newmg/modules/prebook/prebook.php), dan [API lokasi dealer](D:/Laragon/www/newmg/modules/home/home.php). Backup, script pengujian, dan [laporan audit](D:/Laragon/www/newmg/project-artifacts/prebook-audit-20261014/report.md) tersimpan di folder pekerjaan. Graphify tidak diperbarui sesuai instruksi Anda.
- Jangan ikutkan backup dan script pengujian di `project-artifacts` ke deployment.** Aturan `.htaccess` yang saya tambahkan hanya berada di folder backup tersebut, bukan konfigurasi aplikasi yang perlu dibawa ke AWS.
- File yang diubah: [JavaScript pre-book](D:/Laragon/www/newmg/assets/js/prebook.js), [tampilan form](D:/Laragon/www/newmg/modules/prebook/views/index.php), dan [controller pre-book](D:/Laragon/www/newmg/modules/prebook/prebook.php). Backup serta pengujian ada di [folder pekerjaan](D:/Laragon/www/newmg/project-artifacts/prebook-optional-dealer-20261014).
- Backup dan rincian pemeriksaan tersedia di [folder pekerjaan](D:/Laragon/www/newmg/project-artifacts/prebook-only-model-20261014) serta [laporan verifikasi](D:/Laragon/www/newmg/project-artifacts/prebook-only-model-20261014/report.md). Graphify tidak diperbarui; perubahan dealer opsional sebelumnya tetap dipertahankan.
- Rangkuman mencakup implementasi pre-book, OTP/reCAPTCHA, dealer opsional dan lokasi terdekat, perbaikan upload S3, registrasi modul CMS, status **Only Pre-book**, laporan/Excel, file yang diubah, lokasi backup, hasil pengujian, serta langkah lanjutan.
- Rujukan tambahan tersedia di transkrip kronologis.

## Pengujian dan verifikasi historis

- Font prototype juga sudah disesuaikan berdasarkan **computed style halaman test drive aktif**: **Open Sans, ukuran 14px, line-height 21px** untuk field dan tombol. Sebelumnya prototype memakai Gill Sans; sekarang sudah dikoreksi.
- Penghapusan teks dan font sudah diverifikasi di browser. Graphify tidak diperbarui; query hanya mengubah timestamp cache-nya.
- Sudah saya cek source dan halaman test drive yang aktif. **Website MG Motor ini memakai Vue.js 2, tepatnya versi 2.7.14.
- Pola yang digunakan form test drive saat ini:
- | Submit dan OTP | Method `submitForEmail`, `submitForOTPCode`, serta alur verifikasi OTP |
- [index.php form test drive](D:/Laragon/www/newmg/modules/testdrive/views/index.php:141): binding form ke JavaScript.
- Nanti implementasi pre-book akan mengikuti Vue 2 dan pola JavaScript yang sudah ada tersebut**, termasuk Axios, `FormData`, komponen dropdown, serta state loading dan respons submit. Perilaku khusus pre-book—pesan berhasil dan unduhan brosur—akan disesuaikan agar tidak mengubah perilaku form test drive maupun form lain yang memakai script bersama.
- Pemeriksaan kali ini hanya membaca source dan memverifikasi script yang dimuat browser; belum ada perubahan source atau pengujian kirim data/OTP. Graphify hanya dipakai untuk penelusuran, tanpa pembaruan graph.
- Saya tadi membuka browser sebagai verifikasi tambahan bahwa halaman aktif benar-benar memuat file yang sama. Namun, untuk pertanyaan Anda ini langkah tersebut memang tidak diperlukan karena referensinya sudah eksplisit di source.
- 3. **Form test drive terhubung ke instance Vue 2.
- Sedangkan [modules/testdrive/views/index.php:141](D:/Laragon/www/newmg/modules/testdrive/views/index.php:141) menghubungkan submit dengan:
- Catatan pemeriksaan: memori E masih tidak tersedia; konteks NEWMG di C diperiksa. Graphify digunakan untuk menemukan modul test drive, lalu bukti di atas diambil dari source aktif. Timestamp template tidak cocok dengan manifest Graphify, sehingga graph tidak saya jadikan bukti versi; graph juga tidak diperbarui.
- Bisa. **Database pre-book akan mengikuti struktur tabel test drive, tetapi menggunakan tabel tersendiri, yaitu `ml_prebook`.** Jadi bentuk datanya sama, sementara data test drive dan pre-book tetap terpisah sesuai halaman CMS masing-masing.
- Saya sudah membaca struktur pada [mg_2026_oct.sql](C:/Users/aruna/Downloads/mg_2026_oct.sql:30) dan mencocokkannya dengan kode penyimpanan test drive aktif. File yang Anda kirim memuat tabel **`ml_testdrive`**, beserta data dan indeksnya.
- Rujukan tambahan tersedia di transkrip kronologis.

## Masalah belum selesai dan langkah lanjutan

- Pengaturan OTP tetap seperti sebelumnya: **nonaktif secara default**.
- Catatan penting:** saat diperiksa, database lokal belum memiliki model berstatus Only Pre-book. Karena itu, halaman lokal akan menampilkan **“Belum ada model pre-book tersedia”** dan tombol submit dinonaktifkan. Silakan ubah status **MGS6 EV → Only Pre-book** di CMS agar otomatis muncul.
- Belum dilakukan push/pull atau pengujian di AWS; status model pada database server perlu dipastikan sudah `Only Pre-book`.

Untuk melanjutkan, verifikasi kembali Git/source/runtime aktif, gunakan Graphify sebagai peta teknis, dan backup sebelum perubahan. Jangan mengulangi mutasi database/deploy hanya berdasarkan arsip.

## Laporan terbaru lain yang relevan

### 2026-10-14 16:58:34 WIB

Sudah saya simpan percakapan ini ke **dua lokasi**:

- **Drive E:** [E:\AI\Memories\20261014-165643-newmg-prebook-only-model-otp-s3-cms.md](E:/AI/Memories/20261014-165643-newmg-prebook-only-model-otp-s3-cms.md)
- **Drive C:** [C:\Users\aruna\.codex\memories\extensions\ad_hoc\notes\20261014-165643-newmg-prebook-only-model-otp-s3-cms.md](C:/Users/aruna/.codex/memories/extensions/ad_hoc/notes/20261014-165643-newmg-prebook-only-model-otp-s3-cms.md)

Rangkuman mencakup implementasi pre-book, OTP/reCAPTCHA, dealer opsional dan lokasi terdekat, perbaikan upload S3, registrasi modul CMS, status **Only Pre-book**, laporan/Excel, file yang diubah, lokasi backup, hasil pengujian, serta langkah lanjutan.

[Kredensial disamarkan]

Tidak ada perubahan source code atau deployment pada langkah penyimpanan ini.

### 2026-10-15 09:15:01 WIB

Sudah saya **nonaktifkan sementara OTP khusus pada form pre-book**, dengan pengaturan untuk mengaktifkannya kembali nanti.

Saat ini:

- Input nomor telepon tetap memakai **masking**.
- Tombol **Send OTP**, input kode OTP, dan countdown tidak ditampilkan.
- Pengguna dapat submit tanpa verifikasi OTP.
- **Validasi nomor, reCAPTCHA, CSRF, dan validasi data lainnya tetap berjalan.**
- Endpoint pengiriman/verifikasi OTP juga dinonaktifkan di server, sehingga tidak sekadar menyembunyikan tombol.
- Data yang masuk tanpa OTP diberi status internal **`unverified`**, bukan dianggap sudah terverifikasi.

Untuk mengaktifkan kembali, tambahkan atau ubah pengaturan ini di `.env`:

```env
PREBOOK_OTP_ENABLED=true
```

Untuk menonaktifkannya:

```env
PREBOOK_OTP_ENABLED=false
```

**Jika pengaturan tersebut belum ada, default-nya sekarang nonaktif.** Saya tidak mengubah isi `.env` Anda, jadi perubahan kode ini akan menonaktifkan OTP setelah dipull ke server selama server tidak memiliki pengaturan tersebut bernilai `true`.

Perubahan hanya pada tiga file:

- [Controller pre-book](D:/Laragon/www/newmg/modules/prebook/prebook.php)
- [Tampilan pre-book](D:/Laragon/www/newmg/modules/prebook/views/index.php)
- [JavaScript pre-book](D:/Laragon/www/newmg/assets/js/prebook.js)

**Pengujian lulus:** 10 skenario server untuk mode aktif/nonaktif, termasuk penolakan nomor tidak valid dan reCAPTCHA gagal. Pemeriksaan browser juga memastikan masking tetap bekerja, kontrol OTP tidak muncul, tidak ada request OTP otomatis, dan submit tidak terkunci oleh OTP. Backup serta hasil pengujian ada di [folder pekerjaan](D:/Laragon/www/newmg/project-artifacts/prebook-otp-toggle-20261015).

Belum dilakukan push/pull atau pengujian di AWS. Selama OTP nonaktif, format nomor tetap diperiksa, tetapi kepemilikan nom

[Cuplikan; lihat riwayat sumber untuk laporan lengkap.]

### 2026-10-15 13:12:29 WIB

Sudah saya simpan kelanjutan percakapan ini dan memverifikasi isi filenya di:

[**C:\Users\aruna\.codex\memories\extensions\ad_hoc\notes\20261015-131141-newmg-prebook-otp-toggle-locked-model.md**](C:/Users/aruna/.codex/memories/extensions/ad_hoc/notes/20261015-131141-newmg-prebook-otp-toggle-locked-model.md)

Rangkuman mencakup:

- OTP sementara **nonaktif**, cara mengaktifkannya kembali melalui `.env`, dan proteksi yang tetap berjalan.
- Model MG hanya memakai **Only Pre-book (`7`)**, satu mobil terpilih otomatis, dengan dropdown disabled.
- Penanganan saat belum ada model pre-book.
- File yang diubah, lokasi backup, hasil pengujian, serta langkah lanjutan untuk AWS.

**Salinan ke `E:\AI\Memories` belum tersimpan karena drive E sedang tidak tersedia.** Kondisi ini juga sudah dicatat dalam memori C agar tidak terlewat saat melanjutkan pekerjaan.

Tidak ada perubahan source code atau deployment pada langkah penyimpanan ini.

## Hubungan dengan memori sebelumnya

- E:\AI\Memories\20261014-165643-newmg-prebook-only-model-otp-s3-cms.md

## Sumber dan keterbatasan

- Riwayat: \\?\C:\Users\aruna\.codex\sessions\2026\10\13\rollout-2026-10-13T20-10-24-01a13562-b6a4-7332-b010-91bfda6c01f8.jsonl
- Metode: C:\Users\aruna\.codex\thread_history_1.sqlite / thread_items + thread_turns + rollout comparison (8 missing text messages recovered)
- Pesan pengguna: 50; laporan akhir: 34
- Kesalahan sumber: Tidak terdeteksi.
[Kredensial disamarkan]
- Memori lama dipertahankan; catatan ini menambahkan pembaruan per chat.
- Graphify diproses terpisah berdasarkan source aktif setelah arsip memori tersimpan.
- Verifikasi terbaru hanya mencakup integritas penyimpanan E/C; hasil uji aplikasi di atas bersifat historis.
