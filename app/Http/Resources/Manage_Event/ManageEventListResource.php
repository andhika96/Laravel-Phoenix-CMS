<?php

namespace App\Http\Resources\Manage_Event;

use Illuminate\Http\Resources\Json\JsonResource;

class ManageEventListResource extends JsonResource
{
    public function toArray($request): array
    {
        $nextOccurrence = $this->relationLoaded('occurrences')
            ? $this->occurrences->where('lifecycle_status', 'scheduled')->sortBy('starts_at')->first()
            : $this->occurrences()->where('lifecycle_status', 'scheduled')->orderBy('starts_at')->first();

        return [
            'id' => $this->id,
            'uri' => $this->uri,
            'title' => $this->title,
            'summary' => $this->summary,
            'category_id' => $this->category_id,
            'category' => $this->category?->name,
            'publication_status' => $this->publication_status,
            'visibility' => $this->visibility,
            'thumbnail_small_url' => $this->thumbnailUrl($this->thumb_s),
            'occurrence_count' => $this->relationLoaded('occurrences') ? $this->occurrences->count() : $this->occurrences()->count(),
            'next_occurrence_at' => optional($nextOccurrence?->starts_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }

    private function thumbnailUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return \Storage::disk('public')->exists($path) ? \Storage::url($path) : null;
    }
}
