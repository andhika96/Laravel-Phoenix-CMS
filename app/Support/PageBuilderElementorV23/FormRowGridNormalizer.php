<?php

namespace App\Support\PageBuilderElementorV23;

final class FormRowGridNormalizer
{
    private const DEVICES = ['desktop', 'tablet', 'mobile'];

    public function normalizeSettings(array $settings): array
    {
        $fields = is_array($settings['fields'] ?? null) ? $settings['fields'] : [];
        $settings['rowGrid'] = is_array($settings['rowGrid'] ?? null) && is_array($settings['rowGrid']['steps'] ?? null)
            ? $this->normalizeRowGrid($settings['rowGrid'], $fields)
            : $this->fromLegacyFields($fields);
        $settings['fields'] = $this->projectFields($settings['rowGrid']);

        return $settings;
    }

    public function fromLegacyFields(array $fields): array
    {
        $layout = ['version' => 2, 'steps' => [$this->createStep('step-root', 0)]];
        $stepIndex = 0;
        $rowIndex = null;
        $bucket = null;

        foreach ($fields as $rawField) {
            if (! is_array($rawField)) {
                continue;
            }
            if (($rawField['type'] ?? '') === 'step') {
                $stepData = [
                    'id' => trim((string) ($rawField['id'] ?? '')) ?: 'step-'.($stepIndex + 1),
                    'title' => (string) ($rawField['stepTitle'] ?? 'Step'),
                    'description' => (string) ($rawField['stepDescription'] ?? ''),
                    'nextButton' => (string) ($rawField['nextButton'] ?? 'Next'),
                    'previousButton' => (string) ($rawField['previousButton'] ?? 'Previous'),
                    'iconSource' => in_array(($rawField['iconSource'] ?? ''), ['none', 'library', 'svg'], true) ? $rawField['iconSource'] : 'library',
                    'iconStyle' => (string) ($rawField['iconStyle'] ?? 'solid'),
                    'iconName' => (string) ($rawField['iconName'] ?? 'check'),
                    'iconClass' => (string) ($rawField['iconClass'] ?? 'fas fa-check'),
                    'iconSvg' => (string) ($rawField['iconSvg'] ?? ''),
                ];
                $current = array_key_last($layout['steps']);
                $hasFields = array_any(
                    $layout['steps'][$current]['rows'] ?? [],
                    fn (array $row): bool => $this->rowFieldCount($row) > 0,
                );
                if ($hasFields) {
                    $stepIndex++;
                    $layout['steps'][] = array_merge($this->createStep($stepData['id'], $stepIndex), $stepData);
                } else {
                    $layout['steps'][$current] = array_merge($this->createStep($stepData['id'], $current), $stepData);
                }
                $rowIndex = null;
                $bucket = null;
                continue;
            }

            $field = $this->normalizeField($rawField);
            $nextBucket = $this->widthToColumns($field['width'] ?? 100);
            $capacity = $nextBucket === 1 ? PHP_INT_MAX : $nextBucket;
            $current = array_key_last($layout['steps']);
            $currentRow = $rowIndex === null ? null : ($layout['steps'][$current]['rows'][$rowIndex] ?? null);
            if ($currentRow === null || $bucket !== $nextBucket || $this->rowFieldCount($currentRow) >= $capacity) {
                $layout['steps'][$current]['rows'][] = $this->createRow([
                    'desktop' => $nextBucket,
                    'tablet' => $nextBucket,
                    'mobile' => 1,
                ]);
                $rowIndex = array_key_last($layout['steps'][$current]['rows']);
                $bucket = $nextBucket;
            }
            $layout['steps'][$current]['rows'][$rowIndex] = $this->appendItemToRow(
                $layout['steps'][$current]['rows'][$rowIndex],
                $this->fieldItem($field),
            );
        }

        foreach ($layout['steps'] as &$step) {
            $this->ensureStepRow($step);
        }
        unset($step);

        return $this->ensureLayout($layout);
    }

