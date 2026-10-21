# Focused Reader width and navigation concept — 21 October 2026

Scope: preview only. No application source or database settings changed.

- Reading Cards: reading column target 880px, wide cover up to 1040px; centered header. Two thumbnail navigation cards with arrow, clear direction/category/title hierarchy. Hover/focus adds green border, subtle green background, green title, filled arrow circle and directional arrow motion. No hover shadow.
- Editorial Flow: reading column target 960px, left header, cover aligned to reading width; flat divided navigation panels without thumbnails. Same feedback palette and mobile stacking.
- Buttons switch desktop/tablet/mobile local preview; Contoh hover makes the next article hover state visible on touch devices. Native links simulate local selection only. Host design controls can adjust reading width.
- Shared mock typography remains close to the CMS scale (28px title desktop, 24px mobile, small body); this isolated preview does not replace the application CMS typography/content contract.
- Existing source inspected read-only: ArticleTemplateOptions.php (680px default reading width, 880px cover), focused-reader scoped navigation CSS, detail-navigation.blade.php. Existing generic hover shadow explains the user complaint.
- Memory used: E:/AI/Memories/20261020-delta-revamp-focused-reader-01a14ef8-cc2f-78d3-b204-f92352ffbb78.md and C: MEMORY.md Focused Reader registry. Graphify query: `focused-reader detail-navigation ArticleTemplateOptions`, verified directly in source; graph not updated.
- Ten responsive cases (two designs at 320,390,768,1024,1440) passed without horizontal overflow. Device width switches, simulated hover, local navigation, native focus and reduced-motion verified. Browser page errors: zero. Desktop/mobile screenshots reviewed. Lucide placeholders are rendered by the conversation host; standalone QC captures omit host icons.
- All output under this folder. Original production files were not modified, so no production backups were required. A local pre-polish HTML copy was retained. No commit/push.
