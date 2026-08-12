# Header Navigation Page Builder v2.3 Inspector Redesign

Date: 2026-08-12  
Project: Laravel 13 Phoenix  
Route: `/awesome_admin/header-navigation`

## Goal

Rombak UI/UX inspector sidebar kanan Manage Header Navigation agar mengikuti pola sidebar setting Page Builder Elementor v2.3 secara visual dan interaksional: header panel, tiga tab utama, selection summary, accordion section, control density, responsive control, dan ritme spacing yang konsisten.

Seluruh setting Header Navigation, preview, scroll timeline, responsive device state, color linking, box linking, dan persistence tetap bekerja seperti sekarang.

## Reference contract

Source reference yang digunakan adalah runtime Page Builder v2.3 pada route `/pagebuilder-elementor/v2.3/create` dan source aktif berikut:

- `public/assets/css/pagebuilder_elementor_v23.css`
- `public/js/pagebuilder_elementor_v23/app.js`
- `resources/views/pagebuilder_elementor_v23/editor_shell.blade.php`

Nilai visual yang menjadi acuan:

- panel header sekitar `58px`;
- tab bar sekitar `42px`, tiga tab dengan active underline;
- panel body padding `16px`;
- selection summary sekitar `54px`, radius `10px`;
- accordion summary minimal `34px`;
- form group gap sekitar `11px`;
- label sekitar `9.5px`;
- input/select sekitar `34px` dengan font `10px`;
- segmented control sekitar `27px`;
- compact number/unit control sekitar `30px`;
- border tipis, surface putih, muted text, dan aksen brand ungu Page Builder.

Nilai tersebut diterapkan secara scoped pada Header Navigation. CSS Page Builder tidak akan di-load global agar v2.0, v2.3, dan halaman lain tidak ikut berubah.

## Information architecture

Enam kategori Header Navigation yang sudah ada dikelompokkan ke tiga tab Page Builder:

| Tab | Accordion yang ditampilkan |
| --- | --- |
| Layout | Preview, Layout, Sizing |
| Style | Colors, Effects |
| Advanced | Behavior, Config Preview |

Nama dan isi accordion tetap dipertahankan agar pengguna tidak kehilangan setting. Tab hanya mengubah pengelompokan dan navigasi; tidak ada field yang dihapus atau dipindahkan ke fitur lain.

Struktur sidebar:

1. Panel header: tombol kembali/identitas `Header Navigation` dan subtitle `Header navigation settings`.
2. Tiga tab: `Layout`, `Style`, `Advanced`.
3. Selection summary card yang menunjukkan `Header Navigation` dan konteks `Frontend header`.
4. Satu tab panel aktif pada satu waktu.
5. Accordion native untuk setiap kategori, dengan chevron dan keyboard behavior tetap aktif.

Outer preview/sidebar width yang sudah dibuat lega tetap dipertahankan. Adaptasi Page Builder diterapkan pada struktur internal dan density control, bukan mengecilkan kolom sampai color control kembali terhimpit.

## Interaction behavior

- Tab aktif memakai `aria-selected="true"`, active underline, dan panel pasangannya memakai `aria-labelledby`.
- Klik tab hanya mengganti panel yang terlihat dan mengembalikan scroll inspector ke bagian atas.
- State `open` pada accordion tetap native dan tidak memengaruhi nilai setting.
- Deep focus behavior untuk kategori Effects diarahkan ke tab Style lalu membuka/fokus ke Effects.
- Semua event handler setting yang ada tetap dipakai: color picker, linked color, linked box sides, responsive device menu, toggles, select, number input, preview device, dan scroll timeline.
- Tidak ada perubahan pada Save, API request, database config, preview URL, atau frontend renderer.

## Responsive behavior

- Desktop mempertahankan preview dan inspector berdampingan.
- Inspector content menggunakan `min-width: 0` dan grid track `minmax(0, 1fr)` agar label serta form tidak overflow.
- Tab tetap tiga kolom pada lebar normal; padding dan font turun menggunakan token CMS/Page Builder pada viewport lebih kecil.
- Breakpoint existing untuk preview/sidebar tetap dipertahankan. Perubahan hanya memastikan tab, accordion, box control, dan control unit tetap terbaca tanpa overlap.
- Preview frame dan timeline tidak dirombak dalam pekerjaan ini selain kompatibilitas layout akibat perubahan sidebar.

## Source boundary

File yang diperkirakan diubah:

- `resources/views/awesome_admin/header_navigation/awesome_admin_header_navigation.blade.php` — header inspector, tab panels, dan pengelompokan accordion.
- `public/assets/css/awesome-admin-header-navigation.css` — scoped Page Builder v2.3 visual contract untuk inspector.
- `public/assets/js/awesome-admin-header-navigation.js` — tab switching dan focus mapping tanpa mengubah config behavior.
- `tests/Feature/HeaderNavigationSettingsTest.php` — contract/regression assertions untuk struktur, density, dan tab mapping.

File yang tidak diubah:

- backend controller, request, model, migration, routes, dan frontend header renderer;
- Page Builder v2.0/v2.3 source, assets, widgets, renderer, dan tests;
- preview timeline behavior kecuali selector/layout compatibility yang diperlukan.

## Verification plan

1. Tambahkan regression assertions untuk tiga tab, tiga panel mapping, active state contract, dan Page Builder density values.
2. Jalankan test baru dalam kondisi RED sebelum CSS/Blade/JS implementation.
3. Implementasikan perubahan secara bertahap dan jalankan focused test GREEN.
4. Jalankan `php artisan test tests/Feature/HeaderNavigationSettingsTest.php`.
5. Jalankan `node --check public/assets/js/awesome-admin-header-navigation.js`.
6. Jalankan `php artisan view:cache` dan `git diff --check`.
7. Update Graphify secara incremental.
8. Jika browser authenticated tersedia, lakukan QA read-only pada desktop dan breakpoint kecil: tab switch, accordion open/close, responsive control, preview device, dan tidak ada horizontal overflow. Jangan menekan Save.

## Risks and rollback

- Risiko terbesar adalah selector lama `data-inspector-index` atau `focusInspectorSection()` masih mengasumsikan enam tab. Semua caller akan ditelusuri dan regression test akan mengunci mapping baru.
- Risiko kedua adalah grouped accordion menyembunyikan elemen yang masih diperlukan script. Semua `id` control tetap unik dan tetap berada di DOM aktif.
- Sebelum mengubah file existing, backup timestamped dibuat per file.
- Rollback dilakukan dengan memulihkan backup file yang terkait saja; perubahan user lain di worktree tidak disentuh.

## Acceptance criteria

- Sidebar kanan terasa seperti settings sidebar Page Builder v2.3, bukan lagi inspector enam kolom custom.
- Tiga tab `Layout`, `Style`, `Advanced` berfungsi dan mapping enam kategori terlihat jelas.
- Font, input, button, accordion, padding, dan spacing mengikuti density Page Builder tanpa control bertumpuk.
- Semua setting lama tetap mengubah preview dan config JSON seperti sebelumnya.
- Tidak ada perubahan pada v2.0/v2.3 Page Builder atau backend persistence.
- Test fitur, syntax check, Blade cache, diff check, dan Graphify update selesai tanpa failure baru.
