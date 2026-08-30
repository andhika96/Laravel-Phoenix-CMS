<?php

namespace App\Support\PageBuilderElementorV24\StaticImport;

use App\Support\PageBuilderElementorV24\ModuleCatalog;

final class StaticImportMappingValidator
{
    private const MAPPING_KEYS = ['version', 'regions'];

    private const REGION_KEYS = ['regionId', 'strategy', 'blocks'];

    private const BLOCK_KEYS = ['blockId', 'widgetType'];

    private const STRATEGIES = ['auto_native', 'guided_native', 'exact_visual', 'skip'];

    public function __construct(
        ?StaticImportWidgetCompatibility $compatibility = null,
        ?ModuleCatalog $catalog = null,
    ) {
        $this->catalog = $catalog ?? new ModuleCatalog();
        $this->compatibility = $compatibility ?? new StaticImportWidgetCompatibility($this->catalog);
    }

    private readonly ModuleCatalog $catalog;

    private readonly StaticImportWidgetCompatibility $compatibility;

    /** @return array<string, mixed> */
    public function validate(
        array $analysis,
        array $mapping,
        string $actualSourceHash,
        string $submittedSourceHash,
    ): array {
        $errors = [];

        if (! hash_equals($actualSourceHash, $submittedSourceHash)) {
            $errors[] = $this->error('source-hash-mismatch');
        }

        if (($mapping['version'] ?? null) !== 1) {
            $errors[] = $this->error('mapping-version-unsupported');
        }

        $extraMappingKeys = array_diff(array_keys($mapping), self::MAPPING_KEYS);
        if ($extraMappingKeys !== []) {
            $errors[] = $this->error('mapping-extra-property');
        }

        $regions = $mapping['regions'] ?? null;
        if (! is_array($regions) || ! array_is_list($regions)) {
            $errors[] = $this->error('mapping-regions-invalid');

            return $this->result($errors);
        }

        $analysisRegions = $this->indexRegions($analysis);
        $normalizedRegions = [];
        $seenRegions = [];
        $seenBlocks = [];

        foreach ($regions as $regionMapping) {
            if (! is_array($regionMapping)) {
                $errors[] = $this->error('mapping-region-invalid');
                continue;
            }

            $regionId = $this->safeRegionId($regionMapping['regionId'] ?? null);
            $diagnosticRegionId = $regionId;
            $extraRegionKeys = array_diff(array_keys($regionMapping), self::REGION_KEYS);
            if ($extraRegionKeys !== []) {
                $errors[] = $this->error('mapping-extra-property', $diagnosticRegionId);
            }

            if ($regionId === null || ! isset($analysisRegions[$regionId])) {
                $errors[] = $this->error('unknown-region', $diagnosticRegionId);
                continue;
            }

            if (isset($seenRegions[$regionId])) {
                $errors[] = $this->error('duplicate-region-mapping', $regionId);
                continue;
            }
            $seenRegions[$regionId] = true;

            $strategy = $regionMapping['strategy'] ?? null;
            if (! is_string($strategy) || ! in_array($strategy, self::STRATEGIES, true)) {
                $errors[] = $this->error('invalid-strategy', $regionId);
                continue;
            }

            $blocks = $regionMapping['blocks'] ?? [];
            if (! is_array($blocks) || ! array_is_list($blocks)) {
                $errors[] = $this->error('mapping-blocks-invalid', $regionId);
                continue;
            }

            $region = $analysisRegions[$regionId];
            $indexedBlocks = $this->indexBlocks($region['blocks'] ?? [], $regionId);
            $providedBlocks = $this->indexProvidedBlocks(
                $blocks,
                $regionId,
                $indexedBlocks,
                $analysisRegions,
                $seenBlocks,
                $errors,
                $strategy === 'auto_native',
            );

            if (in_array($strategy, ['skip', 'exact_visual'], true)) {
                if ($blocks !== []) {
                    $errors[] = $this->error('strategy-blocks-not-allowed', $regionId);
                }
                $normalizedRegions[] = [
                    'regionId' => $regionId,
                    'strategy' => $strategy,
                    'blocks' => [],
                ];
                continue;
            }

            $normalizedBlocks = [];
            foreach ($indexedBlocks as $blockId => $block) {
                $widgetType = $strategy === 'auto_native'
                    ? $this->recommendedType($block)
                    : ($providedBlocks[$blockId] ?? null);

                if ($widgetType === null) {
                    $errors[] = $this->error('unresolved-block', $regionId, $blockId);
                    continue;
                }

                if (! $this->catalog->active($widgetType)) {
                    $errors[] = $this->error('inactive-widget', $regionId, $blockId);
                    continue;
                }

                $role = is_string($block['role'] ?? null) ? $block['role'] : 'unknown';
                if (! $this->compatibility->isCompatible($role, $widgetType)) {
                    $errors[] = $this->error('incompatible-widget', $regionId, $blockId);
                    continue;
                }

                $normalizedBlocks[] = [
                    'blockId' => $blockId,
                    'widgetType' => $widgetType,
                ];
            }

            $normalizedRegions[] = [
                'regionId' => $regionId,
                'strategy' => $strategy,
                'blocks' => $normalizedBlocks,
            ];
        }

        if ($errors !== []) return $this->result($errors);

        $normalized = [
            'version' => 1,
            'regions' => $normalizedRegions,
        ];

        return [
            'valid' => true,
            'mapping' => $normalized,
            'errors' => [],
            'mappingReport' => $this->mappingReport($normalized, $analysis),
        ];
    }

