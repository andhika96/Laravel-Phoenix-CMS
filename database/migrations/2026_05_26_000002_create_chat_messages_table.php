<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('chat_messages', function (Blueprint $table)
		{
			$table->id();
			$table->unsignedBigInteger('conversation_id')->index();
			$table->unsignedBigInteger('sender_id')->index();
			$table->text('body');
			$table->enum('type', ['text', 'image', 'file'])->default('text');
			$table->timestamp('read_at')->nullable()->comment('NULL = belum dibaca oleh receiver');
			$table->timestamps();

			// Foreign key ke chat_conversations aman karena tabel ini dibuat
			// oleh Laravel migration sendiri (tipe id-nya BIGINT UNSIGNED)
			$table->foreign('conversation_id')
				->references('id')
				->on('chat_conversations')
				->onDelete('cascade');

			$table->index(['conversation_id', 'created_at']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('chat_messages');
		Schema::dropIfExists('chat_conversations');
	}
};
