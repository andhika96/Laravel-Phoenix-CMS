<?php

namespace Tests\Feature\Article;

use App\Support\Article\ArticleTemplateOptions;
use App\Support\Article\ArticleTemplatePreviewFixture;

class ReadingCardsTest extends ManageArticleTemplateTest
{
    public function test_reading_cards_defaults_preserve_explicit_options_and_other_templates(): void
    {
        $normalizer = app(ArticleTemplateOptions::class);
        $defaults = $normalizer->detail('focused-reader')['focused_reader'];
        $this->assertEquals(880, $defaults['reading_width']);
        $this->assertEquals(1040, $defaults['cover_width']);
        $this->assertTrue($defaults['navigation_thumbnails']);
        $this->assertSame(['desktop' => '5/2', 'tablet' => '5/2', 'mobile' => '16/9'], $defaults['cover_ratio']);
        $custom = $normalizer->detail('focused-reader', ['focused_reader' => ['reading_width' => 600, 'cover_width' => 700, 'navigation_thumbnails' => false, 'cover_ratio' => ['mobile' => '4/3']]])['focused_reader'];
        $this->assertEquals(600, $custom['reading_width']);
        $this->assertEquals(700, $custom['cover_width']);
        $this->assertFalse($custom['navigation_thumbnails']);
        $this->assertSame('4/3', $custom['cover_ratio']['mobile']);
        $this->assertArrayNotHasKey('focused_reader', $normalizer->detail('knowledge-toc'));
        $this->assertArrayNotHasKey('focused_reader', $normalizer->detail('editorial-feature'));
    }

    public function test_neighbor_links_have_accessible_cards_without_phantom_neighbors(): void
    {
        $fixture = app(ArticleTemplatePreviewFixture::class);
        $neighbors = $fixture->neighbors();
        foreach ([[true, true], [true, false], [false, true], [false, false]] as [$previous, $next]) {
            $html = view('article.templates.detail.focused-reader', ['article' => $fixture->detailArticle(), 'previousArticle' => $previous ? $neighbors['previous'] : null, 'nextArticle' => $next ? $neighbors['next'] : null])->render();
            $this->assertSame((int) $previous + (int) $next, substr_count($html, '<a class="article-reading-navigation__card '));
            if ($previous) $this->assertStringContainsString(route('cms.core.article.detail', $neighbors['previous']->uri), $html);
            if ($next) $this->assertStringContainsString(route('cms.core.article.detail', $neighbors['next']->uri), $html);
            if ($previous || $next) {
                $this->assertStringContainsString('aria-label="Article navigation"', $html);
                $this->assertStringContainsString('min read', $html);
                $this->assertStringContainsString('loading="lazy"', $html);
            }
        }
        $html = view('article.templates.detail.focused-reader', ['article' => $fixture->detailArticle(), 'previousArticle' => $neighbors['previous'], 'nextArticle' => $neighbors['next'], 'templateOptions' => ['display' => ['navigation' => false]]])->render();
        $this->assertStringNotContainsString('<nav', $html);
        foreach (['knowledge-toc', 'editorial-feature'] as $key) {
            $html = view('article.templates.detail.'.$key, ['article' => $fixture->detailArticle(), 'previousArticle' => $neighbors['previous'], 'nextArticle' => $neighbors['next'], 'templateOptions' => app(ArticleTemplateOptions::class)->detail($key)])->render();
            $this->assertStringNotContainsString('article-reading-navigation', $html);
            $this->assertStringContainsString('article-detail-navigation', $html);
        }
    }
}
