# PRD — Meeting Room Booking dengan Internal Chargeback

## 1. Ringkasan Produk

Meeting Room Booking adalah modul PhoenixCMS untuk menemukan ruang rapat yang
tersedia, melihat fasilitas dan tarif, melakukan booking, memperpanjang durasi,
serta membebankan biaya pemakaian ke divisi atau cost center pengguna.

Produk dirancang memiliki booking core yang kelak dapat mendukung resource lain,
tetapi scope rilis pertama hanya meeting room. UI, istilah, validasi, fasilitas,
dan laporan pada MVP tetap spesifik untuk ruang rapat agar implementasi tidak
terlalu generik sebelum dibutuhkan.

## 2. Latar Belakang

Pemesanan ruang rapat sering bergantung pada komunikasi manual sehingga pengguna
sulit mengetahui jadwal kosong, fasilitas yang tersedia, dan biaya yang akan
dibebankan. Perpanjangan rapat juga dapat berbenturan dengan booking berikutnya
dan berpotensi menimbulkan konflik jika tidak memiliki aturan yang transparan.

PhoenixCMS saat ini telah memiliki autentikasi, role, dan permission, tetapi
belum memiliki struktur jabatan organisasi. Jabatan organisasi dan tingkat
prioritas booking harus dibangun terpisah dari role akses aplikasi.

## 3. Tujuan

1. Menampilkan slot ruang rapat yang benar-benar tersedia untuk hari ini, besok,
   atau tanggal pilihan pengguna.
2. Memungkinkan booking langsung dikonfirmasi ketika slot masih tersedia.
3. Menampilkan kapasitas, fasilitas, lokasi, dan estimasi biaya sebelum booking.
4. Membebankan biaya booking ke divisi, cost center, atau project internal.
5. Mendukung extend yang aman, transparan, dan mempertimbangkan prioritas jabatan.
6. Mencegah double booking melalui pemeriksaan konflik yang bersifat transaksional.
7. Menyediakan laporan pemakaian dan chargeback yang dapat diaudit.

## 4. Non-Goals MVP

- Pembayaran pribadi, payment gateway, kartu kredit, dan dompet digital.
- Pelanggan eksternal atau marketplace penyewaan ruang publik.
- Booking berulang atau recurring meeting.
- Dynamic pricing berdasarkan permintaan.
- Lelang slot, waitlist otomatis, atau auto-preemption berdasarkan jabatan.
- Integrasi Google Calendar, Microsoft Outlook, perangkat akses pintu, atau sensor.
- Booking resource selain meeting room pada UI produksi.
- Paket subscription atau lisensi bulanan per ruang.

## 5. Terminologi

- **Room Rate Plan**: aturan tarif penggunaan sebuah ruang. Istilah ini menggantikan
  "lisensi ruangan" karena model MVP adalah biaya per booking, bukan subscription.
- **Fixed Facility**: fasilitas permanen yang sudah termasuk tarif ruang, seperti
  TV, proyektor terpasang, whiteboard, dan video conference unit.
- **Add-on**: fasilitas atau layanan opsional yang memiliki biaya tambahan, seperti
  proyektor portabel, operator, konsumsi, atau perlengkapan tambahan.
- **Cost Center**: unit pembebanan biaya internal.
- **Priority Level**: tingkat prioritas jabatan untuk mengajukan override extend;
  bukan hak untuk otomatis membatalkan booking pengguna lain.
- **Room Manager**: pengguna yang berwenang mengelola ruang dan menyelesaikan
  konflik extend.

## 6. Persona dan Hak Utama

### 6.1 Employee / Booker

- Melihat ruang dan slot tersedia.
- Melakukan, melihat, membatalkan, dan memperpanjang booking miliknya.
- Memilih cost center atau project yang diizinkan untuknya.
- Melihat estimasi serta biaya final booking.

### 6.2 Room Manager

- Mengelola jadwal operasional, blackout, maintenance, dan konflik extend untuk
  ruang yang menjadi tanggung jawabnya.
- Memindahkan booking terdampak ke ruang pengganti melalui proses terkontrol.
- Menandai no-show atau koreksi pemakaian dengan alasan wajib.

### 6.3 Finance / Billing

- Melihat chargeback per divisi, cost center, project, ruang, dan periode.
- Melakukan adjustment atau reversal dengan alasan dan audit log.
- Memfinalisasi serta mengekspor statement bulanan.

### 6.4 Administrator

