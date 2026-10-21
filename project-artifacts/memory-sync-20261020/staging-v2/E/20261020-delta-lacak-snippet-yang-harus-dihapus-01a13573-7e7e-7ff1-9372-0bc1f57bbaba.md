# Lacak snippet yang harus dihapus

Tanggal penyimpanan: 20 Oktober 2026 (WIB)
Project: New MG [Laptop]
Project saat chat: D:\Laragon\www\newmg
Chat ID: 01a13573-7e7e-7ff1-9372-0bc1f57bbaba
Jenis sumber: codex-local
Rentang chat: 2026-10-13 20:28:43 WIB sampai 2026-10-13 21:49:50 WIB
Tindakan: new
Evidence SHA-256: 265e367d16d34b10df7ceb4fe23db5c1ef2dfdcf91b62d69dbd7c54dfd85c750

## Tujuan dan konteks awal

- ChatGPT saya disuruh remove beberapa snippet script code yang ada di file page.tpl.php dan dibeberapa file untuk dibeberapa halaman, tapi saya bingung dan agak susah men-trace snippet script code yang mana aja yang harus diremove, nah ini saya kirim filenya di file ini ada instruksi snippet script code apa saja yang harus diremove dari file page.tpl.php dan di beberapa file lainnya, kamu baca, analisa, cari dan hasilnya laporkan kepada saya tapi jangan kamu hapus dulu yah yang di file page.tpl.php dan di beberapa file lainnya
- Jadi script code apa saja yang ingin dihapus dan ada dihalaman apa saja?

## Keputusan dan implementasi historis

Laporan di bawah merupakan riwayat pekerjaan, bukan pengujian runtime terbaru pada tanggal penyimpanan. Checkpoint terbaru mengungguli pending dari laporan lebih lama.

- `mgTrackLead` / `mg_form_submit` belum ditemukan pada source aktif yang ditelusuri. Itu pekerjaan implementasi terpisah dalam brief, belum saya kerjakan.
- Tidak ada source yang dimodifikasi, tidak ada backup yang dibuat karena belum ada perubahan, dan tidak ada deployment.** Daftar perubahan Git tetap sama dengan kondisi awal pemeriksaan.
- Belum diperiksa: produksi `www.mgmotor.id`, browser Network/Pixel Helper, dan keberhasilan tracking setelah submission. Risiko saat implementasi nanti: brief mensyaratkan koordinasi publikasi GTM dengan agency agar perubahan tracking tidak menghasilkan event ganda.
- Daftar ini merujuk hasil pemeriksaan sebelumnya; saya belum melakukan penghapusan atau pemeriksaan ulang pada balasan ini. Saat diterapkan nanti, perubahan tracking perlu mengikuti jadwal agency/GTM yang disebut dalam brief.
- Sudah saya hapus snippet yang kita sepakati, dan backup tujuh file sudah dibuat sebelum perubahan.** Backup berisi file lengkap, sehingga bisa dipakai untuk mengembalikan kondisi sebelum penghapusan.
- Setiap file memakai nama `.bak_20261013_203823_tracking_cleanup`. **SHA-256 seluruh backup sudah diverifikasi identik dengan source sebelum perubahan.** Pemetaan lokasi file serta hash sebelum/sesudah tersedia dalam [manifest.json](D:/Laragon/www/newmg/project-artifacts/tracking-cleanup-20261013_203823/manifest.json).
- | File yang diubah | Penghapusan |
- Total perubahan source: **140 baris dihapus, tanpa penambahan kode aplikasi.** Termasuk di dalamnya delapan blok Floodlight `15941435` dan snippet Google Ads `AW-10865541694` / Floodlight `11735253` beserta fallback terkait.
- Pemeriksaan terhadap backup memastikan perubahan source hanya berupa penghapusan; isi baris lain dan urutannya tetap terjaga.
- Betul, nanti bisa di-restore dari backup ini.** Jika setelah pekerjaan ini ada perubahan lain pada file yang sama, kita perlu membandingkannya dahulu agar restore tidak menimpa perubahan terbaru.
- Memori NEWMG di drive C digunakan sebagai konteks; drive E tidak tersedia. Graphify digunakan untuk penelusuran, lalu dicocokkan dengan source aktif. **Data graph tidak diperbarui**, sesuai instruksi Anda. Perubahan lama di repository tetap dipertahankan; tidak ada commit, push, atau deployment.
- Maksud saya: **yang sudah selesai adalah menghapus tracking lama yang tadi kita sepakati.** Sementara itu, file `.md` juga berisi pekerjaan tambahan untuk memasang tracking baru dan merapikan script lain. Bagian tambahan tersebut belum saya kerjakan karena permintaan Anda saat itu adalah menghapus snippet setelah membuat backup.
- Pada pemeriksaan sebelumnya, HTML lokal sudah menunjukkan satu loader Maps per halaman, tetapi loader umum masih disertakan pada banyak halaman. **Pengaturan pemuatan tersebut belum diubah.
- Library-nya tidak diminta dihapus.** Perubahan URL/version ini belum dikerjakan.
- Rujukan tambahan tersedia di transkrip kronologis.

