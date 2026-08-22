# Page Builder v2.4 Modular Refactor — QA Report

Tanggal: 2026-08-23 (Asia/Jakarta)

## Hasil

Page Builder v2.4 tetap terisolasi dari v2.3 dan sekarang ditemukan dari 49 folder modul mandiri: 47 modul tampil di toolbox, sedangkan `container_fluid` dan `row_grid` tetap terdaftar sebagai compatibility module tersembunyi. Test katalog membuktikan pemindahan folder modul keluar dari root menghilangkan modul tersebut dan pengembalian folder mendaftarkannya kembali.

Dalam scope yang benar-benar dijalankan, tidak ada error aplikasi yang masih teramati. Klaim ini didasarkan pada sweep Settings seluruh toolbox, audit control-binding, test Canvas/frontend/runtime, test drag-and-drop, full regression, build, syntax check, browser console, dan checksum isolasi v2.3.

## Defect yang ditemukan dan diperbaiki

- 18 Pro `Settings.vue` memiliki nested `<template>` inert hasil ekstraksi, sehingga panel terlihat kosong. Template dinormalisasi dan generator ekstraksi sekarang menolak pola tersebut.
- URL SFC tanpa ekstensi membuat `vue3-sfc-loader` menolak response HTTP 200. Asset Canvas/Settings sekarang memakai URL `.vue` yang benar.
- Progress Tracker memakai `direction` untuk dua konsep. `indicatorAlignment` sekarang mengatur indicator secara independen pada Canvas dan frontend.
- Media Carousel tidak memakai `thumbsSlidesToShow*`, `thumbsRatio`, dan `centeredSlides`. Canvas dan frontend/runtime sekarang menghormati ketiganya.
- Video Playlist belum memiliki dropdown nyata, icon-state lengkap, dan pemetaan background section yang benar. Canvas dan frontend/runtime sekarang memiliki toggle, ARIA state, alignment, icon library/SVG, warna Normal/Hover/Active, serta background none/classic/gradient.
- Sanitizer shadow 18 Pro module menolak zero length tanpa unit (`0 2px ...`). Grammar 18 Canvas dan 18 frontend diperbaiki tanpa menerima nonzero tanpa unit.
- Image Box kiri/kanan memberi width ke `<img>` tetapi bukan flex media track, sehingga image keluar dari widget/grid. Media track sekarang memiliki width/flex-basis responsif dan image horizontal memakai `width:100%`.
- Native drag nested tertahan oleh `-webkit-user-drag:none`, dan ancestor Sortable dapat mencuri node ketika pointer melintasi header Accordion. Native drag diaktifkan hanya untuk node Sortable aktif dan ancestor capture diblokir selama pointer masih berada pada owner Tabs/Accordion.
- `Slides Name` adalah control hidup tetapi sebelumnya tidak memiliki consumer. Nilai tersebut sekarang menjadi accessible carousel label pada Canvas dan frontend.
- Context menu sekarang ditutup oleh klik kiri di Canvas kosong maupun sidebar/luar Canvas, tanpa memilih item menu.

## Runtime Settings sweep

- 47/47 toolbox module dibuka satu per satu.
- 140/140 kategori Content/Layout, Style, dan Advanced memiliki tinggi serta konten nyata.
- Spacer secara kontrak hanya memiliki Content + Advanced; modul lain memiliki tiga kategori.
- Tidak ada `Loading widget settings` yang macet.
- Tidak ada `Unable to load widget settings`.
- Matriks per modul: [settings-runtime-matrix.md](./settings-runtime-matrix.md).

## Audit control-binding

`project-artifacts/scripts/audit-pagebuilder-v24-control-bindings.mjs` memeriksa 49 modul dan 1.635 control token dari `Settings.vue` terhadap:

- logic Settings;
- Canvas;
- frontend Blade;
- module runtime;
- editor host;
- backend action Form.

Hasil akhir: 0 control tanpa consumer. Audit awal yang hanya menghitung keyword menghasilkan false positive dari helper component, responsive suffix, repeater field, dan key backend dinamis; hasil mentah tersebut tidak dipakai sebagai dasar kelulusan.

## Binding dan behavior berisiko tinggi yang diverifikasi runtime

