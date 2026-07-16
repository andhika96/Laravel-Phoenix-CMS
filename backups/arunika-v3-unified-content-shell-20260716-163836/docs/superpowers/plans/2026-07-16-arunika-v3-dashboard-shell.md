# Arunika V3 Dashboard Shell Implementation Plan

> Execute in this session with test-first checkpoints. Do not create a commit unless the user explicitly asks.

**Goal:** Add an isolated Arunika V3 theme whose shared CMS shell matches the approved dashboard reference without replacing any page's content.

**Architecture:** Clone the live Arunika V2 Blade/CSS/JS files into a new V3 namespace, apply scoped V3 shell markup and styles, then register the new theme through the existing Theme Manager. Preserve V2's dynamic menu, configuration, role, and responsive behavior.

**Tech stack:** Laravel Blade/PHP, Bootstrap, Font Awesome, vanilla JavaScript, Node test runner, PHPUnit.

---

### Task 1: Lock the V3 contract with a failing static test

- Add `tests/arunika-v3-theme-static.test.mjs`.
- Assert the isolated V3 file structure, V3 asset references, dynamic content slot, header/profile hooks, theme registration, and preview asset.
- Run the test and confirm it fails because V3 has not been scaffolded.

### Task 2: Back up shared registration files and scaffold V3

- Back up the existing seeder, Theme Manager controller, feature test, and QA report before editing.
- Copy only the live Arunika V2 theme files into V3 paths, excluding embedded backup artifacts.
- Rename internal V2 namespace and asset references to V3.

### Task 3: Implement the dashboard shell

- Add a V3 body scope.
- Refine the sidebar footer and top header markup while preserving dynamic data and controls.
- Add scoped CSS for reference-aligned sidebar, header, search, profile, and content surfaces.
- Preserve collapse, submenu, mobile, typography, color, and dark-mode behavior.

### Task 4: Register and activate Arunika V3

- Add Arunika V3 to seed data and the Theme Manager allowlist/metadata/order.
- Add an idempotent migration for existing databases.
- Add the approved reference as the initial Theme Manager preview.
- Run the migration and activate V3 locally for inspection.

### Task 5: Verify behavior and visual fidelity

- Run the new static test plus existing Arunika V2 and Theme Manager regression tests.
- Run the Theme Manager feature test, PHP syntax checks, and Blade view compilation.
- Open the local CMS with the in-app browser, capture desktop and narrow screenshots, compare them against the reference, and correct meaningful visual differences.
- Record the final QA result in `design-qa.md` and only hand off after required checks pass.