## Checkpoint substantif terakhir

Tanggal checkpoint: 2026-10-13 21:49:38 WIB

Berikut poin dalam **`developer-brief.md`** yang terkait penghapusan script dan **sudah dikerjakan**:

- ✅ **Bagian 3 — Nomor 1: Floodlight tags `15941435`**  
  Menghapus blok **Landing page** dan **Lead Done** dari Test Drive, Promotion, serta kedua halaman thank-you.

- ✅ **Bagian 3 — Nomor 3: Firebase Analytics**  
  Menghapus import `getAnalytics`, pemanggilan `getAnalytics(app)`, dan `window.analytics` dari `page.tpl.php`. Firebase Auth tetap dipertahankan.

- ✅ **Bagian 1.2 dan Bagian 4: Meta Lead pada pembukaan halaman**  
  Menghapus `fbq('track', 'Lead');` dari layout umum. Meta Pixel dasar dan `PageView` tetap dipertahankan.

- ✅ **Bagian 4: Google Ads lama `AW-10865541694`**  
  Menghapus snippet conversion dari enam halaman thank-you: Test Drive, Promotion, MG Care, Contact, Request Quote, dan Book.

- ✅ **Bagian 4: Floodlight langsung di kode halaman**  
  Menghapus snippet `DC-11735253` dan fallback `<noscript>` terkait dari enam halaman thank-you tersebut. **ID ini tidak disebut khusus dalam brief**; penghapusannya mengikuti larangan umum Floodlight di kode halaman.

**Khusus tabel Bagian 3, nomor yang sudah dihapus adalah: ✅ Nomor 1 dan ✅ Nomor 3.**

Checklist ini mencerminkan perubahan lokal yang sudah diverifikasi sebelumnya; status deployment AWS belum diperiksa.

## File dibuat, dimodifikasi, atau dirujuk

- add (status completed): D:\Laragon\www\newmg\project-artifacts\tracking-cleanup-20261013_203823\verify.py
- Add: D:/Laragon/www/newmg/project-artifacts/tracking-cleanup-20261013_203823/verify.py

- Sudah saya baca [developer-brief.md](C:/Users/aruna/Downloads/developer-brief.md) dan telusuri ke source aktif di `D:\Laragon\www\newmg`. **Belum ada source code yang saya hapus atau ubah.
- Hasil utamanya: **target penghapusan berada di `page.tpl.php` dan enam controller halaman/form.** Ada juga bagian Google Maps dan library CDN yang perlu dirapikan, tetapi tidak boleh langsung dihapus seluruhnya.
- 1. Di `page.tpl.php`, ada empat baris utama yang diminta dihapus
- File: [themes/default/page.tpl.php](D:/Laragon/www/newmg/themes/default/page.tpl.php:53).
- | **Baris 578** | `import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-analytics.js";` | Hapus import Firebase Analytics. |
- import { initializeApp } from ".../firebase-app.js";
- import { getAuth, signInWithPhoneNumber, RecaptchaVerifier } from ".../firebase-auth.js";
- Snippet ini **bukan berada langsung di `page.tpl.php`**. Controller memasukkannya melalui `register_js()`, lalu layout menampilkannya melalui `load_js()` di [page.tpl.php baris 902](D:/Laragon/www/newmg/themes/default/page.tpl.php:902). Ini yang membuat penelusurannya agak membingungkan.
- | `/testdrive` — `index()` | [testdrive.php](D:/Laragon/www/newmg/modules/testdrive/testdrive.php:84) | **84–100** | **101–110** |
- | `/testdrive/thankyou` — `thankyou()` | [testdrive.php](D:/Laragon/www/newmg/modules/testdrive/testdrive.php:376) | **376–392** | **393–402** |
- | `/promotion` — `index()` | [promotion.php](D:/Laragon/www/newmg/modules/promotion/promotion.php:72) | **72–88** | **89–98** |
- | `/promotion/thankyou` — `thankyou()` | [promotion.php](D:/Laragon/www/newmg/modules/promotion/promotion.php:554) | **554–570** | **571–580** |
- | `/testdrive/thankyou` | [testdrive.php](D:/Laragon/www/newmg/modules/testdrive/testdrive.php:373) | **373–375** |
- | `/promotion/thankyou` | [promotion.php](D:/Laragon/www/newmg/modules/promotion/promotion.php:551) | **551–553** |
- Rujukan tambahan tersedia di transkrip kronologis.

