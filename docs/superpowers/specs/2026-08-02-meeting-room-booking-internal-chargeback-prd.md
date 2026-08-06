# PRD — Asset & Meeting Room Booking dengan Optional Payment Module

## 1. Ringkasan Produk

Asset & Meeting Room Booking adalah modul PhoenixCMS untuk mengelola master asset,
menyusun fasilitas yang melekat pada sebuah room, menemukan jadwal yang tersedia,
dan melakukan booking asset. Room merupakan salah satu kategori asset; perangkat,
furniture, consumable, dan kategori lain dikelola melalui master asset yang sama.

Scope rilis pertama tetap berpusat pada meeting room. Satu room dapat memiliki
komposisi fasilitas terstruktur, misalnya satu meja besar, sepuluh kursi, satu
proyektor, dan sepuluh botol air putih. Asset tertentu dapat dibooking secara
mandiri, menjadi komponen/add-on room, atau mendukung kedua mode tersebut.

Payment merupakan modul opsional dengan satu pengaturan global. Kondisi default
adalah nonaktif sehingga booking berjalan tanpa harga atau pembayaran. Ketika
diaktifkan, halaman pengelolaan Room Rate Plan, payment gateway, transaksi, dan
kebijakan pembayaran muncul sesuai permission.

## 2. Latar Belakang

Pemesanan ruang dan perangkat sering bergantung pada komunikasi manual sehingga
pengguna sulit mengetahui jadwal kosong, fasilitas yang didapatkan, dan asset
pendukung yang dapat digunakan. Pengelolaan room yang terpisah dari perangkat dan
furniture juga membuat data fasilitas tidak konsisten.

PhoenixCMS telah memiliki autentikasi, role, dan permission. Sistem booking perlu
memakai mekanisme tersebut serta menambahkan hierarki otoritas booking untuk
Super Admin, Admin, Presiden Komisaris/Preskom, dan Direktur. Hierarki ini
menentukan apakah sebuah booking dapat dipindahkan atau dibatalkan langsung, atau
harus melalui persetujuan organizer.

## 3. Tujuan

1. Menyediakan master asset terstruktur dengan kategori yang dapat dikembangkan.
2. Menjadikan room sebagai asset dengan detail dan aturan booking khusus room.
3. Menyimpan komposisi fasilitas room beserta quantity yang didapatkan pengguna.
4. Menampilkan slot room dan asset yang benar-benar tersedia.
5. Memungkinkan booking langsung dikonfirmasi ketika slot masih tersedia.
6. Mendukung pemindahan dan pembatalan booking berdasarkan hierarki role.
7. Mendukung extend yang aman tanpa menyebabkan double booking.
8. Menyediakan payment module yang dapat diaktifkan atau dinonaktifkan secara
   global tanpa merusak booking yang sudah ada.
9. Menyediakan activity log dan notifikasi untuk perubahan material pada booking.

## 4. Non-Goals MVP

- Inventory stock tracking, stock balance, stock movement, dan pengurangan
  consumable otomatis.
- Substitusi fasilitas otomatis ketika sebuah item tidak tersedia.
- Sinkronisasi ke sistem inventory atau ERP eksternal.
- Booking berulang atau recurring meeting.
- Dynamic pricing berdasarkan permintaan.
- Lelang slot, waitlist otomatis, atau preemption tanpa aturan otoritas.
- Integrasi Google Calendar, Microsoft Outlook, perangkat akses pintu, atau sensor.
- Perubahan atau penghapusan master asset akibat pemindahan/pembatalan booking.
- Pengguna eksternal atau marketplace penyewaan asset publik.

## 5. Terminologi

- **Asset**: resource yang dikelola dalam master data, seperti room, laptop,
  proyektor, meja, kursi, atau consumable.
- **Asset Category**: pengelompokan asset seperti Room, Device, Furniture, dan
  Consumable.
- **Room Composition**: daftar asset/fasilitas beserta quantity yang menjadi
  bagian atau kelengkapan sebuah room.
- **Fixed Facility**: fasilitas yang secara operasional melekat pada room, seperti
  meja besar, kursi utama, TV, atau proyektor terpasang.
- **Bookable Asset**: asset yang memiliki jadwal dan dapat dibooking secara
  mandiri atau bersama room.
