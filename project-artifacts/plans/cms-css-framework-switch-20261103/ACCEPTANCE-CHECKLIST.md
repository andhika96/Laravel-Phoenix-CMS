# Acceptance checklist — CMS Bootstrap/Tailwind

Status seluruh kotak di bawah: **rencana pengujian, belum lulus**. Audit baca-saja saat ini bukan bukti kelulusan migrasi. Case yang tidak berlaku harus memiliki alasan dan referensi scope.

## Konfigurasi, asset dan isolasi

- [ ] CFG-01 Bootstrap tetap default setelah migration; pengaturan lama tidak berubah.
- [ ] CFG-02 Hanya admin yang berwenang dapat mengubah framework; non-admin dan request tanpa CSRF ditolak.
- [ ] CFG-03 Nilai selain allowlist ditolak; tidak dapat memilih path/asset dari input bebas.
- [ ] CFG-04 Simpan Tailwind → reload → renderer dan pilihan aktif benar; kembali Bootstrap juga berhasil.
- [ ] CFG-05 Pilihan framework tidak mengubah theme code, font, palet CMS, primary auth atau data bisnis.
- [ ] CFG-06 Asset/manifest Tailwind hilang atau rusak → aktivasi ditolak dan setting lama dipertahankan.
- [ ] CFG-07 Konfigurasi invalid dapat pulih ke Bootstrap dengan pesan/log aktual; tidak menghasilkan halaman kosong.
- [ ] CFG-08 Cache/hard reload/deploy asset lama tidak menghasilkan campuran renderer dan stylesheet.
- [ ] CFG-09 Dokumen CMS Tailwind tidak memuat Bootstrap CSS/JS; iframe mandiri diperiksa sebagai dokumen berbeda.
- [ ] CFG-10 Satu runtime Vue per dokumen; tidak ada mount ganda pada elemen yang sama.
- [ ] CFG-11 Auth/frontend/editor/file manager tetap memuat asset mandirinya dan tidak berubah computed style.
- [ ] CFG-12 Tab lama tetap konsisten setelah setting global berubah; partial HTML mengikuti engine dokumen yang memintanya.

## Modal, native dialog, overlay dan focus

- [ ] MOD-01 Buka/tutup dari tombol, API, keyboard dan backdrop sesuai konfigurasi tiap modal.
- [ ] MOD-02 Static backdrop/keyboard-disabled tidak berubah menjadi dismissible.
- [ ] MOD-03 Initial focus, trap Tab/Shift+Tab, screen-reader label dan focus return benar.
- [ ] MOD-04 Trigger yang terhapus setelah delete mempunyai fallback focus yang masuk akal.
- [ ] MOD-05 Modal berantai/previous modal tidak menumpuk backdrop atau kehilangan data sebelumnya.
- [ ] MOD-06 Native discard dialog di atas modal hanya menutup lapisan yang semestinya; Keep Editing mempertahankan input.
- [ ] MOD-07 Toast → modal close → reset form → previous modal berjalan dengan callback tepat satu kali.
- [ ] MOD-08 Repeated open/close 25 kali tidak menggandakan listener, request, timer atau elemen backdrop.
- [ ] MOD-09 Rapid open/close dan navigasi saat transisi tidak meninggalkan body terkunci/inert.
- [ ] MOD-10 Form state/editor tidak hilang karena perbedaan mount/unmount.
- [ ] MOD-11 Modal panjang, keyboard virtual mobile, body scroll dan sticky footer tidak memotong tombol utama.
- [ ] MOD-12 Overlay/portal memakai warna, font, ukuran dan z-index tema CMS yang tepat.

## Navigasi dan komponen interaktif

- [ ] NAV-01 Dropdown keyboard arrow/Home/End/Escape, fokus dan outside-click sesuai perilaku yang ditetapkan.
- [ ] NAV-02 Dropdown pada tepi viewport/table scroll tidak terpotong; submenu tidak menutup parent secara salah.
- [ ] NAV-03 Collapse/accordion nested mempertahankan state, aria-expanded dan callback selesai transisi.
- [ ] NAV-04 Sidebar route aktif/parent/submenu tepat, termasuk deep link dan navigasi kembali.
- [ ] NAV-05 Expanded/collapsed/mobile drawer, backdrop, Escape, resize dan focus/inert benar.
- [ ] NAV-06 Periksa batas 768/769 serta breakpoint grid aktual yang ditetapkan G0.
- [ ] NAV-07 Tabs mempertahankan keyboard/aria-selected dan me-resize chart/editor yang awalnya tersembunyi.
- [ ] NAV-08 Tooltip dapat muncul pada hover/focus, konten Vue baru terpasang dan mode sidebar collapsed.
- [ ] NAV-09 Toast sukses/gagal validasi, timer, pause/focus dan dismiss bekerja tanpa menutupi kontrol penting.

## List, pagination, data dan form

