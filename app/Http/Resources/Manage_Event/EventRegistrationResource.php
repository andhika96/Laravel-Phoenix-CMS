<?php

namespace App\Http\Resources\Manage_Event;

use Illuminate\Http\Resources\Json\JsonResource;

class EventRegistrationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'occurrence_id' => $this->occurrence_id,
            'account_id' => $this->account_id,
            'account_name' => $this->account?->fullname,
            'account_email' => $this->account?->email,
            'status' => $this->status,
            'waitlist_position' => $this->waitlist_position,
            'registered_at' => optional($this->registered_at)->toIso8601String(),
            'confirmed_at' => optional($this->confirmed_at)->toIso8601String(),
            'cancelled_at' => optional($this->cancelled_at)->toIso8601String(),
            'attended_at' => optional($this->attended_at)->toIso8601String(),
        ];
    }
}