- **Add-on/Component Asset**: asset yang dapat ditambahkan ke booking room.
- **Room Rate Plan**: aturan harga penggunaan room yang hanya berlaku ketika
  Payment Module aktif.
- **Payment Module**: fitur global opsional untuk rate plan, payment gateway,
  transaksi, refund, dan kebijakan pembayaran.
- **Booking Authority Level**: urutan otoritas role untuk memindahkan atau
  membatalkan booking pengguna lain.
- **Move Booking**: memindahkan booking ke room/asset atau slot lain yang kosong.
- **Force Cancel**: pembatalan langsung oleh role berotoritas terhadap booking
  milik role yang lebih rendah.
- **Booking Change Request**: permintaan move atau cancel kepada organizer ketika
  target memiliki level role yang sama atau lebih tinggi.

## 6. Persona dan Hak Utama

### 6.1 Employee / Booker

- Melihat asset dan slot tersedia.
- Melakukan, melihat, membatalkan, dan memperpanjang booking miliknya.
- Melihat komposisi fasilitas yang didapatkan dari room.
- Menambahkan asset yang tersedia sebagai komponen/add-on booking.
- Menyetujui atau menolak Booking Change Request atas booking miliknya.

### 6.2 Asset / Room Manager

- Mengelola master asset sesuai permission dan scope tanggung jawabnya.
- Mengelola kategori, detail room, jam operasional, blackout, dan maintenance.
- Menentukan komposisi fasilitas dan quantity pada room.
- Menangani masalah operasional asset tanpa mengubah histori booking.

### 6.3 Booking Authority

Role berotoritas terdiri dari:

1. Super Admin;
2. Admin;
3. Presiden Komisaris/Preskom;
4. Direktur.

Role tersebut dapat memindahkan atau melakukan Force Cancel berdasarkan hasil
perbandingan level role organizer target.

### 6.4 Payment Administrator (Kondisional)

Persona ini hanya aktif ketika Payment Module diaktifkan dan memiliki permission
yang sesuai.

- Mengelola Room Rate Plan dan kebijakan harga.
- Mengelola konfigurasi payment gateway.
- Melihat transaksi, kegagalan pembayaran, refund, dan laporan pembayaran.

### 6.5 Administrator

- Mengelola asset, kategori, konfigurasi booking, role, permission, dan level
  otoritas booking.
- Mengaktifkan atau menonaktifkan Payment Module secara global.
- Melihat activity log dan kegagalan proses sistem.

## 7. Scope Fungsional MVP

### 7.1 Master Asset dan Kategori

Master Asset menyimpan data umum:

- kode asset unik;
- nama asset;
- kategori dan subkategori;
- status aktif, nonaktif, maintenance, atau retired;
- lokasi dan unit pemilik opsional;
- deskripsi, foto, dan metadata;
- unit pengukuran, seperti unit, buah, set, atau botol;
- mode booking: standalone, component/add-on, keduanya, atau non-bookable.

Kategori awal mencakup Room, Device, Furniture, dan Consumable. Administrator
dapat menambah kategori tanpa mengubah struktur booking inti.

Asset berserial seperti laptop atau proyektor dapat dicatat sebagai unit asset
tersendiri. Asset berbasis quantity seperti kursi tambahan atau botol air dicatat
sebagai master item, tetapi jumlah stok aktual belum dilacak pada MVP.

### 7.2 Room Catalog dan Room Composition

Room adalah asset berkategori `Room` dan memiliki detail khusus:

- gedung, lantai, lokasi, deskripsi, dan foto;
- kapasitas minimum dan maksimum;
- timezone dan jam operasional;
- durasi minimum dan maksimum;
- interval booking dan buffer antarmeeting;
- Asset/Room Manager;
- blackout dan jadwal maintenance;
- Room Composition.

Saat membuat atau mengubah room, Administrator/Asset Manager dapat menambahkan
komponen fasilitas dari Master Asset. Setiap baris komposisi minimal menyimpan:

- asset/fasilitas;
- quantity;
- unit;
- tipe komponen: fixed facility, device, furniture, atau consumable;
- status included atau optional/add-on;
- catatan dan metadata yang relevan.

Contoh komposisi:

| Komponen | Quantity | Unit | Tipe |
|---|---:|---|---|
| Meja besar | 1 | unit | Fixed facility |
| Kursi | 10 | unit | Furniture |
| Proyektor | 1 | unit | Device |
| Air putih botol | 10 | botol | Consumable |

