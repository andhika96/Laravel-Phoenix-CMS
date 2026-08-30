<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

final class AutomaticCompiledNativeLayoutClassifier
{
    /**
     * @return array<string,mixed>
     */
    public function classify(SectionIndex $sections, array $snapshot): array
    {
        $evidence = AutomaticCompiledNativeEvidence::fromSnapshot($snapshot);
        $blockNormalizer = new AutomaticCompiledNativeBlockNormalizer;
        $widgetSignatures = new AutomaticCompiledNativeWidgetSignatures;
        $relationshipBuilder = new AutomaticCompiledNativeLayoutRelationships;
        $responsiveDelta = new AutomaticCompiledNativeResponsiveDelta;
        $nodes = [];
        foreach ($evidence['nodes'] as $node) {
            $nodes[$node['sourceId']] = $node;
        }

        $classifiedSections = [];
        foreach ($sections->sections as $section) {
            $sectionId = trim((string) ($section['sourceId'] ?? $section['id'] ?? ''));
            $layoutByViewport = [];
            $sectionDiagnostics = is_array($section['diagnostics'] ?? null) ? $section['diagnostics'] : [];

            foreach ($evidence['viewports'] as $viewport) {
                $viewportName = $viewport['name'];
                if (! isset($nodes[$sectionId])) {
                    $layout = $this->unclassifiedLayout('section-root-missing', "Section root '{$sectionId}' is not present in the measurement.");
                } else {
                    $layout = $this->classifyNode($nodes[$sectionId], $nodes, $viewportName);
                }
                $layoutByViewport[$viewportName] = $layout;
                foreach ($layout['diagnostics'] as $diagnostic) {
                    $sectionDiagnostics[] = [
                        ...$diagnostic,
                        'viewport' => $viewportName,
                    ];
                }
            }

            $sectionNodes = $this->sectionNodes($section, $nodes);
            $layoutRelationships = $relationshipBuilder->build($sectionNodes, $evidence['viewports']);
            $responsiveDeltas = $responsiveDelta->compare($sectionNodes, $evidence['viewports']);
            $normalizedBlocks = $widgetSignatures->withCandidates($blockNormalizer->normalizeSection([
                ...$section,
                'nodes' => $sectionNodes,
            ]), $layoutRelationships);
            $classifiedSections[] = [
                ...$section,
                'layoutByViewport' => $layoutByViewport,
                'nodes' => $sectionNodes,
                'layoutRelationships' => $layoutRelationships,
                'responsiveDeltas' => $responsiveDeltas,
                'normalizedBlocks' => $normalizedBlocks,
                'diagnostics' => array_values($sectionDiagnostics),
            ];
        }

        return [
            'version' => 1,
            'viewports' => $evidence['viewports'],
            'sections' => $classifiedSections,
            'diagnostics' => [
                ...$evidence['diagnostics'],
                ...$sections->diagnostics,
            ],
        ];
    }

