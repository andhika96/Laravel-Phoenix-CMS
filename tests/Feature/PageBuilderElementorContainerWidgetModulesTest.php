<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageBuilderElementorContainerWidgetModulesTest extends TestCase
{
    #[DataProvider('containerProvider')]
    public function test_container_has_complete_module_contract(
        string $type,
        string $folder,
        string $label,
        string $contentWidth,
    ): void {
        $catalog = config('pagebuilder_elementor_widgets');

        $this->assertArrayHasKey($type, $catalog);
        $module = $catalog[$type];

        $this->assertSame($label, $module['label']);
        $this->assertSame('layout', $module['category']);
        $this->assertTrue($module['toolbox']);
        $this->assertFileExists(public_path($module['definition']));
        $this->assertFileExists(public_path($module['canvas']));
        $this->assertFileExists(public_path($module['settings']));

        $definition = file_get_contents(public_path($module['definition']));
        $settings = file_get_contents(public_path($module['settings']));
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertStringContainsString("type: '{$type}'", $definition);
        $this->assertStringContainsString("const DEFAULT_CONTENT_WIDTH = '{$contentWidth}'", $definition);
        $this->assertStringContainsString('createNode(node)', $definition);
        $this->assertStringContainsString('normalize(node)', $definition);
        $this->assertStringContainsString("widgets/layout/{$folder}/Canvas.vue", $definition);
        $this->assertStringContainsString("widgets/layout/{$folder}/Settings.vue", $definition);
        $this->assertStringContainsString('<template>', $settings);
        $this->assertStringContainsString('pb-layout-settings', $settings);

        $this->assertStringNotContainsString("case '{$type}':", $app);
        $this->assertStringNotContainsString("/widgets/layout/" . ($type === 'container' ? 'Container' : 'ContainerFluid') . '.vue', $app);
    }

    public function test_container_settings_use_the_shared_dynamic_sidebar_mount(): void
    {
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSame(1, substr_count($app, ':is="loadWidgetSettings(selectedType)"'));
        $this->assertStringNotContainsString("<template v-if=\"selectedType==='container'||selectedType==='container_fluid'\">", $app);
    }

    public static function containerProvider(): array
    {
        return [
            'container' => ['container', 'container', 'Container', 'full'],
            'container fluid' => ['container_fluid', 'container-fluid', 'Container Fluid', 'full'],
        ];
    }
}
