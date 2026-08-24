<?php

namespace Tests\Feature\Event;

use App\Jobs\Event\EventRegistrationNotificationJob;
use App\Models\Awesome_Admin\Account;
use App\Models\Event\Event;
use App\Models\Event\EventCategory;
use App\Models\Event\EventOccurrence;
use App\Models\Event\EventRegistration;
use App\Services\Event\EventReminderService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EventReminderServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        Bus::fake();

        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('fullname')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('status')->default(2);
            $table->timestamps();
        });

        foreach ([
            '2026_08_24_000001_create_event_categories_table.php',
            '2026_08_24_000002_create_events_table.php',
            '2026_08_24_000003_create_event_occurrences_table.php',
            '2026_08_24_000004_create_event_booking_settings_table.php',
            '2026_08_24_000005_create_event_registrations_table.php',
        ] as $migrationFile) {
            $migration = require database_path('migrations/'.$migrationFile);
            $migration->up();
        }
    }

    public function test_due_reminder_is_claimed_once_and_dispatches_a_notification_job(): void
    {
        $category = EventCategory::create(['name' => 'Reminder', 'code' => 'reminder', 'status' => 'active']);
        $event = Event::create([
            'uri' => 'reminder-event',
            'category_id' => $category->id,
            'title' => 'Reminder Event',
            'content' => '<p>Reminder</p>',
            'publication_status' => 'published',
            'visibility' => 'public',
        ]);
        $occurrence = EventOccurrence::create([
            'event_id' => $event->id,
            'starts_at' => now()->addMinutes(30),
            'ends_at' => now()->addMinutes(90),
            'timezone' => 'Asia/Jakarta',
            'location_mode' => 'offline',
            'capacity' => 5,
            'lifecycle_status' => 'scheduled',
        ]);
        $account = Account::create(['email' => 'reminder@example.test', 'username' => 'reminder', 'fullname' => 'Reminder User']);
        $registration = EventRegistration::create([
            'occurrence_id' => $occurrence->id,
            'account_id' => $account->id,
            'status' => 'confirmed',
            'registered_at' => now()->subHour(),
            'confirmed_at' => now()->subHour(),
        ]);

        $service = app(EventReminderService::class);

        $this->assertSame(1, $service->sendDueReminders());
        $this->assertSame(0, $service->sendDueReminders());
        $this->assertNotNull($registration->fresh()->reminder_sent_at);
        Bus::assertDispatchedTimes(EventRegistrationNotificationJob::class, 1);
        Bus::assertDispatched(EventRegistrationNotificationJob::class, function (EventRegistrationNotificationJob $job) use ($account): bool {
            return $job->accountId === $account->id && $job->type === 'event_registration_reminder';
        });
    }
}
