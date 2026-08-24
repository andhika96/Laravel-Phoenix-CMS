# Audit Keamanan Defensif dan Bug Laravel 13 Phoenix

- Tanggal audit: 24 Agustus 2026 (Asia/Jakarta)
- Project: `D:\Laragon\www\laravel-13-phoenix`
- Branch/commit saat baseline: `main` / `16c62863264f81cffe2b17b6af31775255fec42c`
- Mode: read-only terhadap source dan data aplikasi
- Severity: triage defensif, bukan skor CVSS formal

> **Koreksi pasca-validasi:** klaim awal bahwa akses listing/upload CKFinder anonim telah terbukti terlalu kuat. Runtime anonim memang menerima `Init` dengan `enabled=true` karena callback autentikasi selalu `true`, tetapi respons tersebut memiliki **0 resource type**. Akses browse/upload anonim tidak terbukti. Temuan CKFinder tetap valid sebagai authentication boundary yang fail-open, lalu telah diperbaiki dalam laporan eksekusi terpisah.

## Ringkasan eksekutif

Audit awal mengelompokkan lima risiko kritis, sepuluh risiko tinggi, lima risiko menengah, dan empat kelompok bug correctness. Setelah validasi tambahan, CKFinder dikoreksi dari klaim akses resource anonim menjadi authentication boundary fail-open tanpa bukti resource anonim efektif. Risiko paling mendesak lainnya tetap installer web publik, kredensial cloud hard-coded di source tracked, rantai signup → Page Builder → stored XSS, serta rantai File Manager lama yang secara kondisional dapat berujung eksekusi PHP.

Tidak ada eksploitasi destruktif yang dilakukan. Audit tidak membuat akun, tidak login memakai kredensial pengguna, tidak memanggil `setup/process`, tidak upload/rename/delete file, tidak menyimpan setting, dan tidak mengirim email atau webhook. Validasi runtime dibatasi pada GET/HEAD, dispatch kernel in-memory, render Blade in-memory, query database agregat, dan route probe in-memory.

## Kondisi aktual yang mengurangi atau memperbesar risiko

- Runtime lokal: `APP_ENV=local`, debug aktif, nginx `1.28.1`.
- Rate limit login dan signup aktif dengan batas 7, tetapi implementasinya diperiksa setelah autentikasi/pembuatan akun sehingga tidak efektif sebagai pengaman awal.
- reCAPTCHA login dan signup nonaktif.
- `management_menu` aktif pada mode `v2`.
- Database memiliki 57 akun; 43 ber-role `General Member`.
- Database memiliki 27 Page Builder records; satu published, dan tidak ada page yang saat ini dimiliki General Member.
- Tabel legacy File Manager API key saat ini berisi 0 key. Kerentanan API-key tetap aktif di source ketika key pertama dibuat.
- Reverb notification saat ini nonaktif.
- nginx menolak payload traversal HTTP yang diuji dengan status 400, tetapi kernel Laravel sendiri terbukti melayani file di luar storage root ketika request tersebut mencapai aplikasi.

## Temuan kritis

### CRIT-01 — Installer web publik dapat mengubah `.env` dan menjalankan instalasi CMS

**Bukti**

- `routes/experimentalFeaturesWebv2.php:87-95` mendaftarkan `/setup` dan `setup/process` tanpa `auth`, permission, environment gate, atau installation lock.
- `app/Http/Controllers/Web/Setup/Setup_Controller.php:24-85` menerima konfigurasi database, menjalankan `env:set`, lalu `install:cms`.
- Route collection menunjukkan middleware `web` saja untuk GET dan POST.
- GET `/setup` merespons HTTP 200 tanpa autentikasi.

**Dampak**

Pengunjung anonim dapat memperoleh session dan CSRF token miliknya sendiri dari halaman setup. CSRF tidak menggantikan autentikasi. Ia kemudian dapat mengganti koneksi database dan menjalankan seeder instalasi, yang berpotensi mengambil alih atau merusak instalasi.

**Rekomendasi**

Hapus installer dari HTTP production. Jalankan instalasi melalui CLI. Jika web installer benar-benar harus dipertahankan, gunakan installation lock yang dibuat di luar database target, environment allowlist, one-time signed secret, rate limit, audit log, dan fail-closed setelah instalasi pertama.

### CRIT-02 (dikoreksi) — Callback autentikasi CKFinder fail-open; akses resource anonim tidak terbukti

**Bukti**

