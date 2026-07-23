# Image Box Title and Text Effects UI Implementation Plan

1. Back up every existing file in scope and preserve unrelated working-tree changes.
2. Add failing regression tests for automatic title-tag scale, custom Typography override, and compact coordinated text-effect popovers.
3. Add `titleFontSizeMode` defaults and backward-compatible normalization.
4. Wire Typography Size edits/reset to custom/auto mode.
5. Align the canvas and Blade renderer with the same tag-size map.
6. Refactor Text Stroke and Text Shadow into controlled compact popovers and coordinate them in Image Box settings.
7. Run focused and related tests, then browser/design QA at the reported UI state.
8. Update Graphify incrementally and report verified versus unverified results.
