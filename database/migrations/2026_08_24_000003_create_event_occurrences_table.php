<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_occurrences')) {
            return;
        }

        Schema::create('event_occurrences', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('event_id')->index();
            $table->string('label', 150)->nullable();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at');
            $table->string('timezone', 64)->default('Asia/Jakarta');
            $table->string('location_mode', 16)->default('offline');
            $table->text('location_text')->nullable();
            $table->text('address')->nullable();
            $table->string('online_url', 2048)->nullable();
            $table->dateTime('registration_open_at')->nullable();
            $table->dateTime('registration_close_at')->nullable();
            $table->unsignedInteger('capacity');
            $table->string('lifecycle_status', 16)->default('scheduled')->index();
            $table->timestamps();

            $table->index(['event_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_occurrences');
    }
};
