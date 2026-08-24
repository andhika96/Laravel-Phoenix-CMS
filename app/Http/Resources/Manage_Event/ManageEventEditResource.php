<?php

namespace App\Http\Resources\Manage_Event;

use Illuminate\Http\Resources\Json\JsonResource;

class ManageEventEditResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'uri' => $this->uri,
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
            'tags' => $this->tags,
            'category_id' => $this->category_id,
            'publication_status' => $this->publication_status,
            'visibility' => $this->visibility,
            'reminder_lead_minutes' => $this->reminder_lead_minutes,
            'cancel_cutoff_minutes' => $this->cancel_cutoff_minutes,
            'thumbnail_small_url' => $this->thumbnailUrl($this->thumb_s),
            'thumbnail_large_url' => $this->thumbnailUrl($this->thumb_l),
            'occurrences' => EventOccurrenceResource::collection($this->whenLoaded('occurrences')),
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
