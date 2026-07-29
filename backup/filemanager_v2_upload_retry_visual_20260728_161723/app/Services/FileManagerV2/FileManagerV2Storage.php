<?php

namespace App\Services\FileManagerV2;

use Carbon\CarbonImmutable;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\StorageAttributes;
use RuntimeException;

class FileManagerV2Storage
{
    public function prepare(): void
    {
        $local = config('filemanager_v2.local');

        foreach ([
            $local['files_root'],
            $local['cache_root'] . DIRECTORY_SEPARATOR . 'thumbnails',
            $local['metadata_root'],
            $local['cache_root'] . DIRECTORY_SEPARATOR . 'previews',
            $local['runtime_root'] . DIRECTORY_SEPARATOR . 'uploads',
            config('filemanager_v2.settings.root'),
        ] as $directory) {
            File::ensureDirectoryExists($directory, 0755, true);
        }

        // Protect the complete V2 physical root when public/storage is a valid
        // symlink. Laravel also registers a route guard before the legacy
        // generic /storage/{path} fallback for installations without a symlink.
        $htaccess = dirname($local['files_root']) . DIRECTORY_SEPARATOR . '.htaccess';
        if (! File::exists($htaccess)) {
            File::put($htaccess, "Options -Indexes\n<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Deny from all\n</IfModule>\n");
        }
    }

    public function profile(string $storage): array
    {
        $profile = $this->configuredConnections()[$storage] ?? null;

        if (! is_array($profile) || empty($profile['enabled'])) {
            abort(404, 'Storage connection tidak tersedia.');
        }

        return $profile;
    }

    /** @return array<int, array<string, mixed>> */
    public function profiles(): array
    {
        $this->prepare();

        return collect($this->configuredConnections())
            ->filter(fn (array $profile) => ! empty($profile['enabled']))
            ->map(function (array $profile, string $id): array {
                $usage = $this->usage($id, $profile);

                return [
                    ...$this->publicConnection($id, $profile),
                    'connected' => $this->connectionAvailable($id),
                    ...$usage,
                ];
            })
            ->values()
            ->all();
    }

    /** @param array<string, mixed> $profile @return array<string, int|string> */
    private function usage(string $storage, array $profile): array
    {
        $usedBytes = 0;

        try {
            foreach ($this->disk($storage)->listContents('', true) as $item) {
                if ($item instanceof StorageAttributes && ! $item->isDir()) {
                    $usedBytes += (int) ($item->fileSize() ?? 0);
                }
            }
        } catch (\Throwable) {
            // The connection status is reported separately; an unavailable remote disk has no reliable usage total.
        }

        $quotaBytes = max(0, (int) ($profile['quota_bytes'] ?? 0));
        $usagePercent = $quotaBytes > 0 ? min(100, (int) round(($usedBytes / $quotaBytes) * 100)) : 0;

        return [
            'usedBytes' => $usedBytes,
            'quotaBytes' => $quotaBytes,
            'usagePercent' => $usagePercent,
            'usedLabel' => $this->formatBytes($usedBytes),
            'quotaLabel' => $quotaBytes > 0 ? $this->formatBytes($quotaBytes) : 'No limit',
        ];
    }

    public function disk(string $storage): FilesystemAdapter
    {
        return $this->diskForProfile($this->profile($storage));
    }

    /** @param array<string, mixed> $profile */
    private function diskForProfile(array $profile): FilesystemAdapter
    {
        if (($profile['driver'] ?? null) === 'local') {
            $this->prepare();

            return Storage::build([
                'driver' => 'local',
                'root' => $this->localRoot($profile),
                'throw' => false,
            ]);
        }

        if (($profile['driver'] ?? null) === 's3') {
            $key = $this->decryptSecret($profile['key_encrypted'] ?? null);
            $secret = $this->decryptSecret($profile['secret_encrypted'] ?? null);

            if ($key === '' || $secret === '' || empty($profile['bucket'])) {
                throw new RuntimeException('Kredensial storage cloud belum lengkap.');
            }

            return Storage::build([
                'driver' => 's3',
                'key' => $key,
                'secret' => $secret,
                'region' => $profile['region'] ?: 'us-east-1',
                'bucket' => $profile['bucket'],
                'endpoint' => $profile['endpoint'] ?: null,
                'use_path_style_endpoint' => (bool) ($profile['use_path_style_endpoint'] ?? false),
                'root' => $profile['root'] ?? '',
                'throw' => false,
            ]);
        }

        return Storage::disk($profile['disk']);
    }

