<?php

namespace Tests\Feature\Article;

use App\Support\Article\ArticleTemplateOptions;
use Tests\TestCase;

class ArticleTemplateOptionsTest extends TestCase
{
    public function test_it_provides_safe_per_template_defaults_and_normalizes_custom_options(): void
    {
        $options = app(ArticleTemplateOptions::class);

        $minimalDefaults = $options->archive('minimal-reading-list');
        $this->assertTrue($minimalDefaults['header']['eyebrow']['enabled']);
        $this->assertTrue($minimalDefaults['header']['title']['enabled']);
        $this->assertTrue($minimalDefaults['header']['description']['enabled']);
        $this->assertTrue($minimalDefaults['toolbar']['search']['enabled']);
        $this->assertSame('9.3rem', $minimalDefaults['thumbnail']['height']);
        $this->assertTrue($minimalDefaults['sidebar']['enabled']);
        $this->assertTrue($minimalDefaults['sidebar']['categories']['enabled']);
        $this->assertTrue($minimalDefaults['sidebar']['popular']['enabled']);

        $minimalSidebar = $options->archive('minimal-reading-list', [
            'sidebar' => [
                'enabled' => false,
                'categories' => ['enabled' => false],
                'popular' => ['enabled' => false],
            ],
        ]);
        $this->assertFalse($minimalSidebar['sidebar']['enabled']);
        $this->assertFalse($minimalSidebar['sidebar']['categories']['enabled']);
        $this->assertFalse($minimalSidebar['sidebar']['popular']['enabled']);
        $this->assertArrayNotHasKey('sidebar', $options->archive('balanced-card-grid'));

        $focusedDefaults = $options->detail('focused-reader');
        $this->assertTrue($focusedDefaults['header']['title']['enabled']);

        $archive = $options->archive('editorial-journal', [
            'header' => [
                'eyebrow' => ['enabled' => false, 'text' => '  Stories  '],
                'title' => ['enabled' => true, 'text' => 'Journal custom'],
            ],
            'toolbar' => [
                'search' => ['enabled' => true, 'position' => 'center'],
                'category' => ['enabled' => true, 'position' => 'right'],
            ],
            'grid' => ['desktop' => 9, 'tablet' => 0, 'mobile' => 3],
            'ignored' => ['unsafe' => true],
        ]);

        $this->assertFalse($archive['header']['eyebrow']['enabled']);
        $this->assertSame('Stories', $archive['header']['eyebrow']['text']);
        $this->assertSame('Journal custom', $archive['header']['title']['text']);
        $this->assertSame('center', $archive['toolbar']['search']['position']);
        $this->assertSame('right', $archive['toolbar']['category']['position']);
        $this->assertSame(['desktop' => 4, 'tablet' => 1, 'mobile' => 2], $archive['grid']);
        $this->assertArrayNotHasKey('ignored', $archive);

        $detail = $options->detail('focused-reader', [
            'header' => [
                'eyebrow' => ['enabled' => true, 'mode' => 'custom', 'text' => 'Insight'],
                'title' => ['enabled' => false],
                'description' => ['enabled' => true, 'mode' => 'dynamic', 'text' => 'Ignored'],
            ],
        ]);

        $this->assertSame('custom', $detail['header']['eyebrow']['mode']);
        $this->assertSame('Insight', $detail['header']['eyebrow']['text']);
        $this->assertFalse($detail['header']['title']['enabled']);
        $this->assertSame('', $detail['header']['description']['text']);
    }

