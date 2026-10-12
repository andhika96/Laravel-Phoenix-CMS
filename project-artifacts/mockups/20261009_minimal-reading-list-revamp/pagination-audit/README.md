# Audit tiga model pagination Article

Tanggal: 9 Oktober 2026, Asia/Jakarta. Project: Laravel 13 Phoenix.

Scope: memeriksa tiga model yang tersedia dalam Template Options, pemakaiannya pada semua archive template, dan kesamaan CSS Blade/Vue. Source aplikasi dan settings tersimpan tidak diubah.

## Tiga model aktif

| Label settings | Key | Karakter default model |
| --- | --- | --- |
| Minimal Underline | `underline` | Nomor tanpa kotak yang terlihat, halaman aktif dengan garis bawah aksen |
| Classic Boxed | `boxed` | Kotak dengan border dan radius, halaman aktif berisi warna aksen dengan teks putih |
| Soft Highlight | `soft` | Nomor dengan latar transparan, halaman aktif diberi latar aksen lembut dan teks aksen gelap |

Dropdown berasal dari `resources/views/manage_article/templates/partials/options-styling.blade.php:78`. PHP normalizer dan frontend Vue menerima ketiga key yang sama; fallback adalah `boxed`.

Semua lima archive template mengikutsertakan `article.templates.partials.pagination`:

- Minimal Reading List: `resources/views/article/templates/archive/minimal-reading-list.blade.php:51`.
- Editorial Journal: `resources/views/article/templates/archive/editorial-journal.blade.php:85`.
- Mosaic Magazine: `resources/views/article/templates/archive/mosaic-magazine.blade.php:63`.
- Mosaic Classic: `resources/views/article/templates/archive/mosaic-classic.blade.php:122`.
- Balanced Card Grid: `resources/views/article/templates/archive/balanced-card-grid.blade.php:39`.

Pagination bernomor ini merupakan fitur archive. Previous/Next article pada detail template memakai komponen navigasi lain.

## Bukti browser

Perbandingan: http://127.0.0.1:8774/pagination-audit/index.html

- `index.html` berisi snapshot CSS aktif, dengan class template dan markup pagination yang mengikuti renderer aktif. Ini halaman pemeriksaan komponen terpisah, bukan endpoint Laravel authenticated.
- Semua 15 kombinasi memakai settings identik: radius item `.45rem`, gap `.45rem`, footer frame aktif, radius frame `.75rem`, warna model default, posisi center, total disembunyikan untuk fokus pada kontrol.
- Pengukuran browser dilakukan pada class renderer SSR dan Vue, menghasilkan 30 rekaman di `measurements.json`.
- Root font 16 px. SHA-256 CSS aktif yang digunakan: `3914DF200D67E92EF0D3C1AF8251D820D4A3EB349FDE906A5F4FE8EC84F04220`.
- `comparison.png` adalah screenshot CSS asli, bukan rancangan perbaikan. Screenshot diperiksa visual.

Temuan dengan settings identik:

| Aspek | Minimal Reading List | Empat archive template lain |
| --- | --- | --- |
| Tinggi tombol | 44 px | 37.59375 px, berasal dari `2.35rem` |
| Radius halaman aktif Boxed/Soft | 0 px | 7.2 px, sesuai `.45rem` |
| Border halaman aktif Boxed/Soft | Sisa rule border bawah 2 px | Border 1 px pada semua sisi |
| Footer frame aktif | Border atas saja, radius 0 | Border semua sisi, radius 12 px |
| Padding horizontal default SSR | 0 px | 16 px |
| Padding horizontal default Vue | 16 px | 16 px |

Nama dan key model sudah seragam, tetapi visual model dengan settings identik belum seragam.

## Penyebab pada source

1. `public/assets/css/article/article-frontend-2026.css:2060` sampai blok sebelum sticky sidebar memberi override pagination khusus `.article-page--reading-list`. Blok ini memaksa tinggi 44 px, mengubah frame menjadi border atas, serta memaksa active radius 0 dan border bawah. Sebagian properti tersebut tetap mengalahkan atau terbawa ke model Boxed/Soft.
2. Rule tiga model bersama mulai pada `public/assets/css/article/article-frontend-2026.css:2313`. Model menggunakan custom properties dari settings, tetapi tidak seluruh properti legacy Reading List di-reset oleh rule model.
3. `public/assets/css/article/article-frontend-2026.css:1316` memberi `.article-pagination--vue` `padding: 1rem !important`. Ini mengalahkan padding kiri/kanan 0 dari override Reading List, sehingga SSR dan Vue berbeda pada kondisi padding custom tidak diaktifkan.

