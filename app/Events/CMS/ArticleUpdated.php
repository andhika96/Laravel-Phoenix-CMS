<?php

namespace App\Events\CMS;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ArticleUpdated
 *
 * Broadcast real-time notification ketika artikel disunting/diupdate
 * via Laravel Reverb — channel: cms-notifications
 */
class ArticleUpdated implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public function __construct(
		public readonly string $actorName,
		public readonly string $articleTitle,
		public readonly string $articleSlug,
		public readonly string $category,
		public readonly string $status,
		public readonly string $updatedAt,
	) {}

	public function broadcastOn(): array
	{
		return [
			new Channel('cms-notifications'),
		];
	}

	public function broadcastAs(): string
	{
		return 'article.updated';
	}

	public function broadcastWith(): array
	{
		return [
			'type'          => 'article_updated',
			'icon'          => 'file-signature',
			'title'         => 'Artikel Diperbarui',
			'message'       => $this->actorName . ' menyunting artikel: ' . $this->articleTitle,
			'meta'          => [
				'slug'      => $this->articleSlug,
				'category'  => $this->category,
				'status'    => $this->status,
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
