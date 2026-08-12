# Implementasi Blockquote dan Share Buttons

## Urutan kerja

1. Tambahkan RED tests untuk definition, settings mapping, canvas hooks, registry, Blade output, dan runtime action.
2. Buat definition blockquote dan share_buttons berisi defaults, option allow-list, normalizer, dan responsive defaults.
3. Daftarkan widget pada config dan app label/icon/advanced-control gate.
4. Tambahkan panel Content dan Style pada shared Settings.vue, termasuk repeater network dan conditional controls.
5. Tambahkan preview canvas pada shared Canvas.vue dengan mapping style, responsive values, safe links, hover class, dan interaction.
6. Tambahkan renderer Blade dengan allow-list network, URL builder, escaping, CSS responsive, dan state normal/hover.
7. Tambahkan frontend runtime untuk Copy/Print share actions.
8. Jalankan focused Node/PHP tests, semua suite v2.3, syntax checks, dan git diff --check.
9. Perbarui Graphify secara incremental untuk file yang berubah, jalankan health check/query, lalu lakukan browser QA read-only bila Chrome runtime tersedia.

## Batasan

- Tidak mengubah v2.0 atau modul di luar v2.3.
- Tidak menghapus backup, screenshot, atau perubahan lama milik user.
- Tidak memasukkan URL atau class dari setting mentah ke output tanpa validasi.
- Tidak menjalankan Save dari browser.

## Verifikasi sukses

- Registry berjumlah 42 widget aktif setelah dua widget baru.
- Test parity baru lulus.
- Existing Code Highlight, Testimonial Carousel, runtime, toolbar, Node, dan PHP tests tetap lulus.
- Graphify health tidak memiliki missing, dangling, self-loop, atau collapsed edge.
