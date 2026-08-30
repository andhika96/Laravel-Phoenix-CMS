<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('page_builder')) return;

        Schema::table('page_builder', function (Blueprint $table): void {
            if (! Schema::hasColumn('page_builder', 'custom_js')) {
                $table->text('custom_js')->nullable();
            }
            if (! Schema::hasColumn('page_builder', 'custom_js_mode')) {
                $table->string('custom_js_mode', 24)->default('disabled');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('page_builder')) return;

        Schema::table('page_builder', function (Blueprint $table): void {
            if (Schema::hasColumn('page_builder', 'custom_js_mode')) {
                $table->dropColumn('custom_js_mode');
            }
            if (Schema::hasColumn('page_builder', 'custom_js')) {
                $table->dropColumn('custom_js');
            }
        });
    }
};
