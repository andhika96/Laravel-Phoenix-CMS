<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23GoogleMapsWidgetTest extends TestCase
{
    public function test_google_maps_is_registered_and_frontend_renderer_outputs_safe_embed_markup(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.google_maps');

        $this->assertSame('Google Maps', $module['label'] ?? null);
        $this->assertSame('basic', $module['category'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.widgets.basic.google-maps', $module['view'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? 'missing'));

        $html = view($module['view'], [
            'node' => [
                'id' => 'google-maps-test',
                'type' => 'google_maps',
                'settings' => [
                    'location' => 'Sydney Opera House & <script>alert(1)</script>',
                    'zoom' => 99,
                    'height' => '420px',
                    'heightTablet' => '320px',
                    'heightMobile' => '260px',
                    'mapNormalFilter' => ['blur' => 2, 'brightness' => 110],
                    'mapHoverFilter' => ['contrast' => 120],
                    'transitionDuration' => 1.2,
                    'cssClass' => 'maps-preview',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-basic-google-maps', $html);
        $this->assertStringContainsString('<iframe', $html);
        $this->assertStringContainsString('https://www.google.com/maps?q=Sydney%20Opera%20House%20%26%20%3Cscript%3Ealert%281%29%3C%2Fscript%3E', html_entity_decode($html));
        $this->assertStringContainsString('&amp;z=20&amp;output=embed', $html);
        $this->assertStringContainsString('title="Google Maps"', $html);
        $this->assertStringContainsString('loading="lazy"', $html);
        $this->assertStringContainsString('height:420px', $html);
        $this->assertStringContainsString('brightness(110%)', $html);
        $this->assertStringContainsString('--pb-google-maps-transition-duration:1.2s', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_google_maps_without_location_renders_placeholder_without_iframe(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.google_maps');

        $html = view($module['view'], [
            'node' => [
                'id' => 'google-maps-empty',
                'type' => 'google_maps',
                'settings' => ['location' => ''],
            ],
        ])->render();

        $this->assertStringContainsString('data-google-maps-empty', $html);
        $this->assertStringNotContainsString('<iframe', $html);
    }
}
