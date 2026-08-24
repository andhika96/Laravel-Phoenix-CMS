<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventBookingSetting extends Model
{
    use HasFactory;

    protected $table = 'event_booking_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'default_reminder_lead_minutes' => 'integer',
        'default_cancel_cutoff_minutes' => 'integer',
    ];
}
