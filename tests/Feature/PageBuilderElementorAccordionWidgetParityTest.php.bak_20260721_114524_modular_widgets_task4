<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorAccordionWidgetParityTest extends TestCase
{
    public function test_editor_registers_general_accordion_with_three_nested_items(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSourceContains("accordion:      '/js/pagebuilder_elementor/widgets/advanced/Accordion.vue'", $appJs);
        $this->assertSourceContains('function isAccordion(t)', $appJs);
        $this->assertSourceContains('function accordionWidgetDefaultItems()', $appJs);
        $this->assertSourceContains('accordionItems: accordionWidgetDefaultItems()', $appJs);
        $this->assertMatchesRegularExpression(
            "/general:\\s*\\[\\s*\\{ type:'tabs'.*?\\{ type:'accordion',\\s+label:'Accordion'/s",
            $appJs
        );
        $this->assertSourceContains('advanced: []', $appJs);
        $this->assertSourceContains('<div class="pb-section" v-if="toolbox.advanced.length">', $appJs);
    }

    public function test_sidebar_categories_and_color_fields_use_polished_global_spacing(): void
    {
        $builderCss = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        $this->assertSourceContains('.pb-panel.left .pb-tab-content > .pb-collapsible[open] > .pb-collapsible-body', $builderCss);
        $this->assertSourceContains('padding-bottom: 18px;', $builderCss);
        $this->assertSourceContains('.pb-panel.left .clr-field', $builderCss);
        $this->assertSourceContains('display: block;', $builderCss);
        $this->assertSourceContains('width: 100%;', $builderCss);
        $this->assertSourceContains('.pb-panel.left .clr-field button', $builderCss);
        $this->assertSourceContains('width: 48px;', $builderCss);
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
            '<TypographyControl',
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

    public function test_frontend_applies_advanced_responsive_values_for_each_device(): void
    {
        $html = view('pagebuilder_elementor.partials.render_node', ['node' => [
            'id' => 'responsive-accordion',
            'type' => 'accordion',
            'settings' => [
                'cssId' => 'responsive-accordion',
                'accordionItemGap' => '12pt',
                'headerFontSize' => '20px',
                'headerFontSizeTablet' => '18px',
                'headerFontSizeMobile' => '',
                'widthMode' => 'custom',
                'customWidth' => '640px',
                'widthModeTablet' => 'full',
                'widthModeMobile' => 'inline',
                'orderModeTablet' => 'custom',
                'orderTablet' => 7,
                'orderModeMobile' => 'start',
                'sizeModeTablet' => 'custom',
                'flexGrowTablet' => 2,
                'flexShrinkTablet' => .5,
                'sizeModeMobile' => 'grow',
                'position' => 'absolute',
                'horizontalOrientation' => 'right',
                'verticalOrientation' => 'top',
                'positionXTablet' => '12pt',
                'positionYTablet' => '13px',
                'positionXMobile' => '18px',
                'positionYMobile' => '19px',
                'sticky' => 'bottom',
                'stickyOffsetTablet' => '14px',
                'stickyOffsetMobile' => '20px',
                'transformOffsetXHoverTablet' => '21px',
                'transformOffsetYHoverTablet' => '22px',
                'transformOffsetXHoverMobile' => '23px',
                'transformOffsetYHoverMobile' => '24px',
                'advancedBorderRadiusHoverTablet' => '25px',
                'advancedBorderRadiusHoverMobile' => '26px',
                'maskEnabled' => true,
                'maskSizeTablet' => 'custom',
                'maskScaleTablet' => 125,
                'maskPositionTablet' => 'custom',
                'maskPositionXTablet' => '33%',
                'maskPositionYTablet' => '44%',
                'maskRepeatTablet' => 'round',
                'maskSizeMobile' => 'fill',
                'maskPositionMobile' => 'right bottom',
                'maskRepeatMobile' => 'space',
            ],
            'accordionItems' => [['id' => 'one', 'title' => 'One', 'children' => []]],
        ]])->render();

        $this->assertStringContainsString('--accordion-item-gap:12pt', $html);
        $this->assertStringContainsString('#responsive-accordion{', $html);

        $tabletRule = $this->cssMediaRule($html, 1024, 'responsive-accordion');
        foreach (['--accordion-header-font-size:18px', 'width:100%', 'order:7', 'flex:2 0.5 auto', 'right:12pt', 'top:13px', 'bottom:14px', 'mask-size:125%', 'mask-position:33% 44%', 'mask-repeat:round'] as $declaration) {
            $this->assertStringContainsString($declaration, $tabletRule);
        }

        $mobileRule = $this->cssMediaRule($html, 767, 'responsive-accordion');
        foreach (['--accordion-header-font-size:18px', 'width:fit-content', 'order:-9999', 'flex:1 1 0', 'right:18px', 'top:19px', 'bottom:20px', 'mask-size:cover', 'mask-position:right bottom', 'mask-repeat:space'] as $declaration) {
            $this->assertStringContainsString($declaration, $mobileRule);
        }

        $this->assertStringContainsString('@media (max-width:1024px){#responsive-accordion:hover{border-radius:25px;--pb-advanced-transform:', $html);
        $this->assertStringContainsString('translate(21px,22px)', $html);
        $this->assertStringContainsString('@media (max-width:767px){#responsive-accordion:hover{border-radius:26px;--pb-advanced-transform:', $html);
        $this->assertStringContainsString('translate(23px,24px)', $html);
    }

    public function test_accordion_uses_per_control_responsive_popovers_and_spaced_actions(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $css = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        $this->assertFalse(str_contains($appJs, "'accordion-content-'+device.value"));
        $this->assertFalse(str_contains($appJs, "'accordion-style-'+device.value"));
        foreach (['accordion-item-position', 'accordion-icon-position', 'accordion-item-gap', 'accordion-content-distance', 'accordion-border-radius', 'accordion-padding', 'accordion-icon-size', 'accordion-icon-spacing', 'accordion-content-radius', 'accordion-content-padding'] as $controlKey) {
            $this->assertSourceContains("openControlResponsiveMenu('{$controlKey}')", $appJs);
        }
        $this->assertSourceContains('.pb-accordion-add-btn', $css);
        $this->assertSourceContains('margin-top: 12px', $css);
        $this->assertSourceContains('.pb-icon-source-btn', $css);
        $this->assertSourceContains('display: inline-flex', $css);
        $this->assertSourceContains('.pb-panel.left .pb-accordion-settings .pb-four-sides-with-link', $css);
    }

    public function test_accordion_typography_uses_popover_grouped_fonts_and_responsive_dimensions(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $component = file_get_contents(public_path('js/pagebuilder_elementor/widgets/shared/TypographyControl.vue'));
        $accordion = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/Accordion.vue'));
        $renderer = file_get_contents(resource_path('views/pagebuilder_elementor/partials/render_accordion.blade.php'));
        $editorShell = file_get_contents(resource_path('views/pagebuilder_elementor/editor_shell.blade.php'));
        $frontendShell = file_get_contents(resource_path('views/pagebuilder_elementor/frontend_renderer.blade.php'));

        $this->assertSourceContains('<TypographyControl', $appJs);
        foreach (['pb-typography-trigger', 'pb-typography-popover', 'pb-font-family-menu', 'Custom Fonts', 'System', "prefix: { type: String, default: 'header' }", "settingKey('FontStyle')", 'Decoration', 'Word Spacing'] as $marker) {
            $this->assertSourceContains($marker, $component);
        }
        foreach (['typography-font-size', 'typography-line-height', 'typography-letter-spacing', 'typography-word-spacing'] as $controlKey) {
            $this->assertSourceContains($controlKey, $component);
        }
        $this->assertSourceContains('--accordion-header-word-spacing', $accordion);
        $this->assertSourceContains('--accordion-header-word-spacing', $renderer);
        $this->assertSourceContains('window.PB_ELEMENTOR_FONT_FAMILIES', $editorShell);
        $this->assertSourceContains("asset('storage/fonts/", $editorShell);
        $this->assertSourceContains("asset('storage/fonts/", $frontendShell);
    }

    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }

    private function cssMediaRule(string $html, int $breakpoint, string $id): string
    {
        $prefix = '@media (max-width:'.$breakpoint.'px){#'.$id.'{';
        $start = strpos($html, $prefix);
        $this->assertNotFalse($start, 'Missing responsive CSS rule: '.$prefix);
        $end = strpos($html, '}}', $start);
        $this->assertNotFalse($end, 'Unterminated responsive CSS rule: '.$prefix);

        return substr($html, $start, $end + 2 - $start);
    }
}