- `public/assets/plugins/ckfinder/config.php:83-86` menetapkan callback `authentication` yang selalu `return true`.
- Request anonim ke connector `Init` merespons HTTP 200 dan `enabled=true`, tetapi mengembalikan 0 resource type.
- Browse/upload anonim tidak dijalankan dan tidak terbukti dapat mencapai resource apa pun pada ACL aktif saat baseline.
- CSRF protection aktif, tetapi CSRF hanya mencegah request lintas-origin dari session korban; CSRF bukan autentikasi bagi klien yang membuat session-nya sendiri.

**Dampak**

Callback tersebut tidak membuktikan identitas pengguna dan merupakan boundary fail-open. ACL aktif saat baseline menahan akses resource anonim, tetapi perubahan ACL/resource type di kemudian hari dapat mengubah kelemahan laten ini menjadi akses file tanpa autentikasi.

**Rekomendasi**

Nonaktifkan connector sampai callback autentikasi benar-benar mengikat Laravel user/session, role, dan user scope. Default harus `false` ketika session/identity tidak lengkap. Tambahkan integration test anonim 401/403 dan test role/resource-type ACL.

### CRIT-03 — Tiga pasang kredensial cloud tersimpan hard-coded di file tracked

**Bukti**

- `public/assets/plugins/ckfinder/config.php:125-165` memuat tiga field access key dan tiga field secret sebagai literal.
- File tersebut dilacak Git. Nilai rahasia sengaja tidak disalin ke laporan ini.

**Dampak**

Siapa pun yang pernah memperoleh repository atau history berpotensi mengakses bucket sesuai izin key. Menghapus nilai dari HEAD saja tidak mencabut key yang sudah bocor.

**Rekomendasi segera**

Cabut dan rotasi keenam credential value terlebih dahulu. Audit log provider dan kebijakan bucket. Pindahkan credential ke environment/secret manager, gunakan identity berumur pendek bila tersedia, dan batasi permission serta bucket/prefix. Setelah rotasi, bersihkan Git history bila repository pernah dibagikan.

### CRIT-04 — Signup publik dapat berantai menjadi stored XSS pada page publik

**Bukti**

- `routes/web.php:67-86` membuka signup publik; GET `/auth/signup` merespons 200 dan reCAPTCHA runtime nonaktif.
- `routes/pagebuilder_elementor_v24.php:26-45` hanya memakai `auth` dan `checkSuspended`; tidak ada role/capability gate.
- Dispatch kernel dengan akun `General Member` membuktikan `/pagebuilder-elementor/v2.4/create` merespons 200.
- `AddPageBuilderElementorV24Request::authorize()` selalu `true`; `pageStatus` hanya diwajibkan tanpa enum/capability check.
- `PageBuilderElementorV24Controller.php:96-118` menyimpan layout dan status dari request.
- `resources/pagebuilder_elementor_v24/modules/widgets/basic/text-editor/frontend.blade.php:56` merender setting `html` dengan `{!! !!}`.
- Render Blade in-memory membuktikan elemen `<script data-audit>` dipertahankan utuh.
- `PageBuilderElementorPublishedPageController.php:12-30` mempublikasikan page berstatus `publish` pada `/pages/{uri}`.

**Dampak**

Dengan signup terbuka, penyerang dapat memperoleh akun General Member, membuat page, menyimpan JavaScript, dan mempublikasikannya di origin CMS. Stored XSS pada origin yang sama dapat mengambil tindakan sebagai pengunjung/admin yang membuka page.

**Rekomendasi**

Pisahkan capability `pagebuilder.view`, `pagebuilder.create`, `pagebuilder.publish`, dan `pagebuilder.raw_html`. Gate route dan policy secara server-side. Sanitasi HTML dengan allowlist server-side untuk role biasa. Bila raw HTML memang fitur admin, batasi hanya role tepercaya dan tambahkan CSP nonce tanpa `unsafe-inline`.

### CRIT-05 — Rantai legacy File Manager berpotensi menghasilkan eksekusi PHP

**Status:** terverifikasi statis; eksekusi PHP tidak dicoba.

**Bukti**

