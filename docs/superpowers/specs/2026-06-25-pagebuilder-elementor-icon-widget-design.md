# PageBuilder Elementor Icon Widget Design

## Goal
Add a new `Icon` widget under `Basic` with Elementor-like content controls and an Elementor-style icon library modal, while fully syncing the `pagebuilder_elementor` editor shell from Font Awesome `6.5.1` to the local Font Awesome Pro `5.15.3` package.

## Scope
- Switch `resources/views/pagebuilder_elementor/editor_shell.blade.php` to load Font Awesome Pro `5.15.3`.
- Remap existing builder chrome icons that currently use Font Awesome 6-only class names so the editor UI stays visually intact after the version sync.
- Add a new `Icon` basic widget to the sidebar palette, editor state, canvas preview, and frontend renderer.
- Add `Icon` widget `Content` controls for:
  - icon selection
  - `View`: `Default`, `Stacked`, `Framed`
  - `Shape`: `Circle`, `Rounded`, `Square` for `Stacked` and `Framed`
  - `Link`
  - `Link Options`: `Open in new window`, `Add nofollow`, `Custom Attributes`
- Add an Elementor-style icon library modal with local Font Awesome-backed data and groups:
  - `All Icons`
  - `Font Awesome - Regular`
  - `Font Awesome - Solid`
  - `Font Awesome - Brands`
  - `Font Awesome - Light`
  - `Font Awesome - Duotone`
- Keep parity across the three builder layers:
  - editor state in `public/js/pagebuilder_elementor/app.js`
  - canvas preview widget component
  - frontend Blade renderer output

## Out of Scope
- `Icon` widget `Style` tab
- `Icon` widget `Advanced` tab
- user-uploaded custom icon libraries
- frontend-wide Font Awesome migration outside `pagebuilder_elementor`
- non-builder theme or CMS pages that still use another Font Awesome version

## Data Shape
The first-pass `Icon` widget settings should stay compact and explicit:
- `iconStyle`
- `iconName`
- `iconClass`
- `view`
- `shape`
- `link`
- `openInNewWindow`
- `nofollow`
- `attributes`
- `cssClass` for parity with the current basic-widget convention

`attributes` should follow the builder's existing attribute-array convention rather than inventing a new format.

## Expected Result
- The editor shell uses local Font Awesome Pro `5.15.3` without breaking builder chrome icons.
- A new `Icon` tile appears in `Basic`.
- Selecting the `Icon` widget opens Elementor-like content controls.
- Clicking the icon chooser opens a local icon library modal with grouped filtering and search.
- Inserting an icon updates the selected widget immediately in the sidebar preview and canvas preview.
- The saved node renders matching icon markup on the frontend output path.
- Existing builder behavior for other widgets stays intact after the Font Awesome version sync.

## Technical Notes
- Use local package assets and metadata under `public/assets/plugins/fontawesome/5.15.3`.
- Reuse current builder patterns instead of introducing a separate icon-management subsystem.
- Prefer class-based icon rendering for the first pass so preview and frontend output stay simple and close to how the existing app already renders Font Awesome.
- Keep the modal logic inside the current builder app flow unless a small helper extraction clearly reduces duplication.

## Verification Target
- syntax checks still pass
- the editor shell serves with HTTP `200`
- the new widget is present in the builder UI
- the icon library modal exposes the agreed groups and search
- inserted icons render in canvas preview and frontend markup
