<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeResponsiveClassifier;
use Tests\TestCase;

class PageBuilderElementorV24AutomaticCompiledNativeResponsiveClassifierTest extends TestCase
{
    public function test_two_to_one_to_one_transition_is_preserved_without_inventing_a_mobile_grid(): void
    {
        $classifier = new AutomaticCompiledNativeResponsiveClassifier;
        $result = $classifier->classify([
            'desktop' => $this->snapshot('desktop', 'repeat(2, 1fr)', 'grid'),
            'tablet' => $this->snapshot('tablet', '1fr', 'grid'),
            'mobile' => $this->snapshot('mobile', '1fr', 'block'),
        ]);

        $this->assertSame('grid', $result['viewports']['desktop']['mode']);
        $this->assertSame(2, $result['viewports']['desktop']['columns']);
        $this->assertSame(1, $result['viewports']['tablet']['columns']);
        $this->assertSame('stack', $result['viewports']['mobile']['mode']);
        $this->assertSame(['desktop:2→tablet:1', 'tablet:1→mobile:1'], $result['transitions']);
    }

    private function snapshot(string $viewport, string $tracks, string $display): array
    {
        return [
            'version' => 1,
            'viewports' => [[
                'name' => $viewport,
                'width' => $viewport === 'mobile' ? 390 : ($viewport === 'tablet' ? 768 : 1180),
                'height' => 900,
            ]],
            'nodes' => [[
                'sourceId' => 'root',
                'tag' => 'section',
                'id' => 'root',
                'classList' => [],
                'parentSourceId' => null,
                'computedStyle' => ['display' => $display, 'gridTemplateColumns' => $tracks],
                'computedStyleByViewport' => [$viewport => ['display' => $display, 'gridTemplateColumns' => $tracks]],
                'rectByViewport' => [$viewport => ['x' => 0, 'y' => 0, 'width' => 900, 'height' => 300]],
                'scrollSizeByViewport' => [],
                'children' => [],
            ]],
        ];
    }
}