- API signup/login terbuka dan dapat menghasilkan Passport token untuk Account.
- `routes/filemanager.php:23-68` memberi semua operasi file kepada setiap identity yang lolos `FileManagerAuth`; tidak ada role check untuk operasi file biasa.
- `FileManagerController.php:967-980` menolak ekstensi PHP saat upload.
- `FileManagerController.php:1016-1043` mengizinkan rename tanpa memeriksa forbidden extension, sehingga file non-PHP dapat diubah menjadi `.php`.
- Disk `public` mengarah ke `storage/app/public` (`config/filesystems.php:39-45`). `public/storage` saat ini merupakan junction ke lokasi tersebut dan server aktif adalah nginx.
- Permission API key maupun role pengguna tidak ditegakkan oleh controller operasi.

**Dampak**

Pada konfigurasi nginx/PHP yang mengeksekusi file PHP di bawah junction `public/storage`, pengguna terautentikasi dapat mengupload payload dengan ekstensi aman lalu merename menjadi `.php` dan memanggilnya melalui web.

**Rekomendasi segera**

Nonaktifkan legacy File Manager sampai gate dan upload policy diperbaiki. Larang perubahan ekstensi saat rename atau jalankan forbidden-extension check pada nama akhir. Simpan upload di luar web root, sajikan lewat controller sebagai attachment/non-executable content, dan blok eksekusi script pada seluruh storage path di nginx. Tambahkan authorization policy per aksi dan disk.

## Temuan tinggi

### HIGH-01 — Middleware `permission` fail-open

- `app/Http/Middleware/ArunaPermission.php:245-347` hanya menolak ketika record permission tertentu ditemukan; jika record current/previous URI tidak ada, request diteruskan.
- Probe route in-memory dengan akun `General Member` dan middleware `permission:delete data` merespons 200.
- `routes/web.php:179` bahkan mengikat operasi hapus file cover image ke GET dan middleware yang dapat fail-open.

Perbaiki middleware agar tidak adanya role, menu mapping, permission row, previous route, atau malformed JSON selalu menghasilkan 403. Permission harus ditentukan dari current action/resource, bukan `Referer`/previous URL.

### HIGH-02 — Filter webhook dapat dibypass dengan DNS/private resolution

- `app/Support/PageBuilderElementorV24/FormSubmissionHandler.php:430-443` hanya memblokir host yang sudah berupa literal private IP.
- Hostname tidak di-resolve dan redirect tidak dinonaktifkan sebelum `Http::post()` pada line 87.
- Bukti aman: hostname publik-form `127.0.0.1.nip.io` bukan literal IP bagi filter, tetapi resolve ke `127.0.0.1`.
- Implementasi setara terdapat pada v2.0 dan v2.3.

Resolve seluruh A/AAAA record, tolak private/reserved/link-local/metadata/IPv6 transition address, pin koneksi ke IP yang sudah divalidasi, nonaktifkan redirect, dan ulangi validasi setiap redirect bila redirect sengaja diizinkan. Terapkan egress allowlist/firewall.

### HIGH-03 — Semua General Member mendapat akses penuh File Manager V2

- `routes/filemanager_v2.php:17-47` melindungi semua endpoint hanya dengan `auth` dan `checkSuspended`.
- `FileManagerV2Controller.php:26-64` menyediakan baca/tulis settings dan connection test; line 102-285 menyediakan rename, upload, move, delete, preview, dan download.
- Dispatch kernel dengan akun General Member membuktikan halaman admin dan settings API sama-sama merespons 200.

Tambahkan policy/capability per aksi. Settings, credential, delete, dan connection test harus admin-only. Jika library bersifat multi-user, scope semua path, metadata, star, cache, upload session, dan quota ke owner/tenant.

### HIGH-04 — `/storage/{path}` memiliki traversal di application layer

- `routes/web.php:40-55` menggabungkan input route langsung ke `storage_path('app/public/'.$path)` tanpa canonical containment check.
- Dispatch kernel untuk `/storage/../../../composer.json` dan encoded dot segments menghasilkan `BinaryFileResponse` 200 yang menunjuk project `composer.json`.
- nginx saat ini mengembalikan 400 untuk payload HTTP langsung yang diuji. Ini hanya mitigasi perimeter, bukan perbaikan aplikasi.

Hapus fallback ini dan gunakan disk Laravel dengan path normalizer. Jika route diperlukan, tolak dot segments/backslash/NUL, resolve `realpath`, lalu pastikan canonical target diawali canonical storage root. Tambahkan test melalui kernel dan server deployment.

### HIGH-05 — Rate limiter login dan signup terlambat sehingga dapat dilewati

