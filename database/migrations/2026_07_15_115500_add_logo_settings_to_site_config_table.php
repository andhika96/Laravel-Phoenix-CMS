<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		if (Schema::hasTable('site_config') && ! Schema::hasColumn('site_config', 'site_logo'))
		{
			Schema::table('site_config', function (Blueprint $table)
			{
				$table->string('site_logo', 255)->nullable()->after('site_thumbnail');
				$table->decimal('site_logo_width_value', 8, 2)->default(100)->after('site_logo');
				$table->string('site_logo_width_unit', 4)->default('%')->after('site_logo_width_value');
			});
		}
	}

	public function down(): void
	{
		if (Schema::hasTable('site_config') && Schema::hasColumn('site_config', 'site_logo'))
		{
			Schema::table('site_config', function (Blueprint $table)
			{
				$table->dropColumn(['site_logo', 'site_logo_width_value', 'site_logo_width_unit']);
			});
		}
	}
};
