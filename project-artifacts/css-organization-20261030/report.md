# Penataan folder CSS Laravel 13 Phoenix — 30 Oktober 2026

Selesai secara lokal. Enam belas CSS yang semula berada di root public/assets/css telah dikelompokkan menurut fungsi. Semua 32 CSS aktif mempertahankan isi byte identik. Seluruh consumer aktif yang ditemukan diperbarui ke lokasi baru.

## Struktur akhir

```text
public/assets/css/
├─ shared/                  CSS dasar, tambahan Bootstrap, typography bersama
├─ cms/                     penghubung tema Bootstrap CMS
├─ article/                 CSS artikel yang sudah terpisah
├─ frontend/                menu frontend
├─ header-navigation/       pengaturan Header Navigation
├─ pagebuilder/
│  ├─ legacy/               Page Builder lama
│  ├─ elementor/            editor/frontend Elementor awal
│  ├─ elementor-v23/        editor/frontend Elementor v2.3
│  └─ elementor-v24/        editor/frontend Elementor v2.4
├─ setup/                   stylesheet instalasi
├─ themes/                  masing-masing tema dan mobile
└─ workflow/                daftar/editor Workflow
```

## Relokasi CSS aktif

| Lokasi lama relatif terhadap public/assets/css | Lokasi baru |
|---|---|
| `phoenix-cms.css` | `shared/phoenix-cms.css` |
| `extending-css-bootstrap-5.css` | `shared/extending-css-bootstrap-5.css` |
| `theme-responsive-typography.css` | `shared/theme-responsive-typography.css` |
| `cms-bootstrap-theme.css` | `cms/cms-bootstrap-theme.css` |
| `awesome-admin-header-navigation.css` | `header-navigation/awesome-admin-header-navigation.css` |
| `awesome-admin-header-navigation-fullscreen.css` | `header-navigation/awesome-admin-header-navigation-fullscreen.css` |
| `frontend-menu-dropdown.css` | `frontend/frontend-menu-dropdown.css` |
| `workflow-editor.css` | `workflow/workflow-editor.css` |
| `aruna-v4.css` | `setup/aruna-v4.css` |
| `pagebuilder.css` | `pagebuilder/legacy/pagebuilder.css` |
| `pagebuilder_elementor.css` | `pagebuilder/elementor/pagebuilder_elementor.css` |
| `frontend_elementor.css` | `pagebuilder/elementor/frontend_elementor.css` |
| `pagebuilder_elementor_v23.css` | `pagebuilder/elementor-v23/pagebuilder_elementor_v23.css` |
| `frontend_elementor_v23.css` | `pagebuilder/elementor-v23/frontend_elementor_v23.css` |
| `pagebuilder_elementor_v24.css` | `pagebuilder/elementor-v24/pagebuilder_elementor_v24.css` |
| `frontend_elementor_v24.css` | `pagebuilder/elementor-v24/frontend_elementor_v24.css` |

## File pendamping

- 18 file .bak lama dipindahkan ke `project-artifacts/css-organization-20261030/historical-css-backups/`; file tidak dihapus permanen atau dipakai sebagai source analisis.
- base_color.txt dan base_color_from_heroui_theme.txt dipindahkan ke `project-artifacts/references/css-color-notes-20261030/`.
- Folder article dan themes dipertahankan. File Manager V2 tetap pada struktur source/build yang sudah ada.
- Root public/assets/css kini hanya berisi sembilan folder; tidak ada CSS, catatan TXT, atau .bak yang lepas di root.

## Consumer dan batas perubahan

- Path diperbarui pada 35 view aktif dan 53 file test/helper. Daftar lengkap: updated-consumers.csv.
- Pembaruan mencakup asset/url, public_path/filemtime, pembacaan file pada test, serta assertion path isolasi versi Page Builder.
- Seluruh URL aset di dalam 16 CSS diperiksa; tidak ada URL relatif yang perlu direbase. Payload CSS tidak diubah.
- Tidak ada referensi aktif ke path flat lama yang ditemukan setelah perubahan pada scope source yang diperiksa. Database 272 kolom teks/JSON diperiksa dalam transaksi READ ONLY; nol referensi path lama yang perlu dimigrasikan.
- Spasi/tab sisa di akhir 13 file hanya dirapikan pada baris URL CSS yang baru diperbarui, agar git diff --check lulus.

## Backup dan pemulihan

