<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\FileManagerBulkProgress;
use App\Http\Controllers\Controller;
use Aws\S3\S3Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * FileManagerPermissionController
 *
 * Mengelola permission (S3 ACL) dan metadata per file/folder.
 *
 * Keputusan teknis (sesuai memori):
 * ─────────────────────────────────────────────────────────────────────
 * 1. ACL = S3 ACL (public-read, private, authenticated-read, dll)
 * 2. Cloudflare R2 TIDAK support per-object ACL → fitur ACL di-disable
 *    via config acl_supported_disks. Frontend dapat notifikasi.
 * 3. S3 metadata update = server-side copy (CopyObject + MetadataDirective: REPLACE)
 *    Tidak perlu download file — hemat bandwidth.
 * 4. Bulk update: concurrency 1-100 (setting dari request)
 * 5. Progress bar: broadcast via Laravel Reverb App 2 (File Manager)
 *    Frontend listen via Pusher.js CDN (bukan laravel-echo)
 * ─────────────────────────────────────────────────────────────────────
 */
class FileManagerPermissionController extends Controller
{
    // ── Helpers (diambil dari FileManagerController pattern) ──

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

    /**
     * Cek apakah disk mendukung per-object ACL.
     * Cloudflare R2 tidak support → return false.
     */
    protected function diskSupportsAcl(string $disk): bool
    {
        $aclDisks = config('filemanager.acl_supported_disks', ['s3', 's3_aws', 's3_do']);
        return in_array($disk, $aclDisks);
    }

    /**
     * Ambil S3Client dari disk yang aktif.
     * Flysystem tidak expose S3Client langsung, jadi kita buat manual dari config disk.
     */
    protected function getS3Client(string $disk): S3Client
    {
        $cfg = config("filesystems.disks.{$disk}");

        $args = [
            'version'     => 'latest',
            'region'      => $cfg['region']   ?? 'us-east-1',
            'credentials' => [
                'key'    => $cfg['key'],
                'secret' => $cfg['secret'],
            ],
            'http' => [
                'connect_timeout' => 5,   // max 5 detik untuk connect
                'timeout'         => 10,  // max 10 detik total per request
            ],
        ];

        if (!empty($cfg['endpoint'])) {
            $args['endpoint']                = $cfg['endpoint'];
            $args['use_path_style_endpoint'] = $cfg['use_path_style_endpoint'] ?? true;
        }

        return new S3Client($args);
    }

    /**
     * Resolve storage path relatif terhadap bucket (tanpa prefix root_path jika disk S3).
     * Untuk S3, path yang dikirim ke AWS adalah path di dalam bucket langsung.
     */
    protected function getBucketAndKey(string $disk, string $storagePath): array
    {
        $bucket = config("filesystems.disks.{$disk}.bucket");
        return [$bucket, $storagePath];
    }

    // ══════════════════════════════════════════════════════
    //  PERMISSION (ACL)
    // ══════════════════════════════════════════════════════

