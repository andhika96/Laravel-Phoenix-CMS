<?php

namespace App\Services;

use App\Models\FmFile;
use App\Models\FmFolder;
use App\Models\FmQuota;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileManagerService
{
    // ── MIME type → file_type map ──────────────────────────

    protected array $mimeMap = [
        'image'    => ['image/jpeg','image/png','image/gif','image/webp','image/svg+xml','image/bmp','image/tiff'],
        'video'    => ['video/mp4','video/webm','video/ogg','video/quicktime','video/x-msvideo','video/x-matroska'],
        'audio'    => ['audio/mpeg','audio/mp4','audio/ogg','audio/wav','audio/webm','audio/aac','audio/flac'],
        'document' => ['application/pdf','application/msword',
                       'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                       'application/vnd.ms-excel',
                       'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                       'application/vnd.ms-powerpoint',
                       'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                       'text/plain','text/csv'],
        'archive'  => ['application/zip','application/x-rar-compressed','application/x-7z-compressed',
                       'application/x-tar','application/gzip','application/x-bzip2'],
    ];

    // ── Quota helpers ──────────────────────────────────────

    public function getGlobalSetting(string $key, mixed $default = null): mixed
    {
        $row = DB::table('fm_settings')->where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public function getUserQuota(int $userId): array
    {
        $quota  = FmQuota::firstOrCreate(['user_id' => $userId]);
        $global = [
            'max_storage'   => (int) $this->getGlobalSetting('global_max_storage', 10737418240),
            'max_file_size' => (int) $this->getGlobalSetting('global_max_file_size', 104857600),
        ];

        return [
            'max_storage'   => $quota->max_storage   ?? $global['max_storage'],   // -1 = unlimited
            'max_file_size' => $quota->max_file_size ?? $global['max_file_size'],  // -1 = unlimited
            'used_storage'  => $quota->used_storage,
            'is_storage_unlimited'   => ($quota->max_storage   ?? $global['max_storage'])   === -1,
            'is_file_size_unlimited' => ($quota->max_file_size ?? $global['max_file_size']) === -1,
        ];
    }

    public function checkCanUpload(int $userId, int $fileSize): array
    {
        $q = $this->getUserQuota($userId);

        if (!$q['is_file_size_unlimited'] && $fileSize > $q['max_file_size']) {
            return ['ok' => false, 'message' => 'Ukuran file melebihi batas maksimum (' . $this->humanBytes($q['max_file_size']) . ')'];
        }

        if (!$q['is_storage_unlimited']) {
            $remaining = $q['max_storage'] - $q['used_storage'];
            if ($fileSize > $remaining) {
                return ['ok' => false, 'message' => 'Kuota penyimpanan tidak mencukupi. Tersisa: ' . $this->humanBytes($remaining)];
            }
        }

        return ['ok' => true];
    }

    public function incrementUsedStorage(int $userId, int $bytes): void
    {
        FmQuota::firstOrCreate(['user_id' => $userId])
               ->increment('used_storage', $bytes);
    }

    public function decrementUsedStorage(int $userId, int $bytes): void
    {
        // Pastikan used_storage tidak pernah negatif (BIGINT UNSIGNED floor = 0)
        FmQuota::where('user_id', $userId)
               ->where('used_storage', '>=', $bytes)
               ->decrement('used_storage', $bytes);

        // Jika used_storage < bytes, set ke 0 saja (data tidak konsisten, amankan)
        FmQuota::where('user_id', $userId)
               ->where('used_storage', '<', 0)
               ->update(['used_storage' => 0]);
    }

    // ── Disk helper ────────────────────────────────────────

    public function getDefaultDisk(): string
    {
        return $this->getGlobalSetting('default_disk', 'public');
    }

    public function resolveUrl(FmFile $file): string
    {
        try {
            if ($file->disk === 's3') {
                return Storage::disk('s3')->url($file->path);
            }
            if ($file->disk === 'local') {
                return route('filemanager.serve', ['id' => $file->id]);
            }
            return Storage::disk('public')->url($file->path);
        } catch (\Throwable $e) {
            // Jika disk belum dikonfigurasi, kembalikan path saja agar tidak crash
            return '/storage/' . $file->path;
        }
    }

    // ── File type detection ────────────────────────────────

    public function detectFileType(string $mime): string
    {
        foreach ($this->mimeMap as $type => $mimes) {
            if (in_array($mime, $mimes)) return $type;
        }
        return 'other';
    }

    // ── Folder helpers ─────────────────────────────────────

    public function buildFolderPath(?int $parentId): string
    {
        if (!$parentId) return '';
        $folder = FmFolder::find($parentId);
        return $folder ? $folder->path : '';
    }

    public function ensureFolderExists(string $disk, string $path): void
    {
        if ($disk === 's3') return; // S3 tidak perlu buat folder
        if (!Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->makeDirectory($path);
        }
    }

    // ── Upload ─────────────────────────────────────────────

    public function upload(UploadedFile $file, int $userId, ?int $folderId, string $disk): FmFile
    {
        $size   = $file->getSize();
        $check  = $this->checkCanUpload($userId, $size);
        if (!$check['ok']) {
            throw new \Exception($check['message']);
        }

        $mime      = $file->getMimeType();
        $ext       = strtolower($file->getClientOriginalExtension());
        $fileType  = $this->detectFileType($mime);
        $origName  = $file->getClientOriginalName();
        $safeName  = Str::uuid() . '.' . $ext;

        $basePath    = $this->buildFolderPath($folderId);
        $storagePath = $basePath ? $basePath . '/' . $safeName : 'filemanager/' . $safeName;

        // ── Simpan ke storage dulu, baru buat record DB ────
        // Jika storage gagal (misal S3 Bucket belum diset), exception
        // langsung dilempar ke controller tanpa membuat record DB.
        $this->ensureFolderExists($disk, dirname($storagePath));

        try {
            Storage::disk($disk)->put($storagePath, file_get_contents($file));
        } catch (\Throwable $e) {
            throw new \Exception('Gagal menyimpan file ke storage (' . $disk . '): ' . $e->getMessage());
        }

        $meta = [];
        if ($fileType === 'image') {
            try {
                $img = Image::make($file);
                $meta = ['width' => $img->width(), 'height' => $img->height()];
            } catch (\Throwable $e) {}
        }

        // ── Buat record DB dalam transaction ───────────────
        try {
            $fmFile = \DB::transaction(function () use (
                $folderId, $userId, $origName, $safeName, $storagePath,
                $mime, $ext, $fileType, $size, $disk, $meta
            ) {
                $fmFile = FmFile::create([
                    'folder_id'     => $folderId,
                    'user_id'       => $userId,
                    'original_name' => $origName,
                    'name'          => $safeName,
                    'path'          => $storagePath,
                    'url'           => null,
                    'mime_type'     => $mime,
                    'extension'     => $ext,
                    'file_type'     => $fileType,
                    'size'          => $size,
                    'disk'          => $disk,
                    'is_public'     => true,
                    'meta'          => $meta ?: null,
                ]);

                $fmFile->update(['url' => $this->resolveUrl($fmFile)]);
                return $fmFile;
            });
        } catch (\Throwable $e) {
            // DB gagal → hapus file yang sudah terlanjur di-upload ke storage
            try { Storage::disk($disk)->delete($storagePath); } catch (\Throwable $_) {}
            throw new \Exception('Gagal menyimpan record file: ' . $e->getMessage());
        }

        $this->incrementUsedStorage($userId, $size);
        return $fmFile;
    }

    // ── Delete ─────────────────────────────────────────────

    public function deleteFile(FmFile $file): void
    {
        Storage::disk($file->disk)->delete($file->path);
        $this->decrementUsedStorage($file->user_id, $file->size);
        $file->delete();
    }

    // ── Move / Copy ────────────────────────────────────────

    public function moveFile(FmFile $file, ?int $targetFolderId): FmFile
    {
        $basePath    = $this->buildFolderPath($targetFolderId);
        $newPath     = ($basePath ? $basePath . '/' : 'filemanager/') . $file->name;

        Storage::disk($file->disk)->move($file->path, $newPath);
        $file->update(['folder_id' => $targetFolderId, 'path' => $newPath]);
        $file->update(['url' => $this->resolveUrl($file)]);
        return $file;
    }

    public function copyFile(FmFile $file, ?int $targetFolderId, int $userId): FmFile
    {
        $check = $this->checkCanUpload($userId, $file->size);
        if (!$check['ok']) throw new \Exception($check['message']);

        $newName  = Str::uuid() . '.' . $file->extension;
        $basePath = $this->buildFolderPath($targetFolderId);
        $newPath  = ($basePath ? $basePath . '/' : 'filemanager/') . $newName;

        Storage::disk($file->disk)->copy($file->path, $newPath);

        $newFile = $file->replicate()->fill([
            'folder_id' => $targetFolderId,
            'name'      => $newName,
            'path'      => $newPath,
        ]);
        $newFile->save();
        $newFile->update(['url' => $this->resolveUrl($newFile)]);

        $this->incrementUsedStorage($userId, $file->size);
        return $newFile;
    }

    // ── Rename ─────────────────────────────────────────────

    public function renameFile(FmFile $file, string $newName): FmFile
    {
        $ext = $file->extension;
        // Pastikan ekstensi tetap
        if (!Str::endsWith($newName, '.' . $ext)) {
            $newName = $newName . '.' . $ext;
        }
        $dir     = dirname($file->path);
        $newPath = $dir . '/' . $newName;
        Storage::disk($file->disk)->move($file->path, $newPath);
        $file->update(['original_name' => $newName, 'path' => $newPath]);
        $file->update(['url' => $this->resolveUrl($file)]);
        return $file;
    }

    public function renameFolder(FmFolder $folder, string $newName): FmFolder
    {
        $newSlug = Str::slug($newName);
        $oldPath = $folder->path;
        $parent  = dirname($oldPath);
        $newPath = ($parent === '.' ? '' : $parent . '/') . $newSlug;

        // Rename di storage
        $disk = $folder->disk;
        if ($disk !== 's3') {
            Storage::disk($disk)->move($oldPath, $newPath);
        }

        // Update all child paths
        $this->updateChildPaths($folder, $oldPath, $newPath, $disk);

        $folder->update(['name' => $newName, 'slug' => $newSlug, 'path' => $newPath]);
        return $folder;
    }

    protected function updateChildPaths(FmFolder $folder, string $oldBase, string $newBase, string $disk): void
    {
        foreach ($folder->children as $child) {
            $newChildPath = Str::replaceFirst($oldBase, $newBase, $child->path);
            $this->updateChildPaths($child, $child->path, $newChildPath, $disk);
            $child->update(['path' => $newChildPath]);
        }
        foreach ($folder->files as $file) {
            $newFilePath = Str::replaceFirst($oldBase, $newBase, $file->path);
            Storage::disk($disk)->move($file->path, $newFilePath);
            $file->update(['path' => $newFilePath, 'url' => null]);
            $file->update(['url' => $this->resolveUrl($file)]);
        }
    }

    // ── Folder CRUD ────────────────────────────────────────

    public function createFolder(string $name, ?int $parentId, int $userId, string $disk): FmFolder
    {
        $slug     = Str::slug($name);
        $basePath = $this->buildFolderPath($parentId);
        $path     = $basePath ? $basePath . '/' . $slug : 'filemanager/' . $slug;

        $this->ensureFolderExists($disk, $path);

        return FmFolder::create([
            'parent_id' => $parentId,
            'user_id'   => $userId,
            'name'      => $name,
            'slug'      => $slug,
            'path'      => $path,
            'disk'      => $disk,
            'is_public' => true,
        ]);
    }

    public function deleteFolder(FmFolder $folder): void
    {
        $disk = $folder->disk;
        // Recursively delete files
        foreach ($folder->files as $file) {
            $this->deleteFile($file);
        }
        foreach ($folder->children as $child) {
            $this->deleteFolder($child);
        }
        if ($disk !== 's3') {
            Storage::disk($disk)->deleteDirectory($folder->path);
        }
        $folder->delete();
    }

    // ── Utility ────────────────────────────────────────────

    public function humanBytes(int $bytes): string
    {
        if ($bytes === -1) return 'Unlimited';
        if ($bytes >= 1099511627776) return round($bytes / 1099511627776, 2) . ' TB';
        if ($bytes >= 1073741824)    return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)       return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)          return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
