<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorIconWidgetContentParityTest extends TestCase
{
    public function test_editor_shell_uses_font_awesome_5_15_3(): void
    {
        $blade = file_get_contents(resource_path('views/pagebuilder_elementor/editor_shell.blade.php'));

        $this->assertIsString($blade);
        $this->assertStringContainsString("assets/plugins/fontawesome/5.15.3/css/all.min.css", $blade);
    }

    public function test_editor_exposes_icon_widget_controls_and_library_groups(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertIsString($appJs);
        $this->assertStringContainsString("{ type:'icon',", $appJs);
        $this->assertStringContainsString("label:'Icon'", $appJs);
        $this->assertStringContainsString("view: 'default'", $appJs);
        $this->assertStringContainsString("shape: 'circle'", $appJs);
        $this->assertStringContainsString('Font Awesome - Regular', $appJs);
        $this->assertStringContainsString('Font Awesome - Solid', $appJs);
        $this->assertStringContainsString('Font Awesome - Brands', $appJs);
        $this->assertStringContainsString('Font Awesome - Light', $appJs);
        $this->assertStringContainsString('Font Awesome - Duotone', $appJs);
        $this->assertStringContainsString('Open in new window', $appJs);
        $this->assertStringContainsString('Add nofollow', $appJs);
        $this->assertStringContainsString('Custom Attributes', $appJs);
    }

    public function test_frontend_renderer_emits_icon_markup(): void
    {
        $html = view('pagebuilder_elementor.partials.render_node', [
            'node' => [
                'id' => 'icon-node',
                'type' => 'icon',
                'settings' => [
                    'iconClass' => 'fas fa-star',
                    'iconStyle' => 'solid',
                    'iconName' => 'star',
                    'view' => 'framed',
                    'shape' => 'circle',
                    'link' => 'https://example.com',
                    'openInNewWindow' => true,
                    'nofollow' => true,
                    'attributes' => [
                        ['name' => 'data-track', 'value' => 'hero-icon'],
                    ],
                    'cssClass' => 'custom-icon-node',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('custom-icon-node', $html);
        $this->assertStringContainsString('fas fa-star', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $html);
        $this->assertStringContainsString('data-track="hero-icon"', $html);
    }
}
