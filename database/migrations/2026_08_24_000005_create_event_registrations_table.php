<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_registrations')) {
            $indexNames = collect(Schema::getIndexes('event_registrations'))->pluck('name')->all();
            if (! in_array('event_registrations_occ_status_wait_idx', $indexNames, true)) {
                Schema::table('event_registrations', function (Blueprint $table): void {
                    $table->index(['occurrence_id', 'status', 'waitlist_position'], 'event_registrations_occ_status_wait_idx');
                });
            }
            return;
        }

        Schema::create('event_registrations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('occurrence_id');
            $table->unsignedBigInteger('account_id');
            $table->string('status', 16)->default('confirmed')->index();
            $table->unsignedInteger('waitlist_position')->nullable();
            $table->dateTime('registered_at')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('attended_at')->nullable();
            $table->dateTime('reminder_sent_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->unique(['occurrence_id', 'account_id'], 'event_registrations_occ_account_unique');
            $table->index(['occurrence_id', 'status', 'waitlist_position'], 'event_registrations_occ_status_wait_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
