<?php

namespace Tests\Feature;

use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeValidator;
use Tests\TestCase;

class PageBuilderElementorV24AutomaticCompiledNativeValidationTest extends TestCase
{
    public function test_wrong_column_count_is_a_structural_apply_blocker(): void
    {
        $source = $this->snapshot('repeat(2, 1fr)', [
            $this->node('hero', 'section', null, ['copy', 'media'], ['display' => 'grid', 'gridTemplateColumns' => 'repeat(2, 1fr)'], ['width' => 900]),
            $this->node('copy', 'div', 'hero', [], [], ['x' => 0, 'width' => 434]),
            $this->node('media', 'div', 'hero', [], [], ['x' => 466, 'width' => 434]),
        ]);
        $target = $source;
        $report = (new AutomaticCompiledNativeValidator)->compare($source, $this->layoutBlueprint('hero', 'grid', 3, ['1fr', '1fr', '1fr']), $target);

        $this->assertContains('layout-columns-mismatch', array_column($report['structuralErrors'], 'code'));
        $this->assertFalse($report['canApply']);
    }

    public function test_track_ratio_geometry_and_image_sizing_are_reported(): void
    {
        $source = $this->snapshot('42% 58%', [
            $this->node('hero', 'section', null, ['copy', 'media'], ['display' => 'grid', 'gridTemplateColumns' => '42% 58%'], ['width' => 1000]),
            $this->node('copy', 'div', 'hero', [], [], ['x' => 0, 'width' => 420]),
            $this->node('media', 'img', 'hero', [], ['objectFit' => 'cover'], ['x' => 452, 'width' => 548, 'height' => 320]),
        ]);
        $target = $this->snapshot('50% 50%', [
            $this->node('hero', 'section', null, ['copy', 'media'], ['display' => 'grid', 'gridTemplateColumns' => '50% 50%'], ['width' => 1000]),
            $this->node('copy', 'div', 'hero', [], [], ['x' => 0, 'width' => 500]),
            $this->node('media', 'img', 'hero', [], ['objectFit' => 'contain'], ['x' => 532, 'width' => 468, 'height' => 250]),
        ]);
        $report = (new AutomaticCompiledNativeValidator)->compare($source, $this->layoutBlueprint('hero', 'grid', 2, ['50%', '50%']), $target);

        $this->assertNotEmpty($report['nodeDeltas']);
        $this->assertNotEmpty($report['unrepresentedCss']);
        $this->assertFalse($report['canApply']);
    }

    public function test_padding_margin_and_border_deltas_are_not_hidden_by_tolerance(): void
    {
        $source = $this->snapshot('1fr', [
            $this->node('box', 'section', null, ['content'], [
                'display' => 'block', 'paddingTop' => '32px', 'paddingRight' => '24px', 'paddingBottom' => '32px', 'paddingLeft' => '24px',
                'marginTop' => '16px', 'borderTopWidth' => '2px', 'borderRightWidth' => '2px', 'borderBottomWidth' => '2px', 'borderLeftWidth' => '2px',
            ]),
            $this->node('content', 'p', 'box', [], [], ['y' => 36, 'width' => 700, 'height' => 80]),
        ]);
        $target = $this->snapshot('1fr', [
            $this->node('box', 'section', null, ['content'], [
                'display' => 'block', 'paddingTop' => '0px', 'paddingRight' => '0px', 'paddingBottom' => '0px', 'paddingLeft' => '0px',
                'marginTop' => '0px', 'borderTopWidth' => '0px', 'borderRightWidth' => '0px', 'borderBottomWidth' => '0px', 'borderLeftWidth' => '0px',
            ]),
            $this->node('content', 'p', 'box', [], [], ['y' => 0, 'width' => 700, 'height' => 80]),
        ]);
        $report = (new AutomaticCompiledNativeValidator)->compare($source, $this->layoutBlueprint('box', 'stack', 1, ['1fr']), $target);

        $this->assertNotEmpty($report['boxModelDeltas']);
        $this->assertTrue((bool) array_filter($report['boxModelDeltas'], static fn (array $delta): bool => ($delta['property'] ?? '') === 'paddingTop' && ($delta['delta'] ?? 0) === -32.0));
        $this->assertFalse($report['canApply']);
    }