- Web login menjalankan `Auth::attempt()` pada `Auth_Controller.php:79`; limiter baru diperiksa pada line 149-173 setelah attempt gagal.
- Signup membuat akun dan commit pada line 217-241; limiter baru diperiksa pada line 262-286.
- Pola yang sama terdapat di `app/Http/Controllers/Api/v1/Auth/Auth_Controller.php:50-214`.
- API route middleware hanya `api` + `guest`, tanpa global `throttle:api`.

Periksa limiter sebelum hashing/authentication dan sebelum transaksi signup. Hit limiter secara atomik per IP + normalized identifier, gunakan route throttle sebagai lapisan tambahan, dan clear hanya setelah login sukses.

### HIGH-06 — Password reset memakai generator non-kriptografis dan dapat dienumerasi

- Reset code dibuat dengan `random_string('alnum', 22)` pada `Auth_Controller.php:361` dan `:410`.
- `app/Helper/Common.php:1875-1906` mengimplementasikan alnum dengan `str_shuffle`, dan mode lain memakai `mt_rand`/`uniqid`.
- Forgot-password memberi respons berbeda untuk email ada/tidak ada dan tidak memiliki rate limit khusus.
- Token disimpan plaintext dalam row Account.

Gunakan `Password::sendResetLink()` bawaan Laravel atau minimal `random_bytes`, hash token di database, respons generik, single-use expiry, per-account/IP throttle, dan audit event tanpa token.

### HIGH-07 — Preview SVG File Manager V2 dapat menjadi stored XSS

- `config/filemanager_v2.php:66-69` tidak melarang SVG.
- `FileManagerV2Storage.php:992-1021` menerima semua `image/*`; jika thumbnail gagal, binary asli dikirim inline dengan MIME aslinya.
- SVG ber-script dapat dinavigasi langsung pada origin CMS.

Sanitasi SVG dengan parser allowlist atau larang SVG. Jangan pernah fallback mengirim SVG aktif inline; gunakan attachment, forced rasterization, CSP sandbox, dan `X-Content-Type-Options: nosniff`.

### HIGH-08 — Kontrak permission API key legacy tidak pernah ditegakkan

- `FmApiKey.php:13-31` menyimpan `allowed_disks`, `can_upload`, `can_delete`, `can_rename`, `can_move`, dan `can_create_folder`.
- Tidak ada controller operasi yang membaca `fmApiKey`, `allowed_disks`, atau helper `can()`.
- `FM_ALLOW_API_KEY` hanya didefinisikan di config dan tidak dibaca middleware.
- Key dapat dikirim sebagai query `fm_key`, sehingga dapat bocor ke access log/history/referrer.
- Secret HMAC dibuat dan disimpan, tetapi tidak pernah diverifikasi.
- Saat ini tabel berisi 0 key, sehingga jalur ini dormant sampai key dibuat.

Default-deny setiap aksi dan disk pada middleware/policy. Hapus query key, simpan hash key (bukan plaintext), gunakan secret/HMAC dengan timestamp+nonce bila memang diperlukan, dan hormati kill switch config.

### HIGH-09 — Dependency Node production memiliki 7 advisory aktif

`npm audit --omit=dev` menemukan 5 high dan 2 moderate:

- High: `form-data@4.0.5`, `nanoid@3.3.12`, `postcss@8.5.13`, `socket.io-parser@4.2.6`, `ws@8.18.3`.
- Moderate: `engine.io-client@6.6.4`, `esbuild@0.21.5`.

Semua memiliki fix tersedia menurut npm. Update lockfile secara terkontrol, hapus dependency transitive yang dipromosikan tanpa kebutuhan, lalu ulangi build, Node suite, dan audit. `esbuild` terutama berisiko bila dev server diekspos ke jaringan.

### HIGH-10 — Password SMTP disimpan dan ditampilkan plaintext

- `SMTP.php:18-31` memperlakukan password sebagai fillable biasa tanpa encryption cast/hidden.
- `SMTPListResource.php:15-18` mengembalikan seluruh model.
- `awesome_admin_smtp.blade.php:281`, `:316`, `:423`, dan `:526` menampilkan password di tabel atau input text.

Encrypt credential at rest, mask seluruh response/list, gunakan password input kosong dengan semantics “leave unchanged”, batasi reveal sepenuhnya, dan rotasi credential bila response/log pernah terekspos.

## Temuan menengah

### MED-01 — Open redirect setelah login melalui `Referer`

`Auth_Controller.php:56-69` menyimpan `url()->previous()` ke session, lalu line 116-145 me-redirect nilai tersebut. Origin/host tidak divalidasi. Simpan hanya relative internal path atau gunakan `redirect()->intended()` dengan target allowlist.

