# Page Builder v2.4 Custom JavaScript Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menambahkan fitur page-level `Custom JavaScript` yang dapat disimpan/edit seperti Custom CSS, tetapi tidak menjalankan arbitrary JavaScript di editor dan hanya menjalankan kode melalui execution policy yang dipilih secara eksplisit.

**Architecture:** Custom JavaScript disimpan sebagai field page-level terpisah dari `custom_css`, dengan mode eksekusi default `disabled`. Editor hanya menampilkan/edit/analyze code; mode `exact_sandbox` mengirim kode ke iframe `static_html` yang terisolasi, sedangkan mode `published`—bila diaktifkan secara eksplisit dan page published—menjalankannya hanya pada frontend publik. Script hasil import tidak otomatis diaktifkan; user harus memilih dan mengonfirmasi penyimpanannya.

**Tech Stack:** Laravel 13, nullable `page_builder.custom_js` and `custom_js_mode`, Vue 3 editor state/modal, Blade frontend renderer, CSP/nonce-compatible inline script emission, JavaScript static analysis, existing `static_html` sandbox, PHPUnit, Node `node:test`, browser QA read-only.

**Spec:** Keputusan Custom JavaScript pada percakapan 2026-08-28; execution policy `disabled` by default; Exact Visual sandbox remains the safe source-preview fallback.

## Global Constraints

- Tidak mengubah v2.3 atau mengganti perilaku Custom CSS yang sudah ada.
- Tidak pernah menjalankan Custom JavaScript pada editor Canvas utama.
- Default `custom_js_mode` adalah `disabled`.
- `exact_sandbox` hanya berjalan di dalam widget `static_html` sandbox, bukan pada parent editor.
- `published` hanya berjalan pada frontend publik untuk page published dan setelah user mengaktifkan mode tersebut secara eksplisit.
- Import source scripts tidak otomatis disalin atau dieksekusi; modal hanya melaporkan dan menawarkan copy/preview setelah konfirmasi.
- Custom JavaScript menerima kode JS, bukan HTML `<script>` wrapper atau external `<script src>` tag.
- Script disimpan raw sebagai kode user, tidak dieksekusi server-side, tidak diproses melalui `eval`/`new Function` di aplikasi.
- Dangerous patterns wajib diberi warning atau ditolak sesuai policy; jangan menyatakan kode arbitrary aman hanya karena lolos parser.
- Maksimum payload dan output ditetapkan secara eksplisit sebelum implementasi, direkomendasikan `100 KB` per page.
- File Manager V2/ZIP asset tidak termasuk scope.
- Tidak auto-save, commit, push, deploy, atau browser action berisiko.

## Existing Source Map

- `app/Models/Page_Builder/Page_Builder.php` — model shared table; v2.4 reads the new nullable fields, v2.3 remains unaware.
- `database/migrations/` — add one nullable migration for `custom_js` and one mode field, with reversible down path.
- `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php` — Store/Update normalization and frontend page data.
- `app/Http/Requests/Page_Builder_Elementor_V24/AddPageBuilderElementorV24Request.php` and `EditPageBuilderElementorV24Request.php` — validate max length/mode when the existing request contract is extended.
- `public/js/pagebuilder_elementor_v24/app.js` — page settings, Custom CSS editor, Save payload, static import report/modal.
- `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php` — page context and editor asset loading.
- `resources/views/pagebuilder_elementor_v24/frontend_renderer.blade.php` — final public script emission and page status boundary.
- `resources/pagebuilder_elementor_v24/modules/widgets/basic/static-html/` — Exact Visual sandbox consumer.
- `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php` — source script extraction/reporting; source scripts remain disabled by default.
- Existing CSS editor styles in `public/assets/css/pagebuilder_elementor_v24.css` and existing v2.4 isolation tests.

## Data Contract

Add nullable page fields:

```text
custom_js       TEXT NULL
custom_js_mode  VARCHAR(24) NOT NULL DEFAULT 'disabled'
```

Allowed modes:

```text
disabled       stored only; never executed
exact_sandbox  executed only inside static_html Exact Visual iframe
published      executed only on published frontend after explicit enable
```

