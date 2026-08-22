<?php

namespace Tests\Feature;

use App\Support\PageBuilderElementorV24\ModuleCatalog;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Tests\TestCase;

class PageBuilderElementorV24FullModularityTest extends TestCase
{
    public function test_inactive_module_renders_a_deterministic_marker_without_mutating_its_node(): void
    {
        $this->app->instance(
            ModuleCatalog::class,
            new ModuleCatalog(base_path('tests/Fixtures/PageBuilderElementorV24Modules')),
        );

        $node = [
            'id' => 'persisted-inactive-heading',
            'type' => 'heading',
            'settings' => ['text' => 'Keep this persisted payload'],
            'children' => [],
        ];
        $before = json_encode($node, JSON_THROW_ON_ERROR);

        $html = view('pagebuilder_elementor_v24.partials.render_node', ['node' => $node])->render();

        $this->assertStringContainsString(
            '<!-- Inactive Page Builder v2.4 module: heading -->',
            $html,
        );
        $this->assertSame($before, json_encode($node, JSON_THROW_ON_ERROR));
    }

    public function test_production_cutover_has_no_legacy_catalog_or_widget_copy_tree(): void
    {
        $this->assertFileDoesNotExist(config_path('pagebuilder_elementor_v24_widgets.php'));
        $this->assertDirectoryDoesNotExist(public_path('js/pagebuilder_elementor_v24/widgets'));
        $this->assertDirectoryDoesNotExist(resource_path('views/pagebuilder_elementor_v24/widgets'));
    }

    public function test_module_frontends_do_not_include_legacy_widget_or_renderer_views(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                resource_path('pagebuilder_elementor_v24/modules'),
                RecursiveDirectoryIterator::SKIP_DOTS,
            ),
        );

        foreach ($iterator as $file) {
            if (! $file instanceof SplFileInfo || ! $file->isFile() || $file->getFilename() !== 'frontend.blade.php') {
                continue;
            }

            $source = file_get_contents($file->getPathname());
            $this->assertIsString($source);
            $this->assertStringNotContainsString('pagebuilder_elementor_v24.widgets.', $source, $file->getPathname());
            $this->assertDoesNotMatchRegularExpression(
                "/pagebuilder_elementor_v24\\.partials\\.render_(?!node(?:['\"]|\\b))/",
                $source,
                $file->getPathname(),
            );
        }
    }
}
