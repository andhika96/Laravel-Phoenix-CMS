# Manage Event + Event Registration Booking MVP QA

Tanggal: 2026-08-24
Branch: `codex/manage-event-booking`

## Implementasi

- Event content terisolasi: category, event, occurrence, booking settings, registration.
- Admin routes/UI: Manage Event list/filter/category modal, add/edit, thumbnail path `events`, nested occurrence, participant panel, attendance/no-show, occurrence cancellation.
- Attendee routes/UI: authenticated Event list/detail/session, register/cancel.
- Booking service: capacity lock, confirmed/waitlist FIFO, cancel cutoff, re-register, occurrence cancel, attendance.
- Notifications: queued targeted in-app/email job; no participant payload on public Reverb channel.
- Reminder: global/event override, idempotent `reminder_sent_at`, `events:send-reminders` scheduled every minute.

## Evidence

- `php artisan test tests\\Feature\\Event --no-ansi`: 14 passed, 64 assertions, including the template regression after the browser Vue compile failure.
- `php artisan test --no-ansi`: 687 passed, 1 failed, 19,211 assertions. Existing failure: `PageBuilderElementorV23ShellTest` expects 200 but receives 302; unrelated authentication baseline.
- `node --check` for all three Event JS assets: passed.
- `node --test tests\\manage-article-responsive-width.test.mjs`: 4 passed.
- `php artisan view:cache --no-ansi`: passed.
- PHP lint for new Event controllers, requests, resources, models, services, job, mail, migrations, and tests: passed.
- `git diff --check`: passed.
- Runtime migrations 000001–000006: Ran in local MySQL database, including recovery from the initial long-index failure.
- `php artisan events:send-reminders --no-ansi`: `Dispatched 0 event reminder(s).`
- Focused router inspection confirmed `cms.core.manage_event`, `cms.core.event`, and `cms.core.event.occurrence.register`.
- Graphify final incremental `--code-only` and `cluster-only --no-viz`: completed; graph contains 20,132 nodes, 34,766 edges, 1,478 communities.

## Template bug fix

- Root cause: nested Blade interpolation inside Vue conditional label in `manage_event.blade.php` produced malformed Vue expression after compilation.
- Fix: category submit label now uses two Blade-rendered `<span v-if>`/`<span v-else>` branches.

## Browser boundary

In-app Browser navigation reached the Phoenix login page. No credentials were entered. Authenticated browser QA for admin/editor/attendee DOM, responsive layout, and console/network behavior remains pending until a user-authenticated browser session is available.

## Known unrelated issue

Global `php artisan route:list`/one existing PHPUnit shell test still encounter the historical `App\\Http\\Controllers\\Web\\Articles\\Article_Controller` namespace/auth boundary. It was not changed in this scope.
