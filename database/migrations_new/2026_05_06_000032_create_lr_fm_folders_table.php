<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fm_folders')) {
            echo "  Table fm_folders already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table fm_folders...\n";

        Schema::create('fm_folders', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('path', 255);
            $table->enum('disk', ['local','public','s3'])->default('public');
            $table->tinyInteger('is_public')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        // Seeding data
        echo "  Importing data to table fm_folders...\n";
        require_once database_path('seeders_new/FmFoldersSeeder.php');
        (new FmFoldersSeeder())->run();
        echo "  Table fm_folders imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_folders');
    }
};
