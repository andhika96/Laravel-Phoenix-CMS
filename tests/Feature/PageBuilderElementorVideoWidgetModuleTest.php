<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorVideoWidgetModuleTest extends TestCase
{
    public function test_video_has_complete_modular_contract(): void
    {
        $catalog = config('pagebuilder_elementor_widgets');
        $this->assertArrayHasKey('video', $catalog);

        $module = $catalog['video'];
        $this->assertSame('Video', $module['label']);
        $this->assertSame('basic', $module['category']);
        $this->assertTrue($module['toolbox']);
        $this->assertFileExists(public_path($module['definition']));
        $this->assertFileExists(public_path($module['canvas']));
        $this->assertFileExists(public_path($module['settings']));
        $this->assertTrue(view()->exists($module['view']));

        $definition = file_get_contents(public_path($module['definition']));
        $settings = file_get_contents(public_path($module['settings']));
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        foreach (["type: 'video'", 'defaults', 'normalize(node)', 'sourceType', 'youtubeUrl', 'ratio'] as $marker) {
            $this->assertStringContainsString($marker, $definition);
        }
        foreach (['Source', 'Start Time', 'Aspect Ratio', 'Video Options', 'Image Overlay', 'CSS Class'] as $label) {
            $this->assertStringContainsString($label, $settings);
        }

        $this->assertStringNotContainsString("case 'video':", $app);
        $this->assertStringNotContainsString("<template v-if=\"selectedType==='video'\">", $app);
        $this->assertStringNotContainsString("video:          '/js/pagebuilder_elementor/widgets/basic/Video.vue'", $app);
        $this->assertStringNotContainsString("{ type:'video',       label:'Video'", $app);
    }

    public function test_video_frontend_dispatches_through_registered_view(): void
    {
        $html = view('pagebuilder_elementor.partials.render_node', [
            'node' => [
                'id' => 'registry-video',
                'type' => 'video',
                'settings' => [
                    'sourceType' => 'youtube',
                    'youtubeUrl' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'ratio' => '16/9',
                    'cssClass' => 'registry-video-class',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('el-widget-video', $html);
        $this->assertStringContainsString('registry-video-class', $html);
        $this->assertStringContainsString('youtube.com/embed/dQw4w9WgXcQ', $html);
        $this->assertStringContainsString('padding-bottom:56.25%', str_replace(' ', '', $html));
    }
}
