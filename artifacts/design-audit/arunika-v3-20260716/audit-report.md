# Audit visual Arunika v3 terhadap desain referensi

Tanggal audit: 2026-07-16  
Halaman uji: `/manage_article`  
Viewport pembanding: `852 x 693`  
Scope: shell tema (sidebar, header, jarak konten, serta menu profil), bukan kesamaan data halaman Automations dengan Manage Article.

## Kesimpulan

Arunika v3 belum sesuai secara visual dengan desain referensi. Struktur dasar dan permukaan canvas sudah mendekati, tetapi proporsi sidebar, posisi tombol collapse, ritme header-konten, ukuran pencarian, dan komposisi menu kanan header masih berbeda nyata.

## Bukti

- Referensi: `00-reference-dashboard.png`
- Tema expanded: `../../../../output/playwright/arunika-v3-audit/01-expanded-reference-size.png`
- Tema collapsed: `../../../../output/playwright/arunika-v3-audit/02-collapsed-reference-size.png`
- Menu profil terbuka: `../../../../output/playwright/arunika-v3-audit/03-profile-menu-open.png`

## Pengukuran tema saat ini

| Elemen | Nilai aktual pada 852 x 693 |
| --- | ---: |
| Sidebar expanded | 256 px |
| Sidebar collapsed | 76 px |
| Canvas kanan | x 256 px, lebar 588 px |
| Header | tinggi 60 px, padding horizontal 14 px, gap 14 px |
| Pencarian | 320 x 36 px |
| Tombol collapse | x 210 px, y 14 px, 32 x 32 px; masih berada di sidebar |
| Area aksi kanan | 161 x 46 px; gap 4 px |
| Profil | 42 x 46 px; metadata nama/peran `display: none` |
| Area scroll konten | padding 10 px 8 px 18 px |
| Card konten pertama | padding 16 px |

## Temuan prioritas

### P1 — Proporsi shell desktop tidak sama

Sidebar referensi kira-kira 159 px pada gambar 852 px, sedangkan implementasi memakai 256 px. Selisih sekitar 97 px menggeser seluruh header dan konten ke kanan serta membuat area kerja jauh lebih sempit.

### P1 — Tombol collapse berada pada struktur yang salah

Tombol collapse masih menjadi anak header sidebar dan berada di x 210 px. Sesuai arahan desain, tombol perlu menjadi bagian dari grup kiri header, tepat sebelum kolom pencarian, dengan pemisah vertikal di antara tombol dan pencarian. Posisi harus tetap stabil ketika sidebar berubah expanded/collapsed.

### P1 — Komposisi kanan header tidak mengikuti referensi

Implementasi menampilkan palette, dark mode, notifikasi, dan avatar kecil. Referensi menonjolkan notifikasi lalu profil lengkap berupa avatar, nama, peran, dan affordance dropdown. Pada viewport uji, nama dan peran implementasi disembunyikan sehingga hierarki dan identitas pengguna hilang.

Rekomendasi: pertahankan fungsi palette dan dark mode tetapi pindahkan ke dropdown profil atau menu overflow yang lebih tenang; tampilkan bell dan profil lengkap sebagai grup utama.

### P2 — Header dan konten terlalu renggang

Header implementasi setinggi 60 px dan konten pertama mulai pada y 78 px. Referensi memulai area konten sekitar y 61 px dengan header yang lebih padat, sehingga implementasi terasa lebih turun dan boros ruang. Padding card 16 px juga lebih longgar dibanding kepadatan referensi yang kira-kira 8–10 px.

### P2 — Pencarian terlalu dominan

Pencarian implementasi selebar 320 px, sedangkan referensi sekitar 220 px pada viewport yang sama. Lebar saat ini menyulitkan komposisi header kanan dan memperbesar ruang kosong yang tidak terstruktur.

### P2 — Sidebar terlalu lapang

Selain lebar 256 px, jarak dan ukuran identitas/logo membuat menu terlihat lebih renggang dibanding referensi. Referensi menggunakan ritme menu yang lebih kompak dan proporsional terhadap canvas.

### P3 — Dropdown profil cukup baik, tetapi trigger lemah

Dropdown Profile/Settings/Logout bersih dan dapat digunakan. Masalah utamanya adalah trigger hanya berupa avatar/logo tanpa nama/peran, sehingga konteks akun kurang terlihat. Dropdown juga menutupi sebagian kontrol halaman saat terbuka; ini normal untuk overlay, tetapi perlu diverifikasi kembali setelah tinggi header dipadatkan.

## Bagian yang sudah mendekati

- Gutter luar kanan/bawah sekitar 8 px dan radius canvas kanan sekitar 12 px sudah dekat dengan referensi.
- Warna permukaan sidebar terang sudah berada di arah yang tepat.
- Collapse/expand berfungsi.
- State menu aktif dan pemisahan sidebar dengan canvas tetap terbaca.
- Struktur konten Laravel tetap dinamis; perbedaan isi Automations dan Manage Article bukan dianggap cacat tema.

## Catatan aksesibilitas

- Tombol collapse, theme, notifikasi, dan profil memiliki nama aksesibel pada DOM.
- Kontrol icon-only masih perlu diuji ukuran target sentuh, fokus keyboard, dan kontras fokus setelah komposisi header diubah.
- Audit ini belum menjalankan pengujian keyboard end-to-end atau pembaca layar, jadi tidak menyatakan kepatuhan WCAG penuh.

## Urutan perbaikan yang direkomendasikan

1. Pindahkan tombol collapse ke grup kiri header dan tambahkan divider vertikal.
2. Kalibrasi ulang lebar sidebar desktop terhadap referensi.
3. Susun ulang kanan header menjadi notifikasi + profil lengkap; pindahkan aksi sekunder ke dropdown/overflow.
4. Padatkan tinggi/padding header, jarak header-konten, dan padding card shell.
5. Sesuaikan lebar pencarian dan lakukan regresi expanded/collapsed pada 852 x 693 serta 1440 x 900.