    /**
     * Parse the measured parent's CSS grid tracks and use placement/geometry only
     * when the computed declaration does not expose a usable track list.
     *
     * @param array<string,mixed> $parent
     * @param array<string,array<string,mixed>> $nodes
     * @return array{tracks:array<int,string>,columns:int,evidence:array<string,mixed>,diagnostics:array<int,array<string,mixed>>}
     */
    public function gridTracks(array $parent, array $nodes = [], ?string $viewport = null): array
    {
        $viewport ??= $this->firstViewport($parent, 'desktop');
        $style = $this->styleFor($parent, $viewport);
        $rawTracks = trim((string) ($style['gridTemplateColumns'] ?? ''));
        $tracks = $this->expandTrackList($rawTracks);
        if ($tracks !== []) {
            return [
                'tracks' => $tracks,
                'columns' => count($tracks),
                'evidence' => [
                    'rule' => 'computedStyle.gridTemplateColumns',
                    'rules' => ['computedStyle.display', 'computedStyle.gridTemplateColumns'],
                    'computedStyle' => [
                        'display' => (string) ($style['display'] ?? ''),
                        'gridTemplateColumns' => $rawTracks,
                    ],
                ],
                'diagnostics' => [],
            ];
        }

        $placedColumns = $this->explicitGridColumns($parent, $nodes, $viewport);
        if ($placedColumns > 1) {
            $tracks = $this->measuredTracks($parent, $nodes, $viewport, $placedColumns);

            return [
                'tracks' => $tracks,
                'columns' => $placedColumns,
                'evidence' => [
                    'rule' => 'computedStyle.gridColumnStartEnd',
                    'rules' => ['computedStyle.display', 'children.computedStyle.gridColumnStartEnd', 'children.rectByViewport.'.$viewport],
                    'computedStyle' => [
                        'display' => (string) ($style['display'] ?? ''),
                        'gridTemplateColumns' => $rawTracks,
                    ],
                ],
                'diagnostics' => [],
            ];
        }

        $clusters = $this->axisClusters($this->visibleChildren($parent, $nodes, $viewport), 'x', $viewport);
        if (count($clusters) > 1) {
            $tracks = $this->measuredTracks($parent, $nodes, $viewport, count($clusters));

            return [
                'tracks' => $tracks,
                'columns' => count($clusters),
                'evidence' => [
                    'rule' => 'children.rectByViewport.'.$viewport,
                    'rules' => ['computedStyle.display', 'children.rectByViewport.'.$viewport],
                    'computedStyle' => [
                        'display' => (string) ($style['display'] ?? ''),
                        'gridTemplateColumns' => $rawTracks,
                    ],
                    'geometry' => ['xClusters' => count($clusters)],
                ],
                'diagnostics' => [],
            ];
        }

        return [
            'tracks' => [],
            'columns' => 0,
            'evidence' => [],
            'diagnostics' => [],
        ];
    }

    /**
     * Group flex children by their measured cross-axis line while preserving CSS order.
     *
     * @param array<string,mixed> $parent
     * @param array<string,array<string,mixed>> $nodes
     * @return array{lines:array<int,array<int,string>>,columns:int,evidence:array<string,mixed>,diagnostics:array<int,array<string,mixed>>}
     */
    public function flexLines(array $parent, array $nodes = [], ?string $viewport = null): array
    {
        $viewport ??= $this->firstViewport($parent, 'desktop');
        $style = $this->styleFor($parent, $viewport);
        $children = $this->visibleChildren($parent, $nodes, $viewport);
        $direction = strtolower(trim((string) ($style['flexDirection'] ?? 'row')));
        $isRow = in_array($direction, ['row', 'row-reverse'], true);
        $wrap = strtolower(trim((string) ($style['flexWrap'] ?? 'nowrap')));

        usort($children, function (array $left, array $right) use ($nodes, $viewport, $isRow, $wrap): int {
            $leftStyle = $this->styleFor($left, $viewport);
            $rightStyle = $this->styleFor($right, $viewport);
            $order = ((int) ($leftStyle['order'] ?? 0)) <=> ((int) ($rightStyle['order'] ?? 0));
            if ($order !== 0) {
                return $order;
            }

            $leftRect = $this->rectFor($left, $viewport);
            $rightRect = $this->rectFor($right, $viewport);
            $axis = $isRow ? 'y' : 'x';
            $cross = ($leftRect[$axis] ?? 0) <=> ($rightRect[$axis] ?? 0);
            if ($cross !== 0 && $wrap !== 'nowrap') {
                return $cross;
            }

            return (($isRow ? $leftRect['x'] : $leftRect['y']) <=> ($isRow ? $rightRect['x'] : $rightRect['y']))
                ?: (($nodes[$left['sourceId']]['sourceId'] ?? '') <=> ($nodes[$right['sourceId']]['sourceId'] ?? ''));
        });

        if ($children === []) {
            return [
                'lines' => [[]],
                'columns' => 1,
                'evidence' => [
                    'rule' => 'computedStyle.display.flexDirection',
                    'rules' => ['computedStyle.display', 'computedStyle.flexDirection'],
                ],
                'diagnostics' => [],
            ];
        }

        $lines = [];
        if ($wrap === 'nowrap') {
            $lines[] = array_values(array_map(static fn (array $child): string => $child['sourceId'], $children));
        } else {
            $axis = $isRow ? 'y' : 'x';
            foreach ($children as $child) {
                $coordinate = $this->rectFor($child, $viewport)[$axis] ?? 0.0;
                $lastLine = array_key_last($lines);
                $rect = $this->rectFor($child, $viewport);
                $crossEnd = $coordinate + ($isRow ? $rect['height'] : $rect['width']);
                if ($lastLine === null || $coordinate > (($lines[$lastLine]['end'] ?? $coordinate) + 3.0)) {
                    $lines[] = ['coordinate' => $coordinate, 'end' => $crossEnd, 'items' => [$child['sourceId']]];
                } else {
                    $lines[$lastLine]['items'][] = $child['sourceId'];
                    $lines[$lastLine]['end'] = max($lines[$lastLine]['end'], $crossEnd);
                }
            }
            $lines = array_values(array_map(static fn (array $line): array => $line['items'], $lines));
        }

        return [
            'lines' => $lines,
            'columns' => max(1, ...array_map('count', $lines)),
            'evidence' => [
                'rule' => 'children.rectByViewport.'.$viewport,
                'rules' => ['computedStyle.display', 'computedStyle.flexDirection', 'computedStyle.flexWrap', 'children.rectByViewport.'.$viewport],
                'computedStyle' => [
                    'display' => (string) ($style['display'] ?? ''),
                    'flexDirection' => (string) ($style['flexDirection'] ?? ''),
                    'flexWrap' => (string) ($style['flexWrap'] ?? ''),
                ],
            ],
            'diagnostics' => [],
        ];
    }

