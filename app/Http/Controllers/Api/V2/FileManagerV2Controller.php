<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Services\FileManagerV2\FileManagerV2Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileManagerV2Controller extends Controller
{
    public function __construct(private readonly FileManagerV2Storage $storage)
    {
    }

    public function bootstrap(): JsonResponse
    {
        return response()->json(['data' => $this->storage->bootstrap()]);
    }

    public function profiles(): JsonResponse
    {
        return response()->json(['data' => $this->storage->profiles()]);
    }

    public function settings(): JsonResponse
    {
        return response()->json(['data' => $this->storage->settings()]);
    }

    public function saveSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'defaultStorage' => ['required', 'string', 'max:40'],
            'connections' => ['required', 'array', 'min:1', 'max:20'],
            'connections.*.id' => ['required', 'string', 'regex:/^[a-z][a-z0-9-]{0,38}$/'],
            'connections.*.type' => ['required', 'in:local,s3,s3_compatible,r2'],
            'connections.*.name' => ['required', 'string', 'max:80'],
            'connections.*.shortName' => ['nullable', 'string', 'max:20'],
            'connections.*.enabled' => ['required', 'boolean'],
            'connections.*.root' => ['nullable', 'string', 'max:255'],
            'connections.*.quotaBytes' => ['required', 'integer', 'min:0', 'max:10995116277760'],
            'connections.*.bucket' => ['nullable', 'string', 'max:255'],
            'connections.*.region' => ['nullable', 'string', 'max:80'],
            'connections.*.endpoint' => ['nullable', 'string', 'max:2048'],
            'connections.*.usePathStyle' => ['nullable', 'boolean'],
            'connections.*.accessKey' => ['nullable', 'string', 'max:1024'],
            'connections.*.secretKey' => ['nullable', 'string', 'max:2048'],
            'upload' => ['required', 'array'],
            'upload.maxFileSize' => ['required', 'integer', 'min:1', 'max:10995116277760'],
            'upload.chunkSize' => ['required', 'integer', 'min:1', 'max:1073741824'],
            'upload.chunkThreshold' => ['required', 'integer', 'min:1', 'max:10995116277760'],
            'upload.maxParallel' => ['required', 'integer', 'min:1', 'max:10'],
            'upload.retryAttempts' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        return response()->json(['data' => $this->storage->saveSettings($validated)]);
    }

    public function testSettingsConnection(Request $request): JsonResponse
    {
        $validated = $request->validate(['storage' => ['required', 'string', 'max:40']]);

        return response()->json(['data' => $this->storage->testConnection($validated['storage'])]);
    }

    public function browse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['nullable', 'string', 'max:40'],
            'path' => ['nullable', 'string', 'max:2000'],
            'search' => ['nullable', 'string', 'max:180'],
            'type' => ['nullable', 'in:all,image,video,audio,document'],
            'sort' => ['nullable', 'in:modified,name,size'],
            'collection' => ['nullable', 'in:all,recent,starred,shared'],
        ]);

        $storage = $validated['storage'] ?? config('filemanager_v2.default_storage');

        return response()->json(['data' => $this->storage->browse($storage, $validated['path'] ?? '', $validated)]);
    }

    public function folders(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['nullable', 'string', 'max:40'],
        ]);
        $storage = $validated['storage'] ?? config('filemanager_v2.default_storage');

        return response()->json(['data' => $this->storage->folders($storage)]);
    }
    public function details(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'path' => ['required', 'string', 'max:2000'],
        ]);

        return response()->json(['data' => $this->storage->folderDetails($validated['storage'], $validated['path'])]);
    }

    public function rename(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'path' => ['required', 'string', 'max:2000'],
            'name' => ['required', 'string', 'max:180'],
        ]);

        return response()->json(['data' => $this->storage->rename($validated['storage'], $validated['path'], $validated['name'])]);
    }


    public function move(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'paths' => ['required', 'array', 'min:1', 'max:100'],
            'paths.*' => ['required', 'string', 'max:2000'],
            'destination' => ['nullable', 'string', 'max:2000'],
        ]);

        return response()->json(['data' => $this->storage->move(
            $validated['storage'],
            $validated['paths'],
            $validated['destination'] ?? '',
        )]);
    }

    public function toggleStar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'path' => ['required', 'string', 'max:2000'],
        ]);

        return response()->json(['data' => $this->storage->toggleStar($validated['storage'], $validated['path'])]);
    }


    public function createFolder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'path' => ['nullable', 'string', 'max:2000'],
            'name' => ['required', 'string', 'max:180'],
        ]);

        return response()->json(['data' => $this->storage->makeDirectory($validated['storage'], $validated['path'] ?? '', $validated['name'])], 201);
    }

    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'path' => ['nullable', 'string', 'max:2000'],
            'file' => ['required', 'file'],
            'batchId' => ['nullable', 'uuid'],
            'idempotencyKey' => ['nullable', 'string', 'max:120'],
        ]);

        return response()->json(['data' => $this->storage->upload($validated['storage'], $validated['path'] ?? '', $request->file('file'), $validated['batchId'] ?? null, $validated['idempotencyKey'] ?? null)], 201);
    }

    public function startUpload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'path' => ['nullable', 'string', 'max:2000'],
            'name' => ['required', 'string', 'max:180'],
            'size' => ['required', 'integer', 'min:1'],
            'parts' => ['required', 'integer', 'min:1', 'max:10000'],
            'checksum' => ['nullable', 'regex:/^[a-fA-F0-9]{64}$/'],
            'batchId' => ['nullable', 'uuid'],
            'idempotencyKey' => ['nullable', 'string', 'max:120'],
        ]);

        return response()->json(['data' => $this->storage->startUpload(
            $validated['storage'],
            $validated['path'] ?? '',
            $validated['name'],
            $validated['size'],
            $validated['parts'],
            $validated['checksum'] ?? null,
            $validated['batchId'] ?? null,
            $validated['idempotencyKey'] ?? null,
        )], 201);
    }

    public function beginFolderUploadBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'path' => ['nullable', 'string', 'max:2000'],
            'folders' => ['required', 'array', 'min:1', 'max:100000'],
            'folders.*' => ['required', 'string', 'max:2000'],
            'totalBytes' => ['required', 'integer', 'min:1'],
            'fileCount' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        return response()->json(['data' => $this->storage->beginFolderUploadBatch(
            $validated['storage'],
            $validated['path'] ?? '',
            $validated['folders'],
            $validated['totalBytes'],
            $validated['fileCount'],
        )], 201);
    }

    public function completeFolderUploadBatch(string $batch): JsonResponse
    {
        return response()->json(['data' => $this->storage->completeFolderUploadBatch($batch)]);
    }

    public function uploadChunk(Request $request, string $upload, int $part): JsonResponse
    {
        $request->validate(['chunk' => ['required', 'file']]);

        return response()->json(['data' => $this->storage->storeChunk($upload, $part, $request->file('chunk'))]);
    }

    public function completeUpload(string $upload): JsonResponse
    {
        return response()->json(['data' => $this->storage->completeUpload($upload)]);
    }

    public function cancelUpload(string $upload): JsonResponse
    {
        $this->storage->cancelUpload($upload);

        return response()->json(status: 204);
    }

    public function delete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'path' => ['required', 'string', 'max:2000'],
        ]);
        $this->storage->delete($validated['storage'], $validated['path']);

        return response()->json(status: 204);
    }

    public function deletePreview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'paths' => ['required', 'array', 'min:1', 'max:100'],
            'paths.*' => ['required', 'string', 'max:2000'],
        ]);

        return response()->json(['data' => $this->storage->deletePreview($validated['storage'], $validated['paths'])]);
    }

    public function deleteMany(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'paths' => ['required', 'array', 'min:1', 'max:100'],
            'paths.*' => ['required', 'string', 'max:2000'],
        ]);

        return response()->json(['data' => $this->storage->deleteMany($validated['storage'], $validated['paths'])]);
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'path' => ['required', 'string', 'max:2000'],
            'width' => ['nullable', 'integer', 'min:80', 'max:1000'],
        ]);

        return $this->storage->responseForPreview($validated['storage'], $validated['path'], $validated['width'] ?? 360);
    }

    public function download(Request $request)
    {
        $validated = $request->validate([
            'storage' => ['required', 'string', 'max:40'],
            'path' => ['required', 'string', 'max:2000'],
        ]);

        return $this->storage->responseForDownload($validated['storage'], $validated['path']);
    }
}
