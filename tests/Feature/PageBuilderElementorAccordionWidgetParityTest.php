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

    public function test_content_tab_exposes_all_audited_layout_icon_and_schema_controls(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        foreach ([
            'Item Position',
            'Icon Position',
            'Expand Icon',
            'Collapse Icon',
            'Title HTML Tag',
            'FAQ Schema',
        ] as $label) {
            $this->assertSourceContains($label, $appJs);
        }

        $this->assertSourceContains("openAccordionIconLibrary('expand'", $appJs);
        $this->assertSourceContains("chooseAccordionSvg('collapse'", $appJs);
        $this->assertSourceContains("activeResponsiveKey('itemPosition')", $appJs);
        $this->assertSourceContains("activeResponsiveKey('iconPosition')", $appJs);
    }

    public function test_style_tab_exposes_accordion_header_and_content_state_controls(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $component = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/Accordion.vue'));

        foreach ([
            'Space Between Items',
            'Distance from Content',
            'Background Type',
            'Gradient Type',
            'Border Type',
            'Border Width',
            'Border Radius',
            'Title Typography',
            'Text Shadow',
            'Text Stroke',
            'Icon Size',
            'Icon Spacing',
        ] as $label) {
            $this->assertSourceContains($label, $appJs);
        }

        $this->assertSourceContains("{ value: 'normal', label: 'Normal' }", $appJs);
        $this->assertSourceContains("{ value: 'hover', label: 'Hover' }", $appJs);
        $this->assertSourceContains("{ value: 'active', label: 'Active' }", $appJs);
        $this->assertSourceContains("'groove'", $appJs);
        $this->assertSourceContains("'radial'", $appJs);
        $this->assertSourceContains("--accordion-item-gap", $component);
        $this->assertSourceContains("--accordion-header-active-title-color", $component);
        $this->assertSourceContains("--accordion-content-padding", $component);
    }

    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }
}
