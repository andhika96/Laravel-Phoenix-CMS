# Video Image Overlay Core Design

## Goal
Bring the `Video` widget's `Image Overlay` content control closer to Elementor for the core flow only: the overlay section should be available for standard video sources, expose the existing choose-image flow clearly, and keep the overlay click-to-play behavior aligned between editor preview and frontend output.

## Scope
- Reuse the current `imageOverlay` and `overlayImage` settings.
- Keep the existing CKFinder-backed `chooseMedia` flow.
- Keep the current overlay rendering path in `public/js/pagebuilder_elementor/widgets/basic/Video.vue` and `resources/views/pagebuilder_elementor/partials/render_node.blade.php`.
- Limit the code change to the source-gating logic and its regression coverage.

## Out of Scope
- `Image Resolution`
- `Play Icon`
- `Icon`
- `Lightbox`
- New frontend styling sweeps outside the current overlay flow

## Expected Result
- `Image Overlay` appears in the `Video` widget content settings for iframe-based video sources, not only hosted-file sources.
- When the toggle is enabled and an overlay image is chosen, the editor preview and frontend renderer both keep the click-to-play cover behavior.
- Existing hosted-video overlay behavior keeps working.
