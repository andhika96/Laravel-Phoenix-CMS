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
        $definition = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/icon/definition.js'));
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/icon/Settings.vue'));

        $this->assertIsString($appJs);
        $this->assertIsString($definition);
        $this->assertIsString($settings);
        $this->assertStringContainsString("type: 'icon'", $definition);
        $this->assertStringContainsString("label: 'Icon'", $definition);
        $this->assertStringContainsString("view: 'default'", $definition);
        $this->assertStringContainsString("shape: 'circle'", $definition);
        $this->assertStringContainsString('Font Awesome - Regular', $appJs);
        $this->assertStringContainsString('Font Awesome - Solid', $appJs);
        $this->assertStringContainsString('Font Awesome - Brands', $appJs);
        $this->assertStringContainsString('Font Awesome - Light', $appJs);
        $this->assertStringContainsString('Font Awesome - Duotone', $appJs);
        $this->assertStringContainsString('Open in new window', $settings);
        $this->assertStringContainsString('Add nofollow', $settings);
        $this->assertStringContainsString('Custom Attributes', $settings);
    }
    public function test_icon_settings_and_canvas_follow_shared_sidebar_and_attribute_contract(): void
    {
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/icon/Settings.vue'));
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/icon/Canvas.vue'));

        $this->assertIsString($settings);
        $this->assertIsString($canvas);
        $this->assertStringContainsString('class="pb-tab-nav"', $settings);
        foreach (['Content', 'Advanced'] as $tab) {
            $this->assertStringContainsString(">$tab</span>", $settings);
        }
        $this->assertStringContainsString('class="pb-collapsible"', $settings);
        $this->assertStringContainsString('v-model="node.settings.cssClass"', $settings);
        $this->assertStringContainsString('allowedAttributeName(name)', $canvas);
        $this->assertStringContainsString("/^(data-[A-Za-z0-9_.:-]+|aria-[A-Za-z0-9_.:-]+|title)$/", $canvas);
        $this->assertStringContainsString('Object.assign(attrs, this.customAttributes);', $canvas);
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
