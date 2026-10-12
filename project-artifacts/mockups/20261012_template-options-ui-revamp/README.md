# Preview revamp Template Options — 12 Oktober 2026

Status: prototype terisolasi untuk review visual. Source aplikasi aktif tidak diubah pada pekerjaan ini.

Buka http://127.0.0.1:8780/. Pilih salah satu dari delapan template pada toolbar prototype, bandingkan Revamp dengan Saat ini, lalu pilih Success, Warning, atau Failed pada dropdown Notice. Toolbar ini hanya ada di prototype.

## Cakupan dan pendekatan

Prototype mengekstrak modal Blade dan memakai Vue manager aktif, seluruh panel serta kondisi field yang sudah ada. Preview artikel dirender dengan Blade, ArticleTemplateCatalog, ArticleTemplateOptions, dan ArticleTemplatePreviewFixture aktif menggunakan data contoh. Lima archive dan tiga detail template tercakup; seluruh 46 panel telah diperiksa.

Arah UI mengikuti pola CMS: input/select desktop 36px, action kecil 34px, teks field 13–14px, label 12–13px, helper 12px, jarak grup 20px dan field terkait 14px. Native select, unit, responsive controls, linked corners dan conditional fields dipertahankan. Description menjadi tiga baris dengan resize. Panel navigasi, judul grup, divider dan footer dirapikan. Field radius dan spacing menggunakan ukuran font konsisten tanpa penyusutan em bertingkat.

Pada mobile, input menggunakan 40px dan teks minimal 16px, action footer 44px. Mekanisme section picker dan Settings/Preview yang sudah ada tetap digunakan. Footer berada dalam viewport dan panel form dapat discroll secara mandiri.

Notice menyalin markup Add New Article persis: ph-notice, ph-notice-toast, ph-callout-no-border, alert semantics dan close button. Success memakai ph-callout-success, warning ph-callout-warning, failed ph-callout-danger. Warna accent strip berasal dari theme CMS. Notice diletakkan di atas modal. Autohide dinonaktifkan hanya dalam prototype agar contoh mudah ditinjau; perilaku produksi belum diubah. Warning saat membatalkan dirty draft juga memakai class callout CMS.

## Source aktif yang dibaca

- resources/views/manage_article/templates/index.blade.php
- resources/views/manage_article/templates/partials/options-styling.blade.php
- resources/views/manage_article/templates/partials/options-mosaic-magazine.blade.php
- resources/views/manage_article/templates/partials/options-classic-balanced.blade.php
- resources/views/manage_article/manage_article_add.blade.php
- public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js
- public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js
- public/assets/css/article/article-template-manager-2026.css
- public/assets/css/article/article-frontend-2026.css
- public/assets/css/phoenix-cms.css, theme default dan aturan tombol arunika_lucent
- ArticleTemplateCatalog, ArticleTemplateOptions, ArticleTemplatePreviewFixture dan controller preview terkait

Path relatif di atas berada dalam D:\Laragon\www\laravel-13-phoenix. File hasil pekerjaan baru hanya berada dalam project-artifacts/mockups/20261012_template-options-ui-revamp/. Dependency JS/CSS/font/image disalin lokal; tidak ada dependency baru dipasang.

## Memori dan Graphify

Konteks utama: E:\AI\Memories\20260913_191214_laravel13_phoenix_article_template_settings_ui_ux_handoff.md, serta handoff E:\AI\Memories\20261012-all-projects-lanjutkan-template-article-01a0761e-da1a-7d80-ae9a-bd5a04c02938.md. Registry C:\Users\aruna\.codex\memories\MEMORY.md digunakan untuk kontrak CMS compact dan preferensi preview lebih dahulu. Semua konteks historis diperiksa terhadap source aktif.

Query Graphify:
- manage_article_templates Template Options optionsModal options-styling options-editorial-journal
- manage_article add new article notice success warning alert

Hasil dipakai sebagai peta menuju manager, partial form dan Add New Article. Source langsung menjadi bukti akhir; tidak ada relasi INFERRED/AMBIGUOUS yang dijadikan fakta implementasi. Graphify tidak diperbarui karena instruksi terbaru menetapkan update manual.

Skill UI UX Pro Max, Design Taste Frontend, Graphify dan Ponytail digunakan. Superpowers tidak tersedia pada katalog skill sesi ini; coding prototype mengikuti Ponytail.

## Backup dan integritas

Tidak ada file aplikasi aktif dimodifikasi sehingga tidak diperlukan backup source baru. Hash SHA-256 sembilan file terkait direkam sebelum pekerjaan dalam qa/source-checkpoint.json. Perbandingan terbaru dalam qa/source-integrity.json menunjukkan sembilan file tersebut tidak berubah.

Checkpoint awal prototype: qa/backups/initial-preview/. Perbaikan selama QC didahului salinan artifact; backup capture dan verify terakhir memiliki timestamp pada nama file di qa/backups/. Perubahan lama pada repository tetap dipertahankan. Tidak ada commit, stage, reset, clean atau push.

## Verifikasi terbaru

Playwright pada browser lokal: 185 pengecekan lulus, 46 panel, delapan template, nol pageerror atau request resource gagal. Bukti tersimpan dalam qa/results.json dan script qa/verify.js. Pemeriksaan notice visual tambahan dalam qa/notice-visibility.json lulus untuk ketiga status.

Yang diverifikasi di runtime prototype:
- Seluruh panel aktif dan field conditional terkait toolbar, thumbnail, pagination, shell dan Editorial Journal.
- Ukuran control, font field, overflow horizontal dan visibilitas footer.
- Perbandingan Revamp/Saat ini mengembalikan textarea 3/10 baris.
- Edit judul memperbarui H1 pada iframe Blade aktual.
- Warning dirty draft dan Keep editing.
- Tiga notice dengan accent strip CMS, tombol close, alert semantics, opacity penuh dan posisi di atas modal.
- Layout pada 1600, 1280, 1024, 768, 390, 375 dan 320px; section picker mobile, unit/radius field dan tab Preview.

Pemeriksaan statis: PHP syntax prototype dan git diff --check. Suite backend aplikasi tidak dijalankan ulang karena pekerjaan ini tidak mengubah source aplikasi.

Screenshot final: qa/header-desktop.png, qa/editorial-controls.png, qa/pagination-controls.png, qa/mobile-settings.png, qa/mobile-preview.png, qa/tablet-settings.png, serta qa/notice-success.png, qa/notice-warning.png dan qa/notice-failed.png. header-first.png adalah checkpoint awal, bukan hasil final.

## Batas prototype

Apply changes menggunakan draft Vue lokal. /save-demo diblokir HTTP 403. Tidak ada penyimpanan setting artikel aktif, Save Template terautentikasi, upload/CKFinder produksi atau perubahan database yang menjadi bagian pengujian ini. Notice merupakan simulasi hasil proses untuk review visual; wiring hasil request CMS akan menjadi bagian integrasi jika desain diterapkan.

Server preview dijalankan dari folder artifact dengan:

    php -S 127.0.0.1:8780 router.php

Jika server sudah berhenti, jalankan perintah itu dari D:\Laragon\www\laravel-13-phoenix\project-artifacts\mockups\20261012_template-options-ui-revamp. Source index.html dapat diregenerasi lewat php render.php; sebelum mengedit artifact yang sudah ada, buat backup baru.
