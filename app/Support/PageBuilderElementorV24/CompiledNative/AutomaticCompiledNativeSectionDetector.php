<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

final class AutomaticCompiledNativeSectionDetector
{
    public function detect(array $snapshot): SectionIndex
    {
        $evidence = AutomaticCompiledNativeEvidence::fromSnapshot($snapshot);
        $nodes = [];
        $order = [];
        foreach ($evidence['nodes'] as $index => $node) {
            $nodes[$node['sourceId']] = $node;
            $order[$node['sourceId']] = $index;
        }

        $body = $this->firstNodeByTag($nodes, 'body');
        $main = $this->firstNodeByTag($nodes, 'main');
        $rootId = $main['sourceId'] ?? ($body['sourceId'] ?? null);
        if ($rootId === null) {
            return new SectionIndex([], [[
                'code' => 'section-root-missing',
                'message' => 'The source measurement contains no body or main root.',
            ]]);
        }

        $headers = $this->topLevelNodesByTag($nodes, $order, 'header');
        $footers = $this->topLevelNodesByTag($nodes, $order, 'footer');
        $sections = $this->topLevelSections($nodes, $order, $rootId);

        if ($sections !== []) {
            $indexed = [];
            foreach ($headers as $header) {
                $indexed[] = $this->navigationSection($header, $nodes, 'Header', 'header', $order);
            }
            foreach ($sections as $sectionIndex => $section) {
                $indexed[] = $this->bodySection($section, $nodes, 'Section '.($sectionIndex + 1), $order, ['semantic-tag:section'], 1.0, true);
            }
            foreach ($footers as $footer) {
                $indexed[] = $this->navigationSection($footer, $nodes, 'Footer', 'footer', $order);
            }

            usort($indexed, static fn (array $left, array $right): int => ($left['_sourceOrder'] <=> $right['_sourceOrder']));
            foreach ($indexed as &$item) {
                unset($item['_sourceOrder']);
            }
            unset($item);

            return new SectionIndex(array_values($indexed));
        }

        return $this->detectVisualSections($nodes, $order, $rootId, $body, $main, $headers, $footers);
    }

    /** @param array<string,array<string,mixed>> $nodes */
    private function firstNodeByTag(array $nodes, string $tag): ?array
    {
        foreach ($nodes as $node) {
            if (($node['tag'] ?? '') === $tag) {
                return $node;
            }
        }

        return null;
    }

    /** @param array<string,array<string,mixed>> $nodes @param array<string,int> $order @return array<int,array<string,mixed>> */
    private function topLevelNodesByTag(array $nodes, array $order, string $tag): array
    {
        $result = [];
        foreach ($nodes as $node) {
            if (($node['tag'] ?? '') !== $tag || $this->hasAncestorTag($node, $nodes, $tag)) {
                continue;
            }
            $node['_sourceOrder'] = $order[$node['sourceId']] ?? PHP_INT_MAX;
            $result[] = $node;
        }
        usort($result, static fn (array $left, array $right): int => $left['_sourceOrder'] <=> $right['_sourceOrder']);

        return $result;
    }

    /** @param array<string,array<string,mixed>> $nodes @param array<string,int> $order @return array<int,array<string,mixed>> */
    private function topLevelSections(array $nodes, array $order, string $rootId): array
    {
        $result = [];
        foreach ($nodes as $node) {
            if (($node['tag'] ?? '') !== 'section' || $this->hasAncestorTag($node, $nodes, 'section')) {
                continue;
            }
            if (! $this->isDescendantOf($node['sourceId'], $rootId, $nodes)) {
                continue;
            }
            $node['_sourceOrder'] = $order[$node['sourceId']] ?? PHP_INT_MAX;
            $result[] = $node;
        }
        usort($result, static fn (array $left, array $right): int => $left['_sourceOrder'] <=> $right['_sourceOrder']);

        return $result;
    }

