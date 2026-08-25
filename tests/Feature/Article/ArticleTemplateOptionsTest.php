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

    public function test_it_rejects_unsafe_styling_values_and_keeps_detail_shell_options(): void
    {
        $options = app(ArticleTemplateOptions::class);

        $archive = $options->archive('minimal-reading-list', [
            'thumbnail' => ['mode' => 'script', 'fit' => 'fill', 'background_color' => 'url(javascript:alert(1))'],
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
