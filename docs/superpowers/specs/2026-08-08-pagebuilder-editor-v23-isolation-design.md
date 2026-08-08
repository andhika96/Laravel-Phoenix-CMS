# Page Builder Editor v2.3 Isolation and Redesign

## Goal

Create a production Page Builder v2.3 that uses the latest v2.3 prototype design in full while preserving every function available in the current Page Builder Elementor editor. Page Builder v2.0 and v2.3 must remain independently maintainable and must not load each other's internal runtime files.

## Chosen approach

Use one `page_builder` table with an `editor_version` discriminator. Each saved page belongs to exactly one editor version. The two editors have separate routes, controllers, requests, views, JavaScript runtimes, widget modules, CSS, configuration, renderers, support services, and tests.

The shared boundary is deliberately narrow:

- Laravel and third-party vendor libraries may be shared.
- The neutral `Page_Builder` model and the `page_builder` table are shared.
- Public media files and CKFinder infrastructure are shared.
- No v2.3 editor, widget, renderer, configuration, or support file may import an internal v2.0 file.
- No v2.0 editor file may import an internal v2.3 file.

The existing `pagebuilder_elementor` implementation is the v2.0 baseline. Its current URLs and internal paths remain stable. v2.3 receives new suffixed namespaces and paths, avoiding a risky rename of the working v2.0 implementation.

## Visual source of truth

The authoritative design reference is:

`public/mockups/pagebuilder-editor-redesign-prototype-v2.3.html`

The v2.3 production shell must reproduce the prototype's information architecture and visible design, including:

- one contextual left sidebar;
- no dark tool rail;
- no Layers, Pages, or Global Styles tools;
- Elements shown when no canvas entity is selected;
- the same sidebar switching to the selected container, column, or widget settings;
- the latest top bar, canvas framing, spacing, sizing, color, typography, control, button, tooltip, and responsive behavior;
- no reintroduction of controls removed from the approved prototype.

The mockup supplies presentation and interaction structure. Production data and actions must come from the copied v2.3 runtime, not from mockup-only sample state.

## File and namespace isolation

v2.0 keeps its existing files. v2.3 uses the following dedicated boundaries:

- `app/Http/Controllers/Web/PageBuilderElementorV23/`
- `app/Http/Requests/Page_Builder_Elementor_V23/`
- `app/Support/PageBuilderElementorV23/`
- `resources/views/pagebuilder_elementor_v23/`
- `resources/data/pagebuilder_elementor_v23_shapes.json`
- `public/js/pagebuilder_elementor_v23/`
- `public/assets/css/pagebuilder_elementor_v23.css`
- `public/assets/css/frontend_elementor_v23.css`
- `config/pagebuilder_elementor_v23_widgets.php`
- version-specific PHP and Node tests whose names contain `V23` or `v23`.

The complete v2.0 widget tree is copied into the v2.3 JavaScript and Blade trees before the shell redesign. Definitions, Canvas components, Settings components, shared controls, Pro components, frontend runtime, registry, render partials, and supporting services therefore belong to v2.3 and can evolve without affecting v2.0.

Vendor assets such as Vue, Axios, Sortable, CKFinder, CKEditor, Bootstrap, Font Awesome, and the existing picker library are not duplicated. They are infrastructure rather than editor-version source.

## Routes

Existing v2.0 route names and URLs remain unchanged.

v2.3 uses:

- `GET /pagebuilder-elementor/v2.3/create`
- `POST /pagebuilder-elementor/v2.3/store`
- `GET /pagebuilder-elementor/v2.3/edit/{idOrSlug}`
- `POST /pagebuilder-elementor/v2.3/update/{idOrSlug}`
- `GET /pagebuilder-elementor/v2.3/data/{idOrSlug}`
- `GET /pagebuilder-elementor/v2.3/image-rendition`
- `GET /pagebuilder-elementor/v2.3/preview/{idOrSlug}`
- `POST /pagebuilder-elementor/v2.3/form/{idOrSlug}/{nodeId}`

Route names use `cms.core.pagebuilder_elementor_v23.*`. Every generated create, edit, update, preview, rendition, and form URL in v2.3 must reference that route family.

## Versioned data contract

Add a non-null `editor_version` string column to `page_builder`, indexed for editor lookups. Existing records are backfilled as `2.0`, and the database default is `2.0` so legacy writes outside the new v2.3 controller remain compatible.

The v2.3 create path always writes `editor_version = '2.3'`. The v2.3 edit, update, data, preview, and form submission paths query both the page identifier and `editor_version = '2.3'`. The v2.0 Elementor edit, update, data, preview, and form submission paths likewise require version `2.0`.

A version mismatch is never silently converted or saved. HTML requests receive a clear HTTP 409 response. JSON requests receive status 409 with the page's owning editor version and, when safe to expose, the correct edit URL. Missing records continue to return 404.

There is no automatic v2.0-to-v2.3 migration in this scope. A future explicit duplication or conversion workflow may be added only after layout-schema compatibility is separately designed and tested.

## Functional parity contract

v2.3 must preserve the current production behavior, not merely render a similar static shell. The parity baseline includes:

