<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_booking_settings')) {
            return;
        }

        Schema::create('event_booking_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('default_reminder_lead_minutes')->default(1440);
            $table->unsignedInteger('default_cancel_cutoff_minutes')->default(1440);
            $table->timestamps();
        });

        DB::table('event_booking_settings')->insert([
            'id' => 1,
            'default_reminder_lead_minutes' => 1440,
            'default_cancel_cutoff_minutes' => 1440,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('event_booking_settings');
    }
};
