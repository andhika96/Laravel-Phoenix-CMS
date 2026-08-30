<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

final class AutomaticCompiledNativeValidator
{
    public function __construct(private readonly float $pixelTolerance = 1.0)
    {
    }

    /**
     * Compare source and target measurements at equal viewport names.
     *
     * @return array<string,mixed>
     */
    public function compare(array $source, array $targetLayout, array $target): array
    {
        $sourceEvidence = AutomaticCompiledNativeEvidence::fromSnapshot($source);
        $targetEvidence = AutomaticCompiledNativeEvidence::fromSnapshot($target);
        $sourceNodes = $this->indexNodes($sourceEvidence['nodes']);
        $targetNodes = $this->indexNodes($targetEvidence['nodes']);
        $structuralErrors = [];
        $nodeDeltas = [];
        $boxModelDeltas = [];
        $responsiveMismatches = [];
        $unrepresentedCss = [];

        foreach ($sourceNodes as $sourceId => $sourceNode) {
            if (! isset($targetNodes[$sourceId])) {
                $structuralErrors[] = [
                    'code' => 'target-node-missing',
                    'sourceId' => $sourceId,
                    'message' => "Target measurement is missing source node '{$sourceId}'.",
                ];
                continue;
            }
            $targetNode = $targetNodes[$sourceId];
            foreach ($sourceEvidence['viewports'] as $viewport) {
                $viewportName = $viewport['name'];
                $sourceRect = $this->rect($sourceNode, $viewportName);
                $targetRect = $this->rect($targetNode, $viewportName);
                $delta = [
                    'sourceId' => $sourceId,
                    'viewport' => $viewportName,
                    'x' => $targetRect['x'] - $sourceRect['x'],
                    'y' => $targetRect['y'] - $sourceRect['y'],
                    'width' => $targetRect['width'] - $sourceRect['width'],
                    'height' => $targetRect['height'] - $sourceRect['height'],
                ];
                $delta['withinTolerance'] = max(abs($delta['x']), abs($delta['y']), abs($delta['width']), abs($delta['height'])) <= $this->pixelTolerance;
                $nodeDeltas[] = $delta;

                $sourceStyle = $this->style($sourceNode, $viewportName);
                $targetStyle = $this->style($targetNode, $viewportName);
                foreach ($this->boxModelProperties() as $property) {
                    $sourceValue = trim((string) ($sourceStyle[$property] ?? ''));
                    $targetValue = trim((string) ($targetStyle[$property] ?? ''));
                    if ($sourceValue === $targetValue) {
                        continue;
                    }
                    $numericSource = $this->cssNumber($sourceValue);
                    $numericTarget = $this->cssNumber($targetValue);
                    $boxModelDeltas[] = [
                        'sourceId' => $sourceId,
                        'viewport' => $viewportName,
                        'property' => $property,
                        'source' => $sourceValue,
                        'target' => $targetValue,
                        'delta' => $numericSource !== null && $numericTarget !== null ? $numericTarget - $numericSource : null,
                    ];
                }

                foreach ($this->layoutProperties() as $property) {
                    $sourceValue = trim((string) ($sourceStyle[$property] ?? ''));
                    $targetValue = trim((string) ($targetStyle[$property] ?? ''));
                    if ($sourceValue !== $targetValue) {
                        $responsiveMismatches[] = [
                            'code' => 'computed-style-mismatch',
                            'sourceId' => $sourceId,
                            'viewport' => $viewportName,
                            'property' => $property,
                            'source' => $sourceValue,
                            'target' => $targetValue,
                            'message' => "Computed {$property} differs from the source.",
                        ];
                    }
                }

                $sourceVisible = $this->isVisible($sourceNode, $viewportName);
                $targetVisible = $this->isVisible($targetNode, $viewportName);
                if ($sourceVisible !== $targetVisible) {
                    $responsiveMismatches[] = [
                        'code' => 'visibility-mismatch',
                        'sourceId' => $sourceId,
                        'viewport' => $viewportName,
                        'source' => $sourceVisible,
                        'target' => $targetVisible,
                        'message' => 'Source and target visibility differ at this viewport.',
                    ];
                }

                foreach ($this->unrepresentedProperties() as $property) {
                    $sourceValue = trim((string) ($sourceStyle[$property] ?? ''));
                    $targetValue = trim((string) ($targetStyle[$property] ?? ''));
                    if ($sourceValue !== $targetValue) {
                        $unrepresentedCss[] = [
                            'sourceId' => $sourceId,
                            'viewport' => $viewportName,
                            'property' => $property,
                            'source' => $sourceValue,
                            'target' => $targetValue,
                        ];
                    }
                }
            }
        }

        $this->compareDeclaredLayouts($sourceEvidence, $targetLayout, $sourceNodes, $structuralErrors);
        $this->deduplicate($structuralErrors, ['code', 'sourceId', 'viewport']);
        $this->deduplicate($responsiveMismatches, ['code', 'sourceId', 'viewport', 'property']);
        $this->deduplicate($unrepresentedCss, ['sourceId', 'viewport', 'property']);

        $hasGeometryMismatch = (bool) array_filter($nodeDeltas, static fn (array $delta): bool => ! ($delta['withinTolerance'] ?? false));
        $canApply = $structuralErrors === []
            && ! $hasGeometryMismatch
            && $boxModelDeltas === []
            && $responsiveMismatches === []
            && $unrepresentedCss === [];

        return [
            'pixelTolerance' => $this->pixelTolerance,
            'structuralErrors' => array_values($structuralErrors),
            'nodeDeltas' => $nodeDeltas,
            'boxModelDeltas' => $boxModelDeltas,
            'responsiveMismatches' => array_values($responsiveMismatches),
            'unrepresentedCss' => array_values($unrepresentedCss),
            'canApply' => $canApply,
        ];
    }

