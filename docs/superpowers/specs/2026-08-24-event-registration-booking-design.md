# Event Registration Booking MVP Design

## Goal

Memungkinkan user login mendaftar pada occurrence Event dengan kapasitas wajib, auto-confirm, FIFO waitlist, cancel, re-register, attendance, notifications, dan reminder.

## State and invariants

- Registration is unique per `occurrence_id` + `account_id`; re-register reuses a cancelled row.
- `confirmed` is assigned while confirmed count is below capacity; otherwise `waitlisted` receives the next position.
- Cancellation locks the occurrence, changes the registration to `cancelled`, and promotes FIFO waitlist atomically.
- Occurrence cancellation changes all active registrations to `cancelled` and sends notifications.
- User cancellation follows event override or global 24-hour cutoff. Attendance changes only confirmed registrations.
- Occurrence schedule/capacity updates are server validated; capacity cannot fall below confirmed count.

## Notifications

- `EventRegistrationNotificationJob` is dispatched after commit.
- The job writes targeted rows to the existing `notifications` table and sends the email mailable.
- Participant data is never broadcast through the public `cms-notifications` channel.
- Reminder service claims `reminder_sent_at` under row lock, uses global 24-hour default or event override, and is scheduled by `events:send-reminders` every minute.

## Verification

- Feature tests cover schema, routes, HTTP create/list/register/cancel, capacity/waitlist/re-register/cutoff, occurrence cancellation, notification delivery, reminder idempotency, and menu/permission migration.
- Browser verification requires an authenticated session; unauthenticated navigation is intentionally not treated as a product failure.
