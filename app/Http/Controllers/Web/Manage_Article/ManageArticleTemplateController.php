<?php

namespace App\Http\Controllers\Web\Manage_Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\UpdateArticleTemplateSettingRequest;
use App\Models\Article\ArticleTemplateSetting;
use App\Support\Article\ArticleTemplateCatalog;
use App\Support\Article\ArticleTemplatePreviewFixture;
use App\Support\Article\ArticleTemplateOptions;
use App\Support\Article\PublicArticleCategoryOptions;
use Illuminate\Http\Request;

class ManageArticleTemplateController extends Controller
{
    public function __construct(
        private readonly ArticleTemplateCatalog $catalog,
        private readonly ArticleTemplatePreviewFixture $previewFixture,
        private readonly PublicArticleCategoryOptions $publicArticleCategoryOptions,
        private readonly ArticleTemplateOptions $articleTemplateOptions,
    ) {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index(): mixed
    {
        $settings = ArticleTemplateSetting::current();

        return view('manage_article.templates.index', [
            'settings' => $settings,
            'archiveTemplates' => $this->withPreviewUrls($this->catalog->archive()),
            'detailTemplates' => $this->withPreviewUrls($this->catalog->detail()),
            'archiveTemplateOptions' => $this->articleTemplateOptions->archives((array) $settings->archive_template_options),
            'detailTemplateOptions' => $this->articleTemplateOptions->details((array) $settings->detail_template_options),
        ]);
    }

    public function update(UpdateArticleTemplateSettingRequest $request): mixed
    {
        $settings = ArticleTemplateSetting::current();
        $settings->fill([
            'archive_template' => $request->string('archive_template')->toString(),
            'detail_template' => $request->string('detail_template')->toString(),
            'archive_per_page' => $request->integer('archive_per_page'),
            'archive_template_options' => $this->articleTemplateOptions->archives(
                (array) $request->input('archive_template_options', $settings->archive_template_options ?? [])
            ),
            'detail_template_options' => $this->articleTemplateOptions->details(
                (array) $request->input('detail_template_options', $settings->detail_template_options ?? [])
            ),
            'updated_by' => auth()->id(),
        ])->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => t('Article templates saved successfully'),
                'data' => $settings->fresh(),
            ]);
        }

        return redirect()->back()->with('success', t('Article templates saved successfully'));
    }

    public function preview(Request $request, string $surface, string $template): mixed
    {
        abort_unless(in_array($surface, ['archive', 'detail'], true) && $this->catalog->isAllowed($surface, $template), 404);

        $settings = ArticleTemplateSetting::current();
        $templateOptions = $this->previewOptions($request, $settings, $surface, $template);
        if ($surface === 'archive') {
            $previewPath = route('cms.core.manage_article.templates.preview', ['surface' => $surface, 'template' => $template]);
            if ($request->getQueryString()) {
                $previewPath .= '?'.$request->getQueryString();
            }

            return view('manage_article.templates.preview', [
                'surface' => $surface,
                'templateView' => $this->catalog->view($surface, $template),
                'articles' => $this->previewFixture->archivePaginator(
                    $request->integer('page', 1),
                    $previewPath
                ),
                'templateSettings' => $settings,
                'templateOptions' => $templateOptions,
                'article' => null,
                'previousArticle' => null,
                'nextArticle' => null,
                'articleCategories' => $this->publicArticleCategoryOptions->all(),
                'isPreviewFixture' => true,
            ]);
        }

        $neighbors = $this->previewFixture->neighbors();

        return view('manage_article.templates.preview', [
            'surface' => $surface,
            'templateView' => $this->catalog->view($surface, $template),
            'article' => $this->previewFixture->detailArticle(),
            'templateSettings' => $settings,
            'templateOptions' => $templateOptions,
            'articles' => null,
            'previousArticle' => $neighbors['previous'],
            'nextArticle' => $neighbors['next'],
            'isPreviewFixture' => true,
        ]);
    }

    private function withPreviewUrls(array $templates): array
    {
        foreach ($templates as $key => $template) {
            $templates[$key]['preview_image'] = asset(
                $template['preview_image'] ?? 'assets/images/article/article-image-placeholder.svg'
            );
        }

        return $templates;
    }

    private function previewOptions(Request $request, ArticleTemplateSetting $settings, string $surface, string $template): array
    {
        $draft = json_decode((string) $request->query('template_options', ''), true);
        $draft = is_array($draft) ? $draft : [];

        if ($surface === 'archive') {
            return $this->articleTemplateOptions->archive(
                $template,
                $draft !== [] ? $draft : (array) data_get($settings->archive_template_options, $template, [])
            );
        }

        return $this->articleTemplateOptions->detail(
            $template,
            $draft !== [] ? $draft : (array) data_get($settings->detail_template_options, $template, [])
        );
    }
}
