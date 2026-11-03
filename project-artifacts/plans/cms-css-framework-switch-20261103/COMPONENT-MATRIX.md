# Matriks komponen CMS dan padanan Tailwind

3 November 2026. Hasil scan 65 view kandidat dan 28 script. Angka adalah jumlah file dengan rujukan pola, **bukan jumlah instance unik atau klaim semua komponen sudah diuji runtime**.

Tailwind menangani tampilan; perilaku di kolom padanan memerlukan komponen/controller yang sesuai. Status semua padanan adalah **rencana, belum prototype CMS Tailwind**.

| Komponen | File terdeteksi | Padanan/rencana | Risiko | Kontrak yang wajib dipertahankan |
|---|---:|---|---|---|
| Modal / confirmation | 35 | Tailwind + Dialog/AlertDialog (kandidat Reka) | Tinggi | Focus trap/return, backdrop static, keyboard, form tetap, modal berantai dan callback after-close |
| Native dialog | 6 | Pertahankan dialog native dan controller existing; skin menyesuaikan | Tinggi | Discard perubahan, Escape dan fokus ketika berada di atas modal lain |
| Dropdown / action menu | 9 | Tailwind + DropdownMenu | Tinggi | Keyboard, outside-click, auto-close outside, posisi/collision, action async |
| Collapse / accordion | 18 | Tailwind + Collapsible/Accordion atau controller existing yang terbukti | Sedang | aria-expanded, height transition, nested menu, event setelah selesai |
| Toast / feedback | 45 | Tailwind + Toast | Tinggi | Timeout/pause, live region, isShown, urutan tutup toast-modal, cleanup timer |
| Tooltip | 2 | Tailwind + Tooltip | Sedang | Hover/focus, portal, collision, konten dinamis setelah Vue render |
| Popover | 0 | Padanan tersedia; jangan dibuat sebelum ada consumer | Belum ditemukan | Scanner tidak menemukan pemakaian pada cakupan ini; konfirmasi G0 |
| Offcanvas / drawer | 2 | Skin Tailwind + controller mobile existing/primitive Dialog stabil | Tinggi | Body scroll lock, focus, inert, backdrop, resize 768/769, nested menu |
| Tabs | 1 | Tailwind + Tabs bila consumer reachable | Sedang | Keyboard, aria-selected, panel tersembunyi, resize editor/chart |
| Carousel | 0 | Styling memungkinkan; tidak masuk implementasi tanpa consumer | Belum ditemukan | Tidak ditemukan pada source scope ini; bukan klaim seluruh repository tidak punya |
| Pagination | 7 | Pertahankan VuejsPaginateNext; ubah skin/kelas driver | Tinggi | Current/disabled, first/last/ellipsis, filter-reset, query, request race dan page limit |
| Table / responsive list | 13 | HTML/Vue existing + utility/komponen CSS | Sedang | Sort/filter/checkbox, detail row, sticky menu, title panjang, overflow mobile |
| Form / input group | 44 | Input native/Blade existing + Tailwind component styles | Tinggi | Label, padding, value, focus, autofill, readonly, disabled, input-group |
| Checkbox / radio / switch | 15 | Input native existing; primitive tambahan hanya bila diperlukan | Sedang | Checked/indeterminate/disabled, label click, keyboard dan payload |
| Validation | 4 | Pertahankan server validation dan Vue state; skin driver | Tinggi | aria-invalid/describedby, error placement, focus error pertama, input tidak hilang |
| Loading / placeholder / progress | 40 | Tailwind + state existing; primitive Progress bila memang perlu | Sedang | Loading/submitting/success/error, tombol generated HTML, ukuran tidak meloncat |
| Buttons / badge / alert / card | 51 | HTML/Blade/Vue existing + Tailwind CSS | Sedang | Normal/hover/focus/active/disabled, kontras matte/kuning/gelap, semantic danger |
| Sidebar / navigation | 12 | Template tema existing + driver/skin | Tinggi | Active route, submenu/parent, collapsed/expanded/mobile, tooltip, role, palet |
| Breadcrumb | 35 | Renderer breadcrumb existing + view/skin driver | Rendah | Urutan/link/label panjang, separator, area kecil dan aksesibilitas |

## Plugin yang tetap dipertahankan

Jumlah rujukan plugin termasuk asset include dan kode init; pemakaian aktif tiap halaman ditutup di G0.

| Plugin/kelompok | File dengan rujukan | Penanganan |
|---|---:|---|
| Coloris | 7 | Pertahankan paket/logic; audit CSS, init, overlay, resize, lifecycle dan input/output. |
| Vue Select | 21 | Pertahankan paket/logic; audit CSS, init, overlay, resize, lifecycle dan input/output. |
| CKEditor | 8 | Pertahankan paket/logic; audit CSS, init, overlay, resize, lifecycle dan input/output. |
| CKFinder media picker | 11 | Pertahankan paket/logic; audit CSS, init, overlay, resize, lifecycle dan input/output. |
| SimpleBar | 13 | Pertahankan paket/logic; audit CSS, init, overlay, resize, lifecycle dan input/output. |
| Charts | 8 | Pertahankan paket/logic; audit CSS, init, overlay, resize, lifecycle dan input/output. |
| Maps | 4 | Pertahankan paket/logic; audit CSS, init, overlay, resize, lifecycle dan input/output. |
| Sortable / drag-drop | 19 | Pertahankan paket/logic; audit CSS, init, overlay, resize, lifecycle dan input/output. |
| Date/time picker | 5 | Pertahankan paket/logic; audit CSS, init, overlay, resize, lifecycle dan input/output. |