- Progress Tracker: root tetap `align-center` ketika indicator Alignment diubah ke Right; computed margin indicator berubah ke sisi kanan.
- Media Carousel: skin Slideshow menghasilkan thumbnail track; per-view 2, ratio 1:1, dan centered offset berubah pada Canvas.
- Video Playlist: dropdown open/close mengubah `aria-expanded` dan visibility; alignment Center mengubah `justify-content`; pointer-events aktif.
- Image Box: posisi Left dan Right mempertahankan media di dalam root tanpa horizontal overflow; contract image intrinsik diverifikasi pada SSR/PHP.
- Slides: input `Slides Name` berubah menjadi `Browser QA Slides` dan Canvas `aria-label` berubah pada frame yang sama.
- Alert dan Rating: Canvas keduanya berhasil dirender ulang setelah FastCGI dipulihkan, tanpa fallback atau console error baru.
- Context menu: buka dengan klik kanan, tutup dengan klik kiri Canvas kosong; buka ulang, tutup dengan klik kiri sidebar.
- Accordion nested DnD: Button dipindahkan item 1 → item 2 dan kembali item 2 → item 1, ID tetap sama dan hanya satu copy tersisa.

## Drag-and-drop dan nested contract

- Root insertion, same-list reorder, cross-parent insertion, Grid column ownership, cycle prevention, owner-scoped Form Row Grid, Tabs, dan Accordion diuji oleh regression suite.
- Focused child-container suite: 30/30 pass.
- Browser Accordion cross-item menggunakan drag diagonal agresif untuk menutup celah ancestor-capture, lalu reverse drag; keduanya pass.
- Native drag override dibatasi pada `.pb-dropzone > .pb-node[draggable="true"]`, bukan seluruh Canvas.

## Automated verification akhir

- Node v2.4: 374 passed, 0 failed.
- PHP Feature + Unit v2.4: 144 passed, 0 failed, 10.022 assertions.
- SFC parse/compile di dalam Node suite: 98 component (49 Canvas + 49 Settings).
- Control-binding audit: 49 module, 1.635 control token, 0 consumerless.
- JavaScript syntax: 92 file, 0 failed.
- PHP syntax: 73 file, 0 failed.
- `npm.cmd run build`: pass, 58 modules transformed.
- `git diff --check`: pass.
- Browser stability: tiga reload berurutan masing-masing menampilkan 47 cards, empty unsaved Canvas, dan zero warning/error baru.

## Isolasi v2.3

- Snapshot baseline: 258 file.
- Snapshot current: 258 file.
- Baseline SHA-256: `0cbfbbac09d627181e0aea0274256ffa8bdace6882696fe57226bdfbc48a804c`.
- Current SHA-256: `0cbfbbac09d627181e0aea0274256ffa8bdace6882696fe57226bdfbc48a804c`.
- `Compare-Object`: 0 line berbeda.

## Runtime environment

Saat sweep, Nginx sempat mengembalikan 502 karena kedua FastCGI listener Laragon (9003 dan 9004) tidak berjalan; proses Nginx sendiri tetap hidup. Dua PHP-CGI worker PHP 8.5.6 dihidupkan kembali dan kedua port diverifikasi listening. Sesudah itu Alert/Rating, tiga reload berurutan, dan final reload semuanya pass. Pesan missing SNMP MIB muncul pada stderr startup PHP-CGI, tetapi tidak masuk ke browser console dan tidak berasal dari v2.4.

## Graphify

Incremental update lokal selesai:

- 19.511 nodes;
- 33.936 edges;
- 1.460 communities;
- `graphify-out/graph.json` dan `GRAPH_REPORT.md` diperbarui;
- HTML visualization dilewati otomatis karena graph melebihi limit 5.000 nodes.

## Backup utama

- `project-artifacts/backups/pagebuilder-v24-settings-render_20260823_004419`
- `project-artifacts/backups/pagebuilder-v24-runtime-bindings_20260823_015302`
- `project-artifacts/backups/pagebuilder-v24-pro-shadow-zero_20260823_021007`
- `project-artifacts/backups/pagebuilder-v24-slides-name_20260823_030343`
- `project-artifacts/backups/pagebuilder-v24-control-binding-audit_20260823_030635`
- `project-artifacts/backups/pagebuilder-v24-final-reports_20260823_031356`

## Batas verifikasi

- Browser QA tidak menekan Save, Reset, submit Form, atau Apply Dataset agar tidak mengubah data pengguna. Persistence, form submission, dan dataset contract diverifikasi oleh PHP regression.
- Media picker tidak menghasilkan modal pada sesi QA Image Box, sehingga intrinsic image dipastikan melalui Canvas SSR dan frontend PHP test, sementara browser mengukur media track/overflow.
- Semua control sudah diperiksa memiliki consumer, semua panel default/conditional source sudah dikompilasi, dan behavior berisiko tinggi diuji runtime. Ini bukan pembuktian matematis bahwa semua kombinasi data masa depan mustahil memiliki bug; ini adalah status tanpa defect yang diketahui pada scope dan evidence di atas.
