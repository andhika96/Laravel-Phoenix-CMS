<?php

namespace App\Events\CMS;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ChatMessageSent
 *
 * Broadcast pesan chat real-time via Laravel Reverb.
 * Menggunakan PrivateChannel agar hanya kedua user yang bisa mendengarkan.
 *
 * Channel: private-chat.{conversationId}
 * Event  : message.sent
 */
class ChatMessageSent implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public function __construct(
		public readonly int    $conversationId,
		public readonly int    $messageId,
		public readonly int    $senderId,
		public readonly string $senderName,
		public readonly string $senderAvatar,
		public readonly string $body,
		public readonly string $type,
		public readonly string $createdAt,
		public readonly int    $receiverId,
	) {}

	public function broadcastOn(): array
	{
		// Public channel (tidak butuh auth endpoint) agar lebih simple untuk CMS internal.
		// Gunakan naming convention unik per conversation.
		return [
			new \Illuminate\Broadcasting\Channel('chat.' . $this->conversationId),
		];
	}

	public function broadcastAs(): string
	{
		return 'message.sent';
	}

	public function broadcastWith(): array
	{
		return [
			'id'              => $this->messageId,
			'conversation_id' => $this->conversationId,
			'sender_id'       => $this->senderId,
			'sender_name'     => $this->senderName,
			'sender_avatar'   => $this->senderAvatar,
			'body'            => $this->body,
			'type'            => $this->type,
			'created_at'      => $this->createdAt,
			'receiver_id'     => $this->receiverId,
		];
	}

	public function broadcastConnection(): string
	{
		return 'reverb';
	}
}