    public function projectFields(array $rowGrid): array
    {
        $fields = [];
        foreach ($rowGrid['steps'] ?? [] as $stepIndex => $step) {
            if ($stepIndex > 0) {
                $fields[] = [
                    'id' => (string) ($step['id'] ?? 'step-'.$stepIndex),
                    'type' => 'step',
                    'stepTitle' => (string) ($step['title'] ?? 'Step'),
                    'stepDescription' => (string) ($step['description'] ?? ''),
                    'nextButton' => (string) ($step['nextButton'] ?? 'Next'),
                    'previousButton' => (string) ($step['previousButton'] ?? 'Previous'),
                    'iconSource' => (string) ($step['iconSource'] ?? 'library'),
                    'iconStyle' => (string) ($step['iconStyle'] ?? 'solid'),
                    'iconName' => (string) ($step['iconName'] ?? 'check'),
                    'iconClass' => (string) ($step['iconClass'] ?? 'fas fa-check'),
                    'iconSvg' => (string) ($step['iconSvg'] ?? ''),
                ];
            }
            foreach ($step['rows'] ?? [] as $row) {
                foreach ($this->visualRowItems($row) as $item) {
                    $fields[] = $item['field'];
                }
            }
        }

        return $fields;
    }

    public function normalizedSteps(array $rowGrid): array
    {
        return array_values(array_filter($rowGrid['steps'] ?? [], 'is_array'));
    }

    public function trackPlan(array $row, string $device = 'desktop', bool $includeTail = false): array
    {
        $device = in_array($device, self::DEVICES, true) ? $device : 'desktop';
        $columns = array_values(array_filter($row['columns'] ?? [], 'is_array'));
        $count = $this->columnCount($row['columnCounts'][$device] ?? 1);
        $placements = [];
        $totalRows = 0;

        for ($groupStart = 0; $groupStart < count($columns); $groupStart += $count) {
            $group = array_slice($columns, $groupStart, $count);
            $spans = array_map(function (array $column) use ($device, $includeTail): int {
                $contentRows = 0;
                foreach ($column['items'] ?? [] as $item) {
                    if (! is_array($item) || ($item['kind'] ?? '') !== 'field') {
                        continue;
                    }
                    $field = is_array($item['field'] ?? null) ? $item['field'] : [];
                    $rowSpan = is_array($field['rowSpan'] ?? null) ? $field['rowSpan'] : [];
                    $contentRows += $this->columnCount($rowSpan[$device] ?? 1);
                }

                return $contentRows + ($includeTail ? 1 : 0);
            }, $group);
            $groupRows = max([1, ...$spans]);
            foreach ($group as $offset => $_column) {
                $placements[$groupStart + $offset] = [
                    'gridColumn' => $offset + 1,
                    'rowStart' => $totalRows + 1,
                    'rowSpan' => $groupRows,
                ];
            }
            $totalRows += $groupRows;
        }

        return [
            'columnCount' => $count,
            'totalRows' => max(1, $totalRows),
            'placements' => $placements,
        ];
    }

    public function moveItem(array $rowGrid, array $source, array $target, bool $confirmed = false): array
    {
        if (! $this->canAcceptDrop($source, $target)) {
            return ['layout' => $rowGrid, 'ok' => false, 'reason' => 'outside'];
        }
        if (($source['stepId'] ?? '') !== ($target['stepId'] ?? '') && ! $confirmed) {
            return ['layout' => $rowGrid, 'ok' => false, 'reason' => 'cross-step'];
        }
        $sourceLocation = $this->findColumnIndexes($rowGrid, $source);
        $targetLocation = $this->findColumnIndexes($rowGrid, $target);
        if ($sourceLocation === null || $targetLocation === null) {
            return ['layout' => $rowGrid, 'ok' => true];
        }
        [$sourceStep, $sourceRow, $sourceColumn] = $sourceLocation;
        [$targetStep, $targetRow, $targetColumn] = $targetLocation;
        $sourceItems = &$rowGrid['steps'][$sourceStep]['rows'][$sourceRow]['columns'][$sourceColumn]['items'];
        $itemId = (string) ($source['itemId'] ?? '');
        $sourceIndex = $itemId !== ''
            ? array_find_key($sourceItems, fn (array $item): bool => (string) ($item['id'] ?? '') === $itemId)
            : min(max(0, (int) ($source['index'] ?? 0)), max(0, count($sourceItems) - 1));
        if ($sourceIndex === null || ! isset($sourceItems[$sourceIndex])) {
            unset($sourceItems);
            return ['layout' => $rowGrid, 'ok' => true];
        }
        $item = array_splice($sourceItems, $sourceIndex, 1)[0];
        unset($sourceItems);
        $targetItems = &$rowGrid['steps'][$targetStep]['rows'][$targetRow]['columns'][$targetColumn]['items'];
        $targetIndex = min(max(0, (int) ($target['index'] ?? count($targetItems))), count($targetItems));
        array_splice($targetItems, $targetIndex, 0, [$item]);
        unset($targetItems);

        return ['layout' => $rowGrid, 'ok' => true];
    }

