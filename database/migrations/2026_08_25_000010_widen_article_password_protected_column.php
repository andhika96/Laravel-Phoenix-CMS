<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('articles') || ! Schema::hasColumn('articles', 'password_protected')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table): void {
            $table->string('password_protected', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Intentionally retained at 255: shrinking a hashed password column would destroy data.
    }
};
