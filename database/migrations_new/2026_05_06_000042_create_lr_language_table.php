<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('language')) {
            echo "  Table language already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table language...\n";

        Schema::create('language', function (Blueprint $table) {
            $table->integer('id');
            $table->string('lang', 12);
            $table->string('lang_from', 255);
            $table->string('lang_to', 255);
        });

        // Seeding data
        echo "  Importing data to table language...\n";
        require_once database_path('seeders_new/LanguageSeeder.php');
        (new LanguageSeeder())->run();
        echo "  Table language imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('language');
    }
};
