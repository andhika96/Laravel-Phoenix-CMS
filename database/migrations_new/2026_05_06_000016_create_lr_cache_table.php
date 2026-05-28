<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cache')) {
            echo "  Table cache already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table cache...\n";

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key', 255);
            $table->mediumText('value');
            $table->integer('expiration');
        });

        echo "  Table cache imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('cache');
    }
};
