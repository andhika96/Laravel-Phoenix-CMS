<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV23\FormRowGridNormalizer;
use Tests\TestCase;

class PageBuilderElementorV23FormRowGridNormalizerTest extends TestCase
{
    public function test_it_normalizes_legacy_fields_into_version_two_column_lists(): void
    {
        $result = app(FormRowGridNormalizer::class)->normalizeSettings([
            'fields' => [
                ['id' => 'first', 'label' => 'First', 'type' => 'text', 'width' => 50],
                ['id' => 'second', 'label' => 'Second', 'type' => 'text', 'width' => 50],
                ['type' => 'step', 'id' => 'step-two', 'stepTitle' => 'Second step'],
                ['id' => 'message', 'label' => 'Message', 'type' => 'textarea', 'width' => 100],
            ],
        ]);

        $this->assertSame(2, $result['rowGrid']['version']);
        $this->assertCount(2, $result['rowGrid']['steps']);
        $this->assertCount(2, $result['rowGrid']['steps'][0]['rows'][0]['columns']);
        $this->assertSame(['first'], $this->fieldIds($result['rowGrid']['steps'][0]['rows'][0]['columns'][0]));
        $this->assertSame(['first', 'second', 'step-two', 'message'], array_column($result['fields'], 'id'));
    }

    public function test_step_icon_configuration_survives_legacy_migration_and_projection(): void
    {
        $result = app(FormRowGridNormalizer::class)->normalizeSettings([
            'fields' => [
                ['id' => 'name', 'label' => 'Name', 'type' => 'text'],
                [
                    'id' => 'step-two',
                    'type' => 'step',
                    'stepTitle' => 'Details',
                    'iconSource' => 'library',
                    'iconStyle' => 'solid',
                    'iconName' => 'star',
                    'iconClass' => 'fas fa-star',
                    'iconSvg' => '',
                ],
                ['id' => 'email', 'label' => 'Email', 'type' => 'email'],
            ],
        ]);

        $step = $result['rowGrid']['steps'][1];
        $marker = collect($result['fields'])->firstWhere('type', 'step');

        $this->assertSame('library', $step['iconSource']);
        $this->assertSame('fas fa-star', $step['iconClass']);
        $this->assertSame('star', $marker['iconName']);
    }

    public function test_it_migrates_the_old_cell_matrix_to_unlimited_track_items_and_removes_submit(): void
    {
        $result = app(FormRowGridNormalizer::class)->normalizeSettings([
            'rowGrid' => [
                'version' => 1,
                'steps' => [[
                    'id' => 'step-root',
                    'rows' => [[
                        'id' => 'row-1',
                        'columnCounts' => ['desktop' => 2, 'tablet' => 1, 'mobile' => 1],
                        'columns' => [
                            $this->legacyCell('cell-1', 'name'),
                            $this->legacyCell('cell-2', 'email'),
                            $this->legacyCell('cell-3', 'message'),
                            $this->legacyCell('cell-4', 'phone'),
                            ['id' => 'submit-slot', 'span' => 'full', 'items' => [['id' => 'submit', 'kind' => 'submit']]],
                        ],
                    ]],
                ]],
            ],
        ]);

        $columns = $result['rowGrid']['steps'][0]['rows'][0]['columns'];

        $this->assertSame(2, $result['rowGrid']['version']);
        $this->assertSame(['name', 'message'], $this->fieldIds($columns[0]));
        $this->assertSame(['email', 'phone'], $this->fieldIds($columns[1]));
        $this->assertSame(['name', 'email', 'message', 'phone'], array_column($result['fields'], 'id'));
        $this->assertStringNotContainsString('"kind":"submit"', json_encode($result['rowGrid'], JSON_THROW_ON_ERROR));
    }