## Backup historis

- Tidak ada source yang dimodifikasi, tidak ada backup yang dibuat karena belum ada perubahan, dan tidak ada deployment.** Daftar perubahan Git tetap sama dengan kondisi awal pemeriksaan.
- Sudah saya hapus snippet yang kita sepakati, dan backup tujuh file sudah dibuat sebelum perubahan.** Backup berisi file lengkap, sehingga bisa dipakai untuk mengembalikan kondisi sebelum penghapusan.
- Backup tersimpan di:
- [**D:\Laragon\www\newmg\project-artifacts\tracking-cleanup-20261013_203823\backups**](D:/Laragon/www/newmg/project-artifacts/tracking-cleanup-20261013_203823/backups)
- Setiap file memakai nama `.bak_20261013_203823_tracking_cleanup`. **SHA-256 seluruh backup sudah diverifikasi identik dengan source sebelum perubahan.** Pemetaan lokasi file serta hash sebelum/sesudah tersedia dalam [manifest.json](D:/Laragon/www/newmg/project-artifacts/tracking-cleanup-20261013_203823/manifest.json).
- Pemeriksaan terhadap backup memastikan perubahan source hanya berupa penghapusan; isi baris lain dan urutannya tetap terjaga.
- [Hasil verifikasi source dan backup](D:/Laragon/www/newmg/project-artifacts/tracking-cleanup-20261013_203823/verification.json)
- Betul, nanti bisa di-restore dari backup ini.** Jika setelah pekerjaan ini ada perubahan lain pada file yang sama, kita perlu membandingkannya dahulu agar restore tidak menimpa perubahan terbaru.
- Maksud saya: **yang sudah selesai adalah menghapus tracking lama yang tadi kita sepakati.** Sementara itu, file `.md` juga berisi pekerjaan tambahan untuk memasang tracking baru dan merapikan script lain. Bagian tambahan tersebut belum saya kerjakan karena permintaan Anda saat itu adalah menghapus snippet setelah membuat backup.
- Yang sudah saya periksa adalah **sintaks PHP, source code, backup, dan HTML dari server lokal**. Pemeriksaan tersebut belum membuktikan event benar-benar terkirim dan diterima Meta/TikTok.
- Hindari `git add .` karena ada perubahan lain di working tree. **Backup dalam `project-artifacts` dan cache Graphify tidak perlu ikut commit.** Contoh pesan commit:
- Backup lengkap sebelum penghapusan tersedia di:
- [D:\Laragon\www\newmg\project-artifacts\tracking-cleanup-20261013_203823\backups](D:/Laragon/www/newmg/project-artifacts/tracking-cleanup-20261013_203823/backups)

## Pengujian dan verifikasi historis