    /**
     * @param array<string,mixed> $parent
     * @param array<string,array<string,mixed>> $nodes
     * @return array<string,mixed>
     */
    private function classifyNode(array $parent, array $nodes, string $viewport): array
    {
        $style = $this->styleFor($parent, $viewport);
        $parentTransform = strtolower(trim((string) ($style['transform'] ?? 'none')));
        if ($parentTransform !== '' && $parentTransform !== 'none') {
            return $this->unclassifiedLayout('transformed-section', 'The section root has a CSS transform that cannot be represented safely as normal columns.', [(string) ($parent['sourceId'] ?? '')]);
        }
        $children = $this->visibleChildren($parent, $nodes, $viewport);
        $unsafe = $this->unsafeChildren($parent, $children, $viewport);
        if ($unsafe !== [] && ! $this->hasReconstructablePositionedChildren($unsafe, $children, $viewport)) {
            return $this->unclassifiedLayout('absolute-or-transformed-child', 'The section contains positioned, transformed, or overlapping children.', $unsafe);
        }

        $display = strtolower(trim((string) ($style['display'] ?? 'block')));
        if ($display === 'grid') {
            $grid = $this->gridTracks($parent, $nodes, $viewport);
            if ($grid['columns'] > 0) {
                if ($grid['columns'] === 1) {
                    return $this->layout('grid', 1, $grid['tracks'], $grid['evidence'], $grid['diagnostics'], $this->gap($style));
                }

                return $this->layout('grid', $grid['columns'], $grid['tracks'], $grid['evidence'], $grid['diagnostics'], $this->gap($style));
            }

            return $this->layout(
                'stack',
                1,
                ['1fr'],
                [
                    'rule' => 'computedStyle.display',
                    'rules' => ['computedStyle.display', 'computedStyle.gridTemplateColumns'],
                    'computedStyle' => [
                        'display' => $display,
                        'gridTemplateColumns' => (string) ($style['gridTemplateColumns'] ?? ''),
                    ],
                ],
                [],
                $this->gap($style),
            );
        }

        if ($display === 'flex' || $display === 'inline-flex') {
            $flex = $this->flexLines($parent, $nodes, $viewport);

            return $this->layout('flex', $flex['columns'], $this->flexTracks($children, $flex['lines'], $viewport), $flex['evidence'], $flex['diagnostics'], $this->gap($style), $flex['lines']);
        }

        return $this->layout(
            'stack',
            1,
            ['1fr'],
            [
                'rule' => 'computedStyle.display',
                'rules' => ['computedStyle.display'],
                'computedStyle' => ['display' => (string) ($style['display'] ?? '')],
            ],
            [],
            $this->gap($style),
        );
    }