Quantity pada Room Composition adalah informasi fasilitas, bukan stock balance.
Perubahan botol air menjadi air putih gelas kaca dilakukan manual pada komposisi
room. Tidak ada pengurangan stok atau substitusi otomatis pada MVP.

### 7.3 Availability

Halaman utama menyediakan filter:

- Hari Ini;
- Besok;
- Pilih Tanggal;
- waktu mulai dan durasi;
- kapasitas;
- lokasi;
- kategori dan fasilitas wajib.

Hasil menampilkan daftar room/asset yang memenuhi kebutuhan beserta slot,
kapasitas, fasilitas utama, dan status `Available`, `Limited`, `Maintenance`, atau
`Closed`.

Asset berserial yang dibooking secara mandiri diperiksa terhadap overlap jadwal.
Quantity room composition tidak digunakan sebagai validasi stok pada MVP.

Identitas, agenda, dan participant booking pengguna lain tidak ditampilkan kepada
pengguna biasa; slot yang terisi hanya ditampilkan sebagai `Booked`.

### 7.4 Create Booking

Form booking memuat:

- room atau asset utama;
- tanggal, waktu mulai, waktu selesai, dan durasi;
- judul dan agenda singkat;
- organizer dan participant opsional;
- jumlah peserta;
- komponen/add-on asset opsional;
- catatan kebutuhan khusus;
- ringkasan fasilitas yang didapatkan;
- informasi harga/payment hanya ketika Payment Module aktif.

Booking langsung berstatus `Confirmed` jika:

1. asset aktif dan berada dalam jam operasional jika aturan waktu diterapkan;
2. kapasitas mencukupi untuk room;
3. slot dan buffer tidak bertabrakan;
4. tidak berada dalam blackout atau maintenance;
5. asset berserial yang dipilih tidak digunakan booking lain;
6. aturan payment terpenuhi hanya ketika Payment Module aktif.

Sistem mengulang pemeriksaan konflik secara transaksional saat konfirmasi. Jika
slot baru saja diambil, booking tidak dibuat dan UI menampilkan alternatif terbaru.

Booking menyimpan snapshot nama asset, room composition, quantity, dan konfigurasi
payment yang berlaku saat booking dibuat. Perubahan master asset setelahnya tidak
mengubah histori booking.

### 7.5 My Bookings

Pengguna dapat melihat booking pada kategori:

- Upcoming;
- In Progress;
- Completed;
- Cancelled;
- No-show.

Detail menampilkan asset utama, jadwal, fasilitas, participant, riwayat extend,
riwayat pemindahan, status payment jika modul aktif, dan activity log yang boleh
dilihat pengguna.

### 7.6 Move Booking dan Force Cancel

Hierarki role booking adalah:

```text
Super Admin > Admin > Presiden Komisaris/Preskom > Direktur > Role biasa
```

Aturan aksi:

1. Role berotoritas dapat langsung memindahkan atau melakukan Force Cancel hanya
   terhadap booking milik organizer dengan level role lebih rendah.
2. Role tidak dapat langsung mengubah booking milik organizer dengan level yang
   sama atau lebih tinggi.
3. Untuk level sama atau lebih tinggi, sistem membuat Booking Change Request
   kepada organizer target.
4. Booking baru dipindahkan atau dibatalkan setelah organizer target menyetujui.
5. Penolakan atau request kedaluwarsa mempertahankan booking awal tanpa perubahan.
6. Semua aksi memerlukan alasan dan dicatat pada activity log.

Pemindahan booking menggunakan daftar slot/asset alternatif yang kosong. Setelah
target dipilih, sistem memeriksa ulang availability dan memindahkan booking secara
atomik. Booking mempertahankan identitas yang sama serta mencatat jadwal/asset
lama dan baru.

Jika target tidak lagi kosong saat persetujuan atau eksekusi, pemindahan gagal
tanpa mengubah booking awal dan sistem meminta pemilihan alternatif baru.

Force Cancel hanya mengubah status booking menjadi `Cancelled`, melepaskan slot,
dan mengirim notifikasi. Aksi ini tidak mengubah atau menghapus Master Asset,
Room Composition, ataupun quantity fasilitas. Karena inventory tracking berada
di luar MVP, pembatalan tidak menghasilkan stock movement.

### 7.7 Extend Booking