    public function deleteRow(array $rowGrid, string $stepId, string $rowId): array
    {
        foreach ($rowGrid['steps'] ?? [] as $stepIndex => $step) {
            if ((string) ($step['id'] ?? '') !== $stepId || count($step['rows'] ?? []) <= 1) {
                continue;
            }
            $rowIndex = array_find_key(
                $step['rows'],
                fn (array $row): bool => (string) ($row['id'] ?? '') === $rowId,
            );
            if ($rowIndex === null) {
                continue;
            }
            $targetOriginalIndex = $rowIndex > 0 ? $rowIndex - 1 : $rowIndex + 1;
            $removed = $this->ensureColumns($step['rows'][$rowIndex]);
            $target = $this->ensureColumns($step['rows'][$targetOriginalIndex]);
            array_splice($step['rows'], $rowIndex, 1);
            $moved = array_fill(0, count($target['columns']), []);
            foreach ($removed['columns'] as $columnIndex => $column) {
                $destination = min($columnIndex, count($target['columns']) - 1);
                array_push($moved[$destination], ...$column['items']);
            }
            foreach ($target['columns'] as $columnIndex => &$column) {
                $column['items'] = $rowIndex < $targetOriginalIndex
                    ? [...$moved[$columnIndex], ...$column['items']]
                    : [...$column['items'], ...$moved[$columnIndex]];
            }
            unset($column);
            $targetIndex = $targetOriginalIndex > $rowIndex ? $targetOriginalIndex - 1 : $targetOriginalIndex;
            $step['rows'][$targetIndex] = $target;
            $rowGrid['steps'][$stepIndex] = $step;

            return ['layout' => $this->ensureLayout($rowGrid), 'ok' => true];
        }

        return ['layout' => $rowGrid, 'ok' => false, 'reason' => 'row-not-found'];
    }

    public function canAcceptDrop(array $source, array $target): bool
    {
        $ownerId = trim((string) ($source['ownerId'] ?? ''));
        $group = 'pb-form-grid:'.$ownerId;

        return $ownerId !== ''
            && $ownerId === trim((string) ($target['ownerId'] ?? ''))
            && ($source['group'] ?? '') === $group
            && ($target['group'] ?? '') === $group
            && ($source['kind'] ?? 'field') !== 'submit';
    }

    private function normalizeRowGrid(array $source, array $legacyFields): array
    {
        if (! is_array($source['steps'] ?? null) || $source['steps'] === []) {
            return $this->fromLegacyFields($legacyFields);
        }
        $version = (int) ($source['version'] ?? 1);
        $layout = ['version' => 2, 'steps' => []];
        foreach ($source['steps'] as $stepIndex => $sourceStep) {
            if (! is_array($sourceStep)) {
                continue;
            }
            $step = $this->createStep((string) ($sourceStep['id'] ?? 'step-'.($stepIndex + 1)), $stepIndex);
            foreach (['title', 'description', 'nextButton', 'previousButton', 'iconStyle', 'iconName', 'iconClass', 'iconSvg'] as $key) {
                if (array_key_exists($key, $sourceStep)) {
                    $step[$key] = (string) $sourceStep[$key];
                }
            }
            if (in_array(($sourceStep['iconSource'] ?? ''), ['none', 'library', 'svg'], true)) {
                $step['iconSource'] = $sourceStep['iconSource'];
            }
            foreach ($sourceStep['rows'] ?? [] as $rowIndex => $sourceRow) {
                if (! is_array($sourceRow)) {
                    continue;
                }
                $step['rows'][] = $version >= 2
                    ? $this->normalizeTrackRow($sourceRow, $stepIndex, $rowIndex)
                    : $this->migrateCellRow($sourceRow, $stepIndex, $rowIndex);
            }
            $this->ensureStepRow($step);
            $layout['steps'][] = $step;
        }

        return $this->ensureLayout($layout);
    }

