# Laporan Eksekusi: CKFinder Hardening dan Penghapusan File Manager Lama

- Tanggal: 24 Agustus 2026 (Asia/Jakarta)
- Project: `D:\Laragon\www\laravel-13-phoenix`
- Branch/commit awal: `main` / `16c62863264f81cffe2b17b6af31775255fec42c`
- Mode: perubahan source terarah, migration menu terarah, tanpa upload/delete file user
- Scope dikecualikan sesuai instruksi: File Manager V2 dan Page Builder tidak dihapus; Page Builder hanya disentuh pada session bridge CKFinder karena CKFinder masih dipakai oleh entry point-nya.

## Hasil singkat

CKFinder sekarang fail-closed: connector anonim tidak mendapat resource, role tidak dikenal ditolak, native session mempunyai user scope dan expiry, session incomplete dirotasi sebelum diberi claim autentikasi, dan logout membersihkan session CKFinder. Credential cloud hard-coded serta backend cloud yang tidak dipakai dihapus dari config aktif. General Member hanya memiliki resource `User Files` yang terscope ke UUID atau authenticated ID-nya; ACL bersama `Articles` dan `Events` untuk role tersebut dihapus.

File Manager lama dinonaktifkan dan source/runtime wiring-nya dihapus. Route legacy memberi 404, sedangkan File Manager V2 tetap terdaftar dan build/test-nya tetap lulus. Tabel dan data lama `fm_*` sengaja dipertahankan untuk recovery karena penghapusan data adalah tindakan berbeda dari penghapusan feature code.

## Perubahan CKFinder

### Session bridge baru

File baru: `app/Support/CkfinderSessionBridge.php`.

Bridge menjadi satu-satunya jalur aktif untuk menulis native PHP session CKFinder dari:

- `Manage_Article_Controller`
- Page Builder legacy Elementor controller
- Page Builder v2.3 controller
- Page Builder v2.4 controller

Field native yang ditulis:

- `CKFinder_UserId`
- `CKFinder_UserRole_UUID`
- `CKFinder_UserRole`
- `CKFinder_AuthExpiresAt`

Role yang diterima secara eksplisit:

- `Super Admin`
- `Administrator`
- `General Member`

Role lain, principal tanpa role provider, user tanpa identifier, UUID non-kosong yang invalid, atau session expired ditolak. UUID kosong memakai authenticated ID agar akun seeded lama tetap dapat memakai CKFinder. Fallback unknown/stale role ke Administrator dihapus. Native session ID hanya dipertahankan bila identity, scope, role, dan expiry lengkap serta masih cocok; session kosong/incomplete/different/expired wajib dirotasi sebelum claim baru ditulis dan gagal rotasi tetap fail-closed.

Logout web dan logout SSO memanggil `CkfinderSessionBridge::clear()` sebelum invalidasi session Laravel.

### Connector config

`public/assets/plugins/ckfinder/config.php` sekarang:

- memeriksa user ID, scope, role allowlist, dan expiry;
- mengembalikan `false` bila session tidak lengkap;
- memakai scope aman `A-Za-z0-9_-` untuk directory user;
- mengganti permission file lokal dari `0777` menjadi `0644`;
- menghapus tiga backend S3/S3-compatible yang tidak dipakai;
- menghapus credential cloud literal dari active config;
- membatasi General Member hanya ke `User Files`;
- menonaktifkan CKFinder debug (`false`).

License value lokal CKFinder masih dibiarkan karena dibutuhkan oleh connector; hal itu bukan credential cloud. Jika license deployment berbeda, pindahkan ke secret/config deployment sebelum production.

## Penghapusan File Manager lama

Source yang dihapus mencakup:

- API V1 File Manager, API-key controller, permission/metadata controller;
- `FileManagerAuth`, `FmAdminMiddleware`;
- `FmApiKey`, `FmFile`, `FmFolder`, `FmQuota`;
- `FileManagerService` dan event progress legacy;
- config `filemanager.php` dan `routes/filemanager.php`;
- route/view legacy `/filemanager`, `/file_manager`, dan Awesome Admin File Manager;
- dua bundle JS legacy Vue File Manager;
- migration/seeder pembuat/import legacy File Manager.

Wiring yang dihapus atau dibersihkan:

