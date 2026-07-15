<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('site_config', function (Blueprint $table)
		{
			$table->decimal('font_size', 8, 3)->default(14)->change();
		});

		if ( ! Schema::hasColumn('site_config', 'font_size_unit'))
		{
			Schema::table('site_config', function (Blueprint $table)
			{
				$table->string('font_size_unit', 4)->default('px')->after('font_size');
			});
		}

		DB::table('site_config')
			->whereNotIn('font_size_unit', ['px', 'em', 'rem'])
			->update(['font_size_unit' => 'px']);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		if (Schema::hasColumn('site_config', 'font_size_unit'))
		{
			Schema::table('site_config', function (Blueprint $table)
			{
				$table->dropColumn('font_size_unit');
			});
		}

		Schema::table('site_config', function (Blueprint $table)
		{
			$table->integer('font_size')->default(14)->change();
		});
	}
};
