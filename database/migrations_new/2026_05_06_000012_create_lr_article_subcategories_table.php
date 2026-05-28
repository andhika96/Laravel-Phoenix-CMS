<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('article_subcategories')) {
            echo "  Table article_subcategories already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table article_subcategories...\n";

        Schema::create('article_subcategories', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('category_id')->default(0);
            $table->string('name', 24);
            $table->string('code', 32);
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        echo "  Table article_subcategories imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('article_subcategories');
    }
};
