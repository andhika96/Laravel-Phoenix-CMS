<?php

namespace Tests\Feature\Event;

use App\Jobs\Event\EventRegistrationNotificationJob;
use App\Mail\EventRegistrationMail;
use App\Models\Awesome_Admin\Account;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EventRegistrationNotificationJobTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        Mail::fake();

        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('fullname')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('status')->default(2);
            $table->timestamps();
        });
        Schema::create('notifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0);
            $table->unsignedBigInteger('from_id')->default(0);
            $table->string('from_fullname');
            $table->unsignedBigInteger('to_id')->default(0);
            $table->string('to_fullname');
            $table->string('type');
            $table->string('icon');
            $table->string('title');
            $table->text('message');
            $table->tinyInteger('hasread')->default(0);
            $table->timestamps();
        });
    }

    public function test_job_persists_targeted_in_app_notification_and_sends_email(): void
    {
        $account = Account::create(['email' => 'notify@example.test', 'username' => 'notify', 'fullname' => 'Notify User']);

        (new EventRegistrationNotificationJob(
            $account->id,
            'event_registration_confirmed',
            'Event registration confirmed',
            'Your registration is confirmed.',
        ))->handle();

        $this->assertDatabaseHas('notifications', [
            'to_id' => $account->id,
            'type' => 'event_registration_confirmed',
            'title' => 'Event registration confirmed',
        ], 'sqlite');
        Mail::assertSent(EventRegistrationMail::class, function (EventRegistrationMail $mail) use ($account): bool {
            return $mail->hasTo($account->email);
        });
    }
}
