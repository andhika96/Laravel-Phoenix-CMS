<?php

namespace Tests\Feature\Article;

use App\Http\Controllers\Web\Manage_Article\ManageArticleTemplateController;
use App\Models\Article\ArticleTemplateSetting;
use App\Models\Awesome_Admin\Account;
use App\Support\Article\ArticleTemplateOptions;
use App\Support\Article\ArticleTemplatePreviewFixture;
use Illuminate\Http\Request;

class FocusedReaderTest extends ManageArticleTemplateTest
{
    public function test_reader_options_are_bounded_idempotent_and_isolated(): void
    {
        $normalizer = app(ArticleTemplateOptions::class);
        $input = ['focused_reader' => ['title_size' => ['desktop' => '48;display:none', 'tablet' => 999, 'mobile' => -5], 'body_leading' => ['mobile' => []], 'reading_width' => 720, 'cover_ratio' => ['mobile' => 'url(evil)'], 'cover_enabled' => 'false', 'cover_caption' => '<b>Caption</b>', 'unknown' => true]];
        $options = $normalizer->detail('focused-reader', $input);
        $reader = $options['focused_reader'];
        $this->assertSame(48.0, $reader['title_size']['desktop']);
        $this->assertSame(80.0, $reader['title_size']['tablet']);
        $this->assertSame(24.0, $reader['title_size']['mobile']);
        $this->assertSame(1.75, $reader['body_leading']['mobile']);
        $this->assertSame('4/3', $reader['cover_ratio']['mobile']);
        $this->assertFalse($reader['cover_enabled']);
        $this->assertSame('Caption', $reader['cover_caption']);
        $this->assertArrayNotHasKey('unknown', $reader);
        $this->assertSame($options, $normalizer->detail('focused-reader', $options));
        $this->assertArrayNotHasKey('focused_reader', $normalizer->detail('knowledge-toc', $input));
        $this->assertArrayNotHasKey('focused_reader', $normalizer->detail('editorial-feature', $input));
    }

    public function test_reader_render_honors_visibility_caption_and_shell(): void
    {
        $fixture = app(ArticleTemplatePreviewFixture::class);
        $data = ['article' => $fixture->detailArticle(), 'previousArticle' => $fixture->neighbors()['previous'], 'nextArticle' => $fixture->neighbors()['next'], 'templateOptions' => ['header' => ['title' => ['enabled' => false], 'description' => ['enabled' => false], 'eyebrow' => ['enabled' => false]], 'display' => ['date' => false, 'author' => false, 'reading_time' => false, 'navigation' => false], 'focused_reader' => ['cover_enabled' => false], 'shell' => ['padding' => ['enabled' => true, 'mobile' => ['left' => '12px']]]]];
        $html = view('article.templates.detail.focused-reader', $data)->render();
        foreach (['<h1', '<figure', 'class="article-detail__meta"', '<nav', 'class="article-detail__dek"', 'class="article-reader-category"'] as $absent) $this->assertStringNotContainsString($absent, $html);
        $this->assertStringContainsString('--article-shell-padding-mobile-left:12px', $html);
        $data['templateOptions'] = ['focused_reader' => ['cover_caption' => '<script>alert(1)</script>Safe & sound', 'metadata_position' => 'before-summary', 'navigation_thumbnails' => true]];
        $html = view('article.templates.detail.focused-reader', $data)->render();
        $this->assertStringContainsString('Safe &amp; sound', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('data-reader-meta-position="before-summary"', $html);
        $this->assertStringContainsString('data-reader-thumbnails="true"', $html);
        $this->assertStringContainsString('min read', $html);
        $this->assertStringContainsString('article-detail-navigation', $html);
    }

    public function test_saved_reader_options_match_preview_and_rendered_variables(): void
    {
        $admin = Account::create(['username' => 'reader-admin', 'email' => 'reader@example.test']);
        $options = ['focused_reader' => ['reading_width' => 720, 'title_size' => ['desktop' => 52, 'tablet' => 38, 'mobile' => 30], 'alignment' => ['mobile' => 'center'], 'cover_ratio' => ['mobile' => '3/2'], 'cover_caption' => 'Saved caption', 'body_leading' => ['mobile' => 1.9]]];
        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.templates.update'), ['archive_template' => 'minimal-reading-list', 'detail_template' => 'focused-reader', 'archive_per_page' => 12, 'detail_template_options' => ['focused-reader' => $options]])->assertOk()->assertJsonPath('success', true);
        $saved = ArticleTemplateSetting::current()->detail_template_options['focused-reader'];
        $this->assertEquals(30, $saved['focused_reader']['title_size']['mobile']);
        $controller = app(ManageArticleTemplateController::class);
        $preview = $controller->preview(Request::create('/preview'), 'detail', 'focused-reader')->getData();
        $this->assertEquals($saved, $preview['templateOptions']);
        $html = view($preview['templateView'], $preview)->render();
        $this->assertStringContainsString('--reader-title-size-mobile:30px', $html);
        $this->assertStringContainsString('--reader-body-leading-mobile:1.9', $html);
        $this->assertStringContainsString('Saved caption', $html);
        $draft = $controller->preview(Request::create('/preview', 'GET', ['template_options' => json_encode(['focused_reader' => ['reading_width' => 600]])]), 'detail', 'focused-reader')->getData();
        $this->assertEquals(600, $draft['templateOptions']['focused_reader']['reading_width']);
        $this->assertEquals(720, ArticleTemplateSetting::current()->detail_template_options['focused-reader']['focused_reader']['reading_width']);
    }

    public function test_export_actual_blade_and_settings_for_browser_checks(): void
    {
        $fixture = app(ArticleTemplatePreviewFixture::class);
        $article = $fixture->detailArticle();
        $data = ['article' => $article, 'previousArticle' => $fixture->neighbors()['previous'], 'nextArticle' => $fixture->neighbors()['next']];
        $html = view('article.templates.detail.focused-reader', $data)->render();
        file_put_contents(__DIR__.'/runtime-default.html', $html);
        $article->title = str_repeat('LongTitleWithoutSpaces', 15);
        $article->content .= '<p>'.str_repeat('long-url-without-breaks', 50).'</p><table><tbody><tr>'.str_repeat('<td style="min-width:200px">Wide table cell</td>', 8).'</tr></tbody></table><pre>'.str_repeat('long_code_line_', 70).'</pre><img src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%221600%22 height=%22900%22/%3E" width="1600" height="900"><iframe title="Embedded video" width="1600" height="900" src="about:blank"></iframe>';
        file_put_contents(__DIR__.'/runtime-stress.html', view('article.templates.detail.focused-reader', $data)->render());
        $data['templateOptions'] = ['focused_reader' => ['title_size' => ['mobile' => 28], 'alignment' => ['mobile' => 'center'], 'cover_ratio' => ['mobile' => '3/2'], 'navigation_thumbnails' => true, 'cover_enabled' => false]];
        file_put_contents(__DIR__.'/runtime-custom.html', view('article.templates.detail.focused-reader', $data)->render());
        file_put_contents(__DIR__.'/runtime-settings.html', view('manage_article.templates.partials.options-focused-reader')->render());
        file_put_contents(__DIR__.'/runtime-options.json', json_encode(app(ArticleTemplateOptions::class)->detail('focused-reader')));
        $this->assertStringContainsString('<h1>Building Resilient Systems at Scale</h1>', $html);
    }
}