The editor context exposes `customJs` and `customJsMode`; Save/Update sends both fields. Existing rows normalize missing/null values to empty code + `disabled`.

## Security Policy

### Editor

- Never inject `customJs` into the editor document.
- Never load a page-level Custom JavaScript `<script>` in `app.js`.
- Show code, size, mode, warnings, and last-saved state only.

### Exact sandbox

- Pass code only to the `static_html` iframe after explicit `exact_sandbox` selection.
- Keep iframe sandbox without `allow-same-origin`, `allow-forms`, `allow-popups`, or top-navigation permissions.
- Bind only the iframe’s own DOM; do not provide parent references or bridge functions.
- Preserve source DOM IDs/classes for source scripts in Exact Visual mode.
- Use a nonce or carefully scoped inline execution path compatible with the existing iframe CSP; never weaken the parent page CSP.

### Published mode

- Emit code only when page status is `publish`, mode is `published`, and the current request is public frontend.
- Do not emit on editor, create, edit, draft preview, JSON, import, or authenticated admin routes.
- Escape `</script` sequences before raw script emission.
- Add a CSP nonce when the application’s existing CSP middleware supports it; otherwise document the current CSP boundary and keep the mode permission-gated.
- Never accept external script tags through this field.

### Static analyzer

Report, at minimum, use of `eval`, `new Function`, `document.cookie`, `localStorage`, `sessionStorage`, `fetch`, `XMLHttpRequest`, `WebSocket`, `window.open`, form submission, cross-origin URLs, timers, and DOM mutation. Warnings are visible in the modal; blocked patterns are not executed.

## Implementation Tasks

### Task 1: Add failing tests for storage and execution modes

**Files:**
- Create: `tests/Feature/PageBuilderElementorV24CustomJavaScriptTest.php`
- Modify: `tests/Feature/PageBuilderElementorV24RoutesAndPersistenceTest.php`
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`
- Create: `tests/Unit/PageBuilderElementorV24CustomJavaScriptPolicyTest.php`

**Steps:**

- [x] Add RED tests for default empty/disabled values on legacy page rows.
- [x] Add RED tests for Store/Update persisting valid code and mode.
- [x] Add RED tests rejecting oversized code, invalid mode, `<script>` wrappers, and external script tags.
- [x] Add RED tests that editor shell context contains state but no executable Custom JS script.
- [x] Add RED tests that frontend emits no Custom JS for disabled, draft, editor, or exact-sandbox-only cases.
- [x] Add RED tests that published mode emits only on published frontend.
- [x] Add RED tests for analyzer warning categories and dangerous-pattern blocking.
- [x] Run focused tests and record the expected failures.

**Done when:** Tests define storage, mode, editor isolation, public execution, and policy behavior before implementation.

### Task 2: Add reversible page-level storage

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_add_custom_javascript_to_page_builder_table.php`
- Modify: `app/Models/Page_Builder/Page_Builder.php` only if casts/accessors are needed
- Modify: `tests/Feature/PageBuilderElementorV24RoutesAndPersistenceTest.php`
- Test: `tests/Feature/PageBuilderElementorV24CustomJavaScriptTest.php`

**Steps:**

- [x] Verify current `page_builder` schema and existing test table definitions before writing the migration.
- [x] Add nullable `custom_js` text and non-null `custom_js_mode` with `disabled` default using a database-compatible migration pattern.
- [x] Add a reversible `down()` that removes only these new columns after explicit rollback approval.
- [x] Update test table setup so SQLite feature tests represent production schema.
- [x] Assert v2.3 and existing pages remain readable when fields are null/defaulted.
- [x] Run migration/schema-focused tests without touching real user data.

**Done when:** Existing pages migrate safely and missing Custom JS data normalizes to disabled.

### Task 3: Implement server normalization and static policy analyzer

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CustomJavaScriptPolicy.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
- Modify: `app/Http/Requests/Page_Builder_Elementor_V24/AddPageBuilderElementorV24Request.php`
- Modify: `app/Http/Requests/Page_Builder_Elementor_V24/EditPageBuilderElementorV24Request.php`
- Test: `tests/Unit/PageBuilderElementorV24CustomJavaScriptPolicyTest.php`

