<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23TestimonialCarouselWidgetTest extends TestCase
{
    public function test_testimonial_carousel_is_registered_and_frontend_renderer_outputs_safe_interactive_markup(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.testimonial_carousel');

        $this->assertSame('Testimonial Carousel', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.partials.render_pro_widget', $module['view'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? 'missing'));

        $html = view($module['view'], [
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
    }
}
