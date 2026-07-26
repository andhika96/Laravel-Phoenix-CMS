<?php

namespace Tests\Feature\FileManagerV2;

use App\Services\FileManagerV2\FileManagerV2Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FileManagerV2ConnectionSettingsTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/filemanager_v2_connection_settings');
        File::deleteDirectory($this->root);

        config([
            'filemanager_v2.local.files_root' => $this->root . '/files',
            'filemanager_v2.local.cache_root' => $this->root . '/cache',
            'filemanager_v2.local.metadata_root' => $this->root . '/metadata',
            'filemanager_v2.local.runtime_root' => $this->root . '/runtime',
            'filemanager_v2.settings.root' => $this->root . '/settings',
            'filemanager_v2.cache.store' => 'array',
            'filemanager_v2.connections.r2.enabled' => false,
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->root);

        parent::tearDown();
    }

    public function test_settings_persist_a_local_quota_and_keep_its_root_inside_v2_storage(): void
    {
        $storage = app(FileManagerV2Storage::class);

        $storage->saveSettings([
            'defaultStorage' => 'local',
            'connections' => [[
                'id' => 'local',
                'type' => 'local',
                'name' => 'Project assets',
                'enabled' => true,
                'root' => 'Team A',
                'quotaBytes' => 5,
            ]],
            'upload' => [
                'maxFileSize' => 10,
                'chunkSize' => 4,
                'chunkThreshold' => 8,
                'maxParallel' => 2,
                'retryAttempts' => 1,
            ],
        ]);

        $profile = collect($storage->profiles())->firstWhere('id', 'local');

        $this->assertSame('Project assets', $profile['name']);
        $this->assertSame(5, $profile['quotaBytes']);
        $this->assertSame('Team A', $profile['root']);

        $storage->upload('local', '', UploadedFile::fake()->createWithContent('small.txt', '1234'));
        $this->assertFileExists($this->root . '/files/Team A/small.txt');

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $storage->upload('local', '', UploadedFile::fake()->createWithContent('too-large.txt', '123456'));
    }

    public function test_non_local_connection_can_be_removed_from_persisted_settings(): void
    {
        $storage = app(FileManagerV2Storage::class);

        $this->assertNotNull(collect($storage->settings()['connections'])->firstWhere('id', 'r2'));

        $settings = $storage->saveSettings([
            'defaultStorage' => 'local',
            'connections' => [[
                'id' => 'local',
                'type' => 'local',
                'name' => 'Local storage',
                'enabled' => true,
                'root' => '',
                'quotaBytes' => 1024,
            ]],
            'upload' => [
                'maxFileSize' => 1024,
                'chunkSize' => 8,
                'chunkThreshold' => 16,
                'maxParallel' => 2,
                'retryAttempts' => 2,
            ],
        ]);

        $this->assertNull(collect($settings['connections'])->firstWhere('id', 'r2'));
        $this->assertNull(collect($storage->settings()['connections'])->firstWhere('id', 'r2'));
    }

    public function test_default_connection_cannot_be_removed(): void
    {
        $storage = app(FileManagerV2Storage::class);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $storage->saveSettings([
            'defaultStorage' => 'r2',
            'connections' => [[
                'id' => 'local',
                'type' => 'local',
                'name' => 'Local storage',
                'enabled' => true,
                'root' => '',
                'quotaBytes' => 1024,
            ]],
            'upload' => [
                'maxFileSize' => 1024,
                'chunkSize' => 8,
                'chunkThreshold' => 16,
                'maxParallel' => 2,
                'retryAttempts' => 2,
            ],
        ]);
    }
    public function test_cloud_credentials_are_encrypted_and_never_returned_by_settings(): void
    {
        $storage = app(FileManagerV2Storage::class);

        $storage->saveSettings([
            'defaultStorage' => 'local',
            'connections' => [
                [
                    'id' => 'local',
                    'type' => 'local',
                    'name' => 'Local storage',
                    'enabled' => true,
                    'root' => '',
                    'quotaBytes' => 1024,
                ],
                [
                    'id' => 'delivery-s3',
                    'type' => 's3_compatible',
                    'name' => 'Delivery objects',
                    'enabled' => true,
                    'root' => 'media',
                    'quotaBytes' => 2048,
                    'bucket' => 'delivery-assets',
                    'region' => 'us-east-1',
                    'endpoint' => 'https://objects.example.test',
                    'usePathStyle' => true,
                    'accessKey' => 'access-key-for-test',
                    'secretKey' => 'secret-key-for-test',
                ],
            ],
            'upload' => [
                'maxFileSize' => 1024,
                'chunkSize' => 8,
                'chunkThreshold' => 16,
                'maxParallel' => 2,
                'retryAttempts' => 2,
            ],
        ]);

        $cloud = collect($storage->settings()['connections'])->firstWhere('id', 'delivery-s3');
        $persisted = File::get($this->root . '/settings/connections.json');

        $this->assertSame('s3_compatible', $cloud['type']);
        $this->assertTrue($cloud['credentialsConfigured']);
        $this->assertArrayNotHasKey('accessKey', $cloud);
        $this->assertArrayNotHasKey('secretKey', $cloud);
        $this->assertStringNotContainsString('access-key-for-test', $persisted);
        $this->assertStringNotContainsString('secret-key-for-test', $persisted);
    }

    public function test_settings_api_hides_secrets_but_allows_local_connection_testing(): void
    {
        $this->withoutMiddleware();

        $payload = [
            'defaultStorage' => 'local',
            'connections' => [[
                'id' => 'local',
                'type' => 'local',
                'name' => 'Local storage',
                'enabled' => true,
                'root' => '',
                'quotaBytes' => 1024,
            ]],
            'upload' => [
                'maxFileSize' => 1024,
                'chunkSize' => 8,
                'chunkThreshold' => 16,
                'maxParallel' => 2,
                'retryAttempts' => 2,
            ],
        ];

        $this->putJson('/api/v2/file-manager/settings', $payload)
            ->assertOk()
            ->assertJsonPath('data.defaultStorage', 'local')
            ->assertJsonPath('data.connections.0.quotaBytes', 1024)
            ->assertJsonMissingPath('data.connections.0.secretKey');

        $this->postJson('/api/v2/file-manager/settings/test', ['storage' => 'local'])
            ->assertOk()
            ->assertJsonPath('data.connected', true);
    }

    public function test_settings_ui_hides_shared_and_only_exposes_operational_controls(): void
    {
        $live = file_get_contents(resource_path('js/filemanager_v2/data/live.js'));
        $modal = file_get_contents(resource_path('js/filemanager_v2/components/StorageSettingsModal.vue'));

        $this->assertStringNotContainsString("{ id: 'shared'", $live);
        $this->assertStringContainsString('Add connection', $modal);
        $this->assertStringContainsString('removeConnection', $modal);
        $this->assertStringContainsString('Saving settings', $modal);
        $this->assertStringContainsString('saveHandler', $modal);
        $this->assertStringContainsString('Automatic retries', $modal);
        $this->assertStringNotContainsString('Verify checksum', $modal);
        $this->assertStringNotContainsString('Resumable uploads', $modal);
        $this->assertStringNotContainsString('Performance</button>', $modal);
    }
}
