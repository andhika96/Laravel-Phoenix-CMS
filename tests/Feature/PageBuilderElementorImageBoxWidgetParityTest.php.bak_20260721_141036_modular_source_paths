<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorImageBoxWidgetParityTest extends TestCase
{
    public function test_registration_maps_general_image_box_with_normalized_dedicated_defaults(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $definition = file_get_contents(public_path('js/pagebuilder_elementor/widgets/general/image-box/definition.js'));
        $module = config('pagebuilder_elementor_widgets.image_box');

        $this->assertSame('js/pagebuilder_elementor/widgets/general/image-box/Canvas.vue', $module['canvas']);
        $this->assertSame('js/pagebuilder_elementor/widgets/general/image-box/Settings.vue', $module['settings']);
        $this->assertSourceContains("type: 'image_box'", $definition);
        $this->assertSourceContains("label: 'Image Box'", $definition);
        $this->assertSourceContains('function imageBoxWidgetDefaults()', $appJs);
        $this->assertFalse(str_contains($appJs, "case 'image_box':"));
        $this->assertSourceContains("if (c.type === 'image_box')", $appJs);
        $this->assertSourceContains('normalizeImageBoxSettings(c.settings)', $appJs);

        foreach ([
            "imageUrl: ''",
            "imageAlt: ''",
            "imageResolution: 'full'",
            "title: 'This is the heading'",
            'description:',
            "linkUrl: ''",
            "linkTarget: ''",
            'linkNofollow: false',
            'linkCustomAttributes: []',
            "titleTag: 'h3'",
            'dynamicBindings: {}',
            "imagePosition: 'top'",
            "alignment: 'center'",
            "imageSpacing: '15px'",
            "contentSpacing: '0px'",
            "imageWidth: '30%'",
            "imageBorderType: 'none'",
            "imageBorderRadius: '0px'",
            'imageNormalFilter:',
            'imageHoverFilter:',
            'imageNormalOpacity: 1',
            'imageHoverOpacity: 1',
            'imageHoverTransition: 0.3',
            "titleColor: ''",
            "descriptionColor: ''",
            "titleFontFamily: 'inherit'",
            "descriptionFontFamily: 'inherit'",
            '...widgetAdvancedDefaults()',
        ] as $marker) {
            $this->assertSourceContains($marker, $appJs);
        }

        foreach ([
            'imagePositionTablet', 'imagePositionMobile',
            'alignmentTablet', 'alignmentMobile',
            'imageSpacingTablet', 'imageSpacingMobile',
            'contentSpacingTablet', 'contentSpacingMobile',
            'imageWidthTablet', 'imageWidthMobile',
            'imageBorderRadiusTablet', 'imageBorderRadiusMobile',
            'titleFontSizeTablet', 'titleFontSizeMobile',
            'descriptionFontSizeTablet', 'descriptionFontSizeMobile',
        ] as $responsiveKey) {
            $this->assertSourceContains($responsiveKey, $appJs);
        }

        foreach ([
            'imageBoxResolutionOptions',
            'imageBoxTitleTagOptions',
            'imageBoxPositionOptions',
            'imageBoxAlignmentOptions',
            'imageBoxBorderTypeOptions',
            'normalizeImageBoxSettings(settings)',
        ] as $normalizationMarker) {
            $this->assertSourceContains($normalizationMarker, $appJs);
        }
    }

    public function test_shared_controls_are_prefix_aware_accessible_and_reusable(): void
    {
        foreach ([
            public_path('js/pagebuilder_elementor/widgets/shared/LinkControl.vue'),
            public_path('js/pagebuilder_elementor/widgets/shared/DynamicTagControl.vue'),
            public_path('js/pagebuilder_elementor/widgets/shared/CssFilterControl.vue'),
        ] as $path) {
            $this->assertFileExists($path);
        }

        $typography = file_get_contents(public_path('js/pagebuilder_elementor/widgets/shared/TypographyControl.vue'));
        $link = file_get_contents(public_path('js/pagebuilder_elementor/widgets/shared/LinkControl.vue'));
        $dynamicTag = file_get_contents(public_path('js/pagebuilder_elementor/widgets/shared/DynamicTagControl.vue'));
        $filters = file_get_contents(public_path('js/pagebuilder_elementor/widgets/shared/CssFilterControl.vue'));

        $this->assertSourceContains("prefix: { type: String, default: 'header' }", $typography);
        $this->assertSourceContains('settingKey(base)', $typography);
        $this->assertSourceContains("return this.prefix + base", $typography);
        $this->assertSourceContains("responsiveKey('FontSize')", $typography);
        $this->assertSourceContains("settingKey('FontWeight')", $typography);
        $this->assertSourceContains("resetDefaults", $typography);

        foreach (['URL', 'Open in new window', 'Add nofollow', 'Custom Attributes'] as $label) {
            $this->assertSourceContains($label, $link);
        }
        $this->assertSourceContains("rel: ['noopener', 'noreferrer'", $link);
        $this->assertSourceContains('aria-label="Link options"', $link);

        foreach (['page_title', 'page_excerpt', 'featured_image', 'page_url', 'site_title', 'site_url', 'user_display_name'] as $tag) {
            $this->assertSourceContains($tag, $dynamicTag);
        }
        $this->assertSourceContains('aria-label="Dynamic tags"', $dynamicTag);
        $this->assertSourceContains('this.$emit(\'update:modelValue\'', $dynamicTag);

        foreach (['Blur', 'Brightness', 'Contrast', 'Saturation', 'Hue'] as $label) {
            $this->assertSourceContains($label, $filters);
        }
        $this->assertSourceContains('aria-label="CSS Filters"', $filters);
        $this->assertSourceContains('resetFilters()', $filters);
        $this->assertSourceContains('this.$emit(\'update:modelValue\'', $filters);
    }
    public function test_editor_canvas_renders_responsive_styled_and_safe_image_box(): void
    {
        $path = public_path('js/pagebuilder_elementor/widgets/general/image-box/Canvas.vue');
        $this->assertFileExists($path);
        $component = file_get_contents($path);

        foreach ([
            "name: 'GeneralImageBox'",
            ':is="safeTitleTag"',
            'pb-image-box__image',
            'pb-image-box__title',
            'pb-image-box__description',
            'responsiveValue(base',
            'boxStyle()',
            'imageStyle()',
            'titleStyle()',
            'descriptionStyle()',
            'filterCss(filters)',
            'safeLinkUrl',
            'linkRel',
            'safeCustomAttributes',
            'settings() {',
            '--pb-image-box-hover-filter',
            '--pb-image-box-hover-opacity',
            'prefers-reduced-motion: reduce',
        ] as $marker) {
            $this->assertSourceContains($marker, $component);
        }

        $this->assertFalse(str_contains($component, 'v-html'));
        $this->assertMatchesRegularExpression('/<a[^>]+pb-image-box__image-link.*?<img/s', $component);
        $this->assertMatchesRegularExpression('/<a[^>]+pb-image-box__title-link.*?<component/s', $component);
        $this->assertDoesNotMatchRegularExpression('/<a[^>]*>\s*<p class="pb-image-box__description"/s', $component);
    }
    public function test_settings_panel_matches_confirmed_elementor_controls_and_accordion_rhythm(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $css = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        foreach ([
            "const LinkControl = defineAsyncComponent",
            "const DynamicTagControl = defineAsyncComponent",
            "const CssFilterControl = defineAsyncComponent",
            'components: { draggable, BuilderNode, CkEditorField, WidgetAdvancedControls, TypographyControl, LinkControl, DynamicTagControl, CssFilterControl }',
            "selectedType==='image_box'",
            'pb-image-box-settings',
            'Choose Image',
            'Image Resolution',
            'Title HTML Tag',
            '<LinkControl',
            '<DynamicTagControl',
            'Image Position',
            'Alignment',
            'Image Spacing',
            'Content Spacing',
            'Width',
            'Border Type',
            'Border Radius',
            '<CssFilterControl',
            'Opacity',
            'Transition Duration',
            '<TypographyControl',
            'Text Stroke',
            'Text Shadow',
            '<WidgetAdvancedControls',
            "activeResponsiveKey('imagePosition')",
            "activeResponsiveKey('alignment')",
            "imageBoxImageState==='normal'",
            'pb-state-tabs pb-state-tabs--two',
        ] as $marker) {
            $this->assertSourceContains($marker, $appJs);
        }

        foreach ([
            '.pb-panel.left .pb-image-box-settings',
            '.pb-image-box-settings .pb-tab-btn',
            '.pb-image-box-settings .pb-collapsible > summary',
            '.pb-image-box-settings .pb-collapsible-body',
            '.pb-image-box-settings .pb-form-group',
            '.pb-image-box-dynamic-field',
            '.pb-image-box-segmented',
            '.pb-image-box-settings .pb-state-tabs--two',
        ] as $selector) {
            $this->assertSourceContains($selector, $css);
        }

        $this->assertSourceContains('gap: 13px;', $css);
        $this->assertSourceContains('margin-bottom: 7px;', $css);
        $this->assertSourceContains('min-height: 48px;', $css);
    }

    public function test_image_box_settings_reuse_compact_segmented_controls_and_layout_spacing(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $css = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        foreach ([
            'pb-form-group pb-image-box-choice-row',
            'pb-btn-group pb-image-box-segmented pb-image-box-segmented--three',
            'pb-btn-group pb-image-box-segmented pb-image-box-segmented--four',
            'class="pb-seg-btn"',
            ':aria-pressed=',
        ] as $marker) {
            $this->assertSourceContains($marker, $appJs);
        }

        foreach ([
            '.pb-panel.left .pb-image-box-settings .pb-tab-content',
            '.pb-panel.left .pb-image-box-settings .pb-collapsible-body',
            'padding: 8px 0 4px;',
            '.pb-image-box-choice-row',
            'grid-template-columns: minmax(0, 1fr) auto;',
            'flex-wrap: nowrap;',
            'flex: 0 0 28px;',
        ] as $marker) {
            $this->assertSourceContains($marker, $css);
        }
    }

    public function test_default_top_center_alignment_centers_media_in_canvas_and_frontend(): void
    {
        $component = file_get_contents(public_path('js/pagebuilder_elementor/widgets/general/image-box/Canvas.vue'));
        $partial = file_get_contents(resource_path('views/pagebuilder_elementor/partials/render_image_box.blade.php'));
        $frontendCss = file_get_contents(public_path('assets/css/frontend_elementor.css'));

        $this->assertSourceContains("'--pb-image-box-media-justify': this.position === 'top' ? this.flexAlignment(this.alignment) : 'center'", $component);
        $this->assertSourceContains('justify-content: var(--pb-image-box-media-justify, center);', $component);
        $this->assertSourceContains("'--pb-image-box-media-justify:' . (\$desktopPosition === 'top' ? \$alignItems(\$desktopAlignment) : 'center')", $partial);
        $this->assertSourceContains("'--pb-image-box-media-justify:' . (\$currentPosition === 'top' ? \$alignItems(\$currentAlignment) : 'center')", $partial);
        $this->assertSourceContains('justify-content: var(--pb-image-box-media-justify, center);', $frontendCss);

        $html = view('pagebuilder_elementor.partials.render_image_box', [
            'node' => [
                'id' => 'default-centered-image-box',
                'type' => 'image_box',
                'settings' => [
                    'imageUrl' => '/images/example.jpg',
                    'title' => 'Centered by default',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('--pb-image-box-media-justify:center', str_replace(' ', '', $html));
    }

    public function test_frontend_renderer_resolves_safe_dynamic_content_links_responsive_styles_and_advanced_controls(): void
    {
        $renderNode = file_get_contents(resource_path('views/pagebuilder_elementor/partials/render_node.blade.php'));
        $partialPath = resource_path('views/pagebuilder_elementor/partials/render_image_box.blade.php');
        $frontendCss = file_get_contents(public_path('assets/css/frontend_elementor.css'));

        $this->assertFileExists($partialPath);
        $partial = file_get_contents($partialPath);

        $this->assertSourceContains("@elseif(\$type === 'image_box')", $renderNode);
        $this->assertSourceContains('pagebuilder_elementor.partials.render_image_box', $renderNode);
        $this->assertSourceContains("in_array(\$type, ['accordion', 'image_box'], true)", $renderNode);
        $this->assertSourceContains('pagebuilder_dynamic_context', $renderNode);
        $this->assertSourceContains("'user_id' =>", $renderNode);
        $this->assertSourceContains('DynamicTagResolver::class', $partial);
        $this->assertSourceContains('ImageRenditionResolver::class', $partial);
        $this->assertSourceContains('WidgetAdvancedStyleResolver::class', $partial);
        $this->assertSourceContains('pb-image-box__image-link', $partial);
        $this->assertSourceContains('pb-image-box__title-link', $partial);
        $this->assertSourceContains('pb-image-box__description', $partial);
        $this->assertSourceContains("@media (max-width: ' . \$breakpoint . 'px)", $partial);
        $this->assertSourceContains('--pb-image-box-hover-filter', $partial);

        foreach ([
            '.el-widget-image-box',
            '.pb-image-box__media',
            '.pb-image-box__image',
            '.pb-image-box__content',
            '.pb-image-box__title',
            '.pb-image-box__description',
            '.pb-image-box--position-left',
            '.pb-image-box--position-right',
            '@media (prefers-reduced-motion: reduce)',
        ] as $selector) {
            $this->assertSourceContains($selector, $frontendCss);
        }

        request()->attributes->set('pagebuilder_dynamic_context', [
            'page' => ['page_name' => 'Dynamic Image Box Title'],
        ]);

        $html = view('pagebuilder_elementor.partials.render_image_box', [
            'node' => [
                'id' => 'image-box-test',
                'type' => 'image_box',
                'settings' => [
                    'imageUrl' => '/images/example.jpg',
                    'imageAlt' => 'Accessible example',
                    'imageResolution' => 'full',
                    'title' => 'Static title',
                    'description' => '<script>alert(1)</script> Safe description',
                    'dynamicBindings' => ['title' => 'page_title'],
                    'linkUrl' => '/documentation',
                    'linkTarget' => '_blank',
                    'linkNofollow' => true,
                    'linkCustomAttributes' => [
                        ['key' => 'data-track', 'value' => 'image-box'],
                        ['key' => 'onclick', 'value' => 'alert(1)'],
                    ],
                    'titleTag' => 'h2',
                    'imagePosition' => 'left',
                    'imagePositionTablet' => 'top',
                    'imagePositionMobile' => 'right',
                    'alignment' => 'left',
                    'alignmentTablet' => 'center',
                    'alignmentMobile' => 'right',
                    'imageSpacing' => '16px',
                    'contentSpacing' => '8px',
                    'imageWidth' => '40%',
                    'imageWidthTablet' => '60%',
                    'imageWidthMobile' => '100%',
                    'imageBorderType' => 'solid',
                    'imageBorderWidth' => '2px',
                    'imageBorderColor' => '#123456',
                    'imageBorderRadius' => '12px',
                    'imageNormalFilter' => ['blur' => 0, 'brightness' => 100, 'contrast' => 100, 'saturation' => 100, 'hue' => 0],
                    'imageHoverFilter' => ['blur' => 1, 'brightness' => 90, 'contrast' => 110, 'saturation' => 105, 'hue' => 5],
                    'imageNormalOpacity' => 0.9,
                    'imageHoverOpacity' => 0.7,
                    'imageHoverTransition' => 0.4,
                    'titleColor' => '#111827',
                    'descriptionColor' => '#475467',
                    'marginTop' => '12px',
                    'hideMobile' => true,
                    'cssClass' => 'marketing-card <unsafe>',
                    'attributes' => [
                        ['name' => 'data-section', 'value' => 'hero'],
                        ['name' => 'onmouseover', 'value' => 'alert(1)'],
                    ],
                    'customCssCode' => 'selector .pb-image-box__title{font-weight:700}',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('id="pb-node-image-box-test"', $html);
        $this->assertStringContainsString('Dynamic Image Box Title', $html);
        $this->assertStringNotContainsString('Static title', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt; Safe description', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertSame(2, substr_count($html, 'href="/documentation"'));
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $html);
        $this->assertStringContainsString('data-track="image-box"', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringContainsString('data-section="hero"', $html);
        $this->assertStringNotContainsString('onmouseover=', $html);
        $this->assertStringContainsString('class="el-widget-image-box pb-image-box pb-advanced-widget marketing-card unsafe pb-hide-mobile pb-image-box--position-left"', $html);
        $this->assertStringContainsString('margin-top:12px', $html);
        $this->assertStringContainsString('#pb-node-image-box-test .pb-image-box__title{font-weight:700}', $html);
        $compactHtml = str_replace(' ', '', $html);
        $this->assertStringContainsString('@media(max-width:1024px)', $compactHtml);
        $this->assertStringContainsString('@media(max-width:767px)', $compactHtml);
    }

    public function test_frontend_renderer_rejects_unsafe_image_and_link_urls(): void
    {
        $html = view('pagebuilder_elementor.partials.render_image_box', [
            'node' => [
                'id' => 'unsafe-image-box',
                'type' => 'image_box',
                'settings' => [
                    'imageUrl' => 'javascript:alert(1)',
                    'title' => 'Safe title',
                    'description' => 'Safe description',
                    'linkUrl' => 'javascript:alert(2)',
                    'titleTag' => 'script',
                ],
            ],
        ])->render();

        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringContainsString('<h3 class="pb-image-box__title"', $html);
        $this->assertStringContainsString('pb-image-box__empty-media', $html);
    }
    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }
}