- create, edit, save, update, data loading, preview, and frontend rendering;
- undo and redo history;
- drag-and-drop from the Elements toolbox and movement within supported drop zones;
- container, fluid container, grid, row grid, columns, and nested widget behavior;
- all Basic, General, and Pro widgets registered by v2.0;
- Content, Style, and Advanced settings, including shared controls;
- desktop, tablet, and mobile editing and preview behavior;
- media selection, CKFinder, CKEditor, image renditions, custom fonts, and icon libraries;
- custom CSS editing and frontend application;
- dynamic preview context;
- interactive widget behavior in the editor and frontend runtime;
- form validation, throttling, submissions, email/webhook actions, messages, and redirect behavior;
- normalization and preservation of saved widget settings.

Before implementation is considered complete, an inventory test must compare the v2.0 and v2.3 registry/config catalogs and prove that every v2.0 widget type exists in v2.3. Intentional future v2.3 additions do not need to be added back to v2.0.

## Editor state and data flow

The v2.3 controller renders the v2.3 editor shell with version-specific save, rendition, preview, and form URLs. The shell publishes a v2.3 browser context object and loads only v2.3 CSS, registry, widget definitions, frontend runtime, and application runtime.

The Vue application owns the same page and layout data used by v2.0. When no entity is selected, the contextual sidebar renders the Elements catalog. Selecting a container, column, or widget keeps the canvas selection and replaces the sidebar body with that entity's settings. Returning to Elements clears only the selection/pending insertion state required by the current workflow; it does not mutate page content.

Saving serializes the v2.3 layout and custom CSS through the v2.3 request/controller. The server validates the version lock before writing. Preview and public rendering resolve only the v2.3 Blade renderer and v2.3 frontend runtime.

## Compatibility and rollout

- The v2.0 route family remains operational throughout implementation.
- Existing records remain editable through v2.0 after migration.
- Adding v2.3 must not change v2.0 HTML asset URLs, widget config, layout JSON, frontend renderer, or visual design.
- v2.3 can initially create new pages only. It does not claim or mutate existing v2.0 records.
- Database rollback removes only the `editor_version` column. It does not delete page records.
- Existing form submission rows remain associated through `page_builder_id`; version ownership is established through the parent page.

## Error handling

- Missing or malformed layouts retain the current normalization behavior and become an empty array rather than breaking the editor.
- Save failures keep the unsaved Vue state and show the current save error feedback.
- Widget module loading failures identify the v2.3 path and must not fall back to v2.0 modules.
- Unsupported or unknown widget types remain in saved JSON where existing normalization preserves them, but produce an explicit editor placeholder instead of crashing the shell.
- Version mismatch, missing page, validation, rendition, and form-action failures preserve their existing HTTP semantics except for the new explicit 409 version conflict.

## Verification

### Isolation

- Static tests assert that the v2.3 shell, config, registry, definitions, SFCs, renderers, and runtime reference only v2.3 internal paths.
- A reverse test asserts that v2.0 source does not reference v2.3 internal paths.
- Route tests prove that both route families resolve independently.
- Asset-loading smoke tests fail on any missing v2.3 JavaScript, Vue, CSS, data, or Blade file.

### Data lock

- Migration tests prove existing records become `2.0` and new v2.3 records become `2.3`.
- Controller tests prove each editor can read and write only its own records.
- Version mismatches return 409 and never change persisted layout, custom CSS, name, URI, or status.
- Preview, image rendition context, and form submission tests use the correct route family.

### Functional parity

- Registry and configuration inventory tests compare all v2.0 widget types with v2.3.
- Existing focused PHP and Node Page Builder tests are duplicated or parameterized against v2.3-owned files without importing v2.0 internals.
- Browser smoke tests cover create, add widget, nested drag/drop, select/edit, responsive switching, undo/redo, save, reload, preview, and frontend interaction.
- Representative Basic, General, layout, and Pro widgets are exercised visually and functionally.

### Visual fidelity

- Static contract tests preserve the prototype's single contextual sidebar and absence of the removed tools.
- Browser screenshots compare production v2.3 with the approved prototype at desktop, tablet, and mobile editor widths.
- Visual QA checks the initial Elements state, container settings, widget settings, responsive menus, canvas selection, empty canvas, populated canvas, loading, save success, and save error states.
- Production v2.0 screenshots are captured before and after implementation to prove its design did not change.

## Acceptance criteria

The work is complete only when:

1. v2.0 and v2.3 have independent internal source trees and route families.
2. The database locks each record to one editor version and rejects cross-version editing.
3. Production v2.3 matches the approved v2.3 prototype, including the single contextual sidebar and removed unsupported tools.
4. Every current v2.0 Page Builder function and registered widget is available from v2.3.
5. v2.0 behavior and design remain unchanged apart from the server-side version guard.
6. Both editors save, reload, preview, and render their own pages successfully.
7. Automated isolation, data-lock, parity, route, rendering, and static visual-contract tests pass.
8. Browser verification demonstrates the critical editor workflow and visual states.

## Non-goals

- Automatically converting an existing v2.0 page into v2.3.
- Adding Layers, Pages, Global Styles, or other unsupported prototype tools.
- Redesigning v2.0.
- Sharing v2.0 internal editor modules with v2.3 to reduce duplication.
- Adding new widget functionality unrelated to preserving the current production feature set.
