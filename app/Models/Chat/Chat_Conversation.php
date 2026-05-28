<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Awesome_Admin\Account;

class Chat_Conversation extends Model
{
	protected $table = 'chat_conversations';

	protected $fillable = [
		'user_one_id',
		'user_two_id',
		'last_message_id',
		'last_activity_at',
	];

	protected $casts = [
		'last_activity_at' => 'datetime',
	];

	// ── Relationships ──────────────────────────────────────

	public function userOne(): BelongsTo
	{
		return $this->belongsTo(Account::class, 'user_one_id');
	}

	public function userTwo(): BelongsTo
	{
		return $this->belongsTo(Account::class, 'user_two_id');
	}

	public function messages(): HasMany
	{
		return $this->hasMany(Chat_Message::class, 'conversation_id')->orderBy('created_at', 'asc');
	}

	public function lastMessage(): BelongsTo
	{
		return $this->belongsTo(Chat_Message::class, 'last_message_id');
	}

	// ── Helpers ────────────────────────────────────────────

	/**
	 * Dapatkan user "lawan bicara" dari sudut pandang $userId
	 */
	public function getOtherUser(int $userId): Account
	{
		return $this->user_one_id === $userId
			? $this->userTwo
			: $this->userOne;
	}

	/**
	 * Cari atau buat conversation antara dua user.
	 * Selalu simpan ID yang lebih kecil di user_one_id.
	 */
	public static function findOrCreateBetween(int $userA, int $userB): self
	{
		[$one, $two] = $userA < $userB ? [$userA, $userB] : [$userB, $userA];

		return self::firstOrCreate(
			['user_one_id' => $one, 'user_two_id' => $two]
		);
	}

	/**
	 * Jumlah pesan belum dibaca untuk user tertentu
	 */
	public function unreadCountFor(int $userId): int
	{
		return $this->messages()
			->where('sender_id', '!=', $userId)
			->whereNull('read_at')
			->count();
	}
}
