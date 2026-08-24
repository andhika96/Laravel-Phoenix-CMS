# Event Registration Booking MVP Implementation Plan

> **For agentic workers:** Implement the tasks in order and keep the focused Event tests green after each task.

**Goal:** Ship safe account-based Event registration with capacity, FIFO waitlist, cancellation, attendance, notifications, and reminders.

**Architecture:** `EventRegistrationService` owns transaction/locking/state transitions. Queued notification jobs own in-app/email delivery. `EventReminderService` claims due reminders idempotently and is invoked by a scheduled command.

**Tech Stack:** Laravel 13, Eloquent transactions/locks, database queue, Laravel Mail, PHPUnit, Vue/Fetch attendee actions.

**Spec:** `docs/superpowers/specs/2026-08-24-event-registration-booking-design.md`

## Global Constraints

- Login-only attendees; no guest registration.
- Registration is per occurrence and unique per account.
- Capacity is mandatory; waitlist promotion is FIFO and atomic.
- Default reminder and cancel cutoff are 24 hours with event overrides.
- No room/asset booking, payment, recurrence, or public Reverb participant payloads.

### Task 1: Registration lifecycle

**Files:**
- Create/modify: `app/Services/Event/EventRegistrationService.php`
- Test: `tests/Feature/Event/EventRegistrationServiceTest.php`, `EventHttpFlowTest.php`

- [x] Implement register, idempotent duplicate handling, re-register, cancel cutoff, FIFO promotion, occurrence cancel, and attendance.
- [x] Lock occurrence/registration rows and enforce capacity through the database state.
- [x] Verify confirmed, waitlisted, cancelled, attended, and no-show transitions.

### Task 2: Notification delivery

**Files:**
- Create: `app/Jobs/Event/EventRegistrationNotificationJob.php`
- Create: `app/Mail/EventRegistrationMail.php`, `resources/views/emails/event_registration.blade.php`
- Test: `tests/Feature/Event/EventRegistrationNotificationJobTest.php`

- [x] Dispatch targeted notification jobs after transaction commit.
- [x] Persist in-app notification rows and send email from the queued job.
- [x] Verify delivery failure remains outside the registration transaction.

### Task 3: Reminder scheduling

**Files:**
- Create: `app/Services/Event/EventReminderService.php`
- Modify: `routes/console.php`
- Test: `tests/Feature/Event/EventReminderServiceTest.php`

- [x] Claim due confirmed registrations under row lock and set `reminder_sent_at` once.
- [x] Apply event override or global 24-hour reminder default.
- [x] Register and schedule `events:send-reminders` every minute.
