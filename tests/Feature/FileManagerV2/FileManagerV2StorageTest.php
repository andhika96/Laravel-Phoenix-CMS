<?php

namespace Tests\Feature\FileManagerV2;

use App\Services\FileManagerV2\FileManagerV2Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FileManagerV2StorageTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/filemanager_v2');
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

    public function test_it_creates_isolated_local_storage_and_hides_internal_directories(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->prepare();

        $this->assertDirectoryExists($this->root . '/files');
        $this->assertDirectoryExists($this->root . '/cache/thumbnails');
        $this->assertDirectoryExists($this->root . '/runtime/uploads');
        $this->assertFileExists($this->root . '/.htaccess');

        $service->makeDirectory('local', '', 'Campaigns');
        $service->upload('local', 'Campaigns', UploadedFile::fake()->createWithContent('readme.txt', 'V2 asset'));
        $browse = $service->browse('local', 'Campaigns');

        $this->assertCount(1, $browse['items']);
        $this->assertSame('readme.txt', $browse['items'][0]['name']);
        $this->assertStringNotContainsString('runtime', $browse['items'][0]['path']);
        $this->assertStringNotContainsString('cache', $browse['items'][0]['path']);
    }

    public function test_it_reassembles_chunked_upload_in_the_visible_files_root(): void
    {
        $service = app(FileManagerV2Storage::class);
        $session = $service->startUpload('local', '', 'large.txt', 10, 2, hash('sha256', 'helloworld'));

        $service->storeChunk($session['id'], 0, UploadedFile::fake()->createWithContent('large.txt.part', 'hello'));
        $service->storeChunk($session['id'], 1, UploadedFile::fake()->createWithContent('large.txt.part', 'world'));
        $asset = $service->completeUpload($session['id']);

        $this->assertSame('large.txt', $asset['name']);
        $this->assertFileExists($this->root . '/files/large.txt');
        $this->assertFileDoesNotExist($this->root . '/runtime/uploads/' . $session['id'] . '/session.json');
    }

    public function test_it_prunes_expired_chunk_upload_runtime_directories(): void
    {
        $service = app(FileManagerV2Storage::class);
        $session = $service->startUpload('local', '', 'expired.txt', 4, 1);
        $sessionPath = $this->root . '/runtime/uploads/' . $session['id'] . '/session.json';
        touch($sessionPath, now()->subHours(25)->getTimestamp());

        $this->assertSame(1, $service->pruneExpiredUploads());
        $this->assertDirectoryDoesNotExist($this->root . '/runtime/uploads/' . $session['id']);
    }

    public function test_it_lists_sidebar_folders_independently_from_the_open_folder(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Testing');
        $service->makeDirectory('local', 'Testing', 'Nested');
        $service->upload('local', 'Testing/Nested', UploadedFile::fake()->createWithContent('inside.txt', 'inside'));

        $browse = $service->browse('local', 'Testing/Nested');
        $folders = $service->folders('local');

        $this->assertSame('Testing/Nested', $browse['path']);
        $this->assertSame(['Testing', 'Testing/Nested'], array_column($folders, 'path'));
        $this->assertSame('inside.txt', $browse['items'][0]['name']);
    }

    public function test_folder_tree_cache_is_refreshed_after_a_managed_change(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Cached');

        $this->assertSame(['Cached'], array_column($service->folders('local'), 'path'));

        File::makeDirectory($this->root . '/files/External');
        $this->assertSame(['Cached'], array_column($service->folders('local'), 'path'));

        $service->makeDirectory('local', '', 'Managed');
        $this->assertEqualsCanonicalizing(['Cached', 'External', 'Managed'], array_column($service->folders('local'), 'path'));
    }

    public function test_all_assets_lists_only_immediate_root_files_and_folders(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Testing');
        $service->makeDirectory('local', 'Testing', 'Nested');
        $service->upload('local', '', UploadedFile::fake()->createWithContent('root.txt', 'root'));
        $service->upload('local', 'Testing', UploadedFile::fake()->createWithContent('top-level.txt', 'top'));
        $service->upload('local', 'Testing/Nested', UploadedFile::fake()->createWithContent('inside.txt', 'inside'));

        $allAssets = $service->browse('local', '', ['collection' => 'all', 'sort' => 'name']);
        $folderAssets = $service->browse('local', 'Testing', ['collection' => 'all', 'sort' => 'name']);

        $this->assertEqualsCanonicalizing(['Testing', 'root.txt'], array_column($allAssets['items'], 'path'));
        $this->assertSame(['Testing'], array_column($allAssets['folders'], 'path'));
        $this->assertSame(['Testing/Nested', 'Testing/top-level.txt'], array_column($folderAssets['items'], 'path'));
        $this->assertNotContains('Testing/top-level.txt', array_column($allAssets['items'], 'path'));
        $this->assertNotContains('Testing/Nested/inside.txt', array_column($allAssets['items'], 'path'));
    }

    public function test_it_calculates_recursive_totals_only_when_folder_details_are_requested(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Campaigns');
        $service->makeDirectory('local', 'Campaigns', 'Images');
        $service->upload('local', 'Campaigns', UploadedFile::fake()->createWithContent('brief.txt', '1234'));
        $service->upload('local', 'Campaigns/Images', UploadedFile::fake()->createWithContent('logo.txt', '12'));

        $this->assertTrue(method_exists($service, 'folderDetails'), 'FileManagerV2Storage::folderDetails() must provide on-demand folder totals.');
        $details = $service->folderDetails('local', 'Campaigns');
        $browse = $service->browse('local', '', ['collection' => 'all', 'sort' => 'name']);

        $this->assertSame('Campaigns', $details['path']);
        $this->assertSame(6, $details['bytes']);
        $this->assertSame('6 B', $details['size']);
        $this->assertSame(2, $details['fileCount']);
        $this->assertSame(1, $details['folderCount']);
        $this->assertSame(0, $browse['items'][0]['bytes']);
    }

    public function test_it_renames_a_file_and_updates_its_starred_path(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Drafts');
        $service->upload('local', 'Drafts', UploadedFile::fake()->createWithContent('old.txt', 'contents'));
        $service->toggleStar('local', 'Drafts/old.txt');

        $this->assertTrue(method_exists($service, 'rename'), 'FileManagerV2Storage::rename() must rename files and folders.');
        $renamed = $service->rename('local', 'Drafts/old.txt', 'final.txt');

        $this->assertSame('Drafts/final.txt', $renamed['path']);
        $this->assertFileExists($this->root . '/files/Drafts/final.txt');
        $this->assertFileDoesNotExist($this->root . '/files/Drafts/old.txt');
        $this->assertSame(['Drafts/final.txt'], array_column($service->browse('local', '', ['collection' => 'starred'])['items'], 'path'));
    }

    public function test_it_renames_a_folder_and_updates_nested_starred_paths(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Campaigns');
        $service->makeDirectory('local', 'Campaigns', 'Images');
        $service->upload('local', 'Campaigns/Images', UploadedFile::fake()->createWithContent('logo.txt', 'logo'));
        $service->toggleStar('local', 'Campaigns/Images/logo.txt');

        $this->assertTrue(method_exists($service, 'rename'), 'FileManagerV2Storage::rename() must rename folders.');
        $renamed = $service->rename('local', 'Campaigns', 'Launch');

        $this->assertSame('Launch', $renamed['path']);
        $this->assertDirectoryDoesNotExist($this->root . '/files/Campaigns');
        $this->assertFileExists($this->root . '/files/Launch/Images/logo.txt');
        $this->assertSame(['Launch', 'Launch/Images'], array_column($service->folders('local'), 'path'));
        $this->assertSame(['Launch/Images/logo.txt'], array_column($service->browse('local', '', ['collection' => 'starred'])['items'], 'path'));
    }

    public function test_it_exposes_folder_details_and_rename_through_the_v2_api(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Campaigns');
        $service->upload('local', 'Campaigns', UploadedFile::fake()->createWithContent('brief.txt', '1234'));

        $this->withoutMiddleware();

        $this->getJson('/api/v2/file-manager/assets/details?storage=local&path=Campaigns')
            ->assertOk()
            ->assertJsonPath('data.fileCount', 1)
            ->assertJsonPath('data.bytes', 4);

        $this->patchJson('/api/v2/file-manager/assets', [
            'storage' => 'local',
            'path' => 'Campaigns/brief.txt',
            'name' => 'final.txt',
        ])
            ->assertOk()
            ->assertJsonPath('data.path', 'Campaigns/final.txt');
    }


    public function test_it_moves_files_and_persists_the_starred_collection(): void
    {
        $service = app(FileManagerV2Storage::class);
        $service->makeDirectory('local', '', 'Incoming');
        $service->makeDirectory('local', '', 'Archive');
        $service->upload('local', 'Incoming', UploadedFile::fake()->createWithContent('brief.txt', 'brief'));

        $this->assertTrue($service->toggleStar('local', 'Incoming/brief.txt')['starred']);
        $this->assertSame(['Incoming/brief.txt'], array_column($service->browse('local', '', ['collection' => 'starred'])['items'], 'path'));

        $moved = $service->move('local', ['Incoming/brief.txt'], 'Archive');

        $this->assertSame('Archive/brief.txt', $moved['items'][0]['path']);
        $this->assertFileExists($this->root . '/files/Archive/brief.txt');
        $this->assertSame(['Archive/brief.txt'], array_column($service->browse('local', '', ['collection' => 'starred'])['items'], 'path'));
        $this->assertSame(['Archive/brief.txt'], array_column($service->browse('local', '', ['collection' => 'recent'])['items'], 'path'));
        $this->assertFalse($service->toggleStar('local', 'Archive/brief.txt')['starred']);
        $this->assertCount(0, $service->browse('local', '', ['collection' => 'starred'])['items']);
    }


    public function test_the_v2_storage_guard_is_registered_before_the_legacy_storage_fallback(): void
    {
        $route = Route::getRoutes()->match(request()->create('/storage/filemanager_v2/runtime/uploads/probe.part'));

        $this->assertSame('filemanager_v2.storage_guard', $route->getName());
    }

    public function test_the_new_laravel_page_references_the_compiled_v2_assets(): void
    {
        $this->withoutMiddleware();

        $this->get(route('filemanager_v2.index'))
            ->assertOk()
            ->assertSee('filemanager-v2.js')
            ->assertSee('FILEMANAGER_V2_CONFIG');
    }
}
