<?php

namespace Tests\Feature\FileManagerV2;

use Tests\TestCase;

class FileManagerV2WorkspaceCacheTest extends TestCase
{
    public function test_storage_switch_restores_cached_workspace_and_exposes_loading_states(): void
    {
        $app = file_get_contents(resource_path('js/filemanager_v2/App.vue'));
        $styles = file_get_contents(resource_path('js/filemanager_v2/styles.css'));

        $this->assertStringContainsString('const WORKSPACE_CACHE_LIMIT = 24;', $app);
        $this->assertStringContainsString('const workspaceCache = new Map();', $app);
        $this->assertStringContainsString('const folderCache = new Map();', $app);
        $this->assertStringContainsString('function restoreWorkspaceFromCache(context, refreshFolders)', $app);
        $this->assertStringContainsString("workspaceLoadState.value = restored ? 'refreshing' : 'loading';", $app);
        $this->assertStringContainsString('clearWorkspaceCache();', $app);
        $this->assertStringContainsString("v-if=\"workspaceLoadState === 'loading'\"", $app);
        $this->assertStringContainsString('workspace-refresh-state', $app);
        $this->assertStringContainsString('workspace-loading', $styles);
        $this->assertStringContainsString('@keyframes workspace-spin', $styles);
    }
}
