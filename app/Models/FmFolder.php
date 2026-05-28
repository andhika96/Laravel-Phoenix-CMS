<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FmFolder extends Model
{
    use SoftDeletes;

    protected $table = 'fm_folders';

    protected $fillable = [
        'parent_id', 'user_id', 'name', 'slug', 'path', 'disk', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // ── Relations ──────────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FmFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(FmFolder::class, 'parent_id')->orderBy('name');
    }

    public function files(): HasMany
    {
        return $this->hasMany(FmFile::class, 'folder_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────────────

    /**
     * Build breadcrumb path array: root → ... → this folder
     */
    public function breadcrumb(): array
    {
        $crumbs = [];
        $folder = $this;
        while ($folder) {
            array_unshift($crumbs, ['id' => $folder->id, 'name' => $folder->name]);
            $folder = $folder->parent;
        }
        return $crumbs;
    }
}
