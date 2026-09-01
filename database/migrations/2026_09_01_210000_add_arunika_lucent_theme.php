<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		if ( ! Schema::hasTable('themes'))
		{
			return;
		}

		DB::transaction(function (): void
		{
			$existingTheme = DB::table('themes')->where('theme_code', 'arunika_lucent')->first();
			$attributes = [
				'theme_name' => 'Arunika Lucent',
				'theme_foldername' => 'arunika_lucent',
				'theme_cms' => 'cms_layout',
				'theme_auth' => 'auth_layout',
				'theme_frontend' => 'frontend_layout',
				'theme_version' => '1.0.0',
				'updated_at' => now(),
			];

			if ($existingTheme)
			{
				DB::table('themes')->where('id', $existingTheme->id)->update($attributes);

				return;
			}

			DB::table('themes')->insert(array_merge($attributes, [
				'id' => ((int) DB::table('themes')->max('id')) + 1,
				'theme_code' => 'arunika_lucent',
				'created_at' => now(),
			]));
		});
	}

	public function down(): void
	{
		if ( ! Schema::hasTable('themes'))
		{
			return;
		}

		DB::transaction(function (): void
		{
			$lucentTheme = DB::table('themes')->where('theme_code', 'arunika_lucent')->first();

			if ( ! $lucentTheme)
			{
				return;
			}

			if (Schema::hasTable('theme_settings') && DB::table('theme_settings')->where('theme_id', $lucentTheme->id)->exists())
			{
				$fallbackTheme = DB::table('themes')->where('theme_code', 'arunika_equinox')->first();

				if ( ! $fallbackTheme)
				{
					throw new RuntimeException('Arunika Equinox must exist before removing an active Arunika Lucent theme.');
				}

				DB::table('theme_settings')->where('theme_id', $lucentTheme->id)->update([
					'theme_id' => $fallbackTheme->id,
					'theme_code' => $fallbackTheme->theme_code,
					'theme_name' => $fallbackTheme->theme_name,
				]);
			}

			DB::table('themes')->where('id', $lucentTheme->id)->delete();
		});
	}
};