- Mengelola ruang, fasilitas, tarif, struktur organisasi, jabatan, priority level,
  permission, dan kebijakan booking global.

## 7. Scope Fungsional MVP

### 7.1 Room Catalog

Setiap ruang memiliki:

- nama, kode unik, status aktif/nonaktif;
- gedung, lantai, lokasi, deskripsi, dan foto;
- kapasitas minimum dan maksimum;
- timezone dan jam operasional;
- durasi minimum, durasi maksimum, interval booking, dan buffer antarmeeting;
- Room Manager;
- fixed facilities dan add-ons;
- Room Rate Plan aktif;
- blackout atau jadwal maintenance.

Fasilitas memiliki nama, kategori, jumlah, unit, deskripsi, dan metadata yang
relevan. Contoh: `TV`, jumlah `1`, ukuran `65`, unit ukuran `inch`; atau
`Projector`, jumlah `1`, resolusi `4K`, tipe `fixed`.

### 7.2 Availability

Halaman utama menyediakan tab atau filter:

- Hari Ini;
- Besok;
- Pilih Tanggal;
- waktu mulai, durasi, kapasitas, lokasi, dan fasilitas wajib.

Hasil menampilkan daftar ruang yang memenuhi kebutuhan beserta:

- slot tersedia;
- kapasitas;
- fasilitas utama;
- tarif dasar;
- estimasi biaya untuk durasi yang dipilih;
- status `Available`, `Limited`, `Maintenance`, atau `Closed`.

Pengguna dapat membuka detail ruang untuk melihat timeline harian. Identitas dan
judul meeting pengguna lain tidak ditampilkan kepada pengguna biasa; slot yang
terisi hanya ditampilkan sebagai `Booked`.

### 7.3 Create Booking

Form booking memuat:

- ruang;
- tanggal, waktu mulai, waktu selesai, dan durasi;
- judul dan agenda singkat;
- organizer dan participant opsional;
- jumlah peserta;
- cost center wajib;
- project opsional;
- add-ons opsional;
- catatan kebutuhan khusus;
- ringkasan tarif dan estimasi chargeback.

Booking langsung berstatus `Confirmed` jika:

1. ruang aktif dan berada dalam jam operasional;
2. kapasitas mencukupi;
3. slot dan buffer tidak bertabrakan;
4. tidak berada dalam blackout atau maintenance;
5. cost center valid dan boleh digunakan organizer;
6. Room Rate Plan aktif;
7. add-on yang memiliki stok memenuhi kebutuhan.

Sistem mengulang pemeriksaan konflik secara transaksional saat konfirmasi. Jika
slot baru saja diambil pengguna lain, booking tidak dibuat dan UI menampilkan
slot alternatif terbaru.

### 7.4 My Bookings

Pengguna dapat melihat booking pada kategori:

- Upcoming;
- In Progress;
- Completed;
- Cancelled;
- No-show.

Detail booking menampilkan ruang, waktu, fasilitas, participant, cost center,
estimasi biaya, biaya final, riwayat extend, dan activity log yang boleh dilihat
pengguna.

### 7.5 Extend Booking

Tombol extend tersedia mulai 30 menit sebelum waktu selesai hingga booking
berakhir. Extend menggunakan interval booking ruang, dengan default 30 menit.

#### Extend tanpa konflik

Jika ruang, fixed facilities, dan add-ons tetap tersedia, extend langsung
disetujui. Sistem memperbarui waktu selesai serta estimasi chargeback secara
atomik.

#### Extend dengan konflik

Jika extend bertabrakan dengan booking berikutnya:

1. Sistem membandingkan priority level jabatan organizer saat ini dengan
   organizer booking berikutnya.
2. Jika prioritas saat ini sama atau lebih rendah, extend ditolak dan sistem
   menawarkan ruang alternatif yang sesuai.
3. Jika prioritas saat ini lebih tinggi, pengguna dapat membuat
   `Priority Override Request`; booking berikutnya tidak otomatis dibatalkan.
4. Sistem mencari ruang pengganti yang memenuhi waktu, kapasitas, dan fasilitas
   booking terdampak.
5. Organizer terdampak dapat menerima perpindahan yang ditawarkan atau menolak.
6. Room Manager dapat menyetujui override hanya dengan memilih ruang pengganti
   yang berhasil direservasi secara atomik, atau setelah organizer terdampak
   secara eksplisit menyetujui pembatalan.
