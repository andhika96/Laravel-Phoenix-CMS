<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use App\Support\Article\ArticleTemplateOptions;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class MosaicMagazineTest extends TestCase
{
    public function test_options_are_scoped_bounded_and_idempotent(): void
    {
        $normalizer = app(ArticleTemplateOptions::class);
        $defaults = $normalizer->archive('mosaic-magazine');
        $this->assertSame(2, data_get($defaults, 'mosaic_magazine.supporting_count'));
        $this->assertSame('13.5rem', $defaults['thumbnail']['height']);
        $this->assertSame(['desktop' => 2, 'tablet' => 2, 'mobile' => 1], data_get($defaults, 'mosaic_magazine.columns'));
        $this->assertTrue($defaults['toolbar']['category']['enabled']);
        $this->assertSame('left', $defaults['toolbar']['category']['position']);
        $custom = $normalizer->archive('mosaic-magazine', ['mosaic_magazine' => [
            'supporting_count' => 999, 'match_grid' => 'true', 'proportion' => 'unsafe',
            'columns' => ['desktop' => 9, 'tablet' => 0, 'mobile' => 9],
        ]]);
        $this->assertSame(24, $custom['mosaic_magazine']['supporting_count']);
        $this->assertTrue($custom['mosaic_magazine']['match_grid']);
        $this->assertSame('equal', $custom['mosaic_magazine']['proportion']);
        $this->assertSame(['desktop' => 4, 'tablet' => 1, 'mobile' => 2], $custom['mosaic_magazine']['columns']);
        $this->assertSame($custom, $normalizer->archive('mosaic-magazine', $custom));
        $this->assertArrayNotHasKey('mosaic_magazine', $normalizer->archive('editorial-journal', $custom));
        $this->assertSame(0, $normalizer->archive('mosaic-magazine', ['mosaic_magazine' => ['supporting_count' => -2]])['mosaic_magazine']['supporting_count']);
        $this->assertFalse($normalizer->archive('mosaic-magazine', ['toolbar' => ['category' => ['enabled' => false]]])['toolbar']['category']['enabled']);
    }

    public function test_partition_preserves_every_article_once_for_all_supporting_counts(): void
    {
        foreach ([0, 1, 2, 3, 4, 24] as $count) {
            $xpath = $this->xpath($this->render(8, ['mosaic_magazine' => ['supporting_count' => $count]]));
            $this->assertSame(1, $xpath->query('//article[@data-mosaic-region="hero"]')->length);
            $this->assertSame(min($count, 7), $xpath->query('//*[@data-mosaic-region="supporting"]/article')->length);
            $this->assertSame(max(0, 7 - $count), $xpath->query('//*[@data-mosaic-region="regular"]/article')->length);
            $this->assertSame(8, $xpath->query('//article')->length);
            $titles = [];
            foreach ($xpath->query('//article//*[contains(@class,"article-title-clamp")]/a') as $title) $titles[] = $title->textContent;
            $this->assertSame(array_map(fn ($id) => "Mosaic story $id", range(1, 8)), $titles);
        }
    }

    public function test_matching_columns_and_asymmetry_follow_each_device(): void
    {
        $html = $this->render(8, ['grid' => ['desktop' => 3, 'tablet' => 2, 'mobile' => 1], 'mosaic_magazine' => [
            'supporting_count' => 3, 'match_grid' => true, 'proportion' => 'asymmetric',
        ]]);
        $xpath = $this->xpath($html);
        $style = $xpath->query('//*[@data-mosaic-region="supporting"]')->item(0)->getAttribute('style');
        $this->assertStringContainsString('--mosaic-columns-desktop:repeat(3, minmax(0, 1fr))', $style);
        $this->assertStringContainsString('--mosaic-columns-tablet:minmax(0, 3fr) minmax(0, 2fr)', $style);
        $this->assertStringContainsString('--mosaic-columns-mobile:repeat(1, minmax(0, 1fr))', $style);
    }

    public function test_filtered_results_use_regular_grid_and_keep_filter_controls(): void
    {
        foreach ([['search' => 'Mosaic'], ['category' => '3'], ['tag' => 'design']] as $query) {
            $html = $this->render(4, [], $query);
            $xpath = $this->xpath($html);
            $this->assertSame(0, $xpath->query('//*[@data-mosaic-region="hero" or @data-mosaic-region="supporting"]')->length);
            $this->assertSame(4, $xpath->query('//*[@data-mosaic-region="regular"]/article')->length);
            $this->assertStringContainsString('data-article-category-select', $html);
            $this->assertStringContainsString('Clear filters', $html);
            $this->assertStringContainsString('article-mosaic-results', $html);
        }
    }

    public function test_empty_single_and_missing_thumbnail_states_have_no_empty_groups(): void
    {
        $empty = $this->render(0);
        $this->assertStringContainsString('No articles found', $empty);
        $this->assertSame(0, $this->xpath($empty)->query('//*[@data-mosaic-region]')->length);
        $single = $this->render(1);
        $this->assertSame(1, $this->xpath($single)->query('//*[@data-mosaic-region]')->length);
        $this->assertStringContainsString('article-image-placeholder.svg', $single);
    }

    private function render(int $count, array $input = [], array $query = []): string
    {
        $this->app->instance('request', Request::create('/article', 'GET', $query));
        $items = collect();
        for ($id = 1; $id <= $count; $id++) {
            $article = new Article(['id' => $id, 'uri' => "mosaic-story-$id", 'title' => "Mosaic story $id", 'content' => '<p>A readable editorial story.</p>', 'created_at' => now()]);
            $article->setRelation('category', null);
            $items->push($article);
        }
        return view('article.templates.archive.mosaic-magazine', [
            'articles' => new LengthAwarePaginator($items, $count, 12, 1, ['path' => route('cms.core.article')]),
            'templateOptions' => app(ArticleTemplateOptions::class)->archive('mosaic-magazine', $input),
            'articleCategories' => collect([(object) ['id' => 3, 'name' => 'Design']]),
            'templateSettings' => null,
        ])->render();
    }

    private function xpath(string $html): \DOMXPath
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        return new \DOMXPath($dom);
    }
}