    public function test_it_inserts_a_cross_row_item_without_swapping_the_target(): void
    {
        $normalizer = app(FormRowGridNormalizer::class);
        $layout = $normalizer->normalizeSettings([
            'rowGrid' => [
                'version' => 2,
                'steps' => [[
                    'id' => 'step-root',
                    'rows' => [
                        [
                            'id' => 'row-1',
                            'columnCounts' => ['desktop' => 2, 'tablet' => 1, 'mobile' => 1],
                            'columns' => [
                                ['id' => 'column-1', 'items' => [$this->fieldItem('name'), $this->fieldItem('message')]],
                                ['id' => 'column-2', 'items' => [$this->fieldItem('email')]],
                            ],
                        ],
                        [
                            'id' => 'row-2',
                            'columnCounts' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1],
                            'columns' => [['id' => 'column-3', 'items' => [$this->fieldItem('new')]]],
                        ],
                    ],
                ]],
            ],
        ])['rowGrid'];

        $moved = $normalizer->moveItem(
            $layout,
            $this->meta('row-2', 'column-3', 'field:new'),
            $this->meta('row-1', 'column-2', '', 1),
        );
        $rows = $moved['layout']['steps'][0]['rows'];

        $this->assertTrue($moved['ok']);
        $this->assertSame(['email', 'new'], $this->fieldIds($rows[0]['columns'][1]));
        $this->assertSame([], $rows[1]['columns'][0]['items']);
    }

    public function test_it_reorders_within_one_column_using_an_insertion_index(): void
    {
        $normalizer = app(FormRowGridNormalizer::class);
        $layout = $normalizer->normalizeSettings([
            'rowGrid' => [
                'version' => 2,
                'steps' => [[
                    'id' => 'step-root',
                    'rows' => [[
                        'id' => 'row-1',
                        'columnCounts' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1],
                        'columns' => [[
                            'id' => 'column-1',
                            'items' => [$this->fieldItem('a'), $this->fieldItem('b'), $this->fieldItem('c')],
                        ]],
                    ]],
                ]],
            ],
        ])['rowGrid'];

        $moved = $normalizer->moveItem(
            $layout,
            $this->meta('row-1', 'column-1', 'field:c', 2),
            $this->meta('row-1', 'column-1', '', 0),
        );

        $this->assertSame(['c', 'a', 'b'], $this->fieldIds($moved['layout']['steps'][0]['rows'][0]['columns'][0]));
    }

    public function test_empty_rows_keep_persistent_columns_and_never_store_submit(): void
    {
        $result = app(FormRowGridNormalizer::class)->normalizeSettings([
            'rowGrid' => [
                'version' => 2,
                'steps' => [[
                    'id' => 'step-root',
                    'rows' => [[
                        'id' => 'row-empty',
                        'columnCounts' => ['desktop' => 2, 'tablet' => 1, 'mobile' => 1],
                        'columns' => [],
                    ]],
                ]],
            ],
        ]);

        $columns = $result['rowGrid']['steps'][0]['rows'][0]['columns'];

        $this->assertCount(2, $columns);
        $this->assertSame([], $columns[0]['items']);
        $this->assertSame([], $columns[1]['items']);
        $this->assertStringNotContainsString('submit', json_encode($result['rowGrid'], JSON_THROW_ON_ERROR));
    }

    public function test_projection_preserves_visual_row_order_and_conditional_data(): void
    {
        $result = app(FormRowGridNormalizer::class)->normalizeSettings([
            'rowGrid' => [
                'version' => 2,
                'steps' => [[
                    'id' => 'root',
                    'rows' => [[
                        'id' => 'row-1',
                        'columnCounts' => ['desktop' => 2, 'tablet' => 1, 'mobile' => 1],
                        'columns' => [
                            ['id' => 'column-1', 'items' => [
                                $this->fieldItem('country', ['conditionalLogic' => ['enabled' => true, 'relation' => 'all', 'rules' => [['fieldId' => 'other']]]]),
                                $this->fieldItem('city'),
                            ]],
                            ['id' => 'column-2', 'items' => [$this->fieldItem('email')]],
                        ],
                    ]],
                ]],
            ],
        ]);

        $this->assertSame(['country', 'email', 'city'], array_column($result['fields'], 'id'));
        $this->assertTrue($result['fields'][0]['conditionalLogic']['enabled']);
    }

    public function test_responsive_row_span_and_owner_guard_remain_compatible(): void
    {
        $normalizer = app(FormRowGridNormalizer::class);
        $result = $normalizer->normalizeSettings([
            'fields' => [[
                'id' => 'message',
                'label' => 'Message',
                'type' => 'textarea',
                'rowSpan' => ['desktop' => 2, 'tablet' => 12, 'mobile' => 0],
            ]],
        ]);

        $this->assertSame(['desktop' => 2, 'tablet' => 4, 'mobile' => 1], $result['fields'][0]['rowSpan']);
        $this->assertTrue($normalizer->canAcceptDrop(
            ['ownerId' => 'form-1', 'group' => 'pb-form-grid:form-1'],
            ['ownerId' => 'form-1', 'group' => 'pb-form-grid:form-1'],
        ));
        $this->assertFalse($normalizer->canAcceptDrop(
            ['ownerId' => 'form-1', 'group' => 'pb-form-grid:form-1'],
            ['ownerId' => 'form-1', 'group' => 'pb-container'],
        ));
    }

    public function test_shared_track_plan_aligns_row_spans_across_responsive_column_groups(): void
    {
        $normalizer = app(FormRowGridNormalizer::class);
        $row = [
            'columnCounts' => ['desktop' => 2, 'tablet' => 2, 'mobile' => 1],
            'columns' => [
                ['id' => 'left', 'items' => [
                    $this->fieldItem('name'),
                    $this->fieldItem('message', ['type' => 'textarea', 'rowSpan' => ['desktop' => 4, 'tablet' => 4, 'mobile' => 4]]),
                ]],
                ['id' => 'right', 'items' => [
                    $this->fieldItem('email'),
                    $this->fieldItem('two'),
                    $this->fieldItem('three'),
                    $this->fieldItem('four'),
                    $this->fieldItem('five'),
                ]],
            ],
        ];

        $this->assertSame([
            'columnCount' => 2,
            'totalRows' => 5,
            'placements' => [
                ['gridColumn' => 1, 'rowStart' => 1, 'rowSpan' => 5],
                ['gridColumn' => 2, 'rowStart' => 1, 'rowSpan' => 5],
            ],
        ], $normalizer->trackPlan($row, 'desktop'));
        $this->assertSame([
            'columnCount' => 1,
            'totalRows' => 10,
            'placements' => [
                ['gridColumn' => 1, 'rowStart' => 1, 'rowSpan' => 5],
                ['gridColumn' => 1, 'rowStart' => 6, 'rowSpan' => 5],
            ],
        ], $normalizer->trackPlan($row, 'mobile'));
    }

    private function legacyCell(string $cellId, string $fieldId): array
    {
        return ['id' => $cellId, 'span' => 'auto', 'items' => [$this->fieldItem($fieldId)]];
    }

    private function fieldItem(string $id, array $extra = []): array
    {
        return [
            'id' => 'field:'.$id,
            'kind' => 'field',
            'field' => array_merge(['id' => $id, 'label' => ucfirst($id), 'type' => 'text'], $extra),
        ];
    }

    private function fieldIds(array $column): array
    {
        return array_map(fn (array $item): string => (string) $item['field']['id'], $column['items'] ?? []);
    }

    private function meta(string $rowId, string $columnId, string $itemId = '', int $index = 0): array
    {
        return [
            'ownerId' => 'form-1',
            'group' => 'pb-form-grid:form-1',
            'stepId' => 'step-root',
            'rowId' => $rowId,
            'columnId' => $columnId,
            'itemId' => $itemId,
            'index' => $index,
            'kind' => 'field',
        ];
    }
}
