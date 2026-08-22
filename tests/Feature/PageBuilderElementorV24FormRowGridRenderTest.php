<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24FormRowGridRenderTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;
    public function test_column_lists_render_multiple_fields_and_submit_as_the_final_step_footer(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('form', [
            'node' => [
                'id' => 'form-row-grid',
                'type' => 'form',
                'settings' => [
                    'formName' => 'Grid Form',
                    'rowGrid' => [
                        'version' => 2,
                        'steps' => [[
                            'id' => 'root',
                            'rows' => [[
                                'id' => 'row-1',
                                'columnCounts' => ['desktop' => 2, 'tablet' => 2, 'mobile' => 1],
                                'columns' => [
                                    ['id' => 'column-1', 'items' => [
                                        $this->fieldItem('name', 'Name', 'text'),
                                        $this->fieldItem('message', 'Message', 'textarea', ['desktop' => 2, 'tablet' => 2, 'mobile' => 1]),
                                    ]],
                                    ['id' => 'column-2', 'items' => [
                                        $this->fieldItem('email', 'Email', 'email'),
                                        $this->fieldItem('phone', 'Phone', 'tel'),
                                    ]],
                                ],
                            ]],
                        ]],
                    ],
                ],
            ],
            'pageData' => null,
        ])->render();

        $this->assertSame(2, substr_count($html, 'class="pb-pro-form__column"'));
        $this->assertStringContainsString('grid-template-columns:repeat(2,minmax(0,1fr))', $html);
        $this->assertStringContainsString('data-form-column="column-1"', $html);
        $this->assertStringContainsString('data-form-column="column-2"', $html);
        $this->assertStringContainsString('--form-row-span:2', $html);
        $this->assertStringContainsString('--form-row-span-tablet:2', $html);
        $this->assertStringContainsString('data-pro-form-field="name"', $html);
        $this->assertStringContainsString('data-pro-form-field="email"', $html);
        $this->assertStringContainsString('data-pro-form-field="message"', $html);
        $this->assertStringContainsString('data-pro-form-field="phone"', $html);
        $this->assertSame(1, substr_count($html, 'class="pb-pro-form__submit-slot"'));
        $this->assertGreaterThan(strpos($html, 'class="pb-pro-form__rows"'), strpos($html, 'class="pb-pro-form__submit-slot"'));
        $this->assertStringNotContainsString('pb-pro-form__column is-full', $html);
        $this->assertStringContainsString('>Send<', $html);
        $this->assertStringContainsString('--form-track-count:', $html);
        $this->assertStringContainsString('grid-template-rows:subgrid', $html);
        $this->assertStringContainsString('--form-column-track-span:', $html);
    }

    public function test_multiple_steps_and_submit_width_render_from_canonical_settings(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('form', [
            'node' => [
                'id' => 'form-steps',
                'type' => 'form',
                'settings' => [
                    'stepType' => 'number',
                    'buttonWidth' => '50',
                    'buttonAlign' => 'center',
                    'rowGrid' => [
                        'version' => 2,
                        'steps' => [
                            [
                                'id' => 'step-one',
                                'title' => 'Contact',
                                'nextButton' => 'Continue',
                                'rows' => [[
                                    'id' => 'row-one',
                                    'columnCounts' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1],
                                    'columns' => [['id' => 'column-one', 'items' => [$this->fieldItem('name', 'Name', 'text')]]],
                                ]],
                            ],
                            [
                                'id' => 'step-two',
                                'title' => 'Details',
                                'previousButton' => 'Back',
                                'rows' => [[
                                    'id' => 'row-two',
                                    'columnCounts' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1],
                                    'columns' => [['id' => 'column-two', 'items' => [$this->fieldItem('email', 'Email', 'email')]]],
                                ]],
                            ],
                        ],
                    ],
                ],
            ],
            'pageData' => null,
        ])->render();

        $this->assertSame(2, substr_count($html, 'data-pro-form-step'));
        $this->assertStringContainsString('class="pb-pro-form__progress type-number"', $html);
        $this->assertStringContainsString('data-pro-next>Continue</button>', $html);
        $this->assertStringContainsString('data-pro-previous>Back</button>', $html);
        $this->assertSame(1, substr_count($html, 'data-pro-form-submit'));
        $this->assertStringContainsString('style="justify-content:center"', $html);
        $this->assertStringContainsString('style="width:50%;', $html);
    }

    public function test_icon_text_step_indicator_renders_the_step_icon_label_and_shape(): void
    {
        $node = $this->multiStepNode('icon-text', 'rounded');
        $node['settings']['rowGrid']['steps'][0] += [
            'iconSource' => 'library',
            'iconStyle' => 'solid',
            'iconName' => 'star',
            'iconClass' => 'fas fa-star',
            'iconSvg' => '',
        ];

        $html = $this->pageBuilderV24ModuleViewByType('form', [
            'node' => $node,
            'pageData' => null,
        ])->render();

        $this->assertStringContainsString('class="pb-pro-form__progress type-icon-text"', $html);
        $this->assertStringContainsString('data-pro-step-marker', $html);
        $this->assertStringContainsString('shape-rounded', $html);
        $this->assertStringContainsString('<i class="fas fa-star"></i>', $html);
        $this->assertStringContainsString('data-pro-step-label>Contact</small>', $html);
    }

    public function test_progress_step_indicator_renders_a_runtime_progressbar_instead_of_number_badges(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('form', [
            'node' => $this->multiStepNode('progress', 'circle'),
            'pageData' => null,
        ])->render();

        $this->assertStringContainsString('class="pb-pro-form__progress type-progress"', $html);
        $this->assertStringContainsString('role="progressbar"', $html);
        $this->assertStringContainsString('data-pro-step-progress-fill', $html);
        $this->assertStringContainsString('data-pro-step-progress-text', $html);
        $this->assertStringNotContainsString('data-pro-step-marker', $html);
    }

    public function test_custom_message_modes_render_a_structured_accessible_message_layer(): void
    {
        foreach (['basic', 'above-form', 'toast', 'modal'] as $display) {
            $node = $this->multiStepNode('none', 'circle');
            $node['settings'] += [
                'customMessages' => true,
                'messageDisplay' => $display,
                'successTitle' => 'Message sent',
                'successMessage' => 'Your form was submitted successfully.',
                'errorTitle' => 'Submission failed',
                'errorMessage' => 'Please retry.',
                'messageShowIcon' => true,
                'messageDismissible' => true,
            ];

            $html = $this->pageBuilderV24ModuleViewByType('form', [
                'node' => $node,
                'pageData' => null,
            ])->render();

            $this->assertStringContainsString('data-message-display="'.$display.'"', $html);
            $this->assertStringContainsString('data-success-title="Message sent"', $html);
            $this->assertStringContainsString('data-error-title="Submission failed"', $html);
            $this->assertStringContainsString('data-pro-form-message-layer', $html);
            $this->assertStringContainsString('display-'.$display, $html);
            $this->assertStringContainsString('data-pro-form-message-title', $html);
            $this->assertStringContainsString('data-pro-form-message-text', $html);
            $this->assertStringContainsString('data-pro-form-message-icon', $html);
            $this->assertStringContainsString('data-pro-form-message-close', $html);
            $this->assertStringContainsString('aria-live="polite"', $html);
        }
    }

    private function fieldItem(string $id, string $label, string $type, array $rowSpan = ['desktop' => 1, 'tablet' => 1, 'mobile' => 1]): array
    {
        return [
            'id' => 'field:'.$id,
            'kind' => 'field',
            'field' => compact('id', 'label', 'type', 'rowSpan'),
        ];
    }

    private function multiStepNode(string $stepType, string $stepShape): array
    {
        return [
            'id' => 'form-step-types',
            'type' => 'form',
            'settings' => [
                'stepType' => $stepType,
                'stepShape' => $stepShape,
                'rowGrid' => [
                    'version' => 2,
                    'steps' => [
                        [
                            'id' => 'step-one',
                            'title' => 'Contact',
                            'rows' => [[
                                'id' => 'row-one',
                                'columnCounts' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1],
                                'columns' => [['id' => 'column-one', 'items' => [$this->fieldItem('name', 'Name', 'text')]]],
                            ]],
                        ],
                        [
                            'id' => 'step-two',
                            'title' => 'Details',
                            'rows' => [[
                                'id' => 'row-two',
                                'columnCounts' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1],
                                'columns' => [['id' => 'column-two', 'items' => [$this->fieldItem('email', 'Email', 'email')]]],
                            ]],
                        ],
                    ],
                ],
            ],
        ];
    }
}
