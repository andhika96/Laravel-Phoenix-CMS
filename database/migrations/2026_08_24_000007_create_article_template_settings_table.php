<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('article_template_settings')) {
            return;
        }

        Schema::create('article_template_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('archive_template', 64)->default('minimal-reading-list');
            $table->string('detail_template', 64)->default('focused-reader');
            $table->unsignedTinyInteger('archive_per_page')->default(12);
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_template_settings');
    }
};
