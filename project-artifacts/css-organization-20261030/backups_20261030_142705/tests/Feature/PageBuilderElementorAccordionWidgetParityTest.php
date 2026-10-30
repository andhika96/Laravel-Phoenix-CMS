<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorAccordionWidgetParityTest extends TestCase
{
    public function test_editor_registers_general_accordion_with_three_nested_items(): void
    {
        $appJs = $this->editorSource();
        $definition = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/accordion/definition.js'));
        $module = config('pagebuilder_elementor_widgets.accordion');

        $this->assertSame('js/pagebuilder_elementor/widgets/advanced/accordion/Canvas.vue', $module['canvas']);
        $this->assertSame('js/pagebuilder_elementor/widgets/advanced/accordion/Settings.vue', $module['settings']);
        $this->assertSourceContains("type: 'accordion'", $definition);
        $this->assertSourceContains("label: 'Accordion'", $definition);
        $this->assertSourceContains('function isAccordion(t)', $appJs);
        $this->assertSourceContains('function accordionWidgetDefaultItems()', $appJs);
        $this->assertSourceContains('node.accordionItems = accordionWidgetDefaultItems()', $appJs);
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
        $appJs = $this->editorSource();

        $this->assertSourceContains("if (c.type === 'accordion')", $appJs);
        $this->assertSourceContains('item.children = norm(item.children || [])', $appJs);
        $this->assertSourceContains('if (n.accordionItems) for (const item of n.accordionItems)', $appJs);
        $this->assertSourceContains('(node.accordionItems || []).forEach', $appJs);
    }

    public function test_accordion_sidebar_and_canvas_use_separate_runtime_state(): void
    {
        $appJs = $this->editorSource();

        $this->assertSourceContains('const accordionRuntimeState = ref({});', $appJs);
        $this->assertSourceContains('function accordionRuntimeForNode(node)', $appJs);
        $this->assertSourceContains('editingItemId', $appJs);
        $this->assertSourceContains('expandedItemIds', $appJs);
        $this->assertSourceContains('function toggleAccordionItem(node, itemId)', $appJs);
        $this->assertFalse(str_contains($appJs, 'settings.editorExpandedItemIds'));
    }

    public function test_accordion_runtime_normalization_does_not_reassign_equal_arrays_during_render(): void
    {
        $appJs = $this->editorSource();

        $this->assertSourceContains('function sameStringArray(left, right)', $appJs);
        $this->assertSourceContains('if (!sameStringArray(runtime.expandedItemIds, normalizedExpandedItemIds))', $appJs);
        $this->assertSourceContains('if (!sameStringArray(runtime.transitioningItemIds, normalizedTransitioningItemIds))', $appJs);
    }

    public function test_editor_exposes_accordion_item_actions_and_interactions(): void
    {
        $appJs = $this->editorSource();

        $this->assertSourceContains('function addAccordionItem(node = selectedNode.value)', $appJs);
        $this->assertSourceContains('function duplicateAccordionItem(node = selectedNode.value', $appJs);
        $this->assertSourceContains('function removeAccordionItem(node = selectedNode.value', $appJs);
        $this->assertSourceContains('editor.accordionItemsForNode(node)', $appJs);
        $this->assertSourceContains('Add Item', $appJs);
        $this->assertSourceContains('Default State', $appJs);
        $this->assertSourceContains('Max Items Expanded', $appJs);
        $this->assertSourceContains('Animation Duration', $appJs);
    }

    public function test_accordion_settings_bind_editable_fields_through_a_valid_computed_member(): void
    {
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/accordion/Settings.vue'));

        $this->assertIsString($settings);
        $this->assertSourceContains('editingItem()', $settings);
        $this->assertSourceContains('v-model="editingItem.title"', $settings);
        $this->assertSourceContains('v-model="editingItem.cssId"', $settings);
        $this->assertFalse(str_contains($settings, 'v-model="editor.accordionEditingItem(node).'));
    }

    public function test_accordion_preview_component_renders_accessible_animated_headers(): void
    {
        $componentPath = public_path('js/pagebuilder_elementor/widgets/advanced/accordion/Canvas.vue');

        $this->assertFileExists($componentPath);

        $component = file_get_contents($componentPath);
        $this->assertSourceContains("name: 'AdvancedAccordion'", $component);
		$this->assertSourceContains(':aria-expanded="isVisuallyExpanded(item.id) ? \'true\' : \'false\'"', $component);
        $this->assertSourceContains("this.\$emit('toggle-item', item.id)", $component);
        $this->assertSourceContains('prefers-reduced-motion: reduce', $component);
    }

    public function test_accordion_preview_animates_from_measured_pixel_heights_before_using_auto(): void
    {
        $component = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/accordion/Canvas.vue'));

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
        $appJs = $this->editorSource();

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
        $appJs = $this->editorSource();

        foreach ([
            'Item Position',
			'<div class="pb-subsection-title">Icon</div>',
			'label="Expand"',
			'label="Collapse"',
            'Title HTML Tag',
            'FAQ Schema',
        ] as $label) {
            $this->assertSourceContains($label, $appJs);
        }

		$this->assertSourceContains('openAccordionIconLibrary(this.roleName', $appJs);
		$this->assertSourceContains('chooseAccordionSvg(this.roleName', $appJs);
		$this->assertSourceContains('base="itemPosition"', $appJs);
		$this->assertSourceContains('base="iconPosition"', $appJs);
    }

    public function test_style_tab_exposes_accordion_header_and_content_state_controls(): void
    {
        $appJs = $this->editorSource();
        $component = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/accordion/Canvas.vue'));

        foreach ([
			'Space between Items',
			'Distance from content',
            'Background Type',
            'Gradient Type',
            'Border Type',
            'Border Width',
            'Border Radius',
            '<component :is="editor.typographyControl"',
            'Text Shadow',
            'Text Stroke',
			'label="Size" base="headerIconSize"',
			'label="Spacing" base="headerIconSpacing"',
			"headerIconSpacing: '10px'",
			"this.node.settings[this.settingKey]||this.node.settings[this.base]",
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
        $appJs = $this->editorSource();

		$this->assertSourceContains('pb-accordion-dimension-control', $appJs);
		$this->assertSourceContains('pb-accordion-box-control', $appJs);
		$this->assertSourceContains('const ResponsiveDimensionControl=', $appJs);
		$this->assertSourceContains('const ResponsiveBoxControl=', $appJs);
		$this->assertSourceContains('this.node.settings[this.settingKey]=values.map', $appJs);
		$this->assertSourceContains('linked:true', $appJs);
        $this->assertSourceContains('type="number"', $appJs);
        $this->assertSourceContains('type="range"', $appJs);
    }

    public function test_accordion_uses_canonical_color_gradient_and_text_shadow_controls(): void
    {
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/accordion/Settings.vue'));

        $this->assertIsString($settings);
        $this->assertStringNotContainsString('<input class="pb-input pb-coloris-input"', $settings);
		$this->assertGreaterThanOrEqual(4, substr_count($settings, 'class="pb-color-row"'));
        $this->assertStringNotContainsString('class="pb-color-swatch"', $settings);
		$this->assertGreaterThanOrEqual(7, substr_count($settings, 'class="pb-input coloris pb-coloris-input"'));
		$this->assertStringContainsString(':is="editor.textShadowControl"', $settings);
		$this->assertStringContainsString('@update:modelValue="node.settings[stateKey(\'headerTextShadow\', titleState)] = $event"', $settings);
		$this->assertStringContainsString(':is="editor.textStrokeControl"', $settings);
        $this->assertStringNotContainsString('placeholder="0 1px 2px rgba(0,0,0,.15)"', $settings);
		$this->assertStringContainsString('const BackgroundControl=', $settings);
		$this->assertStringContainsString('prefix="accordion" :suffix="capitalize(styleState)"', $settings);
		$this->assertStringContainsString("return this.prefix+field+this.suffix", $settings);
		$this->assertStringNotContainsString('styleStatePrefix', $settings);
		$this->assertStringNotContainsString('accordionNormalBackgroundType', $settings);
    }

	public function test_accordion_settings_follow_the_approved_compact_elementor_control_geometry(): void
	{
		$settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/accordion/Settings.vue'));
		$css = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

		foreach ([
			'class="pb-accordion-item-header"',
			':aria-expanded="isItemExpanded(item)"',
			'v-if="isItemExpanded(item) && editingItem"',
			'handle=".pb-accordion-item-header"',
			'const ResponsiveChoice=',
			'const ResponsiveDimensionControl=',
			'const ResponsiveBoxControl=',
			'const IconSourceControl=',
			'const BoxShadowControl=',
			'prefix="accordionBoxShadow" :suffix="capitalize(styleState)"',
			':show-display-conditions="false"',
			':show-cache-settings="false"',
			':elementor-choices="true"',
		] as $marker) {
			$this->assertSourceContains($marker, $settings);
		}
		$this->assertStringNotContainsString('pb-accordion-item-drag', $settings);
		$this->assertStringContainsString("<details class=\"pb-collapsible\">\n\t\t\t\t<summary>Interactions</summary>", $settings);

		foreach ([
			'.pb-panel.left .pb-accordion-settings {',
			'font-size: 12px;',
			'.pb-panel.left .pb-accordion-settings .pb-accordion-item-header',
			'grid-template-columns: minmax(0, 1fr) 36px 36px;',
			'.pb-panel.left .pb-accordion-settings .pb-accordion-inline-control .pb-seg-btn',
			'width: 32px;',
			'.pb-panel.left .pb-accordion-settings .pb-accordion-color-control',
			'.pb-panel.left .pb-accordion-settings textarea.pb-input.pb-advanced-css',
			'height: 180px;',
		] as $marker) {
			$this->assertSourceContains($marker, $css);
		}

		$this->assertMatchesRegularExpression(
			'~\.pb-panel\.left \.pb-accordion-settings \.pb-widget-advanced-controls \.pb-advanced-choice-buttons button:hover,\s*\.pb-panel\.left \.pb-accordion-settings \.pb-widget-advanced-controls \.pb-advanced-choice-buttons button\.active\s*\{[^}]*background:\s*#eaf0ff;[^}]*color:\s*#3348b8;~s',
			$css
		);
	}

	public function test_accordion_box_shadow_and_responsive_four_side_borders_reach_canvas_and_frontend(): void
	{
		$app = $this->editorSource();
		$canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/accordion/Canvas.vue'));
		$renderer = file_get_contents(resource_path('views/pagebuilder_elementor/partials/render_accordion.blade.php'));
		$frontendCss = file_get_contents(public_path('assets/css/frontend_elementor.css'));

		foreach ([
			"['accordionBoxShadowEnabled' + suffix]: false",
			"['accordionBoxShadowColor' + suffix]: 'rgba(0,0,0,.2)'",
			"stateKey('accordionBorderWidth')",
			'base="contentBorderWidth"',
		] as $marker) {
			$this->assertSourceContains($marker, $app);
		}
		foreach (['--accordion-box-shadow-', "responsiveValue('accordionBorderWidth' + suffix", "responsiveValue('contentBorderWidth'", 'boxShadowValue('] as $marker) {
			$this->assertSourceContains($marker, $canvas);
		}
		foreach (['$boxShadow = function', '--accordion-box-shadow-', "responsiveValue('accordionBorderWidth' . \$stateSuffix", "responsiveValue('contentBorderWidth'", "responsiveValue('headerTextStrokeWidth' . \$stateSuffix"] as $marker) {
			$this->assertSourceContains($marker, $renderer);
		}
		$this->assertSourceContains('box-shadow: var(--accordion-box-shadow-normal, none);', $frontendCss);
		$this->assertSourceContains('box-shadow: var(--accordion-box-shadow-hover', $frontendCss);
		$this->assertSourceContains('box-shadow: var(--accordion-box-shadow-active', $frontendCss);
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
        $this->assertStringContainsString('<h4 class="el-widget-heading elementor-heading-title"', $html);
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
        $appJs = $this->editorSource();
        $css = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        $this->assertFalse(str_contains($appJs, "'accordion-content-'+device.value"));
        $this->assertFalse(str_contains($appJs, "'accordion-style-'+device.value"));
		foreach (['accordion-item-position', 'accordion-icon-position', 'accordion-item-gap', 'accordion-content-distance', 'accordion-border-radius', 'accordion-padding', 'accordion-icon-size', 'accordion-icon-spacing', 'accordion-content-radius', 'accordion-content-padding'] as $controlKey) {
			$this->assertSourceContains('control-id="'.$controlKey.'"', $appJs);
		}
        $this->assertSourceContains('.pb-accordion-add-btn', $css);
        $this->assertSourceContains('margin-top: 12px', $css);
        $this->assertSourceContains('.pb-icon-source-btn', $css);
        $this->assertSourceContains('display: inline-flex', $css);
        $this->assertSourceContains('.pb-panel.left .pb-accordion-settings .pb-four-sides-with-link', $css);
    }

    public function test_accordion_typography_uses_popover_grouped_fonts_and_responsive_dimensions(): void
    {
        $appJs = $this->editorSource();
        $component = file_get_contents(public_path('js/pagebuilder_elementor/widgets/shared/TypographyControl.vue'));
        $accordion = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/accordion/Canvas.vue'));
        $renderer = file_get_contents(resource_path('views/pagebuilder_elementor/partials/render_accordion.blade.php'));
        $editorShell = file_get_contents(resource_path('views/pagebuilder_elementor/editor_shell.blade.php'));
        $frontendShell = file_get_contents(resource_path('views/pagebuilder_elementor/frontend_renderer.blade.php'));

        $this->assertSourceContains('<component :is="editor.typographyControl"', $appJs);
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

    private function editorSource(): string
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/advanced/accordion/Settings.vue'));

        $this->assertIsString($appJs);
        $this->assertIsString($settings);

        return $appJs."\n".$settings;
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
