<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('article_template_settings')) {
            return;
        }

        Schema::table('article_template_settings', function (Blueprint $table): void {
            if (!Schema::hasColumn('article_template_settings', 'archive_template_options')) {
                $table->json('archive_template_options')->nullable();
            }
            if (!Schema::hasColumn('article_template_settings', 'detail_template_options')) {
                $table->json('detail_template_options')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('article_template_settings')) {
            return;
        }

        Schema::table('article_template_settings', function (Blueprint $table): void {
            $columns = array_filter([
                Schema::hasColumn('article_template_settings', 'archive_template_options') ? 'archive_template_options' : null,
                Schema::hasColumn('article_template_settings', 'detail_template_options') ? 'detail_template_options' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