- require legacy route dari `routes/api.php`;
- route `/filemanager` dan thumbnail dari `routes/experimentalFeaturesWebv2.php`;
- route `/file_manager` dan Awesome Admin File Manager dari `routes/web.php`;
- breadcrumb dan shortcut dashboard legacy;
- alias middleware `fm.auth` dan `fm.admin`;
- seeded menu dan custom permission legacy.

Migration baru: `database/migrations/2026_08_24_103500_remove_legacy_file_manager_navigation.php`.

Migration itu hanya menghapus custom permission/menu navigation legacy. Ia tidak menghapus file user atau tabel `fm_*`.

## Data yang dipertahankan

Sebelum perubahan dibuat backup source dan snapshot database:

- Backup: `project-artifacts/backups/ckfinder-security-legacy-file-manager-removal-20260824_103148/`
- Manifest: `source-manifest.csv`
- Snapshot: `legacy-database-snapshot.json`
- File source dibackup: 48
- SHA-256 mismatch: 0
- Backup regresi final: `project-artifacts/backups/ckfinder-stale-role-regression-20260824_152826/`
- Backup temuan review final: `project-artifacts/backups/ckfinder-review-findings-20260824_154743/`

Backup source utama memuat salinan historis config CKFinder sebelum credential cloud dihapus. Folder backup harus diperlakukan sebagai data sensitif, dibatasi aksesnya, dan baru dipurge setelah rotasi provider serta kebutuhan recovery selesai.

Data runtime lama yang tidak disentuh:

- `fm_files`: 7 row
- `fm_folders`: 2 row
- `fm_quotas`: 1 row
- `fm_settings`: 4 row
- `fm_api_keys`: 0 row
- physical legacy storage: 21 file, sekitar 10 MB

File Manager V2 tidak disentuh dan tetap berada pada `config/filemanager_v2.php`, `routes/filemanager_v2.php`, `app/Services/FileManagerV2`, dan `resources/js/filemanager_v2`.

## Verifikasi keamanan CKFinder

### Runtime read-only

- `/up`: HTTP 200.
- `/filemanager`: HTTP 404.
- `/file_manager`: HTTP 404.
- `/awesome_admin/filemanager`: HTTP 404.
- `/api/v1/filemanager/browse`: HTTP 404.
- `/admin/file-manager-v2`: HTTP 302 ke login tanpa session; route tetap ada.
- connector CKFinder anonim: HTTP 400 dengan error connector; tidak ada resource type.
- synthetic native session General Member yang valid: HTTP 200, hanya resource `User Files`.
- synthetic General Member ke `All User Files` dan `Articles`: 0 resource type.

Tidak dilakukan upload, rename, delete, email, webhook, atau authenticated browser mutation.

### Regression tests baru

`tests/Feature/CkfinderSecurityTest.php` mencakup:

- role mapping allowlist dan unknown-role fail-closed;
- bridge menulis scope/expiry dan `clear()` mencabut field native;
- bridge menolak akun tanpa role meskipun Laravel session masih menyimpan role legacy yang privileged;
- bridge merotasi session native incomplete sebelum autentikasi tetapi mempertahankan session valid yang exact-match;
- UUID kosong fallback ke authenticated ID, sedangkan scope non-kosong invalid dan principal tanpa role provider ditolak;
- connector menolak session kosong/expired/role unknown;
- tidak ada AWS access key literal, backend cloud aktif, atau debug mode;
- seluruh entry point aktif memakai bridge, bukan direct `$_SESSION` writes.

`tests/Feature/LegacyFileManagerRemovalTest.php` mencakup:

- route legacy hilang sementara CKFinder dan V2 tetap ada;
- source legacy tidak ada;
- bootstrap/routes/views/seeders tidak lagi memuat wiring legacy.

## Hasil pengujian

- Focused security/removal/V2/Page Builder suite: **131 test, 913 assertion, 0 failure**.
- PHP lint baseline aktif: **706 file, 0 syntax error**. Setelah seluruh patch bridge final, `CkfinderSessionBridge.php` dan `CkfinderSecurityTest.php` di-lint ulang: **2 file, 0 syntax error**.
- Full PHP suite: **674 passed, 1 failed, 19.131 assertions**. Failure tetap baseline `PageBuilderElementorV23ShellTest` yang mengharapkan 200 tetapi menerima 302 karena test tidak melakukan `actingAs`; failure tidak berasal dari CKFinder/File Manager removal.
- Active Node suite: **787 test, 781 passed, 6 failed**. Enam failure tetap baseline Arunika Aurora/Prism.
- Default Vite build: exit 0, 58 modules.
- File Manager V2 build: exit 0, 89 modules.
- `git diff --check`: tidak ada whitespace error; ada warning line-ending CRLF pada `bootstrap/app.php`.
- Graphify incremental code update: **19.894 nodes, 34.332 edges, 1.461 communities, 0 missing/dangling endpoints, 0 self-loops, 0 duplicate/collapsed edges**. Visualization tidak dibuat karena graph melebihi batas 5.000 node.
- Independent read-only code review awal menemukan session fixation dan bug UUID kosong; keduanya diperbaiki test-first. Re-review final menyatakan tidak ada issue blocking pada bridge CKFinder.

