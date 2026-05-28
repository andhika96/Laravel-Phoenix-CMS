<?php

namespace App\Events\CMS;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * UserUpdated
 *
 * Broadcast real-time notification ketika data user diupdate
 * via Laravel Reverb — channel: cms-notifications
 */
class UserUpdated implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public function __construct(
		public readonly string $actorName,
		public readonly string $targetUserName,
		public readonly string $targetUserEmail,
		public readonly string $updatedAt,
		public readonly array  $changedFields = [],
	) {}

	public function broadcastOn(): array
	{
		return [
			new Channel('cms-notifications'),
		];
	}

	public function broadcastAs(): string
	{
		return 'user.updated';
	}

	public function broadcastWith(): array
	{
		return [
			'type'          => 'user_updated',
			'icon'          => 'user-edit',
			'title'         => 'User Diperbarui',
			'message'       => $this->actorName . ' memperbarui akun: ' . $this->targetUserName,
			'meta'          => [
				'email'         => $this->targetUserEmail,
				'changed_fields'=> $this->changedFields,
			],
			'actor'         => $this->actorName,
			'created_at'    => $this->updatedAt,
		];
	}

	public function broadcastConnection(): string
	{
		return 'reverb';
	}
}
