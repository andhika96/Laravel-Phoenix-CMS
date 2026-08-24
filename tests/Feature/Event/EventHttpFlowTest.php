<?php

namespace Tests\Feature\Event;

use App\Models\Awesome_Admin\Account;
use App\Models\Event\Event;
use App\Models\Event\EventCategory;
use App\Models\Event\EventOccurrence;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventHttpFlowTest extends TestCase
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
        Schema::create('language', function (Blueprint $table): void {
            $table->id();
            $table->string('lang', 12);
            $table->string('lang_from', 255);
            $table->string('lang_to', 255);
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

    public function test_admin_can_create_event_and_authenticated_users_can_browse_published_event(): void
    {
        $admin = Account::create(['email' => 'admin-event@example.test', 'username' => 'admin-event', 'fullname' => 'Event Admin']);
        $category = EventCategory::create(['name' => 'Public', 'code' => 'public', 'status' => 'active']);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_event.store'), [
            'title' => 'Phoenix Workshop',
            'content' => '<p>Workshop content</p>',
            'category_id' => $category->id,
            'publication_status' => 'published',
            'visibility' => 'public',
        ])->assertOk()->assertJsonPath('success', true);

        $event = Event::query()->firstOrFail();
        EventOccurrence::create([
            'event_id' => $event->id,
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHour(),
            'timezone' => 'Asia/Jakarta',
            'location_mode' => 'offline',
            'capacity' => 10,
            'lifecycle_status' => 'scheduled',
        ]);

        $this->withoutMiddleware()->actingAs($admin, 'web')->getJson(route('cms.core.event.listdata'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.uri', $event->uri);
    }

    public function test_admin_can_manage_event_category_without_manual_code_input(): void
    {
        $admin = Account::create(['email' => 'category-admin@example.test', 'username' => 'category-admin', 'fullname' => 'Category Admin']);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_event.store.category'), [
            'category_name' => 'Community Events',
            'category_status' => 'active',
        ])->assertOk()->assertJsonPath('success', true);

        $category = EventCategory::query()->firstOrFail();
        $this->assertSame('community-events', $category->code);

        $response = $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_event.update.category'), [
            'idOrSlug' => $category->id,
            'category_name' => 'Community Program',
            'category_status' => 'inactive',
        ]);
        $response->assertOk()->assertJsonPath('success', true);

        $category->refresh();
        $this->assertSame('Community Program', $category->name);
        $this->assertSame('inactive', $category->status);
        $this->assertSame('community-program', $category->code);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_event.delete.category', $category->id))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('event_categories', ['id' => $category->id]);
    }

    public function test_authenticated_user_can_register_and_cancel_one_occurrence(): void
    {
        $account = Account::create(['email' => 'attendee@example.test', 'username' => 'attendee', 'fullname' => 'Event Attendee']);
        $event = Event::create([
            'uri' => 'attendee-event',
            'title' => 'Attendee Event',
            'content' => '<p>Content</p>',
            'publication_status' => 'published',
            'visibility' => 'public',
        ]);
        $occurrence = EventOccurrence::create([
            'event_id' => $event->id,
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHour(),
            'timezone' => 'Asia/Jakarta',
            'location_mode' => 'offline',
            'capacity' => 1,
            'lifecycle_status' => 'scheduled',
        ]);

        $this->withoutMiddleware()->actingAs($account, 'web')->postJson(route('cms.core.event.occurrence.register', $occurrence->id))
            ->assertCreated()
            ->assertJsonPath('data.status', 'confirmed');
        $this->withoutMiddleware()->actingAs($account, 'web')->postJson(route('cms.core.event.occurrence.cancel', $occurrence->id))
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');
    }

    public function test_private_event_occurrence_cannot_be_registered_by_attendee_endpoint(): void
    {
        $account = Account::create(['email' => 'private-attendee@example.test', 'username' => 'private-attendee', 'fullname' => 'Private Attendee']);
        $event = Event::create([
            'uri' => 'private-event',
            'title' => 'Private Event',
            'content' => '<p>Private</p>',
            'publication_status' => 'draft',
            'visibility' => 'private',
        ]);
        $occurrence = EventOccurrence::create([
            'event_id' => $event->id,
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHour(),
            'timezone' => 'Asia/Jakarta',
            'location_mode' => 'offline',
            'capacity' => 1,
            'lifecycle_status' => 'scheduled',
        ]);

        $this->withoutMiddleware()->actingAs($account, 'web')->postJson(route('cms.core.event.occurrence.register', $occurrence->id))
            ->assertNotFound();
    }

    public function test_admin_can_remove_an_existing_event_thumbnail(): void
    {
        Storage::fake('public');
        $admin = Account::create(['email' => 'thumbnail-admin@example.test', 'username' => 'thumbnail-admin', 'fullname' => 'Thumbnail Admin']);
        $largePath = 'events/test/removable.jpg';
        $smallPath = 'events/test/removable_small.jpg';
        Storage::disk('public')->put($largePath, 'large-thumbnail');
        Storage::disk('public')->put($smallPath, 'small-thumbnail');

        $event = Event::create([
            'uri' => 'removable-thumbnail-event',
            'title' => 'Removable Thumbnail Event',
            'content' => '<p>Content</p>',
            'publication_status' => 'draft',
            'visibility' => 'public',
            'thumb_l' => $largePath,
            'thumb_s' => $smallPath,
        ]);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_event.update', $event->id), [
            'title' => $event->title,
            'uri' => $event->uri,
            'summary' => $event->summary,
            'content' => $event->content,
            'publication_status' => $event->publication_status,
            'visibility' => $event->visibility,
            'remove_thumbnail' => true,
        ])->assertOk()->assertJsonPath('success', true);

        $event->refresh();
        $this->assertNull($event->thumb_l);
        $this->assertNull($event->thumb_s);
        Storage::disk('public')->assertMissing($largePath);
        Storage::disk('public')->assertMissing($smallPath);
    }

    public function test_admin_can_create_an_event_thumbnail_from_a_ckfinder_event_image(): void
    {
        Storage::fake('public');
        $admin = Account::create(['email' => 'ckfinder-thumbnail-admin@example.test', 'username' => 'ckfinder-thumbnail-admin', 'fullname' => 'CKFinder Thumbnail Admin']);
        $sourcePath = 'ckfinder/events/ckfinder-source.jpg';
        $sourceImage = UploadedFile::fake()->image('ckfinder-source.jpg', 640, 360);
        Storage::disk('public')->put($sourcePath, file_get_contents($sourceImage->getRealPath()));

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_event.store'), [
            'title' => 'CKFinder Thumbnail Event',
            'content' => '<p>Content</p>',
            'publication_status' => 'draft',
            'visibility' => 'public',
            'thumbnail_source' => 'ckfinder',
            'thumbnail_ckfinder_url' => '/storage/ckfinder/events/ckfinder-source.jpg',
        ])->assertOk()->assertJsonPath('success', true);

        $event = Event::query()->firstOrFail();
        $this->assertNotNull($event->thumb_l);
        $this->assertNotNull($event->thumb_s);
        Storage::disk('public')->assertExists($event->thumb_l);
        Storage::disk('public')->assertExists($event->thumb_s);
    }
}