Vue Select, VueDatePicker dan VuejsPaginateNext mempunyai ketergantungan runtime Vue yang harus tetap sama. CKFinder merupakan media picker tersendiri. Referensi Leaflet CSS tidak membuktikan Leaflet map aktif; jsVectorMap mempunyai inisialisasi nyata di dashboard. Nama CSS dropzone bukan bukti library Dropzone/FilePond dipakai, sehingga tidak dimasukkan sebagai paket migrasi.

## Sumber contoh yang sudah dibuka

- **Modal / confirmation**: [public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js:1058); [public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js:1066).
- **Native dialog**: [public/assets/js/awesome-admin-header-navigation-fullscreen.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/awesome-admin-header-navigation-fullscreen.js:54); [public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js:903).
- **Dropdown / action menu**: [resources/views/components/cms-realtime-notification.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/components/cms-realtime-notification.blade.php:21); [resources/views/components/cms-realtime-notification.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/components/cms-realtime-notification.blade.php:34).
- **Collapse / accordion**: [public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js:1828); [public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js:1834).
- **Toast / feedback**: [public/assets/js/vue3/account/vueV3-account-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/account/vueV3-account-2026.js:128); [public/assets/js/vue3/account/vueV3-account-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/account/vueV3-account-2026.js:130).
- **Tooltip**: [resources/views/themes/calm_green/cms/cms_layout.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/themes/calm_green/cms/cms_layout.blade.php:555); [resources/views/themes/calm_green/cms/cms_layout.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/themes/calm_green/cms/cms_layout.blade.php:556).
- **Offcanvas / drawer**: [resources/views/themes/calm_green/cms/cms_layout.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/themes/calm_green/cms/cms_layout.blade.php:217); [resources/views/themes/calm_green/cms/cms_layout.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/themes/calm_green/cms/cms_layout.blade.php:225).
- **Tabs**: [resources/views/notification/notification.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/notification/notification.blade.php:27); [resources/views/notification/notification.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/notification/notification.blade.php:31).
- **Pagination**: [resources/views/awesome_admin/awesome_admin_language_translated.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/awesome_admin/awesome_admin_language_translated.blade.php:130); [resources/views/awesome_admin/awesome_admin_language_untranslated.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/awesome_admin/awesome_admin_language_untranslated.blade.php:130).
- **Table / responsive list**: [public/assets/js/global/jquery/jquery-3.7.0.min.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/global/jquery/jquery-3.7.0.min.js:2); [resources/views/awesome_admin/awesome_admin_language_translated.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/awesome_admin/awesome_admin_language_translated.blade.php:80).
- **Form / input group**: [public/assets/js/awesome-admin-header-navigation-fullscreen.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/awesome-admin-header-navigation-fullscreen.js:74); [public/assets/js/awesome-admin-header-navigation-fullscreen.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/awesome-admin-header-navigation-fullscreen.js:166).
- **Checkbox / radio / switch**: [public/assets/js/awesome-admin-header-navigation-fullscreen.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/awesome-admin-header-navigation-fullscreen.js:145); [public/assets/js/awesome-admin-header-navigation-fullscreen.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/awesome-admin-header-navigation-fullscreen.js:146).
- **Validation**: [public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js:1866); [public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js:1868).
- **Loading / placeholder / progress**: [public/assets/js/vue3/account/vueV3-account-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/account/vueV3-account-2026.js:87); [public/assets/js/vue3/account/vueV3-account-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/account/vueV3-account-2026.js:299).
- **Buttons / badge / alert / card**: [public/assets/js/vue3/account/vueV3-account-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/account/vueV3-account-2026.js:136); [public/assets/js/vue3/account/vueV3-account-2026.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/vue3/account/vueV3-account-2026.js:200).
- **Sidebar / navigation**: [public/assets/js/themes/arunika-mobile-navigation-v2.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/themes/arunika-mobile-navigation-v2.js:48); [public/assets/js/themes/arunika_aurora/arunika_aurora.js](D:/Laragon/www/laravel-13-phoenix/public/assets/js/themes/arunika_aurora/arunika_aurora.js:232).
- **Breadcrumb**: [resources/views/awesome_admin/awesome_admin_appearance.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/awesome_admin/awesome_admin_appearance.blade.php:100); [resources/views/awesome_admin/awesome_admin_config.blade.php](D:/Laragon/www/laravel-13-phoenix/resources/views/awesome_admin/awesome_admin_config.blade.php:886).

## Referensi kemampuan

- [Tailwind utility styling](https://tailwindcss.com/docs/styling-with-utility-classes) dan [custom styles](https://tailwindcss.com/docs/adding-custom-styles): fondasi styling; tidak mengubah API Bootstrap.
- [Reka introduction](https://www.reka-ui.com/docs/overview/introduction): pustaka Vue, unstyled, fokus aksesibilitas dan state yang dapat dikendalikan.
- [Dialog](https://www.reka-ui.com/docs/components/dialog), [DropdownMenu](https://www.reka-ui.com/docs/components/dropdown-menu), [Toast](https://www.reka-ui.com/docs/components/toast), [Tooltip](https://www.reka-ui.com/docs/components/tooltip), [Collapsible](https://www.reka-ui.com/docs/components/collapsible): kandidat perilaku interaktif.
- [Reka Pagination](https://www.reka-ui.com/docs/components/pagination): padanan tersedia, tetapi rencana memilih mempertahankan VuejsPaginateNext existing terlebih dahulu.
- [Headless UI Vue Dialog](https://headlessui.com/v1/vue/dialog): alternatif Vue dengan focus/keyboard; bukan pilihan otomatis untuk seluruh kebutuhan CMS.

Ketersediaan komponen di dokumentasi tidak membuktikan kecocokan dengan Vue global, lifecycle dan plugin CMS. G1 pada PLAN.md wajib lulus sebelum kandidat dipilih final.
