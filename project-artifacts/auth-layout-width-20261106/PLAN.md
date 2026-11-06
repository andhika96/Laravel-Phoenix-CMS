# Opsi lebar layout Ocean Split dan Verdant Split

Persetujuan pengguna: implementasikan rekomendasi Terbatas/Penuh adaptif pada kedua tema. Default tetap ukuran saat ini; field form tetap dibatasi agar tidak melebar pada layar besar. Layout berisi urutan lebar layout, card width (Ocean, hanya Terbatas), posisi panel dan proporsi (Ocean). Nilai card width disimpan ketika tersembunyi. Perilaku tablet/mobile yang sudah ada dipertahankan.

Scope source: AuthThemeCatalog (schema/default/validasi/conditional), Vue Appearance (kelompok Layout dan label pilihan), shared modern Blade (data attribute), CSS modern (lebar penuh untuk dua tema). Mekanisme Save existing dipakai tanpa endpoint, migrasi, dependency atau penggantian tema aktif. Thumbnail tetap mewakili default terbatas yang tidak berubah.

Urutan: backup empat source; uji default/validasi/Save/render baru sebelum implementasi; perubahan sempit; jalankan uji tersebut serta tes render existing; QA browser form setting dan responsive pada 320/390/820/1440/1920/3840px; pastikan snapshot pengaturan tersimpan tetap sama; arsipkan URL preview sementara; laporkan hasil. Graphify tidak diupdate sesuai instruksi manual.
