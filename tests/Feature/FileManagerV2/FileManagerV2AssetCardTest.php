<?php

namespace Tests\Feature\FileManagerV2;

use Tests\TestCase;

class FileManagerV2AssetCardTest extends TestCase
{
    public function test_starred_assets_have_a_persistent_card_indicator(): void
    {
        $template = file_get_contents(resource_path('js/filemanager_v2/components/AssetCard.vue'));
        $styles = file_get_contents(resource_path('js/filemanager_v2/styles.css'));

        $this->assertStringContainsString('<span v-if="asset.starred" class="asset-starred-indicator"', $template);
        $this->assertStringContainsString('bi-star-fill', $template);
        $this->assertStringContainsString('.asset-starred-indicator {', $styles);
        $this->assertStringContainsString('pointer-events: none;', $styles);
        $this->assertStringContainsString('color: #fff; background: #f3b83f;', $styles);
    }

    public function test_checklist_is_an_interactive_selection_button_without_a_leaked_toolbar_binding(): void
    {
        $card = file_get_contents(resource_path('js/filemanager_v2/components/AssetCard.vue'));
        $app = file_get_contents(resource_path('js/filemanager_v2/App.vue'));

        $this->assertMatchesRegularExpression('/<button\\s+class="asset-check"/s', $card);
        $this->assertStringContainsString('@click.stop="emit(\'select\', asset, $event)"', $card);
        $this->assertStringContainsString(':aria-pressed="selected"', $card);
        $this->assertStringNotContainsString('@click="emit(\'select\', asset, $event)"', $card);
        $this->assertStringNotContainsString('@keydown.enter="emit(\'select\', asset, $event)"', $card);
        $this->assertStringContainsString('@click="asset.type === \'folder\' && emit(\'open-folder\', asset)"', $card);
        $this->assertStringNotContainsString('@dblclick="asset.type === \'folder\' && emit(\'open-folder\', asset)"', $card);
        $this->assertStringNotContainsString('tabindex="0"', $card);
        $this->assertStringContainsString('next.has(asset.id) ? next.delete(asset.id) : next.add(asset.id);', $app);
        $this->assertStringNotContainsString('event.ctrlKey || event.metaKey', $app);
        $this->assertSame(1, preg_match('/<section v-if="selectedCount" class="selection-toolbar">(.*?)<\\/section>/s', $app, $matches));
        $this->assertStringNotContainsString('@open-folder=', $matches[1]);
    }
}
