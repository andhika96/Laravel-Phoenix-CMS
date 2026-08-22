<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24TestimonialCarouselWidgetTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;
    public function test_testimonial_carousel_is_registered_and_frontend_renderer_outputs_safe_interactive_markup(): void
    {
        $module = $this->pageBuilderV24Module('testimonial_carousel');

        $this->assertSame('Testimonial Carousel', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertFileExists($module['assets']['view'] ?? 'missing');
        $this->assertFileExists($module['assets']['definition'] ?? 'missing');

        $html = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'testimonial-carousel-test',
                'type' => 'testimonial_carousel',
                'settings' => [
                    'slidesName' => 'Customer Testimonials',
                    'skin' => 'bubble',
                    'layout' => 'image_left',
                    'slidesToShow' => 1,
                    'slidesToShowTablet' => 1,
                    'slidesToShowMobile' => 1,
                    'slidesToScroll' => 1,
                    'arrows' => true,
                    'previousArrowIcon' => 'fas fa-angle-left',
                    'previousArrowIconSource' => 'library',
                    'nextArrowIcon' => 'fas fa-angle-right',
                    'nextArrowIconSource' => 'library',
                    'arrowPosition' => 'outside',
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
                    'pagination' => 'dots',
                    'autoplay' => true,
                    'autoplaySpeed' => 5000,
                    'items' => [[
                        'id' => 'testimonial-1',
                        'imageUrl' => '/storage/avatar.jpg',
                        'name' => 'John Doe',
                        'title' => 'CEO',
                        'content' => '<script>alert(1)</script> Excellent service',
                    ]],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-pro-widget="testimonial_carousel"', $html);
        $this->assertStringContainsString('data-pro-carousel', $html);
        $this->assertStringContainsString('pb-pro-testimonial-carousel__slide', $html);
        $this->assertStringContainsString('John Doe', $html);
        $this->assertStringContainsString('CEO', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('Previous slide', $html);
        $this->assertStringContainsString('Next slide', $html);
        foreach ([
            'arrow-position-outside',
            '--carousel-arrow-button-size:44px',
            '--carousel-arrow-icon-size:19px',
            '--carousel-arrow-edge-position:calc(0px - 44px - 12px)',
            '--carousel-arrow-color:#112233',
            '--carousel-arrow-background:#ddeeff',
            '--carousel-arrow-hover-color:#ffffff',
            '--carousel-arrow-hover-background:#334455',
            '--carousel-arrow-radius:4px 8px 12px 16px',
            'fas fa-angle-left',
            'fas fa-angle-right',
        ] as $expected) {
            $this->assertStringContainsString($expected, $html);
        }
    }

    public function test_frontend_renderer_preserves_legacy_testimonial_arrow_size(): void
    {
        $module = $this->pageBuilderV24Module('testimonial_carousel');
        $html = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'legacy-testimonial-carousel-arrow-test',
                'type' => 'testimonial_carousel',
                'settings' => [
                    'arrows' => true,
                    'arrowsSize' => '34px',
                    'items' => [['id' => 'one', 'content' => 'Legacy']],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('--carousel-arrow-button-size:34px', $html);
        $this->assertStringContainsString('--carousel-arrow-icon-size:16px', $html);
    }
}
