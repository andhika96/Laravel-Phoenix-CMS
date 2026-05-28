<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('testings')) {
            echo "  Table testings already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table testings...\n";

        Schema::create('testings', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        echo "  Table testings imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('testings');
    }
};