    /** @param array<string,array<string,mixed>> $nodes @param array<string,int> $order */
    private function navigationSection(array $node, array $nodes, string $label, string $kind, array $order): array
    {
        return $this->bodySection($node, $nodes, $label, $order, ['semantic-tag:'.$kind], 1.0, false, $kind);
    }

    /** @param array<string,array<string,mixed>> $nodes @param array<string,int> $order @param array<int,string> $boundaryEvidence */
    private function bodySection(array $node, array $nodes, string $label, array $order, array $boundaryEvidence, float $confidence, bool $compile, string $kind = 'section'): array
    {
        return [
            'id' => $node['sourceId'],
            'label' => $label,
            'kind' => $kind,
            'sourceSelector' => $this->sourceSelector($node),
            'sourceId' => $node['sourceId'],
            'boundaryConfidence' => $confidence,
            'boundaryEvidence' => array_values(array_unique($boundaryEvidence)),
            'compile' => $compile,
            'nodeIds' => $this->descendantIds($node['sourceId'], $nodes),
            'diagnostics' => [],
            '_sourceOrder' => $order[$node['sourceId']] ?? PHP_INT_MAX,
        ];
    }

    /** @param array<string,array<string,mixed>> $nodes @param array<string,int> $order @param array<string,array<string,mixed>>|null $body @param array<string,array<string,mixed>>|null $main @param array<int,array<string,mixed>> $headers @param array<int,array<string,mixed>> $footers */
    private function detectVisualSections(array $nodes, array $order, string $rootId, ?array $body, ?array $main, array $headers, array $footers): SectionIndex
    {
        $root = $main ?? $body;
        $candidateIds = is_array($root) ? array_values(array_filter($root['children'] ?? [], static fn (string $id): bool => isset($nodes[$id]))) : [];
        $excluded = array_fill_keys(array_merge(array_column($headers, 'sourceId'), array_column($footers, 'sourceId')), true);
        $candidates = [];
        foreach ($candidateIds as $candidateId) {
            if (isset($excluded[$candidateId])) {
                continue;
            }
            $candidate = $nodes[$candidateId];
            if ($this->isVisualCandidate($candidate, $root, $nodes)) {
                $candidate['_sourceOrder'] = $order[$candidateId] ?? PHP_INT_MAX;
                $candidates[] = $candidate;
            }
        }
        usort($candidates, static fn (array $left, array $right): int => $left['_sourceOrder'] <=> $right['_sourceOrder']);

        if ($this->hasClearVisualBoundaries($candidates, $nodes)) {
            $result = [];
            foreach ($candidates as $index => $candidate) {
                $evidence = ['visual-background-change'];
                if ($index > 0 && $this->hasVerticalBoundary($candidates[$index - 1], $candidate)) {
                    $evidence[] = 'visual-geometry-boundary';
                }
                $result[] = $this->bodySection($candidate, $nodes, 'Section '.($index + 1), $order, $evidence, 0.85, true);
            }

            return new SectionIndex(array_values($result));
        }

        $fallbackNode = $root ?? ['sourceId' => $rootId, 'id' => '', 'children' => []];
        $fallback = $this->bodySection($fallbackNode, $nodes, 'Section 1', $order, [], 0.0, false);
        $fallback['diagnostics'] = [[
            'code' => 'section-boundary-ambiguous',
            'message' => 'Generic wrappers do not provide a reliable visual section boundary.',
        ]];
        $fallback['nodeIds'] = $this->descendantIds($rootId, $nodes);

        return new SectionIndex([$fallback], [[
            'code' => 'section-boundary-ambiguous',
            'sectionId' => $fallback['id'],
            'message' => 'Section boundaries require semantic tags or measurable visual changes.',
        ]]);
    }

    private function sourceSelector(array $node): string
    {
        $id = trim((string) ($node['id'] ?? ''));
        if ($id !== '' && preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/', $id)) {
            return '#'.$id;
        }

        return '[data-pb-source-id="'.str_replace('"', '\\"', (string) $node['sourceId']).'"]';
    }

