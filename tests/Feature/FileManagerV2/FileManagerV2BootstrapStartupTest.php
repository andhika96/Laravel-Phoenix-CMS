<?php

namespace Tests\Feature\FileManagerV2;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FileManagerV2BootstrapStartupTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/filemanager_v2_bootstrap');
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

    public function test_bootstrap_returns_lightweight_profiles_and_defers_live_profile_details(): void
    {
        $this->withoutMiddleware();

        $this->getJson('/api/v2/file-manager/bootstrap')
            ->assertOk()
            ->assertJsonPath('data.profiles.0.id', 'local')
            ->assertJsonPath('data.profiles.0.usagePending', true);

        $this->getJson('/api/v2/file-manager/profiles')
            ->assertOk()
            ->assertJsonPath('data.0.id', 'local')
            ->assertJsonMissingPath('data.0.usagePending');
    }

    public function test_initial_workspace_shows_loading_and_refreshes_profile_details_after_assets_are_ready(): void
    {
        $app = file_get_contents(resource_path('js/filemanager_v2/App.vue'));
        $live = file_get_contents(resource_path('js/filemanager_v2/data/live.js'));

        $this->assertStringContainsString("workspaceLoadState.value = 'loading';", $app);
        $this->assertStringContainsString('void refreshStorageProfiles();', $app);
        $this->assertStringContainsString("const profiles = await request('/profiles');", $live);
    }
}