    /**
     * GET /filemanager/permission?disk=s3&path=folder/file.jpg
     *
     * Ambil ACL saat ini dari sebuah file.
     * Jika disk tidak support ACL → return info dengan acl_supported: false.
     */
    public function getPermission(Request $request): JsonResponse
    {
        $disk        = $this->getDisk($request);
        $userPath    = $this->safePath($request->query('path', ''));
        $storagePath = $this->toStoragePath($userPath);

        // Cek apakah disk support ACL
        if (!$this->diskSupportsAcl($disk)) {
            $label = config("filemanager.available_disks.{$disk}", $disk);
            return $this->ok([
                'acl_supported' => false,
                'disk'          => $disk,
                'disk_label'    => $label,
                'message'       => "{$label} tidak mendukung per-object ACL. Akses file dikontrol melalui pengaturan bucket.",
            ]);
        }

        // Shortcut: jika path __disk_check__, hanya return acl_supported tanpa fetch object
        // Digunakan frontend untuk cek support ACL pada folder tanpa perlu file spesifik
        if ($userPath === '__disk_check__' || $storagePath === '__disk_check__') {
            return $this->ok([
                'acl_supported' => true,
                'disk'          => $disk,
            ]);
        }

        try {
            $s3     = $this->getS3Client($disk);
            [$bucket, $key] = $this->getBucketAndKey($disk, $storagePath);

            $result = $s3->getObjectAcl([
                'Bucket' => $bucket,
                'Key'    => $key,
            ]);

            // Parse grants menjadi ACL sederhana
            $acl = $this->parseAclFromGrants($result['Grants'] ?? []);

            return $this->ok([
                'acl_supported' => true,
                'disk'          => $disk,
                'path'          => $userPath,
                'acl'           => $acl,
                'grants'        => $result['Grants'] ?? [],
                'owner'         => $result['Owner']  ?? null,
            ]);

        } catch (\Aws\S3\Exception\S3Exception $e) {
            $code = $e->getAwsErrorCode();
            // Jika endpoint tidak support ACL (NotImplemented / MethodNotAllowed),
            // fallback ke acl_supported: false agar UI tetap bisa dibuka
            if (in_array($code, ['NotImplemented', 'MethodNotAllowed', 'AccessControlListNotSupported'])) {
                return $this->ok([
                    'acl_supported' => false,
                    'disk'          => $disk,
                    'message'       => 'Disk ini tidak mendukung per-object ACL (' . $code . ').',
                ]);
            }
            return $this->fail('Gagal membaca ACL: ' . $e->getAwsErrorMessage(), 500);
        } catch (\Throwable $e) {
            return $this->fail('Gagal membaca ACL: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /filemanager/permission
     *
     * Update ACL untuk satu file atau semua file dalam satu folder (non-recursive).
     *
     * Body:
     *   disk, path, acl, type (file|folder), recursive (bool)
     *
     * ACL values: public-read | private | authenticated-read | public-read-write
     */
    public function updatePermission(Request $request): JsonResponse
    {
        $disk        = $this->getDisk($request);
        $userPath    = $this->safePath($request->input('path', ''));
        $storagePath = $this->toStoragePath($userPath);
        $acl         = $request->input('acl', 'private');
        $type        = $request->input('type', 'file'); // file | folder
        $recursive   = (bool) $request->input('recursive', false);

        if (!$this->diskSupportsAcl($disk)) {
            $label = config("filemanager.available_disks.{$disk}", $disk);
            return $this->fail("{$label} tidak mendukung per-object ACL.");
        }

        $validAcls = ['public-read', 'private', 'authenticated-read', 'public-read-write', 'aws-exec-read'];
        if (!in_array($acl, $validAcls)) {
            return $this->fail('ACL tidak valid. Pilihan: ' . implode(', ', $validAcls));
        }

        try {
            $s3     = $this->getS3Client($disk);
            [$bucket] = $this->getBucketAndKey($disk, $storagePath);
            $storage  = Storage::disk($disk);

            if ($type === 'file') {
                // Update single file
                $s3->putObjectAcl([
                    'Bucket' => $bucket,
                    'Key'    => $storagePath,
                    'ACL'    => $acl,
                ]);
                return $this->ok([
                    'path' => $userPath,
                    'acl'  => $acl,
                    'updated' => 1,
                ], 'ACL berhasil diperbarui');
            }

            // Update folder — ambil semua file dalam folder
            $method = $recursive ? 'allFiles' : 'files';
            $files  = $storage->{$method}($storagePath ?: null);

            $success = 0;
            $failed  = [];

            foreach ($files as $filePath) {
                try {
                    $s3->putObjectAcl([
                        'Bucket' => $bucket,
                        'Key'    => $filePath,
                        'ACL'    => $acl,
                    ]);
                    $success++;
                } catch (\Throwable $e) {
                    $failed[] = ['path' => $filePath, 'reason' => $e->getMessage()];
                }
            }

            return $this->ok([
                'path'    => $userPath,
                'acl'     => $acl,
                'updated' => $success,
                'failed'  => $failed,
            ], "{$success} file ACL berhasil diperbarui" . ($failed ? ', ' . count($failed) . ' gagal' : ''));

        } catch (\Throwable $e) {
            return $this->fail('Gagal update ACL: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /filemanager/permission/bulk
     *
     * Bulk update ACL dengan:
     * - Progress bar realtime via Reverb
     * - Concurrency setting (1-100)
     * - Filter by extension (opsional)
     *
     * Body:
     *   disk, path (folder), acl, recursive (bool),
     *   extension_filter (string, misal "mp4"),
     *   concurrency (int, 1-100)
     */
    public function bulkUpdatePermission(Request $request): JsonResponse
    {
        $disk            = $this->getDisk($request);
        $userPath        = $this->safePath($request->input('path', ''));
        $storagePath     = $this->toStoragePath($userPath);
        $acl             = $request->input('acl', 'private');
        $recursive       = (bool) $request->input('recursive', true);
        $extensionFilter = strtolower(trim($request->input('extension_filter', '')));
        $concurrency     = max(1, min(100, (int) $request->input('concurrency', 5)));

        if (!$this->diskSupportsAcl($disk)) {
            $label = config("filemanager.available_disks.{$disk}", $disk);
            return $this->fail("{$label} tidak mendukung per-object ACL.");
        }

        $validAcls = ['public-read', 'private', 'authenticated-read', 'public-read-write', 'aws-exec-read'];
        if (!in_array($acl, $validAcls)) {
            return $this->fail('ACL tidak valid. Pilihan: ' . implode(', ', $validAcls));
        }

        try {
            $s3      = $this->getS3Client($disk);
            [$bucket] = $this->getBucketAndKey($disk, $storagePath);
            $storage  = Storage::disk($disk);

            $method = $recursive ? 'allFiles' : 'files';
            $files  = $storage->{$method}($storagePath ?: null);

            // Filter by extension jika ada
            if ($extensionFilter) {
                $files = array_filter((array) $files, function ($f) use ($extensionFilter) {
                    return strtolower(pathinfo($f, PATHINFO_EXTENSION)) === $extensionFilter;
                });
            }

            $files   = array_values((array) $files);
            $total   = count($files);
            $jobId   = Str::uuid()->toString();
            $success = 0;
            $failed  = [];

            // Broadcast start
            event(new FileManagerBulkProgress($jobId, 0, $total, '', 'processing', 'Memulai bulk update ACL...'));

            // Proses dengan concurrency (chunk-based)
            $chunks = array_chunk($files, $concurrency);

            foreach ($chunks as $chunk) {
                foreach ($chunk as $filePath) {
                    try {
                        $s3->putObjectAcl([
                            'Bucket' => $bucket,
                            'Key'    => $filePath,
                            'ACL'    => $acl,
                        ]);
                        $success++;
                    } catch (\Throwable $e) {
                        $failed[] = ['path' => $filePath, 'reason' => $e->getMessage()];
                    }

                    // Broadcast progress setiap file
                    event(new FileManagerBulkProgress(
                        $jobId,
                        $success + count($failed),
                        $total,
                        basename($filePath),
                        'processing',
                        "Memproses: " . basename($filePath),
                    ));
                }
            }

            // Broadcast done
            event(new FileManagerBulkProgress(
                $jobId,
                $total,
                $total,
                '',
                'done',
                "{$success} file ACL berhasil diperbarui" . ($failed ? ', ' . count($failed) . ' gagal' : ''),
            ));

            return $this->ok([
                'job_id'  => $jobId,
                'path'    => $userPath,
                'acl'     => $acl,
                'total'   => $total,
                'updated' => $success,
                'failed'  => $failed,
            ], "{$success} dari {$total} file ACL berhasil diperbarui");

        } catch (\Throwable $e) {
            return $this->fail('Gagal bulk update ACL: ' . $e->getMessage(), 500);
        }
    }

    // ══════════════════════════════════════════════════════
    //  METADATA
    // ══════════════════════════════════════════════════════

    /**
     * GET /filemanager/metadata?disk=s3&path=folder/file.jpg
     *
     * Ambil metadata S3 dari sebuah file (Content-Type, Cache-Control, dll).
     */
    public function getMetadata(Request $request): JsonResponse
    {
        $disk        = $this->getDisk($request);
        $userPath    = $this->safePath($request->query('path', ''));
        $storagePath = $this->toStoragePath($userPath);

        $s3Disks = config('filemanager.s3_disks', ['s3']);
        if (!in_array($disk, $s3Disks)) {
            return $this->fail('Metadata S3 hanya tersedia untuk disk S3/R2.', 422);
        }

        try {
            $s3     = $this->getS3Client($disk);
            [$bucket, $key] = $this->getBucketAndKey($disk, $storagePath);

            $result = $s3->headObject([
                'Bucket' => $bucket,
                'Key'    => $key,
            ]);

            return $this->ok([
                'disk'          => $disk,
                'path'          => $userPath,
                'content_type'  => $result['ContentType']  ?? null,
                'cache_control' => $result['CacheControl'] ?? null,
                'content_disposition' => $result['ContentDisposition'] ?? null,
                'content_encoding'    => $result['ContentEncoding']    ?? null,
                'content_language'    => $result['ContentLanguage']    ?? null,
                'metadata'      => $result['Metadata']     ?? [],
                'last_modified' => $result['LastModified'] ? $result['LastModified']->format('d M Y H:i') : null,
                'content_length'=> $result['ContentLength'] ?? null,
                'etag'          => $result['ETag'] ?? null,
                'storage_class' => $result['StorageClass'] ?? 'STANDARD',
            ]);

        } catch (\Throwable $e) {
            return $this->fail('Gagal membaca metadata: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /filemanager/metadata
     *
     * Update metadata satu file via S3 CopyObject server-side (tidak perlu download).
     * MetadataDirective: REPLACE → metadata lama diganti dengan yang baru.
     *
     * Body:
     *   disk, path, type (file|folder),
     *   content_type, cache_control, content_disposition,
     *   content_encoding, content_language,
     *   custom_metadata (object key-value)
     */
    public function updateMetadata(Request $request): JsonResponse
    {
        $disk        = $this->getDisk($request);
        $userPath    = $this->safePath($request->input('path', ''));
        $storagePath = $this->toStoragePath($userPath);
        $type        = $request->input('type', 'file');
        $recursive   = (bool) $request->input('recursive', false);

        $s3Disks = config('filemanager.s3_disks', ['s3']);
        if (!in_array($disk, $s3Disks)) {
            return $this->fail('Update metadata S3 hanya tersedia untuk disk S3/R2.', 422);
        }

        $metaInput = $this->buildMetadataInput($request);

        try {
            $s3      = $this->getS3Client($disk);
            [$bucket] = $this->getBucketAndKey($disk, $storagePath);
            $storage  = Storage::disk($disk);

            if ($type === 'file') {
                $this->copyObjectWithMeta($s3, $bucket, $storagePath, $storagePath, $metaInput);
                return $this->ok([
                    'path'    => $userPath,
                    'updated' => 1,
                    'meta'    => $metaInput,
                ], 'Metadata berhasil diperbarui');
            }

            // Update folder
            $method  = $recursive ? 'allFiles' : 'files';
            $files   = $storage->{$method}($storagePath ?: null);
            $success = 0;
            $failed  = [];

            foreach ($files as $filePath) {
                try {
                    $this->copyObjectWithMeta($s3, $bucket, $filePath, $filePath, $metaInput);
                    $success++;
                } catch (\Throwable $e) {
                    $failed[] = ['path' => $filePath, 'reason' => $e->getMessage()];
                }
            }

            return $this->ok([
                'path'    => $userPath,
                'updated' => $success,
                'failed'  => $failed,
                'meta'    => $metaInput,
            ], "{$success} file metadata berhasil diperbarui" . ($failed ? ', ' . count($failed) . ' gagal' : ''));

        } catch (\Throwable $e) {
            return $this->fail('Gagal update metadata: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /filemanager/metadata/bulk
     *
     * Bulk update metadata dengan:
     * - Progress bar realtime via Reverb
     * - Concurrency setting (1-100)
     * - Filter by extension (opsional)
     */
    public function bulkUpdateMetadata(Request $request): JsonResponse
    {
        $disk            = $this->getDisk($request);
        $userPath        = $this->safePath($request->input('path', ''));
        $storagePath     = $this->toStoragePath($userPath);
        $recursive       = (bool) $request->input('recursive', true);
        $extensionFilter = strtolower(trim($request->input('extension_filter', '')));
        $concurrency     = max(1, min(100, (int) $request->input('concurrency', 5)));

        $s3Disks = config('filemanager.s3_disks', ['s3']);
        if (!in_array($disk, $s3Disks)) {
            return $this->fail('Update metadata S3 hanya tersedia untuk disk S3/R2.', 422);
        }

        $metaInput = $this->buildMetadataInput($request);

        try {
            $s3      = $this->getS3Client($disk);
            [$bucket] = $this->getBucketAndKey($disk, $storagePath);
            $storage  = Storage::disk($disk);

            $method = $recursive ? 'allFiles' : 'files';
            $files  = $storage->{$method}($storagePath ?: null);

            if ($extensionFilter) {
                $files = array_filter((array) $files, function ($f) use ($extensionFilter) {
                    return strtolower(pathinfo($f, PATHINFO_EXTENSION)) === $extensionFilter;
                });
            }

            $files   = array_values((array) $files);
            $total   = count($files);
            $jobId   = Str::uuid()->toString();
            $success = 0;
            $failed  = [];

            // Broadcast start
            event(new FileManagerBulkProgress($jobId, 0, $total, '', 'processing', 'Memulai bulk update metadata...'));

            $chunks = array_chunk($files, $concurrency);

            foreach ($chunks as $chunk) {
                foreach ($chunk as $filePath) {
                    try {
                        $this->copyObjectWithMeta($s3, $bucket, $filePath, $filePath, $metaInput);
                        $success++;
                    } catch (\Throwable $e) {
                        $failed[] = ['path' => $filePath, 'reason' => $e->getMessage()];
                    }

                    event(new FileManagerBulkProgress(
                        $jobId,
                        $success + count($failed),
                        $total,
                        basename($filePath),
                        'processing',
                        "Memproses: " . basename($filePath),
                    ));
                }
            }

            // Broadcast done
            event(new FileManagerBulkProgress(
                $jobId,
                $total,
                $total,
                '',
                'done',
                "{$success} file metadata berhasil diperbarui" . ($failed ? ', ' . count($failed) . ' gagal' : ''),
            ));

            return $this->ok([
                'job_id'  => $jobId,
                'path'    => $userPath,
                'total'   => $total,
                'updated' => $success,
                'failed'  => $failed,
                'meta'    => $metaInput,
            ], "{$success} dari {$total} file metadata berhasil diperbarui");

        } catch (\Throwable $e) {
            return $this->fail('Gagal bulk update metadata: ' . $e->getMessage(), 500);
        }
    }

    // ══════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════════════════

    /**
     * Server-side copy (tidak perlu download file).
     * MetadataDirective: REPLACE → timpa metadata lama dengan metaInput.
     */
    private function copyObjectWithMeta(S3Client $s3, string $bucket, string $sourceKey, string $destKey, array $metaInput): void
    {
        $params = [
            'Bucket'            => $bucket,
            'CopySource'        => urlencode($bucket . '/' . $sourceKey),
            'Key'               => $destKey,
            'MetadataDirective' => 'REPLACE',
        ];

        // Merge metadata fields ke params — hanya yang tidak null/kosong
        if (!empty($metaInput['ContentType']))        $params['ContentType']        = $metaInput['ContentType'];
        if (!empty($metaInput['CacheControl']))       $params['CacheControl']       = $metaInput['CacheControl'];
        if (!empty($metaInput['ContentDisposition'])) $params['ContentDisposition'] = $metaInput['ContentDisposition'];
        if (!empty($metaInput['ContentEncoding']))    $params['ContentEncoding']    = $metaInput['ContentEncoding'];
        if (!empty($metaInput['ContentLanguage']))    $params['ContentLanguage']    = $metaInput['ContentLanguage'];
        if (!empty($metaInput['Metadata']))           $params['Metadata']           = $metaInput['Metadata'];

        $s3->copyObject($params);
    }

    /**
     * Build array metadata dari request input.
     */
    private function buildMetadataInput(Request $request): array
    {
        $meta = [];

        if ($request->filled('content_type'))        $meta['ContentType']        = $request->input('content_type');
        if ($request->filled('cache_control'))        $meta['CacheControl']       = $request->input('cache_control');
        if ($request->filled('content_disposition'))  $meta['ContentDisposition'] = $request->input('content_disposition');
        if ($request->filled('content_encoding'))     $meta['ContentEncoding']    = $request->input('content_encoding');
        if ($request->filled('content_language'))     $meta['ContentLanguage']    = $request->input('content_language');

        // Custom user metadata (key-value pairs, akan di-prefix x-amz-meta- oleh AWS SDK)
        $customMeta = $request->input('custom_metadata', []);
        if (is_array($customMeta) && count($customMeta)) {
            $meta['Metadata'] = $customMeta;
        }

        return $meta;
    }

    /**
     * Parse S3 ACL grants menjadi string ACL yang mudah dibaca.
     */
    private function parseAclFromGrants(array $grants): string
    {
        foreach ($grants as $grant) {
            $grantee    = $grant['Grantee'] ?? [];
            $permission = $grant['Permission'] ?? '';
            $uri        = $grantee['URI'] ?? '';

            if ($uri === 'http://acs.amazonaws.com/groups/global/AllUsers') {
                if ($permission === 'READ')       return 'public-read';
                if ($permission === 'FULL_CONTROL') return 'public-read-write';
            }
            if ($uri === 'http://acs.amazonaws.com/groups/global/AuthenticatedUsers') {
                if ($permission === 'READ') return 'authenticated-read';
            }
        }

        return 'private';
    }
}
