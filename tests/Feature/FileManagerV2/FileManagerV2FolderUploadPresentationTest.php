<?php

namespace Tests\Feature\FileManagerV2;

use Tests\TestCase;

class FileManagerV2FolderUploadPresentationTest extends TestCase
{
    public function test_folder_batch_defers_asset_grid_updates_until_the_batch_finishes(): void
    {
        $app = file_get_contents(resource_path('js/filemanager_v2/App.vue'));

        $this->assertSame(1, preg_match('/onItemDone: \(job, asset\) => \{(.*?)\n    \},\n    onStartError/s', $app, $matches));
        $this->assertStringContainsString('item.asset = asset;', $matches[1]);
        $this->assertStringNotContainsString('upsertAsset(asset);', $matches[1]);
        $this->assertStringContainsString('scheduleFolderBatchRefresh(batch.storage);', $app);
    }

    public function test_upload_client_turns_non_json_gateway_responses_into_upload_errors(): void
    {
        $live = file_get_contents(resource_path('js/filemanager_v2/data/live.js'));

        $this->assertStringNotContainsString("const body = JSON.parse(request.responseText || '{}');", $live);
        $this->assertStringContainsString('Upload gagal (HTTP ${request.status}).', $live);
    }

    public function test_upload_panel_uses_a_static_svg_placeholder_for_completed_batch_items(): void
    {
        $panel = file_get_contents(resource_path('js/filemanager_v2/components/UploadPanel.vue'));

        $this->assertStringContainsString('file-upload-placeholder.svg', $panel);
        $this->assertStringContainsString('class="upload-file-placeholder"', $panel);
    }

    public function test_svg_assets_use_the_static_placeholder_while_raster_previews_are_lazy_loaded(): void
    {
        $live = file_get_contents(resource_path('js/filemanager_v2/data/live.js'));
        $card = file_get_contents(resource_path('js/filemanager_v2/components/AssetCard.vue'));

        $this->assertStringContainsString("import uploadFilePlaceholder from '../assets/file-upload-placeholder.svg';", $live);
        $this->assertStringContainsString('src: isSvgAsset(asset) ? uploadFilePlaceholder : asset.previewUrl,', $live);
        $this->assertStringContainsString('loading="lazy"', $card);
        $this->assertStringContainsString('decoding="async"', $card);
    }
}
