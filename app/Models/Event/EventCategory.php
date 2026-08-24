<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    use HasFactory;

    protected $table = 'event_categories';

    protected $guarded = ['id'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'category_id');
    }
}
