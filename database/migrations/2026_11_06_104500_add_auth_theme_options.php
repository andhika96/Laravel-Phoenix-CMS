<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_theme_settings', function (Blueprint $table) {
            $table->json('page_theme_options')->nullable();
        });
        foreach (['login', 'signup', 'forgotpassword', 'resetpassword'] as $page) {
            foreach (\App\Support\AuthThemeCatalog::themes() as $code => $theme) {
                if (!DB::table('page_themes')->where('uri', $page)->where('theme_code', $code)->exists()) {
                    DB::table('page_themes')->insert([
                        'uri' => $page,
                        'theme_group' => DB::table('page_themes')->where('uri', $page)->value('theme_group') ?? 'auth',
                        'theme_code' => $code,
                        'theme_name' => $theme['name'],
                        'theme_preview_image' => 'assets/images/auth/themes/'.$code.'.png',
                        'is_active_color_nuances' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $codes = array_keys(\App\Support\AuthThemeCatalog::themes());
        DB::table('page_theme_settings')->whereIn('page_theme', $codes)->update(['page_theme' => 'card']);
        DB::table('page_themes')->whereIn('theme_code', $codes)->whereIn('uri', ['login', 'signup', 'forgotpassword', 'resetpassword'])->delete();
        Schema::table('page_theme_settings', fn (Blueprint $table) => $table->dropColumn('page_theme_options'));
    }
};