    /** @param array<int, array<string, mixed>> $analysis @return array<string, array<string, mixed>> */
    private function indexRegions(array $analysis): array
    {
        $indexed = [];
        foreach ($analysis as $region) {
            if (! is_array($region)) continue;
            $regionId = $this->safeRegionId($region['id'] ?? null);
            if ($regionId !== null) $indexed[$regionId] = $region;
        }
        return $indexed;
    }

    /** @param array<int, mixed> $blocks @return array<string, array<string, mixed>> */
    private function indexBlocks(array $blocks, string $regionId): array
    {
        $indexed = [];
        foreach ($blocks as $block) {
            if (! is_array($block)) continue;
            $blockId = $this->safeBlockId($block['id'] ?? null);
            if ($blockId === null) continue;
            $indexed[$blockId] = $block;
            $children = $block['children'] ?? [];
            if (is_array($children)) {
                foreach ($this->indexBlocks($children, $regionId) as $childId => $child) {
                    $indexed[$childId] = $child;
                }
            }
        }
        return $indexed;
    }

    /** @param array<int, mixed> $blocks @param array<string, array<string, mixed>> $indexedBlocks @param array<string, array<string, mixed>> $analysisRegions @param array<string, bool> $seenBlocks @param array<int, array<string, string>> $errors @return array<string, string> */
    private function indexProvidedBlocks(
        array $blocks,
        string $regionId,
        array $indexedBlocks,
        array $analysisRegions,
        array &$seenBlocks,
        array &$errors,
        bool $ignoreWidgetValidation,
    ): array {
        $provided = [];
        foreach ($blocks as $blockMapping) {
            if (! is_array($blockMapping)) {
                $errors[] = $this->error('mapping-block-invalid', $regionId);
                continue;
            }

            $blockId = $this->safeBlockId($blockMapping['blockId'] ?? null);
            $extraBlockKeys = array_diff(array_keys($blockMapping), self::BLOCK_KEYS);
            if ($extraBlockKeys !== []) {
                $errors[] = $this->error('mapping-extra-property', $regionId, $blockId);
            }

            if ($blockId === null) {
                $errors[] = $this->error('unknown-block', $regionId);
                continue;
            }

            if (isset($seenBlocks[$blockId])) {
                $errors[] = $this->error('duplicate-block-mapping', $regionId, $blockId);
                continue;
            }
            $seenBlocks[$blockId] = true;

            if (! isset($indexedBlocks[$blockId])) {
                $owner = $this->findBlockOwner($analysisRegions, $blockId);
                $errors[] = $owner !== null
                    ? $this->error('block-region-mismatch', $regionId, $blockId)
                    : $this->error('unknown-block', $regionId, $blockId);
                continue;
            }

            $widgetType = $blockMapping['widgetType'] ?? null;
            if (! is_string($widgetType) || preg_match('/^[a-z][a-z0-9_]*$/', $widgetType) !== 1) {
                $errors[] = $this->error('invalid-widget', $regionId, $blockId);
                continue;
            }

            if (! $ignoreWidgetValidation) {
                $provided[$blockId] = $widgetType;
            }
        }

        return $provided;
    }

    /** @param array<string, array<string, mixed>> $analysisRegions */
    private function findBlockOwner(array $analysisRegions, string $blockId): ?string
    {
        foreach ($analysisRegions as $regionId => $region) {
            if (isset($this->indexBlocks($region['blocks'] ?? [], $regionId)[$blockId])) return $regionId;
        }
        return null;
    }

    /** @param array<string, mixed> $block */
    private function recommendedType(array $block): ?string
    {
        $recommended = $block['recommendedWidget'] ?? null;
        return is_string($recommended) && $recommended !== '' ? $recommended : null;
    }

    /** @param array<string, mixed> $block */
    private function safeBlockId(mixed $blockId): ?string
    {
        return is_string($blockId) && preg_match('/^block-[A-Za-z0-9_-]+$/', $blockId) === 1 ? $blockId : null;
    }

    private function safeRegionId(mixed $regionId): ?string
    {
        return is_string($regionId) && preg_match('/^region-[A-Za-z0-9_-]+$/', $regionId) === 1 ? $regionId : null;
    }

    /** @return array<string, string> */
    private function error(string $code, ?string $regionId = null, ?string $blockId = null): array
    {
        $error = ['code' => $code];
        if ($regionId !== null) $error['regionId'] = $regionId;
        if ($blockId !== null) $error['blockId'] = $blockId;
        return $error;
    }

    /** @param array<int, array<string, string>> $errors @return array<string, mixed> */
    private function result(array $errors): array
    {
        return [
            'valid' => false,
            'mapping' => null,
            'errors' => array_values($errors),
            'mappingReport' => null,
        ];
    }

    /** @param array<string, mixed> $mapping @param array<int, array<string, mixed>> $analysis @return array<string, int> */
    private function mappingReport(array $mapping, array $analysis): array
    {
        $regions = $mapping['regions'] ?? [];
        $native = 0;
        $exact = 0;
        $skipped = 0;
        $mappedBlocks = 0;
        foreach (is_array($regions) ? $regions : [] as $region) {
            if (! is_array($region)) continue;
            match ($region['strategy'] ?? null) {
                'exact_visual' => $exact++,
                'skip' => $skipped++,
                'auto_native', 'guided_native' => $native++,
                default => null,
            };
            $mappedBlocks += is_array($region['blocks'] ?? null) ? count($region['blocks']) : 0;
        }

        return [
            'regions' => count(is_array($regions) ? $regions : []),
            'nativeRegions' => $native,
            'exactRegions' => $exact,
            'skippedRegions' => $skipped,
            'mappedBlocks' => $mappedBlocks,
            'unmappedBlocks' => 0,
        ];
    }
}
