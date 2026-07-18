<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		$this->renameTheme([
			'from_code' => 'arunika_canvas',
			'from_name' => 'Arunika Canvas',
			'to_code' => 'arunika_prism',
			'to_name' => 'Arunika Prism',
		]);
	}

	public function down(): void
	{
		$this->renameTheme([
			'from_code' => 'arunika_prism',
			'from_name' => 'Arunika Prism',
			'to_code' => 'arunika_canvas',
			'to_name' => 'Arunika Canvas',
		]);
	}

	private function renameTheme(array $mapping): void
	{
		if ( ! Schema::hasTable('themes'))
		{
			return;
		}

		DB::transaction(function () use ($mapping): void
		{
			$sourceRows = DB::table('themes')->where('theme_code', $mapping['from_code'])->get();
			$targetRows = DB::table('themes')->where('theme_code', $mapping['to_code'])->get();

			if ($sourceRows->count() > 1 || $targetRows->count() > 1)
			{
				throw new RuntimeException("Duplicate theme code found while renaming {$mapping['from_code']} to {$mapping['to_code']}.");
			}

			$sourceTheme = $sourceRows->first();
			$targetTheme = $targetRows->first();

			if ($sourceTheme && $targetTheme && (int) $sourceTheme->id !== (int) $targetTheme->id)
			{
				throw new RuntimeException("Theme code {$mapping['to_code']} already belongs to another theme row.");
			}

			$theme = $sourceTheme ?? $targetTheme;

			if ( ! $theme)
			{
				return;
			}

			DB::table('themes')->where('id', $theme->id)->update([
				'theme_code' => $mapping['to_code'],
				'theme_name' => $mapping['to_name'],
				'theme_foldername' => $mapping['to_code'],
				'updated_at' => now(),
			]);

			if (Schema::hasTable('theme_settings'))
			{
				DB::table('theme_settings')
					->where(function ($query) use ($mapping, $theme): void
					{
						$query->where('theme_id', $theme->id)
							->orWhere('theme_code', $mapping['from_code']);
					})
					->update([
						'theme_id' => $theme->id,
						'theme_code' => $mapping['to_code'],
						'theme_name' => $mapping['to_name'],
					]);
			}
		});
	}
};
