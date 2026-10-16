# Shadow spacing, Inset and swatch edges — 16 October 2026

Project: D:\Laragon\www\laravel-13-phoenix

## Changes

Only public/assets/css/awesome-admin-header-navigation-fullscreen.css changed for this request.

- Shadow metadata spacing increased to 20px, including measured clear space between Shadow color and the Inset row.
- Coloris swatch buttons now have an opaque solid border, padding-box background clipping and clipped overflow. The color overlay's extra inner shadow and corner radius were removed so checkerboard pixels cannot form a broken-looking edge around solid colors.
- Transparency checkerboards remain inside alpha colors; no color values or alpha settings were changed.

## Inset investigation

The existing linkShadowValue and linkShadowJson functions implement Inset as a boolean. It reuses X, Y, Blur, Spread and Shadow color; it does not reveal more controls. The preview applies the shadow to the active menu link.

Using temporary test values X=0, Y=0, Blur=8, Spread=4 and #00000080, Chrome computed:

- Off: rgba(0, 0, 0, 0.5) 0px 0px 8px 4px
- On: rgba(0, 0, 0, 0.5) 0px 0px 8px 4px inset

The Save request payload contained effects.link_shadow.inset=true. That request was intercepted, so no production settings were changed. The page was then reloaded to restore its saved values.

## Verification and context

- 85 focused browser checks passed; all 51 original fields, their select options, seven segmented groups and seven linked controls remain present.
- All 15 swatches have solid opaque edges and no overlay shadow. Transparency checkerboard retained.
- Inspected solid/transparent swatch screenshots and the outer/inner shadow screenshots under qa/.
- git diff --check passed.
- Backup hash verified before editing; exact path and SHA-256 recorded in backups/manifest.json.
- Context read: E:\AI\Memories\20261015-171349-phoenix-header-navigation-revamp-handoff.md.
- Graphify query: linkShadowValue linkShadowJson. Verified the identified functions in the current base JS and the active-link shadow rule in the base CSS. Also inspected Coloris pseudo-element computed styles and the fullscreen CSS.
- No Graphify update, database setting changes, commits or pushes.

Chrome runtime was tested. Firefox/Safari and the backend suite were not rerun for this CSS-only patch. Source evidence confirms the boolean implementation; browser evidence confirms the actual shadow and intercepted save payload.
