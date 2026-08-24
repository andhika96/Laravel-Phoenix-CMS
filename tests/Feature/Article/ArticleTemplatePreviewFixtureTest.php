<?php

namespace Tests\Feature\Article;

use App\Support\Article\ArticleTemplatePreviewFixture;
use Tests\TestCase;

class ArticleTemplatePreviewFixtureTest extends TestCase
{
    public function test_it_provides_deterministic_editorial_preview_content_without_persisting_articles(): void
    {
        $fixture = app(ArticleTemplatePreviewFixture::class);
        $archive = $fixture->archivePaginator();
        $article = $fixture->detailArticle();
        $neighbors = $fixture->neighbors();

        $this->assertSame(48, $archive->total());
        $this->assertCount(6, $archive->items());
        $this->assertTrue(collect($archive->items())->every(fn ($item): bool => ! $item->exists));
        $this->assertTrue(collect($archive->items())->every(fn ($item): bool => str_starts_with((string) $item->thumb_s, 'articles/template-preview-20260825/')));
        $this->assertStringContainsString('<blockquote>', $article->content);
        $this->assertStringContainsString('<h2>', $article->content);
        $this->assertFalse($article->exists);
        $this->assertNotNull($neighbors['previous']);
        $this->assertNotNull($neighbors['next']);
        $this->assertFalse($neighbors['previous']->exists);
        $this->assertFalse($neighbors['next']->exists);
    }
}
