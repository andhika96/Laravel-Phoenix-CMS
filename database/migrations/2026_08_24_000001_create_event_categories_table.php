<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_categories')) {
            return;
        }

        Schema::create('event_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 64);
            $table->string('code', 64)->unique();
            $table->string('status', 16)->default('active')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_categories');
    }
};
