<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeBlockNormalizer;
use Tests\TestCase;

class PageBuilderElementorV24AutomaticCompiledNativeBlockNormalizerTest extends TestCase
{
    public function test_nested_layouts_remain_mapping_roots_and_transparent_wrappers_are_promoted(): void
    {
        $result = (new AutomaticCompiledNativeBlockNormalizer)->normalizeSection($this->section([
            $this->node('hero', 'section', null, ['grid']),
            $this->node('grid', 'div', 'hero', ['left', 'transparent'], ['display' => 'grid', 'gridTemplateColumns' => '1fr 1fr']),
            $this->node('left', 'div', 'grid', ['heading'], ['paddingLeft' => '8px']),
            $this->node('transparent', 'div', 'grid', ['copy']),
            $this->node('heading', 'h1', 'left', [], [], 'Hero title'),
            $this->node('copy', 'p', 'transparent', [], [], 'Hero copy'),
        ]));

        $byId = $this->index($result['mappingNodes']);

        $this->assertSame(['grid'], $result['mappingRoots']);
        $this->assertSame('layout', $byId['grid']['mappingRole']);
        $this->assertSame(['left', 'copy'], $byId['grid']['childMappingIds']);
        $this->assertSame('container', $byId['left']['mappingRole']);
        $this->assertSame('content', $byId['heading']['mappingRole']);
        $this->assertArrayNotHasKey('transparent', $byId);
        $this->assertSame(['copy'], $result['rawNodesById']['transparent']['rawChildSourceIds']);
        $this->assertSame('grid', $byId['copy']['parentMappingId']);
    }

    public function test_semantic_content_owns_inline_descendants_instead_of_duplicate_mapping_rows(): void
    {
        $result = (new AutomaticCompiledNativeBlockNormalizer)->normalizeSection($this->section([
            $this->node('hero', 'section', null, ['title', 'action']),
            $this->node('title', 'h1', 'hero', ['break', 'accent'], [], 'CEO Masters Indonesia 2026', '<br><span>Indonesia 2026</span>'),
            $this->node('break', 'br', 'title'),
            $this->node('accent', 'span', 'title', [], [], 'Indonesia 2026'),
            $this->node('action', 'a', 'hero', ['icon'], ['display' => 'inline-flex'], 'Register', '<i>arrow</i>'),
            $this->node('icon', 'i', 'action'),
        ]));

        $byId = $this->index($result['mappingNodes']);

        $this->assertSame(['title', 'action'], $result['mappingRoots']);
        $this->assertSame('content', $byId['title']['mappingRole']);
        $this->assertSame(['break', 'accent'], $byId['title']['memberSourceIds']);
        $this->assertSame('content', $byId['action']['mappingRole']);
        $this->assertSame(['icon'], $byId['action']['memberSourceIds']);
        $this->assertArrayNotHasKey('break', $byId);
        $this->assertArrayNotHasKey('accent', $byId);
        $this->assertArrayNotHasKey('icon', $byId);
    }

    public function test_icon_label_value_wrapper_is_one_composite_mapping_unit(): void
    {
        $result = (new AutomaticCompiledNativeBlockNormalizer)->normalizeSection($this->section([
            $this->node('meta', 'section', null, ['date']),
            $this->node('date', 'div', 'meta', ['label', 'value']),
            $this->node('label', 'dt', 'date', ['calendar'], ['display' => 'flex']),
            $this->node('calendar', 'i', 'label'),
            $this->node('value', 'dd', 'date', [], [], '10 October 2026'),
        ]));

        $byId = $this->index($result['mappingNodes']);

        $this->assertSame(['date'], $result['mappingRoots']);
        $this->assertSame('composite', $byId['date']['mappingRole']);
        $this->assertSame('icon_box', $byId['date']['mappingKind']);
        $this->assertSame(['label', 'calendar', 'value'], $byId['date']['memberSourceIds']);
        $this->assertSame([], $byId['date']['childMappingIds']);
        $this->assertSame([], $byId['date']['candidateWidgets']);
    }

