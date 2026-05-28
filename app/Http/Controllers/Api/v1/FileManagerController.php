<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\StorageAttributes;

/**
 * FileManagerController — No-Database File Manager
 *
 * PERFORMA S3/R2 — optimasi lengkap:
 *
 * 1. Cache driver: FILE (bukan database)
 *    - Cache::store('file') → baca/tulis file lokal, 0 MySQL query
 *    - Cache database = 2-3 extra MySQL query per browse = lambat
 *
 * 2. Hapus storage->exists() di browse
 *    - exists() = 1 HeadObject ke S3 setiap klik folder (50-150ms wasted)
 *    - Diganti: langsung listContents(), catch error jika folder tidak ada
 *    - Folder root diizinkan auto-create hanya saat root_path dikonfigurasi
 *
 * 3. listContents() = satu ListObjectsV2 API call ke S3/R2
 *    - Tanpa HeadObject per file (fileSize + lastModified sudah ada di response)
 *
 * 4. Cache TTL 5 menit (naik dari 60 detik)
 *    - Bust otomatis setiap write operation (upload, rename, move, copy, delete)
 *
 * 5. Pagination di browse (per_page + page)
 *    - Raw listing di-cache dulu, lalu paginate di PHP
 *    - Frontend bisa infinite scroll tanpa re-fetch ke S3
 *
 * 6. imagePreview: tambah Cache-Control + ETag
 *    - Browser cache thumbnail 24 jam
 *    - 304 Not Modified jika tidak berubah → 0 byte transfer
 */
class FileManagerController extends Controller
{
    // ══════════════════════════════════════════════════════
    //  CACHE STORE — pakai 'file', bukan default 'database'
    // ══════════════════════════════════════════════════════

    protected function fmCache(): \Illuminate\Cache\Repository
    {
        return Cache::store('file');
    }

    // ══════════════════════════════════════════════════════
    //  HELPERS
    // ══════════════════════════════════════════════════════

