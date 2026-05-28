<?php

namespace App\Events\CMS;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ArticleCreated
 *
 * Broadcast real-time notification ketika artikel baru dibuat
 * via Laravel Reverb — channel: cms-notifications
 */
class ArticleCreated implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public function __construct(
		public readonly string $actorName,
		public readonly string $articleTitle,
		public readonly string $articleSlug,
		public readonly string $category,
		public readonly string $status,
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
		return 'article.created';
	}

	public function broadcastWith(): array
	{
		return [
			'type'          => 'article_created',
			'icon'          => 'file-alt',
			'title'         => 'Artikel Baru Dibuat',
			'message'       => $this->actorName . ' membuat artikel: ' . $this->articleTitle,
			'meta'          => [
				'slug'      => $this->articleSlug,
				'category'  => $this->category,
				'status'    => $this->status,
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
