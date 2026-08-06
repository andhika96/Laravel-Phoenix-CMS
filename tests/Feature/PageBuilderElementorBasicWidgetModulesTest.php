<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageBuilderElementorBasicWidgetModulesTest extends TestCase
{
    #[DataProvider('basicWidgetProvider')]
    public function test_basic_widget_has_complete_module_contract(string $type, string $folder, string $label): void
    {
        $catalog = config('pagebuilder_elementor_widgets');
        $this->assertArrayHasKey($type, $catalog);

        $module = $catalog[$type];
        $this->assertSame($label, $module['label']);
        $this->assertSame('basic', $module['category']);
        $this->assertTrue($module['toolbox']);
        $this->assertFileExists(public_path($module['definition']));
        $this->assertFileExists(public_path($module['canvas']));
        $this->assertFileExists(public_path($module['settings']));
        $this->assertTrue(view()->exists($module['view']));

        $definition = file_get_contents(public_path($module['definition']));
        $this->assertStringContainsString("type: '{$type}'", $definition);
        $this->assertStringContainsString('defaults', $definition);
        $this->assertStringContainsString('normalize(node)', $definition);

        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $this->assertStringNotContainsString("<template v-if=\"selectedType==='{$type}'\">", $app);
        $this->assertStringNotContainsString("case '{$type}':", $app);
        $this->assertStringNotContainsString("{$type}:" . str_repeat(' ', max(1, 15 - strlen($type))), $app);
        $this->assertStringContainsString("widgets/basic/{$folder}/Settings.vue", $definition);
    }

    public function test_registered_basic_settings_use_shared_dynamic_sidebar_mount(): void
    {
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertStringContainsString('hasRegisteredWidget(selectedType)', $app);
        $this->assertStringContainsString(':editor="widgetEditorServices"', $app);
        $this->assertSame(1, substr_count($app, ':is="loadWidgetSettings(selectedType)"'));
    }

    public function test_text_editor_settings_and_canvas_follow_the_shared_sidebar_contract(): void
    {
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/text-editor/Settings.vue'));
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/text-editor/Canvas.vue'));

        $this->assertStringContainsString('pb-tab-nav', $settings);
        $this->assertStringContainsString('>Content</span>', $settings);
        $this->assertStringContainsString('>Advanced</span>', $settings);
        $this->assertStringContainsString("editor.settingsTab==='content'", $settings);
        $this->assertStringContainsString("editor.settingsTab==='advanced'", $settings);
        $this->assertStringContainsString('pb-collapsible', $settings);
        $this->assertStringContainsString("['el-widget-text-editor', customClass]", $canvas);
        $this->assertStringContainsString("return String(this.item.settings?.html ?? '');", $canvas);

        $html = view('pagebuilder_elementor.widgets.basic.text-editor', [
            'node' => ['settings' => ['html' => '', 'cssClass' => '']],
        ])->render();
        $this->assertStringNotContainsString('Text editor content', $html);
    }

    public function test_image_settings_use_standard_tabs_media_and_inherited_responsive_dimensions(): void
    {
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/image/Settings.vue'));
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/image/Canvas.vue'));

        $this->assertStringContainsString('pb-tab-nav', $settings);
        foreach (['Content', 'Style', 'Advanced'] as $tab) {
            $this->assertStringContainsString(">$tab</span>", $settings);
        }
        $this->assertStringContainsString('pb-bg-media-field', $settings);
        $this->assertStringNotContainsString('>Image URL</label>', $settings);
        $this->assertStringContainsString('<DimensionSetting label="Width"', $settings);
        $this->assertStringContainsString('<DimensionSetting label="Height"', $settings);
        $this->assertStringContainsString("['el-widget-image', customClass]", $canvas);
        $this->assertStringContainsString("const tabletValue = settings[base + 'Tablet'];", $canvas);
        $this->assertStringContainsString('if (device === \'mobile\'', $canvas);
    }

	public function test_button_settings_and_canvas_follow_shared_sidebar_and_link_contract(): void
	{
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/button/Settings.vue'));
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/button/Canvas.vue'));

        $this->assertIsString($settings);
        $this->assertIsString($canvas);
        $this->assertStringContainsString('class="pb-tab-nav"', $settings);
        foreach (['Content', 'Advanced'] as $tab) {
            $this->assertStringContainsString(">$tab</span>", $settings);
        }
        $this->assertStringContainsString('class="pb-collapsible"', $settings);
        $this->assertStringContainsString(':rel="rel"', $canvas);
        $this->assertStringContainsString("return this.item.settings?.newTab ? 'noopener' : null;", $canvas);
        $this->assertStringContainsString("['el-widget-button', buttonClass]", $canvas);
		$this->assertStringContainsString("return this.item.settings?.text ?? 'Click here';", $canvas);
	}

	public function test_button_icon_control_supports_elementor_sources_position_spacing_and_remove(): void
	{
		$settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/button/Settings.vue'));
		$canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/button/Canvas.vue'));
		$definition = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/button/definition.js'));
		$blade = file_get_contents(resource_path('views/pagebuilder_elementor/widgets/basic/button.blade.php'));
		$app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

		foreach ([$settings, $canvas, $definition, $blade, $app] as $source) $this->assertIsString($source);
		foreach (['Icon', 'Remove icon', 'Upload SVG', 'Icon Library', 'Icon Position', 'Icon Spacing', 'buttonIconSpacing'] as $marker) {
			$this->assertStringContainsString($marker, $settings);
		}
		$this->assertStringContainsString('editor.openIconLibrary(this.node)', $settings);
		$this->assertStringContainsString('editor.chooseButtonSvg(this.node)', $settings);
		$this->assertStringContainsString("this.node.settings.iconSource = 'none'", $settings);
		$this->assertStringContainsString('iconSvgDataUri', $canvas);
		$this->assertStringContainsString('flexDirection: this.hasIcon ? this.iconPosition : \'row\'', $canvas);
		$this->assertStringContainsString("iconSource: 'none'", $definition);
		$this->assertStringContainsString('el-widget-button__icon', $blade);
		$this->assertStringContainsString('rawurlencode($iconSvg)', $blade);
		$this->assertStringContainsString("['icon', 'icon_box', 'image_carousel'].includes(node.type) || node.type === 'button'", $app);
		$this->assertStringContainsString('chooseButtonSvg', $app);
	}

	public function test_icon_style_dimension_controls_expose_elementor_unit_switchers(): void
	{
		$settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/icon/Settings.vue'));

		$this->assertIsString($settings);
		$this->assertStringContainsString(":units=\"['px','%','em','rem','vw']\"", $settings);
		$this->assertStringContainsString(":units=\"['deg','grad','rad','turn']\"", $settings);
		$this->assertStringContainsString('class="pb-mini-unit" :value="parsed.unit"', $settings);
		$this->assertStringContainsString('v-for="option in unitsList"', $settings);
		$this->assertStringNotContainsString('pb-basic-unit-label', $settings);
	}

	public function test_divider_uses_standard_tabs_compound_thickness_control_and_responsive_inheritance(): void
    {
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/divider/Settings.vue'));
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/divider/Canvas.vue'));
		$definition = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/divider/definition.js'));

        $this->assertIsString($settings);
        $this->assertIsString($canvas);
		$this->assertIsString($definition);
        $this->assertStringContainsString('class="pb-tab-nav"', $settings);
        foreach (['Content', 'Style', 'Advanced'] as $tab) {
            $this->assertStringContainsString(">$tab</span>", $settings);
        }
		$this->assertStringContainsString('class="pb-range-value-row"', $settings);
		$this->assertStringContainsString("editor.openControlResponsiveMenu('divider-thickness')", $settings);
		$this->assertStringContainsString("editor.applyResponsiveDevice('divider-thickness', device.value)", $settings);
		$this->assertStringContainsString('class="pb-value-with-unit"><input class="pb-input pb-input-compact"', $settings);
		$this->assertStringContainsString('aria-label="Thickness unit" @change="setThicknessUnit($event)"', $settings);
		$this->assertStringContainsString("allowedUnits: this.thicknessUnits", $settings);
		$this->assertStringContainsString("['el-widget-divider', customClass]", $canvas);
		$this->assertStringContainsString("const tabletValue = settings[base + 'Tablet'];", $canvas);
		$this->assertStringContainsString("const rawThickness = String(this.responsiveValue('thickness', '2px')).trim();", $canvas);
		$this->assertStringContainsString("borderTop: thickness + thicknessUnit", $canvas);
		$this->assertStringContainsString("thicknessUnit: 'px'", $definition);
	}

    public function test_spacer_uses_standard_tabs_and_inherited_responsive_height(): void
    {
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/spacer/Settings.vue'));
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/spacer/Canvas.vue'));

        $this->assertIsString($settings);
        $this->assertIsString($canvas);
        $this->assertStringContainsString('class="pb-tab-nav"', $settings);
        foreach (['Content', 'Advanced'] as $tab) {
            $this->assertStringContainsString(">$tab</span>", $settings);
        }
        $this->assertStringContainsString('class="pb-collapsible"', $settings);
        $this->assertStringContainsString('class="pb-range-value-row"', $settings);
        $this->assertStringContainsString("['el-widget-spacer', customClass]", $canvas);
        $this->assertStringContainsString("const tabletValue = settings[base + 'Tablet'];", $canvas);
    }

    public static function basicWidgetProvider(): array
    {
        return [
            'text editor' => ['text_editor', 'text-editor', 'Text Editor'],
            'image' => ['image', 'image', 'Image'],
            'button' => ['button', 'button', 'Button'],
            'divider' => ['divider', 'divider', 'Divider'],
            'spacer' => ['spacer', 'spacer', 'Spacer'],
            'icon' => ['icon', 'icon', 'Icon'],
        ];
    }
}
