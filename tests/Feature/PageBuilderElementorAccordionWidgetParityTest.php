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

    public function test_accordion_sidebar_and_canvas_use_separate_runtime_state(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSourceContains('const accordionRuntimeState = ref({});', $appJs);
        $this->assertSourceContains('function accordionRuntimeForNode(node)', $appJs);
        $this->assertSourceContains('editingItemId', $appJs);
        $this->assertSourceContains('expandedItemIds', $appJs);
        $this->assertSourceContains('function toggleAccordionItem(node, itemId)', $appJs);
        $this->assertFalse(str_contains($appJs, 'settings.editorExpandedItemIds'));
    }

    public function test_editor_exposes_accordion_item_actions_and_interactions(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSourceContains('function addAccordionItem(node = selectedNode.value)', $appJs);
        $this->assertSourceContains('function duplicateAccordionItem(node = selectedNode.value', $appJs);
        $this->assertSourceContains('function removeAccordionItem(node = selectedNode.value', $appJs);
        $this->assertSourceContains('accordionItemsForNode(selectedNode)', $appJs);
        $this->assertSourceContains('Add Item', $appJs);
        $this->assertSourceContains('Default State', $appJs);
        $this->assertSourceContains('Max Items Expanded', $appJs);
        $this->assertSourceContains('Animation Duration', $appJs);
    }

    public function test_accordion_preview_component_renders_accessible_animated_headers(): void
    {
        $componentPath = public_path('js/pagebuilder_elementor/widgets/advanced/Accordion.vue');

        $this->assertFileExists($componentPath);

        $component = file_get_contents($componentPath);
        $this->assertSourceContains("name: 'AdvancedAccordion'", $component);
        $this->assertSourceContains(':aria-expanded="isExpanded(item.id) ? \'true\' : \'false\'"', $component);
        $this->assertSourceContains("this.\$emit('toggle-item', item.id)", $component);
        $this->assertSourceContains('prefers-reduced-motion: reduce', $component);
    }

    public function test_editor_renders_targeted_nested_dropzones_for_each_expanded_item(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSourceContains('isAccordionNode() { return isAccordion(this.node.type); }', $appJs);
        $this->assertSourceContains('data-parent-node-type="accordion"', $appJs);
        $this->assertSourceContains("pendingInsertTarget.type === 'accordion'", $appJs);
        $this->assertSourceContains("onShowToolbox({ type: 'accordion', nodeId: node.id, itemId: item.id })", $appJs);
        $this->assertSourceContains("if (target.type === 'accordion')", $appJs);
        $this->assertSourceContains('function rerouteAccordionDropToNestedColumn(evt, itemChildren)', $appJs);
    }

    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }
}