    /** @param array<int,array<string,mixed>> $diagnostics @param array<int,string> $lines */
    private function layout(string $mode, int $columns, array $tracks, array $evidence, array $diagnostics, string $gap = '', array $lines = []): array
    {
        $result = [
            'mode' => $mode,
            'columns' => max(1, $columns),
            'tracks' => array_values($tracks),
            'gap' => $gap,
            'confidence' => $mode === 'unclassified' ? 0.0 : ($evidence['geometry'] ?? null ? 0.9 : 1.0),
            'evidence' => $evidence,
            'diagnostics' => array_values($diagnostics),
        ];
        if ($lines !== []) {
            $result['lines'] = $lines;
        }

        return $result;
    }

    /** @param array<int,string> $sourceIds */
    private function unclassifiedLayout(string $code, string $message, array $sourceIds = []): array
    {
        return [
            'mode' => 'unclassified',
            'columns' => 1,
            'tracks' => ['1fr'],
            'gap' => '',
            'confidence' => 0.0,
            'evidence' => [
                'rule' => $code,
                'rules' => ['children.geometry'],
            ],
            'diagnostics' => [[
                'code' => $code,
                'message' => $message,
                'sourceIds' => array_values(array_unique($sourceIds)),
            ]],
        ];
    }

    /** @param array<string,mixed> $section @param array<string,array<string,mixed>> $nodes @return array<int,array<string,mixed>> */
    private function sectionNodes(array $section, array $nodes): array
    {
        $ids = is_array($section['nodeIds'] ?? null) ? $section['nodeIds'] : [];
        $result = [];
        foreach ($ids as $order => $id) {
            $id = (string) $id;
            if (! isset($nodes[$id])) {
                continue;
            }
            $node = $nodes[$id];
            $style = $node['computedStyle'] ?? [];
            $result[] = [
                'sourceId' => $id,
                'tag' => $node['tag'],
                'id' => $node['id'],
                'classList' => $node['classList'],
                'parentSourceId' => $node['parentSourceId'],
                'textContent' => $node['textContent'] ?? '',
                'innerHTML' => $node['innerHTML'] ?? '',
                'attributes' => $node['attributes'] ?? [],
                'order' => $order,
                'layoutRole' => ($node['children'] ?? []) === [] ? 'content' : (in_array(strtolower((string) ($style['display'] ?? '')), ['grid', 'flex', 'inline-flex'], true) ? 'layout' : 'container'),
                'rectByViewport' => $node['rectByViewport'],
                'computedStyleByViewport' => $node['computedStyleByViewport'],
                'pseudoElementsByViewport' => $node['pseudoElementsByViewport'] ?? [],
            ];
        }

        return $result;
    }

    /** @param array<string,mixed> $parent @param array<string,array<string,mixed>> $nodes @return array<int,array<string,mixed>> */
    private function visibleChildren(array $parent, array $nodes, string $viewport): array
    {
        $children = [];
        $childIds = is_array($parent['children'] ?? null) ? $parent['children'] : [];
        if ($childIds === []) {
            foreach ($nodes as $node) {
                if (($node['parentSourceId'] ?? null) === ($parent['sourceId'] ?? null)) {
                    $childIds[] = $node['sourceId'];
                }
            }
        }
        foreach ($childIds as $childId) {
            $childId = (string) $childId;
            if (! isset($nodes[$childId])) {
                continue;
            }
            $child = $nodes[$childId];
            if (! $this->isVisible($child, $viewport)) {
                continue;
            }
            $children[] = $child;
        }

        return $children;
    }

