<?php

namespace Tests\Feature;

use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Tests\TestCase;

class PageBuilderElementorV24WidgetParityTest extends TestCase
{
    public function test_v24_catalog_contains_v20_and_owns_every_module_and_renderer(): void
    {
        $v20 = config('pagebuilder_elementor_widgets', []);
        $discovered = app(ModuleCatalog::class)->all();
        $v24Types = array_keys($discovered);

        $this->assertSame([], array_values(array_diff(array_keys($v20), $v24Types)));

        foreach ($v20 as $type => $module) {
            $copy = $discovered[$type];

            foreach (['type', 'label', 'category', 'icon', 'toolbox'] as $metadata) {
                $this->assertSame($module[$metadata], $copy[$metadata], $type.' '.$metadata);
            }

            $this->assertOwnedAssetsAndRenderer($type, $copy);
        }

        foreach (array_diff($v24Types, array_keys($v20)) as $type) {
            $this->assertOwnedAssetsAndRenderer($type, $discovered[$type]);
        }
    }

    private function assertOwnedAssetsAndRenderer(string $type, array $module): void
    {
        foreach (['definition', 'canvas', 'settings', 'view'] as $asset) {
            $path = (string) $module['assets'][$asset];
            $this->assertStringContainsString('pagebuilder_elementor_v24', $path, $type.' '.$asset);
            $this->assertFileExists($path, $type.' '.$asset);
        }
    }
}
