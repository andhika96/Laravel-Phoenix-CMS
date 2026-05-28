<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('page_theme_settings')) {
            echo "  Table page_theme_settings already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table page_theme_settings...\n";

        Schema::create('page_theme_settings', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('uri', 155)->nullable();
            $table->string('page_name', 155)->nullable();
            $table->string('page_theme', 55)->nullable();
            $table->string('page_color_nuances', 12)->nullable();
            $table->text('page_background_image');
            $table->tinyInteger('is_active_color_nuances')->default(1);
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table page_theme_settings...\n";
        require_once database_path('seeders_new/PageThemeSettingsSeeder.php');
        (new PageThemeSettingsSeeder())->run();
        echo "  Table page_theme_settings imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('page_theme_settings');
    }
};
