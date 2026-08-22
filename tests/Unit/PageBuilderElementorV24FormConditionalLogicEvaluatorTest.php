<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\FormConditionalLogicEvaluator;
use Tests\TestCase;

class PageBuilderElementorV24FormConditionalLogicEvaluatorTest extends TestCase
{
    public function test_it_evaluates_all_and_any_rules_against_submitted_values(): void
    {
        $evaluator = app(FormConditionalLogicEvaluator::class);
        $field = [
            'id' => 'details',
            'conditionalLogic' => [
                'enabled' => true,
                'relation' => 'any',
                'rules' => [
                    ['fieldId' => 'country', 'operator' => 'equals', 'value' => 'ID'],
                    ['fieldId' => 'tags', 'operator' => 'contains', 'value' => 'priority'],
                ],
            ],
        ];

        $this->assertTrue($evaluator->fieldIsVisible($field, ['country' => 'MY', 'tags' => ['priority']]));
        $this->assertFalse($evaluator->fieldIsVisible($field, ['country' => 'MY', 'tags' => ['standard']]));
    }

    public function test_invalid_or_disabled_logic_defaults_to_visible(): void
    {
        $evaluator = app(FormConditionalLogicEvaluator::class);

        $this->assertTrue($evaluator->fieldIsVisible(['id' => 'message'], []));
        $this->assertTrue($evaluator->fieldIsVisible([
            'id' => 'message',
            'conditionalLogic' => ['enabled' => true, 'rules' => []],
        ], []));
    }

    public function test_selected_parent_is_used_as_the_expected_value_source(): void
    {
        $evaluator = app(FormConditionalLogicEvaluator::class);
        $field = [
            'id' => 'details',
            'conditionalLogic' => [
                'enabled' => true,
                'relation' => 'all',
                'rules' => [[
                    'fieldId' => 'country',
                    'operator' => 'equals',
                    'valueSource' => 'selectedParent',
                    'parentFieldId' => 'country',
                    'parentValue' => 'ID',
                ]],
            ],
        ];

        $this->assertTrue($evaluator->fieldIsVisible($field, [
            'country' => 'ID',
        ]));
        $this->assertFalse($evaluator->fieldIsVisible($field, [
            'country' => 'MY',
        ]));
        $this->assertFalse($evaluator->fieldIsVisible($field, ['country' => '']));
    }

    public function test_selected_parent_not_equals_hides_only_for_the_chosen_parent_value(): void
    {
        $evaluator = app(FormConditionalLogicEvaluator::class);
        $field = [
            'id' => 'province',
            'conditionalLogic' => [
                'enabled' => true,
                'relation' => 'all',
                'rules' => [[
                    'fieldId' => 'country',
                    'operator' => 'not_equals',
                    'valueSource' => 'selectedParent',
                    'parentFieldId' => 'country',
                    'parentValue' => 'ID',
                ]],
            ],
        ];

        $this->assertFalse($evaluator->fieldIsVisible($field, ['country' => 'ID']));
        $this->assertTrue($evaluator->fieldIsVisible($field, ['country' => 'MY']));
    }

    public function test_selected_parent_without_a_chosen_value_stays_hidden_for_comparison_operators(): void
    {
        $evaluator = app(FormConditionalLogicEvaluator::class);
        $field = [
            'id' => 'province',
            'conditionalLogic' => [
                'enabled' => true,
                'relation' => 'all',
                'rules' => [[
                    'fieldId' => 'country',
                    'operator' => 'equals',
                    'valueSource' => 'selectedParent',
                    'parentFieldId' => 'country',
                    'parentValue' => '',
                ]],
            ],
        ];

        $this->assertFalse($evaluator->fieldIsVisible($field, ['country' => 'ID']));
        $this->assertFalse($evaluator->fieldIsVisible($field, ['country' => 'MY']));
    }
}