7. Jika tidak ada keputusan sebelum waktu selesai booking saat ini, permintaan
   extend kedaluwarsa dan jadwal awal tetap berlaku.

Semua permintaan, persetujuan, penolakan, perpindahan, dan pembatalan menyimpan
pelaku, waktu, alasan, nilai prioritas, serta kondisi sebelum/sesudah.

### 7.6 Cancellation dan No-show

Kebijakan default MVP:

- pembatalan minimal 2 jam sebelum mulai: tidak ada charge;
- pembatalan kurang dari 2 jam sebelum mulai: charge 50% dari tarif ruang;
- pembatalan setelah waktu mulai atau no-show: charge 100% tarif ruang;
- add-on yang belum dikonsumsi tidak ditagihkan;
- Finance dapat membuat adjustment dengan alasan wajib.

Nilai persentase dan cancellation window dapat diubah Administrator, tetapi
perubahan hanya berlaku untuk booking baru agar histori biaya tetap konsisten.

## 8. Aturan Harga dan Chargeback

### 8.1 Tarif Ruang

Setiap ruang memiliki satu Room Rate Plan aktif pada suatu waktu. Rate plan
memiliki tanggal efektif, tarif per jam, interval penagihan, mata uang, dan aturan
pembulatan. Default MVP menggunakan interval 30 menit dan membulatkan durasi ke
atas ke interval terdekat.

```text
Billable Room Cost = Hourly Rate × Rounded Billable Minutes / 60
Estimated Cost     = Billable Room Cost + Estimated Add-on Cost
Final Cost         = Actual Approved Duration Cost + Fulfilled Add-on Cost
                     + Cancellation/No-show Charge + Adjustment
```

Fixed facilities sudah termasuk dalam tarif ruang. Add-on dapat menggunakan
harga per booking, per unit, atau per jam. Harga yang berlaku disalin sebagai
snapshot ke booking sehingga perubahan tarif di masa depan tidak mengubah
booking lama.

### 8.2 Internal Chargeback

- Setiap booking wajib memiliki satu cost center.
- Cost center berasal dari unit organisasi dan akses pengguna.
- Estimasi biaya ditampilkan sebelum konfirmasi.
- Biaya final diposting setelah booking selesai, dibatalkan terlambat, atau
  ditandai no-show.
- Extend menambah biaya berdasarkan rate snapshot booking yang sama.
- Statement bulanan mengelompokkan biaya per divisi, cost center, project,
  ruang, dan organizer.
- MVP menyediakan ekspor CSV/XLSX; posting otomatis ke ERP berada di luar scope.

## 9. Struktur Organisasi dan Prioritas

Role/permission aplikasi dan jabatan organisasi adalah dua konsep berbeda.

- `Role/Permission` menentukan tindakan yang boleh dilakukan dalam aplikasi.
- `Position` menyimpan nama jabatan dan `priority_level`.
- `Organization Unit` menyimpan divisi/departemen dan struktur induknya.
- `Cost Center` menyimpan kode pembebanan dan unit pemilik.
- Setiap pengguna memiliki satu posisi utama aktif serta satu unit utama pada MVP.
- Administrator mengatur priority level; pengguna tidak dapat mengubahnya.
- Priority level hanya digunakan pada konflik extend dalam MVP.
- Priority level tidak memberikan akses admin dan tidak memengaruhi booking awal
  yang sudah lebih dahulu dikonfirmasi.

## 10. Status dan State Transition

### 10.1 Booking

```text
Confirmed → In Progress → Completed
Confirmed → Cancelled
In Progress → Cancelled
Confirmed → No-show
```

Booking yang dipindahkan tetap memiliki identitas yang sama, tetapi menyimpan
room lama, room baru, alasan, actor, dan timestamp di activity log.

### 10.2 Extension Request

```text
Pending → Approved
Pending → Rejected
Pending → Expired
Pending → Cancelled
```

### 10.3 Charge

```text
Estimated → Posted → Adjusted
Posted/Adjusted → Reversed
```

Setiap transisi invalid harus ditolak di server dan tidak hanya disembunyikan
dari UI.

## 11. Notifikasi

Notifikasi in-app dan email dikirim untuk:

- booking confirmed atau cancelled;
- reminder 24 jam dan 30 menit sebelum mulai;
- perubahan ruang atau waktu;
- extend berhasil;
- Priority Override Request diterima organizer terdampak dan Room Manager;
- permintaan disetujui, ditolak, dibatalkan, atau kedaluwarsa;
- booking ditandai no-show;
- statement chargeback bulanan selesai difinalisasi.

