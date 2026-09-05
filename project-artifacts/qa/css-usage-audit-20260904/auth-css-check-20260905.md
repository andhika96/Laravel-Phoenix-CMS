# Auth CSS Cross-check

Tanggal: 2026-09-05  
Scope: `login`, `signup`, `forgotpassword`, dan `recoveryaccount`.

## Verdict

Enam CSS di `public/assets/archived/css` tidak digunakan oleh halaman auth. Tidak ditemukan referensi basename maupun path archive pada auth layout, auth view, controller, route, database, atau response HTML auth yang berhasil diambil.

## Auth resolver

`app/Http/Controllers/Web/Auth/Auth_Controller.php` memilih view auth secara dinamis melalui `custom_page_theme()` lalu view tersebut mewarisi `themes.<active-theme>.auth.auth_layout`.

Page theme database saat pemeriksaan:

| URI | Page theme |
|---|---|
| `login` | `default` |
| `signup` | `split_left` |
| `forgotPassword` | `split_left` |
| `resetPassword` | `split_left` |

Layout auth yang tersedia dan CSS yang dimuat:

| Layout | CSS layout |
|---|---|
| `themes/arunika_aurora/auth/auth_layout.blade.php` | Calm Green compatibility + `phoenix-cms.css` |
| `themes/arunika_equinox/auth/auth_layout.blade.php` | Calm Green compatibility + `phoenix-cms.css` |
| `themes/arunika_lucent/auth/auth_layout.blade.php` | `phoenix-cms.css` + Lucent + responsive typography |
| `themes/arunika_prism/auth/auth_layout.blade.php` | Calm Green compatibility + `phoenix-cms.css` |
| `themes/calm_green/auth/auth_layout.blade.php` | Calm Green compatibility + `phoenix-cms.css` |
| `themes/default/auth/auth_layout.blade.php` | Default theme + `phoenix-cms.css` |

Tidak satu pun layout tersebut memuat:

- `aruna-admin-v6.css`;
- `aruna-admin-v7-phoenix-elegant.css`;
- `aruna-admin-v7-simple-part-2.css`;
- `aruna-admin-v7-simple.css`;
- `aruna-admin-v7.css`;
- `aruna-v3.css`.

## Runtime HTML verification

GET read-only ke host aplikasi menghasilkan status `200` untuk:

- `/auth/login`;
- `/auth/signup`;
- `/auth/forgotpassword`.

CSS yang benar-benar tercantum pada response auth tersebut hanya:

- `assets/css/themes/calm_green/phoenix-cms-calm-green.css`;
- `assets/css/phoenix-cms.css`;
- pada response historis/test tertentu, theme CSS aktif seperti Aurora, Lucent, atau Prism.

Tidak ada response auth yang memuat enam basename archive.

`/auth/recoveryaccount` mengembalikan `419` saat dipanggil tanpa recovery session/code yang valid. Ini adalah validasi alur token/session, bukan indikasi CSS; source route dan view tetap menggunakan resolver auth yang sama.

## Source scan

- Auth-related view tree: 16 page views untuk empat page theme.
- Referensi `assets/css` di auth page views: tidak ada; CSS berasal dari `auth_layout`.
- Pencarian enam basename archive pada auth views, auth layouts, auth controller, route, config, database, public JS, dan public asset JS: 0 hit.
- Scan 249 kolom text/JSON database untuk enam basename: 0 hit.
- Request access log auth: 292 CSS request lines, hanya enam CSS runtime auth unik; tidak ada enam basename archive.

## Kesimpulan

Pemindahan enam file ke `public/assets/archived/css` tidak memutus CSS auth login/signup/forgot-password. File yang tetap diperlukan auth berada di layout aktif, terutama `phoenix-cms.css`, `phoenix-cms-calm-green.css`, `phoenix-cms-default.css`, Lucent, dan responsive typography sesuai theme/layout yang dipilih.

