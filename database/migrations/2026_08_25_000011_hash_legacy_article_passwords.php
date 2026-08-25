<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('articles') || ! Schema::hasColumn('articles', 'password_protected')) {
            return;
        }

        DB::table('articles')
            ->select(['id', 'password_protected'])
            ->where('visibility', 'password_protected')
            ->whereNotNull('password_protected')
            ->where('password_protected', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($articles): void {
                foreach ($articles as $article) {
                    $password = (string) $article->password_protected;
                    if ((password_get_info($password)['algo'] ?? null) !== null) {
                        continue;
                    }

                    DB::table('articles')
                        ->where('id', $article->id)
                        ->update(['password_protected' => Hash::make($password)]);
                }
            });
    }

    public function down(): void
    {
        // Password hashes are intentionally irreversible.
    }
};
