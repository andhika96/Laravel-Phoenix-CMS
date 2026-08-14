# Responsive Hero Banner Prototype — Design QA

**Tanggal:** 2026-08-14  
**URL:** `http://127.0.0.1:4179/`  
**Source truth:** Screenshot hero MG5 GT yang diberikan pengguna dan runtime section `mgmotor.id/mgmodels/mg5gt` yang sebelumnya diverifikasi.

## Visual comparison

- Struktur hero tetap mengikuti komposisi sumber: copy di sisi kiri, mobil dominan di sisi kanan, CTA berbentuk pill, dan media memenuhi frame.
- Builder shell mengikuti bahasa visual Page Builder v2.3 yang sudah dipakai prototype: inspector putih, device switcher, live canvas, selection outline, dan decision bar.
- Desktop default tampil proporsional tanpa overlap. Mobile memakai aset portrait terpisah dan Button Group column agar copy tidak menabrak mobil.
- Panel menambah density hanya pada Content tab; Style dan Advanced tetap menjadi boundary prototype.

## Runtime checks

- Grouped Content Order memindahkan Subtitle ke atas Title; hasil canvas: `Light Up Desire → MG 5 GT → Watch Video`.
- Independent mode merender tiga target terpisah dan posisi Button Group berubah ke `left: 44%` saat X diubah.
- Add dan Duplicate berhenti tepat pada 3 button; Add disabled pada batas; Remove mengembalikan jumlah ke 2 dan mengaktifkan Add lagi.
- Link action merender `<a>` dengan URL, `_blank`, dan `rel="noopener noreferrer nofollow"`.
- YouTube Watch URL dinormalisasi ke `https://www.youtube.com/embed/h529sg3pEV4?autoplay=1`.
- Video modal terlihat, close button menerima focus, Escape menutup, dan click backdrop menutup.
- Image popup mengambil aset dari simulated CKFinder picker dan merender media modal.
- Tablet mewarisi layout dan media dari Desktop; edit membuat Custom override; Reset menghapus override.
- Mobile mulai sebagai Custom override, dapat reset ke Desktop, lalu kembali Custom setelah memilih aset mobile.
- Viewport 1440 × 900: `scrollWidth 1440`, lulus tanpa horizontal overflow.
- Viewport 1024 × 900: `scrollWidth 1024`, lulus tanpa horizontal overflow.
- Viewport 720 × 1000: `scrollWidth 720`, lulus tanpa horizontal overflow.
- Browser console setelah seluruh interaction pass: 0 error, 0 warning.

## Findings and patches

- Button tunggal sebelumnya diposisikan sebagai target `button`; diperluas menjadi satu target `buttons` agar seluruh repeater bergerak sebagai Button Group.
- Background `<picture>` statis diganti satu resolved media state supaya object fit, object position, source, dan fallback benar-benar mengikuti device aktif.
- Modal memakai satu instance bersama untuk picker, video, dan image; media dilepas dari DOM saat close sehingga playback berhenti.
- Tidak ditemukan defect visual blocking pada final desktop dan narrow-viewport review.

## Prototype boundary

- CKFinder adalah dialog simulasi dengan dua aset lokal; tidak mengakses session atau storage produksi.
- Custom attributes disimpan pada state prototype tetapi sengaja tidak dirender sampai sanitizer production v2.3 dipakai.
- Save dan Preview tetap disabled; tidak ada Page Builder data yang disimpan.
- Source produksi Page Builder v2.3 tidak diubah.

final result: passed
