# Preview mobile — tekan-tahan untuk mode pilih, 3 November 2026

## Hasil

Preview existing diperbarui di `project-artifacts/previews/manage-article-mobile-20261103/manage-article-mobile.html`. Kedua alternatif, **Judul utama** dan **Kontrol di kiri**, memakai perilaku yang sama:

- Mode biasa menampilkan detail dan menu, tanpa checkbox permanen.
- Tahan item atau judul 700ms untuk masuk mode pilih dan memilih item pertama. Lepas setelah tahan tidak membatalkan pilihan atau membuka detail.
- Pada mode pilih, tampil indikator lingkaran/checklist, warna selected mengikuti accent, jumlah pilihan, tombol X, pilih semua, dan simulasi aksi Ubah status.
- Tap item menambah/membatalkan pilihan. Checkbox native tetap tersedia di balik tampilan lingkaran dengan area klik 44 × 44px. Tidak ada double toggle ketika input diklik.
- Mode pilih tetap aktif pada 0 pilihan; tombol aksi menjadi nonaktif. Tombol X keluar dan membersihkan pilihan. Tap biasa kemudian kembali membuka/menutup detail.
- Tombol Pilih memberikan jalur masuk tanpa gesture untuk mouse/keyboard dan kemudahan menemukan fungsi. Enter/Space pada judul memilih/membatalkan saat mode pilih, Escape keluar. Focus setelah jalur masuk keyboard berpindah ke X, lalu kembali ke Pilih saat keluar.
- Perpindahan pointer lebih dari 10px, scrolling, pointercancel, pergantian fokus, tab tersembunyi, dan interaksi multitouch membatalkan gesture yang masih menunggu. Pointerdown pada kontrol menu/detail, link, input, atau isi panel detail tidak memulai tekan-tahan.
- Mode pilih menutup detail yang terbuka dan menyembunyikan aksi per item. Panel detail normal masih bisa diseleksi teksnya.

## Cakupan dan preservasi

Hanya fragment preview yang diubah. Source Vue/Blade/CSS Manage Article serta graph tidak diubah. Perubahan Appearance/Auth dan shared CSS yang sudah ada di repository dipertahankan. Tidak ada stage, commit, push, install dependency baru, atau update Graphify.

Memori disaring di E dan registry C. Konteks yang dipakai: `MEMORY.md:280–287` mengenai urutan kontrol, preview tanpa mutasi, update Graphify manual, serta semantic palette. Instruksi terbaru pengguna menggantikan kebutuhan checkbox permanen pada mobile. Graphify explain untuk stylesheet Manage Article tetap dipakai sebagai peta lokasi; fitur ini adalah simulasi lokal, bukan perubahan source runtime.

Backup fragment sebelum perubahan dan sebelum koreksi lanjutan berada di `project-artifacts/backups/manage-article-longpress-preview-20261103`. `before-source.json` mencatat hash source, graph, HEAD, serta hash backup awal; `preservation-verification.json` membuktikan semuanya terjaga.

## Verifikasi terbaru

- Chrome: **16 kasus layout** lulus; Firefox: **16 kasus layout** lulus. Kedua alternatif × lebar 320/390/414/720 × appearance light/dark. Tidak ada overflow, teks header tetap terpusat, target selection 44px, checkbox tidak terlihat pada mode biasa.
- Dua kelompok interaksi pada masing-masing browser lulus: tap biasa/detail, tahan singkat yang belum memilih, tahan 700ms, release suppression, pilih beberapa, deselect satu, 0 pilihan tetap aktif, pilih semua, batal semua, X, keyboard Enter/Space/Escape, gerakan/scroll/pointercancel membatalkan gesture, menu tidak memulai seleksi, dan memilih hasil pencarian.
- Chrome memakai input sentuh melalui CDP: **touchStart → tahan → touchEnd** benar-benar menghasilkan pilihan pertama dan release mempertahankannya. Pengujian sentuh tambahan membuktikan touchMove/drag membatalkan seleksi, X membersihkan pilihan, dan tap setelah keluar membuka detail lagi. Ini emulasi browser, bukan perangkat fisik.
- Firefox gesture tahan diuji dengan pointer mouse normal. Menggabungkan emulasi touch dan input mouse Firefox pada probe awal tidak memancarkan pointerdown/mouse yang diperlukan; suite akhir memakai konteks input yang konsisten. Tidak ada klaim long-press pada Firefox Android fisik.
- Error JavaScript 0; request dari preview ke CMS 0; fragment tidak memiliki fetch/XHR/WebSocket. Syntax JS valid; fragment kurang dari 41 KiB.
- Focus issue yang ditemukan saat masuk mode pilih dari keyboard diperbaiki dengan memfokuskan X. Tidak mengubah focus ketika gesture tahan dipakai. Icon checklist dan X dikonfirmasi tersedia setelah rendering sentuh dan diperiksa visualnya.
- Label aksesibel eksplisit disediakan untuk aksi Ubah status agar tetap terbaca saat label visual disembunyikan pada 320px.

Hasil terstruktur: `verification-chrome.json`, `verification-firefox.json`, `touch-verification.json`, `preservation-verification.json`. Screenshot mencakup normal/selected light/dark dan `chrome-touch-confirmed.png`. Script QA berada dalam `project-artifacts/tests/manage-article-longpress-preview-20261103`.

## Batas

Preview menggunakan artikel contoh dan seluruh interaksi lokal. Belum diterapkan ke CMS aktif. Safari, perangkat fisik, dan integrasi Vue/backend untuk gesture ini belum diuji. Graphify menunggu perintah manual. Simpan catatan ini sebagai artifact QC, bukan memori proyek otomatis.
