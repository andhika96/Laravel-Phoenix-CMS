<?php

namespace Tests\Feature;

use App\Support\PageBuilderElementor\WidgetDisplayConditionEvaluator;
use App\Support\PageBuilderElementor\WidgetFragmentCache;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PageBuilderElementorWidgetAdvancedParityTest extends TestCase
{
    public function test_shared_advanced_model_is_normalized_for_accordion_widgets(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertSourceContains('function widgetAdvancedDefaults()', $appJs);
        $this->assertSourceContains('function normalizeWidgetAdvancedSettings(settings)', $appJs);
        $this->assertSourceContains('...widgetAdvancedDefaults()', $appJs);
        $this->assertSourceContains("normalizeWidgetAdvancedSettings(c.settings)", $appJs);
        $this->assertSourceContains("'/js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue'", $appJs);
        $this->assertSourceContains('<WidgetAdvancedControls', $appJs);
    }

    public function test_shared_advanced_controls_cover_every_approved_section(): void
    {
        $path = public_path('js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue');
        $this->assertFileExists($path);

        $component = file_get_contents($path);
        foreach ([
            'Layout',
            'Display Conditions',
            'Cache Settings',
            'Motion Effects',
            'Animate With AI',
            'Transform',
            'Background',
            'Border',
            'Mask',
            'Responsive',
            'Attributes',
            'Custom CSS',
        ] as $section) {
            $this->assertSourceContains($section, $component);
        }

        foreach ([
            'Scrolling Effects',
            'Vertical Scroll',
            'Horizontal Scroll',
            'Transparency',
            'Blur',
            'Rotate',
            'Scale',
            'Mouse Track',
            '3D Tilt',
            'Sticky',
            'Entrance Animation',
            'Hover Transition Duration',
            'Shape',
            'Hide On Desktop',
        ] as $control) {
            $this->assertSourceContains($control, $component);
        }

        $this->assertSourceContains('AI service is not connected', $component);
        $this->assertSourceContains("this.\$emit('unavailable-ai')", $component);
    }

    public function test_shared_advanced_dimension_controls_are_numeric_unit_aware_and_two_state_tabs_fill_the_row(): void
    {
        $component = file_get_contents(public_path('js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue'));
        $css = file_get_contents(public_path('assets/css/pagebuilder_elementor.css'));

        $this->assertSourceContains('pb-state-tabs pb-state-tabs--two', $component);
        $this->assertSourceContains('<DimensionControl', $component);
        $this->assertSourceContains('<EdgeControl', $component);
        $this->assertSourceContains('<BoxControl', $component);
        $this->assertSourceContains('type="range"', $component);
        $this->assertSourceContains('type="number"', $component);
        $this->assertSourceContains('.pb-state-tabs--two', $css);
        $this->assertSourceContains('grid-template-columns: repeat(2, minmax(0, 1fr))', $css);
    }

    public function test_advanced_settings_render_safe_styles_attributes_and_scoped_css(): void
    {
        $html = view('pagebuilder_elementor.partials.render_node', ['node' => [
            'id' => 'advanced-accordion',
            'type' => 'accordion',
            'settings' => [
                'cssId' => 'faq-widget',
                'cssClass' => 'custom-faq another-class',
                'marginTop' => '12px',
                'paddingLeft' => '18px',
                'widthMode' => 'custom',
                'customWidth' => '640px',
                'position' => 'absolute',
                'horizontalOrientation' => 'right',
                'positionX' => '20px',
                'zIndex' => 9,
                'advancedBackgroundType' => 'classic',
                'advancedBackgroundColor' => '#112233',
                'advancedBorderType' => 'solid',
                'advancedBorderWidth' => '2px',
                'advancedBorderColor' => '#445566',
                'advancedBorderRadius' => '14px',
                'maskEnabled' => true,
                'maskShape' => 'circle',
                'transformRotate' => '4deg',
                'transformScale' => 1.05,
                'hideMobile' => true,
                'entranceAnimation' => 'fadeInUp',
                'scrollingEffects' => true,
                'verticalScrollEnabled' => true,
                'attributes' => [
                    ['name' => 'data-track', 'value' => 'faq'],
                    ['name' => 'aria-label', 'value' => 'Questions'],
                    ['name' => 'onclick', 'value' => 'alert(1)'],
                    ['name' => 'style', 'value' => 'display:none'],
                    ['name' => 'id', 'value' => 'override'],
                    ['name' => 'href', 'value' => 'javascript:alert(1)'],
                ],
                'customCssCode' => 'selector { outline: 2px solid red; }',
            ],
            'accordionItems' => [[
                'id' => 'item-one',
                'title' => 'Question',
                'children' => [],
            ]],
        ]])->render();

        $this->assertStringContainsString('id="faq-widget"', $html);
        $this->assertStringContainsString('custom-faq another-class', $html);
        $this->assertStringContainsString('margin-top:12px', $html);
        $this->assertStringContainsString('width:640px', $html);
        $this->assertStringContainsString('right:20px', $html);
        $this->assertStringContainsString('background-color:#112233', $html);
        $this->assertStringContainsString('border-style:solid', $html);
        $this->assertStringContainsString('-webkit-mask-image:', $html);
        $this->assertStringContainsString('data-pb-motion=', $html);
        $this->assertStringContainsString('data-track="faq"', $html);
        $this->assertStringContainsString('aria-label="Questions"', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringNotContainsString('javascript:alert', $html);
        $this->assertStringNotContainsString('id="override"', $html);
        $this->assertStringContainsString('#faq-widget { outline: 2px solid red; }', $html);
    }

    public function test_editor_and_shared_runtime_apply_advanced_visual_effects(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $runtime = file_get_contents(public_path('js/pagebuilder_elementor/frontend-runtime.js'));

        $this->assertSourceContains('function widgetAdvancedPreviewStyle(settings, device)', $appJs);
        $this->assertSourceContains('widgetAdvancedPreviewStyle(s, device)', $appJs);
        $this->assertSourceContains("window.addEventListener('scroll', scheduleMotion", $runtime);
        $this->assertSame(1, substr_count($runtime, "window.addEventListener('scroll', scheduleMotion"));
        $this->assertSourceContains('new IntersectionObserver', $runtime);
        $this->assertSourceContains("requestAnimationFrame(updateMotion)", $runtime);
        $this->assertSourceContains("window.addEventListener('pointermove'", $runtime);
        $this->assertSourceContains('prefersReducedMotion()', $runtime);
    }

    public function test_display_conditions_use_and_inside_groups_and_or_between_groups(): void
    {
        $evaluator = app(WidgetDisplayConditionEvaluator::class);
        $request = Request::create('/docs/getting-started', 'GET');
        $request->attributes->set('pagebuilder_page_id', 42);
        $request->attributes->set('pagebuilder_page_slug', 'getting-started');

        $groups = [[
            'rules' => [
                ['effect' => 'include', 'source' => 'page-id', 'operator' => 'is', 'value' => '42'],
                ['effect' => 'include', 'source' => 'auth-state', 'operator' => 'is', 'value' => 'guest'],
            ],
        ]];

        $this->assertTrue($evaluator->allows($groups, $request, null));
        $groups[0]['rules'][0]['value'] = '99';
        $this->assertFalse($evaluator->allows($groups, $request, null));
        $groups[] = ['rules' => [[
            'effect' => 'include', 'source' => 'page-slug', 'operator' => 'is', 'value' => 'getting-started',
        ]]];
        $this->assertTrue($evaluator->allows($groups, $request, null));
        $this->assertTrue($evaluator->allows([], $request, null));
        $this->assertFalse($evaluator->allows([['rules' => [['source' => 'unknown', 'value' => 'x']]]], $request, null));
    }

    public function test_display_conditions_cover_roles_dates_devices_and_exclusions(): void
    {
        $evaluator = app(WidgetDisplayConditionEvaluator::class);
        $request = Request::create('/account', 'GET', [], [], [], ['HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)']);
        $request->attributes->set('pagebuilder_now', '2026-07-19 12:00:00');
        $user = new class implements Authenticatable {
            public function getAuthIdentifierName() { return 'id'; }
            public function getAuthIdentifier() { return 7; }
            public function getAuthPasswordName() { return 'password'; }
            public function getAuthPassword() { return ''; }
            public function getRememberToken() { return null; }
            public function setRememberToken($value) {}
            public function getRememberTokenName() { return ''; }
            public function getRoleNames() { return collect(['editor']); }
        };

        $this->assertTrue($evaluator->allows([['rules' => [
            ['effect' => 'include', 'source' => 'user-role', 'operator' => 'is', 'value' => 'editor'],
            ['effect' => 'include', 'source' => 'date-range', 'operator' => 'is', 'value' => '2026-07-19 00:00:00|2026-07-20 00:00:00'],
            ['effect' => 'include', 'source' => 'device', 'operator' => 'is', 'value' => 'mobile'],
            ['effect' => 'exclude', 'source' => 'page-slug', 'operator' => 'is', 'value' => 'admin'],
        ]]], $request, $user));
    }

    public function test_fragment_cache_bypasses_inactive_reuses_active_and_hashes_context(): void
    {
        Cache::clear();
        $cache = app(WidgetFragmentCache::class);
        $counter = 0;
        $inactive = ['id' => 'one', 'type' => 'accordion', 'settings' => ['cacheMode' => 'inactive']];
        $active = ['id' => 'one', 'type' => 'accordion', 'settings' => ['cacheMode' => 'active']];
        $context = ['page_id' => 42, 'page_slug' => 'docs', 'auth' => 'guest', 'roles' => []];

        $cache->remember($inactive, $context, function () use (&$counter) { return 'inactive-' . ++$counter; });
        $cache->remember($inactive, $context, function () use (&$counter) { return 'inactive-' . ++$counter; });
        $this->assertSame(2, $counter);

        $first = $cache->remember($active, $context, function () use (&$counter) { return 'active-' . ++$counter; });
        $second = $cache->remember($active, $context, function () use (&$counter) { return 'active-' . ++$counter; });
        $this->assertSame($first, $second);
        $this->assertSame(3, $counter);
        $this->assertNotSame($cache->key($active, $context), $cache->key($active, [...$context, 'page_id' => 43]));
        $this->assertNotSame($cache->key($active, $context), $cache->key([...$active, 'label' => 'changed'], $context));
    }

    public function test_renderer_integrates_conditions_before_fragment_cache(): void
    {
        $blade = file_get_contents(resource_path('views/pagebuilder_elementor/partials/render_node.blade.php'));

        $this->assertSourceContains('WidgetDisplayConditionEvaluator::class', $blade);
        $this->assertSourceContains('WidgetFragmentCache::class', $blade);
        $this->assertSourceContains("\$__pbConditionEvaluator->allows", $blade);
        $this->assertSourceContains("\$__pbFragmentCache->remember", $blade);
    }

    private function assertSourceContains(string $needle, string $source): void
    {
        $this->assertTrue(str_contains($source, $needle), 'Missing source marker: '.$needle);
    }
}
