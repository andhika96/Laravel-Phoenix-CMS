# Penyeragaman modal CMS

Tujuan: memakai modal Manage User/Role/Permission sebagai acuan CMS utama. Page Builder dan File Manager V2 dikecualikan sesuai jawaban pengguna.

Pendekatan: pakai `ph-modal-dialog` existing, bukan membuat sistem modal baru. Semua dialog Bootstrap CMS harus memakai class tersebut dan tetap centered. Konfirmasi hapus Event mengikuti layout konfirmasi Role/Permission. Header Navigation dan Workflow tetap memakai native dialog tetapi shell mengikuti aturan shared yang sama. Ukuran preview dan panel khusus dipertahankan. Dialog browser `confirm`, `prompt`, dan peringatan saat menutup tab adalah UI browser, di luar shell modal aplikasi pada perubahan visual ini.

Memori: `E:\AI\Memories\20261103_160100-laravel13-phoenix-css-auth-sidebar-tailwind-handoff.md`. Graphify: query CMS modal dan explain awesome_admin_user.blade.php; graph 3 November dipakai sebagai peta, source aktif diverifikasi langsung. Graph tidak diperbarui sesuai instruksi manual.

- [x] Inventaris source aktif dan baseline runtime, termasuk dialog khusus.
- [x] Backup seluruh source yang disentuh ke folder backups bertimestamp; pertahankan perubahan CSS lama.
- [x] Lengkapi class modal standar, konfirmasi Event, dan shell native dialog; hapus konflik border shell Template Options.
- [x] Uji browser desktop/mobile dan light/dark, uji buka/tutup tanpa submit/hapus data, serta regresi yang sesuai.
- [x] Tinjau diff dan tulis laporan dengan cakupan, bukti, dan keterbatasan.

Tidak ada perubahan database, dependency, endpoint, aturan submit, atau pembaruan Graphify otomatis.
