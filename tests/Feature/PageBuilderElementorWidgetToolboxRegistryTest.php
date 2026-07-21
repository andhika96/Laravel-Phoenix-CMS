<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorWidgetToolboxRegistryTest extends TestCase
{
    public function test_heading_toolbox_item_is_derived_from_the_widget_registry(): void
    {
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertStringContainsString('widgetRegistry?.toolbox()', $app);
        $this->assertStringContainsString('Object.entries(registeredToolbox)', $app);
        $this->assertStringNotContainsString("{ type:'heading',     label:'Heading',     icon:'fas fa-heading' }", $app);
    }
}
