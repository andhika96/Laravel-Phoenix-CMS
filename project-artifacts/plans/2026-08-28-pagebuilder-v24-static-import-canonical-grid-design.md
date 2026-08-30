# Page Builder v2.4 Static Import Canonical Grid Design

- Tanggal: 2026-08-28
- Status: scope disetujui untuk implementasi inline
- Baseline: `bc662a9946039bfea1d60f7049e08e5c423cad88`

## Tujuan

Memperbaiki struktur layout dan responsive grid hanya pada converter static HTML Page Builder v2.4 agar class Tailwind dan Bootstrap 5 menghasilkan node canonical yang langsung dapat dikonsumsi Canvas yang sudah ada.

## Scope

- Tokenize class HTML sebelum melakukan deteksi framework; `flex-row` tidak boleh dianggap sebagai Bootstrap `.row`.
- Tailwind:
  - `grid`, `grid-cols-N`, `sm:grid-cols-N`, `md:grid-cols-N`, `lg:grid-cols-N`, dan arbitrary track `grid-cols-[...]`.
  - `gap-*`, `gap-x-*`, `gap-y-*`, `p-*`, `px-*`, `py-*`, `pt-*`, `pr-*`, `pb-*`, dan `pl-*` pada layout wrapper.
  - Mapping responsive mobile/tablet/desktop mengikuti batas device v2.4 yang tersedia.
- Bootstrap 5:
  - `.container`, `.container-fluid`, `.row`, `.col`, `.col-N`, dan breakpoint columns `col-sm-N`, `col-md-N`, `col-lg-N`, `col-xl-N`, `col-xxl-N`.
  - `.row` menghasilkan flex row yang dapat wrap dan column width tidak lagi hanya berlaku pada satu breakpoint.
- Grid container hasil import selalu memakai:
  - `settings.displayType = 'grid'`;
  - `settings.gridColumns`, `gridColumnsTablet`, `gridColumnsMobile` bila tersedia;
  - `settings.gridTemplateColumns` hanya untuk template arbitrary yang sudah divalidasi;
  - `settings.gridRows*` yang cukup untuk menampung child; dan
  - `columns` canonical berisi child dalam urutan row-major, tanpa loose `children` pada grid.
- Warning report tetap dipakai untuk class yang tidak dapat direpresentasikan.

## Non-goals

- Tidak mengubah `norm()`, `moveLooseGridChildrenIntoColumns()`, drag/drop, widget definitions, atau renderer global.
- Tidak mengimplementasikan CDN Tailwind/Bootstrap, asset ingestion File Manager, scoped custom CSS, JavaScript interaction, accordion, tabs, gallery, atau video.
- Tidak mengubah Page Builder v2.3.
- Tidak menyimpan page ke database, commit, push, atau deploy.

## Contract dan breakpoint

Converter mempertahankan payload yang sudah diterima Canvas. Untuk Tailwind dan Bootstrap yang mobile-first, converter memakai pemetaan coarse yang deterministik:

- class tanpa prefix menjadi mobile;
- `sm:` dan `md:` menjadi tablet bila ada;
- `lg:`, `xl:`, dan `2xl:` menjadi desktop bila ada;
- nilai yang tidak memiliki override memakai nilai terdekat yang tersedia;
- breakpoint Tailwind/Bootstrap yang tidak identik dengan breakpoint Canvas dicatat sebagai batasan di report bila kehilangan fidelity.

`gridTemplateColumns` hanya menerima token track dengan angka, unit CSS terbatas, nama fungsi grid yang dikenal, tanda kurung seimbang, dan tanpa `;`, `{`, `}`, quote, URL, atau custom property. Nilai arbitrary tidak boleh menjadi jalur CSS injection.

## Invariants

- Import baru tidak boleh membuat Canvas menebak jumlah grid menjadi tiga ketika sumber sudah menyatakan jumlah track.
- Child grid tidak boleh dipindahkan ulang oleh migrasi legacy karena payload import sudah canonical.
- HTML/ZIP, entry selection, script removal, URL safety, size limits, dan manual Save tetap berlaku.
- Existing saved layouts tanpa payload import harus menghasilkan perilaku yang sama.

## Acceptance criteria

1. Tailwind `flex flex-row` terdeteksi sebagai Tailwind saja.
2. Tailwind grid numeric dan arbitrary menghasilkan `columns` canonical serta responsive column counts.
3. Bootstrap `.row`/`.col-md-6` menghasilkan flex row/wrap dan width desktop/tablet/mobile yang konsisten.
4. Test importer lama tetap lulus dan test regression baru gagal sebelum production code diubah lalu lulus setelah implementasi.
5. Full v2.4 PHPUnit, Node static checks, PHP lint, view cache, dan `git diff --check` lulus.
6. Hanya jalur static import yang berubah; diff tidak menyentuh v2.3 atau global Canvas migration behavior.

## Rollback

- Sebelum edit, backup timestamp dibuat untuk setiap existing file yang dimodifikasi.
- Jika slice gagal, restore hanya file slice ke backup baru atau baseline `bc662a994...` setelah status user changes diverifikasi.
- Backup historis tetap dipertahankan.
