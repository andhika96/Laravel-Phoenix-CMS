<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

final class AutomaticCompiledNativeBlueprint
{
    /** @return array<string,mixed> */
    public static function normalize(array $blueprint): array
    {
        $viewports = [];
        foreach (is_array($blueprint['viewports'] ?? null) ? $blueprint['viewports'] : [] as $viewport) {
            if (! is_array($viewport) || ! is_string($viewport['name'] ?? null)) {
                continue;
            }
            $viewports[] = [
                'name' => strtolower(trim($viewport['name'])),
                'width' => self::number($viewport['width'] ?? 0),
                'height' => self::number($viewport['height'] ?? 0),
            ];
        }
        $viewports = self::stableSort($viewports, ['desktop', 'tablet', 'mobile']);

        $sections = [];
        foreach (is_array($blueprint['sections'] ?? null) ? $blueprint['sections'] : [] as $sectionIndex => $section) {
            if (! is_array($section)) {
                continue;
            }

            $nodes = [];
            foreach (is_array($section['nodes'] ?? null) ? $section['nodes'] : [] as $nodeIndex => $node) {
                if (! is_array($node)) {
                    continue;
                }
                $nodes[] = [
                    ...$node,
                    'sourceId' => (string) ($node['sourceId'] ?? ''),
                    'order' => (int) ($node['order'] ?? $nodeIndex),
                ];
            }
            $nodes = self::stableOrder($nodes);

            $layoutByViewport = [];
            foreach (is_array($section['layoutByViewport'] ?? null) ? $section['layoutByViewport'] : [] as $viewport => $layout) {
                if (! is_array($layout)) {
                    continue;
                }
                $normalizedLayout = [
                    ...$layout,
                    'mode' => strtolower((string) ($layout['mode'] ?? 'unclassified')),
                    'columns' => isset($layout['columns']) && is_numeric($layout['columns']) ? (int) $layout['columns'] : null,
                    'tracks' => array_values(array_map(static fn (mixed $track): string => (string) $track, is_array($layout['tracks'] ?? null) ? $layout['tracks'] : [])),
                    'evidence' => is_array($layout['evidence'] ?? null) ? $layout['evidence'] : [],
                ];
                $layoutByViewport[(string) $viewport] = $normalizedLayout;
            }

            $sections[] = [
                ...$section,
                'id' => (string) ($section['id'] ?? 'section-'.($sectionIndex + 1)),
                'order' => (int) ($section['order'] ?? $sectionIndex),
                'kind' => (string) ($section['kind'] ?? 'section'),
                'sourceSelector' => (string) ($section['sourceSelector'] ?? ''),
                'boundaryConfidence' => self::confidence($section['boundaryConfidence'] ?? 0),
                'layoutByViewport' => $layoutByViewport,
                'nodes' => $nodes,
                'diagnostics' => is_array($section['diagnostics'] ?? null) ? array_values($section['diagnostics']) : [],
            ];
        }
        $sections = self::stableOrder($sections);

        return [
            ...$blueprint,
            'version' => 1,
            'viewports' => $viewports,
            'sections' => $sections,
        ];
    }

