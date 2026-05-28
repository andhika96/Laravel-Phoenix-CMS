<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('article_categories')) {
            echo "  Table article_categories already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table article_categories...\n";

        Schema::create('article_categories', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('name', 32)->nullable();
            $table->string('code', 32)->nullable();
            $table->enum('status', ['active','inactive','hide'])->default('active');
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table article_categories...\n";
        require_once database_path('seeders_new/ArticleCategoriesSeeder.php');
        (new ArticleCategoriesSeeder())->run();
        echo "  Table article_categories imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('article_categories');
    }
};
