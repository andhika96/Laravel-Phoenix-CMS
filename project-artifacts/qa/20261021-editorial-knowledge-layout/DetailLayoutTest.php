<?php

namespace Tests\Feature\Article;

use App\Support\Article\ArticleTemplateOptions;
use App\Support\Article\ArticleTemplatePreviewFixture;

class DetailLayoutTest extends ManageArticleTemplateTest
{
    public function test_reading_cards_and_display_options_work_for_both_templates(): void
    {
        $fixture = app(ArticleTemplatePreviewFixture::class);
        $neighbors = $fixture->neighbors();
        foreach (['editorial-feature', 'knowledge-toc'] as $key) {
            foreach ([[true, true], [true, false], [false, true], [false, false]] as [$previous, $next]) {
                $html = view('article.templates.detail.'.$key, ['article' => $fixture->detailArticle(), 'templateOptions' => app(ArticleTemplateOptions::class)->detail($key), 'previousArticle' => $previous ? $neighbors['previous'] : null, 'nextArticle' => $next ? $neighbors['next'] : null])->render();
                $this->assertSame((int) $previous + (int) $next, substr_count($html, '<a class="article-reading-navigation__card '));
                $this->assertSame(1, substr_count($html, 'class="article-back-link"'));
                $this->assertStringContainsString('min read', $html);
                $this->assertLessThan(strpos($html, 'class="article-detail__meta"'), strpos($html, '<h1>'));
            }
            $options = app(ArticleTemplateOptions::class)->detail($key, ['display' => ['navigation' => false, 'date' => false, 'author' => false, 'reading_time' => false], 'header' => ['title' => ['enabled' => false], 'description' => ['enabled' => false]]]);
            $html = view('article.templates.detail.'.$key, ['article' => $fixture->detailArticle(), 'templateOptions' => $options, 'previousArticle' => $neighbors['previous']])->render();
            foreach (['article-reading-navigation', '<h1>', 'article-detail__dek', 'article-detail__meta'] as $hidden) $this->assertStringNotContainsString($hidden, $html);
        }
    }

    public function test_outline_keeps_existing_heading_ids_and_generated_links(): void
    {
        $article = app(ArticleTemplatePreviewFixture::class)->detailArticle();
        $article->content = '<h2 id="existing">Existing</h2><h2 data-id="custom">Same title</h2><h3>Same title</h3><h2> </h2><h3 id="a&amp;b">Encoded</h3>';
        $html = view('article.templates.detail.knowledge-toc', ['article' => $article, 'templateOptions' => app(ArticleTemplateOptions::class)->detail('knowledge-toc')])->render();
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $links = $xpath->query('//aside//a');
        $this->assertCount(4, $links);
        foreach ($links as $link) {
            $target = substr($link->getAttribute('href'), 1);
            $this->assertCount(1, $xpath->query('//*[@id="'.$target.'"]'));
        }
        $this->assertCount(1, $xpath->query('//h2[@id="existing"]'));
        $this->assertStringContainsString('<details open>', $html);
        $this->assertStringContainsString('<summary>', $html);
        $this->assertStringContainsString('data-has-outline="true"', $html);
        $this->assertStringNotContainsString('id="existing" id=', $html);
    }

    public function test_headingless_articles_do_not_reserve_an_empty_toc_column(): void
    {
        $article = app(ArticleTemplatePreviewFixture::class)->detailArticle();
        $article->content = '<p>Article without headings.</p>';
        $html = view('article.templates.detail.knowledge-toc', ['article' => $article, 'templateOptions' => app(ArticleTemplateOptions::class)->detail('knowledge-toc')])->render();
        $this->assertStringContainsString('data-has-outline="false"', $html);
        $this->assertStringNotContainsString('<aside', $html);
        $this->assertStringContainsString($article->content, $html);
    }
}