    public function test_responsive_mode_and_visibility_mismatch_is_reported(): void
    {
        $source = $this->snapshot('repeat(2, 1fr)', [
            $this->node('responsive', 'section', null, ['one', 'two'], ['display' => 'grid', 'gridTemplateColumns' => 'repeat(2, 1fr)']),
            $this->node('one', 'div', 'responsive'),
            $this->node('two', 'div', 'responsive'),
        ], ['mobile' => ['display' => 'block', 'gridTemplateColumns' => 'none']]);
        $target = $this->snapshot('repeat(2, 1fr)', [
            $this->node('responsive', 'section', null, ['one', 'two'], ['display' => 'grid', 'gridTemplateColumns' => 'repeat(2, 1fr)']),
            $this->node('one', 'div', 'responsive'),
            $this->node('two', 'div', 'responsive', [], ['display' => 'none']),
        ], ['mobile' => ['display' => 'grid', 'gridTemplateColumns' => 'repeat(2, 1fr)']]);
        $report = (new AutomaticCompiledNativeValidator)->compare($source, $this->layoutBlueprint('responsive', 'grid', 2, ['1fr', '1fr']), $target);

        $this->assertNotEmpty($report['responsiveMismatches']);
        $this->assertFalse($report['canApply']);
    }

    private function layoutBlueprint(string $id, string $mode, int $columns, array $tracks): array
    {
        return [
            'sections' => [[
                'id' => $id,
                'sourceId' => $id,
                'layoutByViewport' => ['desktop' => [
                    'mode' => $mode,
                    'columns' => $columns,
                    'tracks' => $tracks,
                    'evidence' => ['rule' => 'test'],
                ]],
            ]],
        ];
    }

    /** @param array<int,array<string,mixed>> $nodes @param array<string,array<string,string>> $responsiveStyles */
    private function snapshot(string $tracks, array $nodes, array $responsiveStyles = []): array
    {
        $viewports = [['name' => 'desktop', 'width' => 1180, 'height' => 900]];
        foreach ($responsiveStyles as $viewport => $style) {
            $viewports[] = ['name' => $viewport, 'width' => $viewport === 'mobile' ? 390 : 768, 'height' => 900];
        }
        foreach ($nodes as &$node) {
            $desktopStyle = $node['computedStyleByViewport']['desktop'];
            $node['computedStyleByViewport'] = ['desktop' => $desktopStyle];
            $node['rectByViewport'] = ['desktop' => $node['rectByViewport']['desktop']];
            foreach ($responsiveStyles as $viewport => $style) {
                $node['computedStyleByViewport'][$viewport] = array_merge($desktopStyle, $node['sourceId'] === 'responsive' ? $style : []);
                $node['rectByViewport'][$viewport] = $node['rectByViewport']['desktop'];
            }
        }
        unset($node);

        return ['version' => 1, 'viewports' => $viewports, 'nodes' => $nodes];
    }

    /** @param array<string,string> $style @param array<string,int|float> $rect */
    private function node(string $id, string $tag, ?string $parent = null, array $children = [], array $style = [], array $rect = []): array
    {
        $computedStyle = array_merge([
            'display' => 'block', 'boxSizing' => 'border-box', 'position' => 'static', 'visibility' => 'visible', 'opacity' => '1',
            'gridTemplateColumns' => 'none', 'gridTemplateRows' => 'none', 'flexDirection' => 'row', 'flexWrap' => 'nowrap',
            'paddingTop' => '0px', 'paddingRight' => '0px', 'paddingBottom' => '0px', 'paddingLeft' => '0px',
            'marginTop' => '0px', 'marginRight' => '0px', 'marginBottom' => '0px', 'marginLeft' => '0px',
            'borderTopWidth' => '0px', 'borderRightWidth' => '0px', 'borderBottomWidth' => '0px', 'borderLeftWidth' => '0px',
            'borderTopStyle' => 'none', 'borderRightStyle' => 'none', 'borderBottomStyle' => 'none', 'borderLeftStyle' => 'none',
            'borderTopColor' => '#000000', 'borderRightColor' => '#000000', 'borderBottomColor' => '#000000', 'borderLeftColor' => '#000000',
            'borderRadius' => '0px', 'backgroundImage' => 'none', 'boxShadow' => 'none', 'objectFit' => 'fill',
            'width' => '900px', 'height' => '300px', 'visibility' => 'visible', 'overflow' => 'visible',
        ], $style);
        $record = ['x' => (float) ($rect['x'] ?? 0), 'y' => (float) ($rect['y'] ?? 0), 'width' => (float) ($rect['width'] ?? 900), 'height' => (float) ($rect['height'] ?? 300)];

        return [
            'sourceId' => $id, 'tag' => $tag, 'id' => $id, 'classList' => [], 'parentSourceId' => $parent,
            'computedStyle' => $computedStyle, 'computedStyleByViewport' => ['desktop' => $computedStyle],
            'rectByViewport' => ['desktop' => $record], 'scrollSizeByViewport' => [], 'children' => $children,
        ];
    }
}
