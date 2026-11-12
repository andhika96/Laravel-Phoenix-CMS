# Audit extending-css-bootstrap-5.css — 6 November 2026

Scope: review kelengkapan dan kebenaran utility pada `public/assets/css/shared/extending-css-bootstrap-5.css`; tidak mengimplementasikan perbaikan, menggabungkan file, mengubah staging atau konfigurasi aplikasi.

## Konteks dan cara pemeriksaan

- Memori utama `E:\AI\Memories` diperiksa tetapi tidak tersedia. Laporan penataan CSS pada `project-artifacts/css-organization-20261030/report.md` dibaca sebagai konteks; source aktif tetap menjadi acuan.
- Branch saat audit: main. Target CSS tidak memiliki perubahan lokal terhadap HEAD ketika pemeriksaan selesai.
- Graphify explain `extending-css-bootstrap-5.css` menunjukkan8 pemanggil EXTRACTED. Source pencarian mengonfirmasi8 layout: CMS default/calm_green, frontend calm_green/Aurora/Equinox/Prism/Lucent, dan Setup. CMS Lucent tidak memuat file ini secara langsung. Tidak memakai relasi INFERRED/AMBIGUOUS.
- Perbandingan dilakukan terhadap **Bootstrap5.3.6_custom yang benar-benar dimuat layout**, termasuk isi `bootstrap.css` dan `bootstrap.min.css`; versi npm5.3.8 tidak dijadikan pengganti runtime.
- PostCSS terpasang dipakai untuk membaca seluruh6.423 baris/69.805byte:1.212 blok aturan,1.412 entri selector termasuk selector yang dikelompokkan,9 media breakpoint. Inventaris seluruh aturan/deklarasi/baris ada di `inventory.json`.
- Chrome headless lokal memuat kedua stylesheet dari disk dalam fixture terisolasi, tanpa login/request aplikasi/penyimpanan data. CSS.supports memeriksa seluruh deklarasi;7 kasus computed style terdiri dari5 reproduksi masalah dan2 kontrol yang berfungsi. Hasil di `browser-findings.json`.
- Source lain yang ditelusuri: partial/layout pemanggil, pemakaian class pada Blade/Vue, stylesheet shared/tema, Article, auth modern, dan contoh inline min-width0 pada Role v2. Tidak memindai backup/generated/vendor/node_modules sebagai source aplikasi; PostCSS/Playwright yang sudah terpasang hanya dipakai menjalankan audit.

## Temuan yang perlu dibenahi

1. **70 class end memiliki nilai salah.** `.end-40` sampai `.end-10` masing-masing memakai right45%, berulang pada base dan sembilan breakpoint. Contoh baris163 dan1553. Dalam parent1000px, `.end-25` menghasilkan450px, seharusnya250px. `.end-md-10` menghasilkan450px, seharusnya100px. Perbaikan minimum: nilai sesuai angka class, lalu cek pemanggil yang mungkin mengandalkan perilaku lama. Tidak menyatakan70 class ini sedang dipakai semua halaman.
2. **10 ukuran negatif tidak valid.** `.wn-1`–`.wn-5` dan `.hn-1`–`.hn-5` (baris413–485) memakai width/height negatif. Seluruh10 deklarasi ditolak CSS.supports; parser stylesheet tidak menganggap ini kesalahan struktur tetapi browser mengabaikan nilainya. Tujuan class perlu ditetapkan sebelum dibuang/diganti; pengurangan ukuran biasanya memerlukan calc terhadap suatu ukuran, bukan width negatif. Pencarian literal Blade/Vue yang ditelusuri tidak menemukan pengguna keluarga ini; class dinamis/data database belum diaudit sehingga tidak dinyatakan aman dihapus secara global.
3. **Utility responsive tidak konsisten dengan !important Bootstrap.** Ada27 aturan position responsive,32 opacity responsive,64 viewport responsive yang tidak memakai important. Kasus terbukti pada viewport1000×800: `position-relative position-md-fixed` tetap relative; `opacity-100 opacity-md-25` tetap1; `vh-100 vh-md-50` tetap800px, seharusnya400px. Utility tersebut bekerja sendiri (kontrol opacity-md-25 =0.25). Perbaikan minimum: samakan kebijakan prioritas utility dengan Bootstrap untuk keluarga terkait, bukan menambah important ke semua CSS komponen.
4. **Duplikasi yang dapat dikurangi.** Lima blok identik berulang: text-tiny/small/big/huge/text-shadow-none, mulai baris3 dan223. Sembilan nama utility base juga sudah tersedia pada Bootstrap runtime: empat opacity, dua overflow-hidden dan tiga vertical-align. Perlu verifikasi deklarasi/consumer sebelum menghapus. Pengulangan `.mx-*`/`.my-*` untuk mengisi dua sisi adalah aturan saling melengkapi, bukan bug duplikasi.

## Kelengkapan utility yang belum ada

### Prioritas setelah perbaikan dasar

