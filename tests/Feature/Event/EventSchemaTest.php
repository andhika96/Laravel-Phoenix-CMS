<?php

namespace Tests\Feature\Event;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EventSchemaTest extends TestCase
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
    }

    public function test_event_migrations_create_the_content_occurrence_and_registration_contract(): void
    {
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

        foreach (['event_categories', 'events', 'event_occurrences', 'event_booking_settings', 'event_registrations'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Expected {$table} to exist.");
        }

        $this->assertTrue(Schema::hasColumns('events', [
            'uri', 'category_id', 'created_by', 'title', 'content', 'publication_status', 'visibility',
        ]));
        $this->assertTrue(Schema::hasColumns('event_occurrences', [
            'event_id', 'starts_at', 'ends_at', 'timezone', 'location_mode', 'capacity', 'lifecycle_status',
        ]));
        $this->assertTrue(Schema::hasColumns('event_registrations', [
            'occurrence_id', 'account_id', 'status', 'waitlist_position', 'reminder_sent_at',
        ]));

        $this->assertSame(1440, (int) DB::table('event_booking_settings')
            ->where('id', 1)
            ->value('default_reminder_lead_minutes'));
        $this->assertSame(1440, (int) DB::table('event_booking_settings')
            ->where('id', 1)
            ->value('default_cancel_cutoff_minutes'));
    }
}
