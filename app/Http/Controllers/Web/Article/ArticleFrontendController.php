<?php

namespace App\Http\Controllers\Web\Article;

use App\Http\Controllers\Api\Base_API_Rev_Controller;
use App\Http\Controllers\Controller;
use App\Http\Resources\Article\PublicArticleResource;
use App\Models\Article\Article;
use App\Models\Article\ArticleTemplateSetting;
use App\Support\Article\ArticleTemplateCatalog;
use App\Support\Article\ArticleTemplateOptions;
use App\Support\Article\ArticlePasswordAccess;
use App\Support\Article\PublicArticleCategoryOptions;
use App\Support\Article\PublicArticleQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ArticleFrontendController extends Controller
{
    public function __construct(
        private readonly PublicArticleQuery $publicArticleQuery,
        private readonly ArticleTemplateCatalog $templateCatalog,
        private readonly PublicArticleCategoryOptions $publicArticleCategoryOptions,
        private readonly ArticleTemplateOptions $articleTemplateOptions,
        private readonly ArticlePasswordAccess $articlePasswordAccess,
    ) {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index(Request $request): mixed
    {
        return view('article.archive', $this->archiveContext($request));
    }

    public function listData(Request $request): mixed
    {
        $context = $this->archiveContext($request);
        $articles = $context['articles'];

        $api = new Base_API_Rev_Controller();
        $formatted = $api->paginateResponse($articles, PublicArticleResource::class);
        $formatted['html'] = view($context['archiveView'], [
            'articles' => $articles,
            'templateSettings' => $context['templateSettings'],
            'articleCategories' => $context['articleCategories'],
            'templateOptions' => $context['templateOptions'],
        ])->render();

        return $api->setStatusMsg('success')
            ->respondOK($formatted, $formatted['total'] ? t('Data found') : t('No data found'), true);
    }

    public function detail(Request $request, string $idOrSlug): mixed
    {
        $article = $this->publicationCandidate($idOrSlug);
        abort_unless($article, 404);

        if ($article->visibility === 'private') {
            return response()->view('article.private', [], 403);
        }

        if ($article->visibility === 'password_protected' && ! $this->articlePasswordAccess->allows($article, $request)) {
            return view('article.password-protected', [
                'unlockUrl' => route('cms.core.article.unlock', $article->uri),
                'redirectUrl' => route('cms.core.article.detail', $article->uri),
            ]);
        }

        abort_unless(in_array($article->visibility, ['public', 'password_protected'], true), 404);

        return $this->renderDetail($this->detailArticle($article));
    }

    public function unlock(Request $request, string $idOrSlug): mixed
    {
        $article = $this->publicationCandidate($idOrSlug);
        abort_unless($article && $article->visibility === 'password_protected', 404);

        $password = (string) $request->validate([
            'password' => ['required', 'string', 'max:128'],
        ])['password'];

        if (! $this->articlePasswordAccess->attempt($article, $password, $request)) {
            $message = 'The password is incorrect. Please try again.';

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withErrors(['password' => $message])->withInput();
        }

        $redirect = route('cms.core.article.detail', $article->uri);

        return $request->wantsJson()
            ? response()->json(['success' => true, 'redirect' => $redirect])
            : redirect()->to($redirect);
    }

    /** @return array{articles: mixed, templateSettings: ArticleTemplateSetting, archiveView: string, articleCategories: mixed, templateOptions: array} */
    private function archiveContext(Request $request): array
    {
        $this->validateListRequest($request);
        $settings = ArticleTemplateSetting::current();
        $articles = $this->publicArticleQuery
            ->builder($request)
            ->paginate($this->perPage($settings->archive_per_page))
            ->withPath(route('cms.core.article'))
            ->withQueryString();

        return [
            'articles' => $articles,
            'templateSettings' => $settings,
            'archiveView' => $this->templateCatalog->view('archive', $settings->archive_template),
            'articleCategories' => $this->publicArticleCategoryOptions->all(),
            'templateOptions' => $this->articleTemplateOptions->archive(
                $settings->archive_template,
                (array) data_get($settings->archive_template_options, $settings->archive_template, [])
            ),
        ];
    }

    private function publicationCandidate(string $idOrSlug): ?Article
    {
        return Article::query()
            ->select(['id', 'uri', 'visibility', 'password_protected', 'created_at', 'updated_at'])
            ->where(is_numeric($idOrSlug) ? 'id' : 'uri', $idOrSlug)
            ->where('status', 'publish')
            ->where('created_at', '<=', now())
            ->first();
    }

    private function detailArticle(Article $candidate): Article
    {
        $article = Article::query()
            ->select([
                'id', 'uri', 'user_id', 'category_id', 'title', 'content', 'tags',
                'thumb_s', 'thumb_l', 'created_at', 'updated_at', 'visibility',
            ])
            ->with([
                'category:id,name,code',
                'author:id,fullname,username',
            ])
            ->whereKey($candidate->id)
            ->where('status', 'publish')
            ->where('created_at', '<=', now())
            ->whereIn('visibility', ['public', 'password_protected'])
            ->first();

        abort_unless($article, 404);

        return $article;
    }

    private function renderDetail(Article $article): mixed
    {

        $settings = ArticleTemplateSetting::current();
        $neighbors = $this->neighbors($article);

        return view('article.detail', [
            'article' => $article,
            'templateSettings' => $settings,
            'detailView' => $this->templateCatalog->view('detail', $settings->detail_template),
            'previousArticle' => $neighbors['previous'],
            'nextArticle' => $neighbors['next'],
            'templateOptions' => $this->articleTemplateOptions->detail(
                $settings->detail_template,
                (array) data_get($settings->detail_template_options, $settings->detail_template, [])
            ),
        ]);
    }

    private function validateListRequest(Request $request): void
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'integer', 'exists:article_categories,id'],
            'tag' => ['nullable', 'string', 'max:64'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    private function perPage(mixed $value): int
    {
        return in_array((int) $value, [12, 18, 24], true) ? (int) $value : 12;
    }

    /** @return array{previous: ?Article, next: ?Article} */
    private function neighbors(Article $article): array
    {
        $createdAt = $article->created_at;

        $previous = $this->publicArticleQuery
            ->eligible()
            ->where(function (Builder $query) use ($article, $createdAt): void {
                $query->where('created_at', '<', $createdAt)
                    ->orWhere(function (Builder $tie) use ($article, $createdAt): void {
                        $tie->where('created_at', '=', $createdAt)
                            ->where('id', '<', $article->id);
                    });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        $next = $this->publicArticleQuery
            ->eligible()
            ->where(function (Builder $query) use ($article, $createdAt): void {
                $query->where('created_at', '>', $createdAt)
                    ->orWhere(function (Builder $tie) use ($article, $createdAt): void {
                        $tie->where('created_at', '=', $createdAt)
                            ->where('id', '>', $article->id);
                    });
            })
            ->orderBy('created_at')
            ->orderBy('id')
            ->first();

        return ['previous' => $previous, 'next' => $next];
    }
}
