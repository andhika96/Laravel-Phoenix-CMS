<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('page_builder', function (Blueprint $table): void {
            $table->string('editor_version', 10)->default('2.0')->index();
        });
    }

    public function down(): void
    {
        Schema::table('page_builder', function (Blueprint $table): void {
            $table->dropIndex(['editor_version']);
            $table->dropColumn('editor_version');
        });
    }
};