## Celah dan bug yang masih harus diperbaiki

### Prioritas segera

1. **Rotasi credential cloud lama.** Credential sudah dihapus dari active CKFinder config, tetapi pernah berada di source/history. Rotasi provider, audit log, bucket policy, dan bersihkan Git history bila repository pernah dibagikan.
2. **Web installer publik.** `/setup` dan `setup/process` masih publik dan dapat mengubah `.env`/menjalankan instalasi. Pindahkan ke CLI atau kunci dengan installation lock dan environment gate.
3. **Permission middleware fail-open.** `ArunaPermission` masih meneruskan request saat mapping permission tidak ditemukan. Ubah menjadi fail-closed.
4. **Security headers.** Runtime `/login` belum mengirim CSP, HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, maupun Permissions-Policy. Tambahkan header tersebut dengan kebijakan yang sesuai.
5. **Authentication throttling.** Rate limit login/signup masih diperiksa terlambat; pindahkan check sebelum `Auth::attempt()`/pembuatan akun dan tambahkan throttle route.
6. **Password reset token.** Generator lama masih memakai `str_shuffle`/`mt_rand`/`uniqid`; ganti dengan Laravel Password Broker atau token kriptografis yang di-hash dan single-use.

### Ditunda sesuai scope

- File Manager V2: authorization per role/tenant, SVG preview, CORS/clickjacking, dan settings access masih belum diperbaiki karena user meminta V2 tetap development.
- Page Builder: raw HTML/stored XSS, publish capability, webhook DNS/SSRF, dan page-builder role gate belum diperbaiki karena user meminta Page Builder dikeluarkan dari scope.
- Legacy `fm_*` tables/files: feature code sudah dihapus, tetapi data fisik/schema dipertahankan. `storage/app/public/filemanager` masih berada di balik junction `public/storage`, sehingga file lama dapat tetap diakses bila URL-nya diketahui. Tidak ditemukan ekstensi executable pada 21 file yang dipertahankan, tetapi karantina ke storage non-public atau purge permanen memerlukan approval cleanup data terpisah.

### Bug correctness di luar removal scope

Route integrity masih menemukan 15 action class/method yang hilang, termasuk API benchmark, legacy Page Builder routes, setup success, dashboard menu, menu update, role test, user actions, dan SMTP testing. Ini tidak disebabkan perubahan CKFinder/File Manager dan perlu tiket remediasi terpisah.

## File modified/created utama

- `app/Support/CkfinderSessionBridge.php`
- `public/assets/plugins/ckfinder/config.php`
- `app/Http/Controllers/Web/Auth/Auth_Controller.php`
- `app/Http/Controllers/Web/Manage_Article/Manage_Article_Controller.php`
- tiga controller Page Builder untuk memakai shared CKFinder bridge
- `bootstrap/app.php`, `routes/api.php`, `routes/experimentalFeaturesWebv2.php`, `routes/web.php`, `routes/breadcrumbs.php`
- `database/migrations/2026_08_24_103500_remove_legacy_file_manager_navigation.php`
- cleaned `CustomPermissionsSeeder`, `MenuParentmenuJsonSeeder`, `MigrationsSeeder`
- dua regression test baru

## Batasan

Authenticated browser QA CKFinder belum dijalankan karena tidak menggunakan kredensial pengguna dan tidak melakukan perubahan file. Runtime session test menggunakan synthetic native PHP session untuk memverifikasi ACL boundary. `down()` migration navigation hanya memulihkan empat permission/menu default; variasi kustom harus direstore dari `legacy-database-snapshot.json`, sehingga rollback tidak lossless tanpa artifact tersebut. License key deployment dan status rotasi credential provider harus diverifikasi oleh operator yang memiliki akses secret/provider.
