<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorAccordionWidgetParityTest extends TestCase
{
    public function test_editor_registers_advanced_accordion_with_three_nested_items(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSourceContains("accordion:      '/js/pagebuilder_elementor/widgets/advanced/Accordion.vue'", $appJs);
        $this->assertSourceContains('function isAccordion(t)', $appJs);
        $this->assertSourceContains('function accordionWidgetDefaultItems()', $appJs);
        $this->assertSourceContains('accordionItems: accordionWidgetDefaultItems()', $appJs);
        $this->assertSourceContains("advanced: [", $appJs);
        $this->assertSourceContains("{ type:'accordion',   label:'Accordion'", $appJs);
    }

    public function test_recursive_helpers_visit_accordion_item_children(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSourceContains("if (c.type === 'accordion')", $appJs);
        $this->assertSourceContains('item.children = norm(item.children || [])', $appJs);
        $this->assertSourceContains('if (n.accordionItems) for (const item of n.accordionItems)', $appJs);
        $this->assertSourceContains('(node.accordionItems || []).forEach', $appJs);
    }

    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }
}