    private function isVisualCandidate(array $node, ?array $root, array $nodes): bool
    {
        if (($node['tag'] ?? '') === 'script' || ($node['tag'] ?? '') === 'style') {
            return false;
        }
        $style = $node['computedStyle'] ?? [];
        $background = strtolower(trim((string) ($style['backgroundColor'] ?? '')));
        $rect = $this->rect($node);
        $rootRect = $root === null ? ['width' => 0.0] : $this->rect($root);
        $fullWidth = $rootRect['width'] > 0 && $rect['width'] >= $rootRect['width'] * 0.8;
        $hasBackground = $background !== '' && ! in_array($background, ['transparent', 'rgba(0, 0, 0, 0)'], true);

        return $fullWidth || $hasBackground || $rect['height'] > 160;
    }

    private function hasClearVisualBoundaries(array $candidates, array $nodes): bool
    {
        if (count($candidates) < 2) {
            return false;
        }
        for ($index = 1; $index < count($candidates); $index++) {
            $previous = $candidates[$index - 1]['computedStyle'] ?? [];
            $current = $candidates[$index]['computedStyle'] ?? [];
            if (strtolower((string) ($previous['backgroundColor'] ?? '')) !== strtolower((string) ($current['backgroundColor'] ?? ''))) {
                return true;
            }
            if ($this->hasVerticalBoundary($candidates[$index - 1], $candidates[$index])) {
                return true;
            }
        }

        return false;
    }

    private function hasVerticalBoundary(array $previous, array $current): bool
    {
        $previousRect = $this->rect($previous);
        $currentRect = $this->rect($current);
        return abs($currentRect['y'] - ($previousRect['y'] + $previousRect['height'])) <= 2.0;
    }

    /** @return array{x:float,y:float,width:float,height:float} */
    private function rect(array $node): array
    {
        $viewport = $node['rectByViewport']['desktop'] ?? reset($node['rectByViewport']) ?: [];

        return [
            'x' => (float) ($viewport['x'] ?? 0),
            'y' => (float) ($viewport['y'] ?? 0),
            'width' => (float) ($viewport['width'] ?? 0),
            'height' => (float) ($viewport['height'] ?? 0),
        ];
    }

    /** @param array<string,array<string,mixed>> $nodes @return array<int,string> */
    private function descendantIds(string $sourceId, array $nodes): array
    {
        if (! isset($nodes[$sourceId])) {
            return [];
        }
        $result = [$sourceId];
        foreach ($nodes[$sourceId]['children'] ?? [] as $childId) {
            $result = [...$result, ...$this->descendantIds((string) $childId, $nodes)];
        }

        return array_values(array_unique($result));
    }

    /** @param array<string,array<string,mixed>> $nodes */
    private function hasAncestorTag(array $node, array $nodes, string $tag): bool
    {
        $parentId = $node['parentSourceId'] ?? null;
        $visited = [];
        while (is_string($parentId) && $parentId !== '' && isset($nodes[$parentId]) && ! isset($visited[$parentId])) {
            if (($nodes[$parentId]['tag'] ?? '') === $tag) {
                return true;
            }
            $visited[$parentId] = true;
            $parentId = $nodes[$parentId]['parentSourceId'] ?? null;
        }

        return false;
    }

    /** @param array<string,array<string,mixed>> $nodes */
    private function isDescendantOf(string $sourceId, string $ancestorId, array $nodes): bool
    {
        $parentId = $nodes[$sourceId]['parentSourceId'] ?? null;
        $visited = [];
        while (is_string($parentId) && $parentId !== '' && ! isset($visited[$parentId])) {
            if ($parentId === $ancestorId) {
                return true;
            }
            $visited[$parentId] = true;
            $parentId = $nodes[$parentId]['parentSourceId'] ?? null;
        }

        return false;
    }
}
