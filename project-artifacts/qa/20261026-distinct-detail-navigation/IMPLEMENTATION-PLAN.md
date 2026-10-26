# Distinct Detail Navigation Implementation Plan

> Execute inline in this session using Superpowers executing-plans and test-driven-development. User approved the26October preview and explicitly requested direct implementation.

**Goal:** Focused Reader keeps Reading Cards; Editorial Feature uses Story Spread; Knowledge & TOC uses Knowledge Links.

**Architecture:** Keep the common display/neighbor gate in detail-navigation. Select two new Blade partials from the existing callers. Scoped CSS styles the new markup; no JS, dependency, setting, database or content changes.

**Tech Stack:** Laravel Blade, existing Font Awesome, article frontend CSS, PHP feature tests and Edge Chromium.

**Spec:** project-artifacts/mockups/20261026-distinct-detail-navigation/detail-navigation-concepts.html and PREVIEW-QA.md approved in chat.

## Constraints and rulings

- All backups, planning and QA files stay inside project-artifacts per user instructions.
- Reuse the current checkout for the user's running local app; preserve untracked mockup/preview directories from other work. No commit, push, branch change or database template switch.
- Graphify remains read-only until requested. Actual source was checked after the graph query.
- Direct execution is already authorized; no second approval gate for this implementation plan.
- Keep all CMS typography, CKEditor content styles, article eligibility, existing routes and saved settings.
- Scope is small and sequential; verify the final change directly without delegating to a model outside the user's named options.

## Review focus

1. Both/previous-only/next-only/no neighbors and navigation disabled.
2. Missing cover, missing small image, absent category and empty content.
3. Escaped titles, route destinations and minimum read time.
4. Long unbroken titles, narrow content containers, keyboard focus and reduced motion.
5. Focused Reader output unchanged; preview and frontend share the new renderers.

## Task1: template-specific navigation

Files:modify shared detail-navigation, Editorial Feature and Knowledge & TOC callers, article frontend CSS. Create detail-story-spread and detail-knowledge-links Blade partials. Tests/artifacts in this folder.

- [x] Write rendering tests for distinct wrappers/links, neighbor combinations, display gate, escaped titles, metadata and cover fallback.
- [x] Run tests and confirm expected failure before editing application source.
- [x] Add the two guarded dispatcher branches, two partials and scoped CSS matching the approved preview. Keep Reading Cards unchanged.
- [x] Run article PHP tests, existing presentation/manager/fixture JS checks, lint/Blade compilation and diff check.
- [x] Verify actual renderer in browser at320/390/576/834/1024/1440px; hover/focus/reduced motion, one-card layout and long titles.
- [x] Recheck saved settings unchanged and CKEditor content regression. Review the final diff and report current evidence/limits.
