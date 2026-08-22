<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\ModuleUsageCollector;
use PHPUnit\Framework\TestCase;

class PageBuilderElementorV24ModuleUsageCollectorTest extends TestCase
{
    public function test_types_walks_nested_children_and_grid_columns_in_first_seen_order(): void
    {
        $nodes = [[
            'type' => 'container',
            'children' => [
                ['type' => 'heading'],
                [
                    'type' => 'grid',
                    'columns' => [
                        ['children' => [['type' => 'tabs'], ['type' => 'heading']]],
                        ['children' => [['type' => 'form']]],
                    ],
                ],
            ],
        ], ['type' => 'tabs']];
        $original = $nodes;

        $this->assertSame(
            ['container', 'heading', 'grid', 'tabs', 'form'],
            (new ModuleUsageCollector())->types($nodes),
        );
        $this->assertSame($original, $nodes);
    }

    public function test_types_ignores_invalid_values_and_walks_tab_and_accordion_collections(): void
    {
        $this->assertSame(['tabs', 'image', 'accordion', 'button'], (new ModuleUsageCollector())->types([
            [
                'type' => 'tabs',
                'tabItems' => [['children' => [['type' => 'image']]]],
            ],
            [
                'type' => 'accordion',
                'accordionItems' => [['children' => [['type' => 'button'], ['type' => '']]]],
            ],
            null,
        ]));
    }
}