    /** @param array<string,mixed> $report */
    public function canApply(array $report): bool
    {
        return ($report['canApply'] ?? false) === true;
    }

    /** @param array<int,array<string,mixed>> $nodes @return array<string,array<string,mixed>> */
    private function indexNodes(array $nodes): array
    {
        $indexed = [];
        foreach ($nodes as $node) {
            if (is_array($node) && trim((string) ($node['sourceId'] ?? '')) !== '') {
                $indexed[(string) $node['sourceId']] = $node;
            }
        }

        return $indexed;
    }

    /** @param array<string,mixed> $node @return array<string,string> */
    private function style(array $node, string $viewport): array
    {
        $styles = is_array($node['computedStyleByViewport'] ?? null) ? $node['computedStyleByViewport'] : [];
        $style = $styles[$viewport] ?? ($node['computedStyle'] ?? []);

        return is_array($style) ? $style : [];
    }

    /** @param array<string,mixed> $node @return array{x:float,y:float,width:float,height:float} */
    private function rect(array $node, string $viewport): array
    {
        $rects = is_array($node['rectByViewport'] ?? null) ? $node['rectByViewport'] : [];
        $rect = is_array($rects[$viewport] ?? null) ? $rects[$viewport] : (is_array(reset($rects)) ? reset($rects) : []);

        return [
            'x' => (float) ($rect['x'] ?? 0),
            'y' => (float) ($rect['y'] ?? 0),
            'width' => (float) ($rect['width'] ?? 0),
            'height' => (float) ($rect['height'] ?? 0),
        ];
    }

    /** @param array<string,mixed> $node */
    private function isVisible(array $node, string $viewport): bool
    {
        $style = $this->style($node, $viewport);
        if (strtolower(trim((string) ($style['display'] ?? ''))) === 'none') {
            return false;
        }
        if (in_array(strtolower(trim((string) ($style['visibility'] ?? ''))), ['hidden', 'collapse'], true)) {
            return false;
        }
        $rect = $this->rect($node, $viewport);

        return $rect['width'] > 0 && $rect['height'] > 0;
    }

    /** @return array<int,string> */
    private function boxModelProperties(): array
    {
        return [
            'boxSizing', 'width', 'height',
            'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft',
            'marginTop', 'marginRight', 'marginBottom', 'marginLeft',
            'borderTopWidth', 'borderRightWidth', 'borderBottomWidth', 'borderLeftWidth',
        ];
    }

    /** @return array<int,string> */
    private function layoutProperties(): array
    {
        return ['display', 'gridTemplateColumns', 'gridTemplateRows', 'flexDirection', 'flexWrap', 'gap', 'rowGap', 'columnGap'];
    }

    /** @return array<int,string> */
    private function unrepresentedProperties(): array
    {
        return ['backgroundColor', 'backgroundImage', 'backgroundSize', 'backgroundPosition', 'borderRadius', 'boxShadow', 'objectFit', 'objectPosition', 'transform'];
    }

    private function cssNumber(string $value): ?float
    {
        if (preg_match('/^-?\d+(?:\.\d+)?(?:px)?$/i', $value) !== 1) {
            return null;
        }

        return (float) preg_replace('/px$/i', '', $value);
    }

    /** @param array<string,mixed> $sourceEvidence @param array<string,mixed> $targetLayout @param array<string,array<string,mixed>> $sourceNodes @param array<int,array<string,mixed>> $errors */
    private function compareDeclaredLayouts(array $sourceEvidence, array $targetLayout, array $sourceNodes, array &$errors): void
    {
        foreach (is_array($targetLayout['sections'] ?? null) ? $targetLayout['sections'] : [] as $section) {
            if (! is_array($section)) {
                continue;
            }
            $sectionId = (string) ($section['sourceId'] ?? $section['id'] ?? '');
            $sourceNode = $sourceNodes[$sectionId] ?? null;
            if (! is_array($sourceNode)) {
                continue;
            }
            $layouts = is_array($section['layoutByViewport'] ?? null) ? $section['layoutByViewport'] : [];
            foreach ($layouts as $viewport => $layout) {
                if (! is_array($layout)) {
                    continue;
                }
                $sourceStyle = $this->style($sourceNode, (string) $viewport);
                $sourceColumns = $this->sourceColumnCount($sourceNode, $sourceStyle, (string) $viewport);
                $targetColumns = isset($layout['columns']) && is_numeric($layout['columns']) ? (int) $layout['columns'] : null;
                if ($targetColumns !== null && $sourceColumns !== null && $targetColumns !== $sourceColumns) {
                    $errors[] = [
                        'code' => 'layout-columns-mismatch',
                        'sourceId' => $sectionId,
                        'viewport' => (string) $viewport,
                        'source' => $sourceColumns,
                        'target' => $targetColumns,
                        'message' => 'The target blueprint column count differs from the measured source.',
                    ];
                }
                $targetMode = strtolower(trim((string) ($layout['mode'] ?? '')));
                $sourceMode = $this->sourceMode($sourceStyle);
                if ($targetMode !== '' && $targetMode !== 'unclassified' && $sourceMode !== 'unclassified' && $targetMode !== $sourceMode) {
                    $errors[] = [
                        'code' => 'layout-mode-mismatch',
                        'sourceId' => $sectionId,
                        'viewport' => (string) $viewport,
                        'source' => $sourceMode,
                        'target' => $targetMode,
                        'message' => 'The target blueprint layout mode differs from the measured source.',
                    ];
                }
            }
        }
    }

