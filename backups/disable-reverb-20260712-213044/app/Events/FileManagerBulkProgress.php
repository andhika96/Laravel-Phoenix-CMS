<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * FileManagerBulkProgress
 *
 * Broadcast real-time progress untuk bulk operation (update permission / metadata)
 * via Laravel Reverb — App 2 (File Manager), channel: fm-bulk-progress.{jobId}
 *
 * Menggunakan ShouldBroadcastNow (synchronous, tanpa queue) agar progress
 * langsung dikirim ke frontend tanpa delay queue worker.
 *
 * Frontend mendengarkan via Pusher.js CDN langsung (bukan laravel-echo package)
 * dengan app key dari REVERB_FM_APP_KEY.
 */
class FileManagerBulkProgress implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $jobId,
        public readonly int    $current,
        public readonly int    $total,
        public readonly string $currentFile,
        public readonly string $status,      // 'processing' | 'done' | 'error'
        public readonly string $message = '',
    ) {}

    /**
     * Channel: private per jobId agar tidak bocor antar user.
     * Frontend subscribe ke channel ini dengan jobId yang diterima dari response awal.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('fm-bulk-progress.' . $this->jobId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'progress';
    }

    public function broadcastWith(): array
    {
        return [
            'job_id'       => $this->jobId,
            'current'      => $this->current,
            'total'        => $this->total,
            'percent'      => $this->total > 0 ? round($this->current / $this->total * 100) : 0,
            'current_file' => $this->currentFile,
            'status'       => $this->status,
            'message'      => $this->message,
        ];
    }

    /**
     * Override connection agar event ini di-broadcast via Reverb App 2 (File Manager),
     * bukan Reverb App 1 (CMS Main).
     *
     * Config reverb.php sudah punya dua apps:
     *   - App 1: REVERB_APP_KEY (CMS Main)
     *   - App 2: REVERB_FM_APP_KEY (File Manager)
     *
     * Kita perlu set BROADCAST_CONNECTION=reverb dan pastikan
     * Reverb memilih app yang benar berdasarkan app_key.
     * Karena kedua app share server yang sama (port 9001),
     * Reverb server akan route ke app yang tepat berdasarkan key.
     */
    public function broadcastConnection(): string
    {
        return 'reverb';
    }
}
