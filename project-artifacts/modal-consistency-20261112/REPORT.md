# Penyeragaman modal CMS utama — 12 November 2026

Penyeragaman diterapkan pada checkout lokal `D:\Laragon\www\laravel-13-phoenix`. Acuan: Manage User, Manage Role, dan Manage Permission. Pengguna memilih CMS utama dahulu; editor Page Builder dan File Manager V2 tetap.

## Hasil

- Inventaris source: 44 definisi dialog pada 26 Blade view, terdiri dari 41 dialog Bootstrap dan 3 native dialog. Semua kini memakai `ph-modal-dialog`.
- Sebelum perubahan, 11 dialog Bootstrap belum memakai class standar. Ketiga native dialog juga mempunyai shell tersendiri.
- Sudut 16 px, judul tebal dengan ukuran responsive CMS, header/footer tanpa separator, warna permukaan mengikuti tema, dan tombol sekunder/warna tema diselaraskan.
- SMTP, Category Menu backend/frontend, preview Appearance/Themes, dan Dropdown Parent Menu memakai shell existing. Lebar lg/xl untuk preview tetap.
- Hapus Event, hapus occurrence, dan pembatalan occurrence mengikuti konfirmasi Role/Permission. Nama record, peringatan, handler aksi, dan tombol pembatalan dipertahankan; modal dipindahkan ke body memakai Teleport seperti acuan CMS.
- Konfirmasi Header Navigation, pemilih ikon Workflow, dan konfirmasi discard Template Options memakai shell shared tanpa mengganti lifecycle native dialog.
- Selector shell Template Options dibatasi ke anak langsung agar pengaturan tinggi, scrolling, dan footer panel utama tidak bocor ke konfirmasi discard yang bersarang.
- Ukuran judul dan warna permukaan native dialog cocok dengan modal Manage User pada empat kondisi browser yang diuji.

## Memori dan Graphify

Memori yang dibaca: `E:\AI\Memories\20261103_160100-laravel13-phoenix-css-auth-sidebar-tailwind-handoff.md`, dengan pencarian terarah melalui `E:\AI\Memories\INDEX.md`. Konteks yang digunakan: lokasi CSS shared/CMS, enam tema, isolasi auth, serta batas CMS utama dari editor terpisah. Memori bukan bukti runtime terbaru.

Graph lokal `graphify-out/graph.json` bertanggal 3 November. Query: `CMS modal manage user manage role manage permission shared modal CSS layouts`, `manage_user.blade.php manage_role.blade.php manage_permission.blade.php`, dan `graphify explain awesome_admin_user.blade.php`. Query umum kurang spesifik; explain menemukan node view aktif dengan degree 0. Tidak ada relasi INFERRED/AMBIGUOUS yang dijadikan bukti perubahan. Inventaris dan keputusan diverifikasi kembali terhadap source aktif dan browser. Graph tidak diperbarui, mengikuti instruksi pembaruan manual.

## File dibaca dan diubah

Daftar lengkap 26 view yang diinventarisasi beserta posisi dialog tersimpan di `source-inventory.json`. Baseline dan hasil akhir 17 halaman ada di `before-inventory.json` dan `after-inventory.json`.

Source utama yang dibaca sebagai acuan: `resources/views/awesome_admin/awesome_admin_user.blade.php`, `awesome_admin_role.blade.php`, `awesome_admin_role_v2.blade.php`, `awesome_admin_permission.blade.php`, view Article/Cover Image/Config dan menu lain yang tercantum di inventaris; shared CSS dan bridge CMS; token modal enam tema; layout CMS Lucent; script Event, Workflow, Header Navigation, dan Article Templates untuk memverifikasi handler dan lifecycle. Controller Role dan route Workflow juga diperiksa untuk memastikan view aktif.

15 source yang diubah:

1. `public/assets/css/shared/phoenix-cms.css`
2. `public/assets/css/article/article-template-manager-2026.css`
3. `public/assets/css/header-navigation/awesome-admin-header-navigation-fullscreen.css`
4. `public/assets/css/workflow/workflow-editor.css`
5. `resources/views/awesome_admin/awesome_admin_appearance.blade.php`
6. `resources/views/awesome_admin/awesome_admin_smtp.blade.php`
7. `resources/views/awesome_admin/awesome_admin_themes.blade.php`
8. `resources/views/awesome_admin/header_navigation/awesome_admin_header_navigation.blade.php`
9. `resources/views/awesome_admin/menu/backend/awesome_admin_category_menu_be.blade.php`
10. `resources/views/awesome_admin/menu/frontend/awesome_admin_category_menu_fe.blade.php`
11. `resources/views/awesome_admin/menu/frontend/awesome_admin_parent_menu_fe.blade.php`
12. `resources/views/manage_article/templates/index.blade.php`
13. `resources/views/manage_event/manage_event.blade.php`
14. `resources/views/manage_event/partials/form.blade.php`
15. `resources/views/workflow/editor.blade.php`

