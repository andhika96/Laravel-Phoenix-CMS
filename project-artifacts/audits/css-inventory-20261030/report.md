# Inventaris CSS Laravel 13 Phoenix — 30 Oktober 2026

Audit file-level, baca-saja terhadap source aktif dan sampel browser lokal. Source aplikasi tidak diubah, dipindah, dihapus, di-build ulang, di-commit, atau di-push.

## Ringkasan

- 173 CSS lokal diinventarisasi; mencakup custom public, plugin/vendor publik, source modul, source build, dan font storage.
- 43 CSS lokal teramati dengan HTTP 200; 28 source stylesheet modul teramati sebagai style inline.
- 32 CSS custom: 21 teramati runtime, 11 direferensikan kondisional oleh tema alternatif atau setup.
- 1 kandidat custom beta: `public/assets/beta_css/new.css`, tanpa referensi source dan nol hit pada 272 kolom teks/JSON database.
- 66 varian distribusi plugin tanpa pemanggil aplikasi yang ditemukan; status kandidat, bukan bukti aman dihapus.
- 9 CSS CKFinder dipertahankan sebagai dependensi/alternatif skin dinamis; tidak diklasifikasikan sebagai unused.
- 11 CSS contoh/docs/test plugin dipisahkan dari stylesheet CMS; 2 CSS source/distribusi Spectrum dipertahankan sebagai companion build.
- `resources/css/app.css` kosong (0 byte), terdaftar sebagai input Vite; tidak ditemukan consumer @vite pada view aktif.
- 7 import CSS paket npm dan 1 SCSS responsive File Manager ditelusuri secara terarah, di luar hitungan 173 file lokal.

## Metode dan batas

Urutan pemeriksaan: memori E: → query/explain Graphify → source aktual → browser → pemeriksaan hash. Komentar Blade/HTML/blok kode dikeluarkan dari pencocokan. Jumlah referensi adalah occurrence, termasuk filemtime; bukan jumlah stylesheet yang dimuat.
Knowledge graph digunakan sebagai peta saja; query `css cms layout theme article` dan explain node layout Equinox/Prism menunjukkan relasi references_stylesheet EXTRACTED. Sebagian metadata graph stale; lihat graph-freshness.json. Graph tidak diperbarui sesuai instruksi manual pengguna. Tidak ada relasi INFERRED/AMBIGUOUS yang dijadikan dasar status akhir.
Scanner mengecualikan backup, .bak, arsip historis, project-artifacts, cache/log, generated rendition, node_modules penuh, vendor PHP, mockup/prototype, dan duplikasi junction public/storage. public/assets/vendor tetap diperiksa. Font dihitung sekali dari storage/app/public/fonts.
Belum dilakukan audit selector/property yang mati, semua kombinasi widget/data, semua tema melalui runtime, akses URL eksternal langsung, atau pemeriksaan seluruh histori access log. Status CORS responseStatus=0 pada CSS CDN tidak dianggap HTTP gagal ataupun bukti HTTP 200.

## CSS custom (32)

