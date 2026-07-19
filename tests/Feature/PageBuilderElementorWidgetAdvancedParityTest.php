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

    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }
}
