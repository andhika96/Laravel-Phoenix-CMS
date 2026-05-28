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
		if ( ! Schema::hasTable('page_builder_rewrite'))
		{
			Schema::create('page_builder_rewrite', function (Blueprint $table)
			{
				$table->id();
				$table->unsignedBigInteger('user_id')->nullable();
				$table->string('uri', 255)->unique();
				$table->string('page_name', 255)->unique();
				$table->longText('custom_css')->nullable();
				$table->json('vars')->nullable();
				$table->string('status', 50)->default('draft');
				$table->timestamps();

				$table->index('user_id');
				$table->index('status');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('page_builder_rewrite');
	}
};