- 2. Floodlight `15941435` ditemukan di Test Drive dan Promotion, termasuk kedua halaman thank-you
- | `/testdrive` — `index()` | [testdrive.php](D:/Laragon/www/newmg/modules/testdrive/testdrive.php:84) | **84–100** | **101–110** |
- | `/testdrive/thankyou` — `thankyou()` | [testdrive.php](D:/Laragon/www/newmg/modules/testdrive/testdrive.php:376) | **376–392** | **393–402** |
- | `/testdrive/thankyou` | [testdrive.php](D:/Laragon/www/newmg/modules/testdrive/testdrive.php:373) | **373–375** |
- `/testdrive`
- | Moment JS | [page.tpl.php baris 638](D:/Laragon/www/newmg/themes/default/page.tpl.php:638) | Ganti URL `momentjs/latest/...` dengan versi pasti atau aset lokal. |
- | Vue Select CSS | [page.tpl.php baris 115–116](D:/Laragon/www/newmg/themes/default/page.tpl.php:115) | Ganti `vue-select@latest` pada kedua URL. |
- Memori NEWMG di drive C digunakan untuk konteks historis saja karena `E:\AI\Memories` tidak dapat diakses. Graphify digunakan melalui query `page tpl testdrive promotion`; hasilnya memuat beberapa node historis sehingga kesimpulan selalu saya cocokkan dengan file aktif. Hash `page.tpl.php` juga sudah berbeda dari manifest graph. **Graph tidak diperbarui**, sesuai instruksi Anda; query dapat menyentuh cache penelusuran, tetapi bukan source aplikasi.
- Verifikasi runtime terbatas pada respons HTML lokal**, yang mengonfirmasi keberadaan Lead, Firebase Analytics, dan Floodlight pada halaman terkait. Saya belum membuktikan event terkirim atau diterima platform iklan. Homepage mengalami timeout saat pemeriksaan HTTP. Halaman thank-you diperiksa dari source tanpa mengirim formulir.
- Halaman terdampak:** seluruh halaman publik yang memakai layout tersebut, termasuk Test Drive, Promotion, News, halaman model, dan Dealer.
- | **Test Drive** — `/testdrive` | [modules/testdrive/testdrive.php](D:/Laragon/www/newmg/modules/testdrive/testdrive.php:84) | **84–110** |
- | **Test Drive Thank You** — `/testdrive/thankyou` | [modules/testdrive/testdrive.php](D:/Laragon/www/newmg/modules/testdrive/testdrive.php:376) | **376–402** |
- | **Test Drive Thank You** — `/testdrive/thankyou` | [modules/testdrive/testdrive.php](D:/Laragon/www/newmg/modules/testdrive/testdrive.php:373) | **373–375** |
- | **Moment JS, Date Range Picker, Vue Select CSS** | Ganti URL tanpa versi/`latest` menjadi versi pasti atau aset lokal. Library tetap digunakan. |
- Rujukan tambahan tersedia di transkrip kronologis.

## Masalah belum selesai dan langkah lanjutan

- Checklist ini mencerminkan perubahan lokal yang sudah diverifikasi sebelumnya; status deployment AWS belum diperiksa.

Untuk melanjutkan, verifikasi kembali Git/source/runtime aktif, gunakan Graphify sebagai peta teknis, dan backup sebelum perubahan. Jangan mengulangi mutasi database/deploy hanya berdasarkan arsip.

## Laporan terbaru lain yang relevan

### 2026-10-13 20:51:54 WIB

**Perubahan lokal sudah siap dari sisi penghapusan snippet dan pemeriksaan yang sudah dijalankan.** Namun, sebelum Anda pull ke AWS, ada satu hal yang perlu dipastikan: **brief meminta deployment menunggu konfirmasi agency bahwa perubahan GTM mereka sudah dipublikasikan.** Status konfirmasi itu belum saya ketahui.

Push ke Git dapat dilakukan untuk menyimpan perubahan. Jika push Anda otomatis memicu deployment, perlakukan langkah itu sebagai deployment juga.

Saat commit, pilih **tujuh file source yang kita ubah**:

```text
themes/default/page.tpl.php
modules/testdrive/testdrive.php
modules/promotion/promotion.php
modules/mgcare/mgcare.php
modules/contact/contact.php
modules/requestquote/requestquote.php
modules/book/book.php
```

Hindari `git add .` karena ada perubahan lain di working tree. **Backup dalam `project-artifacts` dan cache Graphify tidak perlu ikut commit.** Contoh pesan commit:

```text
Remove legacy tracking snippets and page-load lead events
```