Tombol extend tersedia mulai 30 menit sebelum waktu selesai hingga booking
berakhir. Extend menggunakan interval booking asset, dengan default 30 menit.

Jika slot tetap kosong, extend langsung disetujui secara atomik. Jika bertabrakan,
pengguna biasa menerima alternatif dan tidak dapat mengambil slot berikutnya.

Role berotoritas dapat menjalankan aturan Move Booking/Force Cancel terhadap
booking berikutnya. Booking role lebih rendah dapat ditangani langsung; booking
role sama atau lebih tinggi harus melalui Booking Change Request. Tidak ada
pembatalan otomatis hanya karena level role lebih tinggi.

Jika tidak ada keputusan sebelum waktu booking saat ini berakhir, permintaan
extend kedaluwarsa dan jadwal awal tetap berlaku.

### 7.8 Cancellation dan No-show

Pengguna dapat membatalkan booking miliknya sesuai kebijakan waktu yang ditetapkan
Administrator. Pembatalan melepaskan slot tanpa mengubah Master Asset atau Room
Composition.

Pada kondisi Payment Module nonaktif, cancellation dan no-show tidak memiliki
dampak finansial. Jika Payment Module aktif, kebijakan biaya/refund yang tersimpan
pada snapshot booking dapat diterapkan tanpa mengubah histori booking lain.

## 8. Optional Payment Module

### 8.1 Global Toggle

Sistem memiliki satu konfigurasi global `Payment Module` dengan kondisi default
`Disabled`.

Ketika `Disabled`:

- menu dan form payment disembunyikan;
- Room Rate Plan tidak diwajibkan;
- booking tidak menampilkan harga, checkout, payment gateway, atau status bayar;
- endpoint mutasi payment ditolak oleh backend;
- booking baru menyimpan bahwa payment tidak berlaku.

Ketika `Enabled`:

- menu Room Rate Plan dan payment gateway muncul sesuai permission;
- Administrator dapat mengelola tarif, interval, mata uang, dan kebijakan harga;
- pengguna melihat estimasi harga dan langkah pembayaran pada booking;
- transaksi, kegagalan, refund, dan audit payment dicatat;
- booking tidak dapat dikonfirmasi sebelum aturan payment yang aktif terpenuhi.

Perubahan toggle tidak mengubah histori. Booking yang dibuat ketika module
nonaktif tetap tanpa payment. Menonaktifkan module setelah pernah digunakan tidak
menghapus transaksi lama; histori tetap tersedia bagi pengguna berpermission
untuk kebutuhan audit.

### 8.2 Room Rate Plan

Ketika Payment Module aktif, sebuah room dapat memiliki satu Room Rate Plan aktif
pada suatu waktu. Rate plan dapat menyimpan tanggal efektif, tarif, interval
penagihan, mata uang, pembulatan, cancellation policy, dan refund policy.

Harga yang berlaku disalin sebagai snapshot ke booking agar perubahan rate plan
tidak mengubah transaksi lama.

### 8.3 Payment Gateway

Konfigurasi gateway minimal mencakup provider, mode sandbox/production, status,
payment method yang diizinkan, callback/webhook setting, dan credential terenkripsi.
Secret harus disamarkan pada UI dan tidak boleh masuk log atau response biasa.

## 9. Role Hierarchy dan Otoritas Booking

Role/permission menentukan akses aplikasi dan Booking Authority Level menentukan
batas langsung terhadap booking pengguna lain. Keduanya tetap berada dalam domain
RBAC aplikasi, bukan jabatan organisasi terpisah.

Urutan default:

1. Super Admin;
2. Admin;
3. Presiden Komisaris/Preskom;
4. Direktur;
5. role biasa.

Aturan otoritas:

- aksi hanya berjalan jika actor memiliki permission yang sesuai;
- perbandingan level menggunakan role aktif organizer target;
- direct move dan Force Cancel hanya berlaku ke level lebih rendah;
- level sama atau lebih tinggi wajib melalui Booking Change Request;
- level role tidak otomatis memberikan akses mengubah Master Asset atau Payment;
- perubahan level role tidak mengubah histori actor/target yang sudah disnapshot.

Jika pengguna memiliki lebih dari satu role, sistem menggunakan level otoritas
tertinggi yang aktif. Detail mapping level ke role diputuskan pada implementation
plan dan disimpan secara terstruktur, bukan hard-coded di UI.

