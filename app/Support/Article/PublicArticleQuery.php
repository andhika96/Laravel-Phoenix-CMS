<?php

namespace App\Support\Article;

use App\Models\Article\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class PublicArticleQuery
{
    public function eligible(): Builder
    {
        return Article::query()
            ->select([
                'id', 'uri', 'user_id', 'category_id', 'title', 'content', 'tags',
                'thumb_s', 'thumb_l', 'created_at', 'updated_at',
            ])
            ->with([
                'category:id,name,code',
                'author:id,fullname,username',
            ])
            ->where('status', 'publish')
            ->where('visibility', 'public')
            ->where('created_at', '<=', now());
    }

    public function builder(Request $request): Builder
    {
        return $this->eligible()
            ->when($request->filled('search'), fn (Builder $query) => $query->where('title', 'like', '%'.trim((string) $request->input('search')).'%'))
            ->when($request->filled('category'), fn (Builder $query) => $query->where('category_id', (int) $request->input('category')))
            ->when($request->filled('tag'), fn (Builder $query) => $query->where('tags', 'like', '%'.trim((string) $request->input('tag')).'%'))
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }
}