    public function test_it_normalizes_archive_styling_and_dimension_units_without_custom_css(): void
    {
        $options = app(ArticleTemplateOptions::class);

        $archive = $options->archive('balanced-card-grid', [
            'thumbnail' => [
                'mode' => 'asset',
                'fit' => 'contain',
                'height' => '12rem',
                'background_color' => '#123456',
                'frame' => [
                    'enabled' => true,
                    'border_color' => '#abcdef',
                    'border_width' => '1.25em',
                    'radius' => '12%',
                ],
            ],
            'pagination' => [
                'show_total' => false,
                'position' => 'center',
                'frame' => [
                    'enabled' => false,
                    'border_color' => '#fedcba',
                    'border_width' => '12%',
                    'radius' => '2rem',
                    'background_color' => '#101828',
                ],
                'padding' => [
                    'enabled' => true,
                    'desktop' => ['top' => '1.25rem', 'right' => '20px', 'bottom' => '5%', 'left' => '12pt'],
                    'tablet' => ['top' => '4em', 'right' => '2.6rem', 'bottom' => '4%', 'left' => '18pt'],
                    'mobile' => ['top' => '8px', 'right' => '2px', 'bottom' => '3px', 'left' => '4px'],
                ],
                'margin' => [
                    'enabled' => true,
                    'desktop' => ['top' => '-1px', 'right' => 'calc(1px)', 'bottom' => '1.239rem', 'left' => '0'],
                ],
            ],
            'article_title' => ['tag' => 'h2'],
            'shell' => [
                'padding' => [
                    'enabled' => true,
                    'desktop' => ['top' => '3em', 'right' => '4%', 'bottom' => '14pt', 'left' => '1.5rem'],
                ],
                'margin' => [
                    'enabled' => true,
                    'desktop' => ['top' => '0', 'right' => '0', 'bottom' => '2rem', 'left' => '0'],
                ],
                'frame' => [
                    'enabled' => true,
                    'border_color' => '#0f172a',
                    'border_width' => '2pt',
                    'radius' => '1.5em',
                    'background_color' => '#ffffff',
                ],
            ],
        ]);

        $this->assertSame('asset', $archive['thumbnail']['mode']);
        $this->assertSame('contain', $archive['thumbnail']['fit']);
        $this->assertSame('12rem', $archive['thumbnail']['height']);
        $this->assertSame('#123456', $archive['thumbnail']['background_color']);
        $this->assertSame('1.25em', $archive['thumbnail']['frame']['border_width']);
        $this->assertSame('12%', $archive['thumbnail']['frame']['radius']);
        $this->assertFalse($archive['pagination']['show_total']);
        $this->assertSame('center', $archive['pagination']['position']);
        $this->assertSame('1px', $archive['pagination']['frame']['border_width']);
        $this->assertSame(['top' => '1.25rem', 'right' => '20px', 'bottom' => '5%', 'left' => '12pt'], $archive['pagination']['padding']['desktop']);
        $this->assertSame(['top' => '0px', 'right' => '0px', 'bottom' => '1.24rem', 'left' => '0px'], $archive['pagination']['margin']['desktop']);
        $this->assertSame('h2', $archive['article_title']['tag']);
        $this->assertSame(['top' => '3em', 'right' => '4%', 'bottom' => '14pt', 'left' => '1.5rem'], $archive['shell']['padding']['desktop']);
        $this->assertSame('2pt', $archive['shell']['frame']['border_width']);
        $this->assertSame('1.5em', $archive['shell']['frame']['radius']);
    }

    public function test_it_normalizes_named_pagination_models_and_device_ranges(): void
    {
        $options = app(ArticleTemplateOptions::class);

        $defaults = $options->archive('minimal-reading-list')['pagination'];

        $this->assertSame('boxed', $defaults['type']);
        $this->assertSame(['desktop' => 3, 'tablet' => 3, 'mobile' => 2], $defaults['range']);

        $custom = $options->archive('minimal-reading-list', [
            'pagination' => [
                'type' => 'soft',
                'range' => ['desktop' => 7, 'tablet' => 4, 'mobile' => 1],
            ],
        ])['pagination'];

        $this->assertSame('soft', $custom['type']);
        $this->assertSame(['desktop' => 7, 'tablet' => 4, 'mobile' => 1], $custom['range']);

        $invalid = $options->archive('minimal-reading-list', [
            'pagination' => [
                'type' => 'legacy',
                'range' => ['desktop' => 99, 'tablet' => 0, 'mobile' => 'calc(1rem)'],
            ],
        ])['pagination'];

        $this->assertSame('boxed', $invalid['type']);
        $this->assertSame(['desktop' => 9, 'tablet' => 1, 'mobile' => 2], $invalid['range']);
    }

    public function test_it_normalizes_search_models_visual_overrides_and_fontawesome_icon_allowlist(): void
    {
        $options = app(ArticleTemplateOptions::class);

        $defaults = $options->archive('minimal-reading-list')['toolbar']['search'];
        $this->assertSame('attached', $defaults['type']);
        $this->assertSame('0.75rem', $defaults['radius']);
        $this->assertSame('0.75rem', $defaults['gap']);
        $this->assertSame('fas fa-search', $defaults['icon']);
        $this->assertNull($defaults['button_hover_background_color']);

        $custom = $options->archive('minimal-reading-list', [
            'toolbar' => [
                'search' => [
                    'type' => 'underline',
                    'radius' => '1.25rem',
                    'gap' => '12px',
                    'icon' => 'fas fa-sliders-h',
                    'input_background_color' => '#f8fafc',
                    'input_text_color' => '#172033',
                    'button_background_color' => '#16a579',
                    'button_text_color' => '#ffffff',
                    'button_hover_background_color' => '#087956',
                    'button_hover_text_color' => '#ffffff',
                    'button_active_background_color' => '#065f46',
                    'button_active_text_color' => '#ffffff',
                ],
            ],
            'pagination' => [
                'item_radius' => '1rem',
                'item_gap' => '10px',
                'previous_icon' => 'fas fa-angle-left',
                'next_icon' => 'fas fa-arrow-right',
                'item_hover_background_color' => '#f0fdf4',
                'item_active_text_color' => '#ffffff',
            ],
        ]);

        $search = $custom['toolbar']['search'];
        $pagination = $custom['pagination'];
        $this->assertSame('underline', $search['type']);
        $this->assertSame('1.25rem', $search['radius']);
        $this->assertSame('12px', $search['gap']);
        $this->assertSame('fas fa-sliders-h', $search['icon']);
        $this->assertSame('#087956', $search['button_hover_background_color']);
        $this->assertSame('1rem', $pagination['item_radius']);
        $this->assertSame('10px', $pagination['item_gap']);
        $this->assertSame('fas fa-angle-left', $pagination['previous_icon']);
        $this->assertSame('fas fa-arrow-right', $pagination['next_icon']);

        $invalid = $options->archive('minimal-reading-list', [
            'toolbar' => ['search' => ['type' => 'legacy', 'icon' => 'javascript:alert(1)']],
            'pagination' => ['previous_icon' => 'svg evil', 'next_icon' => 'javascript:alert(1)'],
        ]);
        $this->assertSame('attached', $invalid['toolbar']['search']['type']);
        $this->assertSame('fas fa-search', $invalid['toolbar']['search']['icon']);
        $this->assertSame('fas fa-chevron-left', $invalid['pagination']['previous_icon']);
        $this->assertSame('fas fa-chevron-right', $invalid['pagination']['next_icon']);
    }