Notifikasi konflik harus menjelaskan dampak, alternatif yang ditawarkan, batas
waktu respons, dan siapa yang mengambil keputusan.

## 12. Halaman dan Navigasi

### Employee

1. **Booking Dashboard** — pencarian Hari Ini/Besok/Tanggal dan rekomendasi ruang.
2. **Room Detail** — foto, lokasi, fasilitas, kebijakan, tarif, dan timeline.
3. **Booking Review** — detail meeting, cost center, add-ons, serta estimasi biaya.
4. **My Bookings** — daftar, detail, cancel, dan extend.
5. **Override Resolution** — menerima atau menolak perpindahan booking terdampak.

### Management

1. **Room Management** — ruang, jam operasional, fasilitas, blackout, dan manager.
2. **Rate Plans & Add-ons** — tarif efektif dan katalog add-on.
3. **Organization & Priority** — unit, posisi, priority level, dan cost center.
4. **Conflict Center** — permintaan extend yang memerlukan resolusi.
5. **Chargeback Reports** — biaya, utilization, no-show, cancellation, dan ekspor.

## 13. Model Data Konseptual

Entitas utama:

- `bookable_resources` — fondasi resource generik; MVP hanya memakai tipe
  `meeting_room`;
- `meeting_rooms` — detail khusus ruang;
- `room_operating_hours` dan `room_blackouts`;
- `facilities` dan `room_facilities`;
- `addons` dan `room_addons`;
- `room_rate_plans`;
- `bookings` dan `booking_participants`;
- `booking_addons`;
- `booking_extension_requests`;
- `booking_relocations`;
- `booking_charge_snapshots` dan `booking_charge_adjustments`;
- `organization_units`, `positions`, `cost_centers`, dan `user_org_profiles`;
- `chargeback_statements` dan `chargeback_statement_items`;
- `booking_activity_logs`.

Relasi dan nama tabel final diputuskan pada implementation plan setelah schema
aktif serta konvensi repository diperiksa lebih dalam.

## 14. Permission Konseptual

- `booking.view_availability`
- `booking.create`
- `booking.view_own`
- `booking.cancel_own`
- `booking.extend_own`
- `booking.resolve_affected`
- `booking.manage_rooms`
- `booking.manage_rates`
- `booking.manage_organization`
- `booking.resolve_overrides`
- `booking.view_chargebacks`
- `booking.adjust_charges`
- `booking.finalize_statements`

Permission menggunakan mekanisme RBAC PhoenixCMS yang sudah ada. Priority level
jabatan tidak boleh digunakan sebagai pengganti authorization.

## 15. Arah Arsitektur dan Data Flow

PRD ini tidak menetapkan struktur class final, tetapi menetapkan batas domain
yang harus tetap terpisah:

- **Resource Catalog** mengelola ruang, fasilitas, operating hours, blackout,
  add-on, dan Room Manager.
- **Availability Engine** menghitung slot dari jam operasional, buffer, blackout,
  booking, kapasitas, fasilitas, dan stok add-on.
- **Booking Service** memvalidasi dan membuat perubahan booking secara
  transaksional.
- **Pricing & Chargeback Service** menghasilkan quote, menyimpan snapshot,
  mem-posting charge, adjustment, reversal, dan statement.
- **Extension Workflow** menangani extend normal, priority comparison, override,
  relocation, serta expiry.
- **Organization Service** mengelola unit, posisi, priority level, cost center,
  dan assignment pengguna.
- **Notification Service** menerima event domain setelah transaksi berhasil;
  kegagalan notifikasi tidak boleh membatalkan booking yang sudah committed.
- **Audit Service** menyimpan jejak perubahan material secara konsisten.

### 15.1 Data Flow Pencarian dan Booking

1. Pengguna mengirim tanggal, waktu, durasi, kapasitas, dan fasilitas.
2. Availability Engine mengembalikan ruang serta slot yang memenuhi syarat.
3. Pricing Service menghasilkan quote bertimestamp dari rate plan aktif.
4. Pengguna memilih cost center dan mengonfirmasi booking.
5. Booking Service memeriksa ulang permission, quote, cost center, stok, dan
   overlap di dalam transaksi.
6. Booking, price snapshot, add-on reservation, dan activity log disimpan.
7. Setelah commit, event booking memicu notifikasi dan pembaruan tampilan jadwal.