    private function normalizeTrackRow(array $source, int $stepIndex, int $rowIndex): array
    {
        $row = $this->createRow($source['columnCounts'] ?? []);
        $row['id'] = trim((string) ($source['id'] ?? '')) ?: 'row-'.($stepIndex + 1).'-'.($rowIndex + 1);
        $row['columns'] = [];
        foreach ($source['columns'] ?? [] as $columnIndex => $sourceColumn) {
            if (! is_array($sourceColumn) || ($sourceColumn['span'] ?? 'auto') === 'full') {
                continue;
            }
            $items = [];
            foreach ($sourceColumn['items'] ?? [] as $item) {
                $normalized = $this->normalizeItem($item);
                if ($normalized !== null) {
                    $items[] = $normalized;
                }
            }
            $row['columns'][] = $this->createColumn(
                $items,
                trim((string) ($sourceColumn['id'] ?? '')) ?: 'column-'.($stepIndex + 1).'-'.($rowIndex + 1).'-'.($columnIndex + 1),
            );
        }

        return $this->ensureColumns($row);
    }

    private function migrateCellRow(array $source, int $stepIndex, int $rowIndex): array
    {
        $row = $this->createRow($source['columnCounts'] ?? []);
        $row['id'] = trim((string) ($source['id'] ?? '')) ?: 'row-'.($stepIndex + 1).'-'.($rowIndex + 1);
        $cellIndex = 0;
        foreach ($source['columns'] ?? [] as $sourceColumn) {
            if (! is_array($sourceColumn) || ($sourceColumn['span'] ?? 'auto') === 'full') {
                continue;
            }
            $target = $cellIndex % count($row['columns']);
            foreach ($sourceColumn['items'] ?? [] as $item) {
                $normalized = $this->normalizeItem($item);
                if ($normalized !== null) {
                    $row['columns'][$target]['items'][] = $normalized;
                }
            }
            $cellIndex++;
        }

        return $row;
    }

    private function normalizeItem(mixed $item): ?array
    {
        if (! is_array($item)) {
            return null;
        }
        if (($item['kind'] ?? '') === 'field') {
            return $this->fieldItem($this->normalizeField(is_array($item['field'] ?? null) ? $item['field'] : []));
        }
        if (isset($item['id'], $item['type'])) {
            return $this->fieldItem($this->normalizeField($item));
        }

        return null;
    }

    private function normalizeField(array $field): array
    {
        $field = array_merge([
            'id' => 'field',
            'label' => 'Field',
            'type' => 'text',
            'required' => false,
            'width' => 100,
            'rowSpan' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1],
            'datasetMode' => 'static',
            'datasetId' => '',
            'datasetParentFieldId' => '',
            'conditionalLogic' => ['enabled' => false, 'relation' => 'all', 'rules' => []],
        ], $field);
        $span = is_array($field['rowSpan'] ?? null) ? $field['rowSpan'] : [];
        $field['rowSpan'] = [
            'desktop' => $this->columnCount($span['desktop'] ?? 1),
            'tablet' => $this->columnCount($span['tablet'] ?? 1),
            'mobile' => $this->columnCount($span['mobile'] ?? 1),
        ];

