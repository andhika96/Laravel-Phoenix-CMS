# Page Builder v2.4 — Runtime UI/UX QA

Tanggal: 2026-08-23

## Status

Runtime editor v2.4 kembali normal setelah refactor modular pada scope yang diuji. Semua 47 modul toolbox menampilkan Settings yang benar, Canvas module dapat dimuat, shared Advanced tetap satu sumber, context menu mengikuti outside-click behavior, dan nested drag/drop tidak lagi dicuri ancestor.

## Bukti visual/runtime

- Toolbox: Layout 2, Basic 9, General 15, Pro 21; total 47 visible module.
- Settings: 47 module, 140 kategori, zero empty/inert panel, zero loading hang, zero load-error.
- Shared Advanced: tampil pada Layout, Grid, dan widget sesuai profile/capability masing-masing.
- Browser final reload: empty unsaved Canvas, 47 cards, zero warning/error baru.
- Tiga reload stabil berurutan: 47/47 cards pada setiap reload, tanpa 4xx/5xx atau console error baru.
- Context popup menutup pada dua jalur yang diminta pengguna: klik kiri Canvas kosong dan klik kiri sidebar.

## Binding runtime yang diamati

| Area | Mutation | Bukti Canvas |
|---|---|---|
| Progress Tracker | Alignment → Right | indicator margin berpindah kanan, root direction tidak berubah |
| Media Carousel | thumbs 2, ratio 1:1, centered | CSS vars, width thumbnail, dan track transform berubah |
| Video Playlist | dropdown toggle/alignment | `aria-expanded`, list visibility, active class, dan `justify-content` berubah |
| Image Box | image position Left/Right | media flex-basis responsif, tidak keluar root, zero horizontal overflow |
| Slides | Slides Name | Canvas `aria-label` berubah langsung |
| Context menu | click di luar popup | popup hilang tanpa action menu |
| Accordion nested | drag lintas item dua arah | ID tetap, target owner berubah, duplicate count tetap satu |

## Responsive dan nested

- Responsive key/fallback diuji pada Grid, Container, shared Advanced, Image, Image Box, carousel family, dan Pro widgets melalui SSR/unit/runtime contract.
- Grid menggunakan column-owned children; Container menggunakan child list; Form Row Grid memakai owner-scoped persistent columns.
- Cycle guard dan ancestor-capture guard lulus regression.

## Catatan environment

Satu 502 terjadi ketika dua process FastCGI Laragon sudah tidak listening. Setelah listener 9003/9004 dipulihkan, retest dan reload stability pass. Warning missing SNMP MIB hanya muncul pada stderr bootstrap PHP-CGI, bukan pada aplikasi/editor.

## Matriks lengkap

Lihat [settings-runtime-matrix.md](./settings-runtime-matrix.md) untuk hasil per modul dan jumlah control yang visible pada setiap kategori default.
