<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23ImageCarouselArrowWidgetTest extends TestCase
{
    public function test_frontend_renderer_applies_complete_responsive_arrow_button_contract(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.image_carousel');
        $html = view($module['view'], [
            'node' => [
                'id' => 'image-carousel-arrow-test',
                'type' => 'image_carousel',
                'settings' => [
                    'images' => [
                        ['id' => 'one', 'url' => '/storage/one.jpg', 'alt' => 'One'],
                        ['id' => 'two', 'url' => '/storage/two.jpg', 'alt' => 'Two'],
                    ],
                    'slidesToShow' => '1',
                    'slidesToScroll' => '1',
                    'navigation' => 'arrows',
                    'previousArrowIcon' => 'fas fa-angle-left',
                    'nextArrowIcon' => 'fas fa-angle-right',
                    'arrowPosition' => 'outside',
                    'arrowPositionTablet' => 'inside',
                    'arrowEdgeOffset' => '12px',
                    'arrowButtonSize' => '44px',
                    'arrowIconSize' => '19px',
                    'arrowColor' => '#112233',
                    'arrowBackground' => '#ddeeff',
                    'arrowHoverColor' => '#ffffff',
                    'arrowHoverBackground' => '#334455',
                    'arrowRadiusTop' => '4px',
                    'arrowRadiusRight' => '8px',
                    'arrowRadiusBottom' => '12px',
                    'arrowRadiusLeft' => '16px',
                ],
            ],
        ])->render();

        foreach ([
            '--pb-carousel-arrow-button-size:44px',
            '--pb-carousel-arrow-icon-size:19px',
            '--pb-carousel-arrow-edge-offset:12px',
            '--pb-carousel-arrow-color:#112233',
            '--pb-carousel-arrow-background:#ddeeff',
            '--pb-carousel-arrow-hover-color:#ffffff',
            '--pb-carousel-arrow-hover-background:#334455',
            '--pb-carousel-arrow-radius:4px 8px 12px 16px',
            'fas fa-angle-left',
            'fas fa-angle-right',
        ] as $expected) {
            $this->assertStringContainsString($expected, $html);
        }

        $this->assertStringContainsString('@media(max-width:1024px)', $html);
    }

    public function test_frontend_renderer_preserves_legacy_arrow_size_for_old_saved_nodes(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.image_carousel');
        $html = view($module['view'], [
            'node' => [
                'id' => 'legacy-image-carousel-arrow-test',
                'type' => 'image_carousel',
                'settings' => [
                    'images' => [
                        ['id' => 'one', 'url' => '/storage/one.jpg', 'alt' => 'One'],
                        ['id' => 'two', 'url' => '/storage/two.jpg', 'alt' => 'Two'],
                    ],
                    'slidesToShow' => '1',
                    'navigation' => 'arrows',
                    'arrowSize' => '33px',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('--pb-carousel-arrow-button-size:45px', $html);
        $this->assertStringContainsString('--pb-carousel-arrow-icon-size:33px', $html);
    }
}
