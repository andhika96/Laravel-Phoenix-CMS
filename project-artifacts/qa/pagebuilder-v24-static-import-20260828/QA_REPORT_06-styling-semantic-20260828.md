# QA Report 06 — Static Import Styling, Dependencies, and Semantic Mapping

Tanggal: 2026-08-28 22:40:15 WIB  
Project: `D:\Laragon\www\laravel-13-phoenix`  
Fixture: `E:\Apps\Laragon\www\ceo-masters\index.html`

## Scope

- Tailwind/Bootstrap 5 dependency metadata untuk halaman hasil import.
- Scoped custom CSS dari `<style>` sumber.
- Allowlist stylesheet eksternal yang aman.
- Konfigurasi tema Tailwind yang aman tanpa mengeksekusi script sumber.
- Semantic CTA, icon Phosphor ke Font Awesome lokal, dan heading `<br>`.
- URL gambar absolut dipakai langsung.
- File Manager V2/ZIP asset upload tidak termasuk scope; path relatif tetap unresolved.
- Halaman manual harus tetap menggunakan perilaku widget lama.

## Implementasi yang diverifikasi

- Import root menyimpan `settings.staticImport` berisi framework, stylesheet Google Fonts, dan konfigurasi tema Tailwind yang disaring.
- Tailwind CDN hanya dimuat untuk root hasil import, dengan `preflight: false` dan scope `important: '.pb-import-root'`.
- Bootstrap 5.3.3 hanya dimuat pada frontend halaman hasil import yang mendeteksi Bootstrap.
- CSS sumber dibersihkan dari `@import`, selector berbahaya, URL tidak aman, dan aturan at-rule yang tidak didukung; selector di-scope ke `.pb-import-root`.
- Class tema custom seperti `text-cream`, `bg-ink/95`, `border-line`, `shadow-gold`, dan family font diekstrak menjadi utility scoped.
- `preserveCssClasses` hanya aktif pada node hasil import. Sanitizer class halaman manual tetap jalur lama.
- `<a>` menjadi Button hanya bila `.btn`, class berakhiran `-button`, atau `role="button"`; link navigasi biasa tetap Text Editor.
- `<button>` menjadi Button; icon-only button tidak diberi label fiktif “Button”.
- Ikon Phosphor yang dikenal dipetakan ke class Font Awesome lokal yang finite dan aman.
- `<br>` pada heading menjadi line break opt-in melalui `sourceLineBreaks`.
- `<img src="https://...">` dipertahankan langsung; URL/path relatif tidak diupload dan dicatat sebagai `missingAssets`.

## Fixture probe

- Framework terdeteksi: `tailwind`.
- Scoped custom CSS: 5.632 byte, termasuk utility theme custom hasil ekstraksi aman.
- Konfigurasi tema Tailwind: 9 colors, 1 box shadow, 2 font families.
- Node: 301 mapped, 1 placeholder.
- Widget: 14 Button, 23 Icon, 23 Heading, 11 Image, 110 Text Editor, 121 Container.
- ID dipertahankan: 13.
- Class dipertahankan: 1.219.
- Gambar URL absolut: 3 dipakai langsung.
- Path relatif belum tersedia: 8.
- Script sumber tidak dieksekusi: 5 dropped.

## Test results

- PHPUnit terfokus: **22 passed, 136 assertions**.
- Static Node import tests: **4 passed, 0 failed**.
- Full Node v2.4: **404 passed, 0 failed**.
- Full PHPUnit v2.4 filter: **158 passed, 33 failed**; seluruh 33 gagal pada HTTP 419 CSRF sebelum assertion bisnis, konsisten dengan baseline harness sebelumnya. Tidak ditemukan failure non-419 dari perubahan import.
- `git diff --check`: lulus.
- PHP syntax check untuk processor, resolver, dan Button Blade: lulus.
- Browser QA read-only melalui preview fixture lokal: root, Tailwind script, Google Fonts, 14 CTA, 23 icon, 3 remote image, custom color/font, dan heading line break terverifikasi di DOM/computed style.
- Editor authenticated browser QA belum dapat dilakukan karena URL editor mengarah ke login dan sesi Chrome terhubung tidak tersedia. Tidak ada kredensial yang dimasukkan, tidak ada Save/Reset/Preview aplikasi yang ditekan.

## Backup

Backup dibuat sebelum perubahan pada setiap file source yang diedit, termasuk backup historis import grid/generic/visibility/classes-UTF8/styling-semantic serta backup bertimestamp untuk CSS processor, resolver, Button, Heading, app.js, frontend renderer, dan test.

## Status dan batasan

Implementasi styling/dependency/semantic pada jalur import sudah selesai dan terisolasi. Kesamaan visual fixture belum 100% karena path asset lokal relatif sengaja belum dipindahkan ke File Manager V2, beberapa utility arbitrer yang tidak memiliki padanan native masih mengandalkan class/CSS scoped, dan runtime interaksi script sumber memang tidak diimpor.

Graphify diperbarui incremental setelah perubahan source: 20.647 nodes dan 38.129 edges; backup, QA, dan graph lama dikecualikan oleh `.graphifyignore`.
