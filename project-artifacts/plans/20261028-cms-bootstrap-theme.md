# CMS Bootstrap theme parity

Tanggal: 28 Oktober 2026. Persetujuan: permintaan pengguna agar state dropdown dan komponen Bootstrap di CMS mengikuti warna/template CMS.

## Lingkup

Tambahkan satu stylesheet pemetaan token Bootstrap ke token tema Phoenix yang sudah ada. Muat setelah stylesheet tema pada enam layout CMS: Default, Calm Green, Aurora, Prism, Equinox, Lucent. Jangan memuatnya pada auth/frontend atau mengubah Bootstrap vendor, struktur komponen, data, atau warna danger/success/warning/info. Pertahankan perilaku dan layout Manage Article yang sudah disetujui.

Dropdown memakai background focus/active yang berasal dari surface/subtle tema, serta warna teks yang sesuai, sehingga active biru Bootstrap dan teks hijau di atas biru tidak muncul. Primary buttons, checked controls, navigation/pagination/list-group, progress, accordion, primary utilities/alerts dan surface overlay mengikuti token CMS. Pertahankan state disabled, valid/invalid, keyboard focus, dan semantic variants.

## Pekerjaan dan bukti

- [x] Audit memori, graph, source token/layout aktif dan cascade runtime.
- [x] Buat regression browser dengan CSS produksi dan reproduksi active biru sebelum koreksi.
- [x] Backup enam layout; snapshot hash source lama, HEAD/index, dan graph.
- [x] Tambahkan CSS bersama dan enam link pada CMS, tanpa perubahan frontend/auth.
- [x] Uji enam tema pada light/dark dan pergantian accent di harness lokal; semantic danger dan valid/invalid harus tetap benar.
- [x] Verifikasi CMS aktif terautentikasi pada Manage Article dan halaman lain: hover/focus/pressed/active, keyboard, mobile, dan dark mode tanpa penulisan data.
- [x] Jalankan regression tema terkait dan diff check; review batas scope dan laporkan hasil beserta keterbatasan.

## Acuan

Memori C: bagian Lucent semantic buttons dan token warna; drive E: tidak tersedia. Graph menemukan `public/assets/css/phoenix-cms.css`; query path dari layout Lucent tidak menemukan relasi, sehingga include dibuktikan dari source layout. Graph tetap menunggu pembaruan manual.

## Rulings dan hasil

- Beberapa tema mengubah token pada body. Pemetaan dihitung ulang pada root, body, dan nested data-bs-theme agar warna aktual komponen mengikuti scope yang benar.
- Aturan font/background/border dropdown lokal Manage Article dipindahkan ke stylesheet bersama; satu rule duplikat di CSS artikel dihapus setelah backup. Susunan toolbar, detail/checkbox, dan loading dipertahankan.
- Ikon accordion memakai SVG Bootstrap yang sama sebagai mask berwarna currentColor, sehingga warna biru yang tertanam pada ikon mode gelap tidak bocor.
- Selesai: 24 kasus stylesheet terisolasi untuk enam tema; 12 kasus dropdown pada CMS aktif dan empat halaman lain; 10 regresi header/font artikel; 38 kontrak tema/isolation lolos. Satu assertion frontend lama gagal dengan input source/test identik HEAD dan tidak diperbaiki di scope CMS ini.
- Bukti final: project-artifacts/qa/cms-bootstrap-theme-20261028/completion.md.