    protected function ok(mixed $data = null, string $msg = 'OK', int $code = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $msg, 'data' => $data], $code);
    }

    protected function fail(string $msg, int $code = 422): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $msg], $code);
    }

    protected function getDisk(Request $request): string
    {
        $d       = $request->query('disk', $request->input('disk', config('filemanager.default_disk', 'public')));
        $allowed = array_keys(config('filemanager.available_disks', ['public' => 'Local/Public']));
        return in_array($d, $allowed) ? $d : config('filemanager.default_disk', 'public');
    }

    protected function safePath(?string $path): string
    {
        $path = $path ?? '';
        $path = str_replace(['..', '\\'], ['', '/'], $path);
        return trim($path, '/');
    }

    protected function toStoragePath(string $userPath): string
    {
        $root     = trim(config('filemanager.root_path', ''), '/');
        $userPath = $this->safePath($userPath);
        if (!$root) return $userPath;
        if (!$userPath) return $root;
        return $root . '/' . $userPath;
    }

    protected function toUserPath(string $storagePath): string
    {
        $root = trim(config('filemanager.root_path', ''), '/');
        if (!$root) return $storagePath;
        if ($storagePath === $root) return '';
        if (str_starts_with($storagePath, $root . '/')) {
            return substr($storagePath, strlen($root) + 1);
        }
        return $storagePath;
    }

    protected function guessMime(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $map = [
            'jpg'  => 'image/jpeg',    'jpeg' => 'image/jpeg',
            'png'  => 'image/png',     'gif'  => 'image/gif',
            'webp' => 'image/webp',    'svg'  => 'image/svg+xml',
            'bmp'  => 'image/bmp',
            'mp4'  => 'video/mp4',     'webm' => 'video/webm',
            'mov'  => 'video/quicktime','avi'  => 'video/x-msvideo',
            'mkv'  => 'video/x-matroska',
            'mp3'  => 'audio/mpeg',    'wav'  => 'audio/wav',
            'ogg'  => 'audio/ogg',     'aac'  => 'audio/aac',
            'flac' => 'audio/flac',
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt'  => 'text/plain',    'csv'  => 'text/csv',
            'zip'  => 'application/zip',
            'rar'  => 'application/x-rar-compressed',
            '7z'   => 'application/x-7z-compressed',
            'tar'  => 'application/x-tar',
            'gz'   => 'application/gzip',
        ];
        return $map[$ext] ?? 'application/octet-stream';
    }

    protected function fileType(string $mime): string
    {
        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_starts_with($mime, 'video/')) return 'video';
        if (str_starts_with($mime, 'audio/')) return 'audio';
        $docs = ['application/pdf','application/msword','application/vnd.ms',
                 'application/vnd.openxmlformats','text/plain','text/csv'];
        foreach ($docs as $d) { if (str_contains($mime, $d)) return 'document'; }
        $arch = ['application/zip','application/x-rar','application/x-7z',
                 'application/x-tar','application/gzip'];
        foreach ($arch as $a) { if (str_contains($mime, $a)) return 'archive'; }
        return 'other';
    }

    protected function humanBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    /**
     * Generate public URL untuk file.
     * Mendukung s3_r2, s3_do, s3_b2, s3_aws, dll.
     */
    protected function fileUrl(string $disk, string $path): string
    {
        try {
            if ($disk === 'local') {
                return route('filemanager.serve', ['disk' => $disk, 'path' => $path]);
            }
            $s3Disks = config('filemanager.s3_disks', ['s3']);
            if (in_array($disk, $s3Disks)) {
                return Storage::disk($disk)->url($path);
            }
            return Storage::disk('public')->url($path);
        } catch (\Throwable $e) {
            return '/storage/' . $path;
        }
    }

    // ── Cache helpers — semua pakai store('file') ──────────

    protected function browseCacheKey(string $disk, string $storagePath): string
    {
        return 'fm_browse_' . $disk . '_' . md5($storagePath);
    }

    /**
     * Bust cache browse untuk path yang terdampak operasi write.
     * Pakai fmCache() agar konsisten dengan store('file').
     */
    protected function bustBrowseCache(string $disk, array $paths = []): void
    {
        $cache = $this->fmCache();
        $cache->forget($this->browseCacheKey($disk, ''));
        $root = trim(config('filemanager.root_path', ''), '/');
        if ($root) $cache->forget($this->browseCacheKey($disk, $root));

        foreach ($paths as $path) {
            if (!$path) continue;
            $cache->forget($this->browseCacheKey($disk, $path));
            $parent = dirname($path);
            if ($parent && $parent !== '.') {
                $cache->forget($this->browseCacheKey($disk, $parent));
            }
        }
    }

    /**
     * Bust disk cache thumbnail untuk file-file yang dihapus/rename/move/edit.
     *
     * Cache thumbnail disimpan di storage/app/fm_thumbs/{hash}.jpg
     * Hash = md5(disk + '|' + storagePath + '|' + width)
     *
     * Karena width bisa 50-800, kita scan semua file dengan prefix hash
     * dari disk+path saja (tanpa width) untuk memastikan semua ukuran ikut terhapus.
     */
    protected function bustThumbCache(string $disk, array $storagePaths = []): void
    {
        $cacheDir = storage_path('app/fm_thumbs');
        if (!is_dir($cacheDir)) return;

        foreach ($storagePaths as $storagePath) {
            if (!$storagePath) continue;

            // Hash prefix = md5(disk + '|' + storagePath) — cocok untuk semua width
            // Scan semua file cache dengan prefix ini
            $prefix = md5($disk . '|' . $storagePath);
            $files  = glob($cacheDir . '/' . $prefix . '*.jpg');

            // Karena hash sudah full md5 (32 char) tanpa prefix variable,
            // kita cek setiap width yang mungkin dipakai (50-800)
            // Lebih efisien: langsung hapus hash exact untuk width umum yang dipakai
            foreach ([220, 300, 400, 600, 800] as $w) {
                $hash     = md5($disk . '|' . $storagePath . '|' . $w);
                $filePath = $cacheDir . '/' . $hash . '.jpg';
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        }
    }

    /**
     * Bust semua thumbnail cache di dalam sebuah folder (untuk deleteFolder).
     * Scan semua file .jpg di fm_thumbs dan cari yang path-nya berada di dalam folder.
     * Ini lebih berat tapi dipanggil hanya saat folder dihapus.
     */
    protected function bustThumbCacheFolder(string $disk, string $folderStoragePath): void
    {
        $cacheDir = storage_path('app/fm_thumbs');
        if (!is_dir($cacheDir)) return;

        // Tidak bisa scan berdasarkan isi folder karena thumbnail adalah hash — tidak ada mapping.
        // Solusi: hapus SEMUA thumbnail cache (akan di-regenerate saat dibuka lagi).
        // Ini terjadi hanya saat delete folder, jadi acceptable.
        $files = glob($cacheDir . '/*.jpg');
        if ($files) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    // ══════════════════════════════════════════════════════
    //  CORS PREFLIGHT
    // ══════════════════════════════════════════════════════

    public function preflight(): JsonResponse
    {
        return response()->json(['ok' => true])->withHeaders([
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-FM-Key, X-Requested-With',
        ]);
    }

    // ══════════════════════════════════════════════════════
    //  DISK MANAGEMENT
    // ══════════════════════════════════════════════════════

    public function getDisks(): JsonResponse
    {
        $availableDisks = config('filemanager.available_disks', ['public' => 'Local/Public']);
        $s3Disks        = config('filemanager.s3_disks', ['s3']);
        $result         = [];

        foreach ($availableDisks as $diskKey => $diskLabel) {
            $isS3      = in_array($diskKey, $s3Disks);
            $available = true;
            $reason    = null;

            if ($isS3) {
                $bucket = config("filesystems.disks.{$diskKey}.bucket");
                $key    = config("filesystems.disks.{$diskKey}.key");
                $secret = config("filesystems.disks.{$diskKey}.secret");

                if (empty($bucket) || empty($key) || empty($secret)) {
                    $available = false;
                    $missing   = array_filter([
                        empty($bucket) ? 'bucket' : null,
                        empty($key)    ? 'key'    : null,
                        empty($secret) ? 'secret' : null,
                    ]);
                    $reason = "Konfigurasi {$diskLabel} belum lengkap. Field kosong: " . implode(', ', $missing);
                }
            }

            $result[] = [
                'key'       => $diskKey,
                'label'     => $diskLabel,
                'type'      => $isS3 ? 's3' : 'local',
                'available' => $available,
                'reason'    => $reason,
            ];
        }

        return $this->ok($result);
    }

    public function testDisk(Request $request): JsonResponse
    {
        $diskKey   = $request->query('disk', 's3');
        $s3Disks   = config('filemanager.s3_disks', ['s3']);
        $diskLabel = config("filemanager.available_disks.{$diskKey}", $diskKey);

        if (!in_array($diskKey, $s3Disks)) {
            return $this->ok(['available' => true, 'disk' => $diskKey, 'message' => 'Disk local tidak perlu test koneksi.']);
        }

        $bucket   = config("filesystems.disks.{$diskKey}.bucket");
        $key      = config("filesystems.disks.{$diskKey}.key");
        $secret   = config("filesystems.disks.{$diskKey}.secret");
        $region   = config("filesystems.disks.{$diskKey}.region");
        $endpoint = config("filesystems.disks.{$diskKey}.endpoint");

        $debug = [
            'disk'     => $diskKey,
            'label'    => $diskLabel,
            'bucket'   => $bucket   ? substr($bucket, 0, 3) . '***' : '(kosong)',
            'key'      => $key      ? substr($key, 0, 6) . '***' : '(kosong)',
            'secret'   => $secret   ? '***set***' : '(kosong)',
            'region'   => $region   ?? '(tidak diset)',
            'endpoint' => $endpoint ?? '(tidak diset)',
        ];

        if (empty($bucket) || empty($key) || empty($secret)) {
            return $this->ok(array_merge($debug, ['available' => false, 'error' => 'Credential belum lengkap.']));
        }

        try {
            Storage::disk($diskKey)->exists('_fm_test_' . time());
            return $this->ok(array_merge($debug, ['available' => true, 'message' => 'Koneksi berhasil!']));
        } catch (\Throwable $e) {
            return $this->ok(array_merge($debug, [
                'available'   => false,
                'error'       => $e->getMessage(),
                'error_class' => get_class($e),
            ]));
        }
    }

    public function checkDisk(Request $request): JsonResponse
    {
        $disk   = $request->query('disk', config('filemanager.default_disk', 'public'));
        $s3List = config('filemanager.s3_disks', ['s3']);

        if (!in_array($disk, $s3List)) {
            return $this->ok(['available' => true, 'disk' => $disk]);
        }

        $bucket = config("filesystems.disks.{$disk}.bucket");
        $key    = config("filesystems.disks.{$disk}.key");
        $secret = config("filesystems.disks.{$disk}.secret");

        if (empty($bucket) || empty($key) || empty($secret)) {
            $label = config("filemanager.available_disks.{$disk}", $disk);
            return $this->ok([
                'available' => false,
                'disk'      => $disk,
                'reason'    => "Konfigurasi {$label} belum lengkap.",
            ]);
        }

        try {
            Storage::disk($disk)->exists('_fm_healthcheck_');
            return $this->ok(['available' => true, 'disk' => $disk]);
        } catch (\Throwable $e) {
            return $this->ok(['available' => false, 'disk' => $disk, 'reason' => $e->getMessage()]);
        }
    }

    // ══════════════════════════════════════════════════════
    //  BROWSE — OPTIMIZED
    //
    //  Perubahan dari versi lama:
    //  - Cache::store('file') — eliminasi MySQL queries
    //  - Hapus storage->exists() check — hemat 1 S3 HeadObject per klik
    //  - TTL 300 detik (5 menit, naik dari 60 detik)
    //  - Pagination: ?page=1&per_page=100
    //  - Response tambah: total_folders, total_files, page, per_page, total_pages
    // ══════════════════════════════════════════════════════

    public function browse(Request $request): JsonResponse
    {
        $disk        = $this->getDisk($request);
        $userPath    = $this->safePath($request->query('path', ''));
        $storagePath = $this->toStoragePath($userPath);
        $storage     = Storage::disk($disk);

        $search  = trim($request->query('search', ''));
        $type    = $request->query('type', '');
        $sortBy  = in_array($request->query('sort_by'), ['name', 'size', 'date']) ? $request->query('sort_by') : 'date';
        $sortDir = $request->query('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $sizeMin = ($request->query('size_min') !== null && $request->query('size_min') !== '') ? (int)$request->query('size_min') : null;
        $sizeMax = ($request->query('size_max') !== null && $request->query('size_max') !== '') ? (int)$request->query('size_max') : null;

        // Pagination params
        $perPage = max(1, min(500, (int)($request->query('per_page', 100))));
        $page    = max(1, (int)($request->query('page', 1)));

        // ── Cache raw listing 1 jam via file store (0 MySQL query) ──
        // Tidak lakukan exists() check — langsung listContents(), catch jika error
        // Root path auto-create tetap dipertahankan untuk kompatibilitas
        $cacheKey   = $this->browseCacheKey($disk, $storagePath);
        $rawListing = $this->fmCache()->remember($cacheKey, 3600, function () use ($storage, $storagePath, $disk) {
            // Khusus root_path yang dikonfigurasi: auto-create jika belum ada
            $root = trim(config('filemanager.root_path', ''), '/');
            if ($storagePath === $root && $root !== '') {
                try {
                    if (!$storage->directoryExists($storagePath)) {
                        $storage->makeDirectory($storagePath);
                    }
                } catch (\Throwable $_) {}
            }
            return $this->fetchListing($storage, $storagePath);
        });

        // Jika fetchListing return null (folder tidak ada di S3), kembalikan 404
        if ($rawListing === null) {
            return $this->fail('Folder tidak ditemukan.', 404);
        }

        $folders = [];
        $files   = [];

        foreach ($rawListing as $item) {
            if ($item['type'] === 'dir') {
                $name = basename($item['path']);
                if ($search && !str_contains(strtolower($name), strtolower($search))) continue;
                $folders[] = [
                    'name' => $name,
                    'path' => $this->toUserPath($item['path']),
                    'type' => 'folder',
                ];
            } else {
                $name  = basename($item['path']);
                $mime  = $this->guessMime($name);
                $fType = $this->fileType($mime);
                $ext   = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $size  = $item['size'] ?? 0;
                $mod   = $item['lastModified'] ?? 0;

                if ($type   && $fType !== $type)                                        continue;
                if ($search && !str_contains(strtolower($name), strtolower($search)))  continue;
                if ($sizeMin !== null && $size < $sizeMin)                              continue;
                if ($sizeMax !== null && $size > $sizeMax)                              continue;

                $files[] = [
                    'name'          => $name,
                    'path'          => $this->toUserPath($item['path']),
                    'url'           => $this->fileUrl($disk, $item['path']),
                    'mime_type'     => $mime,
                    'extension'     => $ext,
                    'file_type'     => $fType,
                    'size'          => $size,
                    'size_human'    => $this->humanBytes($size),
                    'last_modified' => $mod,
                    'date'          => $mod ? date('d M Y H:i', $mod) : '-',
                    'type'          => 'file',
                ];
            }
        }

        usort($folders, function ($a, $b) use ($sortDir) {
            $r = strcmp(strtolower($a['name']), strtolower($b['name']));
            return $sortDir === 'desc' ? -$r : $r;
        });

        usort($files, function ($a, $b) use ($sortBy, $sortDir) {
            $r = match ($sortBy) {
                'name' => strcmp(strtolower($a['name']), strtolower($b['name'])),
                'size' => $a['size'] <=> $b['size'],
                default => $a['last_modified'] <=> $b['last_modified'],
            };
            return $sortDir === 'desc' ? -$r : $r;
        });

        // ── Pagination ────────────────────────────────────
        // Folders selalu tampil semua di halaman pertama (jumlah biasanya sedikit)
        // Files di-paginate
        $totalFolders = count($folders);
        $totalFiles   = count($files);

        // Halaman pertama: tampilkan semua folder + files halaman 1
        // Halaman berikutnya: folder kosong + files halaman N
        if ($page === 1) {
            $pagedFiles   = array_slice($files, 0, $perPage);
            $pagedFolders = $folders;
        } else {
            // Offset: (page-1)*perPage, tapi halaman 1 sudah ambil $perPage item
            $offset       = ($page - 1) * $perPage;
            $pagedFiles   = array_slice($files, $offset, $perPage);
            $pagedFolders = [];
        }

        $totalPages = $totalFiles > 0 ? (int)ceil($totalFiles / $perPage) : 1;

        $breadcrumb = [];
        if ($userPath !== '') {
            $parts = explode('/', $userPath);
            $built = '';
            foreach ($parts as $part) {
                $built        = $built ? $built . '/' . $part : $part;
                $breadcrumb[] = ['name' => $part, 'path' => $built];
            }
        }

        return $this->ok([
            'disk'          => $disk,
            'path'          => $userPath,
            'breadcrumb'    => $breadcrumb,
            'folders'       => array_values($pagedFolders),
            'files'         => array_values($pagedFiles),
            'total'         => $totalFolders + $totalFiles,
            'total_folders' => $totalFolders,
            'total_files'   => $totalFiles,
            'page'          => $page,
            'per_page'      => $perPage,
            'total_pages'   => $totalPages,
            'has_more'      => $page < $totalPages,
        ]);
    }

    /**
     * Fetch listing dari storage — satu ListObjectsV2 API call untuk S3/R2.
     *
     * Return null jika folder tidak ditemukan (bukan array kosong).
     * Array kosong = folder ada tapi isinya kosong.
     * null = folder tidak ada di S3.
     */
    protected function fetchListing($storage, string $storagePath): ?array
    {
        $listing = [];

        try {
            $contents = $storage->listContents($storagePath ?: '', false);

            foreach ($contents as $item) {
                if ($item->isDir()) {
                    $listing[] = [
                        'type'         => 'dir',
                        'path'         => $item->path(),
                        'size'         => null,
                        'lastModified' => null,
                    ];
                } else {
                    $listing[] = [
                        'type'         => 'file',
                        'path'         => $item->path(),
                        'size'         => method_exists($item, 'fileSize') ? ($item->fileSize() ?? 0) : 0,
                        'lastModified' => method_exists($item, 'lastModified') ? ($item->lastModified() ?? 0) : 0,
                    ];
                }
            }

            return $listing;

        } catch (\League\Flysystem\UnableToListContents $e) {
            // Folder tidak ada di S3 — return null agar browse bisa return 404
            return null;
        } catch (\Throwable $e) {
            // Fallback ke cara lama jika listContents gagal karena alasan lain
            try {
                $dirs  = $storage->directories($storagePath ?: null);
                $files = $storage->files($storagePath ?: null);
                foreach ($dirs as $d) {
                    $listing[] = ['type' => 'dir', 'path' => $d, 'size' => null, 'lastModified' => null];
                }
                foreach ($files as $f) {
                    $size = 0; $mod = 0;
                    try { $size = $storage->size($f); }         catch (\Throwable $_) {}
                    try { $mod  = $storage->lastModified($f); } catch (\Throwable $_) {}
                    $listing[] = ['type' => 'file', 'path' => $f, 'size' => $size, 'lastModified' => $mod];
                }
                return $listing;
            } catch (\Throwable $_) {
                return [];
            }
        }
    }

    // ══════════════════════════════════════════════════════
    //  INFO
    // ══════════════════════════════════════════════════════

    public function info(Request $request): JsonResponse
    {
        $disk        = $this->getDisk($request);
        $userPath    = $this->safePath($request->query('path', ''));
        $storagePath = $this->toStoragePath($userPath);
        $type        = $request->query('type', 'file');
        $storage     = Storage::disk($disk);

        if ($storagePath && !$storage->exists($storagePath)) {
            return $this->fail('Path tidak ditemukan.', 404);
        }

        if ($type === 'folder') {
            $totalSize = 0; $totalFiles = 0; $totalDirs = 0;
            try {
                $allFiles   = $storage->allFiles($storagePath ?: null);
                $allDirs    = $storage->allDirectories($storagePath ?: null);
                $totalFiles = count($allFiles);
                $totalDirs  = count($allDirs);
                foreach ($allFiles as $f) {
                    try { $totalSize += $storage->size($f); } catch (\Throwable $e) {}
                }
            } catch (\Throwable $e) {}

            return $this->ok([
                'type'             => 'folder',
                'name'             => $storagePath ? basename($storagePath) : 'Root',
                'path'             => $userPath,
                'disk'             => $disk,
                'total_size'       => $totalSize,
                'total_size_human' => $this->humanBytes($totalSize),
                'total_files'      => $totalFiles,
                'total_dirs'       => $totalDirs,
                'total_items'      => $totalFiles + $totalDirs,
            ]);
        }

        try {
            $size    = $storage->size($storagePath);
            $modTime = $storage->lastModified($storagePath);
        } catch (\Throwable $e) {
            return $this->fail('Gagal membaca info file: ' . $e->getMessage(), 500);
        }

        $name = basename($storagePath);
        $mime = $this->guessMime($name);

        return $this->ok([
            'type'          => 'file',
            'name'          => $name,
            'path'          => $userPath,
            'disk'          => $disk,
            'url'           => $this->fileUrl($disk, $storagePath),
            'mime_type'     => $mime,
            'extension'     => strtolower(pathinfo($name, PATHINFO_EXTENSION)),
            'file_type'     => $this->fileType($mime),
            'size'          => $size,
            'size_human'    => $this->humanBytes($size),
            'last_modified' => $modTime,
            'date'          => date('d M Y H:i', $modTime),
            'date_only'     => date('d M Y', $modTime),
        ]);
    }

    // ══════════════════════════════════════════════════════
    //  IMAGE EDITOR
    // ══════════════════════════════════════════════════════

    public function imageEdit(Request $request): JsonResponse
    {
        $disk        = $this->getDisk($request);
        $userPath    = $this->safePath($request->input('path', ''));
        $storagePath = $this->toStoragePath($userPath);
        $storage     = Storage::disk($disk);

        if (!$storagePath || !$storage->exists($storagePath)) {
            return $this->fail('File gambar tidak ditemukan.', 404);
        }
        if (!class_exists(\Intervention\Image\ImageManager::class)) {
            return $this->fail('Package intervention/image v3 belum terinstall.', 500);
        }

        try {
            $ops        = $request->input('operations', []);
            $saveAsNew  = (bool)$request->input('save_as_new', true);
            $outputFmt  = $request->input('output_format', 'original');
            $quality    = max(10, min(100, (int)$request->input('quality', 90)));

            $imageData  = $storage->get($storagePath);
            $manager    = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image      = $manager->read($imageData);

            foreach ($ops as $op) {
                $opName = $op['type'] ?? '';
                match ($opName) {
                    'crop'       => $image->crop((int)$op['width'], (int)$op['height'], (int)$op['x'], (int)$op['y']),
                    'resize'     => $image->scale(width: (int)$op['width'], height: (int)($op['height'] ?? 0)),
                    'rotate'     => $image->rotate((int)$op['angle']),
                    'flip_h'     => $image->flip(),
                    'flip_v'     => $image->flop(),
                    'brightness' => $image->brightness((int)$op['value']),
                    'contrast'   => $image->contrast((int)$op['value']),
                    'grayscale'  => $image->greyscale(),
                    'blur'       => $image->blur((int)($op['amount'] ?? 5)),
                    'sharpen'    => $image->sharpen((int)($op['amount'] ?? 10)),
                    default      => null,
                };
            }

            $ext     = strtolower(pathinfo($storagePath, PATHINFO_EXTENSION));
            $saveExt = ($outputFmt !== 'original' && in_array($outputFmt, ['jpg','jpeg','png','webp'])) ? $outputFmt : $ext;

            if ($saveAsNew) {
                $base     = pathinfo($storagePath, PATHINFO_FILENAME);
                $dir      = dirname($storagePath);
                $newName  = $base . '_edited_' . time() . '.' . $saveExt;
                $savePath = ($dir === '.') ? $newName : $dir . '/' . $newName;
            } else {
                $savePath = $storagePath;
            }

            $encoded = match($saveExt) {
                'jpg', 'jpeg' => $image->toJpeg($quality)->toString(),
                'png'         => $image->toPng()->toString(),
                'webp'        => $image->toWebp($quality)->toString(),
                'gif'         => $image->toGif()->toString(),
                default       => $image->toJpeg($quality)->toString(),
            };

            $storage->put($savePath, $encoded);
            $this->bustBrowseCache($disk, [dirname($savePath)]);
            // Bust thumbnail cache: jika overwrite (bukan save as new), path sama
            // jika save as new, path lama tidak berubah tapi perlu bust path baru juga
            $this->bustThumbCache($disk, [$storagePath, $savePath]);

            return $this->ok([
                'path' => $this->toUserPath($savePath),
                'url'  => $this->fileUrl($disk, $savePath),
                'name' => basename($savePath),
            ], 'Gambar berhasil diedit');

        } catch (\Throwable $e) {
            return $this->fail('Gagal edit gambar: ' . $e->getMessage(), 500);
        }
    }

    /**
     * imagePreview — resize gambar on-the-fly untuk thumbnail di grid view.
     *
     * Route ini didaftarkan di routes/web.php (bukan api.php) dengan middleware
     * 'auth' biasa, sehingga session web guard bekerja normal dan browser
     * bisa load <img src="..."> tanpa perlu kirim Authorization header.
     *
     * Optimasi berlapis:
     * 1. Browser cache 24 jam (Cache-Control + ETag) — request kedua = 0ms
     * 2. Disk cache di storage/app/fm_thumbs/ — tidak perlu download dari S3 lagi
     *    setelah thumbnail pertama dibuat. Cache berdasarkan hash path+disk+width.
     * 3. Jika cache disk ada → serve langsung, 0 S3 request
     */
    public function imagePreview(Request $request): mixed
    {
        $disk      = $this->getDisk($request);
        $userPath  = $this->safePath($request->query('path', ''));
        $storagePath = $this->toStoragePath($userPath);
        $width     = max(50, min((int)$request->query('w', 220), 800));

        if (!$storagePath) abort(404);

        // ── Disk cache key ─────────────────────────────────────
        // Hash dari disk + path + width → nama file cache unik
        $cacheHash    = md5($disk . '|' . $storagePath . '|' . $width);
        $cacheDir     = storage_path('app/fm_thumbs');
        $cachePath    = $cacheDir . '/' . $cacheHash . '.jpg';
        $mime         = $this->guessMime(basename($storagePath));
        $etag         = '"' . $cacheHash . '"';

        // ── Cek If-None-Match dulu — jika browser sudah punya, 304 langsung ──
        $ifNoneMatch = $request->header('If-None-Match');
        if ($ifNoneMatch && $ifNoneMatch === $etag) {
            return response('', 304)->withHeaders([
                'ETag'          => $etag,
                'Cache-Control' => 'private, max-age=86400',
            ]);
        }

        // ── Serve dari disk cache jika sudah ada ───────────────
        if (file_exists($cachePath)) {
            return response()->file($cachePath, [
                'Content-Type'  => 'image/jpeg',
                'Cache-Control' => 'private, max-age=86400',
                'ETag'          => $etag,
            ]);
        }

        // ── Cache miss: download dari S3 + resize + simpan ke disk ──
        try {
            $storage = Storage::disk($disk);

            if (!$storage->exists($storagePath)) abort(404);

            $imageData = $storage->get($storagePath);

            // Buat direktori cache jika belum ada
            if (!is_dir($cacheDir)) mkdir($cacheDir, 0755, true);

            if (!class_exists(\Intervention\Image\ImageManager::class)) {
                // Tanpa Intervention: simpan file asli ke cache (tidak di-resize)
                file_put_contents($cachePath, $imageData);
                return response($imageData, 200)->withHeaders([
                    'Content-Type'  => $mime,
                    'Cache-Control' => 'private, max-age=86400',
                    'ETag'          => $etag,
                ]);
            }

            // Resize dengan Intervention Image
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image   = $manager->read($imageData)->scale(width: $width);
            $encoded = $image->toJpeg(80)->toString();

            // Simpan ke disk cache
            file_put_contents($cachePath, $encoded);

            return response($encoded, 200)->withHeaders([
                'Content-Type'  => 'image/jpeg',
                'Cache-Control' => 'private, max-age=86400',
                'ETag'          => $etag,
            ]);

        } catch (\Throwable $e) {
            abort(404);
        }
    }

    // ══════════════════════════════════════════════════════
    //  QUOTA
    // ══════════════════════════════════════════════════════

    public function quota(Request $request): JsonResponse
    {
        $disk    = $this->getDisk($request);
        $userId  = $request->fmUser?->id ?? 0;
        $storage = Storage::disk($disk);

        $maxStorage = config('filemanager.default_max_storage', 10737418240);

        $usedStorage = 0;
        try {
            $allFiles = $storage->allFiles('');
            foreach ($allFiles as $f) {
                try { $usedStorage += $storage->size($f); } catch (\Throwable $_) {}
            }
        } catch (\Throwable $_) {}

        $isUnlimited  = $maxStorage <= 0;
        $usedPercent  = (!$isUnlimited && $maxStorage > 0) ? round($usedStorage / $maxStorage * 100, 1) : 0;

        return $this->ok([
            'disk'                  => $disk,
            'used_storage'          => $usedStorage,
            'used_storage_human'    => $this->humanBytes($usedStorage),
            'max_storage'           => $maxStorage,
            'max_storage_human'     => $isUnlimited ? '∞' : $this->humanBytes($maxStorage),
            'is_storage_unlimited'  => $isUnlimited,
            'used_percent'          => $usedPercent,
        ]);
    }

    // ══════════════════════════════════════════════════════
    //  FOLDER OPERATIONS
    // ══════════════════════════════════════════════════════

    public function createFolder(Request $request): JsonResponse
    {
        $disk   = $this->getDisk($request);
        $parent = $this->toStoragePath($this->safePath($request->input('path', '')));
        $name   = trim($request->input('name', ''));

        if (!$name) return $this->fail('Nama folder diperlukan.');

        // Validasi nama — karakter tidak boleh mengandung / atau karakter ilegal
        if (preg_match('/[\/\\\\<>:"|?*]/', $name)) {
            return $this->fail('Nama folder mengandung karakter tidak valid.');
        }

        $newPath = $parent ? $parent . '/' . $name : $name;
        $storage = Storage::disk($disk);

        // Hapus directoryExists() check — ini 1 S3 API call ekstra yang tidak perlu.
        // S3/R2 tidak punya konsep "folder" secara fisik. makeDirectory di Flysystem S3
        // hanya buat placeholder kosong. Jika folder sudah ada, tidak ada error.
        try {
            $storage->makeDirectory($newPath);
            $this->bustBrowseCache($disk, [$parent]);
            return $this->ok(['path' => $this->toUserPath($newPath), 'name' => $name], 'Folder berhasil dibuat', 201);
        } catch (\Throwable $e) {
            return $this->fail('Gagal membuat folder: ' . $e->getMessage(), 500);
        }
    }

    public function renameFolder(Request $request): JsonResponse
    {
        $disk    = $this->getDisk($request);
        $oldPath = $this->toStoragePath($this->safePath($request->input('path', '')));
        $newName = trim($request->input('name', ''));

        if (!$oldPath || !$newName) return $this->fail('Path dan nama baru diperlukan.');

        $dir     = dirname($oldPath);
        $newPath = ($dir === '.') ? $newName : $dir . '/' . $newName;
        $storage = Storage::disk($disk);

        if (!$storage->exists($oldPath)) return $this->fail('Folder tidak ditemukan.', 404);
        if ($storage->exists($newPath))  return $this->fail('Nama folder sudah digunakan.');

        try {
            $storage->move($oldPath, $newPath);
            $this->bustBrowseCache($disk, [$oldPath, $newPath, $dir]);
            return $this->ok(['old_path' => $oldPath, 'new_path' => $newPath], 'Folder berhasil diubah');
        } catch (\Throwable $e) {
            return $this->fail('Gagal rename folder: ' . $e->getMessage(), 500);
        }
    }

    public function moveFolder(Request $request): JsonResponse
    {
        $disk    = $this->getDisk($request);
        $src     = $this->toStoragePath($this->safePath($request->input('path', '')));
        $target  = $this->toStoragePath($this->safePath($request->input('target', '')));
        $name    = basename($src);
        $dest    = $target ? $target . '/' . $name : $name;
        $storage = Storage::disk($disk);

        if (!$storage->exists($src)) return $this->fail('Folder tidak ditemukan.', 404);
        if ($storage->exists($dest)) return $this->fail('Folder tujuan sudah ada.');

        try {
            $storage->move($src, $dest);
            $this->bustBrowseCache($disk, [$src, $dest, dirname($src), $target]);
            return $this->ok(['old_path' => $src, 'new_path' => $dest], 'Folder berhasil dipindahkan');
        } catch (\Throwable $e) {
            return $this->fail('Gagal memindahkan folder: ' . $e->getMessage(), 500);
        }
    }

    public function deleteFolder(Request $request): JsonResponse
    {
        $disk = $this->getDisk($request);
        $path = $this->toStoragePath($this->safePath($request->input('path', '')));

        if (!$path) return $this->fail('Path folder diperlukan.');

        $storage = Storage::disk($disk);
        if (!$storage->exists($path)) return $this->fail('Folder tidak ditemukan.', 404);

        try {
            $storage->deleteDirectory($path);
            $this->bustBrowseCache($disk, [$path, dirname($path)]);
            $this->bustThumbCacheFolder($disk, $path);
            return $this->ok(null, 'Folder berhasil dihapus');
        } catch (\Throwable $e) {
            return $this->fail('Gagal menghapus folder: ' . $e->getMessage(), 500);
        }
    }

    // ══════════════════════════════════════════════════════
    //  FILE OPERATIONS
    // ══════════════════════════════════════════════════════

    public function upload(Request $request): JsonResponse
    {
        if (!$request->hasFile('file')) return $this->fail('File diperlukan.');

        $disk       = $this->getDisk($request);
        $userFolder = $this->safePath($request->input('path') ?? '');
        $folder     = $this->toStoragePath($userFolder);
        $file       = $request->file('file');
        $name       = $file->getClientOriginalName();
        $ext        = strtolower($file->getClientOriginalExtension());

        $forbidden = config('filemanager.forbidden_extensions', []);
        if (in_array($ext, $forbidden)) {
            return $this->fail("Ekstensi file .{$ext} tidak diizinkan untuk diupload.");
        }

        $storage = Storage::disk($disk);
        $dest    = $folder ? $folder . '/' . $name : $name;

        if ($storage->exists($dest)) {
            $base = pathinfo($name, PATHINFO_FILENAME);
            $name = $base . '_' . time() . '.' . $ext;
            $dest = $folder ? $folder . '/' . $name : $name;
        }

        try {
            $storage->put($dest, file_get_contents($file));
        } catch (\Throwable $e) {
            return $this->fail('Gagal upload ke ' . $disk . ': ' . $e->getMessage(), 500);
        }

        $mime     = $this->guessMime($name);
        $size     = $storage->size($dest);
        $userDest = $this->toUserPath($dest);

        $this->bustBrowseCache($disk, [$folder]);

        return $this->ok([
            'name'       => $name,
            'path'       => $userDest,
            'url'        => $this->fileUrl($disk, $dest),
            'mime_type'  => $mime,
            'extension'  => $ext,
            'file_type'  => $this->fileType($mime),
            'size'       => $size,
            'size_human' => $this->humanBytes($size),
        ], 'File berhasil diupload', 201);
    }

    public function renameFile(Request $request): JsonResponse
    {
        $disk    = $this->getDisk($request);
        $oldPath = $this->toStoragePath($this->safePath($request->input('path', '')));
        $newName = trim($request->input('name', ''));

        if (!$oldPath || !$newName) return $this->fail('Path dan nama baru diperlukan.');

        $oldExt = strtolower(pathinfo($oldPath, PATHINFO_EXTENSION));
        $newExt = strtolower(pathinfo($newName, PATHINFO_EXTENSION));
        if (!$newExt) $newName .= '.' . $oldExt;

        $dir     = dirname($oldPath);
        $newPath = ($dir === '.') ? $newName : $dir . '/' . $newName;
        $storage = Storage::disk($disk);

        if (!$storage->exists($oldPath)) return $this->fail('File tidak ditemukan.', 404);
        if ($storage->exists($newPath))  return $this->fail('Nama file sudah digunakan.');

        try {
            $storage->move($oldPath, $newPath);
            $this->bustBrowseCache($disk, [$dir]);
            $this->bustThumbCache($disk, [$oldPath]);
            return $this->ok([
                'old_path' => $oldPath,
                'new_path' => $newPath,
                'url'      => $this->fileUrl($disk, $newPath),
            ], 'File berhasil diubah');
        } catch (\Throwable $e) {
            return $this->fail('Gagal rename file: ' . $e->getMessage(), 500);
        }
    }

    public function moveFile(Request $request): JsonResponse
    {
        $disk    = $this->getDisk($request);
        $src     = $this->toStoragePath($this->safePath($request->input('path', '')));
        $target  = $this->toStoragePath($this->safePath($request->input('target', '')));
        $name    = basename($src);
        $dest    = $target ? $target . '/' . $name : $name;
        $storage = Storage::disk($disk);

        if (!$storage->exists($src)) return $this->fail('File tidak ditemukan.', 404);
        if ($storage->exists($dest)) {
            $base = pathinfo($name, PATHINFO_FILENAME);
            $ext  = pathinfo($name, PATHINFO_EXTENSION);
            $dest = ($target ? $target . '/' : '') . $base . '_' . time() . '.' . $ext;
        }

        try {
            $storage->move($src, $dest);
            $this->bustBrowseCache($disk, [dirname($src), $target]);
            $this->bustThumbCache($disk, [$src]);
            return $this->ok(['old_path' => $src, 'new_path' => $dest, 'url' => $this->fileUrl($disk, $dest)], 'File berhasil dipindahkan');
        } catch (\Throwable $e) {
            return $this->fail('Gagal memindahkan file: ' . $e->getMessage(), 500);
        }
    }

    public function copyFile(Request $request): JsonResponse
    {
        $disk    = $this->getDisk($request);
        $src     = $this->toStoragePath($this->safePath($request->input('path', '')));
        $target  = $this->toStoragePath($this->safePath($request->input('target', '')));
        $name    = basename($src);
        $base    = pathinfo($name, PATHINFO_FILENAME);
        $ext     = pathinfo($name, PATHINFO_EXTENSION);
        $dest    = ($target ? $target . '/' : '') . $base . '_copy_' . time() . '.' . $ext;
        $storage = Storage::disk($disk);

        if (!$storage->exists($src)) return $this->fail('File tidak ditemukan.', 404);

        try {
            $storage->copy($src, $dest);
            $this->bustBrowseCache($disk, [$target]);
            return $this->ok(['source_path' => $src, 'new_path' => $dest, 'url' => $this->fileUrl($disk, $dest)], 'File berhasil disalin', 201);
        } catch (\Throwable $e) {
            return $this->fail('Gagal menyalin file: ' . $e->getMessage(), 500);
        }
    }

    public function deleteFile(Request $request): JsonResponse
    {
        $disk = $this->getDisk($request);
        $path = $this->toStoragePath($this->safePath($request->input('path', '')));

        if (!$path) return $this->fail('Path file diperlukan.');

        $storage = Storage::disk($disk);
        if (!$storage->exists($path)) return $this->fail('File tidak ditemukan.', 404);

        try {
            $storage->delete($path);
            $this->bustBrowseCache($disk, [dirname($path)]);
            $this->bustThumbCache($disk, [$path]);
            return $this->ok(null, 'File berhasil dihapus');
        } catch (\Throwable $e) {
            return $this->fail('Gagal menghapus file: ' . $e->getMessage(), 500);
        }
    }

    public function serveFile(Request $request): mixed
    {
        $disk    = $request->query('disk', 'local');
        $path    = $this->toStoragePath($this->safePath($request->query('path', '')));
        if (!$path) abort(400);
        $storage = Storage::disk($disk);
        if (!$storage->exists($path)) abort(404);
        try {
            return response($storage->get($path), 200)
                ->header('Content-Type', $this->guessMime(basename($path)));
        } catch (\Throwable $e) {
            abort(500);
        }
    }

    // ══════════════════════════════════════════════════════
    //  BATCH OPERATIONS
    // ══════════════════════════════════════════════════════

    public function batchMove(Request $request): JsonResponse
    {
        $disk    = $this->getDisk($request);
        $items   = $request->input('items', []);
        $target  = $this->toStoragePath($this->safePath($request->input('target', '')));

        if (empty($items)) return $this->fail('Items tidak boleh kosong.');

        $storage       = Storage::disk($disk);
        $success       = [];
        $failed        = [];
        $affectedPaths = [$target];

        foreach ($items as $item) {
            $path = $this->toStoragePath($this->safePath($item['path'] ?? ''));
            if (!$path) continue;

            $name = basename($path);
            $dest = $target ? $target . '/' . $name : $name;
            $affectedPaths[] = dirname($path);

            try {
                if ($storage->exists($dest)) {
                    $base = pathinfo($name, PATHINFO_FILENAME);
                    $ext  = pathinfo($name, PATHINFO_EXTENSION);
                    $dest = ($target ? $target . '/' : '') . $base . '_' . time() . ($ext ? '.' . $ext : '');
                }
                $storage->move($path, $dest);
                $success[] = $path;
            } catch (\Throwable $e) {
                $failed[] = ['path' => $path, 'reason' => $e->getMessage()];
            }
        }

        $this->bustBrowseCache($disk, array_unique($affectedPaths));

        $msg = count($success) . ' item berhasil dipindahkan';
        if ($failed) $msg .= ', ' . count($failed) . ' gagal';

        return $this->ok(['success' => $success, 'failed' => $failed], $msg);
    }

    public function batchDelete(Request $request): JsonResponse
    {
        $disk    = $this->getDisk($request);
        $items   = $request->input('items', []);

        if (empty($items)) return $this->fail('Items tidak boleh kosong.');

        $storage       = Storage::disk($disk);
        $success       = [];
        $failed        = [];
        $affectedPaths = [];
        $deletedFiles  = []; // track path file (bukan folder) untuk bust thumb cache

        foreach ($items as $item) {
            $path = $this->toStoragePath($this->safePath($item['path'] ?? ''));
            $type = $item['type'] ?? 'file';
            if (!$path) continue;

            $affectedPaths[] = dirname($path);

            try {
                if ($type === 'folder') {
                    $storage->deleteDirectory($path);
                } else {
                    $storage->delete($path);
                    $deletedFiles[] = $path;
                }
                $success[] = $path;
            } catch (\Throwable $e) {
                $failed[] = ['path' => $path, 'reason' => $e->getMessage()];
            }
        }

        $this->bustBrowseCache($disk, array_unique($affectedPaths));
        if ($deletedFiles) $this->bustThumbCache($disk, $deletedFiles);

        $msg = count($success) . ' item berhasil dihapus';
        if ($failed) $msg .= ', ' . count($failed) . ' gagal';

        return $this->ok(['success' => $success, 'failed' => $failed], $msg);
    }

    // ══════════════════════════════════════════════════════
    //  SETTINGS (Admin only)
    // ══════════════════════════════════════════════════════

    public function getSettings(Request $request): JsonResponse
    {
        return $this->ok([
            'default_disk'       => config('filemanager.default_disk'),
            'available_disks'    => config('filemanager.available_disks'),
            'default_max_storage'   => config('filemanager.default_max_storage'),
            'default_max_file_size' => config('filemanager.default_max_file_size'),
            'allowed_mime_types'    => config('filemanager.allowed_mime_types'),
            'forbidden_extensions'  => config('filemanager.forbidden_extensions'),
            'image_editor_enabled'  => config('filemanager.image_editor_enabled'),
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        // Settings dikelola via .env / config — tidak disimpan ke DB
        return $this->fail('Ubah settings melalui file config/filemanager.php atau .env', 422);
    }

    public function updateUserQuota(Request $request, int $userId): JsonResponse
    {
        return $this->fail('Fitur user quota belum diimplementasi.', 501);
    }
}