## 10. Status dan State Transition

### 10.1 Booking

```text
Confirmed → In Progress → Completed
Confirmed → Cancelled
In Progress → Cancelled
Confirmed → No-show
```

Pemindahan tidak membuat booking baru dan tidak menjadi status terminal. Sistem
menyimpan event `Moved` berisi target lama, target baru, actor, alasan, dan waktu.

### 10.2 Booking Change Request

```text
Pending → Approved
Pending → Rejected
Pending → Expired
Pending → Cancelled
```

Approval terhadap move hanya berhasil jika target masih tersedia pada saat
eksekusi. Approval terhadap cancel mengubah booking target menjadi `Cancelled`.

### 10.3 Extension Request

```text
Pending → Approved
Pending → Rejected
Pending → Expired
Pending → Cancelled
```

### 10.4 Payment Transaction (Kondisional)

```text
Pending → Paid
Pending → Failed
Paid → Refunded
Paid → Partially Refunded
```

State payment hanya digunakan ketika Payment Module aktif. Semua transisi invalid
ditolak di server dan tidak hanya disembunyikan dari UI.

## 11. Notifikasi

Notifikasi in-app dan email dikirim untuk:

- booking confirmed, moved, cancelled, completed, atau no-show;
- reminder sebelum booking dimulai;
- perubahan room, asset, atau waktu;
- extend berhasil atau gagal;
- Booking Change Request diterima organizer target;
- request disetujui, ditolak, dibatalkan, atau kedaluwarsa;
- Force Cancel oleh role berotoritas;
- status payment/refund ketika Payment Module aktif.

Notifikasi perubahan harus menjelaskan actor, alasan, dampak, target alternatif,
batas waktu respons, dan hasil akhir. Kegagalan notifikasi tidak boleh membatalkan
transaksi booking yang sudah committed.

## 12. Halaman dan Navigasi

### Employee

1. **Booking Dashboard** — pencarian Hari Ini/Besok/Tanggal dan rekomendasi asset.
2. **Asset/Room Detail** — foto, lokasi, fasilitas, kebijakan, dan timeline.
3. **Booking Review** — jadwal, peserta, komposisi fasilitas, dan add-on.
4. **My Bookings** — daftar, detail, cancel, dan extend.
5. **Booking Change Requests** — menerima atau menolak request move/cancel.
6. **Payment/Checkout** — hanya tampil ketika Payment Module aktif.

### Management

1. **Asset Categories** — kategori dan aturan umum asset.
2. **Master Asset** — room, device, furniture, consumable, dan asset lain.
3. **Room Composition** — fasilitas, quantity, included, dan optional/add-on.
4. **Booking Control Center** — move, Force Cancel, request, dan activity log.
5. **Payment Settings** — global toggle dan gateway, sesuai permission.
6. **Room Rate Plans** — hanya tampil ketika Payment Module aktif.
7. **Payment Transactions & Reports** — hanya tampil ketika module aktif.

## 13. Model Data Konseptual

Entitas utama MVP:

- `asset_categories`;
- `assets` sebagai master data umum;
- `room_asset_details` untuk data khusus room;
- `room_operating_hours` dan `asset_blackouts`;
- `room_asset_components` untuk komposisi fasilitas dan quantity;
- `bookings` dan `booking_participants`;
- `booking_assets` untuk asset utama dan component/add-on yang disnapshot;
- `booking_moves`;
- `booking_change_requests`;
- `booking_extension_requests`;
- `booking_activity_logs`;
- konfigurasi global/feature settings untuk Payment Module;
- mapping Booking Authority Level terhadap role.

Entitas kondisional ketika Payment Module aktif:

- `room_rate_plans`;
- `payment_gateways`;
- `payment_transactions`;
- `payment_refunds`;
- snapshot harga dan kebijakan payment pada booking.

Inventory tables seperti stock balances, stock movements, stock reservations,
dan substitution rules berada di luar MVP. Nama tabel dan relasi final diputuskan
pada implementation plan setelah konvensi repository diperiksa lebih dalam.

## 14. Permission Konseptual

### Asset dan Room

- `asset.view`
- `asset.manage`
- `asset.manage_categories`
- `room.manage_details`
- `room.manage_composition`

### Booking

