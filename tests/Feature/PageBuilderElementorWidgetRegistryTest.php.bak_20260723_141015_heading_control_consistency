<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorWidgetRegistryTest extends TestCase
{
    public function test_heading_module_exposes_complete_registry_contract(): void
    {
        $catalog = config('pagebuilder_elementor_widgets');

        $this->assertIsArray($catalog);
        $this->assertArrayHasKey('heading', $catalog);

        $heading = $catalog['heading'];

        $this->assertSame('heading', $heading['type']);
        $this->assertSame('basic', $heading['category']);
        $this->assertSame('Heading', $heading['label']);
        $this->assertTrue($heading['toolbox']);
        $this->assertFileExists(public_path($heading['definition']));
        $this->assertFileExists(public_path($heading['canvas']));
        $this->assertFileExists(public_path($heading['settings']));
        $this->assertTrue(view()->exists($heading['view']));
    }

    public function test_editor_loads_registry_and_heading_definition_before_app(): void
    {
        $source = file_get_contents(resource_path('views/pagebuilder_elementor/editor_shell.blade.php'));
        $registryPosition = strpos($source, 'pagebuilder_elementor/widget-registry.js');
        $definitionPosition = strpos($source, "['definition']");
        $appPosition = strpos($source, 'pagebuilder_elementor/app.js');

        $this->assertNotFalse($registryPosition);
        $this->assertNotFalse($definitionPosition);
        $this->assertNotFalse($appPosition);
        $this->assertLessThan($definitionPosition, $registryPosition);
        $this->assertLessThan($appPosition, $definitionPosition);
    }

    public function test_heading_defaults_and_sidebar_are_owned_by_its_module(): void
    {
        $definition = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/heading/definition.js'));
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/heading/Settings.vue'));
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        foreach ([
            "type: 'heading'",
            "text: 'Add your heading text'",
            "tag: 'h2'",
            "align: 'left'",
            "color: '#101828'",
            "cssClass: ''",
            'normalize(node)',
        ] as $marker) {
            $this->assertStringContainsString($marker, $definition);
        }

        foreach (['Text', 'HTML Tag', 'Alignment', 'Text Color', 'CSS Class'] as $label) {
            $this->assertStringContainsString($label, $settings);
        }

        $this->assertStringContainsString('loadWidgetSettings(selectedType)', $app);
        $this->assertStringNotContainsString("<template v-if=\"selectedType==='heading'\">", $app);
        $this->assertStringNotContainsString("case 'heading':", $app);
    }

    public function test_editor_mounts_before_preloading_registered_settings_and_preserves_loader_diagnostics(): void
    {
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertIsString($app);

        foreach ([
            'const _sfcModulePromises = {};',
            'const _sfcResolvedModules = {};',
            'function loadSfcModule(path)',
            'function preloadWidgetSettingsModules()',
            'widgetRegistry?.all()',
            'Promise.allSettled(modulePaths.map(loadSfcModule))',
            '_sfcResolvedModules[path] || _settingsCache[type]',
            "console.warn('[PB] Settings preload failed:', modulePaths[index], result.reason);",
            'log(type, msg, detail)',
            'function scheduleWidgetSettingsPreload()',
            'window.requestIdleCallback(run',
            'scheduleWidgetSettingsPreload();',
            '(async function () {',
        ] as $marker) {
            $this->assertStringContainsString($marker, $app);
        }

        foreach (['AdvancedControls.vue', 'TypographyControl.vue', 'LinkControl.vue', 'DynamicTagControl.vue', 'CssFilterControl.vue'] as $sharedControl) {
            $this->assertStringContainsString($sharedControl, $app);
        }

        $mountPosition = strrpos($app, "}).mount('#pbElementorApp');");
        $preloadPosition = strrpos($app, 'scheduleWidgetSettingsPreload();');
        $this->assertNotFalse($mountPosition);
        $this->assertNotFalse($preloadPosition);
        $this->assertLessThan($preloadPosition, $mountPosition);
        $this->assertStringNotContainsString('await preloadWidgetSettingsModules();', $app);
    }

    public function test_heading_frontend_dispatches_through_registered_view(): void
    {
        $renderNode = file_get_contents(resource_path('views/pagebuilder_elementor/partials/render_node.blade.php'));
        $html = view('pagebuilder_elementor.partials.render_node', [
            'node' => [
                'id' => 'registry-heading',
                'type' => 'heading',
                'settings' => [
                    'text' => 'Registry Heading',
                    'tag' => 'h3',
                    'align' => 'center',
                    'color' => '#123456',
                    'cssClass' => 'registry-title',
                ],
            ],
        ])->render();

        $this->assertStringContainsString("config('pagebuilder_elementor_widgets.' . \$type)", $renderNode);
        $this->assertStringContainsString('Registry Heading', $html);
        $this->assertStringContainsString('<h3', $html);
        $this->assertStringContainsString('registry-title', $html);
        $this->assertStringContainsString('text-align:center', str_replace(' ', '', $html));
    }
}
