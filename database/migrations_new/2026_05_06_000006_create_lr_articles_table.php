<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('articles')) {
            echo "  Table articles already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table articles...\n";

        Schema::create('articles', function (Blueprint $table) {
            $table->integer('id');
            $table->string('uri', 255);
            $table->bigInteger('user_id')->default(0);
            $table->bigInteger('category_id')->default(0);
            $table->bigInteger('subcategory_id')->default(0);
            $table->string('title', 255);
            $table->text('content');
            $table->string('tags', 255)->nullable();
            $table->string('thumb_s', 255)->nullable();
            $table->string('thumb_l', 255)->nullable();
            $table->enum('visibility', ['public','private','password_protected'])->default('public');
            $table->string('password_protected', 32)->nullable();
            $table->enum('status', ['publish','draft','pending','scheduled'])->default('draft');
            $table->enum('scheduled', ['true','false'])->default('false');
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table articles...\n";
        require_once database_path('seeders_new/ArticlesSeeder.php');
        (new ArticlesSeeder())->run();
        echo "  Table articles imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