Setelah agency memberi konfirmasi dan perubahan ditarik ke AWS, periksa homepage, `/testdrive`, serta `/promotion`: halaman tetap terbuka normal, tidak ada Lead saat sekadar membuka halaman, dan request Floodlight lama sudah tidak muncul.

**Belum diverifikasi:** Meta Pixel ganda dari GTM dan kondisi server produksi. Fungsi `mgTrackLead` pengganti juga belum dipasang; penghapusan ini belum berarti seluruh implementasi tracking dalam brief selesai.

### 2026-10-13 20:59:07 WIB

Berikut **daftar script code yang sudah dihapus** dari tujuh file source. Totalnya **140 baris**, termasuk komentar pembungkus dan pemanggilan `register_js()` yang menjadi kosong.

**1. Meta Lead yang otomatis berjalan ketika halaman dibuka**

Dihapus dari [themes/default/page.tpl.php](D:/Laragon/www/newmg/themes/default/page.tpl.php):

```js
fbq('track', 'Lead');
```

Sebelumnya berada di layout umum, sehingga ikut berjalan pada halaman yang memakai layout tersebut.

**Yang tetap ada:**

```js
fbq('init', '1318994630046964')
fbq('track', 'PageView');
```

**2. Firebase Analytics**

Tiga baris berikut dihapus dari [themes/default/page.tpl.php](D:/Laragon/www/newmg/themes/default/page.tpl.php):

```js
import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-analytics.js";
```

```js
const analytics = getAnalytics(app);
```

```js
window.analytics = analytics;
```

Firebase untuk autentikasi/OTP tetap dipertahankan: `initializeApp`, `getAuth`, `signInWithPhoneNumber`, dan `RecaptchaVerifier`.

**3. Floodlight advertiser `15941435` — blok “Landing page”**

Seluruh blok dihapus, termasuk:

- Komentar `Start of Floodlight Tag` sampai `End of Floodlight Tag`.
- Pembuatan angka acak berikut:

```js
var axel = Math.random() + "";
var a = axel * 10000000000000;
```

- `document.write(...)` yang menyisipkan iframe ke alamat dengan penanda:

```text
https://15941435.fls.doubleclick.net/activityi
src=15941435
type=landi0
cat=landi0
```

- Fallback `<noscript><iframe ...></iframe></noscript>` untuk tag yang sama.

Blok ini dihapus dari **empat halaman**:

