<?php

namespace Tests\Feature;

use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Tests\TestCase;

class PageBuilderElementorV24FrontendParserDefaultsTest extends TestCase
{
    public function test_every_active_v24_module_renders_through_the_frontend_dispatcher_without_warnings(): void
    {
        $modules = app(ModuleCatalog::class)->all();
        $warnings = [];

        set_error_handler(function (int $severity, string $message, string $file, int $line) use (&$warnings): bool {
            if (in_array($severity, [E_WARNING, E_NOTICE, E_DEPRECATED], true)) {
                $warnings[] = [
                    'message' => $message,
                    'file' => basename($file),
                    'line' => $line,
                ];
            }

            return true;
        });

        try {
            foreach ($modules as $type => $module) {
                $html = view('pagebuilder_elementor_v24.partials.render_node', [
                    'node' => [
                        'id' => 'frontend-defaults-'.$type,
                        'type' => $type,
                        'settings' => [],
                        'children' => [],
                        'columns' => [],
                    ],
                    'pageData' => null,
                ])->render();

                $this->assertNotSame('', trim($html), $type.' should render frontend HTML');
            }
        } finally {
            restore_error_handler();
        }

        $this->assertCount(50, $modules);
        $this->assertSame([], $warnings, json_encode($warnings, JSON_UNESCAPED_SLASHES));
    }

    public function test_layout_nodes_bypass_widget_fragment_cache_path(): void
    {
        $html = view('pagebuilder_elementor_v24.partials.render_node', [
            'node' => [
                'id' => 'frontend-layout-cache-regression',
                'type' => 'container',
                'settings' => [
                    'cacheMode' => 'active',
                    'displayType' => 'flex',
                ],
                'children' => [],
            ],
            'pageData' => null,
        ])->render();

        $this->assertStringContainsString('el-layout-container', $html);
        $this->assertStringContainsString('display:flex', $html);
    }
}
