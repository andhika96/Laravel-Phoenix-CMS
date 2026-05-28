<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('theme_settings')) {
            echo "  Table theme_settings already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table theme_settings...\n";

        Schema::create('theme_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('theme_id');
            $table->string('theme_code', 155);
            $table->string('theme_name', 255);
        });

        // Seeding data
        echo "  Importing data to table theme_settings...\n";
        require_once database_path('seeders_new/ThemeSettingsSeeder.php');
        (new ThemeSettingsSeeder())->run();
        echo "  Table theme_settings imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
