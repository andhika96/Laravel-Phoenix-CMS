<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('migrations')) {
            echo "  Table migrations already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table migrations...\n";

        Schema::create('migrations', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('migration', 255);
            $table->integer('batch');
        });

        // Seeding data
        echo "  Importing data to table migrations...\n";
        require_once database_path('seeders_new/MigrationsSeeder.php');
        (new MigrationsSeeder())->run();
        echo "  Table migrations imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('migrations');
    }
};
