<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

use InvalidArgumentException;

final class AutomaticCompiledNativeResponsiveClassifier
{
    public function __construct(
        private readonly AutomaticCompiledNativeLayoutClassifier $layoutClassifier = new AutomaticCompiledNativeLayoutClassifier,
    ) {
    }

    /**
     * @param array<string,array<string,mixed>> $snapshots
     * @return array<string,mixed>
     */
    public function classify(array $snapshots): array
    {
        if ($snapshots === []) {
            throw new InvalidArgumentException('At least one viewport snapshot is required.');
        }

        $viewports = [];
        $diagnostics = [];
        foreach ($this->orderedSnapshotNames($snapshots) as $snapshotName) {
            $evidence = AutomaticCompiledNativeEvidence::fromSnapshot($snapshots[$snapshotName]);
            $viewport = $evidence['viewports'][0] ?? null;
            if (! is_array($viewport)) {
                throw new InvalidArgumentException("Snapshot '{$snapshotName}' has no viewport.");
            }
            $viewportName = $viewport['name'];
            $root = $evidence['nodes'][0] ?? null;
            if (! is_array($root)) {
                $diagnostics[] = [
                    'code' => 'responsive-root-missing',
                    'viewport' => $viewportName,
                    'message' => 'The viewport snapshot contains no measurable root node.',
                ];
                continue;
            }
            $section = new SectionIndex([[
                'id' => $root['sourceId'],
                'sourceId' => $root['sourceId'],
                'kind' => 'section',
                'sourceSelector' => '[data-pb-source-id="'.$root['sourceId'].'"]',
                'boundaryConfidence' => 1.0,
                'nodeIds' => array_values(array_map(static fn (array $node): string => $node['sourceId'], $evidence['nodes'])),
                'diagnostics' => [],
            ]]);
            $classified = $this->layoutClassifier->classify($section, $evidence);
            $layout = $classified['sections'][0]['layoutByViewport'][$viewportName] ?? null;
            if (! is_array($layout)) {
                $diagnostics[] = [
                    'code' => 'responsive-layout-missing',
                    'viewport' => $viewportName,
                    'message' => 'No layout classification was produced for the viewport.',
                ];
                continue;
            }
            $viewports[$viewportName] = [
                'width' => $viewport['width'],
                'height' => $viewport['height'],
                ...$layout,
            ];
            $diagnostics = [...$diagnostics, ...($classified['diagnostics'] ?? [])];
        }

        $names = array_keys($viewports);
        $transitions = [];
        for ($index = 1; $index < count($names); $index++) {
            $previousName = $names[$index - 1];
            $currentName = $names[$index];
            $previous = $viewports[$previousName];
            $current = $viewports[$currentName];
            if (($previous['columns'] ?? null) !== ($current['columns'] ?? null) || ($previous['mode'] ?? null) !== ($current['mode'] ?? null)) {
                $transitions[] = sprintf('%s:%s→%s:%s', $previousName, (string) ($previous['columns'] ?? '?'), $currentName, (string) ($current['columns'] ?? '?'));
            }
        }

        return [
            'version' => 1,
            'viewports' => $viewports,
            'transitions' => $transitions,
            'diagnostics' => $diagnostics,
        ];
    }

    /** @param array<string,array<string,mixed>> $snapshots @return array<int,string> */
    private function orderedSnapshotNames(array $snapshots): array
    {
        $priority = ['desktop' => 0, 'tablet' => 1, 'mobile' => 2];
        $names = array_keys($snapshots);
        usort($names, static fn (string $left, string $right): int => ($priority[strtolower($left)] ?? 3) <=> ($priority[strtolower($right)] ?? 3));

        return $names;
    }
}
