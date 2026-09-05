# Arunika Mobile V2 baseline

Tanggal: 2026-09-03. Baseline dibuat sebelum implementasi controller/layout baru.

## Source state

- Branch: main; existing dirty changes preserved.
- Dirty files before plan execution:
  - public/assets/css/themes/arunika_lucent/arunika_lucent.css
  - tests/arunika-lucent-theme-static.test.mjs
- SHA-256 current baseline CSS: 4FD7694536E49259D7A9D6C72C98F5ACCB9C43BB6AACFC19F4D06F6CB6EB52DD
- SHA-256 current baseline test: DC307B18E6A121A10A684AAF34F1142E37A71AF90D6C9CAF142FED737469B50C
- No reset, commit, push, or staging performed.

## Fresh checks

- node --test theme regression set: 31/31 pass.
- Existing theme-specific tests remained green before plan edits.
- Reference image dimensions and SHA-256 locked in reference-measurements.json.
- Graphify graph.json exists and was mapped with query “sidebar lucent mosaic aurora prism equinox”; graph modified 2026-09-01, so source files remain final authority.
- Memory used: E:/AI/Memories/20260901-arunika-lucent-handoff.md. It is historical context only; current source wins.

## Scope entering implementation

Task 1 Vue 3 CDN controller, Task 2 shared dashboard seam, then one mobile overlay per theme. The auth Lucent button/notice fix is preserved. Preview board captions/disclaimers are not application markup.

