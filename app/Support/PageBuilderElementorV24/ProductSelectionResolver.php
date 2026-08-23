<?php

namespace App\Support\PageBuilderElementorV24;

use App\Models\Page_Builder\Page_Builder;
use App\Models\PageBuilderElementorV24\FormDataset;
use Illuminate\Validation\ValidationException;

final class ProductSelectionResolver
{
    private const LEVEL_DEFAULTS = [
        ['key' => 'model', 'label' => 'Model', 'fieldId' => 'product_model'],
        ['key' => 'type', 'label' => 'Type', 'fieldId' => 'product_type'],
        ['key' => 'variant', 'label' => 'Variant', 'fieldId' => 'product_variant'],
    ];

    public function resolve(Page_Builder $page, array $settings, array $values, array $reservedFieldIds = []): array
    {
        $levelCount = max(1, min(3, (int) ($settings['productLevelCount'] ?? 3)));
        $levels = $this->levels($settings['productLevels'] ?? [], $levelCount);
        $reserved = array_fill_keys(array_map('strval', $reservedFieldIds), true);

        foreach ($levels as $level) {
            if (isset($reserved[$level['fieldId']])) {
                throw ValidationException::withMessages([
                    $level['fieldId'] => 'Product selection field IDs must not duplicate Form field IDs.',
                ]);
            }
        }

        $datasetId = (int) ($settings['productData']['datasetId'] ?? 0);
        $dataset = $datasetId > 0
            ? FormDataset::query()->where('user_id', (int) ($page->user_id ?? 1))->find($datasetId)
            : null;
        if (! $dataset) {
            throw ValidationException::withMessages([
                'productData.datasetId' => 'A valid Product Lead dataset is required.',
            ]);
        }

        $records = $this->records(is_array($dataset->nodes) ? $dataset->nodes : [], $levelCount);
        $fields = [];
        $definitions = [];
        $selection = [];
        $parentId = null;

        foreach ($levels as $index => $level) {
            $fieldId = $level['fieldId'];
            $rawValue = $values[$fieldId] ?? '';
            if (is_array($rawValue) || is_object($rawValue)) {
                throw ValidationException::withMessages([$fieldId => "The {$level['label']} selection is invalid."]);
            }
            $value = trim((string) $rawValue);
            $definitions[$fieldId] = [
                'id' => $fieldId,
                'label' => $level['label'],
                'type' => 'text',
                'required' => $level['required'],
            ];
            $fields[$fieldId] = $value;

            if ($value === '') {
                if ($level['required']) {
                    throw ValidationException::withMessages([$fieldId => "The {$level['label']} selection is required."]);
                }
                $parentId = null;
                continue;
            }

            $matches = array_values(array_filter(
                $records,
                static fn (array $record): bool => $record['depth'] === $index
                    && $record['parentId'] === $parentId
                    && hash_equals($record['value'], $value),
            ));
            if (count($matches) !== 1) {
                throw ValidationException::withMessages([$fieldId => "The selected {$level['label']} is unavailable."]);
            }

            $record = $matches[0];
            $fields[$fieldId] = $record['value'];
            $selection[] = [
                'level' => $level['key'],
                'fieldId' => $fieldId,
                'id' => $record['id'],
                'label' => $record['label'],
                'code' => $record['code'],
                'value' => $record['value'],
            ];
            $parentId = $record['id'];
        }

        return [
            'fields' => $fields,
            'definitions' => $definitions,
            'meta' => $selection,
        ];
    }

    private function levels(mixed $source, int $levelCount): array
    {
        $input = is_array($source) ? array_values($source) : [];
        $levels = [];
        $used = [];

        foreach (array_slice(self::LEVEL_DEFAULTS, 0, $levelCount) as $index => $fallback) {
            $raw = is_array($input[$index] ?? null) ? $input[$index] : [];
            $fieldId = trim((string) ($raw['fieldId'] ?? $fallback['fieldId']));
            if (! preg_match('/^[A-Za-z][A-Za-z0-9_-]{0,63}$/', $fieldId) || isset($used[$fieldId])) {
                $fieldId = $fallback['fieldId'];
            }
            $used[$fieldId] = true;
            $levels[] = [
                'key' => $fallback['key'],
                'label' => trim((string) ($raw['label'] ?? '')) ?: $fallback['label'],
                'fieldId' => $fieldId,
                'required' => ($raw['required'] ?? true) !== false,
            ];
        }

        return $levels;
    }

    private function records(array $nodes, int $levelCount): array
    {
        $active = array_values(array_filter($nodes, static fn ($node): bool => is_array($node) && ($node['active'] ?? true) !== false));
        $byId = [];
        foreach ($active as $node) {
            $id = trim((string) ($node['id'] ?? ''));
            if ($id !== '') {
                $byId[$id] = $node;
            }
        }

        $memo = [];
        $depth = function (string $id, array $trail = []) use (&$depth, &$memo, $byId): ?int {
            if (array_key_exists($id, $memo)) {
                return $memo[$id];
            }
            if (! isset($byId[$id]) || isset($trail[$id])) {
                return null;
            }
            $parentId = trim((string) ($byId[$id]['parentId'] ?? ''));
            if ($parentId === '') {
                return $memo[$id] = 0;
            }
            if (! isset($byId[$parentId])) {
                return $memo[$id] = null;
            }
            $trail[$id] = true;
            $parentDepth = $depth($parentId, $trail);

            return $memo[$id] = $parentDepth === null ? null : $parentDepth + 1;
        };

        $records = [];
        foreach ($active as $index => $node) {
            $id = trim((string) ($node['id'] ?? ''));
            $nodeDepth = $id === '' ? null : $depth($id);
            if ($nodeDepth === null || $nodeDepth >= $levelCount) {
                continue;
            }
            $value = trim((string) ($node['value'] ?? $node['code'] ?? $id));
            if ($value === '') {
                continue;
            }
            $records[] = [
                'id' => $id,
                'parentId' => ($node['parentId'] ?? null) === null || trim((string) ($node['parentId'] ?? '')) === ''
                    ? null
                    : trim((string) $node['parentId']),
                'depth' => $nodeDepth,
                'label' => trim((string) ($node['label'] ?? $node['name'] ?? $value)) ?: $value,
                'code' => trim((string) ($node['code'] ?? $value)),
                'value' => $value,
                'sortOrder' => is_numeric($node['sortOrder'] ?? null) ? (int) $node['sortOrder'] : $index,
            ];
        }

        usort($records, static fn (array $left, array $right): int => [$left['depth'], $left['sortOrder'], $left['id']] <=> [$right['depth'], $right['sortOrder'], $right['id']]);

        return $records;
    }
}
