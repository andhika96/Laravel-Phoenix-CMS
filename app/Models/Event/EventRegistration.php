<?php

namespace App\Models\Event;

use App\Models\Awesome_Admin\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $table = 'event_registrations';

    protected $guarded = ['id'];

    protected $casts = [
        'registered_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'attended_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function occurrence(): BelongsTo
    {
        return $this->belongsTo(EventOccurrence::class, 'occurrence_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
