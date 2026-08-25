<?php

namespace App\Support\Article;

use App\Models\Article\Article_Categories;
use Illuminate\Support\Collection;

final class PublicArticleCategoryOptions
{
    public function __construct(private readonly PublicArticleQuery $publicArticleQuery)
    {
    }

    /** @return Collection<int, Article_Categories> */
    public function all(): Collection
    {
        return Article_Categories::query()
            ->select(['id', 'name', 'code'])
            ->where('status', 'active')
            ->whereIn('id', $this->publicArticleQuery->eligible()->reorder()->select('category_id'))
            ->orderBy('name')
            ->get();
    }
}
