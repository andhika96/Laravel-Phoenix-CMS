<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23FeatureShowcaseWidgetTest extends TestCase
{
    public function test_feature_showcase_is_registered_as_a_general_widget(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');

        $this->assertIsArray($module);
        $this->assertSame('feature_showcase', $module['type'] ?? null);
        $this->assertSame('Feature Showcase', $module['label'] ?? null);
        $this->assertSame('general', $module['category'] ?? null);
        $this->assertSame('js/pagebuilder_elementor_v23/widgets/general/feature-showcase/Canvas.vue', $module['canvas'] ?? null);
        $this->assertSame('js/pagebuilder_elementor_v23/widgets/general/feature-showcase/Settings.vue', $module['settings'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.partials.render_feature_showcase', $module['view'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? ''));
        $this->assertFileExists(public_path($module['canvas'] ?? ''));
        $this->assertFileExists(public_path($module['settings'] ?? ''));
    }

    public function test_frontend_renderer_supports_all_templates_and_responsive_positions(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $templates = [
            'specifications_metrics',
            'specifications_hero',
            'performance_collage',
            'exterior_gallery',
            'feature_image',
        ];

        foreach ($templates as $template) {
            $html = view($module['view'], [
                'node' => [
                    'id' => 'showcase-' . $template,
                    'type' => 'feature_showcase',
                    'settings' => [
                        'template' => $template,
                        'title' => '<Unsafe title>',
                        'subtitle' => 'Subtitle',
                        'description' => 'Description',
                        'metrics' => [
                            ['label' => 'Wheelbase', 'value' => '2680', 'unit' => 'MM'],
                            ['label' => 'Power', 'value' => '114', 'unit' => 'PS'],
                            ['label' => 'Torque', 'value' => '150', 'unit' => 'NM'],
                        ],
                        'images' => [
                            ['url' => '/assets/one.webp', 'alt' => 'One'],
                            ['url' => '/assets/two.webp', 'alt' => 'Two'],
                            ['url' => 'javascript:alert(1)', 'alt' => 'Unsafe'],
                        ],
                        'textPosition' => 'right',
                        'imagePosition' => 'left',
                        'textAlign' => 'center',
                        'verticalAlign' => 'end',
                        'contentWidth' => '40%',
                        'imageWidth' => '60%',
                        'mobileOrder' => 'image-first',
                        'featuredImagePosition' => '2',
                        'tallImagePosition' => 'right',
                        'mediaOrder' => ['2', '1', '3'],
                    ],
                ],
            ])->render();

            $this->assertStringContainsString('data-feature-showcase', $html);
            $this->assertStringContainsString('data-template="' . $template . '"', $html);
            $this->assertStringContainsString('&lt;Unsafe title&gt;', $html);
            $this->assertStringContainsString('Subtitle', $html);
            $this->assertStringContainsString('Description', $html);
            if ($template === 'specifications_metrics') {
                $this->assertStringContainsString('2680', $html);
            }
            if ($template === 'specifications_hero') {
                $this->assertStringContainsString('114', $html);
                $this->assertStringContainsString('150', $html);
            }
            $this->assertTrue(str_contains($html, '/assets/one.webp') || str_contains($html, '/assets/two.webp'));
            if (in_array($template, ['performance_collage', 'exterior_gallery'], true)) {
                $this->assertStringContainsString('/assets/one.webp', $html);
                $this->assertStringContainsString('/assets/two.webp', $html);
            }
            $this->assertStringNotContainsString('javascript:', $html);
            $this->assertStringContainsString('@media(max-width:1024px)', $html);
            $this->assertStringContainsString('@media(max-width:767px)', $html);
        }
    }

    public function test_frontend_renderer_uses_safe_placeholders_when_media_is_missing(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $html = view($module['view'], [
            'node' => [
                'id' => 'showcase-placeholder',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'exterior_gallery',
                    'title' => 'Gallery',
                    'images' => [],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('pb-feature-showcase__placeholder', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function test_specifications_metrics_matches_media_object_reference_structure(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $html = view($module['view'], [
            'node' => [
                'id' => 'showcase-specifications-metrics-default',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'metrics' => [],
                    'images' => [],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('Wheelbase', $html);
        $this->assertStringContainsString('Max Horsepower', $html);
        $this->assertStringContainsString('Max Torque', $html);
        $this->assertStringContainsString('grid-template-columns:repeat(2,minmax(0,1fr))', $html);
        $this->assertStringNotContainsString('<h2 class="pb-feature-showcase__title"', $html);
    }

    public function test_specifications_metrics_supports_four_metrics_and_centered_third_metric(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $html = view($module['view'], [
            'node' => [
                'id' => 'showcase-specifications-metrics-four',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'threeMetricPosition' => 'center',
                    'metrics' => [
                        ['label' => 'Metric One', 'value' => '1', 'unit' => 'A'],
                        ['label' => 'Metric Two', 'value' => '2', 'unit' => 'B'],
                        ['label' => 'Metric Three', 'value' => '3', 'unit' => 'C'],
                        ['label' => 'Metric Four', 'value' => '4', 'unit' => 'D'],
                    ],
                    'images' => [],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('Metric One', $html);
        $this->assertStringContainsString('Metric Two', $html);
        $this->assertStringContainsString('Metric Three', $html);
        $this->assertStringContainsString('Metric Four', $html);
        $this->assertStringNotContainsString('pb-feature-showcase__metrics is-three-center', $html);

        $threeMetricHtml = view($module['view'], [
            'node' => [
                'id' => 'showcase-specifications-metrics-three-center',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'threeMetricPosition' => 'center',
                    'threeMetricPositionTablet' => 'center',
                    'metrics' => [
                        ['label' => 'Metric One', 'value' => '1', 'unit' => 'A'],
                        ['label' => 'Metric Two', 'value' => '2', 'unit' => 'B'],
                        ['label' => 'Metric Three', 'value' => '3', 'unit' => 'C'],
                    ],
                    'images' => [],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('pb-feature-showcase__metrics is-three-center', $threeMetricHtml);
        $this->assertStringContainsString('grid-column:1 / -1', $threeMetricHtml);
        $this->assertStringContainsString('@media(max-width:1024px)', $threeMetricHtml);
        $this->assertStringContainsString('#pb-node-showcase-specifications-metrics-three-center .pb-feature-showcase__metrics .pb-feature-showcase__metric:last-child{grid-column:1 / -1', $threeMetricHtml);

        $oneMetricHtml = view($module['view'], [
            'node' => [
                'id' => 'showcase-specifications-metrics-one',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'metrics' => [
                        ['label' => 'Metric One', 'value' => '1', 'unit' => 'A'],
                    ],
                    'images' => [],
                ],
            ],
        ])->render();

        $this->assertSame(1, substr_count($oneMetricHtml, 'class="pb-feature-showcase__metric"'));
        $this->assertStringNotContainsString('Metric Two', $oneMetricHtml);
    }

    public function test_frontend_renderer_accepts_direct_asset_image_urls_and_rejects_unsafe_urls(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $html = view($module['view'], [
            'node' => [
                'id' => 'showcase-direct-image-url',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'images' => [
                        [
                            'imageSource' => 'url',
                            'url' => 'https://cdn.example.test/mg5.png',
                            'alt' => 'MG 5',
                        ],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('src="https://cdn.example.test/mg5.png"', $html);
        $this->assertStringContainsString('alt="MG 5"', $html);

        $unsafeHtml = view($module['view'], [
            'node' => [
                'id' => 'showcase-unsafe-image-url',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'images' => [
                        [
                            'imageSource' => 'url',
                            'url' => 'javascript:alert(1)',
                            'alt' => 'Unsafe',
                        ],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringNotContainsString('src="javascript:', $unsafeHtml);
    }

    public function test_metric_typography_supports_label_value_unit_and_image_left_auto_fit(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $html = view($module['view'], [
            'node' => [
                'id' => 'showcase-metric-typography',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'imagePosition' => 'left',
                    'contentWidth' => '42%',
                    'metricLabelFontFamily' => 'Georgia, serif',
                    'metricLabelFontSize' => '18px',
                    'metricValueFontFamily' => 'Arial, sans-serif',
                    'metricValueFontSize' => '64px',
                    'metricUnitFontFamily' => 'Tahoma, sans-serif',
                    'metricUnitFontSize' => '16px',
                    'metrics' => [
                        ['label' => 'Wheelbase', 'value' => '2680', 'unit' => 'MM'],
                    ],
                    'images' => [],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('font-family:Georgia, serif', $html);
        $this->assertStringContainsString('font-size:15.84px', $html);
        $this->assertStringContainsString('font-family:Arial, sans-serif', $html);
        $this->assertStringContainsString('font-size:56.32px', $html);
        $this->assertStringContainsString('font-family:Tahoma, sans-serif', $html);
        $this->assertStringContainsString('font-size:14.08px', $html);
        $this->assertStringContainsString('font-size:56.32px', $html);
    }

    public function test_specifications_metrics_scopes_alignment_and_applies_radius_and_unit_spacing(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $html = view($module['view'], [
            'node' => [
                'id' => 'showcase-specifications-metrics-layout-contract',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'title' => 'Heading',
                    'subtitle' => 'Subheading',
                    'description' => 'Description',
                    'textAlign' => 'center',
                    'imageRadius' => '18px',
                    'metrics' => [
                        ['label' => 'Wheelbase', 'value' => '2680', 'unit' => 'MM'],
                    ],
                    'images' => [],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('--pb-feature-radius:18px', $html);
        $this->assertStringContainsString('pb-feature-showcase--specifications_metrics .pb-feature-showcase__media img{height:420px;object-fit:contain;border-radius:var(--pb-feature-radius,45px)}', $html);
        $this->assertStringContainsString('pb-feature-showcase__metric-intro', $html);
        $this->assertStringContainsString('text-align:left', $html);
        $this->assertStringNotContainsString('pb-feature-showcase__content" style="text-align:center', $html);
        $this->assertStringContainsString('column-gap:12px', $html);
        $this->assertStringContainsString('flex:0 0 auto;white-space:nowrap', $html);
    }

    public function test_specifications_metrics_uses_responsive_image_height_and_mobile_position_fallback(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $html = view($module['view'], [
            'node' => [
                'id' => 'showcase-responsive-image-height',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'imagePosition' => 'left',
                    'imagePositionMobile' => 'left',
                    'horizontalAlign' => 'right',
                    'imageHeight' => '420px',
                    'imageHeightTablet' => '300px',
                    'imageHeightMobile' => '160px',
                    'horizontalAlignMobile' => 'left',
                    'metrics' => [
                        ['label' => 'Wheelbase', 'value' => '2680', 'unit' => 'MM'],
                    ],
                    'images' => [],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('--pb-feature-image-height:420px', $html);
        $this->assertStringContainsString('--pb-feature-image-height:160px', $html);
        $this->assertStringContainsString('height:var(--pb-feature-image-height,420px)', $html);
        $this->assertStringContainsString('flex-direction:column-reverse', $html);
        $this->assertStringContainsString('is-horizontal-right', $html);
        $this->assertStringContainsString('#pb-node-showcase-responsive-image-height .pb-feature-showcase__split .pb-feature-showcase__content{width:100%;align-self:flex-start}', $html);
        $this->assertStringContainsString('#pb-node-showcase-responsive-image-height .pb-feature-showcase__metrics--specifications{align-self:flex-start;width:max-content;max-width:100%}', $html);
        $this->assertStringNotContainsString('#pb-node-showcase-responsive-image-height .pb-feature-showcase__split .pb-feature-showcase__media{width:58%;align-self:center;justify-content:flex-start}', $html);
        $this->assertStringNotContainsString('pb-feature-showcase--mobile-', $html);
    }

    public function test_specifications_metrics_horizontal_alignment_positions_metrics_block(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $expectedAlignments = [
            'left' => 'flex-start',
            'center' => 'center',
            'right' => 'flex-end',
        ];

        foreach ($expectedAlignments as $alignment => $alignSelf) {
            $html = view($module['view'], [
                'node' => [
                    'id' => 'showcase-horizontal-' . $alignment,
                    'type' => 'feature_showcase',
                    'settings' => [
                        'template' => 'specifications_metrics',
                        'imagePosition' => 'bottom',
                        'horizontalAlign' => $alignment,
                        'metrics' => [
                            ['label' => 'Metric', 'value' => '123', 'unit' => 'PX'],
                        ],
                        'images' => [],
                    ],
                ],
            ])->render();

            $this->assertStringContainsString('is-horizontal-' . $alignment, $html);
            $this->assertStringContainsString('class="pb-feature-showcase__content" style="align-self:' . $alignSelf . ';width:100%"', $html);
            $this->assertStringContainsString('class="pb-feature-showcase__metrics pb-feature-showcase__metrics--specifications" style="text-align:left;align-self:' . $alignSelf . ';width:max-content;max-width:100%"', $html);
        }
    }

    public function test_specifications_hero_horizontal_alignment_positions_metrics_block(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $html = view($module['view'], [
            'node' => [
                'id' => 'showcase-hero-horizontal-right',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_hero',
                    'imagePosition' => 'bottom',
                    'horizontalAlign' => 'right',
                    'metrics' => [
                        ['label' => 'Wheelbase', 'value' => '2680', 'unit' => 'MM'],
                        ['label' => 'Power', 'value' => '114', 'unit' => 'PS'],
                        ['label' => 'Torque', 'value' => '150', 'unit' => 'NM'],
                    ],
                    'images' => [],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('class="pb-feature-showcase__content" style="align-self:flex-end;width:100%"', $html);
        $this->assertStringContainsString('class="pb-feature-showcase__metrics pb-feature-showcase__metrics--specifications" style="text-align:left;align-self:flex-end;width:max-content;max-width:100%"', $html);
        $this->assertStringNotContainsString('justify-content:flex-end', $html);
    }

    public function test_specifications_metrics_support_responsive_metric_columns_and_third_position_gate_contract(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.feature_showcase');
        $threeMetricHtml = view($module['view'], [
            'node' => [
                'id' => 'showcase-metric-columns-responsive',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'metricColumns' => '1',
                    'metricColumnsTablet' => '2',
                    'metricColumnsMobile' => '2',
                    'threeMetricPosition' => 'center',
                    'threeMetricPositionTablet' => 'left',
                    'threeMetricPositionMobile' => 'center',
                    'metrics' => [
                        ['label' => 'Metric One', 'value' => '1', 'unit' => 'A'],
                        ['label' => 'Metric Two', 'value' => '2', 'unit' => 'B'],
                        ['label' => 'Metric Three', 'value' => '3', 'unit' => 'C'],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('--pb-feature-metric-columns:1', $threeMetricHtml);
        $this->assertStringContainsString('grid-template-columns:repeat(var(--pb-feature-metric-columns,2),minmax(0,1fr))', $threeMetricHtml);
        $this->assertStringContainsString('#pb-node-showcase-metric-columns-responsive{--pb-feature-metric-columns:2}', $threeMetricHtml);
        $this->assertStringContainsString('grid-column:1 / -1', $threeMetricHtml);
        $this->assertStringContainsString('is-three-center', $threeMetricHtml);

        $fourMetricHtml = view($module['view'], [
            'node' => [
                'id' => 'showcase-metric-columns-four',
                'type' => 'feature_showcase',
                'settings' => [
                    'template' => 'specifications_metrics',
                    'metricColumns' => '2',
                    'threeMetricPosition' => 'center',
                    'metrics' => [
                        ['label' => 'Metric One', 'value' => '1', 'unit' => 'A'],
                        ['label' => 'Metric Two', 'value' => '2', 'unit' => 'B'],
                        ['label' => 'Metric Three', 'value' => '3', 'unit' => 'C'],
                        ['label' => 'Metric Four', 'value' => '4', 'unit' => 'D'],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('--pb-feature-metric-columns:2', $fourMetricHtml);
        $this->assertStringNotContainsString('pb-feature-showcase__metrics is-three-center', $fourMetricHtml);
    }
}
