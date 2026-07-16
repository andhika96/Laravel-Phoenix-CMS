<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Register Arunika V3 without assuming the next numeric theme ID.
	 */
	public function up(): void
	{
		if ( ! Schema::hasTable('themes'))
		{
			return;
		}

		$existingTheme = DB::table('themes')->where('theme_code', 'arunika_v3')->first();
		$attributes = [
			'theme_name' => 'Arunika V3 Theme',
			'theme_foldername' => 'arunika_v3',
			'theme_cms' => 'cms_layout',
			'theme_auth' => 'auth_layout',
			'theme_frontend' => 'frontend_layout',
			'theme_version' => '1.0.0',
			'updated_at' => now(),
		];

		if ($existingTheme)
		{
			DB::table('themes')->where('theme_code', 'arunika_v3')->update($attributes);

			return;
		}

		DB::table('themes')->insert(array_merge($attributes, [
			'id' => ((int) DB::table('themes')->max('id')) + 1,
			'theme_code' => 'arunika_v3',
			'created_at' => now(),
		]));
	}

	/**
	 * Fall back to Arunika V2 before removing V3 if it is currently active.
	 */
	public function down(): void
	{
		if ( ! Schema::hasTable('themes'))
		{
			return;
		}

		if (Schema::hasTable('theme_settings') && DB::table('theme_settings')->where('theme_code', 'arunika_v3')->exists())
		{
			$fallbackTheme = DB::table('themes')->where('theme_code', 'arunika_v2')->first();

			if ($fallbackTheme)
			{
				DB::table('theme_settings')->where('theme_code', 'arunika_v3')->update([
					'theme_id' => $fallbackTheme->id,
					'theme_code' => $fallbackTheme->theme_code,
					'theme_name' => $fallbackTheme->theme_name,
				]);
			}
		}

		DB::table('themes')->where('theme_code', 'arunika_v3')->delete();
	}
};
