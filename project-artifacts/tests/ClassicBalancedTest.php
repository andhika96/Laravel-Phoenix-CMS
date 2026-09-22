<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use App\Support\Article\ArticleTemplateOptions;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ClassicBalancedTest extends TestCase
{
    public function test_defaults_and_invalid_values_are_normalized_per_template(): void
    {
        $normalizer = app(ArticleTemplateOptions::class);
        $classic = $normalizer->archive('mosaic-classic');
        $this->assertSame(3, data_get($classic, 'mosaic_classic.side_count'));
        $this->assertSame(['desktop' => 3, 'tablet' => 2, 'mobile' => 1], $classic['grid']);
        $this->assertSame('13.5rem', $classic['thumbnail']['height']);
        $this->assertSame('22rem', $classic['mosaic_classic']['lead_height']['desktop']);
        $balanced = $normalizer->archive('balanced-card-grid');
        $this->assertSame('outline', data_get($balanced, 'balanced_grid.card_style'));
        $this->assertSame('13.5rem', $balanced['thumbnail']['height']);
        $this->assertArrayNotHasKey('mosaic_classic', $balanced);
        $this->assertArrayNotHasKey('balanced_grid', $classic);
        foreach (['mosaic-magazine', 'editorial-journal', 'minimal-reading-list'] as $key) {
            $this->assertArrayNotHasKey('mosaic_classic', $normalizer->archive($key));
            $this->assertArrayNotHasKey('balanced_grid', $normalizer->archive($key));
        }
        $custom = $normalizer->archive('mosaic-classic', ['mosaic_classic' => [
            'side_count' => 99, 'side_position' => 'unsafe', 'lead_ratio' => 'bad',
            'side_layout' => 'bad', 'lead_height' => ['desktop' => 'url(evil)'],
            'gap' => ['desktop' => '-5px'], 'show_date' => 'false',
        ]]);
        $this->assertSame(24, $custom['mosaic_classic']['side_count']);
        $this->assertSame('right', $custom['mosaic_classic']['side_position']);
        $this->assertSame('67-33', $custom['mosaic_classic']['lead_ratio']);
        $this->assertSame('list', $custom['mosaic_classic']['side_layout']);
        $this->assertSame('22rem', $custom['mosaic_classic']['lead_height']['desktop']);
        $this->assertFalse($custom['mosaic_classic']['show_date']);
        $this->assertSame($custom, $normalizer->archive('mosaic-classic', $custom));
        $this->assertSame($balanced, $normalizer->archive('balanced-card-grid', $balanced));
        $percent = $normalizer->archive('balanced-card-grid', ['balanced_grid' => ['gap' => ['desktop' => '5%'], 'card_padding' => '4%', 'card_radius' => '10%']]);
        $this->assertSame('5%', $percent['balanced_grid']['gap']['desktop']);
        $this->assertSame('4%', $percent['balanced_grid']['card_padding']);
        $this->assertSame('10%', $percent['balanced_grid']['card_radius']);
    }

    public function test_classic_partition_keeps_every_post_once_at_zero_and_large_side_counts(): void
    {
        foreach ([0, 1, 3, 5, 24] as $count) {
            $xpath = $this->xpath($this->render('mosaic-classic', ['mosaic_classic' => ['side_count' => $count]]));
            $this->assertSame(1, $xpath->query('//article[@data-classic-region="lead"]')->length);
            $this->assertSame(min(9, $count), $xpath->query('//*[@data-classic-region="side"]/article')->length);
            $this->assertSame(max(0, 9 - $count), $xpath->query('//*[@data-classic-region="grid"]/article')->length);
            $this->assertSame(10, $xpath->query('//article')->length);
            $titles = [];
            foreach ($xpath->query('//article//*[contains(@class,"article-title-clamp")]/a') as $title) $titles[] = $title->textContent;
            $this->assertSame(array_map(fn ($id) => "Revamp story $id", range(1, 10)), $titles);
        }
    }

    public function test_classic_controls_reach_markup_and_filtered_results_skip_the_lead(): void
    {
        $html = $this->render('mosaic-classic', ['mosaic_classic' => [
            'side_position' => 'left', 'lead_ratio' => '60-40', 'side_layout' => 'stacked',
            'lead_height' => ['desktop' => '25rem'], 'side_height' => ['mobile' => '9rem'],
            'divider' => false, 'show_excerpt' => false, 'show_date' => false, 'show_category' => false, 'show_read_more' => true,
        ]]);
        $this->assertStringContainsString('data-classic-side="left"', $html);
        $this->assertStringContainsString('data-classic-ratio="60-40"', $html);
        $this->assertStringContainsString('data-classic-side-layout="stacked"', $html);
        $this->assertStringContainsString('--classic-lead-height-desktop:25rem', $html);
        $this->assertStringContainsString('--classic-side-height-mobile:9rem', $html);
        $this->assertSame(0, $this->xpath($html)->query('//article//p[contains(@class,"article-excerpt-clamp")] | //article//time | //article//*[contains(@class,"article-chip")]')->length);
        $this->assertSame(10, $this->xpath($html)->query('//article//a[contains(@class,"article-text-link")]')->length);
        foreach ([['search' => 'story'], ['category' => 2], ['tag' => 'design']] as $query) {
            $filtered = $this->xpath($this->render('mosaic-classic', [], 10, $query));
            $this->assertSame(0, $filtered->query('//*[@data-classic-region="lead" or @data-classic-region="side"]')->length);
            $this->assertSame(10, $filtered->query('//*[@data-classic-region="grid"]/article')->length);
        }
    }

    public function test_balanced_grid_exposes_card_options_and_keeps_an_even_collection(): void
    {
        $html = $this->render('balanced-card-grid', ['grid' => ['desktop' => 4, 'tablet' => 3, 'mobile' => 2], 'balanced_grid' => [
            'card_style' => 'elevated', 'text_align' => 'center', 'card_padding' => '2rem', 'card_radius' => '12px',
            'gap' => ['desktop' => '2.5rem'], 'excerpt_lines' => 4, 'show_read_more' => true,
        ]]);
        $this->assertStringContainsString('data-card-style="elevated"', $html);
        $this->assertStringContainsString('data-card-align="center"', $html);
        $this->assertStringContainsString('--balanced-card-padding:2rem', $html);
        $this->assertStringContainsString('--balanced-card-radius:12px', $html);
        $this->assertStringContainsString('--story-gap-desktop:2.5rem', $html);
        $this->assertStringContainsString('--balanced-excerpt-lines:4', $html);
        $this->assertStringContainsString('--article-template-grid-desktop:4', $html);
        $this->assertSame(10, $this->xpath($html)->query('//article')->length);
    }

    public function test_empty_single_and_custom_thumbnail_states_remain_valid(): void
    {
        foreach (['mosaic-classic', 'balanced-card-grid'] as $template) {
            $empty = $this->render($template, [], 0);
            $this->assertStringContainsString('No articles found', $empty);
            $this->assertSame(0, $this->xpath($empty)->query('//article')->length);
            $single = $this->render($template, ['thumbnail' => ['height' => '18rem']], 1);
            $this->assertSame(1, $this->xpath($single)->query('//article')->length);
            $this->assertStringContainsString('article-image-placeholder.svg', $single);
            $this->assertStringContainsString('--article-thumbnail-height:18rem;', $single);
        }
    }

    private function render(string $template, array $input = [], int $count = 10, array $query = []): string
    {
        $this->app->instance('request', Request::create('/article', 'GET', $query));
        $items = collect();
        for ($id = 1; $id <= $count; $id++) {
            $article = new Article(['uri' => "revamp-story-$id", 'title' => "Revamp story $id", 'content' => '<p>Readable editorial content.</p>', 'created_at' => now()]);
            $article->setRelation('category', null);
            $items->push($article);
        }
        return view('article.templates.archive.'.$template, [
            'articles' => new LengthAwarePaginator($items, $count, 12, 1, ['path' => route('cms.core.article')]),
            'templateOptions' => app(ArticleTemplateOptions::class)->archive($template, $input),
            'articleCategories' => collect(), 'templateSettings' => null,
        ])->render();
    }

    private function xpath(string $html): \DOMXPath
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        return new \DOMXPath($dom);
    }
}