- **Opacity responsive lengkap:** nilai50 belum ada pada md sampai6xl; seluruh opacity-sm belum ada. Base opacity50 sudah disediakan Bootstrap, sehingga tidak perlu diduplikasi.
- **Viewport di sm:** vw-sm-25/50/75/100 dan vh-sm-25/50/75/100 belum ada; md sampai6xl sudah memiliki keluarganya.
- **Tinggi persentase responsive:** h-sm/md/...-25/50/75/100/auto belum tersedia, sedangkan width responsive auto/0/25/50/75/100 sudah ada dan terbukti bekerja. Jangan menambah ulang w-md-50 atau w-sm-100.
- **Reset posisi dan offset responsive:** position-*-static dan varian start/end untuk0/50/100/auto belum lengkap. Top/bottom responsive juga belum ada. Perlu menentukan daftar nilai yang dipakai, tidak otomatis membuat seluruh kombinasi tanpa kebutuhan.
- **Batas minimum flex/grid:** min-w-0 dan min-h-0 belum tersedia pada extension/Bootstrap yang dibandingkan. Kasus kebutuhan nyata min-width0 sudah ada di `awesome_admin_role_v2.blade.php:48`; pola yang sama ada pada stylesheet Article dan auth. Ini kandidat helper kecil untuk pemakaian berulang, bukan alasan mengganti semua CSS modul.

### Pelengkap berdasarkan kebutuhan

- Margin/gap6–10 dapat melengkapi padding6–10 jika desain memakai jarak tersebut; saat ini m-6 dan gap-6 tidak tersedia pada kedua stylesheet.
- Line-clamp2/3 untuk ringkasan beberapa baris; text-truncate satu baris sudah ada pada Bootstrap.
- Helper tinggi viewport dinamis, misalnya min-dvh-100/dvh-100, untuk kasus layar mobile; CSS auth saat ini sudah memakai100dvh langsung sehingga helper tidak diperlukan untuk memperbaiki auth yang sudah bekerja.
- Cursor pointer/grab/grabbing hanya jika ada kebutuhan berulang; perilaku tombol/akses keyboard tetap mengikuti elemen semantik.
- Daftar isi, penanda START/END perkeluarga, dokumentasi skala dan matriks breakpoint. Header saat ini satu komentar umum; font-size-normal14px dan w-3=1rem perlu didokumentasikan karena nama angka width rem berbeda dari width persentase Bootstrap.

Breakpoint custom1920/2160/2560/3840 **sudah cocok** dengan Bootstrap custom aktual. Ini bukan temuan kesalahan dan tidak diusulkan diubah hanya untuk mengikuti Bootstrap distribusi standar.

## Yang sudah disediakan Bootstrap

Tidak perlu menduplikasi d-flex, d-grid, flex/gap responsive, text-truncate, text-wrap, text-break, object-fit-cover, overflow-auto/overflow-x-auto/overflow-y-auto, opacity base, sizing base dan helper aksesibilitas Bootstrap yang tersedia. Konfirmasi dilakukan pada stylesheet runtime; bukan asumsi dari dokumentasi saja.

## Rekomendasi urutan

Benahi nilai end, tentukan nasib ukuran negatif, dan selaraskan prioritas responsive terlebih dahulu. Setelah itu lengkapi keluarga yang sudah ada, tambahkan helper kecil yang memang dipakai, lalu bersihkan duplikasi serta dokumentasi. Untuk banyak varian berulang, Bootstrap Utilities API/Sass dapat menghasilkan aturan dari satu definisi agar salah salin tidak berulang. Ini rekomendasi untuk implementasi berikutnya, belum diterapkan.

Penambahan class baru hanya berpengaruh pada layout yang memuat file ini. Keputusan memperluas pemuatannya ke CMS lain memerlukan pemeriksaan scope/cascade terpisah; tidak dimasukkan sebagai perubahan otomatis dalam audit.

## Referensi primer

- https://getbootstrap.com/docs/5.3/utilities/api/ — responsive generator dan important utility.
- https://getbootstrap.com/docs/5.3/utilities/spacing/ — skala spacing dan margin negatif.
- https://getbootstrap.com/docs/5.3/utilities/sizing/ — ukuran bawaan.
- https://getbootstrap.com/docs/5.3/helpers/text-truncation/ — helper satu baris.
- https://getbootstrap.com/docs/5.3/utilities/overflow/ — overflow bawaan.
- https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Properties/width — nilai lebar nonnegatif.

Tidak ada source aplikasi yang dimodifikasi dan tidak diperlukan backup source untuk audit read-only ini. Script audit/hasil pemeriksaan disimpan di folder project-artifacts ini; revisi script memiliki backup lokal. Graphify tidak diperbarui. Belum dilakukan regresi seluruh halaman CMS/frontend, browser lain, penggunaan class dinamis dari database, RTL atau perangkat fisik. Temuan browser adalah reproduksi kontrak utility dalam fixture terisolasi, bukan klaim semua halaman CMS saat ini rusak.
