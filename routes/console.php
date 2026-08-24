<?php

use App\Services\FileManagerV2\FileManagerV2Storage;
use App\Services\Event\EventReminderService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function() 
{
	$this->comment(Inspiring::quote());

})->purpose('Display an inspiring quote')->hourly();

Schedule::command('suspend:clear')->hourly();
Artisan::command('events:send-reminders', function (EventReminderService $service): void {
    $sent = $service->sendDueReminders();
    $this->info("Dispatched {$sent} event reminder(s).");
})->purpose('Send due event registration reminders');
Schedule::command('events:send-reminders')->everyMinute();

Artisan::command('filemanager-v2:prune-uploads', function (FileManagerV2Storage $storage): void {
    $deleted = $storage->pruneExpiredUploads();

    $this->info("Removed {$deleted} expired File Manager V2 upload session(s).");
})->purpose('Remove expired File Manager V2 chunk upload sessions')->hourly();
