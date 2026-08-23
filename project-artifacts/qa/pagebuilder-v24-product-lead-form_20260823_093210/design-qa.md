# Product Lead Form v2.4 Design QA

## Source visual truth

- MG Test Drive reference: `mg-testdrive-reference-desktop.png`
- Reference URL: `https://www.mgmotor.id/testdrive?model=mgs5ev`
- Reference pixels: 1405 x 544

## Rendered implementation

- Public QA page: temporary `product-lead-form-qa-*` page, removed after QA
- Implementation capture: `public-product-lead-desktop-1405x544.jpg`
- Implementation pixels: 1405 x 544 after top-crop normalization
- State: child-only `variant=long-range`; runtime inferred model and type; `utm_source` and hash preserved
- Browser CSS mobile check: actual controlled viewport 433 x 938; body collapsed to one grid column; horizontal overflow was false
- Console errors/warnings: none (`[]`)

## Comparison

- Structure matches the requested MG flow: level selector first, then media/detail and form in a two-column layout.
- Product Lead Form intentionally uses the Page Builder neutral theme instead of MG branding.
- The QA fixture contained two models, so its model cards are wider rectangles; production defaults remain five model columns with circular model media (`imageRadius: 50%`).
- Product selection changes updated hidden submission values and URL query keys without navigation.
- Media, title, description, and detail metadata inherited from the selected parent/child chain.

## Editor evidence and limitation

- `editor-selector-interaction-reference.png` records the pre-fix Canvas interaction issue and empty child level.
- `editor-toast-contained-reference.png` and `editor-modal-contained-reference.png` record the pre-fix overlay containment issue.
- The controllable browser session was not authenticated, so a fresh post-fix editor screenshot could not be captured. The post-fix Canvas is covered by SFC compilation, source contract tests, and the Teleport/z-index implementation, but editor visual parity remains unverified until an authenticated hard reload.

## Final result

blocked

Blocker: authenticated editor runtime screenshot/DOM verification after the selector interaction and Canvas Toast/Modal fixes is still required.