### MED-02 — Mutasi data memakai GET

- Logout: `routes/web.php:76-77`.
- Delete cover image file: `routes/web.php:179` dan controller line 503-519.
- Notification index melakukan `send_notification()` saat GET pada `Notification_Controller.php:26-34`.

Ubah seluruh mutasi menjadi POST/DELETE + CSRF + authorization. GET harus safe dan idempotent.

### MED-03 — Public Reverb channel dan DOM insertion tanpa escaping

- Event CMS memakai `new Channel('cms-notifications')`, bukan private/presence channel.
- Payload event berisi actor/name dan metadata email.
- `cms-realtime-notification.blade.php:224-251` memasukkan title/message ke `innerHTML`.
- Reverb saat audit nonaktif, sehingga risikonya dormant.

Gunakan private channel dengan authorization, minimalkan PII, dan bangun DOM dengan `textContent`.

### MED-04 — CORS/clickjacking legacy File Manager tidak aman

`FileManagerAuth.php:131-149` menggabungkan `Access-Control-Allow-Origin: *` dengan credentials, lalu mengizinkan framing dari semua origin. Query-key dan wildcard origin memperbesar dampak kebocoran key. Gunakan exact-origin allowlist, `Vary: Origin`, tanpa credentials untuk key-based clients, serta `frame-ancestors` allowlist.

### MED-05 — Header keamanan global tidak tersedia pada login lokal

HTTP response `/auth/login` tidak memiliki HSTS, CSP, X-Frame-Options/frame-ancestors, X-Content-Type-Options, Referrer-Policy, atau Permissions-Policy. Terapkan header di reverse proxy/middleware dengan CSP yang kompatibel dan test otomatis. HSTS hanya diaktifkan setelah seluruh domain benar-benar HTTPS.

## Bug correctness dan wiring

### BUG-01 — 15 route action tidak valid

Audit route collection menemukan 15 action dengan class atau method hilang. Contoh utama:

- Dua API benchmark menunjuk class `Api\v1\Testing\Testing_Controller` yang tidak ada.
- Tiga route Article menunjuk namespace `Web\Articles` yang tidak ada.
- `pagebuilder/data`, `pagebuilder/ads`, `setup/success`, dan `dashboard/menu` menunjuk method hilang.
- Dua `update_submenu`, `awesome_admin/role/test`, dua route User, dan `awesome_admin/smtp/testing` menunjuk method hilang.

`php artisan route:list --json` gagal pada missing API class. Request publik ke benchmark menghasilkan HTTP 500 dan, karena debug lokal aktif, error body sekitar 1,1 MiB.

### BUG-02 — Notification view hilang

`Notification_Controller.php:46-53` memanggil `notification.notification_filled`, tetapi `resources/views/notification/notification_filled.blade.php` tidak ada. Route `/notification/filled` akan gagal setelah autentikasi.

### BUG-03 — Error path authentication rusak

- API Auth memakai `RecaptchaV3` tanpa import; saat reCAPTCHA signup diaktifkan, jalur ini berpotensi menjadi 500.
- Web forgot-password menangkap `Exception` tanpa namespace/import pada line 459 dan memakai variabel `$th` yang tidak didefinisikan.
- Banyak catch mengembalikan exception message mentah ke klien.

### BUG-04 — Model API key tidak konsisten dengan guard Account

`FmApiKey::user()` mengarah ke `App\Models\User`, sedangkan aplikasi memakai `Awesome_Admin\Account`. Controller juga memvalidasi `exists:users,id`, tetapi guard API memakai provider `accounts`. Akibatnya key owner dapat resolve ke model/tabel yang salah dan global key fallback ke Account ID 1.

## Hasil dependency dan scanner

- `composer validate --strict`: valid dengan warning constraint exact `geoip2/geoip2` dan wildcard `torann/geoip`.
- `composer audit --locked`: tidak dapat memakai TLS lokal karena CA/Avast interception.
- Fallback read-only ke official Packagist Security Advisories API, difilter dengan Composer Semver terhadap seluruh versi locked: 0 advisory cocok.
- `npm audit --omit=dev`: 7 advisory (5 high, 2 moderate).
- Scanner `gitleaks`, `trufflehog`, `semgrep`, PHPStan, dan Psalm tidak tersedia. Secret scan memakai targeted tracked-source patterns; ini bukan pengganti scanner secret penuh.
- Dangerous-function scan hanya menemukan `unserialize` pada helper yang tidak memiliki caller aktif terdeteksi. Raw SQL yang ditemukan memakai expression statis.

