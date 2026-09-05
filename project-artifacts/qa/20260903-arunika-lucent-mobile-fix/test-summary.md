# Test summary

- Focused Lucent/mobile/theme regression (12 explicit files): **45 passed, 0 failed** after the sidebar-profile revision.
- `node --check` for controller, theme scripts, and dashboard script: **pass**.
- `php artisan view:cache`: **pass**.
- `git diff --check`: **pass** (only CRLF normalization warnings).
- Browser harness recheck: `300x844`, `400x844`, `500x844`, and breakpoint `768/769`; hamburger remains left-aligned, avatar right-aligned, no header brand/bell, no sidebar brand orphan, the `Administrator` name renders without ellipsis, and semantic colors remain isolated.
- Graphify final: `graphify update . --no-cluster` completed with **21.627 nodes / 39.791 edges**; follow-up `graphify check-update .` exit `0`.
- `node --test` repository-wide was attempted as requested, but automatic discovery includes vendored ECharts/Spectrum tests and historical backup payloads. It produced unrelated module/ESM/browser failures and was stopped; it is not a valid project suite command. The explicit affected suite above is the reliable result.
