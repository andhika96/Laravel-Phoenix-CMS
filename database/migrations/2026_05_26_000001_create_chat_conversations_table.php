<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('chat_conversations', function (Blueprint $table)
		{
			$table->id();
			// Tidak pakai foreign key constraint ke lr_accounts karena tabel accounts
			// tidak dibuat via Laravel migration (tipe id-nya mungkin berbeda).
			// Konsisten dengan pattern file_manager migrations di project ini.
			$table->unsignedBigInteger('user_one_id')->index()->comment('User dengan ID lebih kecil');
			$table->unsignedBigInteger('user_two_id')->index()->comment('User dengan ID lebih besar');
			$table->unsignedBigInteger('last_message_id')->nullable();
			$table->timestamp('last_activity_at')->nullable();
			$table->timestamps();

			// Pastikan setiap pasang user hanya punya 1 conversation
			$table->unique(['user_one_id', 'user_two_id']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('chat_conversations');
	}
};
