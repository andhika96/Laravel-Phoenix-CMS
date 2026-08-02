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

        foreach (['Title', 'Link', 'HTML Tag', 'Alignment', 'Text Color'] as $label) {
            $this->assertStringContainsString($label, $settings);
        }

        $this->assertStringContainsString('loadWidgetSettings(selectedType)', $app);
        $this->assertStringNotContainsString("<template v-if=\"selectedType==='heading'\">", $app);
        $this->assertStringNotContainsString("case 'heading':", $app);
    }

    public function test_heading_settings_use_standard_tabs_picker_and_safe_html_tags(): void
    {
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/heading/Settings.vue'));
        $definition = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/heading/definition.js'));
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/heading/Canvas.vue'));
        $blade = file_get_contents(resource_path('views/pagebuilder_elementor/widgets/basic/heading.blade.php'));
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertStringContainsString('pb-tab-nav', $settings);
        foreach (['Content', 'Style', 'Advanced'] as $tab) {
            $this->assertStringContainsString(">$tab</span>", $settings);
        }
        $this->assertStringContainsString('pb-color-row', $settings);
        $this->assertStringNotContainsString('pb-color-swatch', $settings);
        $this->assertStringContainsString('pb-input coloris pb-coloris-input', $settings);
        $this->assertStringContainsString("const allowedTags = Object.freeze(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p']);", $definition);
        $this->assertStringContainsString('settings.tag = allowedTags.includes(String(settings.tag).toLowerCase())', $definition);
        $this->assertStringContainsString('safeTag()', $canvas);
        $this->assertStringContainsString("['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p']", $blade);
        $this->assertStringContainsString("settingsTab.value = type && !isCont(type) && !isGrid(type) ? 'content' : 'layout';", $app);
    }

    public function test_editor_waits_for_the_first_gesture_to_settle_before_preloading_registered_settings(): void
    {
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertIsString($app);

        foreach ([
            'const _sfcModulePromises = {};',
            'const _sfcResolvedModules = {};',
            'function loadSfcModule(path)',
            'function preloadWidgetSettingsModules()',
            'function preloadSettingsModuleAtIdle(modulePaths, cursor, onComplete)',
            'widgetRegistry?.all()',
            'const path = modulePaths[cursor];',
            'preloadSettingsModuleAtIdle(modulePaths, cursor + 1, onComplete);',
            '_sfcResolvedModules[path] || _settingsCache[type]',
            "console.warn('[PB] Settings preload failed:', path, error);",
            'log(type, msg, detail)',
            'function scheduleWidgetSettingsPreloadAfterFirstGesture()',
            'window.requestIdleCallback(run',
            "window.addEventListener('pointerdown', observeFirstGesture, { once: true, passive: true });",
            "window.addEventListener('dragend', settleAfterDrag, { once: true });",
            'scheduleWidgetSettingsPreloadAfterFirstGesture();',
            '(async function () {',
        ] as $marker) {
            $this->assertStringContainsString($marker, $app);
        }

        foreach (['AdvancedControls.vue', 'TypographyControl.vue', 'LinkControl.vue', 'DynamicTagControl.vue', 'CssFilterControl.vue'] as $sharedControl) {
            $this->assertStringContainsString($sharedControl, $app);
        }

        $mountPosition = strrpos($app, "}).mount('#pbElementorApp');");
        $preloadPosition = strrpos($app, 'scheduleWidgetSettingsPreloadAfterFirstGesture();');
        $this->assertNotFalse($mountPosition);
        $this->assertNotFalse($preloadPosition);
        $this->assertLessThan($preloadPosition, $mountPosition);
        $this->assertStringNotContainsString('await preloadWidgetSettingsModules();', $app);
        $this->assertStringNotContainsString('Promise.allSettled(modulePaths.map(loadSfcModule))', $app);
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

    public function test_heading_exposes_elementor_content_style_and_advanced_contracts(): void
    {
        $definition = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/heading/definition.js'));
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/heading/Settings.vue'));
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/heading/Canvas.vue'));
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        foreach ([
            "linkUrl: ''",
            "linkTarget: ''",
            'linkNofollow: false',
            'linkCustomAttributes: []',
            'dynamicBindings:',
            "headingFontFamily: 'inherit'",
            "headingTextStrokeWidth: '0px'",
            "headingTextShadow: 'none'",
            "blendMode: 'normal'",
            "hoverColor: ''",
            'hoverTransitionDuration: 0.3',
        ] as $marker) {
            $this->assertStringContainsString($marker, $definition);
        }

        foreach ([
            'editor.linkControl',
            'editor.typographyControl',
            'editor.textStrokeControl',
            'editor.textShadowControl',
            'editor.widgetAdvancedControls',
            'Justified',
            'Blend Mode',
            'Normal',
            'Hover',
            'Link Color',
            'Transition Duration',
        ] as $marker) {
            $this->assertStringContainsString($marker, $settings);
        }

        $this->assertStringNotContainsString('editor.dynamicTagControl', $settings);

        $this->assertStringContainsString("value:'justify',icon:'fas fa-align-justify'", $settings);
        $this->assertStringContainsString('safeLinkUrl', $canvas);
        $this->assertStringContainsString('heading-title-link', $canvas);
        $this->assertStringContainsString("this.node.type === 'heading'", $app);
    }

    public function test_heading_and_general_widgets_hide_non_reference_advanced_sections_by_default(): void
    {
        $headingSettings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/heading/Settings.vue'));
        $sharedControls = file_get_contents(public_path('js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue'));
        $imageBoxSettings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/general/image-box/Settings.vue'));

        $this->assertStringContainsString(':show-display-conditions="false"', $headingSettings);
        $this->assertStringContainsString(':show-cache-settings="false"', $headingSettings);

        $this->assertStringContainsString('<details v-if="showDisplayConditions"', $sharedControls);
        $this->assertStringContainsString('<details v-if="showCacheSettings"', $sharedControls);
        $this->assertStringContainsString("showDisplayConditions: { type: Boolean, default: false }", $sharedControls);
        $this->assertStringContainsString("showCacheSettings: { type: Boolean, default: false }", $sharedControls);

        $this->assertStringContainsString(':show-display-conditions="false"', $imageBoxSettings);
        $this->assertStringContainsString(':show-cache-settings="false"', $imageBoxSettings);
    }

    public function test_heading_frontend_renders_safe_link_typography_and_advanced_wrapper(): void
    {
        $html = view('pagebuilder_elementor.partials.render_node', [
            'node' => [
                'id' => 'heading-parity',
                'type' => 'heading',
                'settings' => [
                    'text' => 'Elementor Heading',
                    'tag' => 'h2',
                    'linkUrl' => 'https://example.com/docs',
                    'linkTarget' => '_blank',
                    'linkNofollow' => true,
                    'linkCustomAttributes' => [['key' => 'data-track', 'value' => 'heading']],
                    'align' => 'center',
                    'alignTablet' => 'right',
                    'alignMobile' => 'justify',
                    'color' => '#112233',
                    'hoverColor' => '#445566',
                    'hoverTransitionDuration' => .4,
                    'headingFontFamily' => 'Arial, sans-serif',
                    'headingFontSize' => '32px',
                    'headingFontSizeTablet' => '28px',
                    'headingFontSizeMobile' => '24px',
                    'headingFontWeight' => '700',
                    'headingTextTransform' => 'uppercase',
                    'headingFontStyle' => 'italic',
                    'headingTextDecoration' => 'underline',
                    'headingLineHeight' => '1.2em',
                    'headingLetterSpacing' => '1px',
                    'headingWordSpacing' => '2px',
                    'headingTextStrokeWidth' => '1px',
                    'headingTextStrokeColor' => '#abcdef',
                    'headingTextShadow' => '1px 2px 3px rgba(0,0,0,.4)',
                    'blendMode' => 'multiply',
                    'marginTop' => '12px',
                    'cssId' => 'hero-heading',
                    'cssClass' => 'tracking-heading',
                    'attributes' => [['name' => 'data-widget', 'value' => 'heading']],
                    'customCssCode' => 'selector { opacity: .95; }',
                ],
            ],
        ])->render();

        $compact = preg_replace('/\s+/', ' ', $html);

        $this->assertStringContainsString('id="hero-heading"', $compact);
        $this->assertStringContainsString('pb-heading-widget', $compact);
        $this->assertStringContainsString('data-widget="heading"', $compact);
        $this->assertStringContainsString('<h2', $compact);
        $this->assertStringContainsString('elementor-heading-title', $compact);
        $this->assertStringContainsString('href="https://example.com/docs"', $compact);
        $this->assertStringContainsString('target="_blank"', $compact);
        $this->assertStringContainsString('rel="nofollow noopener noreferrer"', $compact);
        $this->assertStringContainsString('data-track="heading"', $compact);
        $this->assertStringContainsString('font-family:Arial, sans-serif', $compact);
        $this->assertStringContainsString('font-size:32px', $compact);
        $this->assertStringContainsString('-webkit-text-stroke:1px #abcdef', $compact);
        $this->assertStringContainsString('mix-blend-mode:multiply', $compact);
        $this->assertStringContainsString('@media(max-width:1024px)', str_replace(' ', '', $html));
        $this->assertStringContainsString('@media(max-width:767px)', str_replace(' ', '', $html));
        $this->assertStringContainsString('#hero-heading{opacity:.95;}', str_replace(' ', '', $html));
    }
}
