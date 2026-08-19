<?php

namespace App\Support\PageBuilderElementorV23;

final class FormDatasetNormalizer
{
    private const SCHEMA_VERSION = 1;

    public function normalize(array $payload): array
    {
        $nodes = is_array($payload['nodes'] ?? null) ? $payload['nodes'] : [];

        return [
            'schemaVersion' => max(1, (int) ($payload['schemaVersion'] ?? self::SCHEMA_VERSION)),
            'id' => trim((string) ($payload['id'] ?? '')),
            'name' => trim((string) ($payload['name'] ?? 'Dataset')),
            'levels' => is_array($payload['levels'] ?? null) ? array_values($payload['levels']) : [],
            'nodes' => array_values(array_map(function ($node, $index): array {
                $node = is_array($node) ? $node : [];
                $id = trim((string) ($node['id'] ?? 'node-'.($index + 1)));
                $label = trim((string) ($node['label'] ?? $node['name'] ?? $node['value'] ?? 'Item '.($index + 1)));
                $value = trim((string) ($node['value'] ?? $node['code'] ?? $id ?: $label));

                return [
                    'id' => $id,
                    'parentId' => ($node['parentId'] ?? null) === null || ($node['parentId'] ?? '') === ''
                        ? null
                        : trim((string) $node['parentId']),
                    'label' => $label,
                    'name' => trim((string) ($node['name'] ?? $label)),
                    'code' => trim((string) ($node['code'] ?? $value)),
                    'value' => $value,
                    'kind' => trim((string) ($node['kind'] ?? 'item')) ?: 'item',
                    'sortOrder' => is_numeric($node['sortOrder'] ?? null) ? (int) $node['sortOrder'] : $index,
                    'active' => array_key_exists('active', $node) ? (bool) $node['active'] : true,
                    ...((is_array($node['meta'] ?? null)) ? ['meta' => $node['meta']] : []),
                ];
            }, $nodes, array_keys($nodes))),
        ];
    }

    public function validate(array $payload): array
    {
        $dataset = $this->normalize($payload);
        $errors = [];
        $byId = [];

        foreach ($dataset['nodes'] as $index => $node) {
            if ($node['id'] === '') {
                $errors[] = [
                    'code' => 'missing-id',
                    'path' => "nodes.{$index}.id",
                    'message' => 'Node id is required.',
                ];
                continue;
            }

            if (isset($byId[$node['id']])) {
                $errors[] = [
                    'code' => 'duplicate-id',
                    'path' => "nodes.{$index}.id",
                    'message' => "Duplicate node id: {$node['id']}.",
                ];
            }

            $byId[$node['id']] = $node;
        }

        foreach ($dataset['nodes'] as $index => $node) {
            if ($node['parentId'] !== null && ! isset($byId[$node['parentId']])) {
                $errors[] = [
                    'code' => 'missing-parent',
                    'path' => "nodes.{$index}.parentId",
                    'message' => "Parent not found: {$node['parentId']}.",
                ];
            }
        }

        $visited = [];
        $visiting = [];
        $visit = function (string $id) use (&$visit, &$byId, &$visited, &$visiting, &$errors): void {
            if (! isset($byId[$id]) || isset($visited[$id])) {
                return;
            }

            if (isset($visiting[$id])) {
                $errors[] = [
                    'code' => 'cycle',
                    'path' => "nodes.{$id}",
                    'message' => "Circular parent reference at {$id}.",
                ];
                return;
            }

            $visiting[$id] = true;
            $parentId = $byId[$id]['parentId'];
            if ($parentId !== null) {
                $visit((string) $parentId);
            }
            unset($visiting[$id]);
            $visited[$id] = true;
        };

        foreach (array_keys($byId) as $id) {
            $visit((string) $id);
        }

        $depthMemo = [];
        $depth = function (string $id, array $trail = []) use (&$depth, &$depthMemo, &$byId): int {
            if (! isset($byId[$id])) {
                return 1;
            }
            if (isset($depthMemo[$id])) {
                return $depthMemo[$id];
            }
            if (isset($trail[$id])) {
                return 1;
            }

            $trail[$id] = true;
            $parentId = $byId[$id]['parentId'];
            $value = $parentId === null ? 1 : 1 + $depth((string) $parentId, $trail);
            return $depthMemo[$id] = $value;
        };

        $rootCount = count(array_filter($dataset['nodes'], fn (array $node): bool => $node['parentId'] === null));
        $maxDepth = 0;
        foreach (array_keys($byId) as $id) {
            $maxDepth = max($maxDepth, $depth((string) $id));
        }

        return [
            'valid' => $errors === [],
            'errors' => $errors,
            'dataset' => $dataset,
            'stats' => [
                'nodeCount' => count($dataset['nodes']),
                'rootCount' => $rootCount,
                'maxDepth' => $maxDepth,
            ],
        ];
    }
}
