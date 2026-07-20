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

    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }
}
