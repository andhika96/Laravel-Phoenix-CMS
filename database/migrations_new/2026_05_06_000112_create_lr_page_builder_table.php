<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('page_builder')) {
            echo "  Table page_builder already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table page_builder...\n";

        Schema::create('page_builder', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('user_id')->default(0);
            $table->string('uri', 255)->nullable();
            $table->string('page_name', 255)->nullable();
            $table->text('custom_css');
            $table->json('vars');
            $table->enum('status', ['publish','not_active','draft'])->default('draft');
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table page_builder...\n";
        require_once database_path('seeders_new/PageBuilderSeeder.php');
        (new PageBuilderSeeder())->run();
        echo "  Table page_builder imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('page_builder');
    }
};
