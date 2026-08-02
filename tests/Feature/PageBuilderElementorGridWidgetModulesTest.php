<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageBuilderElementorGridWidgetModulesTest extends TestCase
{
    #[DataProvider('layoutWidgetProvider')]
    public function test_grid_widget_has_complete_module_contract(
        string $type,
        string $folder,
        string $label,
        int $defaultColumns,
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
        $this->assertStringContainsString("const DEFAULT_COLUMNS = {$defaultColumns}", $definition);
        $this->assertStringContainsString('toolbox: '.($toolboxVisible ? 'true' : 'false'), $definition);
        $this->assertStringContainsString('createNode(node)', $definition);
        $this->assertStringContainsString('normalize(node)', $definition);
        $this->assertStringContainsString("widgets/layout/{$folder}/Canvas.vue", $definition);
        $this->assertStringContainsString("widgets/layout/{$folder}/Settings.vue", $definition);
        $this->assertStringContainsString('<template>', $settings);
        $this->assertStringContainsString('pb-grid-settings', $settings);
        $this->assertStringContainsString('Box Shadow', $settings);
        $this->assertStringContainsString('Motion Effects', $settings);
        $this->assertStringContainsString('scrollViewportStart', $settings);
        $this->assertStringContainsString('scrollViewportEnd', $settings);
        $this->assertStringNotContainsString('tokens truncated', $settings);

        $this->assertStringNotContainsString("case '{$type}':", $app);
        $this->assertStringNotContainsString("/widgets/layout/" . ($type === 'grid' ? 'Grid' : 'RowGrid') . '.vue', $app);
    }

    public function test_grid_settings_use_the_shared_dynamic_sidebar_mount(): void
    {
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSame(1, substr_count($app, ':is="loadWidgetSettings(selectedType)"'));
        $this->assertStringNotContainsString("<template v-if=\"selectedType==='grid'||selectedType==='row_grid'\">", $app);
    }

    public function test_grid_and_row_grid_use_compound_unit_controls_for_gap_padding_and_margin(): void
    {
        foreach (['grid', 'row-grid'] as $folder) {
            $settings = file_get_contents(public_path("js/pagebuilder_elementor/widgets/layout/{$folder}/Settings.vue"));

            $this->assertIsString($settings);
            $this->assertStringContainsString("editor.sizeControlDisplayValue(node, 'columnGap', '20px')", $settings);
            $this->assertStringContainsString("editor.sizeControlUnit(node, 'rowGap', '20px')", $settings);
            $this->assertStringContainsString("editor.spacingUnit(node, 'padding')", $settings);
            $this->assertStringContainsString("editor.spacingSideValue(node, 'padding', side)", $settings);
            $this->assertStringContainsString("editor.spacingUnit(node, 'margin')", $settings);
            $this->assertStringContainsString("editor.spacingSideValue(node, 'margin', side)", $settings);
            $this->assertStringNotContainsString("v-model=\"node.settings[editor.activeResponsiveKey('columnGap')]\"", $settings);
            $this->assertStringNotContainsString("v-model=\"node.settings[editor.activeResponsiveKey('paddingTop')]\"", $settings);
        }
    }

    public static function layoutWidgetProvider(): array
    {
        return [
            'grid' => ['grid', 'grid', 'Grid', 3, true],
            'row grid' => ['row_grid', 'row-grid', 'Row Grid', 1, false],
        ];
    }
    public function test_grid_and_row_grid_advanced_dimensions_use_numeric_unit_controls(): void
    {
        foreach (['grid', 'row-grid'] as $folder) {
            $settings = file_get_contents(public_path("js/pagebuilder_elementor/widgets/layout/{$folder}/Settings.vue"));

            $this->assertIsString($settings);
            $this->assertStringContainsString("dimensionValue('borderWidth'", $settings);
            $this->assertStringContainsString("dimensionGroupUnit(responsiveRadiusKeys(), 'px')", $settings);
            $this->assertStringContainsString("dimensionGroupUnit(['shadowH','shadowV','shadowBlur','shadowSpread']", $settings);
            $this->assertStringContainsString("{key:'stickyOffset',label:'Sticky Offset'}", $settings);
            $this->assertStringContainsString("{key:'transformRotate',label:'Rotate'}", $settings);
            $this->assertStringContainsString("dimensionValue(control.key, 'deg')", $settings);
            $this->assertStringContainsString("dimensionGroupUnit(['positionTop','positionRight','positionBottom','positionLeft']", $settings);
            $this->assertStringNotContainsString('v-model="node.settings.borderWidth"', $settings);
            $this->assertStringNotContainsString('v-model="node.settings.shadowH"', $settings);
            $this->assertStringNotContainsString('v-model="node.settings.transformRotate"', $settings);
            $this->assertStringNotContainsString('v-model="node.settings.positionTop"', $settings);
        }
    }
}
