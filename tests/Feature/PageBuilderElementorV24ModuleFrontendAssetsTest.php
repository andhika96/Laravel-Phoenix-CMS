<?php

namespace Tests\Feature;

use App\Models\Page_Builder\Page_Builder;
use Tests\TestCase;

class PageBuilderElementorV24ModuleFrontendAssetsTest extends TestCase
{
    public function test_frontend_emits_only_optional_assets_for_used_module_types(): void
    {
        $page = (new Page_Builder())->forceFill([
            'uri' => 'module-assets-page',
            'page_name' => 'Module Assets Page',
            'custom_css' => '',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
        ]);
        $nodes = [
            ['id' => 'tabs-runtime', 'type' => 'tabs', 'settings' => [], 'tabItems' => []],
            ['id' => 'code-runtime', 'type' => 'code_highlight', 'settings' => []],
            ['id' => 'heading-static', 'type' => 'heading', 'settings' => ['text' => 'Static']],
        ];

        $html = view('pagebuilder_elementor_v24.frontend_renderer', compact('page', 'nodes') + [
            'pageData' => $page,
        ])->render();

        $this->assertSame(1, substr_count($html, 'data-pb-module-runtime="tabs"'));
        $this->assertSame(1, substr_count($html, 'data-pb-module-runtime="code_highlight"'));
        $this->assertStringNotContainsString('data-pb-module-runtime="video_playlist"', $html);
        $this->assertStringNotContainsString('data-pb-module-runtime="heading"', $html);
        $this->assertStringContainsString('js/pagebuilder_elementor_v24/frontend-runtime.js', $html);
    }
}
