<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use App\Models\Article\Article_Categories;
use App\Support\Article\ArticleTemplateOptions;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ArticleMinimalReadingListFilterTest extends TestCase
{
    public function test_category_mode_select_and_button_list_are_mutually_exclusive(): void
    {
        $article = new Article([
            'id' => 7201,
            'uri' => 'minimal-filter-mode-test',
            'title' => 'Minimal Filter Mode Test',
            'content' => '<p>Preview content for category filter modes.</p>',
            'created_at' => now(),
        ]);
        $article->setRelation('category', new Article_Categories(['id' => 7202, 'name' => 'Design']));
        $articles = new LengthAwarePaginator([$article], 1, 1, 1, [
            'path' => route('cms.core.article'),
        ]);
        $categories = collect(range(1, 12))->map(fn (int $index): Article_Categories => new Article_Categories([
            'id' => 7200 + $index,
            'name' => 'Category '.$index,
        ]));
        $normalizer = app(ArticleTemplateOptions::class);

        $buttonList = view('article.templates.archive.minimal-reading-list', [
            'articles' => $articles,
            'templateSettings' => null,
            'templateOptions' => $normalizer->archive('minimal-reading-list', [
                'toolbar' => ['category' => ['enabled' => true, 'mode' => 'button-list']],
            ]),
            'articleCategories' => $categories,
            'popularArticles' => collect(),
        ])->render();

        $this->assertStringContainsString('data-article-category-search', $buttonList);
        $this->assertStringContainsString('data-article-category-link', $buttonList);
        $this->assertSame(11, substr_count($buttonList, 'data-article-category-link'), 'All categories plus at most ten category options should render');
        $this->assertStringContainsString('--article-reading-list-post-gap:0.75rem', $buttonList);
        $this->assertStringNotContainsString('article-reading-list-popular-gap', $buttonList);
        $this->assertStringNotContainsString('data-article-category-select', $buttonList);
        $this->assertStringNotContainsString('article-reading-list__category-select', $buttonList);

        $select = view('article.templates.archive.minimal-reading-list', [
            'articles' => $articles,
            'templateSettings' => null,
            'templateOptions' => $normalizer->archive('minimal-reading-list', [
                'toolbar' => ['category' => ['enabled' => true, 'mode' => 'select']],
            ]),
            'articleCategories' => $categories,
            'popularArticles' => collect(),
        ])->render();

        $this->assertStringContainsString('data-article-category-select', $select);
        $this->assertStringNotContainsString('data-article-category-search', $select);
        $this->assertStringNotContainsString('data-article-category-link', $select);
        $this->assertStringNotContainsString('>Filter<', $select);
    }

    public function test_post_list_spacing_and_sidebar_positions_use_normalized_defaults_and_overrides(): void
    {
        $options = app(ArticleTemplateOptions::class);
        $defaults = $options->archive('minimal-reading-list');

        $this->assertTrue($defaults['toolbar']['category']['enabled']);
        $this->assertSame('button-list', $defaults['toolbar']['category']['mode']);
        $this->assertSame('0.75rem', $defaults['post_list']['item_gap']);
        $this->assertSame('static', $defaults['sidebar']['categories']['position']);
        $this->assertSame('static', $defaults['sidebar']['popular']['position']);
        $this->assertArrayNotHasKey('item_gap', $defaults['sidebar']['popular']);

        $custom = $options->archive('minimal-reading-list', [
            'toolbar' => ['category' => ['mode' => 'select']],
            'post_list' => ['item_gap' => '10px'],
            'sidebar' => [
                'categories' => ['position' => 'sticky'],
                'popular' => ['position' => 'sticky'],
            ],
        ]);

        $this->assertSame('select', $custom['toolbar']['category']['mode']);
        $this->assertSame('10px', $custom['post_list']['item_gap']);
        $this->assertSame('sticky', $custom['sidebar']['categories']['position']);
        $this->assertSame('sticky', $custom['sidebar']['popular']['position']);
        $this->assertArrayNotHasKey('item_gap', $custom['sidebar']['popular']);

        $invalid = $options->archive('minimal-reading-list', [
            'toolbar' => ['category' => ['mode' => 'legacy']],
            'post_list' => ['item_gap' => 'calc(1rem)'],
            'sidebar' => [
                'categories' => ['position' => 'fixed'],
                'popular' => ['position' => 'absolute'],
            ],
        ]);

        $this->assertSame('button-list', $invalid['toolbar']['category']['mode']);
        $this->assertSame('0.75rem', $invalid['post_list']['item_gap']);
        $this->assertSame('static', $invalid['sidebar']['categories']['position']);
        $this->assertSame('static', $invalid['sidebar']['popular']['position']);
        $this->assertArrayNotHasKey('item_gap', $invalid['sidebar']['popular']);
    }
}
