<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folder_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('original_name');
            $table->string('name'); // stored filename (hashed/unique)
            $table->string('path'); // full storage path
            $table->string('url')->nullable(); // public URL or S3 URL
            $table->string('mime_type');
            $table->string('extension', 20);
            $table->enum('file_type', ['image', 'video', 'audio', 'document', 'archive', 'other'])->default('other');
            $table->unsignedBigInteger('size'); // bytes
            $table->enum('disk', ['local', 'public', 's3'])->default('public');
            $table->boolean('is_public')->default(true);
            $table->json('meta')->nullable(); // width, height, duration, pages, etc.
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('folder_id')->references('id')->on('fm_folders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_files');
    }
};
