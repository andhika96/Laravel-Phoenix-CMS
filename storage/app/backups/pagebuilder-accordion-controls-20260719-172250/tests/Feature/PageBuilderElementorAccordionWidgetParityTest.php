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

    public function test_accordion_runtime_normalization_does_not_reassign_equal_arrays_during_render(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSourceContains('function sameStringArray(left, right)', $appJs);
        $this->assertSourceContains('if (!sameStringArray(runtime.expandedItemIds, normalizedExpandedItemIds))', $appJs);
        $this->assertSourceContains('if (!sameStringArray(runtime.transitioningItemIds, normalizedTransitioningItemIds))', $appJs);
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
		$this->assertSourceContains(':aria-expanded="isVisuallyExpanded(item.id) ? \'true\' : \'false\'"', $component);
        $this->assertSourceContains("this.\$emit('toggle-item', item.id)", $component);
        $this->assertSourceContains('prefers-reduced-motion: reduce', $component);
    }

    public function test_accordion_preview_animates_from_measured_pixel_heights_before_using_auto(): void
    {
        $component = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/Accordion.vue'));

		$this->assertSourceContains('startPanelTransitions(transitions)', $component);
		$this->assertSourceContains('skipNextExpandedWatch', $component);
		$this->assertSourceContains("'is-opening': state === 'opening'", $component);
		$this->assertSourceContains("'is-closing': state === 'closing'", $component);
		$this->assertSourceContains('@keyframes pb-accordion-open', $component);
		$this->assertSourceContains('@keyframes pb-accordion-close', $component);
		$this->assertSourceContains('var(--accordion-animation-duration, 400ms)', $component);
		$this->assertSourceContains("endingState === 'opening' ? 'auto' : '0px'", $component);
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
		$this->assertFalse(str_contains($appJs, 'v-show="expanded"'));
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

    public function test_accordion_style_dimensions_use_numeric_unit_aware_controls(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

		$this->assertSourceContains('pb-accordion-dimension-control', $appJs);
		$this->assertSourceContains('pb-accordion-box-control', $appJs);
        $this->assertSourceContains('accordionDimensionValue(selectedNode', $appJs);
        $this->assertSourceContains('setAccordionDimensionUnit(selectedNode', $appJs);
        $this->assertSourceContains('accordionBoxSideValue(selectedNode', $appJs);
        $this->assertSourceContains('toggleAccordionBoxLink(', $appJs);
        $this->assertSourceContains('type="number"', $appJs);
        $this->assertSourceContains('type="range"', $appJs);
    }

    public function test_frontend_renders_semantic_nested_accordion_and_safe_faq_schema(): void
    {
        $html = view('pagebuilder_elementor.partials.render_node', ['node' => [
            'id' => 'accordion-main',
            'type' => 'accordion',
            'settings' => [
                'defaultState' => 'first-expanded',
                'maxExpanded' => 'one',
                'animationDuration' => 400,
                'faqSchema' => true,
                'titleTag' => 'h3',
                'expandIconSource' => 'library',
                'expandIconClass' => 'fas fa-plus',
                'collapseIconSource' => 'library',
                'collapseIconClass' => 'fas fa-minus',
                'accordionBorderRadius' => '4px 8px 12px 16px',
                'contentPadding' => '10px 20px 30px 40px',
            ],
            'accordionItems' => [
                [
                    'id' => 'first',
                    'title' => 'First question?',
                    'cssId' => 'first-question',
                    'children' => [[
                        'id' => 'answer-heading',
                        'type' => 'heading',
                        'settings' => ['tag' => 'h4', 'text' => 'First answer'],
                    ]],
                ],
                [
                    'id' => 'second',
                    'title' => 'Second question?',
                    'cssId' => 'invalid id',
                    'children' => [[
                        'id' => 'answer-text',
                        'type' => 'text_editor',
                        'settings' => ['html' => '<p>Second <strong>answer</strong></p>'],
                    ]],
                ],
            ],
        ]])->render();

        $this->assertStringContainsString('data-accordion-root="1"', $html);
        $this->assertSame(2, substr_count($html, '<details'));
        $this->assertStringContainsString('data-max-expanded="one"', $html);
        $this->assertStringContainsString('data-animation-duration="400"', $html);
        $this->assertStringContainsString('id="first-question"', $html);
        $this->assertStringNotContainsString('id="invalid id"', $html);
        $this->assertStringContainsString('pb-accordion-summary-accordion-main-first', $html);
        $this->assertStringContainsString('aria-expanded="true"', $html);
        $this->assertStringContainsString('<h4 class="el-widget-heading"', $html);
        $this->assertStringContainsString('"@type":"FAQPage"', $html);
        $this->assertStringContainsString('First question?', $html);
        $this->assertStringContainsString('Second answer', $html);
        $this->assertStringContainsString('--accordion-border-radius:4px 8px 12px 16px', $html);
        $this->assertStringContainsString('--accordion-content-padding:10px 20px 30px 40px', $html);
    }

    public function test_frontend_runtime_is_shared_by_editor_and_renderer_shells(): void
    {
        $runtimePath = public_path('js/pagebuilder_elementor/frontend-runtime.js');
        $this->assertFileExists($runtimePath);

        $runtime = file_get_contents($runtimePath);
        $editorShell = file_get_contents(resource_path('views/pagebuilder_elementor/editor_shell.blade.php'));
        $frontendShell = file_get_contents(resource_path('views/pagebuilder_elementor/frontend_renderer.blade.php'));

        $this->assertSourceContains('window.PageBuilderElementorRuntime', $runtime);
        $this->assertSourceContains("querySelectorAll('[data-accordion-root]')", $runtime);
        $this->assertSourceContains("case 'ArrowDown'", $runtime);
        $this->assertSourceContains("case 'Home'", $runtime);
        $this->assertSourceContains('prefers-reduced-motion: reduce', file_get_contents(public_path('assets/css/frontend_elementor.css')));
        $this->assertSourceContains('frontend-runtime.js', $editorShell);
        $this->assertSourceContains('frontend-runtime.js', $frontendShell);
    }

    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }
}
