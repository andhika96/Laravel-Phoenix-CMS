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
			$prismTheme = DB::table('themes')->where('theme_code', 'arunika_prism')->first();

			if ( ! $prismTheme)
			{
				throw new RuntimeException('Arunika Prism must exist before removing Arunika Mosaic.');
			}

			$mosaicTheme = DB::table('themes')->where('theme_code', 'arunika_mosaic')->first();

			if (Schema::hasTable('theme_settings'))
			{
				if ($mosaicTheme)
				{
					DB::table('theme_settings')
						->where(function ($query) use ($mosaicTheme): void
						{
							$query->where('theme_id', $mosaicTheme->id)
								->orWhere('theme_code', 'arunika_mosaic');
						})
						->update([
							'theme_id' => $prismTheme->id,
							'theme_code' => $prismTheme->theme_code,
							'theme_name' => $prismTheme->theme_name,
						]);
				}

				DB::table('theme_settings')->updateOrInsert(
					['id' => 1],
					[
						'theme_id' => $prismTheme->id,
						'theme_code' => $prismTheme->theme_code,
						'theme_name' => $prismTheme->theme_name,
					]
				);
			}

			if ($mosaicTheme)
			{
				DB::table('themes')->where('id', $mosaicTheme->id)->delete();
			}
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
			$mosaicTheme = DB::table('themes')->where('theme_code', 'arunika_mosaic')->first();

			if ( ! $mosaicTheme)
			{
				$restoreId = DB::table('themes')->where('id', 5)->exists()
					? ((int) DB::table('themes')->max('id')) + 1
					: 5;

				DB::table('themes')->insert([
					'id' => $restoreId,
					'theme_code' => 'arunika_mosaic',
					'theme_name' => 'Arunika Mosaic',
					'theme_foldername' => 'arunika_mosaic',
					'theme_cms' => 'cms_layout',
					'theme_auth' => 'auth_layout',
					'theme_frontend' => 'frontend_layout',
					'theme_version' => '1.0.0',
					'updated_at' => now(),
					'created_at' => now(),
				]);

				$mosaicTheme = DB::table('themes')->where('id', $restoreId)->first();
			}

			if (Schema::hasTable('theme_settings'))
			{
				DB::table('theme_settings')->updateOrInsert(
					['id' => 1],
					[
						'theme_id' => $mosaicTheme->id,
						'theme_code' => $mosaicTheme->theme_code,
						'theme_name' => $mosaicTheme->theme_name,
					]
				);
			}
		});
	}
};
