<?php

namespace App\Http\Controllers\Web\Manage_Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\UpdateArticleTemplateSettingRequest;
use App\Models\Article\ArticleTemplateSetting;
use App\Support\Article\ArticleTemplateCatalog;
use App\Support\Article\ArticleTemplatePreviewFixture;
use Illuminate\Http\Request;

class ManageArticleTemplateController extends Controller
{
    public function __construct(
        private readonly ArticleTemplateCatalog $catalog,
        private readonly ArticleTemplatePreviewFixture $previewFixture,
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
        ]);
    }

    public function update(UpdateArticleTemplateSettingRequest $request): mixed
    {
        $settings = ArticleTemplateSetting::current();
        $settings->fill([
            'archive_template' => $request->string('archive_template')->toString(),
            'detail_template' => $request->string('detail_template')->toString(),
            'archive_per_page' => $request->integer('archive_per_page'),
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
        if ($surface === 'archive') {
            return view('manage_article.templates.preview', [
                'surface' => $surface,
                'templateView' => $this->catalog->view($surface, $template),
                'articles' => $this->previewFixture->archivePaginator(
                    $request->integer('page', 1),
                    route('cms.core.manage_article.templates.preview', ['surface' => $surface, 'template' => $template])
                ),
                'templateSettings' => $settings,
                'article' => null,
                'previousArticle' => null,
                'nextArticle' => null,
                'isPreviewFixture' => true,
            ]);
        }

        $neighbors = $this->previewFixture->neighbors();

        return view('manage_article.templates.preview', [
            'surface' => $surface,
            'templateView' => $this->catalog->view($surface, $template),
            'article' => $this->previewFixture->detailArticle(),
            'templateSettings' => $settings,
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
}
