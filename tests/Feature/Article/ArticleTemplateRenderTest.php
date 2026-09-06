<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use App\Support\Article\ArticleTemplateCatalog;
use App\Support\Article\ArticleTemplateOptions;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ArticleTemplateRenderTest extends TestCase
{
    public function test_every_registered_article_template_has_a_blade_view(): void
    {
        $catalog = app(ArticleTemplateCatalog::class);

        foreach ([$catalog->archive(), $catalog->detail()] as $templates) {
            foreach ($templates as $template) {
                $this->assertTrue(view()->exists($template['view']), $template['view'].' must exist');
            }
        }
    }

    public function test_archive_templates_use_accessible_article_links_and_numbered_pagination_contract(): void
    {
        foreach ([
            'minimal-reading-list',
            'mosaic-magazine',
            'editorial-journal',
            'mosaic-classic',
            'balanced-card-grid',
        ] as $key) {
            $source = file_get_contents(resource_path('views/article/templates/archive/'.$key.'.blade.php'));

            $this->assertStringContainsString("route('cms.core.article.detail'", $source);
            $this->assertStringContainsString("article.templates.partials.pagination", $source);
            $this->assertStringContainsString('article-image-placeholder.svg', $source);
            $this->assertStringContainsString('article-excerpt-clamp', $source);
            $this->assertStringContainsString('article.templates.partials.media-link', $source);
            $this->assertStringContainsString('article.templates.partials.archive-title', $source);
            $this->assertStringContainsString('article.templates.partials.template-shell-attributes', $source);
            $this->assertStringNotContainsString('<h4 class="article-title-clamp">', $source);
        }

        $sharedMedia = file_get_contents(resource_path('views/article/templates/partials/media-link.blade.php'));
        $this->assertStringContainsString('article-media-frame', $sharedMedia);
        $this->assertStringContainsString('article-background-media', $sharedMedia);

        foreach ([
            'focused-reader',
            'editorial-feature',
            'knowledge-toc',
        ] as $key) {
            $source = file_get_contents(resource_path('views/article/templates/detail/'.$key.'.blade.php'));

            $this->assertStringContainsString('article-image-placeholder.svg', $source);
            $this->assertStringContainsString('article.templates.partials.detail-navigation', $source);
            $this->assertStringContainsString('article-detail__header', $source);
            $this->assertStringContainsString('article.templates.partials.template-shell-attributes', $source);
        }

        $pagination = file_get_contents(resource_path('views/article/templates/partials/pagination.blade.php'));
        $this->assertStringContainsString('aria-label="{{ t(\'Article pagination\') }}"', $pagination);
        $this->assertStringContainsString("aria-current=\"page\"", $pagination);
    }

    public function test_mosaic_classic_renders_a_placeholder_when_an_article_has_no_thumbnail(): void
    {
        $article = new Article([
            'uri' => 'mosaic-classic-test',
            'title' => 'Mosaic Classic Test',
            'content' => '<p>Preview content</p>',
        ]);
        $article->setRelation('category', null);
        $article->created_at = now();
        $articles = new LengthAwarePaginator([$article], 1, 12, 1, [
            'path' => route('cms.core.article'),
        ]);

        $html = view('article.templates.archive.mosaic-classic', [
            'articles' => $articles,
            'templateSettings' => null,
        ])->render();

        $this->assertStringContainsString('Mosaic Classic', $html);
        $this->assertStringContainsString('article-image-placeholder.svg', $html);
        $this->assertStringContainsString('mosaic-classic-test', $html);
    }

    public function test_every_archive_renderer_places_stable_vue_slots_inside_the_shell_before_server_pagination(): void
    {
        $article = new Article([
            'uri' => 'archive-footer-slot-test',
            'title' => 'Archive Footer Slot Test',
            'content' => '<p>Preview content</p>',
        ]);
        $article->setRelation('category', null);
        $article->created_at = now();
        $articles = new LengthAwarePaginator([$article], 24, 12, 1, [
            'path' => route('cms.core.article'),
        ]);

        foreach (app(ArticleTemplateCatalog::class)->archive() as $key => $template) {
            $html = view($template['view'], [
                'articles' => $articles,
                'templateSettings' => null,
                'templateOptions' => [],
                'articleCategories' => collect(),
            ])->render();

            $shell = strpos($html, 'article-shell');
            $stateSlot = strpos($html, 'data-article-vue-list-state-slot');
            $listContent = strpos($html, 'data-article-vue-list-content');
            $controlSlot = strpos($html, 'data-article-vue-control-slot');
            $pagination = strpos($html, 'article-pagination--ssr');

            $this->assertNotFalse($shell, $key.' must render an article shell');
            $this->assertNotFalse($stateSlot, $key.' must render a stable Vue loading slot');
            $this->assertNotFalse($listContent, $key.' must render the Vue list content wrapper');
            $this->assertNotFalse($controlSlot, $key.' must render a stable Vue pagination slot');
            $this->assertNotFalse($pagination, $key.' must render SSR pagination fallback');
            $this->assertGreaterThan($shell, $stateSlot, $key.' loading slot must stay inside its article shell');
            $this->assertGreaterThan($stateSlot, $listContent, $key.' list content must follow the loading slot');
            $this->assertGreaterThan($listContent, $controlSlot, $key.' pagination slot must follow the list content');
            $this->assertGreaterThan($controlSlot, $pagination, $key.' SSR pagination must remain after the Vue pagination slot');
        }
    }

    public function test_public_pagination_uses_the_cms_visual_contract_with_result_context(): void
    {
        $articles = new LengthAwarePaginator(range(1, 12), 96, 12, 5, [
            'path' => route('cms.core.article'),
        ]);

        $html = view('article.templates.partials.pagination', ['articles' => $articles])->render();

        $this->assertStringContainsString('article-pagination__summary', $html);
        $this->assertStringContainsString('Showing 49–60 of 96', $html);
        $this->assertStringContainsString('ph-pagination', $html);
        $this->assertStringContainsString('aria-label="First page"', $html);
        $this->assertStringContainsString('aria-label="Last page"', $html);
        $this->assertStringContainsString('data-article-pagination-link', $html);
    }

    public function test_pagination_model_visual_overrides_and_fontawesome_arrows_reach_ssr_markup(): void
    {
        $articles = new LengthAwarePaginator(range(1, 12), 96, 12, 5, [
            'path' => route('cms.core.article'),
        ]);
        $options = app(ArticleTemplateOptions::class)->archive('minimal-reading-list', [
            'pagination' => [
                'type' => 'soft',
                'item_radius' => '1rem',
                'item_gap' => '10px',
                'item_active_background_color' => '#16a579',
                'item_active_text_color' => '#ffffff',
                'previous_icon' => 'fas fa-angle-left',
                'next_icon' => 'fas fa-arrow-right',
            ],
        ]);

        $html = view('article.templates.partials.pagination', [
            'articles' => $articles,
            'templateOptions' => $options,
        ])->render();

        $this->assertStringContainsString('article-pagination--model-soft', $html);
        $this->assertStringContainsString('--article-pagination-item-radius:1rem', $html);
        $this->assertStringContainsString('--article-pagination-item-gap:10px', $html);
        $this->assertStringContainsString('--article-pagination-item-active-background:#16a579', $html);
        $this->assertStringContainsString('fas fa-angle-left', $html);
        $this->assertStringContainsString('fas fa-arrow-right', $html);
    }

    public function test_shared_archive_fragments_render_safe_heading_media_and_shell_variables(): void
    {
        $article = new Article([
            'uri' => 'styled-article',
            'title' => 'Styled Article',
            'content' => '<p>Preview content</p>',
        ]);
        $options = app(ArticleTemplateOptions::class)->archive('minimal-reading-list', [
            'article_title' => ['tag' => 'h2'],
            'thumbnail' => [
                'mode' => 'asset',
                'fit' => 'contain',
                'frame' => ['enabled' => true, 'border_width' => '2px', 'radius' => '8px'],
            ],
            'shell' => [
                'padding' => ['enabled' => true, 'desktop' => ['top' => '2rem', 'right' => '1rem', 'bottom' => '2rem', 'left' => '1rem']],
                'frame' => ['enabled' => true, 'border_width' => '1px', 'radius' => '12px'],
            ],
        ]);

        $title = view('article.templates.partials.archive-title', compact('article', 'options'))->render();
        $media = view('article.templates.partials.media-link', [
            'href' => route('cms.core.article.detail', $article->uri),
            'class' => 'article-reading-list__media',
            'mediaUrl' => asset('assets/images/article/article-image-placeholder.svg'),
            'title' => $article->title,
            'templateOptions' => $options,
        ])->render();
        $shell = view('article.templates.partials.template-shell-attributes', ['templateOptions' => $options])->render();

        $this->assertStringContainsString('<h2 class="article-title-clamp">', $title);
        $this->assertStringContainsString('article-asset-media', $media);
        $this->assertStringContainsString('article-thumbnail-frame--custom', $media);
        $this->assertStringContainsString('data-article-shell-padding="true"', $shell);
        $this->assertStringContainsString('--article-shell-padding-desktop-top:2rem', $shell);
        $this->assertStringContainsString('data-article-shell-frame="true"', $shell);
    }

    public function test_thumbnail_modes_render_distinct_background_and_asset_markup_with_background_height(): void
    {
        $normalizer = app(ArticleTemplateOptions::class);
        $mediaArguments = [
            'href' => route('cms.core.article.detail', 'thumbnail-mode-test'),
            'class' => 'article-reading-list__media',
            'mediaUrl' => asset('assets/images/article/article-image-placeholder.svg'),
            'title' => 'Thumbnail mode test',
        ];

        $backgroundOptions = $normalizer->archive('minimal-reading-list', [
            'thumbnail' => ['mode' => 'background', 'fit' => 'contain', 'height' => '12rem'],
        ]);
        $background = view('article.templates.partials.media-link', $mediaArguments + [
            'templateOptions' => $backgroundOptions,
        ])->render();

        $assetOptions = $normalizer->archive('minimal-reading-list', [
            'thumbnail' => ['mode' => 'asset', 'fit' => 'contain', 'height' => '12rem'],
        ]);
        $asset = view('article.templates.partials.media-link', $mediaArguments + [
            'templateOptions' => $assetOptions,
        ])->render();

        $this->assertStringContainsString('article-background-media', $background);
        $this->assertStringContainsString('--article-media-image:url(', $background);
        $this->assertStringContainsString('--article-thumbnail-height:12rem', $background);
        $this->assertStringNotContainsString('<img', $background);
        $this->assertStringContainsString('article-asset-media', $asset);
        $this->assertStringContainsString('<img src=', $asset);
        $this->assertStringNotContainsString('article-background-media', $asset);
        $this->assertStringNotContainsString('--article-thumbnail-height:', $asset);
    }

    public function test_every_archive_and_detail_renderer_receives_normalized_structured_template_styles(): void
    {
        $article = new Article([
            'uri' => 'template-style-render-test',
            'title' => 'Template Style Render Test',
            'content' => '<p>Preview content</p><h2>Section</h2>',
        ]);
        $article->setRelation('category', null);
        $article->setRelation('author', null);
        $article->created_at = now();
        $articles = new LengthAwarePaginator([$article], 24, 12, 1, ['path' => route('cms.core.article')]);
        $normalizer = app(ArticleTemplateOptions::class);

        foreach (app(ArticleTemplateCatalog::class)->archive() as $key => $template) {
            $options = $normalizer->archive($key, [
                'thumbnail' => ['mode' => 'asset', 'fit' => 'contain', 'frame' => ['enabled' => true]],
                'shell' => ['padding' => ['enabled' => true, 'desktop' => ['top' => '2rem', 'right' => '1rem', 'bottom' => '2rem', 'left' => '1rem']]],
            ]);
            $html = view($template['view'], compact('articles', 'options') + [
                'templateOptions' => $options,
                'templateSettings' => null,
                'articleCategories' => collect(),
            ])->render();

            $this->assertStringContainsString('data-article-shell-padding="true"', $html, $key.' must receive shell padding variables');
            $this->assertStringContainsString('--article-shell-padding-desktop-top:2rem', $html, $key.' must expose normalized shell variables');
            $this->assertStringContainsString('article-asset-media', $html, $key.' must use the shared asset thumbnail mode');
        }

        foreach (app(ArticleTemplateCatalog::class)->detail() as $key => $template) {
            $options = $normalizer->detail($key, [
                'shell' => ['frame' => ['enabled' => true, 'border_width' => '2pt', 'radius' => '1rem']],
            ]);
            $html = view($template['view'], [
                'article' => $article,
                'templateOptions' => $options,
                'templateSettings' => null,
                'previousArticle' => null,
                'nextArticle' => null,
            ])->render();

            $this->assertStringContainsString('data-article-shell-frame="true"', $html, $key.' must receive the detail shell frame');
            $this->assertStringContainsString('--article-shell-frame-border-width:2pt', $html, $key.' must expose normalized detail shell variables');
        }
    }
}
