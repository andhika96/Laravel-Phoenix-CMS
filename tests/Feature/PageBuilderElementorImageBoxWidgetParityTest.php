<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorImageBoxWidgetParityTest extends TestCase
{
    public function test_registration_maps_general_image_box_with_normalized_dedicated_defaults(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSourceContains("image_box:      '/js/pagebuilder_elementor/widgets/general/ImageBox.vue'", $appJs);
        $this->assertMatchesRegularExpression(
            "/general:\s*\[.*?\{ type:'image_box',\s+label:'Image Box'/s",
            $appJs
        );
        $this->assertSourceContains('function imageBoxWidgetDefaults()', $appJs);
        $this->assertSourceContains("case 'image_box':", $appJs);
        $this->assertSourceContains("settings: imageBoxWidgetDefaults()", $appJs);
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
        $path = public_path('js/pagebuilder_elementor/widgets/general/ImageBox.vue');
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
    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }
}
