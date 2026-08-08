<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23ShellTest extends TestCase
{
    public function test_v23_create_shell_loads_only_the_v23_editor_context_and_runtime(): void
    {
        $html = $this->get(route('cms.core.pagebuilder_elementor_v23.create'))->assertOk()->getContent();

        $this->assertStringContainsString('id="pbElementorV23App"', $html);
        $this->assertStringContainsString('PAGE_BUILDER_ELEMENTOR_V23_CONTEXT', $html);
        $this->assertStringContainsString("editorVersion: '2.3'", $html);
        $this->assertStringContainsString('assets/css/pagebuilder_elementor_v23.css', $html);
        $this->assertStringContainsString('js/pagebuilder_elementor_v23/frontend-runtime.js', $html);
        $this->assertStringContainsString('js/pagebuilder_elementor_v23/widget-registry.js', $html);
        $this->assertStringContainsString('js/pagebuilder_elementor_v23/app.js', $html);
        $this->assertStringNotContainsString('js/pagebuilder_elementor/frontend-runtime.js', $html);
        $this->assertStringNotContainsString('js/pagebuilder_elementor/widget-registry.js', $html);
        $this->assertStringNotContainsString('js/pagebuilder_elementor/widgets/', $html);
        $this->assertStringNotContainsString('js/pagebuilder_elementor/app.js', $html);
    }
}
