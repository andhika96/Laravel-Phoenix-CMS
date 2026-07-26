<?php

namespace Tests\Feature\FileManagerV2;

use Tests\TestCase;

class FileManagerV2BrowserHistoryTest extends TestCase
{
    public function test_folder_navigation_persists_and_restores_browser_history_state(): void
    {
        $app = file_get_contents(resource_path('js/filemanager_v2/App.vue'));

        $this->assertStringContainsString("const FILE_MANAGER_HISTORY_KEY = 'fileManagerV2';", $app);
        $this->assertStringContainsString("url.searchParams.set('fm_storage', state.storage);", $app);
        $this->assertStringContainsString("url.searchParams.set('fm_collection', state.collection);", $app);
        $this->assertStringContainsString("url.searchParams.set('fm_folder', state.folder);", $app);
        $this->assertStringContainsString('window.history.pushState', $app);
        $this->assertStringContainsString('window.history.replaceState', $app);
        $this->assertStringContainsString("window.addEventListener('popstate', handleBrowserHistory);", $app);
        $this->assertStringContainsString("window.removeEventListener('popstate', handleBrowserHistory);", $app);
        $this->assertStringContainsString("changeFolder(id, { historyMode = 'push' } = {})", $app);
        $this->assertStringContainsString("changeFolder(state.folder, { historyMode: 'none' })", $app);
    }
}
