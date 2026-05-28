<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('page_themes')) {
            echo "  Table page_themes already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table page_themes...\n";

        Schema::create('page_themes', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('uri', 155)->nullable();
            $table->string('theme_group', 155)->nullable();
            $table->string('theme_code', 55)->nullable();
            $table->string('theme_name', 155)->nullable();
            $table->text('theme_preview_image');
            $table->tinyInteger('is_active_color_nuances')->default(1);
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table page_themes...\n";
        require_once database_path('seeders_new/PageThemesSeeder.php');
        (new PageThemesSeeder())->run();
        echo "  Table page_themes imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('page_themes');
    }
};