### 15.2 Data Flow Extend dan Override

1. Booking Service memvalidasi status, ownership, waktu, serta interval extend.
2. Availability Engine memeriksa ruang dan add-on hingga waktu selesai baru.
3. Jika kosong, Booking Service memperpanjang booking dan Pricing Service
   memperbarui estimasi.
4. Jika konflik, Extension Workflow membandingkan priority level yang tersimpan
   pada profil organisasi aktif.
5. Request yang memenuhi syarat menyimpan snapshot priority dan alternatif ruang.
6. Approval melakukan reservasi ruang pengganti, relocation, extend, repricing,
   dan audit log dalam satu transaksi.
7. Setelah commit, seluruh pihak menerima hasil yang sama melalui notifikasi.

### 15.3 Error Handling

- Quote yang kedaluwarsa atau berubah ditolak dengan quote baru untuk dikonfirmasi
  pengguna; sistem tidak diam-diam menambah biaya.
- Konflik akibat request bersamaan menghasilkan respons conflict dan rekomendasi
  slot terbaru tanpa membuat booking parsial.
- Cost center nonaktif, rate plan tidak aktif, add-on habis, atau ruang maintenance
  menghasilkan pesan spesifik yang dapat ditindaklanjuti.
- Kegagalan reservasi ruang pengganti membatalkan seluruh approval override;
  booking lama dan booking terdampak tetap utuh.
- Retry request menggunakan idempotency key agar tidak menggandakan booking,
  charge, adjustment, atau notifikasi.
- Kegagalan notifikasi masuk retry queue dan terlihat oleh Administrator, tetapi
  tidak mengubah hasil transaksi domain.

### 15.4 Strategi Pengujian

- **Unit tests**: slot calculation, buffer, blackout, priority comparison,
  pembulatan tarif, cancellation charge, add-on pricing, dan state transition.
- **Feature tests**: permission, room filtering, booking lifecycle, chargeback,
  extend, override, relocation, adjustment, ekspor, dan privasi booking lain.
- **Concurrency tests**: dua booking pada slot sama, booking vs extend, serta dua
  approval terhadap ruang pengganti yang sama.
- **Notification tests**: event hanya dikirim setelah commit dan retry tidak
  menggandakan pesan.
- **Browser/runtime QA**: pencarian Hari Ini/Besok, responsive layout, keyboard,
  quote review, conflict recovery, dan seluruh alur extend.
- **Regression tests**: autentikasi, RBAC, navigasi CMS, serta modul eksisting
  yang berbagi account dan permission tetap berjalan.

## 16. Non-Functional Requirements

### Konsistensi dan Concurrency

- Tidak boleh ada dua booking aktif yang overlap pada ruang yang sama.
- Pemeriksaan availability dan pembuatan/perpindahan booking harus berada dalam
  transaksi database dengan strategi locking yang sesuai.
- Endpoint mutasi harus idempotent terhadap retry atau double-click.

### Waktu

- Waktu disimpan dalam UTC dan ditampilkan mengikuti timezone ruang/pengguna.
- Perhitungan buffer, blackout, extend, dan billing menggunakan sumber waktu
  server, bukan jam browser.

### Keamanan dan Privasi

- Pengguna biasa tidak dapat melihat judul, agenda, participant, atau cost center
  booking milik pengguna lain.
- Semua aksi billing dan override memiliki audit log immutable pada level aplikasi.
- Input teks, identifier, enum, tanggal, tarif, dan quantity divalidasi di server.

### Performa

- Pencarian availability untuk rentang satu hari dan hingga 100 ruang memiliki
  target respons p95 maksimal 2 detik pada beban operasional normal.
- Kalender tidak memuat seluruh histori; data diambil berdasarkan rentang tanggal.

### Aksesibilitas dan Responsivitas

- Alur pencarian dan booking dapat digunakan melalui keyboard.
- Status tidak dibedakan hanya dengan warna.
- Layout mendukung desktop, tablet, dan mobile tanpa menghilangkan informasi biaya
  atau konflik penting.

## 17. Acceptance Criteria MVP

1. Pengguna dapat memilih Hari Ini atau Besok dan melihat hanya slot yang valid
   berdasarkan jam operasional, blackout, buffer, dan booking aktif.
2. Filter kapasitas dan fasilitas mengeluarkan ruang yang tidak memenuhi syarat.
3. Detail ruang menampilkan fasilitas terstruktur, termasuk jumlah dan metadata
   seperti ukuran TV dalam inch.
