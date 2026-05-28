<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FmApiKey extends Model
{
    protected $table = 'fm_api_keys';

    protected $fillable = [
        'user_id', 'name', 'key', 'secret',
        'allowed_origins', 'allowed_disks',
        'can_upload', 'can_delete', 'can_rename', 'can_move', 'can_create_folder',
        'is_active', 'expires_at', 'last_used_at', 'last_used_ip', 'request_count',
    ];

    protected $casts = [
        'allowed_origins'    => 'array',
        'allowed_disks'      => 'array',
        'can_upload'         => 'boolean',
        'can_delete'         => 'boolean',
        'can_rename'         => 'boolean',
        'can_move'           => 'boolean',
        'can_create_folder'  => 'boolean',
        'is_active'          => 'boolean',
        'expires_at'         => 'datetime',
        'last_used_at'       => 'datetime',
    ];

    protected $hidden = ['secret'];

    // ── Relations ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────────────

    /**
     * Generate API key baru (64 char hex)
     */
    public static function generateKey(): string
    {
        return bin2hex(random_bytes(32)); // 64 hex chars
    }

    /**
     * Cek apakah key masih valid (aktif & belum expired)
     */
    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }

    /**
     * Cek apakah origin diizinkan
     */
    public function isOriginAllowed(string $origin): bool
    {
        if (empty($this->allowed_origins)) return true; // NULL = semua
        foreach ($this->allowed_origins as $allowed) {
            if ($allowed === '*') return true;
            if (fnmatch($allowed, $origin)) return true;
        }
        return false;
    }

    /**
     * Rekam penggunaan
     */
    public function recordUsage(string $ip): void
    {
        $this->update([
            'last_used_at'  => now(),
            'last_used_ip'  => $ip,
            'request_count' => $this->request_count + 1,
        ]);
    }

    /**
     * Permission check shorthand
     */
    public function can(string $action): bool
    {
        $map = [
            'upload'         => 'can_upload',
            'delete'         => 'can_delete',
            'rename'         => 'can_rename',
            'move'           => 'can_move',
            'create_folder'  => 'can_create_folder',
        ];
        return isset($map[$action]) ? (bool) $this->{$map[$action]} : false;
    }
}