| File | Status | Bukti source |
|---|---|---|
| `public/assets/css/article/article-editor-content.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/article/detail.blade.php:20; resources/views/article/detail.blade.php:20; resources/views/manage_article/manage_article_add.blade.php:267; resources/views/manage_article/manage_article_add.blade.php:267; resources/views/manage_article/manage_article_edit.blade.php:267; resources/views/manage_article/manage_article_edit.blade.php:267; resources/views/manage_article/templates/preview.blade.php:13; resources/views/manage_article/templates/preview.blade.php:13 |
| `public/assets/css/article/article-frontend-2026.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/article/archive.blade.php:36; resources/views/article/detail.blade.php:19; resources/views/article/password-protected.blade.php:41; resources/views/article/private.blade.php:27; resources/views/manage_article/templates/preview.blade.php:12 |
| `public/assets/css/article/article-template-manager-2026.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/manage_article/templates/index.blade.php:197 |
| `public/assets/css/article/manage-article-2026.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/manage_article/manage_article.blade.php:8; resources/views/manage_article/manage_article.blade.php:8 |
| `public/assets/css/aruna-v4.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/setup/setup.blade.php:20 |
| `public/assets/css/awesome-admin-header-navigation-fullscreen.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/awesome_admin/header_navigation/awesome_admin_header_navigation.blade.php:10 |
| `public/assets/css/awesome-admin-header-navigation.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/awesome_admin/header_navigation/awesome_admin_header_navigation.blade.php:9 |
| `public/assets/css/cms-bootstrap-theme.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/themes/arunika_aurora/cms/cms_layout.blade.php:52; resources/views/themes/arunika_aurora/cms/cms_layout.blade.php:52; resources/views/themes/arunika_equinox/cms/cms_layout.blade.php:52; resources/views/themes/arunika_equinox/cms/cms_layout.blade.php:52; resources/views/themes/arunika_lucent/cms/cms_layout.blade.php:40; resources/views/themes/arunika_lucent/cms/cms_layout.blade.php:40; resources/views/themes/arunika_prism/cms/cms_layout.blade.php:52; resources/views/themes/arunika_prism/cms/cms_layout.blade.php:52 |
| `public/assets/css/extending-css-bootstrap-5.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/setup/setup.blade.php:18; resources/views/themes/arunika_aurora/frontend/frontend_layout.blade.php:18; resources/views/themes/arunika_equinox/frontend/frontend_layout.blade.php:18; resources/views/themes/arunika_lucent/frontend/frontend_layout.blade.php:22; resources/views/themes/arunika_prism/frontend/frontend_layout.blade.php:18; resources/views/themes/calm_green/cms/cms_layout.blade.php:40; resources/views/themes/calm_green/frontend/frontend_layout.blade.php:18; resources/views/themes/default/cms/cms_layout.blade.php:38 |
| `public/assets/css/frontend-menu-dropdown.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/awesome_admin/menu/frontend/awesome_admin_parent_menu_fe.blade.php:829; resources/views/themes/arunika_aurora/frontend/frontend_layout.blade.php:21; resources/views/themes/arunika_equinox/frontend/frontend_layout.blade.php:21; resources/views/themes/arunika_lucent/frontend/frontend_layout.blade.php:26; resources/views/themes/arunika_prism/frontend/frontend_layout.blade.php:21; resources/views/themes/calm_green/frontend/frontend_layout.blade.php:21; resources/views/themes/default/frontend/frontend_layout.blade.php:20 |
| `public/assets/css/frontend_elementor.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/pagebuilder_elementor/editor_shell.blade.php:47; resources/views/pagebuilder_elementor/editor_shell.blade.php:47; resources/views/pagebuilder_elementor/frontend_renderer.blade.php:27; resources/views/pagebuilder_elementor/frontend_renderer.blade.php:27 |
| `public/assets/css/frontend_elementor_v23.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/pagebuilder_elementor_v23/editor_shell.blade.php:48; resources/views/pagebuilder_elementor_v23/editor_shell.blade.php:48; resources/views/pagebuilder_elementor_v23/frontend_renderer.blade.php:27; resources/views/pagebuilder_elementor_v23/frontend_renderer.blade.php:27 |
| `public/assets/css/frontend_elementor_v24.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/pagebuilder_elementor_v24/editor_shell.blade.php:48; resources/views/pagebuilder_elementor_v24/editor_shell.blade.php:48; resources/views/pagebuilder_elementor_v24/frontend_renderer.blade.php:49; resources/views/pagebuilder_elementor_v24/frontend_renderer.blade.php:49 |
| `public/assets/css/pagebuilder.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/pagebuilder/pagebuilder.blade.php:17 |
| `public/assets/css/pagebuilder_elementor.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/pagebuilder_elementor/editor_shell.blade.php:48; resources/views/pagebuilder_elementor/editor_shell.blade.php:48 |
| `public/assets/css/pagebuilder_elementor_v23.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/pagebuilder_elementor_v23/editor_shell.blade.php:49; resources/views/pagebuilder_elementor_v23/editor_shell.blade.php:49 |
| `public/assets/css/pagebuilder_elementor_v24.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/pagebuilder_elementor_v24/editor_shell.blade.php:49; resources/views/pagebuilder_elementor_v24/editor_shell.blade.php:49 |
| `public/assets/css/phoenix-cms.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/setup/setup.blade.php:19; resources/views/themes/arunika_aurora/auth/auth_layout.blade.php:26; resources/views/themes/arunika_aurora/cms/cms_layout.blade.php:47; resources/views/themes/arunika_aurora/frontend/frontend_layout.blade.php:20; resources/views/themes/arunika_equinox/auth/auth_layout.blade.php:26; resources/views/themes/arunika_equinox/cms/cms_layout.blade.php:47; resources/views/themes/arunika_equinox/frontend/frontend_layout.blade.php:20; resources/views/themes/arunika_lucent/auth/auth_layout.blade.php:29 |
| `public/assets/css/theme-responsive-typography.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/article/archive.blade.php:34; resources/views/article/detail.blade.php:17; resources/views/article/password-protected.blade.php:39; resources/views/article/private.blade.php:25; resources/views/manage_article/templates/preview.blade.php:10; resources/views/themes/arunika_aurora/cms/cms_layout.blade.php:49; resources/views/themes/arunika_equinox/cms/cms_layout.blade.php:49; resources/views/themes/arunika_lucent/auth/auth_layout.blade.php:31 |
| `public/assets/css/themes/arunika_aurora/arunika_aurora.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/themes/arunika_aurora/cms/cms_layout.blade.php:48 |
| `public/assets/css/themes/arunika_aurora/mobile-v2.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/themes/arunika_aurora/cms/cms_layout.blade.php:50 |
| `public/assets/css/themes/arunika_equinox/arunika_equinox.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/themes/arunika_equinox/cms/cms_layout.blade.php:48 |
| `public/assets/css/themes/arunika_equinox/mobile-v2.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/themes/arunika_equinox/cms/cms_layout.blade.php:50 |
| `public/assets/css/themes/arunika_lucent/arunika_lucent.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/themes/arunika_lucent/auth/auth_layout.blade.php:30; resources/views/themes/arunika_lucent/cms/cms_layout.blade.php:36; resources/views/themes/arunika_lucent/frontend/frontend_layout.blade.php:24 |
| `public/assets/css/themes/arunika_lucent/mobile-v2.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/themes/arunika_lucent/cms/cms_layout.blade.php:38 |
| `public/assets/css/themes/arunika_prism/arunika_prism.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/themes/arunika_prism/cms/cms_layout.blade.php:48 |
| `public/assets/css/themes/arunika_prism/mobile-v2.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/themes/arunika_prism/cms/cms_layout.blade.php:50 |
| `public/assets/css/themes/calm_green/aruna-admin-v7-calm-green.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/themes/calm_green/cms/cms_layout.blade.php:43 |
| `public/assets/css/themes/calm_green/phoenix-cms-calm-green.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/themes/arunika_aurora/auth/auth_layout.blade.php:25; resources/views/themes/arunika_aurora/frontend/frontend_layout.blade.php:19; resources/views/themes/arunika_equinox/auth/auth_layout.blade.php:25; resources/views/themes/arunika_equinox/frontend/frontend_layout.blade.php:19; resources/views/themes/arunika_prism/auth/auth_layout.blade.php:25; resources/views/themes/arunika_prism/frontend/frontend_layout.blade.php:19; resources/views/themes/calm_green/auth/auth_layout.blade.php:25; resources/views/themes/calm_green/cms/cms_layout.blade.php:41 |
| `public/assets/css/themes/default/aruna-admin-v7-default.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/themes/default/cms/cms_layout.blade.php:41 |
| `public/assets/css/themes/default/phoenix-cms-default.css` | TERPAKAI_KONDISIONAL_SOURCE | resources/views/themes/default/auth/auth_layout.blade.php:23; resources/views/themes/default/cms/cms_layout.blade.php:39; resources/views/themes/default/frontend/frontend_layout.blade.php:18 |
| `public/assets/css/workflow-editor.css` | TERPAKAI_RUNTIME_HTTP_200 | resources/views/workflow/editor.blade.php:189; resources/views/workflow/index.blade.php:116 |

