<?php

namespace App\Services\Event;

use App\Jobs\Event\EventRegistrationNotificationJob;
use App\Models\Awesome_Admin\Account;
use App\Models\Event\EventBookingSetting;
use App\Models\Event\EventOccurrence;
use App\Models\Event\EventRegistration;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class EventRegistrationService
{
    public function register(EventOccurrence $occurrence, Account $account): EventRegistration
    {
        $notificationStatus = null;
        $registration = DB::transaction(function () use ($occurrence, $account, &$notificationStatus): EventRegistration {
            $lockedOccurrence = EventOccurrence::query()
                ->with('event')
                ->lockForUpdate()
                ->findOrFail($occurrence->id);

            $this->assertRegistrationOpen($lockedOccurrence);

            $registration = EventRegistration::query()
                ->where('occurrence_id', $lockedOccurrence->id)
                ->where('account_id', $account->id)
                ->lockForUpdate()
                ->first();

            if ($registration && in_array($registration->status, ['confirmed', 'waitlisted'], true)) {
                return $registration;
            }

            if ($registration && in_array($registration->status, ['attended', 'no_show'], true)) {
                throw new DomainException('This registration has already been completed.');
            }

            $now = now();
            $confirmedCount = EventRegistration::query()
                ->where('occurrence_id', $lockedOccurrence->id)
                ->where('status', 'confirmed')
                ->count();
            $status = $confirmedCount < (int) $lockedOccurrence->capacity ? 'confirmed' : 'waitlisted';

            $attributes = [
                'occurrence_id' => $lockedOccurrence->id,
                'account_id' => $account->id,
                'status' => $status,
                'waitlist_position' => $status === 'waitlisted' ? $this->nextWaitlistPosition($lockedOccurrence->id) : null,
                'registered_at' => $now,
                'confirmed_at' => $status === 'confirmed' ? $now : null,
                'cancelled_at' => null,
                'attended_at' => null,
                'reminder_sent_at' => null,
                'cancellation_reason' => null,
            ];
            $notificationStatus = $status;

            if ($registration) {
                $registration->fill($attributes);
                $registration->save();

                return $registration->fresh();
            }

            return EventRegistration::query()->create($attributes);
        }, 3);

        if ($notificationStatus !== null) {
            $this->dispatchRegistrationNotification($registration, $notificationStatus);
        }

        return $registration;
    }

    public function cancel(EventOccurrence $occurrence, Account $account, ?string $reason = null): EventRegistration
    {
        $promotedIds = [];
        $registration = DB::transaction(function () use ($occurrence, $account, $reason, &$promotedIds): EventRegistration {
            $lockedOccurrence = EventOccurrence::query()
                ->with('event')
                ->lockForUpdate()
                ->findOrFail($occurrence->id);
            $registration = EventRegistration::query()
                ->where('occurrence_id', $lockedOccurrence->id)
                ->where('account_id', $account->id)
                ->lockForUpdate()
                ->first();

            if (! $registration || $registration->status === 'cancelled') {
                throw new DomainException('No active registration was found.');
            }

            if (in_array($registration->status, ['attended', 'no_show'], true)) {
                throw new DomainException('This registration can no longer be cancelled.');
            }

            $this->assertCancellationAllowed($lockedOccurrence);
            $wasConfirmed = $registration->status === 'confirmed';
            $registration->fill([
                'status' => 'cancelled',
                'waitlist_position' => null,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);
            $registration->save();

            if ($wasConfirmed) {
                $promotedIds = collect($this->promoteWaitlist($lockedOccurrence->id, (int) $lockedOccurrence->capacity))
                    ->pluck('id')
                    ->all();
            }

            return $registration->fresh();
        }, 3);

        $this->dispatchRegistrationNotification($registration, 'cancelled');
        foreach ($promotedIds as $promotedId) {
            $promoted = EventRegistration::query()->find($promotedId);
            if ($promoted) {
                $this->dispatchRegistrationNotification($promoted, 'promoted');
            }
        }

        return $registration;
    }

    public function cancelOccurrence(EventOccurrence $occurrence, ?string $reason = null): int
    {
        $cancelledAccountIds = [];
        $cancelled = DB::transaction(function () use ($occurrence, $reason, &$cancelledAccountIds): int {
            $lockedOccurrence = EventOccurrence::query()->lockForUpdate()->findOrFail($occurrence->id);
            if ($lockedOccurrence->lifecycle_status === 'cancelled') {
                return 0;
            }

            $cancelledAccountIds = EventRegistration::query()
                ->where('occurrence_id', $lockedOccurrence->id)
                ->whereIn('status', ['confirmed', 'waitlisted'])
                ->pluck('account_id')
                ->all();
            $cancelled = EventRegistration::query()
                ->where('occurrence_id', $lockedOccurrence->id)
                ->whereIn('status', ['confirmed', 'waitlisted'])
                ->update([
                    'status' => 'cancelled',
                    'waitlist_position' => null,
                    'cancelled_at' => now(),
                    'cancellation_reason' => $reason,
                    'updated_at' => now(),
                ]);

            $lockedOccurrence->update(['lifecycle_status' => 'cancelled']);

            return $cancelled;
        }, 3);

        foreach ($cancelledAccountIds as $accountId) {
            $registration = EventRegistration::query()
                ->where('occurrence_id', $occurrence->id)
                ->where('account_id', $accountId)
                ->first();
            if ($registration) {
                $this->dispatchRegistrationNotification($registration, 'occurrence_cancelled');
            }
        }

        return $cancelled;
    }

    public function markAttendance(EventRegistration $registration, string $status): EventRegistration
    {
        if (! in_array($status, ['attended', 'no_show'], true)) {
            throw new DomainException('Invalid attendance status.');
        }

        return DB::transaction(function () use ($registration, $status): EventRegistration {
            $lockedRegistration = EventRegistration::query()->lockForUpdate()->findOrFail($registration->id);
            if ($lockedRegistration->status !== 'confirmed') {
                throw new DomainException('Only confirmed registrations can receive attendance.');
            }

            $lockedRegistration->update([
                'status' => $status,
                'attended_at' => now(),
            ]);

            $updated = $lockedRegistration->fresh();
            $this->dispatchRegistrationNotification($updated, $status);

            return $updated;
        }, 3);
    }

    private function assertRegistrationOpen(EventOccurrence $occurrence): void
    {
        if ($occurrence->lifecycle_status !== 'scheduled') {
            throw new DomainException('This event occurrence is not available.');
        }

        $now = now();
        if ($occurrence->registration_open_at && $now->lt($occurrence->registration_open_at)) {
            throw new DomainException('Registration has not opened yet.');
        }

        if ($occurrence->registration_close_at && $now->gt($occurrence->registration_close_at)) {
            throw new DomainException('Registration is closed.');
        }
    }

    private function assertCancellationAllowed(EventOccurrence $occurrence): void
    {
        $eventCutoff = $occurrence->event?->cancel_cutoff_minutes;
        $defaultCutoff = EventBookingSetting::query()->whereKey(1)->value('default_cancel_cutoff_minutes');
        $cutoffMinutes = $eventCutoff ?? (int) ($defaultCutoff ?? 1440);
        $cutoffAt = $occurrence->starts_at->copy()->subMinutes($cutoffMinutes);

        if (now()->gte($cutoffAt)) {
            throw new DomainException('The cancellation deadline has passed.');
        }
    }

    private function nextWaitlistPosition(int $occurrenceId): int
    {
        return ((int) EventRegistration::query()
            ->where('occurrence_id', $occurrenceId)
            ->where('status', 'waitlisted')
            ->max('waitlist_position')) + 1;
    }

    private function promoteWaitlist(int $occurrenceId, int $capacity): array
    {
        $confirmedCount = EventRegistration::query()
            ->where('occurrence_id', $occurrenceId)
            ->where('status', 'confirmed')
            ->count();
        $availableSlots = max(0, $capacity - $confirmedCount);

        if ($availableSlots === 0) {
            return [];
        }

        $waitlisted = EventRegistration::query()
            ->where('occurrence_id', $occurrenceId)
            ->where('status', 'waitlisted')
            ->orderBy('waitlist_position')
            ->orderBy('id')
            ->lockForUpdate()
            ->limit($availableSlots)
            ->get();

        $promoted = [];
        foreach ($waitlisted as $registration) {
            $registration->fill([
                'status' => 'confirmed',
                'waitlist_position' => null,
                'confirmed_at' => now(),
                'reminder_sent_at' => null,
            ]);
            $registration->save();
            $promoted[] = $registration->fresh();
        }

        return $promoted;
    }

    private function dispatchRegistrationNotification(EventRegistration $registration, string $status): void
    {
        $registration->loadMissing(['account', 'occurrence.event']);
        $account = $registration->account;
        $eventTitle = $registration->occurrence?->event?->title ?: 'Event';
        $messages = [
            'confirmed' => ['event_registration_confirmed', 'Event registration confirmed', "Your registration for {$eventTitle} is confirmed."],
            'waitlisted' => ['event_registration_waitlisted', 'Event waitlist updated', "You are on the waitlist for {$eventTitle}."],
            'cancelled' => ['event_registration_cancelled', 'Event registration cancelled', "Your registration for {$eventTitle} was cancelled."],
            'promoted' => ['event_registration_promoted', 'Event registration promoted', "A place is now confirmed for {$eventTitle}."],
            'attended' => ['event_registration_attended', 'Event attendance recorded', "Your attendance for {$eventTitle} was recorded."],
            'no_show' => ['event_registration_no_show', 'Event attendance updated', "Your attendance for {$eventTitle} was recorded as no-show."],
            'occurrence_cancelled' => ['event_occurrence_cancelled', 'Event session cancelled', "A session of {$eventTitle} was cancelled."],
        ];
        [$type, $headline, $message] = $messages[$status] ?? $messages['confirmed'];

        if ($account) {
            EventRegistrationNotificationJob::dispatch((int) $account->id, $type, $headline, $message)->afterCommit();
        }

        foreach ($this->adminRecipientIds() as $adminId) {
            if ((int) $adminId === (int) $account?->id) {
                continue;
            }
            EventRegistrationNotificationJob::dispatch((int) $adminId, 'event_admin_'.$type, $headline, $message)->afterCommit();
        }
    }

    private function adminRecipientIds(): array
    {
        try {
            return Account::query()->role(['Administrator', 'Super Admin'])->pluck('id')->all();
        } catch (\Throwable) {
            return [];
        }
    }
}