- Backup utama: `project-artifacts/css-organization-20261030/backups_20261030_142705`.
- 127 file asli dicadangkan sebelum relokasi/perubahan consumer; hash setiap backup cocok.
- Manifest pemulihan: recovery-manifest.json. Berisi lokasi asal/tujuan, hash, backup, consumer, dan hash tiga file perubahan lama user.
- Snapshot perubahan lama user: pre-existing-working-tree.patch. Tidak melakukan reset, checkout, stash, atau stage.
- Backup sebelum merapikan spasi URL: `project-artifacts/css-organization-20261030/backups_20261030_142953_reference-whitespace`.
- Recovery dapat dilakukan per file dengan lokasi asal di manifest dan salinan asli di backup utama; jangan menimpa perubahan baru tanpa membandingkan source aktif.

## Verifikasi

- 32/32 CSS aktif tetap identik berdasarkan SHA-256; 16/16 CSS hasil relokasi juga teruji melalui HTTP dengan isi sesuai hash asli.
- Node baseline dan sesudah perubahan: 256 lulus, 0 gagal, dari 40 file test terarah.
- PHP asset isolation/baseline Page Builder v2.4: 5 lulus, 8.619 assertion, sebelum dan sesudah perubahan.
- Pemeriksaan syntax PHP: 59 file lulus.
- git diff --check: lulus; Git masih memberi peringatan normalisasi line ending pada beberapa file, tanpa whitespace error.
- Browser: 21 kasus pada 18 URL, seluruh halaman dan CSS lokal yang diperiksa berstatus HTTP 200. Tidak ada link CSS flat lama yang terdeteksi.
- Screenshot login, Dashboard, dan Page Builder v2.4 disimpan di folder task ini. Login dan Dashboard diperiksa visual setelah relokasi.
- Tiga file perubahan lama user tetap cocok hash-nya: manage-article-2026.css, vueV3-manage-article-2026.js, dan manage_article.blade.php.

| Halaman | Lebar viewport | HTTP |
|---|---:|---:|
| `/auth/login` | 1440 | 200 |
| `/auth/login` | 390 | 200 |
| `/auth/signup` | 1440 | 200 |
| `/auth/forgotpassword` | 1440 | 200 |
| `/dashboard` | 1440 | 200 |
| `/dashboard` | 390 | 200 |
| `/manage_article` | 1440 | 200 |
| `/manage_article/add` | 1440 | 200 |
| `/manage_article/templates` | 1440 | 200 |
| `/awesome_admin/header-navigation` | 1440 | 200 |
| `/workflow` | 1440 | 200 |
| `/workflow/create` | 1440 | 200 |
| `/home` | 1440 | 200 |
| `/article` | 1440 | 200 |
| `/setup` | 1440 | 200 |
| `/pagebuilder` | 1440 | 200 |
| `/pagebuilder-elementor/create` | 1440 | 200 |
| `/pagebuilder-elementor/v2.3/create` | 1440 | 200 |
| `/pagebuilder-elementor/v2.4/create` | 1440 | 200 |
| `/pagebuilder-elementor/v2.4/create` | 390 | 200 |
| `/admin/file-manager-v2` | 1440 | 200 |

## Memori dan Graphify

- Memori digunakan: E:/AI/Memories/20260905-laravel13-phoenix-css-archive-and-equinox-mobile-handoff.md; E:/AI/Memories/20260825-laravel13-phoenix-public-resources-views-cleanup-handoff.md.
- Graphify explain dipakai pada phoenix-cms.css dan pagebuilder_elementor_v24.css untuk menemukan layout dan test pemanggil. Relasi references_stylesheet EXTRACTED kemudian diperiksa terhadap source aktual.
- Metadata hash seluruh 16 CSS sesuai sebelum relokasi. Graphify tidak diperbarui karena pengguna meminta update manual; graph kini masih menunjuk sebagian path lama. Gunakan manifest relokasi ini sampai update manual dilakukan.

## Batas verifikasi

- Ini penataan file dan path. Isi bundle auth/CMS, kesalahan sintaks CSS lama, pemuatan Vue Select/Datepicker, dan cache versioning yang dibahas pada audit sebelumnya tidak diubah.
- Runtime diuji pada browser lokal dan viewport sampel, bukan seluruh browser/perangkat/role/keadaan interaksi atau production.
- Tidak menjalankan seluruh suite aplikasi atau build ulang. Pemeriksaan difokuskan pada consumer aset dan halaman yang memuat kelompok CSS yang dipindahkan.
- Tidak ada staging, commit, push, atau deployment. Pemindahan dan perubahan consumer perlu dibawa bersama jika nanti di-deploy.

## Bukti utama

- relocations.csv / recovery-manifest.json: asal, tujuan, kategori, dan hash file.
- updated-consumers.csv: 88 file consumer/test yang diperbarui.
- final-node-tests.tap / final-php-assets.xml / php-syntax-check.json: hasil pengujian.
- browser-auth-verified.json / browser-pages-verified.json / browser-new-css-verified.json: bukti runtime yang berhasil.
- verification-summary.json: ringkasan verifikasi.