    /** @param array<string,mixed> $parent @param array<int,array<string,mixed>> $children @return array<int,string> */
    private function unsafeChildren(array $parent, array $children, string $viewport): array
    {
        $unsafe = [];
        foreach ($children as $child) {
            $style = $this->styleFor($child, $viewport);
            $position = strtolower(trim((string) ($style['position'] ?? 'static')));
            $transform = strtolower(trim((string) ($style['transform'] ?? 'none')));
            if (in_array($position, ['absolute', 'fixed'], true) || ($transform !== '' && $transform !== 'none')) {
                $unsafe[] = $child['sourceId'];
            }
        }

        $parentDisplay = strtolower(trim((string) (($this->styleFor($parent, $viewport))['display'] ?? '')));
        if (! in_array($parentDisplay, ['grid', 'flex', 'inline-flex'], true)) {
            for ($leftIndex = 0; $leftIndex < count($children); $leftIndex++) {
                for ($rightIndex = $leftIndex + 1; $rightIndex < count($children); $rightIndex++) {
                    if ($this->rectsOverlap($this->rectFor($children[$leftIndex], $viewport), $this->rectFor($children[$rightIndex], $viewport))) {
                        $unsafe[] = $children[$leftIndex]['sourceId'];
                        $unsafe[] = $children[$rightIndex]['sourceId'];
                    }
                }
            }
        }

        return array_values(array_unique($unsafe));
    }

    /** @param array<int,string> $unsafe @param array<int,array<string,mixed>> $children */
    private function hasReconstructablePositionedChildren(array $unsafe, array $children, string $viewport): bool
    {
        $byId = [];
        $hasPositioned = false;
        foreach ($children as $child) {
            $byId[(string) ($child['sourceId'] ?? '')] = $child;
        }
        foreach ($unsafe as $sourceId) {
            $style = $this->styleFor($byId[(string) $sourceId] ?? [], $viewport);
            $position = strtolower(trim((string) ($style['position'] ?? 'static')));
            $transform = strtolower(trim((string) ($style['transform'] ?? 'none')));
            if ($transform !== '' && $transform !== 'none') {
                return false;
            }
            if (in_array($position, ['absolute', 'fixed'], true)) {
                $hasPositioned = true;
            }
        }

        return $unsafe !== [] && $hasPositioned;
    }

    /** @param array<string,mixed> $node */
    private function isVisible(array $node, string $viewport): bool
    {
        $style = $this->styleFor($node, $viewport);
        if (strtolower(trim((string) ($style['display'] ?? ''))) === 'none') {
            return false;
        }
        if (in_array(strtolower(trim((string) ($style['visibility'] ?? ''))), ['hidden', 'collapse'], true)) {
            return false;
        }
        if ((float) ($style['opacity'] ?? 1) <= 0) {
            return false;
        }
        $rect = $this->rectFor($node, $viewport);

        return $rect['width'] > 0 && $rect['height'] > 0;
    }

    /** @param array<string,mixed> $node @return array<string,string> */
    private function styleFor(array $node, string $viewport): array
    {
        $styles = is_array($node['computedStyleByViewport'] ?? null) ? $node['computedStyleByViewport'] : [];
        $style = $styles[$viewport] ?? ($node['computedStyle'] ?? []);

        return is_array($style) ? $style : [];
    }

    /** @param array<string,mixed> $node @return array{x:float,y:float,width:float,height:float} */
    private function rectFor(array $node, string $viewport): array
    {
        $rects = is_array($node['rectByViewport'] ?? null) ? $node['rectByViewport'] : [];
        $rect = is_array($rects[$viewport] ?? null) ? $rects[$viewport] : (is_array(reset($rects)) ? reset($rects) : []);

        return [
            'x' => (float) ($rect['x'] ?? 0),
            'y' => (float) ($rect['y'] ?? 0),
            'width' => max(0, (float) ($rect['width'] ?? 0)),
            'height' => max(0, (float) ($rect['height'] ?? 0)),
        ];
    }

