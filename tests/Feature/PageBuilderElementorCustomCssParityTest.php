<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageBuilderElementorCustomCssParityTest extends TestCase
{
    public function test_editor_canvas_injects_live_page_custom_css(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertIsString($appJs);
        $this->assertMatchesRegularExpression(
            '/<component\s+v-if="customCss"\s+:is="\'style\'">\{\{\s*customCss\s*\}\}<\/component>/',
            $appJs
        );
    }

    public function test_custom_css_editor_normalizes_common_important_typo_before_apply(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertIsString($appJs);
        $this->assertStringContainsString('function applyCustomCssEditorChanges() {', $appJs);
        $this->assertStringContainsString("value.replace(/\\b1important\\b/gi, '!important');", $appJs);
        $this->assertStringContainsString('closeCustomCssEditor();', $appJs);
        $this->assertStringContainsString('@click="applyCustomCssEditorChanges"', $appJs);
    }

    #[DataProvider('basicWidgetCssClassProvider')]
    public function test_frontend_renderer_emits_basic_widget_css_class(array $node, string $expectedClass): void
    {
        $html = view('pagebuilder_elementor.partials.render_node', ['node' => $node])->render();

        preg_match_all('/class="([^"]*)"/', $html, $matches);

        $tokens = [];
        foreach ($matches[1] ?? [] as $classList) {
            $parts = preg_split('/\s+/', trim($classList)) ?: [];
            $tokens = array_merge($tokens, array_filter($parts));
        }

        $this->assertContains($expectedClass, $tokens);
    }

    public static function basicWidgetCssClassProvider(): array
    {
        return [
            'heading' => [
                [
                    'type' => 'heading',
                    'settings' => [
                        'text' => 'Heading',
                        'tag' => 'h2',
                        'align' => 'left',
                        'color' => '#101828',
                        'cssClass' => 'test-heading',
                    ],
                ],
                'test-heading',
            ],
            'heading_dot_prefixed' => [
                [
                    'type' => 'heading',
                    'settings' => [
                        'text' => 'Heading',
                        'tag' => 'h2',
                        'align' => 'left',
                        'color' => '#101828',
                        'cssClass' => '.test-heading',
                    ],
                ],
                'test-heading',
            ],
            'text_editor' => [
                [
                    'type' => 'text_editor',
                    'settings' => [
                        'html' => '<p>Text</p>',
                        'cssClass' => 'test-text-editor',
                    ],
                ],
                'test-text-editor',
            ],
            'image' => [
                [
                    'type' => 'image',
                    'settings' => [
                        'src' => 'https://placehold.co/640x360',
                        'alt' => 'Image',
                        'width' => '100%',
                        'height' => 'auto',
                        'cssClass' => 'test-image',
                    ],
                ],
                'test-image',
            ],
            'video' => [
                [
                    'type' => 'video',
                    'settings' => [
                        'sourceType' => 'youtube',
                        'youtubeEmbed' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                        'ratio' => '16/9',
                        'cssClass' => 'test-video',
                    ],
                ],
                'test-video',
            ],
            'divider' => [
                [
                    'type' => 'divider',
                    'settings' => [
                        'style' => 'solid',
                        'width' => '100%',
                        'thickness' => 2,
                        'color' => '#d0d7e6',
                        'cssClass' => 'test-divider',
                    ],
                ],
                'test-divider',
            ],
            'spacer' => [
                [
                    'type' => 'spacer',
                    'settings' => [
                        'height' => '32px',
                        'cssClass' => 'test-spacer',
                    ],
                ],
                'test-spacer',
            ],
        ];
    }
}
