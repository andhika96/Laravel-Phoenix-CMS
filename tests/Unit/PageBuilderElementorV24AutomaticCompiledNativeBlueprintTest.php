<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeBlueprint;
use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeEvidence;
use InvalidArgumentException;
use Tests\TestCase;

class PageBuilderElementorV24AutomaticCompiledNativeBlueprintTest extends TestCase
{
    public function test_evidence_normalizes_measured_style_geometry_and_source_identity(): void
    {
        $evidence = AutomaticCompiledNativeEvidence::fromSnapshot([
            'version' => 1,
            'viewports' => [
                ['name' => 'desktop', 'width' => 1180, 'height' => 900],
                ['name' => 'mobile', 'width' => 390, 'height' => 900],
            ],
            'nodes' => [
                [
                    'sourceId' => 'hero',
                    'tag' => 'section',
                    'id' => 'hero-id',
                    'classList' => ['hero', 'grid'],
                    'parentSourceId' => null,
                    'computedStyle' => [
                        'display' => 'grid',
                        'gridTemplateColumns' => 'repeat(2, minmax(0, 1fr))',
                        'paddingTop' => '32px',
                        'paddingRight' => '40px',
                        'paddingBottom' => '48px',
                        'paddingLeft' => '40px',
                        'marginTop' => '0px',
                        'marginRight' => 'auto',
                        'marginBottom' => '24px',
                        'marginLeft' => 'auto',
                        'borderTopWidth' => '1px',
                        'borderRightWidth' => '1px',
                        'borderBottomWidth' => '1px',
                        'borderLeftWidth' => '1px',
                        'boxSizing' => 'border-box',
                    ],
                    'computedStyleByViewport' => [
                        'desktop' => ['display' => 'grid', 'gridTemplateColumns' => '1fr 1fr'],
                        'mobile' => ['display' => 'block', 'gridTemplateColumns' => 'none'],
                    ],
                    'rectByViewport' => [
                        'desktop' => ['x' => 0, 'y' => 120, 'width' => 1180, 'height' => 620],
                    ],
                    'scrollSizeByViewport' => [
                        'desktop' => ['width' => 1180, 'height' => 620],
                    ],
                    'children' => ['copy', 'media'],
                ],
            ],
        ]);

        $this->assertSame(1, $evidence['version']);
        $this->assertSame('hero', $evidence['nodes'][0]['sourceId']);
        $this->assertSame(['hero', 'grid'], $evidence['nodes'][0]['classList']);
        $this->assertSame('border-box', $evidence['nodes'][0]['computedStyle']['boxSizing']);
        $this->assertSame('block', $evidence['nodes'][0]['computedStyleByViewport']['mobile']['display']);
        $this->assertSame(1180.0, $evidence['nodes'][0]['rectByViewport']['desktop']['width']);
        $this->assertSame(620.0, $evidence['nodes'][0]['scrollSizeByViewport']['desktop']['height']);
    }

    public function test_evidence_rejects_duplicate_or_incomplete_node_records(): void
    {
        $this->expectException(InvalidArgumentException::class);

        AutomaticCompiledNativeEvidence::fromSnapshot([
            'version' => 1,
            'viewports' => [['name' => 'desktop', 'width' => 1180, 'height' => 900]],
            'nodes' => [
                ['sourceId' => 'duplicate', 'computedStyle' => [], 'rectByViewport' => []],
                ['sourceId' => 'duplicate', 'computedStyle' => [], 'rectByViewport' => []],
            ],
        ]);
    }

    public function test_blueprint_normalizes_viewports_sections_and_nodes_into_stable_order(): void
    {
        $blueprint = AutomaticCompiledNativeBlueprint::normalize([
            'version' => 1,
            'viewports' => [
                ['name' => 'mobile', 'width' => 390, 'height' => 900],
                ['name' => 'desktop', 'width' => 1180, 'height' => 900],
                ['name' => 'tablet', 'width' => 768, 'height' => 1024],
            ],
            'sections' => [
                [
                    'id' => 'section-2',
                    'order' => 2,
                    'kind' => 'section',
                    'sourceSelector' => 'main > section:nth-of-type(2)',
                    'nodes' => [
                        ['sourceId' => 'node-2', 'order' => 2],
                        ['sourceId' => 'node-1', 'order' => 1],
                    ],
                ],
                [
                    'id' => 'section-1',
                    'order' => 1,
                    'kind' => 'section',
                    'sourceSelector' => 'main > section:nth-of-type(1)',
                    'nodes' => [],
                ],
            ],
        ]);

        $this->assertSame(['desktop', 'tablet', 'mobile'], array_column($blueprint['viewports'], 'name'));
        $this->assertSame(['section-1', 'section-2'], array_column($blueprint['sections'], 'id'));
        $this->assertSame(['node-1', 'node-2'], array_column($blueprint['sections'][1]['nodes'], 'sourceId'));
        $this->assertSame(0.0, $blueprint['sections'][0]['boundaryConfidence']);
        $this->assertSame([], $blueprint['sections'][0]['diagnostics']);
    }

    public function test_blueprint_requires_evidence_for_every_automatic_column_count(): void
    {
        $errors = AutomaticCompiledNativeBlueprint::validate([
            'version' => 1,
            'viewports' => [['name' => 'desktop', 'width' => 1180, 'height' => 900]],
            'sections' => [[
                'id' => 'hero',
                'kind' => 'section',
                'sourceSelector' => 'main > section:first-child',
                'layoutByViewport' => [
                    'desktop' => [
                        'mode' => 'grid',
                        'columns' => 3,
                        'tracks' => ['1fr', '1fr', '1fr'],
                    ],
                ],
                'nodes' => [],
            ]],
        ]);

        $this->assertFalse($errors['valid']);
        $this->assertContains('layout-evidence-missing', array_column($errors['errors'], 'code'));
    }

    public function test_blueprint_accepts_dynamic_tracks_only_when_their_evidence_is_recorded(): void
    {
        $result = AutomaticCompiledNativeBlueprint::validate([
            'version' => 1,
            'viewports' => [['name' => 'desktop', 'width' => 1180, 'height' => 900]],
            'sections' => [[
                'id' => 'hero',
                'kind' => 'section',
                'sourceSelector' => 'main > section:first-child',
                'layoutByViewport' => [
                    'desktop' => [
                        'mode' => 'grid',
                        'columns' => 2,
                        'tracks' => ['0.9fr', '1.1fr'],
                        'evidence' => [
                            'computedStyle' => ['gridTemplateColumns' => '0.9fr 1.1fr'],
                            'rule' => 'computedStyle.gridTemplateColumns',
                        ],
                    ],
                ],
                'nodes' => [],
            ]],
        ]);

        $this->assertTrue($result['valid']);
        $this->assertSame([], $result['errors']);
    }
}