    /** @param array<string,mixed> $style */
    private function gap(array $style): string
    {
        $gap = trim((string) ($style['gap'] ?? ''));
        if ($gap !== '' && $gap !== '0px' && $gap !== 'normal') {
            return $gap;
        }

        $columnGap = trim((string) ($style['columnGap'] ?? ''));

        return $columnGap !== '' && $columnGap !== '0px' && $columnGap !== 'normal' ? $columnGap : '';
    }

    /** @param array<string,mixed> $parent @param array<string,array<string,mixed>> $nodes */
    private function explicitGridColumns(array $parent, array $nodes, string $viewport): int
    {
        $max = 0;
        foreach ($this->visibleChildren($parent, $nodes, $viewport) as $child) {
            $style = $this->styleFor($child, $viewport);
            $start = $this->gridLine((string) ($style['gridColumnStart'] ?? ''));
            $end = $this->gridLine((string) ($style['gridColumnEnd'] ?? ''));
            if ($start !== null && $end !== null && $end > $start) {
                $max = max($max, $end - 1);
            } elseif ($start !== null) {
                $max = max($max, $start);
            }
        }

        return $max;
    }

    private function gridLine(string $value): ?int
    {
        $value = strtolower(trim($value));
        if ($value === '' || $value === 'auto' || str_contains($value, 'span')) {
            if (preg_match('/^span\s+(\d+)$/', $value, $match)) {
                return (int) $match[1] + 1;
            }

            return null;
        }

        return preg_match('/^\d+$/', $value) ? (int) $value : null;
    }

    /** @param array<string,mixed> $parent @param array<string,array<string,mixed>> $nodes @return array<int,string> */
    private function measuredTracks(array $parent, array $nodes, string $viewport, int $columns): array
    {
        $children = $this->visibleChildren($parent, $nodes, $viewport);
        $clusters = $this->axisClusters($children, 'x', $viewport);
        $tracks = [];
        foreach (array_slice($clusters, 0, $columns) as $cluster) {
            $width = 0.0;
            foreach ($cluster as $child) {
                $width = max($width, $this->rectFor($child, $viewport)['width']);
            }
            $tracks[] = $this->pixel($width);
        }
        while (count($tracks) < $columns) {
            $tracks[] = '1fr';
        }

        return $tracks;
    }

    /** @param array<int,array<string,mixed>> $children @return array<int,array<int,array<string,mixed>>> */
    private function axisClusters(array $children, string $axis, string $viewport): array
    {
        usort($children, function (array $left, array $right) use ($axis, $viewport): int {
            return $this->rectFor($left, $viewport)[$axis] <=> $this->rectFor($right, $viewport)[$axis];
        });
        $clusters = [];
        foreach ($children as $child) {
            $coordinate = $this->rectFor($child, $viewport)[$axis];
            $last = array_key_last($clusters);
            if ($last === null || abs($coordinate - ($clusters[$last]['coordinate'] ?? $coordinate)) > 3.0) {
                $clusters[] = ['coordinate' => $coordinate, 'items' => [$child]];
            } else {
                $clusters[$last]['items'][] = $child;
            }
        }

        return array_values(array_map(static fn (array $cluster): array => $cluster['items'], $clusters));
    }

    /** @param array<int,array<string,mixed>> $children @param array<int,array<int,string>> $lines @return array<int,string> */
    private function flexTracks(array $children, array $lines, string $viewport): array
    {
        if ($children === [] || $lines === []) {
            return ['1fr'];
        }

        $byId = [];
        foreach ($children as $child) {
            $byId[$child['sourceId']] = $child;
        }
        $tracks = [];
        $columns = max(1, ...array_map('count', $lines));
        for ($column = 0; $column < $columns; $column++) {
            $width = 0.0;
            foreach ($lines as $line) {
                $sourceId = $line[$column] ?? null;
                if ($sourceId !== null && isset($byId[$sourceId])) {
                    $width = max($width, $this->rectFor($byId[$sourceId], $viewport)['width']);
                }
            }
            $tracks[] = $width > 0 ? $this->pixel($width) : '1fr';
        }

        return $tracks;
    }

