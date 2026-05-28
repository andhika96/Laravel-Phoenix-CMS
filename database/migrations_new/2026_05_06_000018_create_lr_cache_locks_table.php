<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cache_locks')) {
            echo "  Table cache_locks already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table cache_locks...\n";

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key', 255);
            $table->string('owner', 255);
            $table->integer('expiration');
        });

        echo "  Table cache_locks imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
    }
};
