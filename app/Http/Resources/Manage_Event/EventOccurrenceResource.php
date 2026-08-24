<?php

namespace App\Http\Resources\Manage_Event;

use Illuminate\Http\Resources\Json\JsonResource;

class EventOccurrenceResource extends JsonResource
{
    public function toArray($request): array
    {
        $confirmedCount = $this->relationLoaded('registrations')
            ? $this->registrations->where('status', 'confirmed')->count()
            : $this->registrations()->where('status', 'confirmed')->count();

        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'label' => $this->label,
            'starts_at' => optional($this->starts_at)->toIso8601String(),
            'ends_at' => optional($this->ends_at)->toIso8601String(),
            'timezone' => $this->timezone,
            'location_mode' => $this->location_mode,
            'location_text' => $this->location_text,
            'address' => $this->address,
            'online_url' => $this->online_url,
            'registration_open_at' => optional($this->registration_open_at)->toIso8601String(),
            'registration_close_at' => optional($this->registration_close_at)->toIso8601String(),
            'capacity' => (int) $this->capacity,
            'confirmed_count' => $confirmedCount,
            'lifecycle_status' => $this->lifecycle_status,
        ];
    }
}
