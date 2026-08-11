<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23ReviewsWidgetTest extends TestCase
{
    public function test_reviews_widget_is_registered_and_frontend_renderer_outputs_safe_interactive_markup(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.reviews');

        $this->assertSame('Reviews', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.partials.render_pro_widget', $module['view'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? 'missing'));

        $html = view($module['view'], [
            'node' => [
                'id' => 'reviews-test',
                'type' => 'reviews',
                'settings' => [
                    'slidesName' => 'Customer Reviews',
                    'slidesToShow' => 1,
                    'slidesToShowTablet' => 1,
                    'slidesToShowMobile' => 1,
                    'slidesToScroll' => 1,
                    'arrows' => true,
                    'pagination' => 'dots',
                    'autoplay' => true,
                    'items' => [[
                        'id' => 'review-1',
                        'imageUrl' => '/storage/avatar.jpg',
                        'name' => 'John Doe',
                        'title' => '@username',
                        'rating' => 4.5,
                        'review' => 'Excellent service',
                        'linkUrl' => '/profile',
                        'linkTarget' => '_blank',
                        'linkNofollow' => true,
                        'linkCustomAttributes' => [
                            ['key' => 'data-review', 'value' => 'john'],
                            ['key' => 'onclick', 'value' => 'alert(1)'],
                        ],
                        'iconSource' => 'library',
                        'iconClass' => 'fab fa-twitter',
                    ]],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-pro-widget="reviews"', $html);
        $this->assertStringContainsString('data-pro-carousel', $html);
        $this->assertStringContainsString('pb-pro-reviews__slide', $html);
        $this->assertStringContainsString('John Doe', $html);
        $this->assertStringContainsString('Excellent service', $html);
        $this->assertStringContainsString('fab fa-twitter', $html);
        $this->assertStringContainsString('href="/profile"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $html);
        $this->assertStringContainsString('data-review="john"', $html);
        $this->assertStringNotContainsString('onclick=', $html);
    }
}

