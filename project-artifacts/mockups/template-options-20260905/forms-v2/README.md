# Template Options form UX previews — Minimal Reading List

Design-only preview set for the Template Options modal. This artifact does not change production Blade, Vue, or CSS.

## What this set resolves

- Category filter `OFF` renders only its owner switch; dependent Position and Filter style controls are absent.
- Category filter `ON` reveals Position and Filter style in one vertical flow, so the controls cannot overlap.
- `Button list` previews the category search and wrapped category buttons inside a dedicated Categories panel.
- `Form select` previews one native category select in the archive toolbar and no button-list controls.
- Sidebar visibility, Categories, Popular Posts, and their Stay/Sticky controls are grouped by owner.
- Frame, spacing, color, numeric/unit, and responsive shell controls use the same label → control → helper rhythm.
- Numeric/unit compounds use a narrower unit column only after the numeric field has enough space to remain readable.

## Typography direction

The mockup uses a system-first sans stack and responsive `clamp()` tokens rather than arbitrary fixed pixel font sizes:

| Token | Range | Use |
| --- | --- | --- |
| `--fs-body` | 14–16px | base UI copy |
| `--fs-meta` | 11–13px | overlines, metadata, badges |
| `--fs-caption` | 12–14px | helper text, notes, preview copy |
| `--fs-label` | 13–15px | form labels, buttons, list titles |
| `--fs-section` | 17–21px | panel and preview headings |
| `--fs-modal` | 19–25px | modal title |
| `--fs-page` | 18–22px | artifact heading |

The scale follows the UI/UX Pro Max guidance for a consistent type hierarchy, readable labels, visible focus, responsive layout, and no tiny body text on mobile.

## Screenshot previews

- [Header content](screens/01-header-content.png)
- [Archive toolbar — Category filter off](screens/02-toolbar-category-off.png)
- [Archive toolbar — Button list](screens/03-toolbar-button-list.png)
- [Archive toolbar — Form select](screens/04-toolbar-form-select.png)
- [Post list](screens/05-post-list.png)
- [Reading list sidebar](screens/06-reading-list-sidebar.png)
- [Thumbnail](screens/07-thumbnail.png)
- [Pagination](screens/08-pagination.png)
- [Archive shell](screens/09-archive-shell.png)

## Interactive preview

Serve this directory and open `index.html` with one of these query strings:

```text
?view=header
?view=toolbar-off
?view=toolbar-button-list
?view=toolbar-form-select
?view=post-list
?view=sidebar
?view=thumbnail
?view=pagination
?view=shell
```

The preview is intentionally isolated under `project-artifacts/mockups` and uses no new dependency or external font request.
