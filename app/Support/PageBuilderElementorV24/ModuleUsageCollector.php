<?php

namespace App\Support\PageBuilderElementorV24;

final class ModuleUsageCollector
{
    public function types(array $nodes): array
    {
        $seen = [];
        $ordered = [];

        $walk = function (mixed $value) use (&$walk, &$seen, &$ordered): void {
            if (! is_array($value)) {
                return;
            }

            if (array_is_list($value)) {
                foreach ($value as $entry) {
                    $walk($entry);
                }

                return;
            }

            $type = trim((string) ($value['type'] ?? ''));
            if ($type !== '' && ! isset($seen[$type])) {
                $seen[$type] = true;
                $ordered[] = $type;
            }

            $walk($value['children'] ?? null);

            foreach ((array) ($value['columns'] ?? []) as $column) {
                if (is_array($column)) {
                    $walk($column['children'] ?? null);
                }
            }

            foreach (['tabItems', 'accordionItems'] as $collectionKey) {
                foreach ((array) ($value[$collectionKey] ?? []) as $item) {
                    if (is_array($item)) {
                        $walk($item['children'] ?? null);
                    }
                }
            }
        };

        $walk($nodes);

        return $ordered;
    }
}
