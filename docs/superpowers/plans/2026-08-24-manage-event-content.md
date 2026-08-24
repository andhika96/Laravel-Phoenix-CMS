# Manage Event Content Implementation Plan

> **For agentic workers:** Implement the tasks in order and keep the focused Event tests green after each task.

**Goal:** Ship isolated Event content management and authenticated browsing without modifying Article.

**Architecture:** New Event models, migrations, requests, resources, controllers, Blade views, and Vue assets reuse Phoenix CMS conventions. The active menu/permission seed path is updated idempotently through a migration.

**Tech Stack:** Laravel 13, Eloquent, Blade, Vue 3 global runtime, Axios, Bootstrap, CKEditor/CKFinder, MySQL-compatible migrations, PHPUnit.

**Spec:** `docs/superpowers/specs/2026-08-24-manage-event-content-design.md`

## Global Constraints

- Keep Article and Meeting Room/Asset Booking outside scope.
- Use `database/migrations`, not `database/migrations_new`.
- Use named short database indexes compatible with MySQL identifier limits.
- Preserve existing user changes and create backups before editing existing files.

### Task 1: Event persistence contract

**Files:**
- Create: `database/migrations/2026_08_24_000001_create_event_categories_table.php` through `000006_sync_manage_event_navigation_and_permissions.php`
- Create: `app/Models/Event/`
- Test: `tests/Feature/Event/EventSchemaTest.php`, `EventNavigationMigrationTest.php`

- [x] Create Event/category/occurrence/settings/registration tables with indexes and 24-hour defaults.
- [x] Add Eloquent relations and datetime casts.
- [x] Add active menu parent, normalized legacy links, and CRUD permission rows.
- [x] Verify migrations on SQLite and the local MySQL runtime.

### Task 2: Admin and attendee HTTP surfaces

**Files:**
- Modify: `routes/web.php`, `routes/breadcrumbs.php`
- Create: `app/Http/Controllers/Web/Manage_Event/`, `app/Http/Controllers/Web/Event/`
- Create: `app/Http/Requests/Event/`, `app/Http/Resources/Manage_Event/`

- [x] Register middleware-protected admin routes and authenticated attendee routes.
- [x] Implement CRUD, category management, occurrence endpoints, list pagination, and public visibility filtering.
- [x] Preserve the existing response envelope and generic validation errors.

### Task 3: CMS and attendee UI

**Files:**
- Create: `resources/views/manage_event/`, `resources/views/event/`
- Create: `public/assets/js/vue3/manage_event/`, `public/assets/js/vue3/event/`

- [x] Add Manage Event list/filter/category modal and Bootstrap confirmation modals.
- [x] Add add/edit content forms, thumbnail preview, nested occurrence editor, participant panel, and attendance controls.
- [x] Add authenticated Event cards/detail/session browsing with register/cancel hooks for Booking MVP.
- [x] Run Blade cache and JavaScript syntax checks.
