<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		if ( ! Schema::hasTable('menu_fe_parentmenu_dropdown_configs'))
		{
			Schema::create('menu_fe_parentmenu_dropdown_configs', function (Blueprint $table)
			{
				$table->id();
				$table->string('menu_page', 100)->default('awesome_admin');
				$table->string('parent_code', 100);
				$table->string('dropdown_type', 30)->default('none');
				$table->string('mega_layout', 50)->nullable();
				$table->json('config_json')->nullable();
				$table->timestamps();

				$table->unique(['menu_page', 'parent_code'], 'menu_fe_parent_dropdown_config_unique');
				$table->index('parent_code', 'menu_fe_parent_dropdown_parent_code_index');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('menu_fe_parentmenu_dropdown_configs');
	}
};
