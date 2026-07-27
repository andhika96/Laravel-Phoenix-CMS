<?php

namespace Tests\Feature\FileManagerV2;

use App\Services\FileManagerV2\FileManagerV2Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FileManagerV2UploadRecoveryTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/filemanager_v2_upload_recovery');
        File::deleteDirectory($this->root);
        config([
            'filemanager_v2.local.files_root' => $this->root . '/files',
            'filemanager_v2.local.cache_root' => $this->root . '/cache',
            'filemanager_v2.local.metadata_root' => $this->root . '/metadata',
            'filemanager_v2.local.runtime_root' => $this->root . '/runtime',
            'filemanager_v2.settings.root' => $this->root . '/settings',
            'filemanager_v2.cache.store' => 'array',
            'filemanager_v2.connections.local.enabled' => true,
            'filemanager_v2.connections.r2.enabled' => false,
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->root);

        parent::tearDown();
    }

    public function test_direct_retry_with_the_same_key_returns_the_original_asset_without_a_duplicate_name(): void
    {
        $storage = app(FileManagerV2Storage::class);
        $first = $storage->upload('local', 'Retries', UploadedFile::fake()->createWithContent('icon.svg', '<svg />'), null, 'folder-job-icon');
        $retried = $storage->upload('local', 'Retries', UploadedFile::fake()->createWithContent('icon.svg', '<svg />'), null, 'folder-job-icon');

        $this->assertSame('Retries/icon.svg', $first['path']);
        $this->assertSame($first['path'], $retried['path']);
        $this->assertSame(['Retries/icon.svg'], array_column($storage->browse('local', 'Retries')['items'], 'path'));
    }

    public function test_chunked_retry_with_the_same_key_returns_the_completed_asset_instead_of_another_session(): void
    {
        $storage = app(FileManagerV2Storage::class);
        $session = $storage->startUpload('local', 'Retries', 'large.txt', 10, 2, hash('sha256', 'helloworld'), null, 'folder-job-large');
        $storage->storeChunk($session['id'], 0, UploadedFile::fake()->createWithContent('large.txt.part', 'hello'));
        $storage->storeChunk($session['id'], 1, UploadedFile::fake()->createWithContent('large.txt.part', 'world'));
        $asset = $storage->completeUpload($session['id']);

        $retried = $storage->startUpload('local', 'Retries', 'large.txt', 10, 2, hash('sha256', 'helloworld'), null, 'folder-job-large');

        $this->assertSame('Retries/large.txt', $asset['path']);
        $this->assertSame($asset['path'], $retried['asset']['path']);
    }
}
