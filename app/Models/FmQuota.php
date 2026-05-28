<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FmQuota extends Model
{
    protected $table = 'fm_quotas';

    protected $fillable = [
        'user_id', 'max_storage', 'max_file_size', 'used_storage',
    ];

    protected $casts = [
        'max_storage'   => 'integer',
        'max_file_size' => 'integer',
        'used_storage'  => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
