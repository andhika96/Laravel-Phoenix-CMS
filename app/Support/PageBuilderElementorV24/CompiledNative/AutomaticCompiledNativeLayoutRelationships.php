<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

final class AutomaticCompiledNativeLayoutRelationships
{
    /**
     * Build traceable layout relationships from measured nodes without changing
     * the raw node records or deciding widget types.
     *
     * @param array<int,array<string,mixed>>|array<string,array<string,mixed>> $nodes
     * @param array<int,array{name:string,width:float|int,height:float|int}> $viewports
     * @return array<string,mixed>
     */
    public function build(array $nodes, array $viewports): array
    {
        $nodes = $this->indexNodes($nodes);
        $viewportNames = array_values(array_filter(array_map(static fn (mixed $viewport): string => is_array($viewport) ? strtolower(trim((string) ($viewport['name'] ?? ''))) : '', $viewports)));
        if ($viewportNames === []) {
            $viewportNames = ['desktop'];
        }

        $containingBlocks = [];
        $positionedLayers = [];
        foreach ($nodes as $sourceId => $node) {
            $positionByViewport = [];
            foreach ($viewportNames as $viewport) {
                $style = $this->styleFor($node, $viewport);
                $position = strtolower(trim((string) ($style['position'] ?? 'static')));
                if (! in_array($position, ['absolute', 'fixed', 'relative', 'sticky'], true)) {
                    continue;
                }
                $parent = $this->containingBlock($node, $nodes, $position);
                $parentRect = $parent === null ? ['x' => 0.0, 'y' => 0.0] : $this->rectFor($parent, $viewport);
                $rect = $this->rectFor($node, $viewport);
                $positionByViewport[$viewport] = [
                    'position' => $position,
                    'relativeRect' => [
                        'x' => round((float) ($rect['x'] ?? 0) - (float) ($parentRect['x'] ?? 0), 3),
                        'y' => round((float) ($rect['y'] ?? 0) - (float) ($parentRect['y'] ?? 0), 3),
                        'width' => (float) ($rect['width'] ?? 0),
                        'height' => (float) ($rect['height'] ?? 0),
                    ],
                    'style' => $style,
                    'zIndex' => $this->zIndex($style),
                ];
            }
            if ($positionByViewport === []) {
                continue;
            }
            $isLayer = array_reduce($positionByViewport, static fn (bool $carry, array $record): bool => $carry || in_array($record['position'], ['absolute', 'fixed'], true), false);
            if (! $isLayer) {
                continue;
            }
            $parentId = $this->containingBlockId($node, $nodes, $positionByViewport[array_key_first($positionByViewport)]['position']);
            $positionedLayers[] = [
                'sourceId' => $sourceId,
                'containingBlockId' => $parentId,
                'positionByViewport' => $positionByViewport,
            ];
            if ($parentId !== null) {
                $containingBlocks[$sourceId] = ['sourceId' => $parentId];
            }
        }

        return [
            'normalFlowGroups' => $this->normalFlowGroups($nodes),
            'positionedLayers' => $positionedLayers,
            'overlapPairs' => $this->overlapPairs($nodes, $viewportNames),
            'containingBlocks' => $containingBlocks,
            'stackingOrder' => $this->stackingOrder($nodes, $viewportNames),
            'pseudoElements' => $this->pseudoElements($nodes, $viewportNames),
            'responsiveDeltas' => $this->responsiveDeltas($positionedLayers, $viewportNames),
        ];
    }

    /** @param array<int,array<string,mixed>>|array<string,array<string,mixed>> $nodes @return array<string,array<string,mixed>> */
    private function indexNodes(array $nodes): array
    {
        $indexed = [];
        foreach ($nodes as $key => $node) {
            if (! is_array($node)) {
                continue;
            }
            $id = trim((string) ($node['sourceId'] ?? $key));
            if ($id !== '') {
                $indexed[$id] = $node;
            }
        }

        return $indexed;
    }

