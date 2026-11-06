# Manage Event mengikuti tabel Manage Article

Scope: pakai stylesheet Article yang sama dengan cakupan selector diperluas untuk Event; tidak mengubah perilaku Article. Toolbar status/search/filter/sort, kategori dan visibility, detail baris, checkbox/bulk status existing, mode pilih mobile/long press, footer range/per-page/pagination, loading/empty/error/retry mengikuti Article. Kolom Event menampilkan judul+kategori, Sessions, Status dan Next occurrence. Form/kategori/pendaftaran/editor event tetap memakai implementasi existing.

Perubahan backend hanya listData agar sort/per_page/status_counts mendukung kontrol tabel; whitelist input dan urutan stabil. Bulk memakai endpoint event existing dan confirmation modal. Tidak menyimpan/menghapus event saat QA browser. Uji backend memakai database SQLite in-memory seperti tes Event yang sudah ada. Semua backup, test dan QC ada di folder ini. Graphify tetap manual.