    /** @param array{x:float,y:float,width:float,height:float} $left @param array{x:float,y:float,width:float,height:float} $right */
    private function rectsOverlap(array $left, array $right): bool
    {
        return min($left['x'] + $left['width'], $right['x'] + $right['width']) > max($left['x'], $right['x'])
            && min($left['y'] + $left['height'], $right['y'] + $right['height']) > max($left['y'], $right['y']);
    }

    private function firstViewport(array $node, string $fallback): string
    {
        $styles = is_array($node['computedStyleByViewport'] ?? null) ? array_keys($node['computedStyleByViewport']) : [];

        return (string) ($styles[0] ?? $fallback);
    }

    private function pixel(float $value): string
    {
        return rtrim(rtrim(number_format(max(0, $value), 2, '.', ''), '0'), '.').'px';
    }

    /** @return array<int,string> */
    private function expandTrackList(string $value): array
    {
        $value = trim($value);
        if ($value === '' || in_array(strtolower($value), ['none', 'auto', 'normal', 'subgrid'], true)) {
            return [];
        }

        $tracks = [];
        foreach ($this->splitTopLevelWhitespace($value) as $token) {
            $function = $this->functionCall($token);
            if ($function === null || strtolower($function['name']) !== 'repeat') {
                $tracks[] = $token;
                continue;
            }
            $arguments = $this->splitTopLevel($function['arguments'], ',');
            $count = isset($arguments[0]) && preg_match('/^\d+$/', trim($arguments[0])) ? (int) trim($arguments[0]) : 0;
            if ($count < 1 || $count > 100 || ! isset($arguments[1])) {
                continue;
            }
            $inner = $this->expandTrackList(trim($arguments[1]));
            if ($inner === []) {
                continue;
            }
            for ($index = 0; $index < $count; $index++) {
                $tracks = [...$tracks, ...$inner];
            }
        }

        return $tracks;
    }

    /** @return array<int,string> */
    private function splitTopLevelWhitespace(string $value): array
    {
        $result = [];
        $buffer = '';
        $depth = 0;
        $length = strlen($value);
        for ($index = 0; $index < $length; $index++) {
            $character = $value[$index];
            if ($character === '(') {
                $depth++;
            } elseif ($character === ')' && $depth > 0) {
                $depth--;
            }
            if ($depth === 0 && ctype_space($character)) {
                if (trim($buffer) !== '') {
                    $result[] = trim($buffer);
                    $buffer = '';
                }
                continue;
            }
            $buffer .= $character;
        }
        if (trim($buffer) !== '') {
            $result[] = trim($buffer);
        }

        return $result;
    }

    /** @return array<int,string> */
    private function splitTopLevel(string $value, string $separator): array
    {
        $result = [];
        $buffer = '';
        $depth = 0;
        foreach (str_split($value) as $character) {
            if ($character === '(') {
                $depth++;
            } elseif ($character === ')' && $depth > 0) {
                $depth--;
            }
            if ($depth === 0 && $character === $separator) {
                $result[] = trim($buffer);
                $buffer = '';
                continue;
            }
            $buffer .= $character;
        }
        $result[] = trim($buffer);

        return array_values(array_filter($result, static fn (string $item): bool => $item !== ''));
    }

    /** @return array{name:string,arguments:string}|null */
    private function functionCall(string $value): ?array
    {
        $open = strpos($value, '(');
        if ($open === false || ! str_ends_with($value, ')')) {
            return null;
        }
        $name = trim(substr($value, 0, $open));
        if ($name === '') {
            return null;
        }

        return [
            'name' => $name,
            'arguments' => substr($value, $open + 1, -1),
        ];
    }
}