Hipotesis padding diuji hanya pada DOM halaman audit: override kiri/kanan 0 dengan `!important` untuk Reading List Vue mengembalikan padding ke hasil Blade pada ketiga model. Hasil `restoresBladePadding: true` tersimpan dalam `hypothesis.json`. Override percobaan tidak disimpan ke source aplikasi atau HTML audit.

Pemeriksaan parity di `verify.js` masih sengaja melaporkan `SSR and Vue CSS results differ` untuk CSS source aktif. Audit berhasil menemukan ketidaksamaan; ini bukan klaim bahwa source sudah diperbaiki atau semua pemeriksaan parity lulus.

## Alur settings dan renderer yang diperiksa

- `app/Support/Article/ArticleTemplateOptions.php:359`: normalisasi pagination bersama, termasuk type, range per device, radius, gap, warna, ikon, total, posisi, frame, padding, dan margin.
- `resources/views/manage_article/templates/partials/options-styling.blade.php`: panel settings bersama untuk seluruh archive template.
- `resources/views/article/templates/partials/pagination.blade.php`: markup SSR dan CSS custom properties.
- `resources/views/article/archive.blade.php:21`: pagination Vue memakai `paginationClasses`, `paginationStyle`, dan range responsif.
- `public/assets/js/vue3/article/vueV3-article-frontend-2026.js`: normalisasi model dan pembentukan class/style Vue.
- `public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js:664`: options dibaca berdasarkan `activeTemplateKey`; `applyTemplateOptions()` menyimpan draft ke key template terkait.
- `app/Support/Article/ArticleTemplateCatalog.php`: daftar lima archive template.

Settings saat ini tersimpan per template. Target penyamaan visual berarti setiap model memiliki standar tampilan yang sama pada kelima template ketika nilai settings sama. Target ini tidak dengan sendirinya mengubah penyimpanan menjadi satu setting global untuk semua template.

## Memori, Graphify, dan skill

Drive `E:\AI\Memories` tidak tersedia. Memori C pada `C:\Users\aruna\.codex\memories\MEMORY.md` bagian Article Template Options/pagination digunakan sebagai konteks, kemudian diverifikasi terhadap source aktif.

Graphify query: `pagination article template`, budget 1400. Explain: `.paginationType()`. Relasi `EXTRACTED` menghubungkan `.pagination()` ke `.paginationType()` pada normalizer. Node generik lain tidak digunakan sebagai bukti Article.

`graphify-out/graph.json` masih sesuai HEAD `966a8057536d6ddc53c602e68bf62c5ccf336be1`; tidak ada perubahan pada source Article yang diperiksa. Graph tidak diperbarui. CSS aktual dibaca langsung dan hasilnya diuji di browser.

Skill: Graphify, UI UX Pro Max, Design Taste Frontend, Ponytail, Playwright; pada kelanjutan audit Superpowers Systematic Debugging dan Verification Before Completion juga tersedia dan digunakan.

## Artifact, backup, dan verifikasi

File baru dalam folder audit ini: `index.html`, `verify.js`, `comparison.png`, `initial-snapshot.yml`, `measurements.json`, `hypothesis.json`, laporan ini, serta snapshot CLI lokal.

Sebelum revisi skrip diagnostik, backup hash-verified dibuat di `backups/verify.js.bak_20261009_142748_save-parity-result`. Perubahan skrip hanya memastikan data pengukuran tersimpan sebelum assertion parity melaporkan perbedaan.

- Verifikasi browser: 15 kombinasi model/template, dua class renderer; perbedaan yang dijelaskan di atas terukur.
- Uji hipotesis padding: sesuai hasil Blade setelah satu rule percobaan pada DOM audit.
- `node --check verify.js` dan `git diff --check` lulus.
- `git diff --name-only` kosong; hanya folder prototype sebelumnya beserta artifact audit yang untracked.
- Tidak ada source production, data/settings database, commit, push, atau Graphify yang diubah.

Belum diverifikasi: Manager authenticated, Save Template, keseluruhan lifecycle Vue pada endpoint Laravel, semua state hover/focus, dan seluruh kombinasi nilai custom settings. Browser test di sini membuktikan perilaku CSS komponen dengan settings pembanding yang disebutkan.

## Dasar revamp berikutnya

Ketiga model tetap tersedia. Gunakan komponen, rule dasar ukuran/spacing, state normal/hover/active/disabled, serta frame bersama untuk semua lima archive template. Hilangkan konflik override legacy Reading List pada pagination dan satukan aturan default padding SSR/Vue. Custom settings yang didukung model tetap menjadi sumber nilai.

Pekerjaan ini berhenti pada pemeriksaan sesuai permintaan "check dulu". Implementasi penyamaan belum diterapkan. Snapshot CSS dalam `index.html` perlu diperbarui dari source baru bila audit dipakai lagi setelah source berubah.
