<?php

namespace Tests\Feature\Article;

use App\Support\Article\ArticleTemplateOptions;
use App\Support\Article\ArticleTemplatePreviewFixture;
use Illuminate\Support\Facades\Storage;

class DistinctNavigationTest extends ManageArticleTemplateTest
{
    public function test_each_detail_template_renders_its_own_navigation_and_real_neighbor_links(): void
    {
        $fixture = app(ArticleTemplatePreviewFixture::class);
        $neighbors = $fixture->neighbors();
        $styles = ['focused-reader' => 'article-reading-navigation', 'editorial-feature' => 'article-story-navigation', 'knowledge-toc' => 'article-reference-navigation'];
        foreach ($styles as $key => $style) {
            foreach ([[true, true], [true, false], [false, true], [false, false]] as [$previous, $next]) {
                $html = view('article.templates.detail.'.$key, ['article' => $fixture->detailArticle(), 'templateOptions' => app(ArticleTemplateOptions::class)->detail($key), 'previousArticle' => $previous ? $neighbors['previous'] : null, 'nextArticle' => $next ? $neighbors['next'] : null])->render();
                $dom = new \DOMDocument();
                @$dom->loadHTML($html);
                $xpath = new \DOMXPath($dom);
                $this->assertCount((int) ($previous || $next), $xpath->query('//nav[@class="'.$style.'"]'), $key);
                $links = $xpath->query('//nav//a');
                $this->assertCount((int) $previous + (int) $next, $links);
                $expected = [];
                if ($previous) $expected[] = route('cms.core.article.detail', $neighbors['previous']->uri);
                if ($next) $expected[] = route('cms.core.article.detail', $neighbors['next']->uri);
                $this->assertSame($expected, array_map(fn ($a) => $a->getAttribute('href'), iterator_to_array($links)));
                if ($key === 'knowledge-toc') $this->assertCount(0, $xpath->query('//nav//img'));
                foreach ($styles as $other => $wrapper) if ($other !== $key) $this->assertCount(0, $xpath->query('//nav[@class="'.$wrapper.'"]'));
            }
            $options = app(ArticleTemplateOptions::class)->detail($key, ['display' => ['navigation' => false]]);
            $html = view('article.templates.detail.'.$key, ['article' => $fixture->detailArticle(), 'templateOptions' => $options, 'previousArticle' => $neighbors['previous'], 'nextArticle' => $neighbors['next']])->render();
            $this->assertStringNotContainsString('<nav', $html);
        }
    }

    public function test_new_navigation_escapes_titles_handles_missing_media_and_uses_actual_read_time(): void
    {
        Storage::set('public', Storage::build(['driver' => 'local', 'root' => __DIR__.'/storage-public', 'url' => '/storage']));
        Storage::disk('public')->put('large-cover.jpg', 'test');
        Storage::disk('public')->put('small-cover.jpg', 'test');
        $fixture = app(ArticleTemplatePreviewFixture::class);
        $neighbors = $fixture->neighbors();
        $neighbors['previous']->title = '<script>alert("x")</script> & Decisions';
        $neighbors['previous']->content = str_repeat('word ', 441);
        $neighbors['previous']->thumb_l = 'large-cover.jpg';
        $neighbors['previous']->thumb_s = 'small-cover.jpg';
        $neighbors['previous']->setRelation('category', null);
        $neighbors['next']->content = '';
        $neighbors['next']->thumb_l = 'missing.jpg';
        $neighbors['next']->thumb_s = 'small-cover.jpg';
        foreach (['editorial-feature', 'knowledge-toc'] as $key) {
            $html = view('article.templates.detail.'.$key, ['article' => $fixture->detailArticle(), 'templateOptions' => app(ArticleTemplateOptions::class)->detail($key), 'previousArticle' => $neighbors['previous'], 'nextArticle' => $neighbors['next']])->render();
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);
            $this->assertCount(0, $xpath->query('//nav//script'));
            $nav = $xpath->query('//nav')->item(0);
            $this->assertStringContainsString($neighbors['previous']->title, $nav->textContent);
            $this->assertStringContainsString('3 min read', $nav->textContent);
            $this->assertStringContainsString('1 min read', $nav->textContent);
            if ($key === 'editorial-feature') {
                $images = $xpath->query('//nav//img');
                $this->assertCount(2, $images);
                $this->assertStringEndsWith('large-cover.jpg', $images->item(0)->getAttribute('src'));
                $this->assertStringEndsWith('small-cover.jpg', $images->item(1)->getAttribute('src'));
            }
        }
        $neighbors['next']->thumb_s = null;
        $html = view('article.templates.detail.editorial-feature', ['article' => $fixture->detailArticle(), 'templateOptions' => app(ArticleTemplateOptions::class)->detail('editorial-feature'), 'nextArticle' => $neighbors['next']])->render();
        $this->assertStringContainsString('article-image-placeholder.svg', $html);
    }
}