    /** @param array<string,array<string,mixed>> $nodes @return array<int,array<string,mixed>> */
    private function normalFlowGroups(array $nodes): array
    {
        $groups = [];
        foreach ($nodes as $sourceId => $node) {
            $style = $this->styleFor($node, 'desktop');
            if (in_array(strtolower(trim((string) ($style['position'] ?? 'static'))), ['absolute', 'fixed'], true)) continue;
            $parentSourceId = isset($node['parentSourceId']) && $node['parentSourceId'] !== null ? (string) $node['parentSourceId'] : null;
            $groupKey = $parentSourceId ?? '__root__';
            if (! isset($groups[$groupKey])) {
                $groups[$groupKey] = ['parentSourceId' => $parentSourceId, 'sourceIds' => [], 'layout' => 'normal-flow'];
            }
            $groups[$groupKey]['sourceIds'][] = $sourceId;
        }

        return array_values($groups);
    }

    /** @param array<string,array<string,mixed>> $nodes @param array<int,string> $viewports @return array<int,array<string,mixed>> */
    private function overlapPairs(array $nodes, array $viewports): array
    {
        $pairs = [];
        foreach ($viewports as $viewport) {
            $ids = array_keys($nodes);
            for ($leftIndex = 0; $leftIndex < count($ids); $leftIndex++) {
                for ($rightIndex = $leftIndex + 1; $rightIndex < count($ids); $rightIndex++) {
                    $leftId = $ids[$leftIndex];
                    $rightId = $ids[$rightIndex];
                    if (($nodes[$leftId]['parentSourceId'] ?? null) !== ($nodes[$rightId]['parentSourceId'] ?? null)) {
                        continue;
                    }
                    $left = $this->rectFor($nodes[$leftId], $viewport);
                    $right = $this->rectFor($nodes[$rightId], $viewport);
                    if (! $this->intersects($left, $right)) {
                        continue;
                    }
                    $leftZ = $this->zIndex($this->styleFor($nodes[$leftId], $viewport));
                    $rightZ = $this->zIndex($this->styleFor($nodes[$rightId], $viewport));
                    if ($leftZ === $rightZ && $leftZ === 0) {
                        continue;
                    }
                    $pairs[] = [
                        'lower' => $leftZ <= $rightZ ? $leftId : $rightId,
                        'upper' => $leftZ <= $rightZ ? $rightId : $leftId,
                        'viewport' => $viewport,
                    ];
                }
            }
        }

        return $pairs;
    }

    /** @param array<string,array<string,mixed>> $nodes @param array<int,string> $viewports @return array<string,array<string,array<int,string>>> */
    private function stackingOrder(array $nodes, array $viewports): array
    {
        $result = [];
        foreach ($viewports as $viewport) {
            $groups = [];
            foreach ($nodes as $sourceId => $node) {
                $parentId = (string) ($node['parentSourceId'] ?? '__root__');
                $groups[$parentId][] = ['id' => $sourceId, 'z' => $this->zIndex($this->styleFor($node, $viewport))];
            }
            foreach ($groups as $parentId => $items) {
                usort($items, static fn (array $left, array $right): int => $left['z'] <=> $right['z']);
                $groups[$parentId] = array_values(array_map(static fn (array $item): string => $item['id'], $items));
            }
            $result[$viewport] = $groups;
        }

        return $result;
    }

    /** @param array<string,array<string,mixed>> $nodes @param array<int,string> $viewports @return array<int,array<string,mixed>> */
    private function pseudoElements(array $nodes, array $viewports): array
    {
        $result = [];
        foreach ($nodes as $sourceId => $node) {
            $records = $node['pseudoElementsByViewport'] ?? [];
            if (! is_array($records)) {
                continue;
            }
            foreach ($viewports as $viewport) {
                if (is_array($records[$viewport] ?? null) && $records[$viewport] !== []) {
                    $result[] = ['sourceId' => $sourceId, 'viewport' => $viewport, 'pseudoElements' => $records[$viewport]];
                }
            }
        }

        return $result;
    }

