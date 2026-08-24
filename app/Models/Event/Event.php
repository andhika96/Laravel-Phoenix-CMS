<?php

namespace App\Models\Event;

use App\Models\Awesome_Admin\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $guarded = ['id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'created_by');
    }

    public function occurrences(): HasMany
    {
        return $this->hasMany(EventOccurrence::class, 'event_id');
    }
}
