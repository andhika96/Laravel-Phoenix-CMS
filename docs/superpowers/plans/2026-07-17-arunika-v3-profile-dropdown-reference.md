# Arunika V3 Profile Dropdown Reference Implementation Plan

## Scope

Implement the approved reference-driven profile dropdown in the existing Arunika V3 sidebar without changing routes, account logic, or theme persistence.

## Tasks

1. Back up the Blade layout, Arunika V3 stylesheet, focused static test, and current design QA report.
2. Extend `tests/arunika-v3-header-actions-static.test.mjs` with failing assertions for the new dropdown structure and CSS contract.
3. Run the focused Node test and confirm the new assertions fail before implementation.
4. Update `resources/views/themes/arunika_v3/cms/cms_layout.blade.php` with the dynamic user summary, action rows, Dark Mode switch, collapsible theme colors, and outside-only auto-close behavior.
5. Update `public/assets/css/themes/arunika_v3/arunika_v3.css` with the 240px card treatment, compact user summary, 40px rows, switch, collapse, and dark-theme-compatible states.
6. Re-run the focused Node test and confirm it passes.
7. Run the Arunika V3 static suite, Laravel test suite, Blade view compilation, and served CSS checks; distinguish unrelated pre-existing failures from regressions.
8. Capture the authenticated open dropdown using the allowed browser workflow, compare it with the supplied reference, and record `design-qa.md` as passed or blocked with evidence.

## Commands

```powershell
node --test tests/arunika-v3-header-actions-static.test.mjs
node --test tests/arunika-v3-*.test.mjs
php artisan test --compact
php artisan view:clear
php artisan view:cache
```

## Completion criteria

- New focused assertions pass.
- No regression is introduced in the Laravel suite.
- Existing dynamic profile behavior and seven theme colors remain intact.
- Visual QA has an explicit final result with reference and implementation evidence, or a precise authenticated-browser blocker.