## Seluruh inventaris CSS aktif/calon (173)

| File | Status review | Byte | Runtime |
|---|---|---:|---|
| `public/assets/beta_css/new.css` | KANDIDAT_TIDAK_TERPAKAI_SOURCE_DB_0_HIT | 17375 |  |
| `public/assets/css/article/article-editor-content.css` | TERPAKAI_RUNTIME_HTTP_200 | 13658 | /manage_article/add |
| `public/assets/css/article/article-frontend-2026.css` | TERPAKAI_RUNTIME_HTTP_200 | 141681 | /article |
| `public/assets/css/article/article-template-manager-2026.css` | TERPAKAI_RUNTIME_HTTP_200 | 80410 | /manage_article/templates |
| `public/assets/css/article/manage-article-2026.css` | TERPAKAI_RUNTIME_HTTP_200 | 17422 | /manage_article |
| `public/assets/css/aruna-v4.css` | TERPAKAI_KONDISIONAL_SOURCE | 13738 |  |
| `public/assets/css/awesome-admin-header-navigation-fullscreen.css` | TERPAKAI_RUNTIME_HTTP_200 | 36123 | /awesome_admin/header-navigation |
| `public/assets/css/awesome-admin-header-navigation.css` | TERPAKAI_RUNTIME_HTTP_200 | 83384 | /awesome_admin/header-navigation |
| `public/assets/css/cms-bootstrap-theme.css` | TERPAKAI_RUNTIME_HTTP_200 | 9082 | /awesome_admin/header-navigation; /dashboard; /manage_article; /manage_article/add; /manage_article/templates; /manage_coverimage/add; /manage_event/add; /workflow; /workflow/create |
| `public/assets/css/extending-css-bootstrap-5.css` | TERPAKAI_RUNTIME_HTTP_200 | 69805 | /article |
| `public/assets/css/frontend-menu-dropdown.css` | TERPAKAI_RUNTIME_HTTP_200 | 22147 | /article |
| `public/assets/css/frontend_elementor.css` | TERPAKAI_RUNTIME_HTTP_200 | 53606 | /pagebuilder-elementor/create |
| `public/assets/css/frontend_elementor_v23.css` | TERPAKAI_RUNTIME_HTTP_200 | 58823 | /pagebuilder-elementor/v2.3/create |
| `public/assets/css/frontend_elementor_v24.css` | TERPAKAI_RUNTIME_HTTP_200 | 7216 | /pagebuilder-elementor/v2.4/create |
| `public/assets/css/pagebuilder.css` | TERPAKAI_RUNTIME_HTTP_200 | 42262 | /pagebuilder |
| `public/assets/css/pagebuilder_elementor.css` | TERPAKAI_RUNTIME_HTTP_200 | 219812 | /pagebuilder-elementor/create |
| `public/assets/css/pagebuilder_elementor_v23.css` | TERPAKAI_RUNTIME_HTTP_200 | 286083 | /pagebuilder-elementor/v2.3/create |
| `public/assets/css/pagebuilder_elementor_v24.css` | TERPAKAI_RUNTIME_HTTP_200 | 181675 | /pagebuilder-elementor/v2.4/create |
| `public/assets/css/phoenix-cms.css` | TERPAKAI_RUNTIME_HTTP_200 | 41006 | /article; /awesome_admin/header-navigation; /dashboard; /manage_article; /manage_article/add; /manage_article/templates; /manage_coverimage/add; /manage_event/add; /workflow; /workflow/create |
| `public/assets/css/theme-responsive-typography.css` | TERPAKAI_RUNTIME_HTTP_200 | 8607 | /article; /awesome_admin/header-navigation; /dashboard; /manage_article; /manage_article/add; /manage_article/templates; /manage_coverimage/add; /manage_event/add; /workflow; /workflow/create |
| `public/assets/css/themes/arunika_aurora/arunika_aurora.css` | TERPAKAI_KONDISIONAL_SOURCE | 58632 |  |
| `public/assets/css/themes/arunika_aurora/mobile-v2.css` | TERPAKAI_KONDISIONAL_SOURCE | 21116 |  |
| `public/assets/css/themes/arunika_equinox/arunika_equinox.css` | TERPAKAI_KONDISIONAL_SOURCE | 111718 |  |
| `public/assets/css/themes/arunika_equinox/mobile-v2.css` | TERPAKAI_KONDISIONAL_SOURCE | 10772 |  |
| `public/assets/css/themes/arunika_lucent/arunika_lucent.css` | TERPAKAI_RUNTIME_HTTP_200 | 108484 | /article; /awesome_admin/header-navigation; /dashboard; /manage_article; /manage_article/add; /manage_article/templates; /manage_coverimage/add; /manage_event/add; /workflow; /workflow/create |
| `public/assets/css/themes/arunika_lucent/mobile-v2.css` | TERPAKAI_RUNTIME_HTTP_200 | 21007 | /awesome_admin/header-navigation; /dashboard; /manage_article; /manage_article/add; /manage_article/templates; /manage_coverimage/add; /manage_event/add; /workflow; /workflow/create |
| `public/assets/css/themes/arunika_prism/arunika_prism.css` | TERPAKAI_KONDISIONAL_SOURCE | 76163 |  |
| `public/assets/css/themes/arunika_prism/mobile-v2.css` | TERPAKAI_KONDISIONAL_SOURCE | 15435 |  |
| `public/assets/css/themes/calm_green/aruna-admin-v7-calm-green.css` | TERPAKAI_KONDISIONAL_SOURCE | 23118 |  |
| `public/assets/css/themes/calm_green/phoenix-cms-calm-green.css` | TERPAKAI_KONDISIONAL_SOURCE | 7444 |  |
| `public/assets/css/themes/default/aruna-admin-v7-default.css` | TERPAKAI_KONDISIONAL_SOURCE | 22118 |  |
| `public/assets/css/themes/default/phoenix-cms-default.css` | TERPAKAI_KONDISIONAL_SOURCE | 6504 |  |
| `public/assets/css/workflow-editor.css` | TERPAKAI_RUNTIME_HTTP_200 | 36355 | /workflow; /workflow/create |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-grid.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 70323 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-grid.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 51789 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-grid.rtl.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 70397 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-grid.rtl.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 51864 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-reboot.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 12065 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-reboot.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 10126 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-reboot.rtl.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 12058 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-reboot.rtl.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 10198 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-utilities.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 107938 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-utilities.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 85457 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-utilities.rtl.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 107806 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap-utilities.rtl.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 85386 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 280202 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 232018 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap.rtl.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 279417 |  |
| `public/assets/plugins/bootstrap/5.3.6/css/bootstrap.rtl.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 232126 |  |
| `public/assets/plugins/bootstrap/5.3.6_custom/bootstrap.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 362625 |  |
| `public/assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css` | TERPAKAI_RUNTIME_HTTP_200 | 285610 | /article; /awesome_admin/header-navigation; /dashboard; /manage_article; /manage_article/add; /manage_article/templates; /manage_coverimage/add; /manage_event/add; /workflow; /workflow/create |
| `public/assets/plugins/ckeditor5/sample/styles.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 11441 |  |
| `public/assets/plugins/ckfinder/libs/jquery.mobile.icons.css` | DEPENDENSI_SKIN_DINAMIS_CKFINDER_PERTAHANKAN | 127053 |  |
| `public/assets/plugins/ckfinder/libs/jquery.mobile.structure.css` | DEPENDENSI_SKIN_DINAMIS_CKFINDER_PERTAHANKAN | 68712 |  |
| `public/assets/plugins/ckfinder/libs/jquery.mobile.theme.css` | DEPENDENSI_SKIN_DINAMIS_CKFINDER_PERTAHANKAN | 11976 |  |
| `public/assets/plugins/ckfinder/samples/css/sample.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 24612 |  |
| `public/assets/plugins/ckfinder/skins/core/ckfinder.css` | DEPENDENSI_SKIN_DINAMIS_CKFINDER_PERTAHANKAN | 48846 |  |
| `public/assets/plugins/ckfinder/skins/jquery-mobile/icons.css` | DEPENDENSI_SKIN_DINAMIS_CKFINDER_PERTAHANKAN | 338802 |  |
| `public/assets/plugins/ckfinder/skins/moono/ckfinder.css` | DEPENDENSI_SKIN_DINAMIS_CKFINDER_PERTAHANKAN | 19282 |  |
| `public/assets/plugins/ckfinder/skins/moono/icons.css` | DEPENDENSI_SKIN_DINAMIS_CKFINDER_PERTAHANKAN | 378080 |  |
| `public/assets/plugins/ckfinder/skins/neko/ckfinder.css` | DEPENDENSI_SKIN_DINAMIS_CKFINDER_PERTAHANKAN | 54015 |  |
| `public/assets/plugins/ckfinder/skins/neko/icons.css` | DEPENDENSI_SKIN_DINAMIS_CKFINDER_PERTAHANKAN | 271841 |  |
| `public/assets/plugins/echarts/5.5.1/test/lib/caseFrame.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 1943 |  |
| `public/assets/plugins/echarts/5.5.1/test/lib/reset.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 3101 |  |
| `public/assets/plugins/echarts/5.5.1/test/runTest/client/client.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 6066 |  |
| `public/assets/plugins/echarts/5.5.1/test/runTest/recorder/recorder.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 4170 |  |
| `public/assets/plugins/echarts/5.5.1/test/stream-basic.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 1203 |  |
| `public/assets/plugins/filemanager_v2/filemanager-v2.css` | TERPAKAI_RUNTIME_HTTP_200 | 384427 | /admin/file-manager-v2 |
| `public/assets/plugins/fontawesome/5.15.3/css/all.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 213221 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/all.min.css` | TERPAKAI_RUNTIME_HTTP_200 | 174333 | /awesome_admin/header-navigation; /dashboard; /manage_article; /manage_article/add; /manage_article/templates; /manage_coverimage/add; /manage_event/add; /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create; /workflow; /workflow/create |
| `public/assets/plugins/fontawesome/5.15.3/css/brands.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 689 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/brands.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 632 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/duotone.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 98374 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/duotone.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 81475 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/fontawesome.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 112825 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/fontawesome.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 91088 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/light.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 677 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/light.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 620 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/regular.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 689 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/regular.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 632 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/solid.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 682 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/solid.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 624 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/svg-with-js.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 8034 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/svg-with-js.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 6316 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/v4-shims.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 41128 |  |
| `public/assets/plugins/fontawesome/5.15.3/css/v4-shims.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 26525 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/all.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 660888 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/all.min.css` | TERPAKAI_RUNTIME_HTTP_200 | 520504 | /pagebuilder |
| `public/assets/plugins/fontawesome/6.5.1/css/brands.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 24523 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/brands.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 19264 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/duotone.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 401697 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/duotone.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 329634 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/fontawesome.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 230706 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/fontawesome.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 168221 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/light.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 579 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/light.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 526 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/regular.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 587 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/regular.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 534 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/sharp-light.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 602 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/sharp-light.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 549 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/sharp-regular.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 610 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/sharp-regular.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 557 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/sharp-solid.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 602 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/sharp-solid.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 549 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/sharp-thin.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 598 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/sharp-thin.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 545 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/solid.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 579 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/solid.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 526 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/svg-with-js.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 21659 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/svg-with-js.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 16974 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/thin.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 575 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/thin.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 522 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/v4-font-face.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 1788 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/v4-font-face.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 1693 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/v4-shims.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 41400 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/v4-shims.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 27423 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/v5-font-face.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 1262 |  |
| `public/assets/plugins/fontawesome/6.5.1/css/v5-font-face.min.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 1133 |  |
| `public/assets/plugins/simplebar/6.2.7/css/simplebar.css` | KANDIDAT_VARIAN_PLUGIN_TANPA_PEMANGGIL_APLIKASI | 4237 |  |
| `public/assets/plugins/simplebar/6.3.1/css/simplebar.css` | TERPAKAI_RUNTIME_HTTP_200 | 4237 | /awesome_admin/header-navigation; /dashboard; /manage_article; /manage_article/add; /manage_article/templates; /manage_coverimage/add; /manage_event/add; /workflow; /workflow/create |
| `public/assets/plugins/spectrum/dist/spectrum.css` | SOURCE_DISTRIBUSI_SPECTRUM | 15936 |  |
| `public/assets/plugins/spectrum/dist/spectrum.min.css` | TERPAKAI_RUNTIME_HTTP_200 | 11514 | /manage_coverimage/add |
| `public/assets/plugins/spectrum/docs/docs.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 4407 |  |
| `public/assets/plugins/spectrum/docs/highlight/styles/default.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 1159 |  |
| `public/assets/plugins/spectrum/docs/highlight/styles/monokai-sublime.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 1026 |  |
| `public/assets/plugins/spectrum/src/spectrum.css` | SOURCE_DISTRIBUSI_SPECTRUM | 15936 |  |
| `public/assets/plugins/spectrum/test/qunit.css` | CONTOH_DOKUMENTASI_TEST_PLUGIN | 4829 |  |
| `public/assets/plugins/vue/plugins/vue-datepicker/css/vue-datepicker-11.0.3.css` | TERPAKAI_RUNTIME_HTTP_200 | 24110 | /manage_coverimage/add; /manage_event/add |
| `public/assets/plugins/vue/plugins/vue-select/css/vue-select.3.20.3.css` | TERPAKAI_RUNTIME_HTTP_200 | 7494 | /awesome_admin/header-navigation; /dashboard; /manage_article; /manage_article/add; /manage_article/templates; /manage_coverimage/add; /manage_event/add; /workflow; /workflow/create |
| `public/assets/vendor/coloris/coloris.min.css` | TERPAKAI_RUNTIME_HTTP_200 | 8506 | /awesome_admin/header-navigation; /manage_article/templates |
| `public/assets/vendor/pb-picker/picker.min.css` | TERPAKAI_RUNTIME_HTTP_200 | 8506 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `resources/css/app.css` | INPUT_VITE_KOSONG_TANPA_CONSUMER_VIEW | 0 |  |
| `resources/js/filemanager_v2/styles.css` | SOURCE_BUILD_TERPAKAI | 51492 |  |
| `resources/pagebuilder_elementor_v24/modules/layout/container/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 14538 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/layout/container-fluid/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 14538 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/layout/grid/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 23139 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/layout/row-grid/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 23139 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/basic/button/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 21839 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/basic/divider/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 2555 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/basic/heading/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 4301 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/basic/icon/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 20648 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/basic/image/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 5423 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/basic/spacer/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 426 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/basic/text-editor/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 12639 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/basic/video/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 4661 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/accordion/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 40334 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/alert/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 2155 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/basic-gallery/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 7301 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/counter/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 304 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/icon-box/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 8119 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/icon-list/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 4026 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/image-box/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 5038 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/image-carousel/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 8547 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/progress-bar/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 801 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/social-icons/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 3107 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/tabs/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 45282 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/general/testimonial/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 1463 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/pro/form/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 3619 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/pro/hero-banner/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 4098 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/pro/hero-slider/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 24467 | /pagebuilder-elementor/v2.4/create |
| `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-lead-form/styles.css` | TERPAKAI_RUNTIME_STYLE_INLINE | 5412 | /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/andika/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 4945 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/fira_sans/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 25043 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/lato/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 9745 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/lobster_two/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 4037 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/montserrat/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 23315 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/noto_sans/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 26429 | /article; /awesome_admin/header-navigation; /dashboard; /manage_article; /manage_article/add; /manage_article/templates; /manage_coverimage/add; /manage_event/add; /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create; /workflow; /workflow/create |
| `storage/app/public/fonts/nunito/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 19765 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/open_sans/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 18377 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/plus_jakarta_sans/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 18695 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/poppins/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 19733 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/roboto/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 25889 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |
| `storage/app/public/fonts/source_code_pro/fonts.css` | TERPAKAI_RUNTIME_HTTP_200 | 23685 | /pagebuilder-elementor/create; /pagebuilder-elementor/v2.3/create; /pagebuilder-elementor/v2.4/create |

## Import paket dan source build tambahan

- `node_modules/bootstrap/dist/css/bootstrap.min.css`
- `node_modules/bootstrap-icons/font/bootstrap-icons.css`
- `node_modules/@fontsource/nunito/400.css`
- `node_modules/@fontsource/nunito/500.css`
- `node_modules/@fontsource/nunito/600.css`
- `node_modules/@fontsource/nunito/700.css`
- `node_modules/filepond/dist/filepond.min.css`
- `resources/js/filemanager_v2/responsive-typography.scss`
- `resources/js/filemanager_v2/components/FilePondUploadEngine.vue` juga memuat style block; style component/build bukan CSS sisa yang boleh dihapus terpisah.
- File hasil build aktif: `public/assets/plugins/filemanager_v2/filemanager-v2.css`; sumbernya mencakup imports CSS/SCSS/Vue. Tidak ditemukan CSS fisik pada public/build/assets; manifest app.css menghasilkan JavaScript kosong.

## CSS eksternal

- `https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css`
- `https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css`
- `https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css`
- `https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css`
- `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css`
- `https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css`
- `https://cdn.plyr.io/3.7.8/plyr.css`
- `https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css`
- `https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap`
- `https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap`
- `https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css`
- `https://unpkg.com/leaflet@1.9.4/dist/leaflet.css`