- `booking.view_availability`
- `booking.create`
- `booking.view_own`
- `booking.cancel_own`
- `booking.extend_own`
- `booking.move_lower`
- `booking.force_cancel_lower`
- `booking.request_change`
- `booking.respond_own_change_request`
- `booking.view_activity`

### Payment (Kondisional)

- `payment.toggle_module`
- `payment.manage_rate_plans`
- `payment.manage_gateways`
- `payment.view_transactions`
- `payment.refund`

Backend wajib memeriksa permission, status global Payment Module, serta Booking
Authority Level. Menyembunyikan tombol atau menu bukan pengganti authorization.

## 15. Arah Arsitektur dan Data Flow

Domain yang harus tetap terpisah:

- **Asset Catalog** mengelola kategori, Master Asset, status, metadata, dan mode
  booking.
- **Room Composition** mengelola fasilitas dan quantity yang melekat pada room.
- **Availability Engine** menghitung slot dari jam operasional, buffer, blackout,
  booking aktif, kapasitas, dan asset berserial yang dipilih.
- **Booking Service** memvalidasi dan membuat perubahan booking secara transaksional.
- **Booking Authority Workflow** membandingkan role, menjalankan direct action,
  membuat request, dan menangani approval/expiry.
- **Optional Payment Service** hanya aktif saat global toggle enabled.
- **Notification Service** menerima domain event setelah transaksi berhasil.
- **Audit Service** menyimpan jejak perubahan material secara konsisten.

Tidak ada Inventory Service pada MVP. Room Composition dan quantity tidak boleh
diam-diam dianggap sebagai stock balance.

### 15.1 Data Flow Pencarian dan Booking

1. Pengguna mengirim tanggal, waktu, durasi, kapasitas, dan fasilitas.
2. Availability Engine mengembalikan room/asset serta slot yang memenuhi syarat.
3. Sistem menampilkan Room Composition dan add-on yang dapat dipilih.
4. Jika Payment Module aktif, Payment Service menghasilkan quote.
5. Pengguna mengonfirmasi booking.
6. Booking Service memeriksa ulang permission, overlap, blackout, dan asset.
7. Jika payment aktif, sistem memeriksa quote dan hasil payment sesuai kebijakan.
8. Booking, asset snapshot, composition snapshot, dan activity log disimpan.
9. Setelah commit, event booking memicu notifikasi.

### 15.2 Data Flow Move dan Force Cancel

1. Actor memilih booking target dan memasukkan alasan.
2. Sistem memeriksa permission serta membandingkan authority level.
3. Jika target lebih rendah, direct action dapat dilanjutkan.
4. Jika target sama/lebih tinggi, sistem membuat Booking Change Request.
5. Untuk move, sistem menampilkan target alternatif yang kosong.
6. Pada eksekusi/approval, availability diperiksa ulang dalam transaksi.
7. Jika valid, booking dipindahkan tanpa mengganti identitas booking.
8. Untuk cancel, status booking diubah menjadi `Cancelled` dan slot dilepas.
9. Master Asset, Room Composition, dan quantity fasilitas tidak diubah.
10. Setelah commit, actor dan organizer menerima notifikasi yang sama.

### 15.3 Data Flow Extend

1. Sistem memvalidasi ownership, status, waktu, dan interval extend.
2. Availability Engine memeriksa hingga waktu selesai baru.
3. Jika kosong, booking diperpanjang dalam satu transaksi.
4. Jika konflik, pengguna menerima alternatif.
5. Role berotoritas menggunakan workflow move/cancel sesuai hierarchy.
6. Jika approval diperlukan dan belum selesai saat booking berakhir, request
   kedaluwarsa dan jadwal awal tetap berlaku.

### 15.4 Error Handling

- Konflik request bersamaan menghasilkan respons conflict dan alternatif terbaru.
- Target move yang tidak lagi kosong membatalkan seluruh pemindahan tanpa mengubah
  booking awal.
- Request level sama/lebih tinggi tanpa approval tidak boleh dieksekusi.
- Asset nonaktif, maintenance, atau blackout menghasilkan pesan spesifik.
- Endpoint payment ditolak ketika Payment Module nonaktif.
- Gateway timeout/failure tidak boleh membuat booking berstatus ambigu.
- Retry request menggunakan idempotency key untuk mencegah booking, move, cancel,
  payment, refund, atau notifikasi ganda.
- Kegagalan notifikasi masuk retry queue dan tidak mengubah hasil transaksi domain.

