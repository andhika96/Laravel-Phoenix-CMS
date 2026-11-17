# Shared CSS consolidation and auth corrections

1. Preserve the three active stylesheet bodies byte for byte in one `phoenix-shared.css`, with an index and START/END section flags.
2. Keep the existing stylesheet URLs as read-only section responses. This preserves selective consumers, relative URLs, and the exact order around theme styles; concatenating all sections into every document would change that contract.
3. Compare the served sections against timestamped backups and compare browser output across CMS/frontend themes. Keep the independent auth stylesheet boundary.
4. Reproduce the Midnight Story seam and button-state spacing, then fix the shared source and test all 32 auth variants without saving settings or sending real signup/recovery requests.
5. Retain backups and proof in this directory. Stay on main, preserve existing Aurora JavaScript changes and staging. Graphify updates remain manual.

Memory: E:/AI/Memories/20261103_160100-laravel13-phoenix-css-auth-sidebar-tailwind-handoff.md. Graphify: phoenix-cms.css, 19 EXTRACTED stylesheet callers, verified against active views. The graph predates recent auth work and is used only for navigation.

Superpowers is no longer present in the configured skill/cache folders at this checkpoint. Continue its previously established inspect, reproduce, minimal fix, verify workflow with the available Ponytail and Karpathy guidelines; no new dependency/plugin installation.
