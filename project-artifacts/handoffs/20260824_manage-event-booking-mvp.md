# Handoff — Manage Event + Event Registration Booking MVP

## Current state

- Implementation branch: `codex/manage-event-booking`.
- Local Event migrations are applied and settings default to 1,440 minutes for reminder/cancel cutoff.
- Active navigation JSON and legacy menu links now include normalized Manage Event wiring; Layout is hidden.
- Article and Meeting Room/Asset Booking code were not modified.
- Manage Event blank page Vue error was fixed by removing nested Blade interpolation from the category submit conditional; `ManageEventTemplateTest` protects the boundary.

## Important files

- `app/Services/Event/EventRegistrationService.php`
- `app/Services/Event/EventReminderService.php`
- `app/Http/Controllers/Web/Manage_Event/Manage_Event_Controller.php`
- `app/Http/Controllers/Web/Event/Event_Controller.php`
- `resources/views/manage_event/` and `resources/views/event/`
- `tests/Feature/Event/`

## Backup

Verified backups are under `project-artifacts/backups/20260824_210000_manage_event/` for existing route, console, breadcrumb, menu, and permission seed files.

## Next step

Authenticate the in-app Browser and perform read-only DOM/layout/console QA for `/manage_event`, `/manage_event/add`, `/manage_event/edit/{id}`, `/event`, and `/event/{uri}`. Do not Save/Delete/Register during the first smoke pass.