### 15.5 Strategi Pengujian

- **Unit tests**: slot calculation, buffer, blackout, role comparison, state
  transition, composition snapshot, dan conditional payment rules.
- **Feature tests**: asset/category CRUD, room composition, booking lifecycle,
  direct move/cancel, approval request, extend, payment toggle, dan privasi.
- **Concurrency tests**: dua booking pada slot sama, booking vs move, dua approval
  terhadap target sama, dan target move yang diambil sebelum approval.
- **Payment tests**: menu/endpoint disabled, rate plan, gateway callback,
  idempotency, failure, dan refund ketika module aktif.
- **Browser/runtime QA**: asset management, room creation, composition input,
  availability, booking, move/cancel request, responsive layout, dan keyboard.
- **Regression tests**: autentikasi, RBAC, navigasi CMS, dan modul existing yang
  memakai account/permission tetap berjalan.

## 16. Non-Functional Requirements

### Konsistensi dan Concurrency

- Tidak boleh ada dua booking aktif yang overlap pada asset yang sama.
- Pemeriksaan availability dan mutasi booking berada dalam transaksi database
  dengan strategi locking yang sesuai.
- Endpoint mutasi harus idempotent terhadap retry atau double-click.
- Move/cancel tidak boleh mengubah Master Asset atau Room Composition.

### Waktu

- Waktu disimpan dalam UTC dan ditampilkan mengikuti timezone asset/pengguna.
- Perhitungan buffer, blackout, extend, dan request expiry menggunakan waktu server.

### Keamanan dan Privasi

- Pengguna biasa tidak dapat melihat agenda, participant, atau detail privat
  booking milik pengguna lain.
- Semua aksi move, Force Cancel, approval, role comparison, dan payment memiliki
  audit log.
- Payment credential dienkripsi dan disamarkan jika module aktif.
- Input identifier, enum, tanggal, quantity, metadata, dan harga divalidasi server.

### Performa

- Pencarian availability satu hari untuk hingga 100 room/asset memiliki target
  respons p95 maksimal 2 detik pada beban operasional normal.
- Kalender mengambil data berdasarkan rentang tanggal, bukan seluruh histori.

### Aksesibilitas dan Responsivitas

- Alur asset, room composition, pencarian, dan booking dapat digunakan keyboard.
- Status tidak dibedakan hanya dengan warna.
- Layout mendukung desktop, tablet, dan mobile tanpa menyembunyikan informasi
  otoritas, konflik, atau payment penting.

## 17. Acceptance Criteria MVP

1. Administrator dapat membuat kategori asset dan Master Asset.
2. Room dibuat sebagai asset berkategori Room dengan detail khusus room.
3. Room Composition dapat menyimpan 1 meja, 10 kursi, 1 proyektor, dan 10 botol
   air sebagai baris terstruktur dengan quantity dan unit.
4. Quantity fasilitas tidak dianggap sebagai stock balance dan tidak berkurang
   akibat booking/cancellation.
5. Asset dapat dikonfigurasi standalone, component/add-on, keduanya, atau
   non-bookable.
6. Pengguna dapat mencari Hari Ini/Besok/Tanggal dan hanya melihat slot valid.
7. Dua request bersamaan pada asset dan waktu sama hanya menghasilkan satu
   booking terkonfirmasi.
8. Booking menyimpan snapshot asset dan Room Composition.
9. Payment Module default nonaktif; menu/form payment tersembunyi dan endpoint
   mutasi ditolak backend.
10. Ketika Payment Module diaktifkan, Room Rate Plan dan payment gateway dapat
    dikelola oleh pengguna berpermission.
11. Booking lama tidak berubah ketika Payment Module diaktifkan/dinonaktifkan.
12. Super Admin, Admin, Preskom, dan Direktur hanya dapat direct move/Force Cancel
    terhadap booking level lebih rendah.
13. Booking level sama atau lebih tinggi hanya dapat diubah setelah organizer
    menyetujui Booking Change Request.
14. Move hanya berhasil ke target kosong dan mempertahankan identitas booking.
15. Force Cancel mengubah status serta melepaskan slot tanpa mengubah Master Asset,
    Room Composition, atau stock.
16. Extend tanpa konflik memperbarui jadwal secara atomik.
17. Semua move, cancel, request, approval, extend, dan payment dicatat dalam
    activity log sesuai status module.
