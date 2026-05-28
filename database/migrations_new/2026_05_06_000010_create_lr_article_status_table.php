<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('article_status')) {
            echo "  Table article_status already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table article_status...\n";

        Schema::create('article_status', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name', 32);
            $table->string('code_name', 32);
            $table->string('class_name', 32)->nullable();
            $table->tinyInteger('is_active')->default(0);
        });

        // Seeding data
        echo "  Importing data to table article_status...\n";
        require_once database_path('seeders_new/ArticleStatusSeeder.php');
        (new ArticleStatusSeeder())->run();
        echo "  Table article_status imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('article_status');
    }
};
