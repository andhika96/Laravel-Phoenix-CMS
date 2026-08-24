<section class="article-page article-page--balanced-grid">
    <div class="article-shell">
        <header class="article-page-heading article-page-heading--inline"><div><p class="article-eyebrow">{{ t('Explore') }}</p><h1>{{ t('Balanced Card Grid') }}</h1></div><form class="article-filter-select" action="{{ route('cms.core.article') }}" method="get" data-article-filter><label for="article-category">{{ t('Category') }}</label><input id="article-category" name="category" type="number" min="1" value="{{ request('category') }}" placeholder="{{ t('All') }}"><button type="submit" class="btn btn-outline-secondary">{{ t('Filter') }}</button></form></header>
        <div class="article-card-grid">
            @forelse ($articles as $article)
                @php($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                <article class="article-card">@include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])<div class="article-card__body"><span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span><h2 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h2><p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 130) }}</p><small>{{ optional($article->created_at)->format('d M Y') }}</small></div></article>
            @empty
                <p class="article-empty">{{ t('No articles found') }}</p>
            @endforelse
        </div>
        @include('article.templates.partials.pagination', ['articles' => $articles])
    </div>
</section>