**Steps:**

- [x] Define constants for `MAX_BYTES`, allowed modes, blocked patterns, and warning patterns.
- [x] Normalize line endings and reject null bytes/control payloads while preserving ordinary JS text.
- [x] Reject `<script>` wrappers, `src=`, `javascript:` URLs, and HTML injection patterns at the request boundary.
- [x] Analyze code without executing it and return structured diagnostics with line/column when practical.
- [x] Normalize missing mode to `disabled`; force empty code to `disabled` regardless of requested mode.
- [x] Normalize Store/Update values through the same policy service, avoiding duplicated controller rules.
- [x] Keep policy service independent from v2.3 and from File Manager code.
- [x] Run unit tests for safe code, warning code, blocked code, Unicode, size boundary, and invalid mode.

**Done when:** Every persisted Custom JS value has one validated mode and structured diagnostics, with no server execution.

### Task 4: Add editor state and Custom JavaScript modal

**Files:**
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `public/assets/css/pagebuilder_elementor_v24.css`
- Modify: `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php` only for explicit context values if needed
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`
- Create: `tests/pagebuilder-v24-custom-javascript-editor.test.mjs`

**Steps:**

- [x] Add refs `customJs`, `customJsMode`, diagnostics, editor visibility, char/line counts, and dirty status without sharing the Custom CSS refs.
- [x] Add a Page Settings card below Custom CSS with summary `Disabled`, `Sandbox only`, or `Published frontend`.
- [x] Add `Open editor` modal with code textarea, line count, byte count, analyzer warnings, execution-mode selector, and explicit enable confirmation.
- [x] Default modal mode to `disabled`; require a second confirmation when changing to `published`.
- [x] Add `Save as Custom JavaScript`/Apply behavior that updates draft state only and marks page dirty.
- [x] Keep keyboard focus inside the modal, provide Escape close, visible focus ring, and `aria-live` diagnostics.
- [x] Add warning that compile/re-import does not automatically overwrite page-level Custom JS.
- [x] Do not inject code into the editor DOM during preview or while typing.
- [x] Add static tests that prove no `eval`, `new Function`, or dynamic script injection was added to the editor runtime.

**Done when:** User can edit and choose policy in the same page settings area without any code executing in the editor.

### Task 5: Integrate source-script report and opt-in Exact Visual sandbox execution

**Files:**
- Modify: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/basic/static-html/Canvas.vue`
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/basic/static-html/frontend.blade.php`
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/basic/static-html/runtime.js`
- Test: `tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php`
- Test: `tests/Feature/PageBuilderElementorV24StaticImportFrontendDependencyTest.php`

**Steps:**

- [x] Extend import report with script count, inline/external classification, detected event selectors, and analyzer warnings; do not include untrusted executable code in the report by default.
- [x] Add an explicit modal action `Copy detected scripts to Custom JavaScript` that requires user confirmation and copies only inline JS into the separate editor state.
- [x] Keep source scripts dropped in `native` mode; the separate compiled-native plan remains responsible for any future `compiled` mode.
- [x] For `static_html` exact mode, append page Custom JS only when mode is `exact_sandbox`; never pass it to the parent document.
- [x] Ensure source DOM IDs/classes and load order remain available inside the iframe.
- [x] Ensure sandbox messages contain only height/diagnostic data and cannot call parent functions.
- [x] Add tests proving disabled/custom native modes do not execute source scripts, while exact sandbox has an opt-in code path.
- [x] Run the available Blade/feature smoke evidence; browser smoke is recorded as not run because no controllable authenticated session was available.

**Done when:** Source scripts are visible as diagnostics/copy candidates and only run in the explicitly selected Exact Visual sandbox.

### Task 6: Add explicit published frontend execution boundary

**Files:**
- Modify: `resources/views/pagebuilder_elementor_v24/frontend_renderer.blade.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php` only if public/draft context needs an explicit attribute
- Create/Modify: existing CSP middleware/helper only after source audit confirms the integration point
- Test: `tests/Feature/PageBuilderElementorV24CustomJavaScriptTest.php`

