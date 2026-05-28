<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FmFile extends Model
{
    use SoftDeletes;

    protected $table = 'fm_files';

    protected $fillable = [
        'folder_id', 'user_id', 'original_name', 'name', 'path',
        'url', 'mime_type', 'extension', 'file_type', 'size',
        'disk', 'is_public', 'meta',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'meta'      => 'array',
        'size'      => 'integer',
    ];

    // ── Relations ──────────────────────────────────────────

    public function folder(): BelongsTo
    {
        return $this->belongsTo(FmFolder::class, 'folder_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    public function humanSize(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
