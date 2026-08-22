<?php

namespace Tests\Feature;

use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Tests\TestCase;

class PageBuilderElementorV24ProModuleBoundaryTest extends TestCase
{
    public function test_all_eighteen_shared_pro_types_resolve_to_independent_module_views(): void
    {
        $markers = [
            'form' => 'data-pro-form',
            'slides' => 'pb-pro-slides',
            'animated_headline' => 'pb-pro-headline',
            'hotspot' => 'pb-pro-hotspot',
            'price_list' => 'pb-pro-price-list',
            'price_table' => 'pb-pro-price-table',
            'call_to_action' => 'pb-pro-cta',
            'countdown' => 'pb-pro-countdown',
            'carousel' => 'pb-pro-carousel',
            'reviews' => 'pb-pro-reviews',
            'testimonial_carousel' => 'pb-pro-testimonial-carousel',
            'media_carousel' => 'pb-pro-media-carousel',
            'flip_box' => 'pb-pro-flip-box',
            'code_highlight' => 'pb-pro-code-highlight',
            'blockquote' => 'pb-pro-blockquote',
            'share_buttons' => 'pb-pro-share-buttons',
            'progress_tracker' => 'pb-pro-progress-tracker',
            'video_playlist' => 'pb-pro-video-playlist',
        ];

        $catalog = app(ModuleCatalog::class);

        foreach ($markers as $type => $marker) {
            $module = $catalog->find($type);
            $this->assertIsArray($module, 'Missing independent Pro module: '.$type);
            $this->assertStringNotContainsString(
                str_replace('/', DIRECTORY_SEPARATOR, 'widgets/pro/shared'),
                $module['assets']['canvas'],
            );

            $html = view('pagebuilder_elementor_v24.partials.render_node', [
                'node' => [
                    'id' => 'boundary-'.$type,
                    'type' => $type,
                    'settings' => [],
                ],
            ])->render();

            $this->assertStringContainsString('data-pro-widget="'.$type.'"', $html);
            $this->assertStringContainsString($marker, $html);
        }
    }
}
