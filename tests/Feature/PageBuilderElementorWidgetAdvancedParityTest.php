<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorWidgetAdvancedParityTest extends TestCase
{
    public function test_shared_advanced_model_is_normalized_for_accordion_widgets(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSourceContains('function widgetAdvancedDefaults()', $appJs);
        $this->assertSourceContains('function normalizeWidgetAdvancedSettings(settings)', $appJs);
        $this->assertSourceContains('...widgetAdvancedDefaults()', $appJs);
        $this->assertSourceContains("normalizeWidgetAdvancedSettings(c.settings)", $appJs);
        $this->assertSourceContains("'/js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue'", $appJs);
        $this->assertSourceContains('<WidgetAdvancedControls', $appJs);
    }

    public function test_shared_advanced_controls_cover_every_approved_section(): void
    {
        $path = public_path('js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue');
        $this->assertFileExists($path);

        $component = file_get_contents($path);
        foreach ([
            'Layout',
            'Display Conditions',
            'Cache Settings',
            'Motion Effects',
            'Animate With AI',
            'Transform',
            'Background',
            'Border',
            'Mask',
            'Responsive',
            'Attributes',
            'Custom CSS',
        ] as $section) {
            $this->assertSourceContains($section, $component);
        }

        foreach ([
            'Scrolling Effects',
            'Vertical Scroll',
            'Horizontal Scroll',
            'Transparency',
            'Blur',
            'Rotate',
            'Scale',
            'Mouse Track',
            '3D Tilt',
            'Sticky',
            'Entrance Animation',
            'Hover Transition Duration',
            'Shape',
            'Hide On Desktop',
        ] as $control) {
            $this->assertSourceContains($control, $component);
        }

        $this->assertSourceContains('AI service is not connected', $component);
        $this->assertSourceContains("this.\$emit('unavailable-ai')", $component);
    }

    public function test_advanced_settings_render_safe_styles_attributes_and_scoped_css(): void
    {
        $html = view('pagebuilder_elementor.partials.render_node', ['node' => [
            'id' => 'advanced-accordion',
            'type' => 'accordion',
            'settings' => [
                'cssId' => 'faq-widget',
                'cssClass' => 'custom-faq another-class',
                'marginTop' => '12px',
                'paddingLeft' => '18px',
                'widthMode' => 'custom',
                'customWidth' => '640px',
                'position' => 'absolute',
                'horizontalOrientation' => 'right',
                'positionX' => '20px',
                'zIndex' => 9,
                'advancedBackgroundType' => 'classic',
                'advancedBackgroundColor' => '#112233',
                'advancedBorderType' => 'solid',
                'advancedBorderWidth' => '2px',
                'advancedBorderColor' => '#445566',
                'advancedBorderRadius' => '14px',
                'maskEnabled' => true,
                'maskShape' => 'circle',
                'transformRotate' => '4deg',
                'transformScale' => 1.05,
                'hideMobile' => true,
                'entranceAnimation' => 'fadeInUp',
                'scrollingEffects' => true,
                'verticalScrollEnabled' => true,
                'attributes' => [
                    ['name' => 'data-track', 'value' => 'faq'],
                    ['name' => 'aria-label', 'value' => 'Questions'],
                    ['name' => 'onclick', 'value' => 'alert(1)'],
                    ['name' => 'style', 'value' => 'display:none'],
                    ['name' => 'id', 'value' => 'override'],
                    ['name' => 'href', 'value' => 'javascript:alert(1)'],
                ],
                'customCssCode' => 'selector { outline: 2px solid red; }',
            ],
            'accordionItems' => [[
                'id' => 'item-one',
                'title' => 'Question',
                'children' => [],
            ]],
        ]])->render();

        $this->assertStringContainsString('id="faq-widget"', $html);
        $this->assertStringContainsString('custom-faq another-class', $html);
        $this->assertStringContainsString('margin-top:12px', $html);
        $this->assertStringContainsString('width:640px', $html);
        $this->assertStringContainsString('right:20px', $html);
        $this->assertStringContainsString('background-color:#112233', $html);
        $this->assertStringContainsString('border-style:solid', $html);
        $this->assertStringContainsString('-webkit-mask-image:', $html);
        $this->assertStringContainsString('data-pb-motion=', $html);
        $this->assertStringContainsString('data-track="faq"', $html);
        $this->assertStringContainsString('aria-label="Questions"', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringNotContainsString('javascript:alert', $html);
        $this->assertStringNotContainsString('id="override"', $html);
        $this->assertStringContainsString('#faq-widget { outline: 2px solid red; }', $html);
    }

    public function test_editor_and_shared_runtime_apply_advanced_visual_effects(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $runtime = file_get_contents(public_path('js/pagebuilder_elementor/frontend-runtime.js'));

        $this->assertSourceContains('function widgetAdvancedPreviewStyle(settings, device)', $appJs);
        $this->assertSourceContains('widgetAdvancedPreviewStyle(s, device)', $appJs);
        $this->assertSourceContains("window.addEventListener('scroll', scheduleMotion", $runtime);
        $this->assertSame(1, substr_count($runtime, "window.addEventListener('scroll', scheduleMotion"));
        $this->assertSourceContains('new IntersectionObserver', $runtime);
        $this->assertSourceContains("requestAnimationFrame(updateMotion)", $runtime);
        $this->assertSourceContains("window.addEventListener('pointermove'", $runtime);
        $this->assertSourceContains('prefersReducedMotion()', $runtime);
    }

    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }
}