| Halaman | File |
|---|---|
| `/testdrive` | [modules/testdrive/testdrive.php](D:/Laragon/www/newmg/modules/testdrive/testdrive.php) |
| `/testdrive/thankyou` | File `testdrive.php` yang sama |
| `/promotio

[Cuplikan; lihat riwayat sumber untuk laporan lengkap.]

### 2026-10-13 21:08:29 WIB

Dari **`developer-brief.md`**, kode yang sudah saya hapus merujuk pada **Bagian 3 nomor 1 dan nomor 3**, serta beberapa ketentuan pada **Bagian 4**. Ada juga arahan terkait Meta Lead di **Bagian 1.2**.

Pemetaan tepatnya:

| Nomor/bagian dalam `.md` | Instruksi di dokumen | Yang sudah saya hapus |
|---|---|---|
| **3.1 — Floodlight tags** | Hapus blok Floodlight advertiser `15941435`, yaitu **Landing page** dan **Lead Done**. | **Sudah dihapus** dari `/testdrive`, `/promotion`, dan kedua halaman thank-you terkait. |
| **3.3 — Firebase Analytics** | Hapus import Analytics, `getAnalytics(app)`, dan `window.analytics`; pertahankan Firebase Auth. | **Ketiga baris sudah dihapus** dari `page.tpl.php`. Firebase Auth tetap ada. |
| **4 — Baris `fbq('track','Lead')` in `<head>`**; juga dijelaskan di **1.2 — Meta pixel** | Jangan menjalankan Lead pada setiap pembukaan halaman. | **Sudah dihapus:** `fbq('track', 'Lead');` dari layout umum. |
| **4 — Baris tag lama yang mencantumkan Google Ads `AW-10865541694`** | Tag tersebut berasal dari website lama dan sudah retired. | **Snippet Google Ads tersebut sudah dihapus** dari enam halaman thank-you. |
| **4 — Baris “Floodlight in the page code”** | Jangan memasang Floodlight langsung di kode halaman. | **Floodlight lama `DC-11735253` beserta fallback `<noscript>` sudah dihapus** dari enam halaman thank-you. ID `11735253` tidak disebut secara khusus dalam brief; penghapusannya mengikuti larangan umum Floodlight ini. |

**Untuk empat nomor pada tabel Bagian 3, statusnya adalah:**

| Nomor | Status |
|---|---|
| **1. Floodlight** | ✅ Sudah dihapus |
| **2. Google Maps dimuat dua kali** | Belum diubah — ini pekerjaan pengaturan pemuatan Maps |
| **3. Firebase Analytics** | ✅ Sudah dihapus |
| **4. Versi library belum dikunci** | Belum diu

[Cuplikan; lihat riwayat sumber untuk laporan lengkap.]

## Hubungan dengan memori sebelumnya

- Tidak tercatat eksplisit; jangan mengasumsikan telah dilakukan.

## Sumber dan keterbatasan

- Riwayat: C:\Users\aruna\.codex\sessions\2026\10\13\rollout-2026-10-13T20-28-44-01a13573-7e7e-7ff1-9372-0bc1f57bbaba.jsonl
- Metode: C:\Users\aruna\.codex\thread_history_1.sqlite / thread_items + thread_turns + rollout comparison (1 missing text messages recovered)
- Pesan pengguna: 10; laporan akhir: 9
- Kesalahan sumber: Tidak terdeteksi.
[Kredensial disamarkan]
- Memori lama dipertahankan; catatan ini menambahkan pembaruan per chat.
- Graphify diproses terpisah berdasarkan source aktif setelah arsip memori tersimpan.
- Verifikasi terbaru hanya mencakup integritas penyimpanan E/C; hasil uji aplikasi di atas bersifat historis.

## Memori pendukung dari sesi terdahulu

Bagian ini adalah handoff historis tambahan, berguna ketika proyeksi pesan aplikasi terpotong. Hasil uji tetap mengikuti tanggal sesi sumber; bukan verifikasi runtime hari ini.

Sumber: C:\Users\aruna\.codex\memories\rollout_summaries\2026-10-13T13-28-43-IUVc-newmg_tracking_cleanup_backup_and_verification.md

thread_id: 01a13573-7e7e-7ff1-9372-0bc1f57bbaba
updated_at: 2026-10-13T14:49:50+00:00
rollout_path: C:\Users\aruna\.codex\sessions\2026\10\13\rollout-2026-10-13T20-28-44-01a13573-7e7e-7ff1-9372-0bc1f57bbaba.jsonl
cwd: \\?\D:\Laragon\www\newmg
git_branch: main

# NEWMG tracking cleanup was analyzed, backed up, edited surgically, and verified

Rollout context: In `D:\Laragon\www\newmg`, the user first asked for a read-only trace of scripts identified by `C:\Users\aruna\Downloads\developer-brief.md`, explicitly saying not to delete anything yet. After receiving the mapped list, the user authorized deletion but required a backup first. The work used the active checkout, PowerShell, Graphify only as supplementary context, and PHP/source/HTTP verification. No deployment, commit, push, form submission, or AWS pull was performed.

## Task 1: Trace the developer brief against active source without mutation

Outcome: success

Preference signals:

- The user initially asked to “baca, analisa, cari dan hasilnya laporkan kepada saya tapi jangan kamu hapus dulu” -> for similar code-cleanup requests, inspect the brief and active source first, report exact files/locations/actions, and wait for explicit mutation approval.
- The user later asked for the result as a simple checklist with icons -> provide a concise checklist mapping brief section/number to completed or pending work when summarizing multi-part cleanup.

Key steps:

- Read `developer-brief.md` and distinguished requested cleanup from installation/deployment instructions.
- Searched active source while excluding backup/old/vendor/generated paths.
- Confirmed that snippets injected via `register_js()` are rendered later through the shared layout’s `load_js()`, explaining why some tracking code was not physically in `page.tpl.php`.
- Verified the local HTML before edits: `/testdrive` and `/promotion` returned HTTP 200 but contained page-load Meta Lead, Firebase Analytics, and Floodlight content; the active HTML showed one Maps script per tested page, so the brief’s alleged duplicate Maps load was not proven locally.
- Identified that the Graphify manifest was stale for `themes/default/page.tpl.php`; active source was treated as authoritative.

Reusable knowledge:

- The brief’s Section 3 cleanup table contains four items: (1) remove Floodlight `15941435`, (2) correct/limit Google Maps loading, (3) remove Firebase Analytics while retaining Firebase Auth, and (4) pin unversioned CDN libraries. Only items 1 and 3 were deletion tasks; item 2 requires loader-scope changes and item 4 requires version/asset changes.
- The page-load Meta Lead instruction is in brief Section 1.2/Section 4, not a numbered Section 3 item.
- The active layout was `themes/default/page.tpl.php`. Active controller sources were `modules/testdrive/testdrive.php`, `modules/promotion/promotion.php`, `modules/mgcare/mgcare.php`, `modules/contact/contact.php`, `modules/requestquote/requestquote.php`, and `modules/book/book.php`.

Failures and how to do differently:

- Graphify included historical/backup nodes and had a stale manifest for `page.tpl.php`; future work must always exclude historical files and confirm findings against current source before editing.
- The local HTML check did not execute JavaScript or prove GTM-side Meta duplication; do not claim that Meta Pixel double-loading is resolved without browser Network/Pixel Helper or GTM configuration evidence.

References:

- `C:\Users\aruna\Downloads\developer-brief.md`
- `themes/default/page.tpl.php`
- `modules/testdrive/testdrive.php`
- `modules/promotion/promotion.php`
- `modules/mgcare/mgcare.php`
- `modules/contact/contact.php`
- `modules/requestquote/requestquote.php`
- `modules/book/book.php`
- `application/common/function.php`: `register_js()` and `load_js()`
- `graphify query 'page tpl testdrive promotion' --budget 1800`

## Task 2: Backup and remove approved legacy tracking snippets

Outcome: success

Preference signals:

- Before authorizing edits, the user said “sebelum kamu hapus kamu buat backupnya terlebih dahulu” and wanted the ability to restore -> create complete, timestamped backups and verify their hashes before every mutation; do not rely on an informal copy or back up only the changed snippets.
- The user explicitly approved deleting the mapped snippets, not implementing every other brief item -> keep the diff limited to approved deletion scope and clearly separate pending installation/fix work.

Key steps:

- Created a timestamped backup directory: `D:\Laragon\www\newmg\project-artifacts\tracking-cleanup-20261013_203823`.
- Backed up all seven source files in full, preserving their relative paths and recording SHA-256 hashes in `manifest.json`.
- Verified all seven backup hashes matched the source bytes before editing.
- Applied deletion-only edits after asserting each source still matched its backup.
- Removed 140 source lines total, with no application-code additions.
- Added `verify.py`, `verification.json`, `http-verification.json`, and `README.md` under the tracking-cleanup artifact directory to document recovery and checks.

Deleted content:

- From `themes/default/page.tpl.php`: `fbq('track', 'Lead');`, Firebase Analytics import, `const analytics = getAnalytics(app);`, and `window.analytics = analytics;`.
- From `modules/testdrive/testdrive.php` and `modules/promotion/promotion.php`: all eight advertiser `15941435` Floodlight blocks across the normal and thank-you flows, including Landing page and Lead Done blocks, `document.write`, iframes, noscript fallbacks, and wrapper comments.
- From the six thank-you controller flows (`testdrive`, `promotion`, `mgcare`, `contact`, `requestquote`, `book`): Google Ads `AW-10865541694`, Floodlight `DC-11735253`, and the related `11735253` noscript image fallback.

Retained intentionally:

- GTM `GTM-MMBKCTN`.
- Meta Pixel `1318994630046964` initialization and `PageView`.
- TikTok Pixel `D5I8I53C77UE3GGHTG90`.
- Firebase initialization/Auth/OTP (`initializeApp`, `getAuth`, `signInWithPhoneNumber`, `RecaptchaVerifier`).
- Google Maps, UI libraries, Contentsquare, Trackier, session guards, thank-you views, and redirects.

Verification evidence:

- All seven files passed `php -l`.
- `git diff --check` passed.
- The custom verifier passed backup integrity, deletion-only scope, target absence, retained integrations, thank-you guards/views/redirects, and PHP lint.
- Eleven local HTML GET checks returned final HTTP 200: `/`, `/testdrive`, `/promotion`, `/news`, `/mgmodels/mgs5ev`, `/mgmodels/mgzshybrid`, `/mgdealer`, `/mgcare`, `/contact`, `/requestquote`, and `/book` (MG Care redirected to `/mgcare/services`). Target tracking strings were absent from the returned HTML; GTM, Meta, TikTok, and Firebase Auth markers remained.
- Follow-up checks on `/testdrive` and `/promotion` confirmed no page-load `fbq('track','Lead')`, no `fls.doubleclick.net`, and one Meta loader reference in HTML.

Failures and how to do differently:

- The HTTP verification was HTML-only: it did not execute JavaScript, submit forms, inspect Pixel Helper, inspect browser Network requests, or validate GTM’s remote configuration. Future agents must preserve this limitation and not claim end-to-end tracking correctness.
- The user’s existing unrelated dirty state (`graphify-out/cache/last_query_stamp` and `project-artifacts/pre-book-preview-20261013/`) was preserved. Do not use `git add .`; selectively stage the seven source files if the user later asks for a commit.
- A restore must be selective if a source file changed after cleanup: compare its current hash to the recorded `sha256_after` before copying the backup, otherwise make a new backup first.

Reusable knowledge:

- Restore artifacts: `D:\Laragon\www\newmg\project-artifacts\tracking-cleanup-20261013_203823\backups`; `manifest.json` records source/backup paths and before/after hashes; `README.md` documents recovery.
- The cleanup did not implement `mgTrackLead`, Maps loader scoping, CDN version pinning, GTM noscript relocation, or browser Pixel Helper tests. Those remain separate tasks and should not be implied to be complete.
- The developer brief requires coordination with the agency before production deployment because GTM changes must be published in sync to avoid double-counted events.

References:

- Backup artifact: `D:\Laragon\www\newmg\project-artifacts\tracking-cleanup-20261013_203823`
- Verification script: `project-artifacts/tracking-cleanup-20261013_203823/verify.py`
- Verification outputs: `verification.json`, `http-verification.json`
- Report/restore instructions: `project-artifacts/tracking-cleanup-20261013_203823/README.md`
- Source diff: seven files, `0` additions and `140` deletions in the approved source scope
- Exact successful verifier output: `PASS: 7 verified backups, 7 deletion-only source changes, 7 PHP lints, target absence, retained integrations, thankyou guards, git diff --check.`

## Task 3: Explain the image/checklist and deployment readiness

Outcome: success

Preference signals:

- When the user asked whether the screenshot’s items were deleted, the response distinguished “already deleted” from “not yet verified/resolved” instead of treating every screenshot item as complete -> maintain explicit status per item.
- When the user asked for a list of brief points with checklist icons, the final mapping was: ✅ Section 3.1 Floodlight, ✅ Section 3.3 Firebase Analytics, ✅ Section 1.2/Section 4 page-load Meta Lead, and ✅ Section 4 retired Google Ads/Floodlight code; Section 3.2 Maps and Section 3.4 library pinning remained pending.
- When the user said they wanted to push Git and pull AWS, the response warned that the brief requires agency coordination and advised selective staging -> before deployment, call out unresolved GTM/agency coordination and avoid staging unrelated artifacts.

Key steps:

- Rechecked active source and local HTML against the screenshot’s three concerns.
- Confirmed page-load Meta Lead and `15941435` Floodlight were removed.
- Correctly left Meta Pixel duplication from GTM unresolved because only static HTML was checked; the page’s own Meta loader remained intentionally.

Failures and how to do differently:

- Do not equate a clean local HTML response with production deployment success. Production `www.mgmotor.id`, AWS state, GTM configuration, and browser-side event behavior were not verified.

Reusable knowledge:

- The approved cleanup is ready for a selective commit, but deployment should be treated as pending until agency/GTM coordination is confirmed and production smoke checks are run.

References:

- User-facing checklist conclusion: “Khusus tabel Bagian 3, nomor yang sudah dihapus adalah: ✅ Nomor 1 dan ✅ Nomor 3.”
- Local verification: `/testdrive HTTP 200 page_load_lead=False floodlight=False`; `/promotion HTTP 200 page_load_lead=False floodlight=False`.
