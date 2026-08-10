<?php

namespace Tests\Feature;

use App\Models\Page_Builder\Page_Builder;
use Tests\TestCase;

class PageBuilderElementorV23FrontendRenderingTest extends TestCase
{
    public function test_v23_renders_representative_layout_basic_general_and_pro_nodes(): void
    {
        $layout = $this->renderNode([
            'id' => 'container-v23',
            'type' => 'container',
            'settings' => ['displayType' => 'flex'],
            'children' => [],
        ]);
        $this->assertStringContainsString('id="pb-node-container-v23"', $layout);
        $this->assertStringContainsString('el-layout-container', $layout);

        $heading = $this->renderNode([
            'id' => 'heading-v23',
            'type' => 'heading',
            'settings' => ['text' => 'V23 Heading'],
        ]);
        $this->assertStringContainsString('V23 Heading', $heading);
        $this->assertStringContainsString('pb-heading-widget', $heading);

        $imageBox = $this->renderNode([
            'id' => 'image-box-v23',
            'type' => 'image_box',
            'settings' => [
                'title' => 'V23 Image Box',
                'description' => 'Independent general renderer',
            ],
        ]);
        $this->assertStringContainsString('pb-image-box', $imageBox);
        $this->assertStringContainsString('V23 Image Box', $imageBox);

        $page = (new Page_Builder())->forceFill([
            'uri' => 'v23-contact-page',
            'page_name' => 'V23 Contact Page',
            'editor_version' => Page_Builder::EDITOR_VERSION_V23,
        ]);
        $form = $this->renderNode([
            'id' => 'form-v23',
            'type' => 'form',
            'settings' => [
                'formName' => 'V23 Contact Form',
                'fields' => [
                    ['id' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ],
                'submitActions' => ['message'],
            ],
        ], $page);
        $submitUrl = route('cms.core.pagebuilder_elementor_v23.form.submit', [
            'idOrSlug' => 'v23-contact-page',
            'nodeId' => 'form-v23',
        ]);

        $this->assertStringContainsString('class="pb-pro-form"', $form);
        $this->assertStringContainsString('action="'.$submitUrl.'"', $form);
        $this->assertStringContainsString('/pagebuilder-elementor/v2.3/form/', $form);
        $this->assertStringNotContainsString('/pagebuilder-elementor/form/v23-contact-page', $form);
    }

    public function test_every_registered_v23_renderer_view_is_owned_by_the_v23_tree(): void
    {
        foreach (config('pagebuilder_elementor_v23_widgets', []) as $type => $module) {
            $view = (string) ($module['view'] ?? '');

            $this->assertStringStartsWith('pagebuilder_elementor_v23.', $view, $type);
            $this->assertTrue(view()->exists($view), $view);
        }
    }

    private function renderNode(array $node, ?Page_Builder $page = null): string
    {
        return view('pagebuilder_elementor_v23.partials.render_node', [
            'node' => $node,
            'pageData' => $page,
        ])->render();
    }
}
