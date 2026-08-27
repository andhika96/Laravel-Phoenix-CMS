<?php

namespace Tests\Feature;

use App\Models\Page_Builder\Page_Builder;
use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Tests\TestCase;

class PageBuilderElementorV24FrontendRenderingTest extends TestCase
{
    public function test_v24_renders_representative_layout_basic_general_and_pro_nodes(): void
    {
        $layout = $this->renderNode([
            'id' => 'container-v24',
            'type' => 'container',
            'settings' => ['displayType' => 'flex'],
            'children' => [],
        ]);
        $this->assertStringContainsString('id="pb-node-container-v24"', $layout);
        $this->assertStringContainsString('el-layout-container', $layout);

        $heading = $this->renderNode([
            'id' => 'heading-v24',
            'type' => 'heading',
            'settings' => ['text' => 'V24 Heading'],
        ]);
        $this->assertStringContainsString('V24 Heading', $heading);
        $this->assertStringContainsString('pb-heading-widget', $heading);

        $imageBox = $this->renderNode([
            'id' => 'image-box-v24',
            'type' => 'image_box',
            'settings' => [
                'title' => 'V24 Image Box',
                'description' => 'Independent general renderer',
            ],
        ]);
        $this->assertStringContainsString('pb-image-box', $imageBox);
        $this->assertStringContainsString('V24 Image Box', $imageBox);

        $page = (new Page_Builder())->forceFill([
            'uri' => 'v24-contact-page',
            'page_name' => 'V24 Contact Page',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
        ]);
        $form = $this->renderNode([
            'id' => 'form-v24',
            'type' => 'form',
            'settings' => [
                'formName' => 'V24 Contact Form',
                'fields' => [
                    ['id' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ],
                'submitActions' => ['message'],
            ],
        ], $page);
        $submitUrl = route('cms.core.pagebuilder_elementor_v24.form.submit', [
            'idOrSlug' => 'v24-contact-page',
            'nodeId' => 'form-v24',
        ]);

        $this->assertStringContainsString('class="pb-pro-form"', $form);
        $this->assertStringContainsString('action="'.$submitUrl.'"', $form);
        $this->assertStringContainsString('/pagebuilder-elementor/v2.4/form/', $form);
        $this->assertStringNotContainsString('/pagebuilder-elementor/form/v24-contact-page', $form);
    }

    public function test_every_registered_v24_renderer_view_is_owned_by_the_v24_tree(): void
    {
        $modules = app(ModuleCatalog::class)->all();
        $this->assertCount(50, $modules);

        foreach ($modules as $type => $module) {
            $view = (string) ($module['assets']['view'] ?? '');

            $this->assertStringContainsString(
                str_replace('\\', '/', resource_path('pagebuilder_elementor_v24/modules')).'/',
                str_replace('\\', '/', $view),
                $type,
            );
            $this->assertFileExists($view, $type);
        }
    }

    public function test_v24_grid_link_renders_safe_frontend_attributes(): void
    {
        $linkedGrid = $this->renderNode([
            'id' => 'grid-link-v24',
            'type' => 'container',
            'settings' => [
                'displayType' => 'grid',
                'htmlTag' => 'a',
                'linkUrl' => '/demo-grid',
                'linkTargetBlank' => true,
                'linkNofollow' => true,
            ],
            'children' => [],
        ]);

        $this->assertStringContainsString('<a id="pb-node-grid-link-v24"', $linkedGrid);
        $this->assertStringContainsString('href="/demo-grid"', $linkedGrid);
        $this->assertStringContainsString('target="_blank"', $linkedGrid);
        $this->assertStringContainsString('rel="nofollow noopener"', $linkedGrid);

        $unsafeGrid = $this->renderNode([
            'id' => 'unsafe-grid-link-v24',
            'type' => 'container',
            'settings' => [
                'displayType' => 'grid',
                'htmlTag' => 'a',
                'linkUrl' => 'javascript:alert(1)',
            ],
            'children' => [],
        ]);

        $this->assertStringContainsString('href="#"', $unsafeGrid);
        $this->assertStringNotContainsString('javascript:alert(1)', $unsafeGrid);
    }

    public function test_v24_container_renders_canonical_and_legacy_layouts_without_content_loss(): void
    {
        $heading = fn (string $id, string $text): array => [
            'id' => $id,
            'type' => 'heading',
            'settings' => ['text' => $text],
        ];

        $canonical = $this->renderNode([
            'id' => 'canonical-parent',
            'type' => 'container',
            'settings' => ['displayType' => 'flex', 'direction' => 'row'],
            'children' => [
                ['id' => 'canonical-left', 'type' => 'container', 'settings' => ['displayType' => 'flex', 'containerWidth' => '33%'], 'children' => [$heading('a', 'First')]],
                ['id' => 'canonical-right', 'type' => 'container', 'settings' => ['displayType' => 'flex', 'containerWidth' => '67%'], 'children' => [$heading('b', 'Second')]],
            ],
        ]);

        $legacy = $this->renderNode([
            'id' => 'legacy-parent',
            'type' => 'container',
            'settings' => ['displayType' => 'flex', 'direction' => 'row'],
            'columns' => [
                ['id' => 'legacy-left', 'flexBasis' => '33%', 'children' => [$heading('c', 'First')]],
                ['id' => 'legacy-right', 'flexBasis' => '67%', 'children' => [$heading('d', 'Second')]],
            ],
        ]);

        foreach ([$canonical, $legacy] as $html) {
            $this->assertStringContainsString('First', $html);
            $this->assertStringContainsString('Second', $html);
            $this->assertLessThan(strpos($html, 'Second'), strpos($html, 'First'));
            $this->assertStringContainsString('33%', $html);
            $this->assertStringContainsString('67%', $html);
        }
        $this->assertStringContainsString('id="pb-node-canonical-left"', $canonical);
        $this->assertStringContainsString('id="pb-node-canonical-right"', $canonical);
    }

    public function test_v24_layout_frontend_nodes_expose_motion_runtime_contract(): void
    {
        foreach (['container', 'container_fluid', 'grid', 'row_grid'] as $type) {
            $html = $this->renderNode([
                'id' => 'motion-'.$type,
                'type' => $type,
                'settings' => [
                    'entranceAnimation' => 'fadeIn',
                    'entranceDelay' => 250,
                    'entranceDuration' => 'fast',
                    'scrollingEffects' => true,
                    'mouseEffects' => true,
                ],
                'children' => [],
                'columns' => [],
            ]);

            $this->assertStringContainsString('data-pb-motion=', $html, $type);
            $this->assertStringContainsString('data-entrance-delay="250"', $html, $type);
            $this->assertStringContainsString('data-entrance-duration="fast"', $html, $type);
        }
    }

    public function test_every_v24_frontend_renderer_exposes_motion_runtime_contract(): void
    {
        foreach (app(ModuleCatalog::class)->all() as $type => $module) {
            $html = $this->renderNode([
                'id' => 'motion-all-'.$type,
                'type' => $type,
                'settings' => ['scrollingEffects' => true, 'mouseEffects' => true, 'entranceAnimation' => 'fadeIn'],
                'children' => [],
                'columns' => [],
            ]);

            $this->assertStringContainsString('data-pb-motion=', $html, $type);
        }
    }

    public function test_v24_text_editor_sanitizes_active_html_without_removing_safe_rich_text(): void
    {
        $html = $this->renderNode([
            'id' => 'text-editor-xss',
            'type' => 'text_editor',
            'settings' => [
                'html' => '<p><strong>Safe</strong> <a href="/docs" onclick="alert(1)">link</a><script>alert(1)</script><img src=x onerror=alert(1)></p>',
            ],
        ]);

        $this->assertStringContainsString('<strong>Safe</strong>', $html);
        $this->assertStringContainsString('href="/docs"', $html);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringNotContainsString('onerror=', $html);
    }

    public function test_v24_grid_columns_render_widgets_containers_and_nested_grids(): void
    {
        $grid = $this->renderNode([
            'id' => 'grid-parent',
            'type' => 'grid',
            'settings' => ['columns' => 3, 'gridRows' => 1],
            'children' => [],
            'columns' => [
                [
                    'id' => 'grid-cell-widget',
                    'children' => [['id' => 'grid-heading', 'type' => 'heading', 'settings' => ['text' => 'Grid Widget']]],
                ],
                [
                    'id' => 'grid-cell-container',
                    'children' => [[
                        'id' => 'grid-container',
                        'type' => 'container',
                        'settings' => ['displayType' => 'flex'],
                        'children' => [['id' => 'container-heading', 'type' => 'heading', 'settings' => ['text' => 'Nested Container']]],
                    ]],
                ],
                [
                    'id' => 'grid-cell-grid',
                    'children' => [[
                        'id' => 'nested-grid',
                        'type' => 'grid',
                        'settings' => ['columns' => 1, 'gridRows' => 1],
                        'children' => [],
                        'columns' => [[
                            'id' => 'nested-grid-cell',
                            'children' => [['id' => 'nested-grid-heading', 'type' => 'heading', 'settings' => ['text' => 'Nested Grid']]],
                        ]],
                    ]],
                ],
            ],
        ]);

        $this->assertSame(4, substr_count($grid, 'class="el-grid-col"'));
        $this->assertStringContainsString('Grid Widget', $grid);
        $this->assertStringContainsString('id="pb-node-grid-container"', $grid);
        $this->assertStringContainsString('Nested Container', $grid);
        $this->assertStringContainsString('id="pb-node-nested-grid"', $grid);
        $this->assertStringContainsString('Nested Grid', $grid);
        $this->assertLessThan(strpos($grid, 'Nested Container'), strpos($grid, 'Grid Widget'));
        $this->assertLessThan(strpos($grid, 'Nested Grid'), strpos($grid, 'Nested Container'));
    }

    public function test_v24_grid_column_styles_render_on_standalone_and_container_grid(): void
    {
        $columnStyle = [
            'borderType' => 'solid',
            'borderWidthTop' => '1px',
            'borderWidthRight' => '2px',
            'borderWidthBottom' => '3px',
            'borderWidthLeft' => '4px',
            'borderColor' => '#112233',
            'borderColorTablet' => '#445566',
            'borderRadiusTL' => '8px',
            'borderRadiusTR' => '9px',
            'borderRadiusBR' => '10px',
            'borderRadiusBL' => '11px',
            'bgType' => 'color',
            'bgColor' => '#abcdef',
        ];
        $standalone = $this->renderNode([
            'id' => 'styled-grid',
            'type' => 'grid',
            'settings' => ['columns' => 2, 'gridRows' => 1],
            'columnStyles' => [$columnStyle, []],
            'columns' => [
                ['id' => 'styled-cell-1', 'children' => []],
                ['id' => 'styled-cell-2', 'styleOverrides' => ['bgType' => 'color', 'bgColor' => '#fedcba'], 'children' => []],
            ],
        ]);
        $container = $this->renderNode([
            'id' => 'styled-container-grid',
            'type' => 'container',
            'settings' => ['displayType' => 'grid', 'gridColumns' => 2, 'gridRows' => '1'],
            'columnStyles' => [$columnStyle, []],
            'columns' => [
                ['id' => 'container-styled-cell-1', 'children' => []],
                ['id' => 'container-styled-cell-2', 'children' => []],
            ],
        ]);

        foreach ([$standalone, $container] as $html) {
            $this->assertStringContainsString('data-pb-grid-track="0"', $html);
            $this->assertStringContainsString('data-pb-grid-cell=', $html);
            $this->assertStringContainsString('border-style:solid', $html);
            $this->assertStringContainsString('border-width:1px 2px 3px 4px', $html);
            $this->assertStringContainsString('border-radius:8px 9px 10px 11px', $html);
            $this->assertStringContainsString('background-color:#abcdef', $html);
            $this->assertStringContainsString('@media (max-width: 1024px)', $html);
            $this->assertStringContainsString('#445566', $html);
        }

        $this->assertStringContainsString('background-color:#fedcba', $standalone);
    }

    public function test_v24_legacy_fallback_never_merges_extra_columns_into_the_last_cell(): void
    {
        $legacy = $this->renderNode([
            'id' => 'legacy-grid',
            'type' => 'container',
            'settings' => ['displayType' => 'grid', 'gridColumns' => 1, 'gridRows' => '1'],
            'columns' => [
                ['id' => 'one', 'children' => [['id' => 'a', 'type' => 'heading', 'settings' => ['text' => 'One']]]],
                ['id' => 'two', 'children' => [['id' => 'b', 'type' => 'heading', 'settings' => ['text' => 'Two']]]],
                ['id' => 'three', 'children' => [['id' => 'c', 'type' => 'heading', 'settings' => ['text' => 'Three']]]],
            ],
        ]);

        $this->assertStringContainsString('One', $legacy);
        $this->assertStringContainsString('Two', $legacy);
        $this->assertStringContainsString('Three', $legacy);
        $this->assertSame(3, substr_count($legacy, 'class="el-grid-col"'));
    }

    public function test_v24_legacy_fallback_recovers_loose_children_before_columns(): void
    {
        $legacy = $this->renderNode([
            'id' => 'legacy-hybrid',
            'type' => 'container',
            'settings' => ['displayType' => 'flex', 'direction' => 'row'],
            'children' => [['id' => 'loose', 'type' => 'heading', 'settings' => ['text' => 'Loose']]],
            'columns' => [
                ['id' => 'column', 'flexBasis' => '100%', 'children' => [['id' => 'column-child', 'type' => 'heading', 'settings' => ['text' => 'Column']]]],
            ],
        ]);

        $this->assertStringContainsString('Loose', $legacy);
        $this->assertStringContainsString('Column', $legacy);
        $this->assertLessThan(strpos($legacy, 'Column'), strpos($legacy, 'Loose'));
        $this->assertSame(2, substr_count($legacy, 'class="el-grid-col"'));
    }

    public function test_v24_tabs_parser_emits_the_canvas_compatible_accordion_structure(): void
    {
        $html = $this->renderNode([
            'id' => 'tabs-parser-v24',
            'type' => 'tabs',
            'settings' => [
                'direction' => 'row',
                'breakpoint' => 'mobile',
                'activeTabId' => 'first',
            ],
            'tabItems' => [
                ['id' => 'first', 'title' => 'First', 'children' => []],
                ['id' => 'second', 'title' => 'Second', 'children' => []],
            ],
        ]);

        $this->assertStringContainsString('data-tabs-widget', $html);
        $this->assertStringContainsString('is-direction-row', $html);
        $this->assertStringContainsString('is-breakpoint-mobile', $html);
        $this->assertStringContainsString('data-tabs-nav', $html);
        $this->assertStringContainsString('el-widget-tabs__accordion-title', $html);
        $this->assertStringNotContainsString('<script>(function(){const root=document.getElementById(', $html);

		$tabsCss = file_get_contents(resource_path('pagebuilder_elementor_v24/modules/widgets/general/tabs/styles.css'));
		$this->assertIsString($tabsCss);
		$this->assertStringContainsString('.el-widget-tabs.is-breakpoint-mobile .el-widget-tabs__pane--accordion {', $tabsCss);
		$this->assertStringContainsString('padding: 0;', substr($tabsCss, strpos($tabsCss, '.el-widget-tabs.is-breakpoint-mobile .el-widget-tabs__pane--accordion {')));
    }

    private function renderNode(array $node, ?Page_Builder $page = null): string
    {
        return view('pagebuilder_elementor_v24.partials.render_node', [
            'node' => $node,
            'pageData' => $page,
        ])->render();
    }
}
