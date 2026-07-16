# Arunika V3 Dashboard Shell Design

## Goal

Create a new, independently selectable `arunika_v3` CMS theme by forking the current Arunika V2 implementation and restyling only the shared dashboard shell to match the approved cropped reference image.

## Approved scope

- Preserve all dynamic CMS page content rendered through `@yield('content')`.
- Preserve role guards, dynamic menu data, active states, expandable submenus, logo settings, typography settings, dark mode, theme color controls, profile access, logout, and responsive sidebar behavior.
- Restyle the expanded sidebar, top header, and content-area surface.
- Add the compact header search, notification bell, and authenticated profile treatment shown by the reference.
- Add a bottom Settings entry to the sidebar while keeping profile/logout available from the header.
- Register Arunika V3 in the database seed data and Theme Manager without changing Arunika V1 or V2.

## Visual direction

- Expanded sidebar remains 256px for compatibility with the existing shell and visually matches the reference proportion at desktop widths.
- Sidebar uses a calm near-white gray surface without a drawn divider at the content boundary.
- Navigation is compact, low-shadow, and uses a white active item.
- Header is white and approximately 60px high, with search on the left and compact controls/profile on the right.
- Header and main content live inside one warm near-white right-side canvas with a small outer gutter on the top, right, and bottom, rounded outer corners, and no second frame around the main panel. Existing page content and cards retain their own layout and behavior.
- Purple remains the accent color; status and semantic colors remain owned by page content.

## Architecture

Arunika V3 is a full isolated fork of the live Arunika V2 theme files:

- `resources/views/themes/arunika_v3/`
- `public/assets/css/themes/arunika_v3/arunika_v3.css`
- `public/assets/js/themes/arunika_v3/arunika_v3.js`

The fork keeps proven V2 behavior but gives V3 independent Blade, CSS, and JavaScript files so future visual changes cannot regress V2.

## Acceptance criteria

- Arunika V1 and V2 files remain unchanged by the V3 visual work.
- Theme Manager lists and can activate Arunika V3.
- V3 renders the same dynamic navigation and page content as V2.
- V3 header exposes functional search, notifications, appearance controls, profile, Settings, and logout access.
- Desktop and mobile shell behavior remain usable.
- Focused static/feature tests pass, Blade compiles, and a runtime screenshot passes visual QA against the approved crop.
