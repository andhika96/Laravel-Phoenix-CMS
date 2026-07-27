<?php

namespace Tests\Feature\FileManagerV2;

use App\Services\FileManagerV2\FileManagerV2Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class FileManagerV2FolderBatchUploadTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/filemanager_v2_folder_batch');
        File::deleteDirectory($this->root);
        config([
            'filemanager_v2.local.files_root' => $this->root . '/files',
            'filemanager_v2.local.cache_root' => $this->root . '/cache',
            'filemanager_v2.local.metadata_root' => $this->root . '/metadata',
            'filemanager_v2.local.runtime_root' => $this->root . '/runtime',
            'filemanager_v2.settings.root' => $this->root . '/settings',
            'filemanager_v2.cache.store' => 'array',
            'filemanager_v2.connections.local.enabled' => true,
            'filemanager_v2.connections.local.quota_bytes' => 12,
            'filemanager_v2.connections.r2.enabled' => false,
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->root);

        parent::tearDown();
    }

    public function test_folder_upload_batch_creates_unique_nested_paths_and_reserves_quota_once(): void
    {
        $storage = app(FileManagerV2Storage::class);

        $batch = $storage->beginFolderUploadBatch('local', 'Plugins', [
            'fontawesome',
            'fontawesome/css',
            'fontawesome/webfonts',
            'fontawesome/css',
        ], 8, 2);

        $this->assertSame('Plugins', $batch['path']);
        $this->assertSame(3, $batch['folderCount']);
        $this->assertDirectoryExists($this->root . '/files/Plugins/fontawesome/css');
        $this->assertDirectoryExists($this->root . '/files/Plugins/fontawesome/webfonts');

        try {
            $storage->beginFolderUploadBatch('local', 'Other', ['assets'], 5, 1);
            $this->fail('A second batch must not exceed the quota reserved by an active batch.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }

        $storage->completeFolderUploadBatch($batch['id']);

        $next = $storage->beginFolderUploadBatch('local', 'Other', ['assets'], 5, 1);
        $this->assertSame('Other', $next['path']);

    }
    public function test_folder_batch_claims_direct_upload_bytes_without_rechecking_quota_per_file(): void
    {
        config(['filemanager_v2.connections.local.quota_bytes' => 8192]);
        $storage = app(FileManagerV2Storage::class);
        $batch = $storage->beginFolderUploadBatch('local', 'Plugins', ['fontawesome/css'], 4096, 1);

        $asset = $storage->upload(
            'local',
            'Plugins/fontawesome/css',
            UploadedFile::fake()->create('all.css', 4),
            $batch['id'],
        );

        $this->assertSame('Plugins/fontawesome/css/all.css', $asset['path']);
        $completed = $storage->completeFolderUploadBatch($batch['id']);
        $this->assertSame(4096, $completed['claimedBytes']);
    }

    public function test_folder_batch_claims_chunked_upload_bytes(): void
    {
        config(['filemanager_v2.connections.local.quota_bytes' => 8192]);
        $storage = app(FileManagerV2Storage::class);
        $batch = $storage->beginFolderUploadBatch('local', 'Plugins', ['fontawesome/js'], 2048, 1);
        $session = $storage->startUpload('local', 'Plugins/fontawesome/js', 'all.js', 2048, 2, null, $batch['id']);

        $storage->storeChunk($session['id'], 0, UploadedFile::fake()->createWithContent('part-0', str_repeat('a', 1024)));
        $storage->storeChunk($session['id'], 1, UploadedFile::fake()->createWithContent('part-1', str_repeat('b', 1024)));
        $asset = $storage->completeUpload($session['id']);

        $this->assertSame('Plugins/fontawesome/js/all.js', $asset['path']);
        $completed = $storage->completeFolderUploadBatch($batch['id']);
        $this->assertSame(2048, $completed['claimedBytes']);

    }
    public function test_folder_batch_api_preflights_and_completes_the_batch(): void
    {
        $this->withoutMiddleware();

        $response = $this->postJson('/api/v2/file-manager/uploads/batches', [
            'storage' => 'local',
            'path' => 'Plugins',
            'folders' => ['fontawesome', 'fontawesome/css'],
            'totalBytes' => 8,
            'fileCount' => 2,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.path', 'Plugins')
            ->assertJsonPath('data.folderCount', 2);
        $id = $response->json('data.id');

        $this->postJson("/api/v2/file-manager/uploads/batches/{$id}/complete")
            ->assertOk()
            ->assertJsonPath('data.id', $id);
    }
}
