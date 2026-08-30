<?php

namespace Tests\Feature;

use App\Models\Page_Builder\Page_Builder;
use Tests\TestCase;

class PageBuilderElementorV24StaticImportFrontendDependencyTest extends TestCase
{
    public function test_frontend_loads_import_dependencies_only_for_an_imported_root(): void
    {
        $page = (new Page_Builder())->forceFill([
            'page_name' => 'Imported Page',
            'custom_css' => '.pb-import-root{color:red}',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
        ]);
        $importedNodes = [[
            'id' => 'import-root',
            'type' => 'container',
            'settings' => [
                'cssClass' => 'pb-import-root',
                'staticImport' => [
                    'frameworks' => ['tailwind', 'bootstrap5', 'unsafe'],
                    'stylesheets' => [
                        'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap',
                        'https://evil.example.com/site.css',
                    ],
                ],
            ],
            'children' => [],
        ]];

        $imported = view('pagebuilder_elementor_v24.frontend_renderer', [
            'page' => $page, 'pageData' => $page, 'nodes' => $importedNodes,
        ])->render();

        $this->assertStringContainsString('data-pb-static-import="bootstrap5"', $imported);
        $this->assertStringContainsString('bootstrap@5.3.3/dist/css/bootstrap.min.css', $imported);
        $this->assertStringContainsString('data-pb-static-import="tailwind"', $imported);
        $this->assertStringContainsString('https://cdn.tailwindcss.com', $imported);
        $this->assertStringContainsString('corePlugins: { preflight: false }', $imported);
        $this->assertStringContainsString("important: '.pb-import-root'", $imported);
        $this->assertStringContainsString('fonts.googleapis.com/css2?family=Montserrat', $imported);
        $this->assertStringNotContainsString('evil.example.com', $imported);

        $manualNodes = [[
            'id' => 'manual-root', 'type' => 'container', 'settings' => [], 'children' => [],
        ]];
        $manual = view('pagebuilder_elementor_v24.frontend_renderer', [
            'page' => $page, 'pageData' => $page, 'nodes' => $manualNodes,
        ])->render();

        $this->assertStringNotContainsString('data-pb-static-import=', $manual);
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $manual);
        $this->assertStringNotContainsString('bootstrap@5.3.3/dist/css/bootstrap.min.css', $manual);
    }

    public function test_static_html_widget_renders_a_sandboxed_source_document(): void
    {
        $page = (new Page_Builder())->forceFill([
            'page_name' => 'Exact Page',
            'custom_css' => '',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
        ]);
        $nodes = [[
            'id' => 'static-source',
            'type' => 'static_html',
            'settings' => [
                'srcdoc' => '<!doctype html><html><body><main id="hero">Exact</main></body></html>',
                'title' => 'Exact Page',
                'height' => '1200px',
            ],
        ]];

        $html = view('pagebuilder_elementor_v24.frontend_renderer', [
            'page' => $page, 'pageData' => $page, 'nodes' => $nodes,
        ])->render();

        $this->assertStringContainsString('data-pb-static-html="true"', $html);
        $this->assertStringContainsString('sandbox="allow-scripts"', $html);
        $this->assertStringContainsString('srcdoc=', $html);
        $this->assertStringContainsString('Exact Page', $html);
    }

    public function test_static_html_widget_receives_custom_javascript_only_in_exact_sandbox_mode(): void
    {
        $code = "document.querySelector('#hero')?.classList.add('ready');";
        $page = (new Page_Builder())->forceFill([
            'page_name' => 'Exact Script Page',
            'custom_css' => '',
            'custom_js' => $code,
            'custom_js_mode' => 'exact_sandbox',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
        ]);
        $nodes = [[
            'id' => 'static-source',
            'type' => 'static_html',
            'settings' => [
                'srcdoc' => '<!doctype html><html><body><main id="hero">Exact</main></body></html>',
                'title' => 'Exact Script Page',
                'height' => '1200px',
            ],
        ]];

        $sandboxHtml = view('pagebuilder_elementor_v24.frontend_renderer', [
            'page' => $page, 'pageData' => $page, 'nodes' => $nodes,
        ])->render();

        $this->assertStringContainsString('data-pb-custom-javascript="sandbox"', $sandboxHtml);
        $this->assertStringContainsString($code, html_entity_decode($sandboxHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $this->assertStringNotContainsString('<script data-pb-custom-javascript="sandbox">', $sandboxHtml);
        $this->assertStringNotContainsString('data-pb-custom-javascript="published"', $sandboxHtml);

        $page->custom_js_mode = 'disabled';
        $disabledHtml = view('pagebuilder_elementor_v24.frontend_renderer', [
            'page' => $page, 'pageData' => $page, 'nodes' => $nodes,
        ])->render();

        $this->assertStringNotContainsString('data-pb-custom-javascript="sandbox"', $disabledHtml);
        $this->assertStringNotContainsString($code, html_entity_decode($disabledHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    public function test_compiled_native_frontend_does_not_load_framework_compiler_assets(): void
    {
        $page = (new Page_Builder())->forceFill([
            'page_name' => 'Compiled Page',
            'custom_css' => "/* PHOENIX_STATIC_IMPORT_COMPILED_START */\n.pb-import-root [data-pb-import-node=\"import-node-1\"]{color:red}\n/* PHOENIX_STATIC_IMPORT_COMPILED_END */",
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
        ]);
        $nodes = [[
            'id' => 'compiled-root',
            'type' => 'container',
            'settings' => [
                'cssClass' => 'pb-import-root',
                'staticImport' => [
                    'mode' => 'compiled',
                    'frameworks' => ['tailwind', 'bootstrap5'],
                ],
            ],
            'children' => [],
        ]];

        $html = view('pagebuilder_elementor_v24.frontend_renderer', [
            'page' => $page, 'pageData' => $page, 'nodes' => $nodes,
        ])->render();

        $this->assertStringContainsString('PHOENIX_STATIC_IMPORT_COMPILED_START', $html);
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $html);
        $this->assertStringNotContainsString('bootstrap@5.3.3/dist/css/bootstrap.min.css', $html);
    }

    public function test_compiled_native_frontend_renders_safe_markers_without_source_visual_classes(): void
    {
        $page = (new Page_Builder())->forceFill([
            'page_name' => 'Compiled Markers',
            'custom_css' => '/* PHOENIX_STATIC_IMPORT_COMPILED_START */.pb-import-root [data-pb-import-node="import-node-1"]{font-size:6.2rem}/* PHOENIX_STATIC_IMPORT_COMPILED_END */',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
        ]);
        $nodes = [[
            'id' => 'compiled-root',
            'type' => 'container',
            'settings' => [
                'cssClass' => 'pb-import-root',
                'importNodeKey' => 'import-node-body',
                'staticImport' => ['mode' => 'compiled', 'frameworks' => ['tailwind']],
            ],
            'children' => [[
                'id' => 'compiled-heading',
                'type' => 'heading',
                'settings' => ['text' => 'Compiled', 'importNodeKey' => 'import-node-1'],
            ]],
        ]];

        $html = view('pagebuilder_elementor_v24.frontend_renderer', [
            'page' => $page, 'pageData' => $page, 'nodes' => $nodes,
        ])->render();

        $this->assertStringContainsString('data-pb-import-node="import-node-body"', $html);
        $this->assertStringContainsString('data-pb-import-node="import-node-1"', $html);
        $this->assertStringNotContainsString('lg:text-[6.2rem]', $html);
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $html);
    }
}
