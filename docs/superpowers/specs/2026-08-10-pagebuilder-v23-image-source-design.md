# Page Builder v2.3 Image Source Design

## Context

The Page Builder v2.3 Image widget currently accepts images only through CKFinder. Elementor keeps the image's click destination under a separate Link control and supports remote images through its media modal. Page Builder v2.3 will provide the equivalent remote-image capability directly in the widget settings without changing CKFinder or conflating image source with link behavior.

The v2.0 and v2.3 Image widget modules are currently equivalent after namespace normalization. This change is intentionally isolated to v2.3.

## Goal

Allow an editor to choose either CKFinder or a direct external image URL as the input method for the v2.3 Image widget, while keeping the existing canvas and frontend rendering contract based on `settings.src`.

## Non-goals

- Do not add Caption, Image Resolution, Link, or Lightbox controls.
- Do not customize CKFinder or add an Insert from URL screen to CKFinder.
- Do not change Page Builder v2.0.
- Do not change page persistence endpoints or save behavior.
- Do not download, proxy, or copy remote images into local storage.

## UX Design

Inside **Content > Image**, add an **Image Source** select before the current image control:

- **CKFinder** is the default and shows the existing media preview, Choose Image action, and Remove Image action.
- **External URL** shows an **Image URL** input with an `https://example.com/image.jpg` placeholder and concise help text explaining that a direct HTTP or HTTPS image URL is required.
- **Alt** remains directly below the source-specific control.

Changing the input method does not clear the current `src`. This prevents accidental data loss and lets the current image remain visible until the editor chooses or enters a replacement. The canvas continues to update from `src` immediately.

## State And Data Flow

- `settings.imageSource` records the selected input method as `ckfinder` or `url`.
- Missing or unknown `imageSource` values fall back to `ckfinder`, preserving existing nodes.
- Both input methods write the active image location to the existing canonical `settings.src` field.
- The new editor-only setting is retained by the existing widget settings object; it does not require a second renderer path.
- Canvas and frontend continue reading `settings.src`, so saved and duplicated widgets retain the selected image.

## Error Handling

- The external field uses a URL input and is labelled as accepting direct HTTP or HTTPS image URLs.
- An empty URL produces an empty image source after the user clears it.
- A remote host may reject hotlinking or later remove the asset; the builder will not proxy or silently upload the image.
- Switching back to CKFinder remains available as the recovery path.

## Compatibility And Isolation

- Existing v2.3 Image nodes without `imageSource` keep CKFinder behavior and render their current `src` unchanged.
- The v2.0 widget tree and renderer remain untouched.
- The v2.3 widget registry, layout schema, routes, and persistence format remain unchanged.
- The page used for the MG 5 GT builder evaluation must remain unsaved unless the user explicitly changes that instruction.

## Verification

Implementation will follow TDD and verify:

1. The v2.3 settings module exposes Image Source with CKFinder and External URL options.
2. CKFinder remains the fallback for legacy or unknown source values.
3. External URL mode binds the direct URL to `settings.src` and keeps Alt available.
4. The v2.3 canvas renders the supplied remote `src`.
5. The v2.3 frontend renderer outputs the supplied remote `src` and escaped Alt text.
6. Existing v2.3 widget parity, route, persistence, and frontend suites remain green.
7. Chrome runtime QA confirms the conditional controls and live canvas update using the supplied MG asset URL.
8. Page Builder Save is not clicked during the MG page evaluation.

## Expected Implementation Scope

- Modify the v2.3 basic Image settings component.
- Add focused v2.3 regression coverage for the source selector and remote rendering contract.
- Change the v2.3 definition, canvas, or frontend renderer only if a failing test proves it is necessary.
- Update Graphify incrementally only if the source change materially changes graph relationships.
