# Graphify cumulative update — 2026-09-05

## Scope

Memperbarui Graphify untuk perubahan Template Options, Minimal Reading List, responsive article list, thumbnail, preview radius, tests, dan perubahan source terkait yang tertunda sejak manifest Graphify terakhir.

## Baseline dan hasil

Incremental scan menemukan:

- `70` code files berubah.
- `112` document files berubah.
- `18` image files berubah.
- `1,581` files tidak berubah dan tetap memakai cache.
- `0` file terhapus.
- Total corpus sekitar `1,946,566` words.

Full semantic update pertama berhenti sebelum menulis graph baru karena `130` file non-code membutuhkan API semantic extraction dan tidak ada API key yang tersedia di environment ini. Tidak ada credential yang diminta atau dimasukkan.

Fallback resmi Graphify yang berhasil dijalankan:

```text
graphify . --update --code-only --no-viz --no-label
graphify cluster-only . --no-viz --no-label
graphify export html --graph graphify-out/graph.json
```

Graph saat ini:

- `21,742` nodes.
- `37,609` edges.
- `1,595` communities.
- HTML agregat: `1,595` community nodes dan `1,104` cross-community edges.

Perbandingan terhadap backup graph sebelum update sesi ini:

- `+11` nodes.
- `-308` nodes yang dipangkas oleh merge incremental.
- `+472` edges.
- `-2,757` edges yang dipangkas oleh merge incremental.

Graph health check read-only:

- Self-loop: `0`.
- Dangling endpoint: `0`.
- Missing endpoint: `0`.

## Semantic coverage limitation

`--code-only` mengekstrak ulang seluruh `70` code files dan mempertahankan semantic graph lama untuk dokumen/image yang sudah ada. Namun `112` dokumen dan `18` image baru/berubah dari batch pekerjaan sebelumnya belum mendapatkan semantic extraction baru. Graph akan lengkap secara semantic setelah environment menyediakan backend Graphify yang dapat dipakai, lalu dijalankan ulang tanpa `--code-only`.

Graph query smoke check menemukan node dan jalur terkait:

- `ArticleTemplateOptions`
- `minimal-reading-list.blade.php`
- `article-minimal-reading-list-filter.test.mjs`
- Template Options form UX preview artifacts

## Outputs

- `graphify-out/graph.json` diperbarui.
- `graphify-out/GRAPH_REPORT.md` diperbarui.
- `graphify-out/graph.html` dibuat ulang dalam mode community aggregation.
- `graphify-out/manifest.json` diperbarui.
- `graphify-out/.graphify_analysis.json` dan labels diperbarui.

## Backup

Backup baseline sebelum update:

`project-artifacts/backups/20260905_220000-graphify-template-options-cumulative/`

Backup mencakup graph JSON, report, manifest, analysis, labels, dan graph lama bila tersedia. SHA-256 backup dibuat setelah penyalinan.

## Status

Graphify sudah diperbarui untuk seluruh source-code changes yang tertunda. Semantic update dokumen/image masih tertunda karena keterbatasan backend/API; kondisi ini sengaja dilaporkan, bukan disamarkan.
