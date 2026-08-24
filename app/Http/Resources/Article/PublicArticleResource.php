<?php

namespace App\Http\Resources\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uri' => $this->uri,
            'title' => $this->title,
            'excerpt' => Str::limit(trim(strip_tags((string) $this->content)), 180),
            'tags' => array_values(array_filter(array_map('trim', explode(',', (string) $this->tags)))),
            'thumbnail_small_url' => $this->thumbnailUrl($this->thumb_s),
            'thumbnail_large_url' => $this->thumbnailUrl($this->thumb_l),
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'code' => $this->category->code,
            ] : null,
            'author' => $this->author ? [
                'id' => $this->author->id,
                'name' => $this->author->fullname ?: $this->author->username,
            ] : null,
            'published_at' => optional($this->created_at)->toIso8601String(),
        ];
    }

    private function thumbnailUrl(?string $path): ?string
    {
        return $path && Storage::disk('public')->exists($path) ? Storage::url($path) : null;
    }
}
