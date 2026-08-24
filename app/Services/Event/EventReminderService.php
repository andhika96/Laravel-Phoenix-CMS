<?php

namespace App\Services\Event;

use App\Jobs\Event\EventRegistrationNotificationJob;
use App\Models\Event\EventBookingSetting;
use App\Models\Event\EventRegistration;
use Illuminate\Support\Facades\DB;

class EventReminderService
{
    public function sendDueReminders(): int
    {
        $now = now();
        $defaultLeadMinutes = (int) (EventBookingSetting::query()->whereKey(1)->value('default_reminder_lead_minutes') ?? 1440);
        $sent = 0;

        EventRegistration::query()
            ->with(['account', 'occurrence.event'])
            ->where('status', 'confirmed')
            ->whereNull('reminder_sent_at')
            ->whereHas('occurrence', fn ($query) => $query->where('lifecycle_status', 'scheduled')->where('starts_at', '>', $now))
            ->chunkById(100, function ($registrations) use ($now, $defaultLeadMinutes, &$sent): void {
                foreach ($registrations as $registration) {
                    $occurrence = $registration->occurrence;
                    $event = $occurrence?->event;
                    if (! $occurrence || ! $event) {
                        continue;
                    }

                    $leadMinutes = $event->reminder_lead_minutes ?? $defaultLeadMinutes;
                    $reminderAt = $occurrence->starts_at->copy()->subMinutes((int) $leadMinutes);
                    if ($now->lt($reminderAt) || $now->gte($occurrence->starts_at)) {
                        continue;
                    }

                    $claimed = DB::transaction(function () use ($registration): bool {
                        $locked = EventRegistration::query()->lockForUpdate()->find($registration->id);
                        if (! $locked || $locked->status !== 'confirmed' || $locked->reminder_sent_at !== null) {
                            return false;
                        }

                        $locked->update(['reminder_sent_at' => now()]);

                        return true;
                    }, 3);

                    if (! $claimed || ! $registration->account) {
                        continue;
                    }

                    EventRegistrationNotificationJob::dispatch(
                        (int) $registration->account->id,
                        'event_registration_reminder',
                        'Event reminder',
                        "Your event {$event->title} starts soon.",
                    )->afterCommit();
                    $sent++;
                }
            });

        return $sent;
    }
}
