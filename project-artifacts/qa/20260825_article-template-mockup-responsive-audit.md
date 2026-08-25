# Article Template Mockup and Responsive Audit

Date: 2026-08-25  
Scope: all five Archive and three Detail templates in the Article Template preview. Thumbnail appearance is explicitly excluded from this audit.

## Evidence

- Approved references:
  - `project-artifacts/mockups/20260825_article-editorial-studio/archive-template-board.png`
  - `project-artifacts/mockups/20260825_article-editorial-studio/detail-template-board.png`
- Runtime screenshots, before and after responsive polish:
  `project-artifacts/qa/20260825_article-template-audit/`
- Fresh browser capture covered all eight templates at desktop `1440x900`, tablet `834x1112`, and mobile `390x844`.

## Verdict

### Responsive live preview: pass after remediation

- All eight templates have visible headings, no horizontal overflow, visible search actions where a search form exists, and visible pagination chevrons at all three viewport profiles.
- The Manage Article Template iframe confirmed its tablet `834x1112` and mobile `390x844` device frames, scaled to fit with an empty console.
- Search on the public Archive route was verified with `?search=Load%20Test`: the control retained its value and returned 12 visible Articles from 5,000 matching records.

### Exact mockup fidelity: partial, not 100 percent

The current build follows the editorial direction, but it is not a literal match to the approved boards. The following differences are deliberate findings, not thumbnail findings:

1. **Archive form pattern differs materially from the mockup.**
   Every Archive mockup presents a compact `All` category selector in the header. Runtime currently has a wide search bar in Minimal Reading List; compact search controls in Mosaic Magazine and Mosaic Classic; no search/filter in Editorial Journal; and a numeric category-ID input plus Filter button in Balanced Card Grid. The numeric field is functional but is not understandable as a category picker for an ordinary CMS user.

2. **Archive layout composition is not a 1:1 implementation of the boards.**
   Minimal Reading List renders long excerpts; Mosaic Magazine uses a wide image plus dark content panel; Mosaic Classic uses a lead card plus side list; and Balanced Card Grid renders three desktop columns. The boards specify denser and different compositions, including a two-column Balanced grid.

3. **Pagination alignment differs.**
   Runtime uses a left-aligned result summary with page controls on the right at desktop. The archive board shows centred page controls with the summary beneath. The current runtime is usable and now displays its chevrons, but it is not pixel/structure-identical to the board.

4. **Detail templates retain a looser surface than the boards.**
   Focused Reader and Editorial Feature do not use the same contained panel treatment as the board. Knowledge + TOC also does not include the board's separated right-rail CTA treatment. Their hierarchy and neighbors work, but exact visual parity remains incomplete.

## Responsive fixes implemented in this pass

- Reduced tablet heading scale from `32.9px` to `30.1px` and heading bottom margin from `40px` to `28px`.
- On mobile, raised horizontal breathing room from `10px` to `16px`, reduced list title scale from `23.14px` to `19.6px`, and tightened list/card gaps.
- Removed stacked Editorial Journal whitespace by changing its mobile/tablet lead padding and gap from `32/48px + 40px` to `0/32px + 20px` at mobile.
- Added an explicit accent Search button to Minimal Reading List and visible icon-plus-text search actions to Mosaic Magazine and Mosaic Classic; all have a 44px mobile hit area.
- Loaded the existing local Font Awesome asset in the isolated preview and corrected pagination chevrons from `far` to `fas`, removing empty previous/next buttons.

## Automated verification

- `php artisan test tests/Feature/Article --testdox` — 13 passed, 142 assertions.
- Relevant Node tests — 23 passed, 0 failed.
- `php artisan view:clear`, `php artisan view:cache`, and `git diff --check` passed.
- Browser console warnings/errors were empty for the final responsive matrix and the Manage Article Template device preview.
- `graphify . --update --no-viz --code-only` refreshed `graphify-out/graph.json` at 2026-08-25 03:13:18 +07:00.

## Scope held back intentionally

This pass did not rewrite the archive form contract, template composition, or the Detail template information architecture. Achieving a literal 100 percent board match requires an explicit decision to standardise all five Archive templates on a category dropdown, decide whether search remains a secondary control, and approve the revised Archive/Detail compositions.