4. Estimasi biaya menggunakan rate plan dan add-on yang berlaku, dengan hasil
   pembulatan yang konsisten antara UI dan server.
5. Booking kosong langsung terkonfirmasi dan menyimpan snapshot harga serta cost
   center.
6. Dua permintaan bersamaan untuk ruang dan waktu yang sama hanya menghasilkan
   satu booking terkonfirmasi.
7. Extend tanpa konflik memperbarui waktu dan estimasi biaya dalam satu transaksi.
8. Extend yang bertabrakan ditolak untuk prioritas sama/lebih rendah.
9. Prioritas lebih tinggi hanya dapat membuat override request dan tidak otomatis
   membatalkan booking berikutnya.
10. Override hanya disetujui setelah ruang pengganti berhasil direservasi atau
    organizer terdampak menyetujui pembatalan.
11. Booking yang dibatalkan atau no-show menghasilkan charge sesuai snapshot
    kebijakan yang berlaku saat booking dibuat.
12. Finance dapat melihat, memfilter, melakukan adjustment beralasan, dan
    mengekspor chargeback per periode.
13. Pengguna tanpa permission ditolak oleh backend meskipun memanggil endpoint
    secara langsung.
14. Semua perubahan booking, override, relocation, dan charge tercatat dalam
    activity log.

## 18. KPI Awal

- Persentase booking berhasil tanpa bantuan admin.
- Utilization rate per ruang dan jam.
- Persentase pencarian yang menghasilkan booking.
- Konflik booking dan konflik extend per 100 booking.
- Waktu rata-rata penyelesaian override request.
- Cancellation dan no-show rate.
- Nilai chargeback per divisi/cost center.
- Persentase ruang dengan fasilitas atau kapasitas yang tidak sesuai kebutuhan.

## 19. Tahapan Rilis

### Phase 1 — MVP Meeting Room

- organisasi, jabatan, priority level, dan cost center;
- room catalog, fasilitas, add-on, jam operasional, dan blackout;
- rate plan dan internal chargeback;
- availability Hari Ini/Besok/Tanggal;
- create, cancel, dan My Bookings;
- extend normal dan controlled priority override;
- notifikasi, audit log, serta laporan dasar.

### Phase 1.1 — Operational Improvements

- check-in manual atau QR;
- deteksi no-show berbasis check-in;
- template room setup;
- dashboard utilization dan rekomendasi kapasitas.

### Phase 2 — Integrasi dan Resource Lain

- recurring booking;
- calendar integration;
- ERP/accounting export terjadwal;
- approval policy opsional per ruang;
- booking kendaraan, perangkat, venue, atau resource lain;
- pengguna eksternal dan payment gateway jika strategi produk membutuhkannya.

## 20. Risiko dan Mitigasi

### Penyalahgunaan Prioritas Jabatan

Mitigasi: tidak ada auto-preemption, alasan wajib, ruang pengganti, persetujuan
eksplisit, permission terpisah, dan audit log.

### Double Booking

Mitigasi: validasi server, transaksi, locking, constraint/strategi overlap, dan
tes concurrency.

### Tarif Berubah Mengubah Histori

Mitigasi: snapshot rate, fasilitas, add-on, dan cancellation policy pada booking.

### Data Organisasi Tidak Akurat

Mitigasi: hanya Administrator yang mengubah position/priority/cost center,
effective dating, dan histori perubahan.

### Fasilitas Tercatat tetapi Tidak Tersedia

Mitigasi: status fasilitas, maintenance, stok add-on, laporan masalah, dan
verifikasi Room Manager.

## 21. Keputusan Produk yang Disepakati

- Rilis pertama berfokus pada meeting room.
- Booking menggunakan daftar jadwal yang tersedia.
- Model monetisasi MVP adalah biaya per booking melalui internal chargeback.
- Biaya dibebankan ke divisi/cost center, bukan dibayar pribadi.
- Availability-first dengan konfirmasi otomatis digunakan sebagai alur utama.
- Fixed facilities termasuk tarif ruang; add-on dapat ditagihkan terpisah.
- Extend didukung dan konflik ditangani dengan controlled priority override.
- Jabatan/prioritas organisasi dipisahkan dari role/permission aplikasi.
- Tidak ada pembatalan otomatis hanya karena pengguna memiliki jabatan lebih tinggi.
