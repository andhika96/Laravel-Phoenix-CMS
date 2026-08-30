# QA Report 08 — Page Builder v2.4 Compiled Native

Tanggal verifikasi: 2026-08-29 02:15 WIB  
Project: `D:\Laragon\www\laravel-13-phoenix`  
Plan: `project-artifacts/plans/2026-08-28-pagebuilder-v24-compiled-native-implementation-plan.md`

## Kesimpulan

Mode **Compiled Native** sudah ditambahkan sebagai mode baru pada static import v2.4. Mode `native` tetap menjadi default API, `exact` tetap menjadi fallback, dan halaman manual tanpa metadata import tetap tidak berubah.

Compiled Native melakukan parsing/mapping native di server, mengirim compile document tersanitasi ke browser, menjalankan compiler hanya di iframe sementara, mengambil CSS framework dari iframe, menulis ulang selector ke marker native, lalu menyimpan hasilnya sebagai satu blok generated CSS di Custom CSS.

## Contract dan hasil

- Mode request: `native`, `exact`, `compiled`.
- Native import root mendapat `settings.importNodeKey` hanya jika token memenuhi `import-node-[A-Za-z0-9_-]+`.
- `classMap` mencakup class responsive, state, dan arbitrary seperti `lg:text-[6.2rem]`.
- `markerMap` memetakan marker source ke ID node native.
- `compilePayload.html` berisi DOM/source CSS tersanitasi tanpa source `<script>`.
- `exactFallback` dikembalikan pada response compiled sehingga fallback tidak perlu upload ulang.
- Layout final compiled tidak menyimpan class visual Tailwind/Bootstrap atau framework metadata; metadata internal hanya menyimpan `mode: compiled`.
- CSS block memakai `PHOENIX_STATIC_IMPORT_COMPILED_START/END`, metadata hash/statistics, dan `generatedBy: browser-utility-compiler` agar output final tidak meninggalkan marker framework.
- CSS manual di luar block dipertahankan; duplicate block diganti idempotently dan hash mismatch diberi warning.
- `--tw-*` menjadi `--pb-import-*`; URL framework dan comments framework dibuang dari CSS hasil compile, sedangkan URL source CSS yang aman tetap dipertahankan.
- Source script tetap dibuang pada compiled/native; tidak ada source JavaScript yang dieksekusi.
- Relative image/path tetap masuk report `relativeAssets` dan tidak diunggah ke File Manager V2.

## Browser compiler

`public/js/pagebuilder_elementor_v24/static-import-compiler.js` menyediakan `window.PhoenixStaticImportCompiler.compile(payload, options)`.

- Tailwind CDN hanya dimuat di iframe ephemeral `sandbox="allow-scripts"`.
- Bootstrap hanya mengambil URL tetap `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css`, lalu meng-inline hasilnya di iframe.
- Source arbitrary scripts tidak pernah ditempel ke iframe compiler.
- Compiler memakai `postMessage` dengan request ID, timeout 12 detik, AbortController, dan cleanup `finally`.
- CSS diambil dari text stylesheet compiler, direwrite dengan media/pseudo/compound/comma selector, lalu divalidasi.

## Editor UX

- Toolbar mempertahankan `Exact Visual` dan `Editable Native`, serta menambah `Compiled Native`.
- Modal progress menampilkan `Reading source`, framework detection/loading, compile, extract, rewrite, validate, cleanup, dan completed.
- Import kedua dinonaktifkan saat proses aktif.
- Cancel meng-abort compiler dan mempertahankan layout serta Custom CSS sebelumnya.
- Failure menampilkan `Use Exact Visual` dari payload fallback tersanitasi.
- Success hanya mengubah draft Canvas/Custom CSS dan menandai dirty; tidak auto-save.
- Custom CSS editor menampilkan summary/badge generated block.

## Test evidence

Lulus:

- `node --test tests/pagebuilder-v24-*.test.mjs` — **423/423 lulus**, exit code 0.
- Compiler helper Node tests — selector rewrite, arbitrary variant, CSS validation, Bootstrap allowlist, success cleanup, cancellation cleanup: lulus.
- CSS block manager Node tests — empty CSS, user CSS preservation, duplicate/malformed block, forbidden marker, hash mismatch: lulus.
- Focused PHPUnit compiled/static/frontend suite — **48 tests, 8.513 assertions lulus**.
- Compiled flow PHPUnit — response payload, exact fallback, no auto-save contract, dan fixture CEO Masters: lulus.
- Migration/policy tests dari pekerjaan Custom JavaScript sebelumnya tetap lulus.
- `node --check` untuk `app.js`, compiler, dan CSS manager: lulus.
- PHP syntax checks dan `php artisan view:cache`: lulus.
- `git diff --check`: lulus.