    public function test_media_wrapper_preserves_nested_overlay_as_members_without_flattening_raw_tree(): void
    {
        $result = (new AutomaticCompiledNativeBlockNormalizer)->normalizeSection($this->section([
            $this->node('media-section', 'section', null, ['figure']),
            $this->node('figure', 'figure', 'media-section', ['image', 'overlay']),
            $this->node('image', 'img', 'figure'),
            $this->node('overlay', 'div', 'figure', ['date'], ['position' => 'absolute']),
            $this->node('date', 'span', 'overlay', [], [], 'Saturday'),
        ]));

        $byId = $this->index($result['mappingNodes']);

        $this->assertSame(['figure'], $result['mappingRoots']);
        $this->assertSame('composite', $byId['figure']['mappingRole']);
        $this->assertSame('image_box', $byId['figure']['mappingKind']);
        $this->assertSame(['image', 'overlay', 'date'], $byId['figure']['memberSourceIds']);
        $this->assertSame(['image', 'overlay'], $result['rawNodesById']['figure']['rawChildSourceIds']);
    }

    public function test_flex_action_wrapper_with_icons_remains_a_layout_group(): void
    {
        $result = (new AutomaticCompiledNativeBlockNormalizer)->normalizeSection($this->section([
            $this->node('actions-section', 'section', null, ['actions']),
            $this->node('actions', 'div', 'actions-section', ['primary', 'secondary'], ['display' => 'flex', 'gap' => '12px'], 'Register Sponsorship'),
            $this->node('primary', 'a', 'actions', ['primary-icon'], [], 'Register'),
            $this->node('primary-icon', 'i', 'primary'),
            $this->node('secondary', 'a', 'actions', ['secondary-icon'], [], 'Sponsorship'),
            $this->node('secondary-icon', 'i', 'secondary'),
        ]));

        $byId = $this->index($result['mappingNodes']);

        $this->assertSame('layout', $byId['actions']['mappingRole']);
        $this->assertSame('layout', $byId['actions']['mappingKind']);
        $this->assertSame(['primary', 'secondary'], $byId['actions']['childMappingIds']);
        $this->assertArrayNotHasKey('primary-icon', $byId);
        $this->assertArrayNotHasKey('secondary-icon', $byId);
    }

    public function test_grid_wrapper_with_media_and_text_remains_a_layout_group(): void
    {
        $result = (new AutomaticCompiledNativeBlockNormalizer)->normalizeSection($this->section([
            $this->node('hero-section', 'section', null, ['hero-layout']),
            $this->node('hero-layout', 'div', 'hero-section', ['copy', 'media'], ['display' => 'grid', 'gridTemplateColumns' => '1fr 1fr'], 'Hero text'),
            $this->node('copy', 'div', 'hero-layout', ['title'], [], 'Hero text'),
            $this->node('title', 'h1', 'copy', [], [], 'Hero title'),
            $this->node('media', 'figure', 'hero-layout', ['image'], [], 'Hero image'),
            $this->node('image', 'img', 'media'),
        ]));

        $byId = $this->index($result['mappingNodes']);

        $this->assertSame('layout', $byId['hero-layout']['mappingRole']);
        $this->assertSame([], $byId['hero-layout']['candidateWidgets']);
        $this->assertSame(['title', 'media'], $byId['hero-layout']['childMappingIds']);
    }

    public function test_large_wrapper_with_nested_icon_action_does_not_collapse_into_icon_box(): void
    {
        $result = (new AutomaticCompiledNativeBlockNormalizer)->normalizeSection($this->section([
            $this->node('section-root', 'section', null, ['wrapper']),
            $this->node('wrapper', 'div', 'section-root', ['actions', 'copy'], [], 'Register copy'),
            $this->node('actions', 'div', 'wrapper', ['action'], ['display' => 'flex'], 'Register'),
            $this->node('action', 'a', 'actions', ['icon'], [], 'Register'),
            $this->node('icon', 'i', 'action'),
            $this->node('copy', 'p', 'wrapper', [], [], 'Description'),
        ]));

        $byId = $this->index($result['mappingNodes']);

        $this->assertSame('container', $byId['wrapper']['mappingRole']);
        $this->assertSame(['actions', 'copy'], $byId['wrapper']['childMappingIds']);
        $this->assertArrayNotHasKey('icon', $byId);
    }