    /** @param array<int,array<string,mixed>> $layers @param array<int,string> $viewports @return array<string,array<string,string>> */
    private function responsiveDeltas(array $layers, array $viewports): array
    {
        $result = [];
        foreach ($layers as $layer) {
            $records = $layer['positionByViewport'] ?? [];
            $values = [];
            foreach ($viewports as $viewport) {
                $value = $records[$viewport]['relativeRect']['x'] ?? null;
                if ($value !== null) {
                    $values[] = rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.').'px';
                }
            }
            if (count(array_unique($values)) > 1) {
                $result[(string) $layer['sourceId']]['positionX'] = implode(' → ', $values);
            }
        }

        return $result;
    }

    /** @param array<string,mixed> $node @param array<string,array<string,mixed>> $nodes */
    private function containingBlock(?array $node, array $nodes, string $position): ?array
    {
        $id = $this->containingBlockId($node, $nodes, $position);
        return $id === null ? null : $nodes[$id];
    }

    /** @param array<string,mixed>|null $node @param array<string,array<string,mixed>> $nodes */
    private function containingBlockId(?array $node, array $nodes, string $position): ?string
    {
        if ($node === null || $position === 'fixed') {
            return null;
        }
        $parentId = $node['parentSourceId'] ?? null;
        while (is_string($parentId) && isset($nodes[$parentId])) {
            $parentStyle = $this->styleFor($nodes[$parentId], 'desktop');
            if (strtolower(trim((string) ($parentStyle['position'] ?? 'static'))) !== 'static') {
                return $parentId;
            }
            $parentId = $nodes[$parentId]['parentSourceId'] ?? null;
        }

        return null;
    }

    /** @param array<string,mixed> $node */
    private function styleFor(array $node, string $viewport): array
    {
        $styles = is_array($node['computedStyleByViewport'] ?? null) ? $node['computedStyleByViewport'] : [];
        $base = is_array($node['computedStyle'] ?? null) ? $node['computedStyle'] : [];
        $desktop = is_array($styles['desktop'] ?? null) ? $styles['desktop'] : $base;
        $style = $viewport === 'desktop' ? $desktop : array_merge($desktop, is_array($styles[$viewport] ?? null) ? $styles[$viewport] : []);
        return is_array($style) ? $style : [];
    }

    /** @param array<string,mixed> $node */
    private function rectFor(array $node, string $viewport): array
    {
        $rects = is_array($node['rectByViewport'] ?? null) ? $node['rectByViewport'] : [];
        $rect = $rects[$viewport] ?? ($rects['desktop'] ?? []);
        return is_array($rect) ? $rect : [];
    }

    /** @param array<string,mixed> $style */
    private function zIndex(array $style): int
    {
        $value = strtolower(trim((string) ($style['zIndex'] ?? 'auto')));
        return is_numeric($value) ? (int) $value : 0;
    }

    /** @param array<string,mixed> $left @param array<string,mixed> $right */
    private function intersects(array $left, array $right): bool
    {
        $leftRight = (float) ($left['x'] ?? 0) + (float) ($left['width'] ?? 0);
        $rightRight = (float) ($right['x'] ?? 0) + (float) ($right['width'] ?? 0);
        $leftBottom = (float) ($left['y'] ?? 0) + (float) ($left['height'] ?? 0);
        $rightBottom = (float) ($right['y'] ?? 0) + (float) ($right['height'] ?? 0);
        return (float) ($left['x'] ?? 0) < $rightRight
            && (float) ($right['x'] ?? 0) < $leftRight
            && (float) ($left['y'] ?? 0) < $rightBottom
            && (float) ($right['y'] ?? 0) < $leftBottom;
    }
}
