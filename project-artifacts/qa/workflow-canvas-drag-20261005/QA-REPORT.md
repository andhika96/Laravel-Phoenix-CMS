# Workflow canvas drag fix — 5 Oktober 2026

Project: D:\Laragon\www\laravel-13-phoenix.

## Masalah dan akar penyebab

Screenshot pengguna menunjukkan node x=0 tertahan di tengah canvas. Source aktif membatasi screenToGraph, moveNode dan keyboard movement dengan Math.max(0,...). Origin graph ikut bergeser melalui viewport pan/Fit; batas world x/y=0 kemudian tampak sebagai invisible wall di dalam area terlihat. screenToGraph juga mengurangi 12 dari kedua sumbu, sehingga tidak menjadi inverse transform murni dan membuat wheel zoom bergeser dari anchor kursor.

## Perubahan

- `public/assets/js/vue3/workflow/workflow-editor.js`: koordinat world bertanda dipertahankan; drag/keyboard dapat melintasi origin; inverse transform pointer tanpa offset tetap; floor zoom-out/Fit 10% sesuai rentang backend yang sudah tersedia.
- `public/assets/css/workflow-editor.css`: hapus transition transform pada world agar posisi visual pan/zoom langsung sesuai perhitungan pointer.
- `project-artifacts/tests/workflow-canvas-geometry.test.mjs`: regression checks empat sudut, signed coordinates, grip offset, zoom anchoring, Fit pada node negatif dan node jauh.
- `project-artifacts/tests/WorkflowEditorTest.php`: persistence test koordinat negatif (-800,-500), jauh (3200,2400), zoom 10%.

Tidak mengubah migration, database, tema, atau modul lain. Backup sebelum perubahan di `project-artifacts/backups/20261005_104704_workflow-canvas-drag/`: workflow-editor.js, workflow-editor.css, workflow-graph.js (dibaca; tidak dimodifikasi), test PHP dan backup awal geometry test.

## Verifikasi terbaru

```powershell
node --check public/assets/js/vue3/workflow/workflow-editor.js
node --test project-artifacts/tests/workflow-canvas-geometry.test.mjs project-artifacts/tests/workflow-editor-interaction.test.mjs project-artifacts/tests/workflow-graph.test.mjs
php vendor/phpunit/phpunit/phpunit --configuration project-artifacts/tests/workflow-phpunit.xml --no-progress --do-not-cache-result
git diff --check
```

- Node: 17 passed, 0 failed.
- PHP: 13 passed, 81 assertions.
- Syntax/diff check: pass; unrelated existing CRLF warning only.
- Failing-first: empat check awal mereproduksi clamp/offset; setelah root fix pass. Strengthened Fit check gagal pada floor35%, lalu pass dengan floor10%.
- Geometry matrix: empat sudut pada zoom 35/63/100/250%, viewport offset x430/y140; error drag dalam toleransi rounding <= setengah pixel world.
- Backend persistence: negative/far coordinates disimpan tanpa clamp di SQLite memory.
- Semua file dirty lama dalam baseline 0 hash mismatch.

## Browser runtime evidence

Browser CMS tersedia masih berada pada login. Harness QA lama dipakai kembali dengan template/Vue/CSS produksi, layout minimal, fixture lokal. Tidak Save/Publish ke database aktif.

CDP pointer mouse aktual dipakai untuk node Approval ke kiri atas, kanan atas, kanan bawah dan kiri bawah. Diulang setelah pan memindahkan origin ke dalam canvas: translate(239px,225px), scale0.71.

Hasil setelah pan:

| Sudut | Margin horizontal | Margin vertikal | Posisi world |
|---|---|---|---|
| Kiri atas | left16.06px | top16.26px | x-314,y-294 |
| Kanan atas | right16.22px | top16.26px | x386,y-294 |
| Kanan bawah | right16.22px | bottom15.84px | x386,y405 |
| Kiri bawah | left16.06px | bottom15.84px | x-314,y405 |

Margin16px adalah target QA, bukan pembatas dalam kode. Node dapat melintasi batas terlihat; canvas adalah viewport yang memotong konten di luar layar, dan pan/Fit menampilkannya lagi. SVG connection paths berubah mengikuti koordinat node, termasuk koordinat negatif. Console errors/warnings: kosong. Data lengkap `browser-corners.json`; screenshot `left-bottom.jpg`. Override viewport1200px untuk seluruh canvas terlihat direset setelah QA.

## Batas dan konteks

- Authenticated Firefox/CMS layout pada screenshot pengguna belum diuji langsung; source produksi identik yang diuji pada harness.
- Backend tetap memiliki rentang validasi koordinat +/-100000 dan zoom0.1..4. UI zoom0.1..2.5. Tidak mengklaim canvas tanpa batas penyimpanan.
- Memori resmi E:\AI\Memories tidak tersedia; quick pass MEMORY.md lokal C dan konteks chat dipakai. Source aktual menjadi bukti utama.
- Graphify query `WorkflowGraph screenToGraph beginNodeDrag workflow-canvas`, budget800, tidak menemukan node Workflow pada graph lama. Graph tidak diperbarui sesuai instruksi manual. Tidak mengandalkan relasi Page Builder yang dikembalikan sebagai relasi Workflow.
- Source yang dibaca: workflow-editor.js, workflow-graph.js, workflow-editor.css, editor.blade.php, WorkflowGraph.php, tests Workflow dan harness QA sebelumnya.
- Tidak commit/push/reset/clean atau menjalankan migration tambahan.