## Browser lokal (15 halaman)

| URL path | HTTP |
|---|---:|
| `/dashboard` | 200 |
| `/manage_article` | 200 |
| `/manage_article/add` | 200 |
| `/manage_article/templates` | 200 |
| `/awesome_admin/header-navigation` | 200 |
| `/admin/file-manager-v2` | 200 |
| `/workflow` | 200 |
| `/workflow/create` | 200 |
| `/pagebuilder-elementor/v2.4/create` | 200 |
| `/article` | 200 |
| `/manage_event/add` | 200 |
| `/manage_coverimage/add` | 200 |
| `/pagebuilder` | 200 |
| `/pagebuilder-elementor/create` | 200 |
| `/pagebuilder-elementor/v2.3/create` | 200 |

Login auth juga diamati pada awal sesi: Bootstrap custom, Font Awesome 5.15.3, Noto Sans, phoenix-cms, tema Lucent, dan responsive typography. Data browser terstruktur yang dipakai menghitung hasil di atas adalah 15 halaman setelah login.

## Duplikasi byte identik

Tidak ada duplikasi byte identik di antara 32 CSS custom. Kelompok berikut sama hash, tetapi dapat memiliki tanggung jawab/URL terpisah; belum digabung atau dihapus.
- `public/assets/plugins/simplebar/6.2.7/css/simplebar.css`; `public/assets/plugins/simplebar/6.3.1/css/simplebar.css`
- `public/assets/plugins/spectrum/dist/spectrum.css`; `public/assets/plugins/spectrum/src/spectrum.css`
- `public/assets/vendor/coloris/coloris.min.css`; `public/assets/vendor/pb-picker/picker.min.css`
- `resources/pagebuilder_elementor_v24/modules/layout/container/styles.css`; `resources/pagebuilder_elementor_v24/modules/layout/container-fluid/styles.css`
- `resources/pagebuilder_elementor_v24/modules/layout/grid/styles.css`; `resources/pagebuilder_elementor_v24/modules/layout/row-grid/styles.css`