- [ ] DAT-01 Pagination kosong/satu halaman/banyak halaman/ellipsis/first-last/current-disabled benar.
- [ ] DAT-02 Search/filter/sort mengirim endpoint dan parameter yang sama serta reset halaman sesuai baseline.
- [ ] DAT-03 Request cepat/terlambat/error tidak menampilkan data filter/page lama atau men-submit dua kali.
- [ ] DAT-04 Page size, total, rentang hasil, query tersimpan/back navigation tetap benar.
- [ ] DAT-05 Row detail, checkbox, select-all/indeterminate dan tombol aksi tetap independen.
- [ ] DAT-06 Header aksi, pagination compact dan table/card mobile tetap jelas pada label panjang/unicode.
- [ ] DAT-07 Validasi server/field, required, aria-invalid/describedby dan focus error pertama tepat.
- [ ] DAT-08 Nilai input, checkbox/radio, readonly, disabled, autofill dan form payload tidak berubah.
- [ ] DAT-09 Submit loading/success/error memulihkan tombol dan field sesuai baseline.
- [ ] DAT-10 Semua HTML yang dibuat via JS mendapatkan skin yang sama dengan render awal.
- [ ] DAT-11 Hak read/add/update/delete memengaruhi aksi dan server response seperti sebelumnya.
- [ ] DAT-12 CRUD hanya pada fixture/transaksi test; data pengguna tidak dihapus atau ditimpa demi QA.

## Plugin dan integrasi modul

- [ ] PLG-01 Vue Select: search, clear, keyboard, multiple/disabled dan value binding tetap.
- [ ] PLG-02 VueDatePicker: tanggal/jam, locale, clear, format payload, min/max dan popup di modal tetap.
- [ ] PLG-03 Coloris: input format, alpha sesuai kebutuhan, focus, Escape dan popup layer tetap.
- [ ] PLG-04 CKEditor: toolbar, editor content, paste/upload integration, resize dan input tidak hilang.
- [ ] PLG-05 CKFinder: dialog/iframe focus, callback hasil picker dan close-parent behavior tetap.
- [ ] PLG-06 Sortable/vuedraggable: urutan, drag handle, touch, scroll dan data hasil sama.
- [ ] PLG-07 SimpleBar: scroll, hidden-content resize, collapsed sidebar dan mobile tidak bertentangan.
- [ ] PLG-08 ECharts/jsVectorMap: ukuran, legend, tooltip, dark colors dan resize setelah panel dibuka benar.
- [ ] PLG-09 Article Template preview iframe/options modal mempertahankan device sizing, draft dan dirty guard.
- [ ] PLG-10 Page Builder/File Manager mandiri tetap dapat dibuka/dipilih/ditutup dari CMS tanpa CSS bocor.

## Visual, aksesibilitas, browser dan kinerja

- [ ] VIS-01 Enam tema × dua framework × light/dark mempertahankan identitas dan hierarchy.
- [ ] VIS-02 Font Site Config dan typography adaptif sama; label, input, tombol dan heading tidak membesar tanpa alasan.
- [ ] VIS-03 Palet delapan warna dan terang/gelap/matte diuji pada tiap state serta semantic danger/success/warning.
- [ ] VIS-04 Kontras teks dan focus indicator sesuai target; aktif pada tinted background tidak dipaksa putih.
- [ ] VIS-05 Tidak ada overflow horizontal/clipping/wrap tak diinginkan pada viewport dan zoom matriks.
- [ ] VIS-06 Screenshot dengan data/font deterministik telah direview; perbedaan geometry di luar toleransi diselesaikan.
- [ ] VIS-07 Keyboard, screen-reader semantics, reduced motion, touch target dan keyboard virtual diperiksa.
- [ ] VIS-08 Chromium, Firefox, WebKit serta perangkat fisik yang disepakati memiliki bukti; yang belum diuji dicatat.
- [ ] VIS-09 Tidak ada page error, missing asset, duplicate-init/request atau warning framework baru yang belum ditangani.
- [ ] VIS-10 Budget bytes/network/startup/interaksi mengikuti baseline dan batas yang disepakati G0.

## Release dan recovery

- [ ] REL-01 Seluruh route CMS dalam ledger mempunyai hasil dan bukti pada dua engine; kasus skipped beralasan.
- [ ] REL-02 Seluruh masalah migrasi dalam scope selesai; baseline issue dicatat terpisah dengan bukti sebelum perubahan.
- [ ] REL-03 Backup hash, migration reversible, bundle Bootstrap working dan instruksi recovery tersedia.
- [ ] REL-04 Rollback drill dari Tailwind ke Bootstrap diuji setelah skenario gagal asset/config.
- [ ] REL-05 Review source mempertahankan perubahan user dan tidak menambahkan dependency/abstraksi yang tidak diperlukan.
- [ ] REL-06 Pilihan Tailwind baru dibuka setelah gate lulus; commit/push/deploy tidak dilakukan tanpa instruksi.
- [ ] REL-07 Laporan akhir membedakan statis, browser fixture, runtime nyata dan hal yang belum diverifikasi; Graphify manual.
