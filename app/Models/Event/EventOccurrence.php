<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventOccurrence extends Model
{
    use HasFactory;

    protected $table = 'event_occurrences';

    protected $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'registration_open_at' => 'datetime',
        'registration_close_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'occurrence_id');
    }
}
