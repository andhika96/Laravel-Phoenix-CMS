<?php

namespace App\Http\Controllers\Web\Article;

use App\Http\Controllers\Api\Base_API_Rev_Controller;
use App\Http\Controllers\Controller;
use App\Http\Resources\Article\PublicArticleResource;
use App\Models\Article\Article;
use App\Models\Article\ArticleTemplateSetting;
use App\Support\Article\ArticleTemplateCatalog;
use App\Support\Article\PublicArticleQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ArticleFrontendController extends Controller
{
    public function __construct(
        private readonly PublicArticleQuery $publicArticleQuery,
        private readonly ArticleTemplateCatalog $templateCatalog,
    ) {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index(Request $request): mixed
    {
        $this->validateListRequest($request);
        $settings = ArticleTemplateSetting::current();
        $articles = $this->publicArticleQuery
            ->builder($request)
            ->paginate($this->perPage($settings->archive_per_page))
            ->withQueryString();

        return view('article.archive', [
            'articles' => $articles,
            'templateSettings' => $settings,
            'archiveView' => $this->templateCatalog->view('archive', $settings->archive_template),
        ]);
    }

    public function listData(Request $request): mixed
    {
        $this->validateListRequest($request);
        $settings = ArticleTemplateSetting::current();
        $articles = $this->publicArticleQuery
            ->builder($request)
            ->paginate($this->perPage($settings->archive_per_page))
            ->withQueryString();

        $api = new Base_API_Rev_Controller();
        $formatted = $api->paginateResponse($articles, PublicArticleResource::class);

        return $api->setStatusMsg($formatted['total'] ? 'success' : 'failed')
            ->respondOK($formatted, $formatted['total'] ? t('Data found') : t('No data found'), (bool) $formatted['total']);
    }

    public function detail(Request $request, string $idOrSlug): mixed
    {
        $article = $this->publicArticleQuery
            ->eligible()
            ->where(is_numeric($idOrSlug) ? 'id' : 'uri', $idOrSlug)
            ->first();

        abort_unless($article, 404);

        $settings = ArticleTemplateSetting::current();
        $neighbors = $this->neighbors($article);

        return view('article.detail', [
            'article' => $article,
            'templateSettings' => $settings,
            'detailView' => $this->templateCatalog->view('detail', $settings->detail_template),
            'previousArticle' => $neighbors['previous'],
            'nextArticle' => $neighbors['next'],
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
