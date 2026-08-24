# Article frontend template design QA

## Comparison target

- Source visual truth: `project-artifacts/mockups/20260824_article-template-directions/minimal-reading-list-manage-article-templates.png` (1487 x 1058).
- Implementation route: `https://laravel-13-phoenix.aruna/manage_article/templates`.
- Intended state: Archive Templates selected, Minimal Reading List persisted as default, desktop preview active.
- Browser implementation viewport: Codex in-app Browser default desktop viewport (1280 x 720 CSS px observed in the session).
- Implementation screenshot: `project-artifacts/qa/20260824_article-template-manager-implementation.png` (1280 x 720).
- Normalized side-by-side comparison: `project-artifacts/qa/20260824_article-template-manager-design-comparison.png` (1436 x 556). Both images were normalized to 700 px width in the contact sheet; browser chrome and the persistent CMS shell are treated as intentional environment framing.

## Visible QA evidence

- The rendered Manager uses the selected direction's split hierarchy: template selection at left, a large live preview at right, Archive/Detail tabs, device controls, and one Save Template action.
- The actual preview is an iframe rendering the real Archive/Detail template rather than a raster placeholder; Archive/Detail selection, Mosaic preview selection, and persisted Default vs unsaved Selected states were exercised without a console error.
- Article Archive rendered Minimal Reading List and linked successfully into the real Article Detail route.
- Full-view and focused selection/preview regions were compared in the normalized contact sheet.

## Required fidelity surfaces

- Typography: implementation uses the project’s existing CMS/display typography. The long-form Article hierarchy preserves the intended large editorial title and smaller metadata hierarchy.
- Spacing/layout rhythm: the manager preserves the source visual’s two-column selector/preview structure. The actual CMS header/sidebar is intentionally retained instead of duplicated from the design board.
- Colors/tokens: white surface, pale supporting backgrounds, and the existing purple action/selection token are consistent with the selected direction.
- Image quality/assets: Article cards and details use real uploaded Article thumbnail renditions; the manager’s live iframe avoids fake CSS/image thumbnails.
- Copy: Archive/Detail labels, template names, state labels, and pagination copy are product-facing and not mock copy.

## Findings

- No actionable P0, P1, or P2 mismatch found.
- [P3] The source board depicts visual thumbnail previews inside the template cards, while the implementation uses semantic icons and one real live iframe preview. This is an intentional v1 trade-off: the live preview cannot become stale when Article rendering changes.

## Comparison history

- Initial capture attempt: the existing in-app Browser tab could show screenshots inline but did not persist one locally; CDP capture timed out.
- Recovery: a fresh in-app Browser tab captured the rendered manager successfully. The resulting capture was normalized with the selected source design and reviewed again.

## Final result

final result: passed