    public function test_large_wrapper_with_nested_media_does_not_collapse_into_image_box(): void
    {
        $result = (new AutomaticCompiledNativeBlockNormalizer)->normalizeSection($this->section([
            $this->node('section-root', 'section', null, ['wrapper']),
            $this->node('wrapper', 'div', 'section-root', ['media', 'copy'], [], 'Image description'),
            $this->node('media', 'div', 'wrapper', ['image']),
            $this->node('image', 'img', 'media'),
            $this->node('copy', 'p', 'wrapper', [], [], 'Image description'),
        ]));

        $byId = $this->index($result['mappingNodes']);

        $this->assertSame('container', $byId['wrapper']['mappingRole']);
        $this->assertSame(['image', 'copy'], $byId['wrapper']['childMappingIds']);
        $this->assertSame('content', $byId['image']['mappingRole']);
    }

    public function test_inline_text_directly_inside_layout_remains_a_content_mapping(): void
    {
        $result = (new AutomaticCompiledNativeBlockNormalizer)->normalizeSection($this->section([
            $this->node('section-root', 'section', null, ['grid']),
            $this->node('grid', 'div', 'section-root', ['label', 'copy'], ['display' => 'grid', 'gridTemplateColumns' => '1fr 1fr']),
            $this->node('label', 'span', 'grid', [], [], 'Label'),
            $this->node('copy', 'p', 'grid', [], [], 'Copy'),
        ]));

        $byId = $this->index($result['mappingNodes']);

        $this->assertSame('content', $byId['label']['mappingRole']);
        $this->assertSame(['label', 'copy'], $byId['grid']['childMappingIds']);
    }

    public function test_text_only_div_remains_a_content_mapping_instead_of_being_dropped(): void
    {
        $result = (new AutomaticCompiledNativeBlockNormalizer)->normalizeSection($this->section([
            $this->node('section-root', 'section', null, ['copy']),
            $this->node('copy', 'div', 'section-root', [], [], 'Plain text inside a div'),
        ]));

        $byId = $this->index($result['mappingNodes']);

        $this->assertSame(['copy'], $result['mappingRoots']);
        $this->assertSame('content', $byId['copy']['mappingRole']);
        $this->assertSame('Plain text inside a div', $byId['copy']['textSummary']);
    }

    /** @param array<int,array<string,mixed>> $nodes */
    private function section(array $nodes): array
    {
        return [
            'id' => 'hero',
            'sourceId' => 'hero',
            'kind' => 'section',
            'nodes' => $nodes,
        ];
    }

    /** @param array<int,string> $children @param array<string,string> $style */
    private function node(string $id, string $tag, ?string $parent, array $children = [], array $style = [], string $text = '', string $innerHTML = ''): array
    {
        return [
            'sourceId' => $id,
            'tag' => $tag,
            'id' => $id,
            'classList' => [],
            'parentSourceId' => $parent,
            'textContent' => $text,
            'innerHTML' => $innerHTML,
            'attributes' => [],
            'rectByViewport' => ['desktop' => ['x' => 0, 'y' => 0, 'width' => 600, 'height' => 100]],
            'computedStyleByViewport' => ['desktop' => array_merge([
                'display' => 'block',
                'boxSizing' => 'border-box',
                'position' => 'static',
                'visibility' => 'visible',
                'opacity' => '1',
                'width' => '600px',
                'height' => '100px',
                'paddingTop' => '0px', 'paddingRight' => '0px', 'paddingBottom' => '0px', 'paddingLeft' => '0px',
                'marginTop' => '0px', 'marginRight' => '0px', 'marginBottom' => '0px', 'marginLeft' => '0px',
                'borderTopWidth' => '0px', 'borderRightWidth' => '0px', 'borderBottomWidth' => '0px', 'borderLeftWidth' => '0px',
                'borderTopStyle' => 'none', 'borderRightStyle' => 'none', 'borderBottomStyle' => 'none', 'borderLeftStyle' => 'none',
                'borderRadius' => '0px', 'backgroundColor' => 'rgba(0, 0, 0, 0)', 'backgroundImage' => 'none',
                'overflow' => 'visible', 'gap' => '0px', 'gridTemplateColumns' => 'none',
            ], $style)],
            'children' => $children,
        ];
    }

    /** @param array<int,array<string,mixed>> $nodes @return array<string,array<string,mixed>> */
    private function index(array $nodes): array
    {
        $indexed = [];
        foreach ($nodes as $node) {
            $indexed[(string) $node['sourceId']] = $node;
        }

        return $indexed;
    }
}
