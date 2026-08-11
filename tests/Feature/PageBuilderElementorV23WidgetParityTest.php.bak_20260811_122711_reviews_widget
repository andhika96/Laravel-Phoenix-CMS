<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23WidgetParityTest extends TestCase
{
    public function test_v23_catalog_matches_v20_and_owns_every_module_and_renderer(): void
    {
        $v20 = config('pagebuilder_elementor_widgets', []);
        $v23 = config('pagebuilder_elementor_v23_widgets', []);

        $this->assertSame(array_keys($v20), array_keys($v23));

        foreach ($v20 as $type => $module) {
            $copy = $v23[$type];

            foreach (['type', 'label', 'category', 'icon', 'toolbox'] as $metadata) {
                $this->assertSame($module[$metadata], $copy[$metadata], $type.' '.$metadata);
            }

            foreach (['definition', 'canvas', 'settings'] as $asset) {
                $path = (string) $copy[$asset];
                $this->assertStringContainsString('pagebuilder_elementor_v23', $path, $type.' '.$asset);
                $this->assertFileExists(public_path($path), $type.' '.$asset);
            }

            $view = (string) $copy['view'];
            $this->assertStringStartsWith('pagebuilder_elementor_v23.', $view, $type.' view');
            $this->assertTrue(view()->exists($view), $type.' '.$view);
        }
    }
}
