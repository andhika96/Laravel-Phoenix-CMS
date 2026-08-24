<?php

namespace App\Jobs\Event;

use App\Mail\EventRegistrationMail;
use App\Models\Awesome_Admin\Account;
use App\Models\Notification\LPNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class EventRegistrationNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $accountId,
        public readonly string $type,
        public readonly string $headline,
        public readonly string $messageBody,
    ) {
    }

    public function handle(): void
    {
        $account = Account::query()->find($this->accountId);
        if (! $account) {
            return;
        }

        if (Schema::hasTable('notifications')) {
            LPNotification::query()->create([
                'user_id' => 0,
                'from_id' => 0,
                'from_fullname' => 'PhoenixCMS',
                'to_id' => $account->id,
                'to_fullname' => $account->fullname ?: $account->username,
                'type' => $this->type,
                'icon' => '<i class="fad fa-calendar-star fa-fw fa-lg"></i>',
                'title' => $this->headline,
                'message' => $this->messageBody,
                'hasread' => 0,
            ]);
        }

        if ($account->email) {
            Mail::to($account->email)->send(new EventRegistrationMail($this->headline, $this->messageBody));
        }
    }
}