Filter terbaru `php artisan test --filter=PageBuilderElementorV24` menghasilkan **189 pass dan 33 failure**; seluruh 33 failure adalah baseline POST CSRF harness HTTP 419. Tidak ada failure non-419 pada scope compiled-native.

Full `php artisan test` terbaru menghasilkan **725 pass dan 68 failure**. Failure tetap berupa baseline POST CSRF harness HTTP 419 serta satu v2.3 shell authentication 302; tidak ada failure production baru yang terkait Compiled Native. Suite menghapus 14 fixture SVG tracked sebagai side effect; seluruhnya dipulihkan dari `HEAD` setelah verifikasi path.

## Fixture CEO Masters

Fixture aktual yang diprobe:

`E:\Apps\Laragon\www\ceo-masters\index.html` — 36.948 bytes.

Server compiled probe membuktikan:

- framework Tailwind terdeteksi;
- native layout dan compile payload terbentuk;
- `compileEligibleNodes`, `classMap`, `relativeAssets`, serta `exactFallback` tersedia;
- layout final tidak mengandung CDN Tailwind, `grid-cols-*`, atau `lg:` visual class.

Browser smoke fixture lokal menghasilkan:

- stages lengkap: `prepare`, `load-framework`, `compile`, `extract`, `rewrite`, `validate`, `cleanup`;
- 7 source classes, 48 generated rules, 7 rewritten rules;
- valid CSS block;
- compiler iframe tersisa: `0` setelah cleanup.

Browser smoke memakai fixture QA lokal, bukan authenticated builder page. Warning console Tailwind menyatakan CDN tidak untuk production; ini expected karena CDN hanya digunakan sebagai compiler sementara. Satu error favicon 404 berasal dari fixture lokal dan tidak memengaruhi compile.

## Manual/frontend isolation

- Frontend compiled tidak memuat `cdn.tailwindcss.com` atau Bootstrap CDN.
- Frontend manual tanpa compiled metadata tidak memuat compiler assets.
- Marker frontend hanya muncul dari `importNodeKey` valid.
- Canvas dan frontend native memakai marker yang sama di root semantic.
- Text Editor hanya mempertahankan nested marker/id tersanitasi pada compiled node; sanitizer manual tetap memakai allowlist lama.
- v2.3 tetap memakai renderer dan asset boundary sendiri.

## Backup dan recovery

Backup timestamp compiled-native dibuat sebelum perubahan source pada controller, request, import service, resolver, app, CSS, editor shell, frontend renderer, layout/widget Canvas/frontend, dan test terkait dengan suffix:

`20260829_031500_compiled_native`

Backup tambahan untuk asset isolation test:

`20260829_043000_compiled_native_assets`

Plan backup:

`project-artifacts/plans/2026-08-28-pagebuilder-v24-compiled-native-implementation-plan.md.bak_20260829_021500_compiled_native_complete`

Rollback dapat dilakukan dengan menonaktifkan mode `compiled`, mengembalikan file terkait dari backup, dan mempertahankan `native`, `exact`, serta data Custom CSS/user CSS. Tidak ada `git reset`, penghapusan backup, commit, push, deploy, atau migration ke database live.

## Graphify dan batasan

Graphify di-update incremental/raw dengan `graphify update . --no-cluster`; hasil terbaru **20.844 nodes dan 38.437 edges**. Backup, QA, generated output, dan secrets dikecualikan melalui `.graphifyignore`. Warning 56 metadata/source files tanpa node tetap merupakan karakteristik extractor dan tidak memblokir jalur compiled.

Belum dilakukan:

- authenticated browser QA pada halaman builder production karena tidak ada session browser terkontrol yang aman;
- perbandingan visual desktop/tablet/mobile penuh pada page builder authenticated;
- penerapan migration ke database development/live.

File Manager V2/ZIP asset upload tetap di luar scope sesuai keputusan user.