Dua tes existing diperbarui untuk mengakui selector direct-child yang menjaga isolasi nested dialog: `tests/article-template-presentation.test.mjs` dan `tests/manage-article-template-manager.test.mjs`.

Perubahan lama pada `public/assets/css/shared/extending-css-bootstrap-5.css` dan dua folder artifact CSS 6 November dipertahankan. Tidak ada perubahan script aplikasi, backend, dependency, database, commit, push, atau deployment.

## Backup

Seluruh 17 file yang diubah dibackup sebelum edit di `project-artifacts/modal-consistency-20261112/backups_20261112_161737`. Backup awal source diverifikasi byte-identik sebelum ditulis. Dua file tes dibackup dan dibandingkan SHA-256 sebelum edit. Daftar file dan SHA-256 backup terdapat dalam `backup-manifest.json`.

## Pengujian terbaru

| Pemeriksaan | Hasil dan cakupan |
| --- | --- |
| `node project-artifacts/modal-consistency-20261112/check-source.mjs` | Lulus: 44 dialog memakai shell shared; ID dan handler konfirmasi Event tetap tersedia. Tes ini gagal pada source awal, lalu lulus setelah perubahan. |
| Inventaris browser sebelum/sesudah | 17 halaman HTTP 200, tidak ada page error; awalnya 11 dialog Bootstrap tidak standar, hasil akhir 0. |
| `visual-check.mjs` | 64/64 lulus: 16 sampel dialog pada viewport 1440×1000 dan 390×844, light/dark. Menguji radius 16 px, judul tebal, separator 0, background tidak transparan, batas horizontal viewport, buka/tutup, serta tombol batal native dialog. Nol page error. Bukti `visual-results.json` dan `screenshots/`. |
| `keyboard-check.mjs` | 5/5 lulus: tombol batal Event, Escape Header Navigation, Escape discard tetap mempertahankan draft dan parent modal, fokus kembali ke opener Template Options, Escape Workflow picker. |
| Tes Node terkait Template Manager, Article Presentation, SMTP responsive | 78/78 lulus. Output `node-tests-final.log`. |
| Tes PHP ArticleTemplateOptionsTest dan ManageEventTemplateTest | 14 lulus, 228 assertion. Output `php-tests.log`. |
| `git diff --check` | Lulus; hanya peringatan normalisasi CRLF Git pada beberapa file. |

Percobaan awal kelompok Node 80 tes menghasilkan 79 lulus, 1 gagal: `tests/manage-event-category-modal.test.mjs` tidak menyediakan `_` untuk `_.debounce` dalam VM sandbox. File tes tersebut dan script Event terkait identik terhadap HEAD (`git diff --exit-code` lulus), sehingga kegagalan fixture ini sudah ada sebelum perubahan modal. Tidak diperluas ke perbaikan fixture yang tidak berkaitan. `node-tests.log` menyimpan hasilnya.

Browser lokal menggunakan Chrome headless dan tema CMS aktif Arunika Lucent. Setelah login, seluruh request POST/PUT/PATCH/DELETE diblokir oleh harness visual/keyboard. Pengujian hanya mengubah draft in-memory lalu membuangnya; tidak mengeksekusi hapus, cancel session, atau submit data aplikasi.

## Batasan

- Seluruh 44 definisi diperiksa statis; uji interaksi dan screenshot mendalam mencakup 16 sampel, bukan masing-masing 44 dialog.
- Enam tema berbagi stylesheet standar, tetapi runtime lintas tema selain Lucent, Firefox, dan perangkat fisik belum diuji.
- `window.confirm`, `window.prompt`, serta dialog `beforeunload` adalah UI milik browser dan tidak diubah pada penyeragaman shell modal aplikasi ini.
- Operasi simpan/hapus/pembatalan data nyata dan produksi belum diuji. Tidak ada deploy atau commit.
- Graphify tetap menunggu perintah manual pengguna.
