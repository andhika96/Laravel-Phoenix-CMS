<?php

namespace Tests\Feature\Event;

use App\Models\Awesome_Admin\Account;
use App\Models\Event\Event;
use App\Models\Event\EventCategory;
use App\Models\Event\EventOccurrence;
use App\Jobs\Event\EventRegistrationNotificationJob;
use App\Services\Event\EventRegistrationService;
use DomainException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EventRegistrationServiceTest extends TestCase
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
            $table->string('uuid')->nullable();
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

    public function test_full_occurrence_uses_fifo_waitlist_and_promotes_after_confirmed_cancel(): void
    {
        $category = EventCategory::create([
            'name' => 'Workshop',
            'code' => 'workshop',
            'status' => 'active',
        ]);
        $event = Event::create([
            'uri' => 'event-registration-test',
            'category_id' => $category->id,
            'title' => 'Registration Test',
            'content' => '<p>Test</p>',
            'publication_status' => 'published',
            'visibility' => 'public',
        ]);
        $occurrence = EventOccurrence::create([
            'event_id' => $event->id,
            'label' => 'Main session',
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHour(),
            'timezone' => 'Asia/Jakarta',
            'location_mode' => 'offline',
            'capacity' => 1,
            'lifecycle_status' => 'scheduled',
        ]);
        $first = Account::create(['email' => 'first@example.test', 'username' => 'first', 'fullname' => 'First User']);
        $second = Account::create(['email' => 'second@example.test', 'username' => 'second', 'fullname' => 'Second User']);

        $service = app(EventRegistrationService::class);

        $confirmed = $service->register($occurrence, $first);
        $waitlisted = $service->register($occurrence, $second);

        $this->assertSame('confirmed', $confirmed->status);
        $this->assertSame('waitlisted', $waitlisted->status);
        $this->assertSame(1, $waitlisted->waitlist_position);

        $service->cancel($occurrence, $first);

        $this->assertSame('cancelled', $confirmed->fresh()->status);
        $this->assertSame('confirmed', $waitlisted->fresh()->status);
        $this->assertNull($waitlisted->fresh()->waitlist_position);
    }

    public function test_duplicate_registration_is_idempotent_and_re_registration_reuses_cancelled_row(): void
    {
        [$occurrence, $first] = $this->makeOccurrenceAndAccount(2, 'idempotent-first@example.test');
        $service = app(EventRegistrationService::class);

        $initial = $service->register($occurrence, $first);
        $duplicate = $service->register($occurrence, $first);
        $service->cancel($occurrence, $first);
        $reRegistered = $service->register($occurrence, $first);

        $this->assertSame($initial->id, $duplicate->id);
        $this->assertSame($initial->id, $reRegistered->id);
        $this->assertSame('confirmed', $reRegistered->status);
        $this->assertSame(1, DB::table('event_registrations')->where('occurrence_id', $occurrence->id)->count());
    }

    public function test_cancel_after_the_global_cutoff_is_rejected(): void
    {
        [$occurrence, $first] = $this->makeOccurrenceAndAccount(2, 'cutoff@example.test');
        $service = app(EventRegistrationService::class);
        $service->register($occurrence, $first);
        $occurrence->update(['starts_at' => now()->addMinutes(30), 'ends_at' => now()->addMinutes(90)]);

        $this->expectException(DomainException::class);
        $service->cancel($occurrence->fresh(), $first);
    }

    public function test_cancelling_an_occurrence_cancels_active_registrations(): void
    {
        [$occurrence, $first] = $this->makeOccurrenceAndAccount(2, 'occurrence-cancel-first@example.test');
        $second = Account::create(['email' => 'occurrence-cancel-second@example.test', 'username' => 'occurrence-cancel-second', 'fullname' => 'Second User']);
        $service = app(EventRegistrationService::class);
        $service->register($occurrence, $first);
        $service->register($occurrence, $second);

        $cancelled = $service->cancelOccurrence($occurrence, 'Weather disruption');

        $this->assertSame(2, $cancelled);
        $this->assertSame('cancelled', $occurrence->fresh()->lifecycle_status);
        $this->assertSame(2, DB::table('event_registrations')->where('occurrence_id', $occurrence->id)->where('status', 'cancelled')->count());
    }

    public function test_registration_dispatches_a_targeted_notification_job(): void
    {
        [$occurrence, $first] = $this->makeOccurrenceAndAccount(1, 'notification@example.test');

        app(EventRegistrationService::class)->register($occurrence, $first);

        Bus::assertDispatched(EventRegistrationNotificationJob::class, function (EventRegistrationNotificationJob $job) use ($first): bool {
            return $job->accountId === $first->id && $job->type === 'event_registration_confirmed';
        });
    }

    private function makeOccurrenceAndAccount(int $capacity, string $email): array
    {
        $category = EventCategory::create(['name' => 'Test', 'code' => 'test-'.str_replace(['@', '.'], '-', $email), 'status' => 'active']);
        $event = Event::create([
            'uri' => 'event-'.str_replace(['@', '.'], '-', $email),
            'category_id' => $category->id,
            'title' => 'Test Event',
            'content' => '<p>Test</p>',
            'publication_status' => 'published',
            'visibility' => 'public',
        ]);
        $occurrence = EventOccurrence::create([
            'event_id' => $event->id,
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHour(),
            'timezone' => 'Asia/Jakarta',
            'location_mode' => 'offline',
            'capacity' => $capacity,
            'lifecycle_status' => 'scheduled',
        ]);
        $account = Account::create(['email' => $email, 'username' => str_replace(['@', '.'], '-', $email), 'fullname' => 'Test User']);

        return [$occurrence, $account];
    }
}
