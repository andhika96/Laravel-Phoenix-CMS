<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeSectionDetector;
use Tests\TestCase;

class PageBuilderElementorV24AutomaticCompiledNativeSectionDetectorTest extends TestCase
{
    public function test_semantic_header_sections_and_footer_are_indexed_without_layout_decisions(): void
    {
        $index = (new AutomaticCompiledNativeSectionDetector)->detect($this->snapshot([
            $this->node('body', 'body', null, ['header', 'main', 'footer']),
            $this->node('header', 'header', 'body'),
            $this->node('main', 'main', 'body', ['section-hero', 'section-cards']),
            $this->node('section-hero', 'section', 'main', ['hero-copy', 'hero-media'], ['backgroundColor' => '#07192d']),
            $this->node('hero-copy', 'div', 'section-hero'),
            $this->node('hero-media', 'div', 'section-hero'),
            $this->node('section-inner', 'section', 'section-hero'),
            $this->node('section-cards', 'section', 'main', ['card-1'], ['backgroundColor' => '#f5f1e8']),
            $this->node('card-1', 'article', 'section-cards'),
            $this->node('footer', 'footer', 'body'),
        ]));

        $sections = $index->sections;
        $this->assertSame(['header', 'section-hero', 'section-cards', 'footer'], array_column($sections, 'id'));
        $this->assertSame(['header', 'section', 'section', 'footer'], array_column($sections, 'kind'));
        $this->assertFalse($sections[0]['compile']);
        $this->assertTrue($sections[1]['compile']);
        $this->assertFalse($sections[3]['compile']);
        $this->assertContains('semantic-tag:section', $sections[1]['boundaryEvidence']);
        $this->assertContains('hero-copy', $sections[1]['nodeIds']);
        $this->assertNotContains('section-inner', array_column($sections, 'id'));
    }

    public function test_generic_div_sections_require_visual_boundary_evidence(): void
    {
        $index = (new AutomaticCompiledNativeSectionDetector)->detect($this->snapshot([
            $this->node('body', 'body', null, ['main']),
            $this->node('main', 'main', 'body', ['visual-one', 'visual-two']),
            $this->node('visual-one', 'div', 'main', [], ['backgroundColor' => '#07192d'], ['y' => 0, 'width' => 1180, 'height' => 300]),
            $this->node('visual-two', 'div', 'main', [], ['backgroundColor' => '#f5f1e8'], ['y' => 300, 'width' => 1180, 'height' => 260]),
        ]));

        $this->assertSame(['visual-one', 'visual-two'], array_column($index->sections, 'id'));
        $this->assertContains('visual-background-change', $index->sections[0]['boundaryEvidence']);
        $this->assertContains('visual-background-change', $index->sections[1]['boundaryEvidence']);
        $this->assertSame([], $index->diagnostics);
    }

    public function test_ambiguous_generic_div_boundaries_are_reported_instead_of_invented(): void
    {
        $index = (new AutomaticCompiledNativeSectionDetector)->detect($this->snapshot([
            $this->node('body', 'body', null, ['main']),
            $this->node('main', 'main', 'body', ['wrapper-one', 'wrapper-two']),
            $this->node('wrapper-one', 'div', 'main'),
            $this->node('wrapper-two', 'div', 'main'),
        ]));

        $this->assertNotEmpty($index->diagnostics);
        $this->assertContains('section-boundary-ambiguous', array_column($index->diagnostics, 'code'));
        $this->assertSame(0.0, $index->sections[0]['boundaryConfidence']);
        $this->assertFalse($index->sections[0]['compile']);
    }

    /** @param array<int,array<string,mixed>> $nodes */
    private function snapshot(array $nodes): array
    {
        return [
            'version' => 1,
            'viewports' => [['name' => 'desktop', 'width' => 1180, 'height' => 900]],
            'nodes' => $nodes,
        ];
    }

    /** @param array<int,string> $children @param array<string,string> $style @param array<string,int|float> $rect */
    private function node(string $id, string $tag, ?string $parent, array $children = [], array $style = [], array $rect = []): array
    {
        $computedStyle = [
            'display' => 'block',
            'backgroundColor' => 'rgba(0, 0, 0, 0)',
        ] + $style;

        return [
            'sourceId' => $id,
            'tag' => $tag,
            'id' => $id,
            'classList' => [],
            'parentSourceId' => $parent,
            'computedStyle' => $computedStyle,
            'computedStyleByViewport' => ['desktop' => $computedStyle],
            'rectByViewport' => ['desktop' => [
                'x' => (float) ($rect['x'] ?? 0),
                'y' => (float) ($rect['y'] ?? 0),
                'width' => (float) ($rect['width'] ?? 1180),
                'height' => (float) ($rect['height'] ?? 100),
            ]],
            'scrollSizeByViewport' => ['desktop' => ['width' => 1180.0, 'height' => 100.0]],
            'children' => $children,
        ];
    }
}
