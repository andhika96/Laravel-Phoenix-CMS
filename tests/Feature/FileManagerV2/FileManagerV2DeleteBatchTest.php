<?php

namespace Tests\Feature\FileManagerV2;

use App\Services\FileManagerV2\FileManagerV2Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FileManagerV2DeleteBatchTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/filemanager_v2_delete_batch');
        File::deleteDirectory($this->root);
        config([
            'filemanager_v2.local.files_root' => $this->root . '/files',
            'filemanager_v2.local.cache_root' => $this->root . '/cache',
            'filemanager_v2.local.metadata_root' => $this->root . '/metadata',
            'filemanager_v2.local.runtime_root' => $this->root . '/runtime',
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

    public function test_delete_preview_counts_folder_contents_without_counting_selected_descendants_twice(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Campaigns');
        $service->makeDirectory('local', 'Campaigns', 'Images');
        $service->upload('local', 'Campaigns', UploadedFile::fake()->createWithContent('brief.txt', 'brief'));
        $service->upload('local', 'Campaigns/Images', UploadedFile::fake()->createWithContent('logo.txt', 'logo'));
        $service->upload('local', '', UploadedFile::fake()->createWithContent('root.txt', 'root'));

        $preview = $service->deletePreview('local', ['Campaigns', 'Campaigns/Images/logo.txt', 'root.txt']);

        $this->assertSame(3, $preview['requestedCount']);
        $this->assertSame(2, $preview['targetCount']);
        $this->assertSame(1, $preview['includedByFolderCount']);
        $this->assertSame(3, $preview['fileCount']);
        $this->assertSame(2, $preview['folderCount']);
        $this->assertSame(1, $preview['selectedFileCount']);
        $this->assertSame(1, $preview['selectedFolderCount']);
        $this->assertSame(2, $preview['nestedFileCount']);
        $this->assertSame(1, $preview['nestedFolderCount']);
        $this->assertSame(13, $preview['bytes']);
    }

    public function test_batch_delete_removes_only_normalized_targets_and_clears_nested_starred_paths(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Campaigns');
        $service->makeDirectory('local', 'Campaigns', 'Images');
        $service->upload('local', 'Campaigns/Images', UploadedFile::fake()->createWithContent('logo.txt', 'logo'));
        $service->upload('local', '', UploadedFile::fake()->createWithContent('root.txt', 'root'));
        $service->toggleStar('local', 'Campaigns/Images/logo.txt');

        $result = $service->deleteMany('local', ['Campaigns', 'Campaigns/Images/logo.txt', 'root.txt']);

        $this->assertSame(2, $result['deletedCount']);
        $this->assertSame(0, $result['failedCount']);
        $this->assertSame(1, $result['preview']['includedByFolderCount']);
        $this->assertDirectoryDoesNotExist($this->root . '/files/Campaigns');
        $this->assertFileDoesNotExist($this->root . '/files/root.txt');
        $this->assertCount(0, $service->browse('local', '', ['collection' => 'starred'])['items']);
    }

    public function test_delete_preview_and_batch_delete_are_available_through_the_v2_api(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Campaigns');
        $service->upload('local', 'Campaigns', UploadedFile::fake()->createWithContent('brief.txt', 'brief'));

        $this->withoutMiddleware();

        $this->postJson('/api/v2/file-manager/assets/delete-preview', [
            'storage' => 'local',
            'paths' => ['Campaigns'],
        ])
            ->assertOk()
            ->assertJsonPath('data.fileCount', 1)
            ->assertJsonPath('data.folderCount', 1);

        $this->postJson('/api/v2/file-manager/assets/delete-batch', [
            'storage' => 'local',
            'paths' => ['Campaigns'],
        ])
            ->assertOk()
            ->assertJsonPath('data.deletedCount', 1)
            ->assertJsonPath('data.failedCount', 0);
    }

    public function test_batch_delete_modal_uses_server_preview_and_not_parallel_single_delete_requests(): void
    {
        $app = file_get_contents(resource_path('js/filemanager_v2/App.vue'));
        $live = file_get_contents(resource_path('js/filemanager_v2/data/live.js'));

        $this->assertStringContainsString('export async function previewDelete', $live);
        $this->assertStringContainsString('export async function deleteAssets', $live);
        $this->assertStringContainsString('await previewDelete(activeStorage.value, paths)', $app);
        $this->assertStringContainsString('await deleteAssets(activeStorage.value, paths)', $app);
        $this->assertStringNotContainsString('Promise.all(selected.map((asset) => deleteAsset(activeStorage.value, asset.path)))', $app);
        $this->assertStringContainsString('Delete permanently', $app);
    }
}
