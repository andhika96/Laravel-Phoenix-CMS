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
        bool $toolboxVisible,
    ): void {
        $catalog = config('pagebuilder_elementor_widgets');

        $this->assertArrayHasKey($type, $catalog);
        $module = $catalog[$type];

        $this->assertSame($label, $module['label']);
        $this->assertSame('layout', $module['category']);
        $this->assertSame($toolboxVisible, $module['toolbox']);
        $this->assertFileExists(public_path($module['definition']));
        $this->assertFileExists(public_path($module['canvas']));
        $this->assertFileExists(public_path($module['settings']));
        $this->assertTrue(view()->exists($module['view']));

        $definition = file_get_contents(public_path($module['definition']));
        $settings = file_get_contents(public_path($module['settings']));
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertStringContainsString("type: '{$type}'", $definition);
        $this->assertStringContainsString("const DEFAULT_CONTENT_WIDTH = '{$contentWidth}'", $definition);
        $this->assertStringContainsString('toolbox: '.($toolboxVisible ? 'true' : 'false'), $definition);
        $this->assertStringContainsString('createNode(node)', $definition);
        $this->assertStringContainsString('normalize(node)', $definition);
        $this->assertStringContainsString("widgets/layout/{$folder}/Canvas.vue", $definition);
        $this->assertStringContainsString("widgets/layout/{$folder}/Settings.vue", $definition);
        $this->assertStringContainsString('<template>', $settings);
        $this->assertStringContainsString('pb-layout-settings', $settings);

        $this->assertStringNotContainsString('@click="node.settings[editor.bgStateKey(', $settings);
        foreach ([
            "editor.setBgStateValue(node, 'bgGradientType', 'linear')",
            "editor.setBgStateValue(node, 'bgGradientType', 'radial')",
            "editor.setBgStateValue(node, 'bgOverlayGradientType', 'linear')",
            "editor.setBgStateValue(node, 'bgOverlayGradientType', 'radial')",
        ] as $safeAssignment) {
            $this->assertStringContainsString($safeAssignment, $settings);
        }

        $this->assertStringNotContainsString("case '{$type}':", $app);
        $this->assertStringNotContainsString("/widgets/layout/" . ($type === 'container' ? 'Container' : 'ContainerFluid') . '.vue', $app);
    }

    public function test_container_settings_use_the_shared_dynamic_sidebar_mount(): void
    {
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSame(1, substr_count($app, ':is="loadWidgetSettings(selectedType)"'));
        $this->assertStringNotContainsString("<template v-if=\"selectedType==='container'||selectedType==='container_fluid'\">", $app);
        $this->assertStringContainsString('function setBgStateValue(node, base, value)', $app);
        $this->assertStringContainsString('setBgStateValue,', $app);
    }

    public function test_container_variants_use_compound_numeric_unit_controls(): void
    {
        foreach (['container', 'container-fluid'] as $folder) {
            $settings = file_get_contents(public_path("js/pagebuilder_elementor/widgets/layout/{$folder}/Settings.vue"));

            $this->assertIsString($settings);
            foreach (['flexColumnGap', 'flexRowGap', 'gridColumnGap', 'gridRowGap'] as $key) {
                $this->assertStringContainsString("{key:'{$key}'", $settings);
            }
            $this->assertStringContainsString('editor.sizeControlDisplayValue(node, control.key', $settings);
            $this->assertStringContainsString('editor.sizeControlUnit(node, control.key', $settings);
            $this->assertStringContainsString("dimensionValue(editor.bgStateKey(node,'borderWidth'), 'px')", $settings);
            $this->assertStringContainsString("dimensionGroupUnit(responsiveRadiusKeys(), 'px')", $settings);
            $this->assertStringContainsString("dimensionGroupUnit([editor.bgStateKey(node,'shadowH')", $settings);
            $this->assertStringContainsString("{key:'stickyOffset',label:'Sticky Offset'}", $settings);
            $this->assertStringContainsString("{key:'transformRotate',label:'Rotate'}", $settings);
            $this->assertStringNotContainsString("v-model=\"node.settings[editor.bgStateKey(node,'borderWidth')]\"", $settings);
            $this->assertStringNotContainsString('v-model="node.settings.transformRotate"', $settings);
        }
    }
    public static function containerProvider(): array
    {
        return [
            'container' => ['container', 'container', 'Container', 'full', true],
            'container fluid' => ['container_fluid', 'container-fluid', 'Container Fluid', 'full', false],
        ];
    }
}
