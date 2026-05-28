<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Awesome_Admin\Account;

class Chat_Message extends Model
{
	protected $table = 'chat_messages';

	protected $fillable = [
		'conversation_id',
		'sender_id',
		'body',
		'type',
		'read_at',
	];

	protected $casts = [
		'read_at' => 'datetime',
	];

	// ── Relationships ──────────────────────────────────────

	public function conversation(): BelongsTo
	{
		return $this->belongsTo(Chat_Conversation::class, 'conversation_id');
	}

	public function sender(): BelongsTo
	{
		return $this->belongsTo(Account::class, 'sender_id');
	}

	// ── Helpers ────────────────────────────────────────────

	public function isReadBy(int $userId): bool
	{
		// Pesan milik sender sendiri dianggap sudah "dibaca"
		if ($this->sender_id === $userId) return true;
		return $this->read_at !== null;
	}
}
