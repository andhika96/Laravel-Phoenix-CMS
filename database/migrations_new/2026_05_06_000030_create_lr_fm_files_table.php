<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fm_files')) {
            echo "  Table fm_files already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table fm_files...\n";

        Schema::create('fm_files', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('original_name', 255);
            $table->string('name', 255);
            $table->string('path', 255);
            $table->string('url', 255)->nullable();
            $table->string('mime_type', 255);
            $table->string('extension', 20);
            $table->enum('file_type', ['image','video','audio','document','archive','other'])->default('other');
            $table->unsignedBigInteger('size');
            $table->enum('disk', ['local','public','s3'])->default('public');
            $table->tinyInteger('is_public')->default(1);
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        // Seeding data
        echo "  Importing data to table fm_files...\n";
        require_once database_path('seeders_new/FmFilesSeeder.php');
        (new FmFilesSeeder())->run();
        echo "  Table fm_files imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_files');
    }
};