**Steps:**

- [x] Add a server-side predicate `shouldRenderPublishedCustomJs($pageData, $request)` that requires mode `published`, status `publish`, public route, and validated code.
- [x] Emit one final script block after the v2.4 frontend runtime, never before page DOM exists.
- [x] Escape closing script tags and render only the normalized stored code.
- [x] Add a stable `data-pb-custom-javascript` marker for observability and QA.
- [x] Keep editor shell, preview, import endpoint, JSON response, and authenticated admin routes excluded.
- [x] If the project already has CSP nonce infrastructure, attach the nonce; otherwise document the exact current boundary and keep execution behind the explicit mode/permission gate.
- [x] Add tests for public published execution, draft suppression, editor suppression, empty code, invalid mode, and cross-version isolation.
- [x] Do not load external source scripts automatically.

**Done when:** Published execution is opt-in, status-gated, route-gated, and absent from all editor/admin surfaces.

### Task 7: Add permission, audit, and UX warning boundary

**Files:**
- Inspect/Modify: existing v2.4 authorization/policy capability source identified by Graphify/source audit
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
- Test: `tests/Feature/PageBuilderElementorV24CustomJavaScriptTest.php`

**Steps:**

- [x] Identify the existing editor permission boundary; do not invent a parallel auth system if a reusable capability exists.
- [x] Reuse the existing authenticated editor and page-ownership boundary for switching/saving; no parallel capability is introduced because no dedicated v2.4 seam exists.
- [x] Show a warning with exact effect: code can read/change public page DOM and issue browser requests.
- [x] Record mode changes and analyzer result in application logs/audit trail only if an existing audit seam exists; no v2.4 audit seam was found, so no full code or sensitive values are logged.
- [x] Add tests for unauthorized mode change, authorized disabled/sandbox use, and no data leakage in logs/responses.

**Done when:** Arbitrary code is treated as a privileged page capability, not as an ordinary style setting.

### Task 8: Final verification and artifact evidence

**Files:**
- Create: `project-artifacts/qa/pagebuilder-v24-static-import-20260828/QA_REPORT_09-custom-javascript-20260828.md`
- Modify: no unrelated production files

**Steps:**

- [x] Run migration/policy/editor/frontend focused PHPUnit and Node tests.
- [x] Run full `node --test tests/pagebuilder-v24-*.test.mjs` and record the count: 411/411.
- [x] Run `php artisan test --filter=PageBuilderElementorV24`; classify 419 harness failures separately from feature failures.
- [x] Run PHP/Blade/JS syntax checks, view cache/clear where relevant, and `git diff --check`.
- [x] Verify editor page settings has no executable script tag and Custom CSS remains unchanged.
- [x] Verify disabled/draft/editor pages contain no `data-pb-custom-javascript` executable block.
- [x] Verify exact sandbox executes only inside iframe when opted in and parent DOM remains isolated.
- [x] Verify published mode appears only on a published public frontend page.
- [x] Verify import source scripts remain dropped by default and copying requires explicit confirmation.
- [x] Run browser QA read-only if authenticated session is available; no controllable authenticated session was available, so Blade/feature evidence is recorded instead.
- [x] Update Graphify incrementally, excluding backups, QA, generated output, and secrets.
- [x] Review `git status --short`, preserve all backups, and do not commit/push/deploy.

**Done when:** Storage, UI, policy, sandbox, published boundary, and cross-version isolation are all evidenced.

## Rollback

Rollback is reversible: disable the migration/code path, restore the pre-change source files from timestamped backups if necessary, and retain stored `custom_js` data rather than deleting it. Existing pages default to `disabled`, so removing execution code does not execute or expose stored scripts.

## Explicit Non-Goals

- Automatically translating arbitrary JavaScript into native widget behavior.
- Executing imported source scripts by default.
- Allowing external script tags or arbitrary third-party script loading.
- Running Custom JavaScript in the editor Canvas parent document.
- Making File Manager V2 resolve relative images.