18. Pengguna tanpa permission ditolak backend meskipun memanggil endpoint langsung.

## 18. KPI Awal

- Persentase booking berhasil tanpa bantuan admin.
- Utilization rate per room dan asset berserial.
- Persentase pencarian yang menghasilkan booking.
- Konflik booking dan extend per 100 booking.
- Jumlah direct move dan Force Cancel per role.
- Jumlah Booking Change Request serta waktu rata-rata respons.
- Cancellation dan no-show rate.
- Persentase room dengan Room Composition lengkap.
- Persentase payment berhasil dan gagal hanya ketika Payment Module aktif.

## 19. Tahapan Rilis

### Phase 1 — Asset & Meeting Room Booking MVP

- kategori dan Master Asset;
- detail room dan Room Composition;
- asset standalone/component/add-on;
- availability Hari Ini/Besok/Tanggal;
- create, cancel, My Bookings, dan extend;
- role hierarchy, direct move/Force Cancel, serta Booking Change Request;
- Payment Module global dengan kondisi default nonaktif;
- conditional Room Rate Plan, gateway management, dan payment flow;
- notifikasi, activity log, dan pengujian runtime.

### Phase 1.1 — Operational Improvements

- check-in manual atau QR;
- no-show berbasis check-in;
- template komposisi room;
- dashboard utilization dan rekomendasi kapasitas;
- inventory stock tracking;
- stock movement dan reservation;
- substitusi consumable/perangkat otomatis.

### Phase 2 — Integrasi dan Resource Lain

- recurring booking;
- calendar integration;
- ERP/accounting integration;
- approval policy tambahan per kategori asset;
- booking kendaraan, venue, atau resource lain dengan aturan khusus;
- pengguna eksternal dan marketplace jika strategi produk membutuhkannya.

## 20. Risiko dan Mitigasi

### Penyalahgunaan Hierarki Role

Mitigasi: permission terpisah, direct action hanya ke level lebih rendah, request
untuk level sama/lebih tinggi, alasan wajib, notifikasi, dan activity log.

### Double Booking Saat Move

Mitigasi: validasi server, transaksi, locking, pengecekan ulang target, dan tes
concurrency.

### Master Asset Berubah Mengubah Histori

Mitigasi: snapshot asset dan Room Composition pada booking. Move/cancel tidak
menulis ke Master Asset.

### Quantity Disalahartikan sebagai Stok

Mitigasi: label UI yang jelas, pemisahan domain Room Composition, dan dokumentasi
bahwa inventory tracking belum aktif pada MVP.

### Payment Toggle Salah Konfigurasi

Mitigasi: default disabled, validasi konfigurasi sebelum enable, permission khusus,
endpoint guard, audit log, dan histori payment tidak dihapus saat disabled.

### Fasilitas Tidak Sesuai Kondisi Aktual

Mitigasi: status asset, maintenance, verifikasi berkala Asset/Room Manager, dan
perubahan manual Room Composition sampai inventory tracking tersedia.

## 21. Keputusan Produk yang Disepakati

- Rilis pertama berfokus pada meeting room dengan fondasi Master Asset generik.
- Room, laptop, proyektor, furniture, consumable, dan item lain dikelola sebagai
  asset berkategori.
- Room memiliki komposisi fasilitas terstruktur beserta quantity dan unit.
- Quantity komposisi belum menjadi inventory stock pada MVP.
- Inventory tracking, stock movement, reservation, dan substitusi otomatis ditunda.
- Payment merupakan modul global opsional dengan kondisi default nonaktif.
- Ketika payment nonaktif, UI dan endpoint payment tidak tersedia untuk booking.
- Ketika payment aktif, Room Rate Plan, gateway, transaksi, dan refund dikelola
  sesuai permission.
- Hierarki role adalah Super Admin, Admin, Presiden Komisaris/Preskom, Direktur,
  lalu role biasa.
- Direct move dan Force Cancel hanya boleh terhadap booking level lebih rendah.
- Booking level sama atau lebih tinggi harus melalui persetujuan organizer.
- Move hanya mengubah alokasi/jadwal booking dan mempertahankan identitas booking.
- Cancel hanya mengubah status serta melepaskan slot; Master Asset dan Room
  Composition tidak berubah.
- Tidak ada pembatalan otomatis hanya karena actor memiliki level role lebih tinggi.
