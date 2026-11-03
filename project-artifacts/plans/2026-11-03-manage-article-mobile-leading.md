# Manage Article mobile · Kontrol di kiri

Implementasi lokal dari varian preview yang telah dipilih. Blade dan CSS memakai baris tabel yang sama untuk menempatkan chevron di kiri serta status, tanggal, dan menu di bawah judul. Tiga tombol halaman tetap memakai komponen CMS dengan konten yang sejajar vertikal.

Vue menggunakan breakpoint daftar yang sudah ada, satu daftar ID terpilih, dan form status massal yang sama. Tekan-tahan 700 ms membuka mode pilih, tap menukar pilihan, dan tombol X atau Escape menutupnya. Pilihan berlaku untuk halaman aktif. Pergantian ukuran memindahkan kontrol yang terlihat dan mempertahankan ID terpilih.

- [x] Rekam baseline browser dan backup tiga file.
- [x] Tambahkan mode pilih dan kontrol kiri di Blade, CSS, dan Vue. Pertahankan aksi desktop dan spinner di isi tabel.
- [x] Uji di CMS lokal dengan Chrome dan Firefox. Cegah permintaan tulis artikel selama QA.
- [x] Simpan hasil dan handoff ke memori drive E dan C.
