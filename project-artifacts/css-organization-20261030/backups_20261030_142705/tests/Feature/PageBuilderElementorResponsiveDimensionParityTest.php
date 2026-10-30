<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageBuilderElementorResponsiveDimensionParityTest extends TestCase
{
    public function test_editor_exposes_standardized_responsive_dimension_controls_for_basic_widgets(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $imageSettings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/image/Settings.vue'));
        $dividerSettings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/divider/Settings.vue'));

        $this->assertIsString($appJs);
        $this->assertIsString($imageSettings);
        $this->assertIsString($dividerSettings);
        $this->assertStringContainsString("const sizeControlUnits = ['px', 'pt', 'em', 'rem', '%'];", $appJs);
        $this->assertStringContainsString('control-key="image-width"', $imageSettings);
        $this->assertStringContainsString('control-key="image-height"', $imageSettings);
        $this->assertStringContainsString('editor.openControlResponsiveMenu(controlKey)', $imageSettings);
        $this->assertStringContainsString("editor.openControlResponsiveMenu('divider-width')", $dividerSettings);
        $this->assertStringContainsString('v-for="unit in editor.sizeControlUnits"', $imageSettings);
        $this->assertStringContainsString('v-for="unit in editor.sizeControlUnits"', $dividerSettings);
        $this->assertStringContainsString('<div class="pb-range-value-row">', $imageSettings);
        $this->assertStringContainsString('<div class="pb-range-value-row">', $dividerSettings);
        $this->assertStringNotContainsString(
            '<option value="vw">vw</option>',
            $imageSettings."\n".$dividerSettings
        );
    }

    public function test_editor_exposes_elementor_like_spacing_units_for_container_margin_and_padding(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $containerSettings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/layout/container/Settings.vue'));
        $builderCss = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        $this->assertIsString($appJs);
        $this->assertIsString($containerSettings);
        $this->assertIsString($builderCss);
        $this->assertStringContainsString("const spacingControlUnits = ['px', '%', 'em', 'rem', 'vw'];", $appJs);
        $this->assertStringContainsString("editor.spacingUnit(node, 'margin')", $containerSettings);
        $this->assertStringContainsString("editor.setSpacingUnit(node, 'margin', \$event.target.value)", $containerSettings);
        $this->assertStringContainsString("editor.spacingUnit(node, 'padding')", $containerSettings);
        $this->assertStringContainsString("editor.setSpacingUnit(node, 'padding', \$event.target.value)", $containerSettings);
        $this->assertStringContainsString("editor.onSpacingSideInput(node, 'margin', 'Top', \$event)", $containerSettings);
        $this->assertStringContainsString("editor.onSpacingSideInput(node, 'padding', 'Top', \$event)", $containerSettings);
        $this->assertStringContainsString('v-for="unit in editor.spacingControlUnits"', $containerSettings);
        $this->assertStringContainsString('pb-edge-unit-select', $containerSettings);
        $this->assertStringContainsString('pb-four-sides-with-link', $containerSettings);
        $this->assertStringContainsString('pb-side-link-cell', $containerSettings);
        $this->assertStringContainsString('.pb-panel.left .pb-layout-settings .pb-edge-unit-select', $builderCss);
        $this->assertStringContainsString('width: 54px;', $builderCss);
        $this->assertStringContainsString('border: 1px solid #cfd6e3;', $builderCss);
        $this->assertStringContainsString('border-radius: 6px;', $builderCss);
        $this->assertStringContainsString('justify-content: flex-end;', $builderCss);
        $this->assertStringContainsString('grid-template-columns: repeat(4, minmax(0, 1fr)) 28px;', $builderCss);
        $this->assertStringContainsString('.pb-panel.left .pb-layout-settings .pb-side-link-cell .pb-link-btn', $builderCss);
    }

    public function test_editor_centers_sidebar_tab_icon_labels(): void
    {
        $builderCss = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        $this->assertIsString($builderCss);
        $this->assertStringContainsString('.pb-panel.left .pb-tab-btn.pb-tab-btn-icon', $builderCss);
        $this->assertStringContainsString('min-height: 48px;', $builderCss);
        $this->assertStringContainsString('justify-content: center;', $builderCss);
        $this->assertStringContainsString('padding: 9px 4px 6px;', $builderCss);
        $this->assertStringContainsString('.pb-panel.left .pb-tab-btn-icon span', $builderCss);
        $this->assertStringContainsString('line-height: 1.25;', $builderCss);
    }

    public function test_editor_adds_breathing_room_between_sidebar_tabs_and_first_group_label(): void
    {
        $builderCss = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        $this->assertIsString($builderCss);
        $this->assertMatchesRegularExpression(
            '/\\.pb-panel\\.left \\.pb-layout-settings \\.pb-tab-content,\\s*\\.pb-panel\\.left \\.pb-grid-settings \\.pb-tab-content \\{\\s*padding-top: 10px;/s',
            $builderCss
        );
    }

    public function test_editor_centers_layout_accordion_labels_and_open_content_spacing(): void
    {
        $builderCss = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        $this->assertIsString($builderCss);
        $this->assertStringContainsString('.pb-panel.left .pb-layout-settings .pb-collapsible summary', $builderCss);
        $this->assertStringContainsString('min-height: 48px;', $builderCss);
        $this->assertStringContainsString('display: flex;', $builderCss);
        $this->assertStringContainsString('align-items: center;', $builderCss);
        $this->assertStringContainsString('padding: 0;', $builderCss);
        $this->assertStringContainsString('.pb-panel.left .pb-layout-settings .pb-collapsible-body', $builderCss);
        $this->assertStringContainsString('padding: 8px 0 4px;', $builderCss);
    }

    public function test_editor_bolds_control_names_without_bolding_existing_headings_or_small_captions(): void
    {
        $builderCss = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        $this->assertIsString($builderCss);
        $marker = 'Control-name emphasis: preserve existing bold headings and regular-weight captions.';
        $this->assertStringContainsString($marker, $builderCss);

        $start = strpos($builderCss, '/* '.$marker.' */');
        $end = strpos($builderCss, '}', $start);
        $controlNameRule = substr($builderCss, $start, $end - $start + 1);

        $this->assertStringContainsString('.pb-panel.left .pb-form-group .pb-form-label', $controlNameRule);
        $this->assertStringContainsString('.pb-panel.left .pb-widget-advanced-controls .pb-advanced-control-head > span', $controlNameRule);
        $this->assertStringContainsString('.pb-panel.left .pb-widget-advanced-controls .pb-advanced-field > span', $controlNameRule);
        $this->assertStringContainsString('.pb-panel.left .pb-widget-advanced-controls .pb-advanced-two-fields > label > span', $controlNameRule);
        $this->assertStringContainsString('.pb-panel.left .pb-widget-advanced-controls .pb-advanced-four-fields > label > span', $controlNameRule);
        $this->assertStringContainsString('.pb-panel.left .pb-widget-advanced-controls .pb-advanced-toggle > span', $controlNameRule);
        $this->assertStringContainsString('font-weight: 600;', $controlNameRule);
        $this->assertStringNotContainsString('pb-collapsible summary', $controlNameRule);
        $this->assertStringNotContainsString('pb-advanced-edge-fields', $controlNameRule);
        $this->assertStringNotContainsString('pb-side-input', $controlNameRule);
    }

    public function test_frontend_renderer_accepts_elementor_like_spacing_units_for_layout_nodes(): void
    {
        $node = [
            'id' => 'container-spacing-units',
            'type' => 'container',
            'settings' => [
                'displayType' => 'flex',
                'paddingTop' => '2em',
                'paddingRight' => '3rem',
                'paddingBottom' => '4vw',
                'paddingLeft' => '5%',
                'marginTop' => '1em',
                'marginRight' => '2rem',
                'marginBottom' => '3vw',
                'marginLeft' => '4%',
            ],
            'columns' => [
                ['id' => 'col-1', 'children' => []],
            ],
        ];

        $html = view('pagebuilder_elementor.partials.render_node', ['node' => $node])->render();

        $this->assertStringContainsString('id="pb-node-container-spacing-units"', $html);
        $this->assertStringContainsString('padding-top:2em', $html);
        $this->assertStringContainsString('padding-right:3rem', $html);
        $this->assertStringContainsString('padding-bottom:4vw', $html);
        $this->assertStringContainsString('padding-left:5%', $html);
        $this->assertStringContainsString('margin-top:1em', $html);
        $this->assertStringContainsString('margin-right:2rem', $html);
        $this->assertStringContainsString('margin-bottom:3vw', $html);
        $this->assertStringContainsString('margin-left:4%', $html);
    }

    public function test_editor_exposes_video_aspect_ratio_as_preset_select(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $videoSettings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/video/Settings.vue'));

        $this->assertIsString($appJs);
        $this->assertIsString($videoSettings);
        $this->assertStringContainsString('const videoAspectRatioOptions = [', $appJs);
        $this->assertStringContainsString("{ value: '16/9', label: '16:9 (Widescreen)' }", $appJs);
        $this->assertStringContainsString("{ value: '4/3', label: '4:3 (Standard)' }", $appJs);
        $this->assertStringContainsString("{ value: '1/1', label: '1:1 (Square)' }", $appJs);
        $this->assertStringContainsString("{ value: '9/16', label: '9:16 (Vertical)' }", $appJs);
        $this->assertStringContainsString('<label class="pb-form-label mb-0">Aspect Ratio</label>', $videoSettings);
        $this->assertStringContainsString(':value="editor.videoAspectRatioValue(node)"', $videoSettings);
        $this->assertStringContainsString('@change="editor.setVideoAspectRatioValue(node, $event.target.value)"', $videoSettings);
        $this->assertStringContainsString('v-for="option in editor.videoAspectRatioOptions"', $videoSettings);
        $this->assertStringNotContainsString('<input class="pb-input" v-model="node.settings.ratio">', $videoSettings);
    }

    public function test_editor_exposes_video_aspect_ratio_as_responsive_select(): void
    {
        $videoSettings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/video/Settings.vue'));

        $this->assertIsString($videoSettings);
        $this->assertStringContainsString("editor.openControlResponsiveMenu('video-ratio')", $videoSettings);
        $this->assertStringContainsString(":value=\"editor.videoAspectRatioValue(node)\"", $videoSettings);
        $this->assertStringContainsString("@change=\"editor.setVideoAspectRatioValue(node, \$event.target.value)\"", $videoSettings);
    }

    public function test_video_widget_preview_reads_responsive_ratio_values(): void
    {
        $videoVue = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/video/Canvas.vue'));

        $this->assertIsString($videoVue);
        $this->assertStringContainsString('responsiveDevice: {', $videoVue);
        $this->assertStringContainsString("const ratio = this.responsiveValue('ratio', '16/9');", $videoVue);
        $this->assertStringContainsString('responsiveValue(base, fallback = \'\') {', $videoVue);
    }

    public function test_frontend_renderer_emits_responsive_video_ratio_rules(): void
    {
        $node = [
            'id' => 'video-responsive',
            'type' => 'video',
            'settings' => [
                'sourceType' => 'youtube',
                'youtubeEmbed' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'ratio' => '16/9',
                'ratioTablet' => '1/1',
                'ratioMobile' => '9/16',
            ],
        ];

        $html = view('pagebuilder_elementor.partials.render_node', ['node' => $node])->render();

        $this->assertStringContainsString('id="pb-node-video-responsive"', $html);
        $this->assertStringContainsString('padding-bottom:56.25%', $html);
        $this->assertStringContainsString('@media (max-width: 1024px){#pb-node-video-responsive > .el-widget-video-wrapper{padding-bottom:100%}}', $html);
        $this->assertStringContainsString('@media (max-width: 767px){#pb-node-video-responsive > .el-widget-video-wrapper{padding-bottom:177.7778%}}', $html);
    }

    #[DataProvider('responsiveBasicWidgetProvider')]
    public function test_frontend_renderer_emits_responsive_dimension_rules_for_basic_widgets(
        array $node,
        string $expectedId,
        string $expectedTabletRule,
        string $expectedMobileRule
    ): void {
        $html = view('pagebuilder_elementor.partials.render_node', ['node' => $node])->render();

        $this->assertStringContainsString('id="' . $expectedId . '"', $html);
        $this->assertStringContainsString($expectedTabletRule, $html);
        $this->assertStringContainsString($expectedMobileRule, $html);
    }

    public static function responsiveBasicWidgetProvider(): array
    {
        return [
            'image' => [
                [
                    'id' => 'img-responsive',
                    'type' => 'image',
                    'settings' => [
                        'src' => 'https://placehold.co/640x360',
                        'alt' => 'Image',
                        'width' => '100%',
                        'height' => 'auto',
                        'widthTablet' => '75%',
                        'widthMobile' => '50%',
                        'heightTablet' => '240px',
                        'heightMobile' => '120px',
                    ],
                ],
                'pb-node-img-responsive',
                '@media (max-width: 1024px){#pb-node-img-responsive > img{width:75%;height:240px}}',
                '@media (max-width: 767px){#pb-node-img-responsive > img{width:50%;height:120px}}',
            ],
            'divider' => [
                [
                    'id' => 'divider-responsive',
                    'type' => 'divider',
                    'settings' => [
                        'style' => 'solid',
                        'width' => '100%',
                        'widthTablet' => '80%',
                        'widthMobile' => '60%',
                        'thickness' => 2,
                        'color' => '#d0d7e6',
                    ],
                ],
                'pb-node-divider-responsive',
                '@media (max-width: 1024px){#pb-node-divider-responsive > hr{width:80%}}',
                '@media (max-width: 767px){#pb-node-divider-responsive > hr{width:60%}}',
            ],
            'spacer' => [
                [
                    'id' => 'spacer-responsive',
                    'type' => 'spacer',
                    'settings' => [
                        'height' => '32px',
                        'heightTablet' => '48px',
                        'heightMobile' => '24px',
                    ],
                ],
                'pb-node-spacer-responsive',
                '@media (max-width: 1024px){#pb-node-spacer-responsive{height:48px}}',
                '@media (max-width: 767px){#pb-node-spacer-responsive{height:24px}}',
            ],
        ];
    }
}