## Hasil test dan verifikasi

- PHP lint: 726 file tracked diperiksa, 0 syntax error.
- Focused Page Builder/Form/File Manager V2: 118 test, 745 assertion, seluruhnya lulus.
- Full PHP: 661 lulus, 1 gagal, 18.963 assertion. Failure sama dengan baseline: `PageBuilderElementorV23ShellTest` mengharapkan 200 tetapi mendapat 302 karena tidak acting-as.
- Active Node suite (`tests/**`, 152 test files): 787 test; 781 lulus dan 6 gagal. Enam failure sama dengan baseline Arunika Aurora/Prism.
- Percobaan `node --test` tanpa scope dibatalkan karena Node ikut memindai backup di `project-artifacts`; hasil itu tidak dipakai. Rerun valid hanya menggunakan test aktif.
- Route integrity: 15 missing class/method.
- HTTP read-only: `/up` 200, `/setup` 200 anonim, CKFinder `Init` 200/enabled anonim dengan 0 resource type, legacy File Manager tanpa auth 401, File Manager V2 tanpa auth 302.
- Traversal HTTP langsung saat ini 400 oleh nginx; traversal kernel menghasilkan file response 200 di luar root.
- Permission probe General Member: 200 (fail-open).
- General Member access probe: File Manager V2 page 200, settings 200, Page Builder v2.4 create 200.
- Git status tetap bersih setelah seluruh scanner/test sebelum laporan ini dibuat; tidak ada source diff.

## Prioritas remediasi

### Dalam 0–24 jam

1. Nonaktifkan route setup dan CKFinder connector dari jaringan.
2. Rotasi seluruh credential cloud hard-coded dan audit provider logs/bucket policy.
3. Nonaktifkan legacy File Manager atau blok upload/rename PHP dan eksekusi script pada storage.
4. Tambahkan authorization fail-closed untuk Page Builder dan File Manager V2.
5. Tutup stored XSS raw HTML bagi General Member.

### Dalam 1–3 hari

1. Perbaiki middleware permission fail-open.
2. Perbaiki webhook SSRF dengan DNS/IP pinning dan egress control.
3. Pindahkan `/storage` ke resolver yang canonical dan contained.
4. Perbaiki rate limiter login/signup dan password reset token.
5. Update dependency npm yang terkena advisory.

### Dalam 1 minggu

1. Perbaiki 15 route action dan missing view.
2. Encrypt/mask SMTP credential.
3. Perbaiki CORS, framing, dan security headers.
4. Tambahkan test security permanen untuk semua finding kritis/tinggi.
5. Jalankan secret scanner dan SAST penuh di CI.

## Batas audit

- Audit dilakukan pada checkout lokal dan runtime lokal nginx, bukan deployment production.
- Tidak ada authenticated browser mutation QA.
- Tidak dilakukan POST installer, signup, login brute-force, upload, rename, delete, save, email, webhook, atau akses cloud.
- Credential yang ditemukan tidak diuji validitasnya.
- Potensi eksekusi PHP pada legacy storage tidak dieksekusi; klasifikasi didasarkan pada source flow, public storage junction, dan konfigurasi nginx/PHP umum.
- Traversal aplikasi terbukti di kernel, tetapi payload langsung yang diuji diblok nginx saat ini.
- Audit ini terarah pada attack surface berisiko tinggi dan bukan jaminan tidak ada bug lain.

## Memori, Graphify, backup, dan perubahan

- Memori resmi `E:\AI\Memories` tidak tersedia karena drive E tidak terpasang. Konteks pengganti dibaca dari local Codex memory untuk baseline dan CKFinder, lalu seluruhnya diverifikasi terhadap source/runtime aktif.
- Graphify aktif dan fresh terhadap commit: 20.092 nodes, 35.051 edges, `.needs_update` tidak ada.
- Query Graphify utama:
  - `authentication authorization permission middleware csrf validation sanitize upload filesystem session webhook security`
  - `pagebuilder authorization update request editor middleware validation data`
- Graphify dipakai sebagai peta. Semua finding final diverifikasi pada source atau runtime.
- Tidak ada backup source karena tidak ada file source existing yang dimodifikasi.
- File baru yang dibuat hanya laporan audit ini.
- Graphify tidak di-update incremental karena source code tidak berubah.
