# Graphify cumulative update — Article pagination models/ranges — 2026-09-06

## Scope

Memperbarui Graphify untuk implementasi pagination model (`Minimal Underline`, `Classic Boxed`, `Soft Highlight`) dan range pagination Desktop/Tablet/Mobile, sekaligus perubahan source code sebelumnya yang masih terdeteksi oleh manifest.

## Incremental scan

- `12` code files berubah sejak update Graphify sebelumnya.
- `112` dokumen dan `18` image masih tertunda dari batch semantic sebelumnya.
- `1,639` files tidak berubah dan memakai cache.
- `0` file terhapus.

Percobaan semantic penuh berhenti karena tidak ada backend/API key Graphify. Tidak ada credential yang diminta atau dimasukkan. Fallback resmi `--code-only` kemudian berhasil mengekstrak seluruh `12` code files terbaru.

## Graph result

- Sebelum update: `21,742` nodes, `37,609` edges.
- Sesudah update: `21,747` nodes, `37,618` edges.
- Delta: `+5` nodes, `+19` edges, `0` removed nodes, `10` removed/reconciled edges.
- Communities sesudah cluster: `1,590`.
- HTML aggregation: `1,590` community nodes dan `1,098` cross-community edges.

Graph health read-only:

- Self-loop: `0`.
- Missing endpoint: `0`.
- Dangling endpoint: `0`.

## Query smoke check

Query `Article pagination model range ArticleTemplateOptions pagination preview` menemukan:

- `ArticleTemplateOptions`
- `.pagination()`
- `.paginationRange()`
- `ManageArticleTemplateController`
- `ArticleFrontendController`
- `ArticleTemplatePreviewFixture`

## Outputs

- `graphify-out/graph.json` diperbarui.
- `graphify-out/GRAPH_REPORT.md` diperbarui.
- `graphify-out/graph.html` dibuat ulang dengan community aggregation.
- `graphify-out/manifest.json` diperbarui.
- analysis dan labels diperbarui oleh cluster step tanpa LLM naming.

## Backup

Backup graph sebelum update berada di:

`project-artifacts/backups/20260906_000000-graphify-article-pagination-cumulative/`

SHA-256 dibuat setelah backup.

## Batasan

Code graph sudah diperbarui. Semantic extraction untuk `112` dokumen dan `18` image masih tertunda karena backend/API tidak tersedia. Graphify tidak akan dijalankan otomatis lagi; update berikutnya menunggu instruksi manual pengguna.
