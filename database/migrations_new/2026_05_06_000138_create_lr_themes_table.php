<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('themes')) {
            echo "  Table themes already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table themes...\n";

        Schema::create('themes', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('theme_code', 32)->nullable();
            $table->string('theme_name', 255)->nullable();
            $table->string('theme_foldername', 155)->nullable();
            $table->string('theme_cms', 155)->nullable();
            $table->string('theme_auth', 155)->nullable();
            $table->string('theme_frontend', 155)->nullable();
            $table->string('theme_version', 12)->nullable();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table themes...\n";
        require_once database_path('seeders_new/ThemesSeeder.php');
        (new ThemesSeeder())->run();
        echo "  Table themes imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