    public function path(string|null $path): string
    {
        $path = str_replace('\\', '/', trim((string) $path));
        $path = trim($path, '/');

        if ($path === '') {
            return '';
        }

        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..' || str_contains($segment, "\0")) {
                abort(422, 'Path tidak valid.');
            }
        }

        return implode('/', $segments);
    }

    /** @return array<string, mixed> */
    public function bootstrap(): array
    {
        $profiles = $this->bootstrapProfiles();
        $default = $this->settings()['defaultStorage'];
        $upload = $this->uploadSettings();

        if (! collect($profiles)->contains('id', $default)) {
            $default = $profiles[0]['id'] ?? 'local';
        }

        return [
            'defaultStorage' => $default,
            'profiles' => $profiles,
            'upload' => [
                'chunkSize' => $upload['chunk_size'],
                'chunkThreshold' => $upload['chunk_threshold'],
                'maxParallel' => $upload['max_parallel'],
                'maxFileSize' => $upload['max_file_size'],
                'retryAttempts' => $upload['retry_attempts'],
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function bootstrapProfiles(): array
    {
        $this->prepare();

        return collect($this->configuredConnections())
            ->filter(fn (array $profile) => ! empty($profile['enabled']))
            ->map(function (array $profile, string $id): array {
                $quotaBytes = max(0, (int) ($profile['quota_bytes'] ?? 0));

                return [
                    ...$this->publicConnection($id, $profile),
                    'connected' => null,
                    'usedBytes' => 0,
                    'quotaBytes' => $quotaBytes,
                    'usagePercent' => 0,
                    'usedLabel' => '0 B',
                    'quotaLabel' => $quotaBytes > 0 ? $this->formatBytes($quotaBytes) : 'No limit',
                    'usagePending' => true,
                ];
            })
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    public function settings(): array
    {
        $stored = $this->storedSettings();
        $connections = $this->configuredConnections();
        $default = (string) ($stored['default_storage'] ?? config('filemanager_v2.default_storage', 'local'));
        $enabledIds = collect($connections)
            ->filter(fn (array $connection): bool => ! empty($connection['enabled']))
            ->keys();

        if (! $enabledIds->contains($default)) {
            $default = (string) ($enabledIds->first() ?? 'local');
        }

        $upload = $this->uploadSettings();

        return [
            'defaultStorage' => $default,
            'connections' => collect($connections)
                ->map(fn (array $profile, string $id): array => $this->publicConnection($id, $profile))
                ->values()
                ->all(),
            'upload' => [
                'maxFileSize' => $upload['max_file_size'],
                'chunkSize' => $upload['chunk_size'],
                'chunkThreshold' => $upload['chunk_threshold'],
                'maxParallel' => $upload['max_parallel'],
                'retryAttempts' => $upload['retry_attempts'],
            ],
        ];
    }

    /** @param array<string, mixed> $settings @return array<string, mixed> */
    public function saveSettings(array $settings): array
    {
        $this->prepare();
        $existing = $this->configuredConnections();
        $connections = [];

        foreach ($settings['connections'] as $connection) {
            $id = (string) $connection['id'];
            abort_if(isset($connections[$id]), 422, 'ID storage connection tidak boleh duplikat.');
            $connections[$id] = $this->normalizeStoredConnection($id, $connection, $existing[$id] ?? []);
        }

        abort_unless(isset($connections['local']), 422, 'Local storage wajib tetap tersedia.');
        $default = (string) $settings['defaultStorage'];
        abort_if(empty($connections[$default]['enabled']), 422, 'Default storage harus aktif.');

        $removedConnections = [];
        foreach (config('filemanager_v2.connections', []) as $id => $profile) {
            $id = (string) $id;
            if (is_array($profile) && $id !== 'local' && ! isset($connections[$id])) {
                $removedConnections[] = $id;
            }
        }

        File::put($this->settingsPath(), json_encode([
            'version' => 1,
            'default_storage' => $default,
            'connections' => $connections,
            'removed_connections' => $removedConnections,
            'upload' => $this->normalizeUploadSettings($settings['upload'] ?? []),
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), LOCK_EX);

        foreach (array_unique([...array_keys($existing), ...array_keys($connections), ...$removedConnections]) as $id) {
            $this->bust($id, '');
        }

        return $this->settings();
    }

    /** @return array<string, mixed> */
    public function testConnection(string $storage): array
    {
        $profile = $this->configuredConnections()[$storage] ?? null;
        abort_unless(is_array($profile), 404, 'Storage connection tidak tersedia.');

        return [
            'storage' => $storage,
            'connected' => $this->connectionAvailable($storage, $profile),
        ];
    }
    /** @return array<string, mixed> */
    public function browse(string $storage, string|null $path, array $filters = []): array
    {
        $path = $this->path($path);
        $collection = (string) ($filters['collection'] ?? 'all');
        abort_unless(in_array($collection, ['all', 'recent', 'starred', 'shared'], true), 422, 'Koleksi tidak valid.');
        if ($collection !== 'all') {
            $path = '';
        }

        $this->prepare();
        $recursive = $collection !== 'all';
        $revision = (int) $this->cache()->get($this->revisionKey($storage, $path), 1);
        $cacheKey = 'filemanager_v2:browse:' . sha1(json_encode([
            $storage,
            $path,
            $filters,
            $recursive,
            $revision,
        ], JSON_THROW_ON_ERROR));

        return $this->cache()->remember($cacheKey, config('filemanager_v2.cache.listing_ttl_seconds'), function () use ($storage, $path, $filters, $collection, $recursive): array {
            $items = [];
            if ($collection === 'shared') {
                return ['path' => $path, 'items' => [], 'folders' => []];
            }

            try {
                foreach ($this->disk($storage)->listContents($path, $recursive) as $item) {
                    if (! $item instanceof StorageAttributes || ($collection !== 'all' && $item->isDir())) {
                        continue;
                    }

                    $items[] = $this->toAsset($storage, $item);
                }
            } catch (\Throwable $exception) {
                throw new RuntimeException('Folder tidak dapat dibaca: ' . $exception->getMessage(), previous: $exception);
            }

            $starred = array_flip($this->starredPaths($storage));
            $items = array_map(function (array $item) use ($starred): array {
                $item['starred'] = isset($starred[$item['path']]);

                return $item;
            }, $items);
            if ($collection === 'starred') {
                $items = array_values(array_filter($items, fn (array $item): bool => $item['starred']));
            }

            $query = mb_strtolower(trim((string) ($filters['search'] ?? '')));
            $type = (string) ($filters['type'] ?? 'all');
            $sort = (string) ($filters['sort'] ?? 'modified');

            $items = array_values(array_filter($items, function (array $item) use ($query, $type): bool {
                if ($query !== '' && ! str_contains(mb_strtolower($item['name']), $query)) {
                    return false;
                }

                return $type === 'all' || $item['kind'] === $type;
            }));

            usort($items, function (array $left, array $right) use ($sort): int {
                return match ($sort) {
                    'name' => strnatcasecmp($left['name'], $right['name']),
                    'size' => ($right['bytes'] ?? 0) <=> ($left['bytes'] ?? 0),
                    default => strcmp($right['modifiedAt'] ?? '', $left['modifiedAt'] ?? ''),
                };
            });

            return [
                'path' => $path,
                'items' => $items,
                'folders' => array_values(array_filter($items, fn (array $item) => $item['type'] === 'folder')),
            ];
        });
    }

    /** @return array<int, array<string, string>> */
    public function folders(string $storage): array
    {
        $this->prepare();
        $revision = (int) $this->cache()->get($this->revisionKey($storage, ''), 1);
        $cacheKey = 'filemanager_v2:folders:' . sha1(json_encode([$storage, $revision], JSON_THROW_ON_ERROR));

        return $this->cache()->remember($cacheKey, config('filemanager_v2.cache.listing_ttl_seconds'), function () use ($storage): array {
            $folders = [];

            foreach ($this->disk($storage)->listContents('', true) as $item) {
                if (! $item instanceof StorageAttributes || ! $item->isDir()) {
                    continue;
                }

                $path = $item->path();
                $folders[] = ['path' => $path, 'name' => basename($path)];
            }

            usort($folders, fn (array $left, array $right): int => strnatcasecmp($left['path'], $right['path']));

            return $folders;
        });
    }
    /** @return array<string, mixed> */
    public function folderDetails(string $storage, string|null $path): array
    {
        $path = $this->path($path);
        $disk = $this->disk($storage);
        abort_unless($path !== '' && $disk->directoryExists($path), 404, 'Folder tidak ditemukan.');

        $rootRevision = (int) $this->cache()->get($this->revisionKey($storage, ''), 1);
        $cacheKey = 'filemanager_v2:folder-details:' . sha1(json_encode([$storage, $path, $rootRevision], JSON_THROW_ON_ERROR));

        return $this->cache()->remember($cacheKey, config('filemanager_v2.cache.listing_ttl_seconds'), function () use ($storage, $path, $disk): array {
            $bytes = 0;
            $fileCount = 0;
            $folderCount = 0;

            foreach ($disk->listContents($path, true) as $item) {
                if (! $item instanceof StorageAttributes || $item->path() === $path) {
                    continue;
                }

                if ($item->isDir()) {
                    $folderCount++;
                    continue;
                }

                $fileCount++;
                $bytes += (int) ($item->fileSize() ?? 0);
            }

            return [
                ...$this->assetForPath($storage, $path),
                'bytes' => $bytes,
                'size' => $this->formatBytes($bytes),
                'fileCount' => $fileCount,
                'folderCount' => $folderCount,
            ];
        });
    }

    /** @return array<string, mixed> */
    public function rename(string $storage, string|null $path, string $name): array
    {
        $path = $this->path($path);
        abort_if($path === '', 422, 'Root storage tidak dapat diubah namanya.');

        $name = $this->fileName($name);
        $disk = $this->disk($storage);
        $isDirectory = $disk->directoryExists($path);
        abort_unless($isDirectory || $disk->fileExists($path), 404, 'Asset tidak ditemukan.');

        $parent = dirname($path) === '.' ? '' : dirname($path);
        $target = $parent === '' ? $name : $parent . '/' . $name;
        if ($target === $path) {
            return $this->assetForPath($storage, $path);
        }
        abort_if($disk->fileExists($target) || $disk->directoryExists($target), 422, 'Nama file atau folder sudah digunakan.');

        $disk->move($path, $target);

        $starred = array_map(function (string $starredPath) use ($path, $target, $isDirectory): string {
            if ($starredPath === $path) {
                return $target;
            }

            if ($isDirectory && str_starts_with($starredPath, $path . '/')) {
                return $target . substr($starredPath, strlen($path));
            }

            return $starredPath;
        }, $this->starredPaths($storage));
        $this->saveStarredPaths($storage, $starred);
        $this->bust($storage, $parent);

        return $this->assetForPath($storage, $target);
    }


    /** @param array<int, string> $paths @return array<string, mixed> */
    public function move(string $storage, array $paths, string|null $destination): array
    {
        $destination = $this->path($destination);
        $paths = array_values(array_unique(array_filter(array_map([$this, 'path'], $paths))));
        abort_if($paths === [], 422, 'Pilih setidaknya satu file untuk dipindahkan.');

        $disk = $this->disk($storage);
        $moves = [];
        foreach ($paths as $path) {
            abort_unless($disk->fileExists($path), 404, 'File yang dipindahkan tidak ditemukan.');
            $target = $destination === '' ? basename($path) : $destination . '/' . basename($path);
            abort_if($path === $target, 422, 'Folder tujuan sama dengan lokasi file.');
            abort_if($disk->fileExists($target) || $disk->directoryExists($target), 422, 'Nama file sudah digunakan di folder tujuan.');
            $moves[$path] = $target;
        }

        foreach ($moves as $source => $target) {
            $disk->move($source, $target);
            $this->bust($storage, dirname($source) === '.' ? '' : dirname($source));
        }
        $this->saveStarredPaths($storage, array_map(fn (string $path): string => $moves[$path] ?? $path, $this->starredPaths($storage)));
        $this->bust($storage, $destination);

        return ['items' => array_values(array_map(fn (string $path): array => $this->assetForPath($storage, $path), $moves))];
    }

    /** @return array<string, mixed> */
    public function toggleStar(string $storage, string|null $path): array
    {
        $path = $this->path($path);
        abort_unless($path !== '' && $this->disk($storage)->fileExists($path), 404, 'File tidak ditemukan.');

        $starred = $this->starredPaths($storage);
        $index = array_search($path, $starred, true);
        $isStarred = $index === false;
        if ($isStarred) {
            $starred[] = $path;
        } else {
            array_splice($starred, $index, 1);
        }

        $this->saveStarredPaths($storage, $starred);
        $this->bust($storage, '');

        return ['path' => $path, 'starred' => $isStarred];
    }


    /** @return array<string, mixed> */
    public function makeDirectory(string $storage, string|null $parent, string $name): array
    {
        $parent = $this->path($parent);
        $name = $this->fileName($name);
        $path = $parent === '' ? $name : $parent . '/' . $name;
        $disk = $this->disk($storage);

        if ($disk->directoryExists($path) || $disk->fileExists($path)) {
            abort(422, 'Nama folder sudah digunakan.');
        }

        $disk->makeDirectory($path);
        $this->bust($storage, $parent);

        return ['path' => $path, 'name' => $name];
    }

    /**
     * Reserve the capacity and materialize the folder tree for one folder-upload batch.
     *
     * @param array<int, string> $folders Paths relative to $parent.
     * @return array<string, int|string>
     */
    public function beginFolderUploadBatch(string $storage, string|null $parent, array $folders, int $totalBytes, int $fileCount): array
    {
        $this->prepare();
        $profile = $this->profile($storage);
        $parent = $this->path($parent);
        abort_if($totalBytes < 1 || $fileCount < 1, 422, 'Batch upload harus memiliki setidaknya satu file.');
        abort_if($fileCount > 100000 || count($folders) > 100000, 422, 'Batch upload terlalu besar.');

        $id = (string) Str::uuid();
        $expiresAt = now()->addHours((int) config('filemanager_v2.uploads.runtime_ttl_hours'));
        $this->reserveFolderUploadQuota($id, $storage, $profile, $totalBytes, $expiresAt->getTimestamp());

        try {
            $relativeFolders = collect($folders)
                ->filter(fn (mixed $folder): bool => is_string($folder))
                ->map(fn (string $folder): string => $this->path($folder))
                ->filter()
                ->unique()
                ->sortBy(fn (string $folder): int => substr_count($folder, '/'))
                ->values();

            $disk = $this->disk($storage);
            foreach ($relativeFolders as $folder) {
                $path = $this->joinPath($parent, $folder);
                abort_if($disk->fileExists($path), 422, 'Path folder berbenturan dengan file yang ada.');

                if (! $disk->directoryExists($path)) {
                    $disk->makeDirectory($path);
                }
            }

            $batch = [
                'id' => $id,
                'storage' => $storage,
                'path' => $parent,
                'totalBytes' => $totalBytes,
                'fileCount' => $fileCount,
                'claimedBytes' => 0,
                'expiresAt' => $expiresAt->getTimestamp(),
            ];
            $this->cache()->put($this->folderUploadBatchKey($id), $batch, $expiresAt);
            $this->bust($storage, $parent);

            return [
                'id' => $id,
                'storage' => $storage,
                'path' => $parent,
                'totalBytes' => $totalBytes,
                'fileCount' => $fileCount,
                'folderCount' => $relativeFolders->count(),
                'expiresAt' => $expiresAt->toIso8601String(),
            ];
        } catch (\Throwable $exception) {
            $this->releaseFolderUploadQuota($id, $storage);

            throw $exception;
        }
    }

    /** @return array<string, int|string> */
    public function completeFolderUploadBatch(string $id): array
    {
        $batch = $this->folderUploadBatch($id);
        $this->releaseFolderUploadQuota($id, (string) $batch['storage']);
        $this->cache()->forget($this->folderUploadBatchKey($id));

        return [
            'id' => $id,
            'storage' => $batch['storage'],
            'path' => $batch['path'],
            'claimedBytes' => (int) $batch['claimedBytes'],
        ];
    }

    /** @return array{asset?: array<string, mixed>, session?: array<string, mixed>}|null */
    private function existingIdempotentUpload(string $storage, string $parent, string $name, int $size, ?string $idempotencyKey): ?array
    {
        $key = $this->normalizedIdempotencyKey($idempotencyKey);
        if ($key === null) {
            return null;
        }

        $cacheKey = $this->uploadIdempotencyCacheKey($key);
        $record = $this->cache()->get($cacheKey);
        if (! is_array($record)) {
            return null;
        }
        abort_unless(
            ($record['storage'] ?? null) === $storage
                && ($record['parent'] ?? null) === $parent
                && ($record['name'] ?? null) === $name
                && (int) ($record['size'] ?? -1) === $size,
            409,
            'Kunci retry upload sudah digunakan untuk file lain.',
        );

        if (($record['state'] ?? null) === 'asset' && is_string($record['path'] ?? null) && $this->disk($storage)->fileExists($record['path'])) {
            return ['asset' => $this->assetForPath($storage, $record['path'])];
        }

        if (($record['state'] ?? null) === 'session' && is_string($record['sessionId'] ?? null)) {
            $sessionPath = $this->runtimeDirectory($record['sessionId']) . DIRECTORY_SEPARATOR . 'session.json';
            if (is_file($sessionPath)) {
                return ['session' => $this->session($record['sessionId'])];
            }
        }

        $this->cache()->forget($cacheKey);

        return null;
    }

    /** @param array<string, mixed> $asset */
    private function rememberIdempotentAsset(string $storage, string $parent, string $name, int $size, ?string $idempotencyKey, array $asset): void
    {
        $key = $this->normalizedIdempotencyKey($idempotencyKey);
        if ($key === null) {
            return;
        }

        $this->cache()->put($this->uploadIdempotencyCacheKey($key), [
            'state' => 'asset',
            'storage' => $storage,
            'parent' => $parent,
            'name' => $name,
            'size' => $size,
            'path' => $asset['path'],
        ], now()->addDay());
    }

    /** @param array<string, mixed> $session */
    private function rememberIdempotentSession(array $session): void
    {
        $key = $this->normalizedIdempotencyKey($session['idempotency_key'] ?? null);
        if ($key === null) {
            return;
        }

        $this->cache()->put($this->uploadIdempotencyCacheKey($key), [
            'state' => 'session',
            'storage' => $session['storage'],
            'parent' => $session['parent'],
            'name' => $session['name'],
            'size' => $session['size'],
            'sessionId' => $session['id'],
        ], now()->addDay());
    }

    private function forgetIdempotentUpload(?string $idempotencyKey): void
    {
        $key = $this->normalizedIdempotencyKey($idempotencyKey);
        if ($key !== null) {
            $this->cache()->forget($this->uploadIdempotencyCacheKey($key));
        }
    }

    private function normalizedIdempotencyKey(?string $idempotencyKey): ?string
    {
        $key = trim((string) $idempotencyKey);

        return $key === '' ? null : $key;
    }

    private function uploadIdempotencyCacheKey(string $idempotencyKey): string
    {
        return 'filemanager_v2:upload-idempotency:' . hash('sha256', $idempotencyKey);
    }

    public function upload(string $storage, string|null $parent, UploadedFile $file, ?string $batchId = null, ?string $idempotencyKey = null): array
    {
        $parent = $this->path($parent);
        $this->assertUpload($file);
        $size = (int) $file->getSize();
        $name = $this->fileName($file->getClientOriginalName());
        if ($existing = $this->existingIdempotentUpload($storage, $parent, $name, $size, $idempotencyKey)) {
            return $existing['asset'];
        }
        if ($batchId !== null) {
            $this->claimFolderUploadBytes($batchId, $storage, $parent, $size);
        } else {
            $this->assertQuota($storage, $size);
        }
        $availableName = $this->availableName($storage, $parent, $name);
        $path = $parent === '' ? $availableName : $parent . '/' . $availableName;

        $stream = fopen($file->getRealPath(), 'rb');
        try {
            $this->disk($storage)->writeStream($path, $stream);
        } catch (\Throwable $exception) {
            if ($batchId !== null) {
                $this->releaseFolderUploadBytes($batchId, $size);
            }

            throw $exception;
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        $this->bust($storage, $parent);

        $asset = $this->assetForPath($storage, $path);
        $this->rememberIdempotentAsset($storage, $parent, $name, $size, $idempotencyKey, $asset);

        return $asset;
    }

    /** @return array<string, mixed> */
    public function startUpload(string $storage, string|null $parent, string $name, int $size, int $parts, ?string $checksum = null, ?string $batchId = null, ?string $idempotencyKey = null): array
    {
        $this->prepare();
        $this->profile($storage);
        $parent = $this->path($parent);
        $name = $this->fileName($name);
        $this->assertUploadMeta($name, $size, $parts);
        if ($existing = $this->existingIdempotentUpload($storage, $parent, $name, $size, $idempotencyKey)) {
            return isset($existing['asset']) ? ['asset' => $existing['asset']] : $existing['session'];
        }
        if ($batchId !== null) {
            $this->claimFolderUploadBytes($batchId, $storage, $parent, $size);
        } else {
            $this->assertQuota($storage, $size);
        }
        $id = (string) Str::uuid();
        $directory = $this->runtimeDirectory($id);
        File::ensureDirectoryExists($directory, 0755, true);

        $session = [
            'id' => $id,
            'storage' => $storage,
            'parent' => $parent,
            'name' => $name,
            'batch_id' => $batchId,
            'size' => $size,
            'parts' => $parts,
            'checksum' => $checksum,
            'idempotency_key' => $this->normalizedIdempotencyKey($idempotencyKey),
            'received' => [],
            'created_at' => now()->toIso8601String(),
        ];
        $this->saveSession($session);
        $this->rememberIdempotentSession($session);

        return $session;
    }

    /** @return array<string, mixed> */
    public function storeChunk(string $id, int $part, UploadedFile $chunk): array
    {
        $session = $this->session($id);
        if ($part < 0 || $part >= $session['parts']) {
            abort(422, 'Nomor chunk tidak valid.');
        }
        abort_if((int) $chunk->getSize() > $this->uploadSettings()['chunk_size'], 422, 'Ukuran chunk melampaui batas upload.');

        $target = $this->runtimeDirectory($id) . DIRECTORY_SEPARATOR . sprintf('%08d.part', $part);
        $chunk->move(dirname($target), basename($target));
        $session['received'] = array_values(array_unique([...$session['received'], $part]));
        sort($session['received']);
        $this->saveSession($session);

        return [
            'id' => $id,
            'received' => count($session['received']),
            'parts' => $session['parts'],
        ];
    }

    /** @return array<string, mixed> */
    public function completeUpload(string $id): array
    {
        $session = $this->session($id);
        if (count($session['received']) !== $session['parts']) {
            abort(422, 'Masih ada chunk yang belum diupload.');
        }

        $combined = $this->runtimeDirectory($id) . DIRECTORY_SEPARATOR . 'completed.upload';
        $output = fopen($combined, 'wb');

        try {
            foreach (range(0, $session['parts'] - 1) as $part) {
                $source = fopen($this->runtimeDirectory($id) . DIRECTORY_SEPARATOR . sprintf('%08d.part', $part), 'rb');
                stream_copy_to_stream($source, $output);
                fclose($source);
            }
        } finally {
            fclose($output);
        }

        if ((int) filesize($combined) !== (int) $session['size']) {
            $this->deleteRuntime($id);
            abort(422, 'Ukuran file setelah digabungkan tidak cocok.');
        }

        if ($session['checksum'] && ! hash_equals(strtolower($session['checksum']), hash_file('sha256', $combined))) {
            $this->deleteRuntime($id);
            abort(422, 'Checksum file tidak cocok.');
        }

        $existing = $this->existingIdempotentUpload($session['storage'], $session['parent'], $session['name'], (int) $session['size'], $session['idempotency_key'] ?? null);
        if ($existing && isset($existing['asset'])) {
            $this->deleteRuntime($id);

            return $existing['asset'];
        }

        $name = $this->availableName($session['storage'], $session['parent'], $session['name']);
        $path = $session['parent'] === '' ? $name : $session['parent'] . '/' . $name;
        $stream = fopen($combined, 'rb');
        $this->disk($session['storage'])->writeStream($path, $stream);
        fclose($stream);
        $this->bust($session['storage'], $session['parent']);
        $asset = $this->assetForPath($session['storage'], $path);
        $this->rememberIdempotentAsset($session['storage'], $session['parent'], $session['name'], (int) $session['size'], $session['idempotency_key'] ?? null, $asset);
        $this->deleteRuntime($id);

        return $asset;
    }

    public function cancelUpload(string $id): void
    {
        $session = $this->session($id);
        if (is_string($session['batch_id'] ?? null) && $session['batch_id'] !== '') {
            $this->releaseFolderUploadBytes($session['batch_id'], (int) $session['size']);
        }
        $this->forgetIdempotentUpload($session['idempotency_key'] ?? null);

        $this->deleteRuntime($id);
    }

    public function pruneExpiredUploads(): int
    {
        $this->prepare();
        $root = config('filemanager_v2.local.runtime_root') . DIRECTORY_SEPARATOR . 'uploads';
        $expiresAt = now()->subHours((int) config('filemanager_v2.uploads.runtime_ttl_hours'));
        $deleted = 0;

        foreach (File::directories($root) as $directory) {
            $sessionPath = $directory . DIRECTORY_SEPARATOR . 'session.json';
            $modifiedAt = is_file($sessionPath) ? filemtime($sessionPath) : filemtime($directory);
            if ($modifiedAt === false || CarbonImmutable::createFromTimestamp($modifiedAt)->isAfter($expiresAt)) {
                continue;
            }

            File::deleteDirectory($directory);
            $deleted++;
        }

        return $deleted;
    }
    public function delete(string $storage, string|null $path): void
    {
        $path = $this->path($path);
        if ($path === '') {
            abort(422, 'Root storage tidak dapat dihapus.');
        }

        $disk = $this->disk($storage);
        if ($disk->directoryExists($path)) {
            $disk->deleteDirectory($path);
        } elseif ($disk->fileExists($path)) {
            $disk->delete($path);
        } else {
            abort(404, 'Asset tidak ditemukan.');
        }

        $starred = $this->starredPaths($storage);
        $remaining = array_values(array_filter($starred, fn (string $item): bool => $item !== $path && ! str_starts_with($item, $path . '/')));
        if (count($remaining) !== count($starred)) {
            $this->saveStarredPaths($storage, $remaining);
        }

        $this->bust($storage, dirname($path) === '.' ? '' : dirname($path));
    }
    /** @param array<int, string> $paths @return array<string, int|string|bool> */
    public function deletePreview(string $storage, array $paths): array
    {
        [$targets, $requestedCount] = $this->deletionTargets($paths);

        return $this->deletionSummary($storage, $this->disk($storage), $targets, $requestedCount);
    }

    /** @param array<int, string> $paths @return array<string, mixed> */
    public function deleteMany(string $storage, array $paths): array
    {
        [$targets, $requestedCount] = $this->deletionTargets($paths);
        $disk = $this->disk($storage);
        $preview = $this->deletionSummary($storage, $disk, $targets, $requestedCount);
        $deleted = [];
        $failed = [];

        foreach ($targets as $path) {
            try {
                if (! $this->deleteTarget($disk, $path)) {
                    throw new RuntimeException('Asset tidak dapat dihapus.');
                }

                $deleted[] = $path;
            } catch (\Throwable) {
                $failed[] = $path;
            }
        }

        if ($deleted !== []) {
            $starred = $this->starredPaths($storage);
            $remaining = array_values(array_filter($starred, function (string $starredPath) use ($deleted): bool {
                foreach ($deleted as $deletedPath) {
                    if ($starredPath === $deletedPath || str_starts_with($starredPath, $deletedPath . '/')) {
                        return false;
                    }
                }

                return true;
            }));
            if (count($remaining) !== count($starred)) {
                $this->saveStarredPaths($storage, $remaining);
            }

            foreach ($deleted as $path) {
                $this->bust($storage, dirname($path) === '.' ? '' : dirname($path));
            }
        }

        return [
            'deletedCount' => count($deleted),
            'failedCount' => count($failed),
            'failedPaths' => $failed,
            'preview' => $preview,
        ];
    }

    public function responseForDownload(string $storage, string|null $path)
    {
        $path = $this->path($path);
        $disk = $this->disk($storage);
        abort_unless($path !== '' && $disk->fileExists($path), 404);

        $stream = $disk->readStream($path);
        abort_unless(is_resource($stream), 404);

        return response()->streamDownload(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, basename($path), ['Content-Type' => $disk->mimeType($path) ?? 'application/octet-stream']);
    }

    public function responseForPreview(string $storage, string|null $path, int $width = 360)
    {
        $path = $this->path($path);
        $disk = $this->disk($storage);
        abort_unless($path !== '' && $disk->fileExists($path), 404);

        $mime = $disk->mimeType($path) ?? 'application/octet-stream';
        abort_unless(str_starts_with($mime, 'image/'), 404);
        $modified = (string) ($disk->lastModified($path) ?? 0);
        $hash = hash('sha256', implode('|', [$storage, $path, $modified, $width]));
        $cache = config('filemanager_v2.local.cache_root') . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . $hash . '.jpg';
        $etag = '"' . $hash . '"';

        if (request()->header('If-None-Match') === $etag) {
            return response('', 304)->withHeaders($this->previewHeaders($etag, is_file($cache) ? 'image/jpeg' : $mime));
        }

        if (is_file($cache)) {
            return response()->file($cache, $this->previewHeaders($etag));
        }

        $binary = $disk->get($path);
        $thumbnail = $this->makeThumbnail($binary, $width);
        if ($thumbnail !== null) {
            File::put($cache, $thumbnail);

            return response($thumbnail, 200, $this->previewHeaders($etag));
        }

        return response($binary, 200, $this->previewHeaders($etag, $mime));
    }

    private function toAsset(string $storage, StorageAttributes $item): array
    {
        $isDirectory = $item->isDir();
        $path = $item->path();
        $mime = $isDirectory ? null : ($item->mimeType() ?? $this->mimeFromName($path));
        $modified = $item->lastModified();
        $kind = $isDirectory ? 'folder' : $this->kind($mime);

        return [
            'id' => $storage . ':' . sha1($path),
            'storage' => $storage,
            'type' => $isDirectory ? 'folder' : 'file',
            'path' => $path,
            'name' => basename($path),
            'folder' => dirname($path) === '.' ? '' : dirname($path),
            'bytes' => $isDirectory ? 0 : ($item->fileSize() ?? 0),
            'size' => $isDirectory ? '—' : $this->formatBytes((int) ($item->fileSize() ?? 0)),
            'mime' => $mime,
            'kind' => $kind,
            'extension' => strtoupper(pathinfo($path, PATHINFO_EXTENSION)),
            'modifiedAt' => $modified ? CarbonImmutable::createFromTimestamp($modified)->toIso8601String() : null,
            'modifiedLabel' => $modified ? CarbonImmutable::createFromTimestamp($modified)->diffForHumans() : '—',
            'previewUrl' => $isDirectory || ! str_starts_with((string) $mime, 'image/') ? null : route('filemanager_v2.assets.preview', ['storage' => $storage, 'path' => $path]),
            'downloadUrl' => $isDirectory ? null : route('filemanager_v2.assets.download', ['storage' => $storage, 'path' => $path]),
        ];
    }

    /** @return array<string, mixed> */
    private function assetForPath(string $storage, string $path): array
    {
        foreach ($this->disk($storage)->listContents(dirname($path) === '.' ? '' : dirname($path), false) as $item) {
            if ($item instanceof StorageAttributes && $item->path() === $path) {
                return $this->toAsset($storage, $item);
            }
        }

        throw new RuntimeException('Asset hasil upload tidak dapat ditemukan.');
    }
    /** @param array<int, string> $paths @return array{0: array<int, string>, 1: int} */
    private function deletionTargets(array $paths): array
    {
        $paths = array_values(array_unique(array_map([$this, 'path'], $paths)));
        abort_if($paths === [], 422, 'Pilih setidaknya satu asset untuk dihapus.');
        abort_if(in_array('', $paths, true), 422, 'Root storage tidak dapat dihapus.');

        usort($paths, static function (string $left, string $right): int {
            $depth = substr_count($left, '/') <=> substr_count($right, '/');

            return $depth !== 0 ? $depth : strnatcasecmp($left, $right);
        });

        $targets = [];
        foreach ($paths as $path) {
            foreach ($targets as $target) {
                if (str_starts_with($path, $target . '/')) {
                    continue 2;
                }
            }

            $targets[] = $path;
        }

        return [$targets, count($paths)];
    }

    /** @param array<int, string> $targets @return array<string, int|string|bool> */
    private function deletionSummary(string $storage, FilesystemAdapter $disk, array $targets, int $requestedCount): array
    {
        $selectedFileCount = 0;
        $selectedFolderCount = 0;
        $fileCount = 0;
        $folderCount = 0;
        $bytes = 0;

        foreach ($targets as $path) {
            if ($disk->directoryExists($path)) {
                $selectedFolderCount++;
                $folderCount++;

                foreach ($disk->listContents($path, true) as $item) {
                    if (! $item instanceof StorageAttributes || $item->path() === $path) {
                        continue;
                    }

                    if ($item->isDir()) {
                        $folderCount++;
                        continue;
                    }

                    $fileCount++;
                    $bytes += (int) ($item->fileSize() ?? 0);
                }

                continue;
            }

            if ($disk->fileExists($path)) {
                $selectedFileCount++;
                $fileCount++;
                $bytes += (int) ($disk->size($path) ?? 0);
                continue;
            }

            abort(404, 'Asset tidak ditemukan.');
        }

        return [
            'requestedCount' => $requestedCount,
            'targetCount' => count($targets),
            'includedByFolderCount' => $requestedCount - count($targets),
            'selectedFileCount' => $selectedFileCount,
            'selectedFolderCount' => $selectedFolderCount,
            'fileCount' => $fileCount,
            'folderCount' => $folderCount,
            'nestedFileCount' => $fileCount - $selectedFileCount,
            'nestedFolderCount' => $folderCount - $selectedFolderCount,
            'bytes' => $bytes,
            'size' => $this->formatBytes($bytes),
            'hasFolderContents' => ($fileCount - $selectedFileCount) > 0 || ($folderCount - $selectedFolderCount) > 0,
        ];
    }

    private function deleteTarget(FilesystemAdapter $disk, string $path): bool
    {
        if ($disk->directoryExists($path)) {
            return $disk->deleteDirectory($path);
        }

        if ($disk->fileExists($path)) {
            return $disk->delete($path);
        }

        throw new RuntimeException('Asset tidak ditemukan.');
    }

    /** @param array<string, mixed>|null $profile */
    private function connectionAvailable(string $storage, ?array $profile = null): bool
    {
        try {
            $disk = $profile === null ? $this->disk($storage) : $this->diskForProfile($profile);
            foreach ($disk->listContents('', false) as $_item) {
                break;
            }

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
    /** @return array<string, array<string, mixed>> */
    private function configuredConnections(): array
    {
        $storedSettings = $this->storedSettings();
        $removedConnections = array_fill_keys(array_filter(
            $storedSettings['removed_connections'] ?? [],
            static fn ($id): bool => is_string($id) && $id !== 'local',
        ), true);
        $connections = [];

        foreach (config('filemanager_v2.connections', []) as $id => $profile) {
            $id = (string) $id;
            if (is_array($profile) && ! isset($removedConnections[$id])) {
                $connections[$id] = $this->normalizeConnectionProfile($id, $profile);
            }
        }

        foreach (($storedSettings['connections'] ?? []) as $id => $profile) {
            if (is_string($id) && is_array($profile) && ! isset($removedConnections[$id])) {
                $connections[$id] = $this->normalizeConnectionProfile($id, [...($connections[$id] ?? []), ...$profile]);
            }
        }

        return $connections;
    }

    /** @param array<string, mixed> $profile @return array<string, mixed> */
    private function normalizeConnectionProfile(string $id, array $profile): array
    {
        $type = (string) ($profile['type'] ?? ($profile['driver'] === 'local' ? 'local' : ($id === 'r2' ? 'r2' : 's3_compatible')));
        $type = in_array($type, ['local', 's3', 's3_compatible', 'r2'], true) ? $type : 's3_compatible';
        $root = $this->path((string) ($profile['root'] ?? ''));

        return [
            'name' => trim((string) ($profile['name'] ?? $this->defaultConnectionName($type))) ?: $this->defaultConnectionName($type),
            'short_name' => trim((string) ($profile['short_name'] ?? $this->defaultConnectionShortName($type))) ?: $this->defaultConnectionShortName($type),
            'icon' => (string) ($profile['icon'] ?? $this->defaultConnectionIcon($type)),
            'enabled' => (bool) ($profile['enabled'] ?? true),
            'type' => $type,
            'driver' => $type === 'local' ? 'local' : (string) ($profile['driver'] ?? 's3'),
            'disk' => $profile['disk'] ?? null,
            'root' => $root,
            'quota_bytes' => max(0, (int) ($profile['quota_bytes'] ?? 1073741824)),
            'bucket' => trim((string) ($profile['bucket'] ?? '')),
            'region' => trim((string) ($profile['region'] ?? ($type === 'r2' ? 'auto' : 'us-east-1'))),
            'endpoint' => trim((string) ($profile['endpoint'] ?? '')),
            'use_path_style_endpoint' => (bool) ($profile['use_path_style_endpoint'] ?? ($type === 's3_compatible')),
            'key_encrypted' => $profile['key_encrypted'] ?? null,
            'secret_encrypted' => $profile['secret_encrypted'] ?? null,
        ];
    }

    /** @param array<string, mixed> $profile @return array<string, mixed> */
    private function publicConnection(string $id, array $profile): array
    {
        return [
            'id' => $id,
            'name' => $profile['name'],
            'shortName' => $profile['short_name'],
            'icon' => $profile['icon'],
            'type' => $profile['type'],
            'enabled' => (bool) $profile['enabled'],
            'root' => $profile['root'],
            'quotaBytes' => (int) $profile['quota_bytes'],
            'bucket' => $profile['bucket'],
            'region' => $profile['region'],
            'endpoint' => $profile['endpoint'],
            'usePathStyle' => (bool) $profile['use_path_style_endpoint'],
            'credentialsConfigured' => ($profile['driver'] ?? null) === 'disk' || (! empty($profile['key_encrypted']) && ! empty($profile['secret_encrypted'])),
        ];
    }

    /** @param array<string, mixed> $input @param array<string, mixed> $existing @return array<string, mixed> */
    private function normalizeStoredConnection(string $id, array $input, array $existing): array
    {
        abort_unless((bool) preg_match('/^[a-z][a-z0-9-]{0,38}$/', $id), 422, 'ID storage connection tidak valid.');
        $type = (string) ($input['type'] ?? '');
        abort_unless(in_array($type, ['local', 's3', 's3_compatible', 'r2'], true), 422, 'Tipe storage connection tidak valid.');
        abort_if($id === 'local' && $type !== 'local', 422, 'Local storage tidak dapat diubah menjadi cloud storage.');
        abort_if($id !== 'local' && $type === 'local', 422, 'Hanya connection local yang dapat memakai driver local.');

        $accessKey = trim((string) ($input['accessKey'] ?? ''));
        $secretKey = trim((string) ($input['secretKey'] ?? ''));
        $usesDynamicS3 = $type !== 'local' && ($accessKey !== '' || $secretKey !== '' || ($existing['driver'] ?? null) === 's3');

        return $this->normalizeConnectionProfile($id, [
            ...$existing,
            'name' => $input['name'] ?? $existing['name'] ?? $this->defaultConnectionName($type),
            'short_name' => $input['shortName'] ?? $existing['short_name'] ?? $this->defaultConnectionShortName($type),
            'icon' => $this->defaultConnectionIcon($type),
            'enabled' => $input['enabled'] ?? $existing['enabled'] ?? true,
            'type' => $type,
            'driver' => $type === 'local' ? 'local' : ($usesDynamicS3 ? 's3' : ($existing['driver'] ?? 's3')),
            'root' => $input['root'] ?? $existing['root'] ?? '',
            'quota_bytes' => $input['quotaBytes'] ?? $existing['quota_bytes'] ?? 1073741824,
            'bucket' => $input['bucket'] ?? $existing['bucket'] ?? '',
            'region' => $input['region'] ?? $existing['region'] ?? ($type === 'r2' ? 'auto' : 'us-east-1'),
            'endpoint' => $input['endpoint'] ?? $existing['endpoint'] ?? '',
            'use_path_style_endpoint' => $input['usePathStyle'] ?? $existing['use_path_style_endpoint'] ?? ($type === 's3_compatible'),
            'key_encrypted' => $this->encryptSecret($accessKey, $existing['key_encrypted'] ?? null),
            'secret_encrypted' => $this->encryptSecret($secretKey, $existing['secret_encrypted'] ?? null),
        ]);
    }

    /** @return array<string, mixed> */
    private function storedSettings(): array
    {
        $path = $this->settingsPath();
        if (! File::exists($path)) {
            return [];
        }

        try {
            $settings = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }

        return is_array($settings) ? $settings : [];
    }

    private function settingsPath(): string
    {
        return config('filemanager_v2.settings.root') . DIRECTORY_SEPARATOR . 'connections.json';
    }

    /** @return array<string, int> */
    private function uploadSettings(): array
    {
        $defaults = config('filemanager_v2.uploads');
        $stored = $this->storedSettings()['upload'] ?? [];
        $chunkSize = max(1, (int) ($stored['chunk_size'] ?? $defaults['chunk_size']));

        return [
            'max_file_size' => max(1, (int) ($stored['max_file_size'] ?? $defaults['max_file_size'])),
            'chunk_size' => $chunkSize,
            'chunk_threshold' => max($chunkSize, (int) ($stored['chunk_threshold'] ?? $defaults['chunk_threshold'])),
            'max_parallel' => min(10, max(1, (int) ($stored['max_parallel'] ?? $defaults['max_parallel']))),
            'retry_attempts' => min(5, max(0, (int) ($stored['retry_attempts'] ?? $defaults['retry_attempts'] ?? 2))),
        ];
    }

    /** @param array<string, mixed> $input @return array<string, int> */
    private function normalizeUploadSettings(array $input): array
    {
        $defaults = $this->uploadSettings();
        $chunkSize = max(1, (int) ($input['chunkSize'] ?? $defaults['chunk_size']));

        return [
            'max_file_size' => max(1, (int) ($input['maxFileSize'] ?? $defaults['max_file_size'])),
            'chunk_size' => $chunkSize,
            'chunk_threshold' => max($chunkSize, (int) ($input['chunkThreshold'] ?? $defaults['chunk_threshold'])),
            'max_parallel' => min(10, max(1, (int) ($input['maxParallel'] ?? $defaults['max_parallel']))),
            'retry_attempts' => min(5, max(0, (int) ($input['retryAttempts'] ?? $defaults['retry_attempts']))),
        ];
    }

    private function localRoot(array $profile): string
    {
        $base = config('filemanager_v2.local.files_root');
        $root = $this->path((string) ($profile['root'] ?? ''));
        $resolved = $root === '' ? $base : $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $root);
        File::ensureDirectoryExists($resolved, 0755, true);

        return $resolved;
    }

    private function encryptSecret(string $secret, mixed $existing): mixed
    {
        return $secret === '' ? $existing : Crypt::encryptString($secret);
    }

    private function decryptSecret(mixed $encrypted): string
    {
        if (! is_string($encrypted) || $encrypted === '') {
            return '';
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable) {
            return '';
        }
    }

    private function defaultConnectionName(string $type): string
    {
        return match ($type) {
            'local' => 'Local storage',
            's3' => 'AWS S3',
            'r2' => 'Cloudflare R2',
            default => 'S3-compatible storage',
        };
    }

    private function defaultConnectionShortName(string $type): string
    {
        return match ($type) {
            'local' => 'LOCAL',
            's3' => 'AWS S3',
            'r2' => 'R2',
            default => 'S3 API',
        };
    }

    private function defaultConnectionIcon(string $type): string
    {
        return $type === 'local' ? 'bi-device-hdd' : 'bi-cloud';
    }

    private function joinPath(string $parent, string $child): string
    {
        return $parent === '' ? $child : ($child === '' ? $parent : $parent . '/' . $child);
    }

    private function folderUploadBatchKey(string $id): string
    {
        abort_unless((bool) preg_match('/^[0-9a-f-]{36}$/i', $id), 404, 'Batch upload tidak ditemukan.');

        return 'filemanager_v2:folder-upload-batch:' . $id;
    }

    private function folderUploadReservationsKey(string $storage): string
    {
        return 'filemanager_v2:folder-upload-reservations:' . $storage;
    }

    /** @return array<string, mixed> */
    private function folderUploadBatch(string $id): array
    {
        $batch = $this->cache()->get($this->folderUploadBatchKey($id));
        abort_unless(is_array($batch), 410, 'Batch upload telah berakhir atau kedaluwarsa.');
        abort_if((int) ($batch['expiresAt'] ?? 0) <= now()->getTimestamp(), 410, 'Batch upload telah kedaluwarsa.');

        return $batch;
    }

    /** @param array<string, mixed> $profile */
    private function reserveFolderUploadQuota(string $id, string $storage, array $profile, int $bytes, int $expiresAt): void
    {
        $this->cache()->lock('filemanager_v2:folder-upload-reservations-lock:' . $storage, 10)->block(5, function () use ($id, $storage, $profile, $bytes, $expiresAt): void {
            $key = $this->folderUploadReservationsKey($storage);
            $reservations = array_filter(
                (array) $this->cache()->get($key, []),
                fn (mixed $reservation): bool => is_array($reservation) && (int) ($reservation['expiresAt'] ?? 0) > now()->getTimestamp(),
            );
            $quota = (int) ($profile['quota_bytes'] ?? 0);
            if ($quota > 0) {
                $used = (int) $this->usage($storage, $profile)['usedBytes'];
                $reserved = array_sum(array_map(fn (array $reservation): int => (int) ($reservation['bytes'] ?? 0), $reservations));
                abort_if($used + $reserved + $bytes > $quota, 422, 'Storage connection sudah mencapai batas kapasitasnya.');
            }

            $reservations[$id] = ['bytes' => $bytes, 'expiresAt' => $expiresAt];
            $this->cache()->put($key, $reservations, now()->addSeconds(max(1, $expiresAt - now()->getTimestamp())));
        });
    }

    private function releaseFolderUploadQuota(string $id, string $storage): void
    {
        $this->cache()->lock('filemanager_v2:folder-upload-reservations-lock:' . $storage, 10)->block(5, function () use ($id, $storage): void {
            $key = $this->folderUploadReservationsKey($storage);
            $reservations = (array) $this->cache()->get($key, []);
            unset($reservations[$id]);

            if ($reservations === []) {
                $this->cache()->forget($key);

                return;
            }

            $latestExpiry = max(array_map(fn (mixed $reservation): int => is_array($reservation) ? (int) ($reservation['expiresAt'] ?? 0) : 0, $reservations));
            $this->cache()->put($key, $reservations, now()->addSeconds(max(1, $latestExpiry - now()->getTimestamp())));
        });
    }

    private function claimFolderUploadBytes(string $id, string $storage, string $parent, int $bytes): void
    {
        abort_if($bytes < 1, 422, 'Ukuran file upload tidak valid.');
        $this->cache()->lock('filemanager_v2:folder-upload-batch-lock:' . $id, 10)->block(5, function () use ($id, $storage, $parent, $bytes): void {
            $batch = $this->folderUploadBatch($id);
            abort_unless($batch['storage'] === $storage, 422, 'Batch upload tidak sesuai dengan storage aktif.');
            $batchPath = (string) $batch['path'];
            abort_unless($batchPath === '' || $parent === $batchPath || str_starts_with($parent, $batchPath . '/'), 422, 'Path upload berada di luar batch folder.');
            abort_if((int) $batch['claimedBytes'] + $bytes > (int) $batch['totalBytes'], 422, 'Ukuran file melebihi reservasi batch upload.');

            $batch['claimedBytes'] = (int) $batch['claimedBytes'] + $bytes;
            $this->cache()->put($this->folderUploadBatchKey($id), $batch, now()->addSeconds(max(1, (int) $batch['expiresAt'] - now()->getTimestamp())));
        });
    }

    private function releaseFolderUploadBytes(string $id, int $bytes): void
    {
        $this->cache()->lock('filemanager_v2:folder-upload-batch-lock:' . $id, 10)->block(5, function () use ($id, $bytes): void {
            $batch = $this->cache()->get($this->folderUploadBatchKey($id));
            if (! is_array($batch)) {
                return;
            }

            $batch['claimedBytes'] = max(0, (int) ($batch['claimedBytes'] ?? 0) - $bytes);
            $this->cache()->put($this->folderUploadBatchKey($id), $batch, now()->addSeconds(max(1, (int) ($batch['expiresAt'] ?? now()->getTimestamp()) - now()->getTimestamp())));
        });
    }
    private function assertQuota(string $storage, int $size): void
    {
        $profile = $this->profile($storage);
        $quota = (int) $profile['quota_bytes'];
        if ($quota === 0) {
            return;
        }

        $used = $this->usage($storage, $profile)['usedBytes'];
        abort_if($used + $size > $quota, 422, 'Storage connection sudah mencapai batas kapasitasnya.');
    }

    private function metadataPath(string $storage): string
    {
        return config('filemanager_v2.local.metadata_root') . DIRECTORY_SEPARATOR . $storage . '-library.json';
    }

    /** @return array<int, string> */
    private function starredPaths(string $storage): array
    {
        $path = $this->metadataPath($storage);
        if (! File::exists($path)) {
            return [];
        }

        try {
            $metadata = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }

        return array_values(array_unique(array_filter($metadata['starred'] ?? [], fn (mixed $value): bool => is_string($value) && $value !== '')));
    }

    /** @param array<int, string> $paths */
    private function saveStarredPaths(string $storage, array $paths): void
    {
        $this->prepare();
        File::put(
            $this->metadataPath($storage),
            json_encode(['starred' => array_values(array_unique($paths))], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            LOCK_EX,
        );
    }


    private function cache(): Repository
    {
        return Cache::store(config('filemanager_v2.cache.store', 'file'));
    }

    private function revisionKey(string $storage, string $path): string
    {
        return 'filemanager_v2:revision:' . $storage . ':' . sha1($path);
    }

    private function bust(string $storage, string $path): void
    {
        foreach (array_unique([$path, dirname($path) === '.' ? '' : dirname($path), '']) as $affected) {
            $key = $this->revisionKey($storage, $affected);
            $this->cache()->forever($key, (int) $this->cache()->get($key, 1) + 1);
        }
    }

    private function fileName(string $name): string
    {
        $name = trim(str_replace(['/', '\\', "\0"], '-', $name));
        abort_if($name === '' || $name === '.' || $name === '..', 422, 'Nama file tidak valid.');

        return Str::limit($name, 180, '');
    }

    private function availableName(string $storage, string $parent, string $name): string
    {
        $disk = $this->disk($storage);
        $candidate = $name;
        $base = pathinfo($name, PATHINFO_FILENAME);
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $counter = 1;

        while ($disk->fileExists($parent === '' ? $candidate : $parent . '/' . $candidate)) {
            $suffix = ' (' . $counter++ . ')';
            $candidate = $base . $suffix . ($extension === '' ? '' : '.' . $extension);
        }

        return $candidate;
    }

    private function assertUpload(UploadedFile $file): void
    {
        abort_unless($file->isValid(), 422, 'File upload tidak valid.');
        $this->assertUploadMeta($file->getClientOriginalName(), (int) $file->getSize(), 1);
    }

    private function assertUploadMeta(string $name, int $size, int $parts): void
    {
        $upload = $this->uploadSettings();
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        abort_if(in_array($extension, config('filemanager_v2.uploads.forbidden_extensions'), true), 422, 'Ekstensi file tidak diizinkan.');
        abort_if($size < 1 || $size > $upload['max_file_size'], 422, 'Ukuran file melampaui batas upload.');
        abort_if($parts < 1 || $parts > 10000, 422, 'Jumlah chunk tidak valid.');
    }

    private function runtimeDirectory(string $id): string
    {
        abort_unless((bool) preg_match('/^[a-f0-9-]{36}$/i', $id), 404);

        return config('filemanager_v2.local.runtime_root') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $id;
    }

    /** @return array<string, mixed> */
    private function session(string $id): array
    {
        $path = $this->runtimeDirectory($id) . DIRECTORY_SEPARATOR . 'session.json';
        abort_unless(is_file($path), 404, 'Upload session tidak ditemukan.');
        $session = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);
        abort_if(now()->diffInHours(CarbonImmutable::parse($session['created_at'])) > config('filemanager_v2.uploads.runtime_ttl_hours'), 410, 'Upload session sudah kedaluwarsa.');

        return $session;
    }

    /** @param array<string, mixed> $session */
    private function saveSession(array $session): void
    {
        File::put($this->runtimeDirectory($session['id']) . DIRECTORY_SEPARATOR . 'session.json', json_encode($session, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }

    private function deleteRuntime(string $id): void
    {
        File::deleteDirectory($this->runtimeDirectory($id));
    }

    private function kind(?string $mime): string
    {
        return match (true) {
            str_starts_with((string) $mime, 'image/') => 'image',
            str_starts_with((string) $mime, 'video/') => 'video',
            str_starts_with((string) $mime, 'audio/') => 'audio',
            default => 'document',
        };
    }

    private function mimeFromName(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'mp4', 'mov', 'webm' => 'video/mp4',
            'mp3', 'wav', 'ogg' => 'audio/mpeg',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }

    private function makeThumbnail(string $binary, int $width): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $source = @imagecreatefromstring($binary);
        if (! $source) {
            return null;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $height = max(1, (int) round($sourceHeight * min(1, $width / max(1, $sourceWidth))));
        $target = imagecreatetruecolor(min($sourceWidth, $width), $height);
        imagecopyresampled($target, $source, 0, 0, 0, 0, imagesx($target), imagesy($target), $sourceWidth, $sourceHeight);
        ob_start();
        imagejpeg($target, null, 82);
        $thumbnail = ob_get_clean();
        imagedestroy($source);
        imagedestroy($target);

        return $thumbnail === false ? null : $thumbnail;
    }

    /** @return array<string, string> */
    private function previewHeaders(string $etag, string $contentType = 'image/jpeg'): array
    {
        return [
            'Content-Type' => $contentType,
            'Cache-Control' => 'private, max-age=' . config('filemanager_v2.cache.preview_ttl_seconds'),
            'ETag' => $etag,
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $index = min((int) floor(log($bytes, 1024)) - 1, count($units) - 1);

        return number_format($bytes / (1024 ** ($index + 1)), $index > 0 ? 1 : 0) . ' ' . $units[$index];
    }
}
