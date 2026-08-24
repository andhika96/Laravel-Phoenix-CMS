<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            return;
        }

        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('uri', 255)->unique();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->string('title', 255);
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('tags', 255)->nullable();
            $table->string('thumb_s', 255)->nullable();
            $table->string('thumb_l', 255)->nullable();
            $table->string('publication_status', 16)->default('draft')->index();
            $table->string('visibility', 16)->default('public')->index();
            $table->unsignedInteger('reminder_lead_minutes')->nullable();
            $table->unsignedInteger('cancel_cutoff_minutes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
