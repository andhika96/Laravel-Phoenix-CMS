<?php

namespace Tests\Feature\FileManagerV2;

use Tests\TestCase;

class FileManagerV2UploadCenterPresentationTest extends TestCase
{
    public function test_upload_center_can_reopen_history_after_the_panel_is_closed(): void
    {
        $app = file_get_contents(resource_path('js/filemanager_v2/App.vue'));

        $this->assertStringContainsString('const hasUploadCenter = computed', $app);
        $this->assertStringContainsString('function openUploads()', $app);
        $this->assertStringContainsString('v-if="hasUploadCenter"', $app);
        $this->assertStringContainsString('@click="openUploads"', $app);
        $this->assertSame(1, preg_match('/function closeUploads\(\) \{(.*?)\n\}/s', $app, $matches));
        $this->assertStringNotContainsString('uploads.value = uploads.value.filter', $matches[1]);
    }

    public function test_upload_panel_exposes_retry_all_only_after_the_queue_settles(): void
    {
        $panel = file_get_contents(resource_path('js/filemanager_v2/components/UploadPanel.vue'));
        $coordinator = file_get_contents(resource_path('js/filemanager_v2/data/folderUploadBatch.js'));

        $this->assertStringContainsString("'retry-failed'", $panel);
        $this->assertStringContainsString("emit('retry-failed')", $panel);
        $this->assertStringContainsString('retryFailed()', $coordinator);
    }
}