    public function test_it_normalizes_border_radius_as_a_page_builder_style_four_value_group(): void
    {
        $options = app(ArticleTemplateOptions::class);

        $archive = $options->archive('minimal-reading-list', [
            'thumbnail' => ['frame' => ['radius' => '4px 8px 12px 16px']],
            'pagination' => ['frame' => ['radius' => '2rem 3rem 4rem 5rem']],
            'shell' => ['frame' => ['radius' => 'calc(1rem)']],
        ]);

        $this->assertSame('4px 8px 12px 16px', $archive['thumbnail']['frame']['radius']);
        $this->assertSame('2rem 3rem 4rem 5rem', $archive['pagination']['frame']['radius']);
        $this->assertSame('1rem', $archive['shell']['frame']['radius']);
    }

    public function test_it_rejects_unsafe_styling_values_and_keeps_detail_shell_options(): void
    {
        $options = app(ArticleTemplateOptions::class);

        $archive = $options->archive('minimal-reading-list', [
            'thumbnail' => ['mode' => 'script', 'fit' => 'fill', 'height' => 'url(javascript:alert(1))', 'background_color' => 'url(javascript:alert(1))'],
            'pagination' => ['position' => 'everywhere'],
            'article_title' => ['tag' => 'script'],
        ]);

        $detail = $options->detail('focused-reader', [
            'shell' => [
                'padding' => ['enabled' => true, 'mobile' => ['top' => '1.5rem', 'right' => '2%', 'bottom' => '4pt', 'left' => '3px']],
                'margin' => ['enabled' => true, 'tablet' => ['top' => '-2px', 'right' => '1px', 'bottom' => '1px', 'left' => '1px']],
                'frame' => ['enabled' => true, 'border_color' => 'red', 'border_width' => '2%', 'radius' => '2.25rem', 'background_color' => '#fff'],
            ],
        ]);

        $this->assertSame('background', $archive['thumbnail']['mode']);
        $this->assertSame('cover', $archive['thumbnail']['fit']);
        $this->assertSame('9.3rem', $archive['thumbnail']['height']);
        $this->assertSame('#f2f4f7', $archive['thumbnail']['background_color']);
        $this->assertSame('right', $archive['pagination']['position']);
        $this->assertSame('h4', $archive['article_title']['tag']);
        $this->assertSame(['top' => '1.5rem', 'right' => '2%', 'bottom' => '4pt', 'left' => '3px'], $detail['shell']['padding']['mobile']);
        $this->assertSame('0px', $detail['shell']['margin']['tablet']['top']);
        $this->assertSame('#e1e6ee', $detail['shell']['frame']['border_color']);
        $this->assertSame('1px', $detail['shell']['frame']['border_width']);
        $this->assertSame('2.25rem', $detail['shell']['frame']['radius']);
    }

    public function test_it_keeps_strictly_valid_coloris_color_formats_without_accepting_raw_css(): void
    {
        $options = app(ArticleTemplateOptions::class);

        $archive = $options->archive('mosaic-magazine', [
            'thumbnail' => ['background_color' => 'rgba(12, 34, 56, 0.5)'],
            'pagination' => ['frame' => ['border_color' => 'hsl(210, 40%, 50%)']],
            'shell' => ['frame' => ['background_color' => 'hsla(210, 40%, 50%, .75)']],
        ]);

        $this->assertSame('rgba(12, 34, 56, 0.5)', $archive['thumbnail']['background_color']);
        $this->assertSame('hsl(210, 40%, 50%)', $archive['pagination']['frame']['border_color']);
        $this->assertSame('hsla(210, 40%, 50%, .75)', $archive['shell']['frame']['background_color']);

        $unsafe = $options->archive('mosaic-magazine', [
            'thumbnail' => ['background_color' => 'rgb(256, 0, 0)'],
            'pagination' => ['frame' => ['border_color' => 'hsl(210, 140%, 50%)']],
            'shell' => ['frame' => ['background_color' => 'url(https://example.test/image.png)']],
        ]);

        $this->assertSame('#f2f4f7', $unsafe['thumbnail']['background_color']);
        $this->assertSame('#e6e9ef', $unsafe['pagination']['frame']['border_color']);
        $this->assertSame('#ffffff', $unsafe['shell']['frame']['background_color']);
    }
}
