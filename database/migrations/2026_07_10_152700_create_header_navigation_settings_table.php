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
		if ( ! Schema::hasTable('header_navigation_settings'))
		{
			Schema::create('header_navigation_settings', function (Blueprint $table)
			{
				$table->id();
				$table->string('menu_page', 100)->default('awesome_admin');
				$table->boolean('is_active')->default(true);
				$table->json('config_json')->nullable();
				$table->timestamps();

				$table->unique('menu_page', 'header_navigation_menu_page_unique');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('header_navigation_settings');
	}
};