        return $field;
    }

    private function fieldItem(array $field): array
    {
        return ['id' => 'field:'.($field['id'] ?? 'field'), 'kind' => 'field', 'field' => $field];
    }

    private function createStep(string $id, int $index): array
    {
        return [
            'id' => $id !== '' ? $id : 'step-'.($index + 1),
            'title' => '',
            'description' => '',
            'nextButton' => 'Next',
            'previousButton' => 'Previous',
            'iconSource' => 'library',
            'iconStyle' => 'solid',
            'iconName' => 'check',
            'iconClass' => 'fas fa-check',
            'iconSvg' => '',
            'rows' => [],
        ];
    }

    private function createRow(array $counts = []): array
    {
        return $this->ensureColumns([
            'id' => 'row-'.substr(md5(uniqid('', true)), 0, 8),
            'columnCounts' => $this->columnCounts($counts),
            'columns' => [],
        ]);
    }

    private function createColumn(array $items = [], ?string $id = null): array
    {
        return [
            'id' => $id ?: 'column-'.substr(md5(uniqid('', true)), 0, 8),
            'items' => array_values(array_filter(
                $items,
                fn ($item): bool => is_array($item) && ($item['kind'] ?? '') === 'field',
            )),
        ];
    }

    private function ensureStepRow(array &$step): void
    {
        if (($step['rows'] ?? []) === []) {
            $step['rows'][] = $this->createRow();
        }
    }

    private function ensureLayout(array $layout): array
    {
        $layout['version'] = 2;
        if (($layout['steps'] ?? []) === []) {
            $layout['steps'][] = $this->createStep('step-root', 0);
        }
        foreach ($layout['steps'] as &$step) {
            $this->ensureStepRow($step);
            foreach ($step['rows'] as &$row) {
                $row = $this->ensureColumns($row);
            }
            unset($row);
        }
        unset($step);

        return $layout;
    }

    private function ensureColumns(array $row): array
    {
        $row['columnCounts'] = $this->columnCounts(is_array($row['columnCounts'] ?? null) ? $row['columnCounts'] : []);
        $columns = [];
        foreach ($row['columns'] ?? [] as $column) {
            if (! is_array($column) || ($column['span'] ?? 'auto') === 'full') {
                continue;
            }
            $columns[] = $this->createColumn(
                is_array($column['items'] ?? null) ? $column['items'] : [],
                trim((string) ($column['id'] ?? '')) ?: null,
            );
        }
        $desired = max(array_map(fn (string $device): int => $row['columnCounts'][$device], self::DEVICES));
        if (count($columns) === $desired) {
            $row['columns'] = $columns;
            return $row;
        }
        $items = $this->visualRowItems(['columns' => $columns]);
        $resized = [];
        for ($index = 0; $index < $desired; $index++) {
            $resized[] = $this->createColumn([], $columns[$index]['id'] ?? null);
        }
        foreach ($items as $index => $item) {
            $resized[$index % $desired]['items'][] = $item;
        }
        $row['columns'] = $resized;

        return $row;
    }

    private function appendItemToRow(array $row, array $item): array
    {
        $row = $this->ensureColumns($row);
        $target = 0;
        foreach ($row['columns'] as $columnIndex => $column) {
            if (count($column['items']) < count($row['columns'][$target]['items'])) {
                $target = $columnIndex;
            }
        }
        $row['columns'][$target]['items'][] = $item;

        return $row;
    }

    private function visualRowItems(array $row): array
    {
        $columns = array_values(array_filter($row['columns'] ?? [], 'is_array'));
        $length = $columns === [] ? 0 : max(array_map(fn (array $column): int => count($column['items'] ?? []), $columns));
        $items = [];
        for ($itemIndex = 0; $itemIndex < $length; $itemIndex++) {
            foreach ($columns as $column) {
                $item = $column['items'][$itemIndex] ?? null;
                if (is_array($item) && ($item['kind'] ?? '') === 'field' && is_array($item['field'] ?? null)) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    private function rowFieldCount(array $row): int
    {
        return array_sum(array_map(fn (array $column): int => count($column['items'] ?? []), $row['columns'] ?? []));
    }

    private function columnCounts(array $counts): array
    {
        return [
            'desktop' => $this->columnCount($counts['desktop'] ?? 1),
            'tablet' => $this->columnCount($counts['tablet'] ?? 1),
            'mobile' => $this->columnCount($counts['mobile'] ?? 1),
        ];
    }

    private function widthToColumns(mixed $width): int
    {
        $value = (float) $width ?: 100;
        return $value >= 80 ? 1 : ($value >= 42 ? 2 : ($value >= 28 ? 3 : 4));
    }

    private function columnCount(mixed $value): int
    {
        return max(1, min(4, (int) $value ?: 1));
    }

    private function findColumnIndexes(array $layout, array $location): ?array
    {
        foreach ($layout['steps'] ?? [] as $stepIndex => $step) {
            if ((string) ($step['id'] ?? '') !== (string) ($location['stepId'] ?? '')) {
                continue;
            }
            foreach ($step['rows'] ?? [] as $rowIndex => $row) {
                if ((string) ($row['id'] ?? '') !== (string) ($location['rowId'] ?? '')) {
                    continue;
                }
                foreach ($row['columns'] ?? [] as $columnIndex => $column) {
                    if ((string) ($column['id'] ?? '') === (string) ($location['columnId'] ?? '')) {
                        return [$stepIndex, $rowIndex, $columnIndex];
                    }
                }
            }
        }

        return null;
    }
}
