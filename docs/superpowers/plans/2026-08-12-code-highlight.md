# Code Highlight implementation plan

1. Add a Pro registry entry and a `code-highlight/definition.js` with complete defaults and normalization for Content, Style, responsive values, and shared Advanced settings.
2. Extend the shared Pro Settings template/options with every Content and Style control from the approved mapping.
3. Extend the shared Pro Canvas with safe line rendering, token classes, line-number/highlight/wrap/theme behavior, responsive styles, and copy interaction.
4. Extend the v2.3 label/icon/Advanced capability maps and Blade renderer with matching settings, CSS variables, safe markup, and copy source.
5. Extend `frontend-runtime.js` with idempotent copy initialization and fallback behavior.
6. Run focused Node/PHP tests, syntax checks, `git diff --check`, and read-only browser QA; update Graphify incrementally after source changes.
