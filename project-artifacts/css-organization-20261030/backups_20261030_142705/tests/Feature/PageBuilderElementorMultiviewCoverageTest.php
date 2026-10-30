<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorMultiviewCoverageTest extends TestCase
{
    public function test_image_box_and_accordion_keep_direct_responsive_triggers_on_the_trailing_edge(): void
    {
        $css = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        $selector = '.pb-panel.left :is(.pb-widget-settings--image-box, .pb-widget-settings--accordion) .pb-label-row.pb-label-row-device';

        $this->assertStringContainsString($selector . " {\n\tjustify-content: space-between;", $css);
        $this->assertStringContainsString($selector . " > .pb-form-label {\n\tflex: 1 1 auto;\n\tmin-width: 0;", $css);
        $this->assertStringContainsString($selector . " > .pb-control-device-wrap {\n\tflex: 0 0 auto;\n\tmargin-left: auto;", $css);
    }
}