## Arsip historis yang sudah ada (tidak dianalisis ulang sebagai source aktif)

Enam nama berikut masih ada di public/assets/archived/css; isi file tidak dimasukkan ke audit source aktif:
- `aruna-admin-v6.css`
- `aruna-admin-v7-phoenix-elegant.css`
- `aruna-admin-v7-simple-part-2.css`
- `aruna-admin-v7-simple.css`
- `aruna-admin-v7.css`
- `aruna-v3.css`

## Memori, file yang dibaca, dan recovery

- Memori: `E:/AI/Memories/20260905-laravel13-phoenix-css-archive-and-equinox-mobile-handoff.md`.
- Memori: `E:/AI/Memories/20260825-laravel13-phoenix-public-resources-views-cleanup-handoff.md`.
- Daftar file aplikasi pemanggil CSS: application-reference-files.txt. Lokasi dan nomor baris referensi: references.json dan css-reviewed.csv.
- Source utama: enam layout CMS, auth/frontend layout, Blade halaman terkait, editor/frontend Page Builder, module.json, ModuleCatalog, ModuleAssetController, route aktif, main.js/styles.css/SCSS/FilePondUploadEngine File Manager, vite configs, dan manifest public/build.
- Backup source aplikasi: tidak diperlukan karena source aplikasi tidak dimodifikasi. Backup revisi alat audit baru saja berada di folder audit ini dengan suffix .bak_20261030_*. Tidak ada backup lama dijadikan source.
- Perubahan milik user tetap: public/assets/css/article/manage-article-2026.css; public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js; resources/views/manage_article/manage_article.blade.php.
- Pemeriksaan: 173 hash CSS tetap cocok; seluruh 15 response halaman dan 43 CSS lokal teramati HTTP 200; 28 module style terdeteksi. Tidak menjalankan full suite/build karena aplikasi tidak diubah.
- Database dicek dalam transaksi READ ONLY lalu rollback. Tidak ada perubahan pengaturan tema atau penyimpanan halaman/widget.
- Graph tidak diperbarui; menunggu perintah manual. Tidak ada staging, commit, push, atau deploy.

## Berkas bukti

- css-reviewed.csv: seluruh klasifikasi hasil review, ukuran, hash, referensi, dan halaman runtime.
- css-inventory.csv / references.json: hasil pencocokan source awal; gunakan reviewed_status pada css-reviewed.csv sebagai kesimpulan.
- runtime-pages.json: hasil browser lokal yang dinormalisasi.
- database-css-check.json: hasil kandidat beta pada database.
- exact-duplicates.json / graph-freshness.json / verification-summary.json: duplikasi, freshness graph, dan verifikasi akhir.
- external-css.json: source referensi stylesheet eksternal berekstensi .css. Google Fonts tercatat dari source dan browser di laporan ini.
