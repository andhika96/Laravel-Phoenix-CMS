<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // null = shared/global
            $table->string('name');
            $table->string('slug')->index();
            $table->string('path'); // full path e.g. media/images/2025
            $table->enum('disk', ['local', 'public', 's3'])->default('public');
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('fm_folders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_folders');
    }
};
