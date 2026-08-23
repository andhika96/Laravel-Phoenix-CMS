# Eksekusi Cleanup High-Confidence `public`, `resources`, dan `resources/views`

Tanggal: 2026-08-23  
Project: `D:\Laragon\www\laravel-13-phoenix`  
Approval: user menyetujui rekomendasi high-confidence dari audit 2026-08-23.  
HEAD awal: `62bcb370a79f4861b9c35ce296bce76b4a76df10` (`main`, sama dengan `origin/main`).

## Hasil

- 4.883 file disposable dihapus: 201.720.894 byte atau 192,38 MiB.
- 13 file artifact direlokasi ke `project-artifacts/mockups`: 221.088 byte atau 0,21 MiB.
- 425 directory kosong dibersihkan.
- 40 image publik confidence menengah dan 31 companion asset sengaja tidak disentuh.
- Tidak ada staging, commit, push, reset, checkout, atau clean.

## Backup dan recovery gate

- Backup root: `project-artifacts/backups/high-confidence-cleanup-20260823_202836/`
- Payload: `project-artifacts/backups/high-confidence-cleanup-20260823_202836/payload/`
- Manifest: `project-artifacts/backups/manifests/public-resources-views-high-confidence-20260823_202836.csv`
- Isi: 4.898 file, 201.943.492 byte atau 192,59 MiB.
- Verifikasi sebelum dan sesudah cleanup: 4.898/4.898 file tersedia, 0 SHA-256 mismatch.
- Seluruh source awal tracked dan cocok dengan Git `HEAD`, sehingga recovery kedua tetap tersedia melalui Git.
- Graphify aktif juga dibackup sebelum incremental update: 10 file/28,30 MiB, 0 mismatch.

## Penghapusan

- `public/phpinfo.php` dan `public/change.php`.
- Sebelas directory `public/assets/plugins/filemanager_v2.bak_20260726_*`.
- Bootstrap `5.3.3`, `5.3.6_source`, `5.3.8`, `5.3.8_custom`, dan `5.3.8_source`.
- ECharts `5.6.0`.
- Font Awesome `5.15.4`.
- `public/assets/plugins/datatables`.
- `public/assets/plugins/uikit-compatible-w-bootstrap`.
- 169 generated rendition di `public/assets/pagebuilder_elementor/renditions`.
- `resources/views/filemanager/filemanager.blade.zip`.

## Relokasi

- Tiga file `public/prototypes/` -> `project-artifacts/mockups/pagebuilder-labs/`.
- Empat RAW email template -> `project-artifacts/mockups/email-template-references/`.
- `dashboard/newTheme.blade.php`, `homepage/homepage_test.blade.php`, dan `welcome.blade.php` -> `project-artifacts/mockups/legacy-view-prototypes/`.
- `pagebuilder/dataType.txt` dan `pagebuilder/style.css` -> `project-artifacts/mockups/legacy-pagebuilder-assets/`.
- `themes/calm_green/frontend/asdasds.txt` -> `project-artifacts/mockups/theme-references/calm-green/`.
- Semua 13 destination cocok dengan SHA-256 source di manifest backup.

## Ignore generated output

- `.gitignore`: `/public/assets/pagebuilder_elementor/renditions/`
- `.graphifyignore`: `public/assets/pagebuilder_elementor/renditions/`
- `git check-ignore` mengonfirmasi file rendition baru akan di-ignore.

## Safety incident dan recovery

Percobaan recursive delete pertama ditolak safety gate sebelum berjalan. Executor penghapusan file kemudian berhasil menghapus 4.883 file satu per satu setelah memvalidasi manifest, tetapi tahap directory kosong berhenti karena `Sort-Object -Unique` menduplikasi berdasarkan panjang path. Tidak ada file di luar target yang terhapus. Versi executor dibackup dengan SHA-256, urutan deduplikasi diperbaiki, dan finalizer terpisah menghapus 425 directory yang telah diverifikasi tidak mengandung file.

Script audit:

- `project-artifacts/scripts/execute-public-resources-views-high-confidence-cleanup.ps1`
- `project-artifacts/scripts/finalize-public-resources-views-empty-directories.ps1`

## Build

Build dilakukan ke output QA terisolasi agar output runtime aktif tidak ditimpa:

- Vite default: 58 modul, lulus; 3 output file.
- File Manager V2: 89 modul, lulus; 44 output file.
- Output: `project-artifacts/qa/public-resources-views-cleanup-20260823_202836/build/`, 47 file/1,59 MiB.

## Automated tests

- Focused PHP: 58 test lulus, 10.482 assertion.
- Full PHP: 661 lulus, 1 gagal, 18.963 assertion. Kegagalan tetap baseline `PageBuilderElementorV23ShellTest` karena route auth menghasilkan 302, bukan regresi cleanup.
- Full Node: 787 test; 781 lulus, 6 gagal. Keenamnya tetap baseline Arunika Aurora/Prism.
- `git diff --check`: lulus.

## Browser QA read-only

- Page Builder v2.4 editor: 48 widget terlihat (2 layout, 9 basic, 15 general, 22 pro), 0 warning/error console. Tidak menekan Save/Preview atau mengubah data.
- Public MG5 v2.3: title benar, heading `MG 5 GT`, 27 image, 11 section, Specifications dan Price tersedia, 0 warning/error console.
- Prototype publik v2.3 yang dipertahankan: HTTP 200, 74 button, canvas nodes tersedia, 0 warning/error console.
- File Manager V2: root tampil, CSS/JS aktif termuat, listing tampil, 0 warning/error console. Tidak mengubah file/storage/settings.
- URL `phpinfo.php`, `change.php`, prototype lama, ECharts 5.6.0, backup File Manager V2, dan rendition probe: HTTP 404.
- ECharts 5.5.1, build File Manager V2 aktif, public mockup, dan halaman MG5: HTTP 200.

## Graphify

- Official updater: `scripts/graphify/update-from-git.ps1 -Event manual-test` berhasil.
- Graph akhir: 20.078 node, 35.038 edge.
- Source node yang masih menunjuk path lama: 0.
- Health check: 0 missing-endpoint edge dan 0 self-loop.
- HTML graph tidak dibuat karena graph melewati batas 5.000 node.
- 53 JSON deklaratif menghasilkan zero node; ini warning extractor, bukan kegagalan cleanup.

## Kondisi Git akhir

- 4.896 tracked file tercatat deleted: 4.883 dihapus dan 13 direlokasi ke path baru yang masih untracked sampai ada staging eksplisit.
- 2 tracked file modified: `.gitignore` dan `.graphifyignore`.
- 64 file untracked mencakup artifact relokasi, audit/script, dan build QA.
- Staged file: 0.

## Batas yang dipertahankan

- 40 image publik dan 31 companion asset confidence menengah belum dibersihkan.
- `public/mockups`, `public/build`, `public/storage`, CKFinder, active plugin versions, Page Builder v2.0/v2.3/v2.4, dan File Manager V2 aktif dipertahankan.
- Bug lama `notification.notification_filled` yang hilang dan mismatch `pagebuilder.editor`/`pagebuilder.pagebuilder_edit` tidak diperbaiki karena berada di luar scope cleanup.
- `php artisan route:list` tidak dijalankan karena baseline controller API Testing yang hilang.
