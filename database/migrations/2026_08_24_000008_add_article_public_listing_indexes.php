<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('articles')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table): void {
            $table->index(['status', 'visibility', 'created_at'], 'articles_public_listing_idx');
            $table->index(['category_id', 'status', 'visibility', 'created_at'], 'articles_public_category_listing_idx');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('articles')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table): void {
            $table->dropIndex('articles_public_listing_idx');
            $table->dropIndex('articles_public_category_listing_idx');
        });
    }
};
