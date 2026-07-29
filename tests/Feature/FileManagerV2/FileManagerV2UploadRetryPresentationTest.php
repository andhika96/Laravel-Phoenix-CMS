<?php

namespace Tests\Feature\FileManagerV2;

use Tests\TestCase;

class FileManagerV2UploadRetryPresentationTest extends TestCase
{
    public function test_upload_retry_uses_five_total_attempts_and_reports_retry_state(): void
    {
        $config = file_get_contents(config_path('filemanager_v2.php'));
        $live = file_get_contents(resource_path('js/filemanager_v2/data/live.js'));
        $folderBatch = file_get_contents(resource_path('js/filemanager_v2/data/folderUploadBatch.js'));
        $filePond = file_get_contents(resource_path('js/filemanager_v2/components/FilePondUploadEngine.vue'));
        $panel = file_get_contents(resource_path('js/filemanager_v2/components/UploadPanel.vue'));
        $app = file_get_contents(resource_path('js/filemanager_v2/App.vue'));
        $controller = file_get_contents(app_path('Http/Controllers/Api/V2/FileManagerV2Controller.php'));
        $storage = file_get_contents(app_path('Services/FileManagerV2/FileManagerV2Storage.php'));

        $this->assertStringContainsString("'retry_attempts' => (int) env('FILEMANAGER_V2_RETRY_ATTEMPTS', 5)", $config);
        $this->assertStringContainsString('const maxAttempts = Math.max(1, Number(uploadOptions.retryAttempts) || 1);', $live);
        $this->assertStringContainsString('onRetry = () => {}', $live);
        $this->assertStringContainsString('onRetry({ attempt: attempt + 2, maxAttempts, delayMs, error });', $live);
        $this->assertStringContainsString('const jitter = 0.85 + (Math.random() * 0.3);', $live);
        $this->assertStringContainsString('onRetry: ({ attempt, maxAttempts, delayMs, error }) =>', $folderBatch);
        $this->assertStringContainsString("status: 'queued',\n    error: '',\n    attempt: 1,\n    maxAttempts: maximumAttempts(),", str_replace("\r\n", "\n", $folderBatch));
        $this->assertStringContainsString("emit('retrying'", $filePond);
        $this->assertStringContainsString("'retrying'", $panel);
        $this->assertStringContainsString('Retrying {{ item.attempt }}/{{ item.maxAttempts }}', $panel);
        $this->assertStringContainsString('{{ retrySecondsRemaining(item) }}s &middot; {{ item.error }}', $panel);
        $this->assertStringContainsString('@retrying="onPondRetrying"', $app);
        $this->assertStringContainsString("'upload.retryAttempts' => ['required', 'integer', 'min:1', 'max:5']", $controller);
        $this->assertStringContainsString("'retry_attempts' => min(5, max(1, (int) (\$stored['retry_attempts'] ?? \$defaults['retry_attempts'] ?? 5)))", $storage);
    }
}
