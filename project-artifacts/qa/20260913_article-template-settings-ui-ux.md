# Article Template Settings UI/UX QA

Date: 2026-09-13  
Project: `D:\Laragon\www\laravel-13-phoenix`

## Final scope

- Align Template Options with the compact form rhythm already used by the CMS.
- Keep native inputs and selects instead of introducing oversized custom choices.
- Normalize typography without nested `em` shrinkage.
- Restore visible spacing between direct Editorial Journal settings.
- Preserve Vue state, preview, persistence, normalizers, and frontend rendering.

## Root cause of the rejected pass

- The prior pass raised controls from 36px to 40px and wrapped every setting in a large surface card.
- Low-cardinality selects were replaced by segmented controls that became visually dominant.
- Label containers used relative `em` sizes, so nested form text became smaller again.
- Editorial Journal `fieldset` elements did not establish a grid, so direct child settings had no reliable vertical gap.

## Final visual contract

- Form controls: 36px.
- Small actions: 34px.
- Field text: 13px-14px clamp, bounded around the CMS adaptive font token.
- Labels: 12px-13px clamp.
- Meta text: 11px-12px clamp.
- Setting-group gap: 20px.
- Related-field gap: 14px.
- Editorial Journal fieldsets: transparent, divider-based, no large surface cards.
- Number inputs retain native semantics while browser spinner chrome is hidden.
- Scroll padding keeps keyboard-focused fields clear of the modal footer.

## Categories checked

| Category | Contract checked |
| --- | --- |
| Header content | Existing compact switch, input, textarea, helper rhythm retained |
| Archive toolbar | Existing native/select/button-group pattern retained |
| Grid columns | Existing geometry retained; button groups expose pressed state |
| Editorial Journal | Direct child gap restored; native selects, switch rows, dimensions, radius, color, height, Read More |
| Thumbnail | Existing native display mode/image fit and frame controls retained |
| Pagination | Existing compact range, frame, radius, padding, and margin controls retained |
| Article title | Existing full-width heading-level select retained |
| Archive/Detail shell | Existing compact spacing and frame groups retained |
| Minimal Reading List | Existing Post list and Sidebar form pattern retained |

## TDD evidence for the correction

RED:

- `node --test tests/article-template-presentation.test.mjs`
- Result before correction: 36 passed, 2 failed.
- Expected failures: compact typography contract absent; segmented/surface-card markup still present.

GREEN:

- `node --test tests/article-template-presentation.test.mjs`
- Result after correction: 38 passed, 0 failed.

## Browser boundary

- The user-provided screenshot was used as runtime evidence for the rejected pass.
- The separate QA browser still redirects Manager to `/auth/login`.
- No login, Apply changes, Save Template, or persistence action was automated.
- A fresh authenticated hard-reload screenshot is still required for final visual confirmation.
- Graphify was queried only and was not updated.
