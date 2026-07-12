<?php

namespace App\Events\CMS;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ChatUserTyping
 *
 * Broadcast indikator "sedang mengetik..." via Reverb.
 * Channel: chat.{conversationId}
 * Event  : user.typing
 */
class ChatUserTyping implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public function __construct(
		public readonly int    $conversationId,
		public readonly int    $userId,
		public readonly string $userName,
		public readonly bool   $isTyping,
	) {}

	public function broadcastOn(): array
	{
		return [
			new Channel('chat.' . $this->conversationId),
		];
	}

	public function broadcastAs(): string
	{
		return 'user.typing';
	}

	public function broadcastWith(): array
	{
		return [
			'conversation_id' => $this->conversationId,
			'user_id'         => $this->userId,
			'user_name'       => $this->userName,
			'is_typing'       => $this->isTyping,
		];
	}

	public function broadcastConnection(): string
	{
		return 'reverb';
	}
}