    /** @return array{valid:bool,errors:array<int,array{code:string,path:string,message:string}>} */
    public static function validate(array $blueprint): array
    {
        $errors = [];
        if (($blueprint['version'] ?? null) !== 1) {
            self::error($errors, 'version-invalid', 'version', 'The blueprint version must be 1.');
        }

        $viewports = is_array($blueprint['viewports'] ?? null) ? $blueprint['viewports'] : [];
        $viewportNames = [];
        foreach ($viewports as $index => $viewport) {
            if (! is_array($viewport) || ! is_string($viewport['name'] ?? null) || trim($viewport['name']) === '') {
                self::error($errors, 'viewport-invalid', "viewports.{$index}", 'Each viewport must have a name.');
                continue;
            }
            $viewportNames[] = trim($viewport['name']);
        }
        if ($viewports === []) {
            self::error($errors, 'viewports-missing', 'viewports', 'At least one viewport is required.');
        }

        $sections = is_array($blueprint['sections'] ?? null) ? $blueprint['sections'] : [];
        if ($sections === []) {
            self::error($errors, 'sections-missing', 'sections', 'At least one section is required.');
        }

        $sectionIds = [];
        foreach ($sections as $sectionIndex => $section) {
            $path = "sections.{$sectionIndex}";
            if (! is_array($section)) {
                self::error($errors, 'section-invalid', $path, 'Each section must be an object.');
                continue;
            }

            $sectionId = trim((string) ($section['id'] ?? ''));
            if ($sectionId === '') {
                self::error($errors, 'section-id-missing', "{$path}.id", 'Each section requires an id.');
            } elseif (isset($sectionIds[$sectionId])) {
                self::error($errors, 'section-id-duplicate', "{$path}.id", "Section id '{$sectionId}' is duplicated.");
            } else {
                $sectionIds[$sectionId] = true;
            }

            $layouts = is_array($section['layoutByViewport'] ?? null) ? $section['layoutByViewport'] : [];
            foreach ($layouts as $viewport => $layout) {
                $layoutPath = "{$path}.layoutByViewport.{$viewport}";
                if (! in_array((string) $viewport, $viewportNames, true)) {
                    self::error($errors, 'layout-viewport-unknown', $layoutPath, 'Layout references an unknown viewport.');
                }
                if (! is_array($layout)) {
                    self::error($errors, 'layout-invalid', $layoutPath, 'Each layout must be an object.');
                    continue;
                }

                $mode = strtolower(trim((string) ($layout['mode'] ?? '')));
                if (! in_array($mode, ['stack', 'flex', 'grid', 'unclassified'], true)) {
                    self::error($errors, 'layout-mode-invalid', "{$layoutPath}.mode", 'Layout mode must be stack, flex, grid, or unclassified.');
                }

                $hasColumns = array_key_exists('columns', $layout) && $layout['columns'] !== null;
                $columns = $hasColumns && is_numeric($layout['columns']) ? (int) $layout['columns'] : null;
                if ($hasColumns && ($columns === null || $columns < 1)) {
                    self::error($errors, 'layout-columns-invalid', "{$layoutPath}.columns", 'Column count must be a positive integer.');
                }
                if ($mode !== 'unclassified' && ! $hasColumns) {
                    self::error($errors, 'layout-columns-missing', "{$layoutPath}.columns", 'A classified layout must record its column count.');
                }

                $tracks = is_array($layout['tracks'] ?? null) ? $layout['tracks'] : [];
                if ($columns !== null && $tracks !== [] && count($tracks) !== $columns) {
                    self::error($errors, 'layout-track-count-mismatch', "{$layoutPath}.tracks", 'Track count must match the recorded column count.');
                }
                if ($hasColumns && (! is_array($layout['evidence'] ?? null) || $layout['evidence'] === [])) {
                    self::error($errors, 'layout-evidence-missing', "{$layoutPath}.evidence", 'Every automatic column count must include decision evidence.');
                }
            }

            $nodeIds = [];
            foreach (is_array($section['nodes'] ?? null) ? $section['nodes'] : [] as $nodeIndex => $node) {
                $nodePath = "{$path}.nodes.{$nodeIndex}";
                if (! is_array($node)) {
                    self::error($errors, 'node-invalid', $nodePath, 'Each source node must be an object.');
                    continue;
                }
                $sourceId = trim((string) ($node['sourceId'] ?? ''));
                if ($sourceId === '') {
                    self::error($errors, 'node-source-id-missing', "{$nodePath}.sourceId", 'Each source node requires a sourceId.');
                } elseif (isset($nodeIds[$sourceId])) {
                    self::error($errors, 'node-source-id-duplicate', "{$nodePath}.sourceId", "Source node id '{$sourceId}' is duplicated within the section.");
                } else {
                    $nodeIds[$sourceId] = true;
                }
            }
        }

        return ['valid' => $errors === [], 'errors' => $errors];
    }

    /** @param array<int,array{code:string,path:string,message:string}> $errors */
    private static function error(array &$errors, string $code, string $path, string $message): void
    {
        $errors[] = compact('code', 'path', 'message');
    }

    /** @param array<int,array<string,mixed>> $items */
    private static function stableOrder(array $items): array
    {
        foreach ($items as $index => &$item) {
            $item['_stableIndex'] = $index;
        }
        unset($item);
        usort($items, static fn (array $left, array $right): int => ((int) ($left['order'] ?? 0) <=> (int) ($right['order'] ?? 0)) ?: ((int) ($left['_stableIndex'] ?? 0) <=> (int) ($right['_stableIndex'] ?? 0)));
        foreach ($items as &$item) {
            unset($item['_stableIndex']);
        }
        unset($item);

        return array_values($items);
    }

    /** @param array<int,array{name:string,width:float,height:float}> $items */
    private static function stableSort(array $items, array $priorityNames): array
    {
        $priority = array_flip($priorityNames);
        foreach ($items as $index => &$item) {
            $item['_stableIndex'] = $index;
        }
        unset($item);
        usort($items, static fn (array $left, array $right): int => (($priority[$left['name']] ?? count($priority)) <=> ($priority[$right['name']] ?? count($priority))) ?: ((int) $left['_stableIndex'] <=> (int) $right['_stableIndex']));
        foreach ($items as &$item) {
            unset($item['_stableIndex']);
        }
        unset($item);

        return array_values($items);
    }

    private static function number(mixed $value): float|int
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    private static function confidence(mixed $value): float
    {
        return max(0.0, min(1.0, self::number($value)));
    }
}