    /** @param array<string,mixed> $node @param array<string,string> $style */
    private function sourceColumnCount(array $node, array $style, string $viewport): ?int
    {
        $display = strtolower(trim((string) ($style['display'] ?? 'block')));
        if ($display === 'grid') {
            $tracks = $this->expandTracks((string) ($style['gridTemplateColumns'] ?? ''));
            if ($tracks !== []) {
                return count($tracks);
            }
        }
        if ($display === 'flex' || $display === 'inline-flex') {
            $direction = strtolower(trim((string) ($style['flexDirection'] ?? 'row')));
            if (in_array($direction, ['column', 'column-reverse'], true)) {
                return 1;
            }
            $children = is_array($node['children'] ?? null) ? $node['children'] : [];
            return max(1, count($children));
        }

        return 1;
    }

    /** @param array<string,string> $style */
    private function sourceMode(array $style): string
    {
        $display = strtolower(trim((string) ($style['display'] ?? 'block')));
        if ($display === 'grid') {
            return 'grid';
        }
        if ($display === 'flex' || $display === 'inline-flex') {
            return 'flex';
        }

        return 'stack';
    }

    /** @return array<int,string> */
    private function expandTracks(string $value): array
    {
        $value = trim($value);
        if ($value === '' || in_array(strtolower($value), ['none', 'auto', 'normal'], true)) {
            return [];
        }

        $tracks = [];
        foreach ($this->splitTrackTokens($value) as $token) {
            $call = $this->functionCall($token);
            if ($call === null || strtolower($call['name']) !== 'repeat') {
                $tracks[] = $token;
                continue;
            }
            $arguments = $this->splitTopLevel($call['arguments'], ',');
            $count = isset($arguments[0]) && preg_match('/^\d+$/', trim($arguments[0])) === 1 ? (int) trim($arguments[0]) : 0;
            if ($count < 1 || $count > 100 || ! isset($arguments[1])) {
                continue;
            }
            $innerTracks = $this->expandTracks(trim($arguments[1])) ?: ['1fr'];
            for ($index = 0; $index < $count; $index++) {
                $tracks = [...$tracks, ...$innerTracks];
            }
        }

        return $tracks;
    }

    /** @return array<int,string> */
    private function splitTrackTokens(string $value): array
    {
        $tokens = [];
        $buffer = '';
        $depth = 0;
        foreach (str_split($value) as $character) {
            if ($character === '(') {
                $depth++;
            } elseif ($character === ')' && $depth > 0) {
                $depth--;
            }
            if ($depth === 0 && ctype_space($character)) {
                if (trim($buffer) !== '') {
                    $tokens[] = trim($buffer);
                    $buffer = '';
                }
                continue;
            }
            $buffer .= $character;
        }
        if (trim($buffer) !== '') {
            $tokens[] = trim($buffer);
        }

        return $tokens;
    }

    /** @return array<int,string> */
    private function splitTopLevel(string $value, string $separator): array
    {
        $parts = [];
        $buffer = '';
        $depth = 0;
        foreach (str_split($value) as $character) {
            if ($character === '(') {
                $depth++;
            } elseif ($character === ')' && $depth > 0) {
                $depth--;
            }
            if ($depth === 0 && $character === $separator) {
                $parts[] = trim($buffer);
                $buffer = '';
                continue;
            }
            $buffer .= $character;
        }
        $parts[] = trim($buffer);

        return array_values(array_filter($parts, static fn (string $part): bool => $part !== ''));
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

        return ['name' => $name, 'arguments' => substr($value, $open + 1, -1)];
    }

    /** @param array<int,array<string,mixed>> $items @param array<int,string> $keys */
    private function deduplicate(array &$items, array $keys): void
    {
        $seen = [];
        $items = array_values(array_filter($items, static function (array $item) use (&$seen, $keys): bool {
            $key = implode('|', array_map(static fn (string $field): string => (string) ($item[$field] ?? ''), $keys));
            if (isset($seen[$key])) {
                return false;
            }
            $seen[$key] = true;
            return true;
        }));
    }
}
