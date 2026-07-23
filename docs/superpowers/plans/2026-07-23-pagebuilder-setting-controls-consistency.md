# Page Builder Setting Controls Consistency Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox checkpoints and must remain sequential per widget.

**Goal:** Menyeragamkan form opsi Page Builder agar color memakai picker, nilai berdimensi memakai numeric/slider/unit control, dan density UI mengikuti pola Layout tanpa mengubah kontrak state, canvas, atau frontend.

**Architecture:** `AdvancedControls.vue` adalah shared control yang dipakai seluruh widget. Defect shared diperbaiki sekali pada root component; setting widget-specific diperbaiki berurutan per widget. Existing setting keys dan resolver tetap dipertahankan.

**Tech Stack:** Laravel 13, Vue SFC runtime loader, PHPUnit feature tests, local `pb-picker` assets.

**Global Constraints:**

- Pertahankan seluruh perubahan pengguna yang tidak terkait.
- Buat backup timestamp sebelum setiap edit source aktif.
- Gunakan Layout settings sebagai referensi control dan density.
- Jangan mengubah setting key atau output resolver tanpa bukti parity editor/canvas/frontend.
- Audit boleh paralel dan read-only; implementasi tetap satu widget/scope pada satu waktu.

## Task 1: Image Box — shared Advanced Background color controls

- [ ] Simpan screenshot before-state ke `output/design-qa/`.
- [ ] Tambahkan test regresi yang mewajibkan seluruh color field di shared Advanced Controls memakai local picker hook.
- [ ] Jalankan focused test dan konfirmasi RED.
- [ ] Ubah hanya color input shared Background/Gradient/Border/Box Shadow ke `pb-coloris-input`.
- [ ] Jalankan focused test, shared advanced suite, dan Image Box parity suite.
- [ ] Verifikasi asset `pb-picker` serta scheduling initializer tetap terhubung.

## Task 2: Audit inventory dan urutan widget

- [ ] Audit Layout sebagai canonical reference.
- [ ] Audit Basic: Heading, Text Editor, Image, Video, Button, Divider, Spacer, Icon.
- [ ] Audit General/Advanced: Image Box, Tabs, Accordion, dan shared Advanced Controls.
- [ ] Catat setiap plain color input, CSS dimension string input, serta inkonsistensi density/layout.
- [ ] Tetapkan antrean implementasi per widget berdasarkan severity dan shared impact.

## Task 3: Implementasi widget-specific secara berurutan

- [ ] Ambil satu widget dari antrean.
- [ ] Buat backup source dan test terkait.
- [ ] Tambahkan regression test RED.
- [ ] Terapkan control canonical tanpa mengubah setting key.
- [ ] Verifikasi state editor, canvas, persistence marker, dan frontend/parser.
- [ ] Perbarui status audit sebelum pindah ke widget berikutnya.

## Task 4: QA dan Graphify

- [ ] Jalankan seluruh Page Builder feature tests yang relevan.
- [ ] Lakukan visual QA pada panel yang diubah jika browser runtime tersedia.
- [ ] Dokumentasikan status verified/blocked dalam `design-qa.md`.
- [ ] Update Graphify incremental bila perubahan source substantif.
