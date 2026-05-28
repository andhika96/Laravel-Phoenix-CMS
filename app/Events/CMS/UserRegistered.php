<?php

namespace App\Events\CMS;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * UserRegistered
 *
 * Broadcast real-time notification ketika admin/superadmin membuat user baru
 * via Laravel Reverb — channel: cms-notifications
 */
class UserRegistered implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public function __construct(
		public readonly string $actorName,
		public readonly string $newUserName,
		public readonly string $newUserEmail,
		public readonly string $newUserRole,
		public readonly string $createdAt,
	) {}

	public function broadcastOn(): array
	{
		return [
			new Channel('cms-notifications'),
		];
	}

	public function broadcastAs(): string
	{
		return 'user.registered';
	}

	public function broadcastWith(): array
	{
		return [
			'type'          => 'user_registered',
			'icon'          => 'user-plus',
			'title'         => 'User Baru Terdaftar',
			'message'       => $this->actorName . ' menambahkan user baru: ' . $this->newUserName,
			'meta'          => [
				'email'     => $this->newUserEmail,
				'role'      => $this->newUserRole,
			],
			'actor'         => $this->actorName,
			'created_at'    => $this->createdAt,
		];
	}

	public function broadcastConnection(): string
	{
		return 'reverb';
	}
}
