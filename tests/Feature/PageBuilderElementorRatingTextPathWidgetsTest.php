<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PageBuilderElementorRatingTextPathWidgetsTest extends TestCase
{
    private const WIDGETS = [
        'rating' => ['label' => 'Rating', 'directory' => 'rating', 'view' => 'render_rating'],
        'text_path' => ['label' => 'Text Path', 'directory' => 'text-path', 'view' => 'render_text_path'],
    ];

    public function test_rating_and_text_path_are_registered_as_general_widgets(): void
    {
        foreach (self::WIDGETS as $type => $widget) {
            $module = config("pagebuilder_elementor_widgets.{$type}");

            $this->assertIsArray($module, "Missing {$type} widget module.");
            $this->assertSame($type, $module['type']);
            $this->assertSame($widget['label'], $module['label']);
            $this->assertSame('general', $module['category']);
            $this->assertTrue($module['toolbox']);
            $this->assertFileExists(public_path($module['definition']));
            $this->assertFileExists(public_path($module['canvas']));
            $this->assertFileExists(public_path($module['settings']));
            $this->assertTrue(view()->exists($module['view']));
        }
    }

    public function test_rating_maps_elementor_content_style_and_advanced_controls(): void
    {
        $settings = File::get(public_path('js/pagebuilder_elementor/widgets/general/rating/Settings.vue'));
        $definition = File::get(public_path('js/pagebuilder_elementor/widgets/general/rating/definition.js'));

        foreach (['Rating Scale', 'Rating', 'Icon', 'Alignment', 'Size', 'Spacing', 'Unmarked Color'] as $label) {
            $this->assertStringContainsString($label, $settings);
        }

        $this->assertStringContainsString('editor.openIconLibrary(node)', $settings);
        $this->assertStringContainsString('editor.chooseRatingSvg(node)', $settings);
        $this->assertStringContainsString('class="pb-general-icon-picker"', $settings);
        $this->assertStringNotContainsString('pb-icon-picker-field pb-rating-icon-picker', $settings);
        $this->assertStringContainsString('editor.widgetAdvancedControls', $settings);
        $this->assertStringContainsString('pb-tab-nav', $settings);
        $this->assertStringContainsString('pb-widget-settings--general-new', $settings);
        $this->assertStringContainsString("ratingScale: 5", $definition);
        $this->assertStringContainsString("rating: 5", $definition);
        $this->assertStringContainsString("iconClass: 'fas fa-star'", $definition);
    }

    public function test_text_path_maps_elementor_controls_and_conditional_sections(): void
    {
        $settings = File::get(public_path('js/pagebuilder_elementor/widgets/general/text-path/Settings.vue'));
        $definition = File::get(public_path('js/pagebuilder_elementor/widgets/general/text-path/definition.js'));

        foreach (['Text', 'Path Type', 'Custom SVG', 'Link', 'Alignment', 'Text Direction', 'Show Path', 'Rotate', 'Typography', 'Text Stroke', 'Word Spacing', 'Starting Point (%)', 'Hover Animation', 'Transition Duration'] as $label) {
            $this->assertStringContainsString($label, $settings);
        }

        $this->assertStringContainsString("node.settings.pathType==='custom'", $settings);
        $this->assertStringContainsString('v-if="node.settings.showPath"', $settings);
        $this->assertStringContainsString('editor.typographyControl', $settings);
        $this->assertStringContainsString('editor.textStrokeControl', $settings);
        $this->assertStringContainsString('editor.linkControl', $settings);
        $this->assertStringContainsString('editor.widgetAdvancedControls', $settings);
        $this->assertStringContainsString('pb-tab-nav', $settings);
        $canvas = File::get(public_path('js/pagebuilder_elementor/widgets/general/text-path/Canvas.vue'));
        $this->assertStringContainsString(':viewBox="viewBox"', $canvas);
        $this->assertStringContainsString("viewBox:'0 0 250 42.4994'", $canvas);
        $this->assertStringContainsString("pathType: 'wave'", $definition);
        $this->assertStringContainsString("text: 'Add Your Curvy Text Here'", $definition);
        $this->assertStringContainsString("textFontSize: '20px'", $definition);
    }

    public function test_shared_editor_runtime_supports_rating_icon_selection(): void
    {
        $app = File::get(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertStringContainsString("rating: 'Rating'", $app);
        $this->assertStringContainsString("text_path: 'Text Path'", $app);
        $this->assertStringContainsString("['counter', 'progress_bar', 'testimonial', 'social_icons', 'alert', 'rating', 'text_path']", $app);
        $this->assertStringContainsString("node.type === 'rating'", $app);
        $this->assertStringContainsString('chooseRatingSvg', $app);
    }

    public function test_rating_frontend_renders_fractional_accessible_output(): void
    {
        $canvas = File::get(public_path('js/pagebuilder_elementor/widgets/general/rating/Canvas.vue'));
        $html = view('pagebuilder_elementor.partials.render_rating', [
            'node' => [
                'id' => 'rating-example',
                'type' => 'rating',
                'settings' => ['ratingScale' => 5, 'rating' => 3.5, 'iconClass' => 'fas fa-star'],
            ],
        ])->render();

        $this->assertStringContainsString('el-widget-rating', $html);
        $this->assertStringContainsString('role="img"', $html);
        $this->assertStringContainsString('aria-label="Rated 3.5 out of 5"', $html);
        $this->assertSame(5, substr_count($html, 'pb-rating__item'));
        $this->assertStringContainsString('--pb-rating-fill:50%', $html);
        $this->assertStringContainsString('clip-path:inset(0 calc(100% - var(--pb-rating-fill)) 0 0)', $canvas);
        $this->assertStringNotContainsString('width:var(--pb-rating-fill)', $canvas);
        $this->assertStringContainsString('clip-path:inset(0 calc(100% - var(--pb-rating-fill)) 0 0)', $html);
        $this->assertStringNotContainsString('width:var(--pb-rating-fill)', $html);
    }

    public function test_text_path_frontend_renders_svg_path_link_and_path_styles_safely(): void
    {
        $html = view('pagebuilder_elementor.partials.render_text_path', [
            'node' => [
                'id' => 'text-path-example',
                'type' => 'text_path',
                'settings' => [
                    'text' => '<Curve & Flow>',
                    'pathType' => 'arc',
                    'linkUrl' => 'javascript:alert(1)',
                    'showPath' => true,
                    'pathColor' => '#f04438',
                    'pathStrokeColor' => '#101828',
                    'pathStrokeWidth' => '2px',
                    'textFontStyle' => 'italic',
                    'textTextTransform' => 'uppercase',
                    'textTextDecoration' => 'underline',
                    'textLineHeight' => '1.5em',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('el-widget-text-path', $html);
        $this->assertStringContainsString('<textPath', $html);
        $this->assertStringContainsString('viewBox="0 0 250.5 125.25"', $html);
        $this->assertStringContainsString('M.25,125.25a125,125,0,0,1,250,0', $html);
        $this->assertStringContainsString('&lt;Curve &amp; Flow&gt;', $html);
        $this->assertStringContainsString('fill:#f04438', $html);
        $this->assertStringContainsString('stroke:#101828', $html);
        $this->assertStringContainsString('--pb-text-font-style:italic', $html);
        $this->assertStringContainsString('--pb-text-transform:uppercase', $html);
        $this->assertStringContainsString('--pb-text-decoration:underline', $html);
        $this->assertStringContainsString('--pb-text-line-height:1.5em', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }
}
