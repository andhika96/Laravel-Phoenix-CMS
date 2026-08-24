<section class="article-page article-page--mosaic-magazine">
    <div class="article-shell">
        <header class="article-page-heading article-page-heading--inline">
            <div><p class="article-eyebrow">{{ t('Journal') }}</p><h1>{{ t('Mosaic Magazine') }}</h1></div>
            <form class="article-search-icon" action="{{ route('cms.core.article') }}" method="get" data-article-filter>
                <label class="visually-hidden" for="article-mosaic-search">{{ t('Search articles') }}</label>
                <input id="article-mosaic-search" name="search" type="search" value="{{ request('search') }}" placeholder="{{ t('Search') }}">
                <button type="submit" aria-label="{{ t('Search') }}"><i class="fas fa-search"></i></button>
            </form>
        </header>

        @php($featured = $articles->first())
        @php($supporting = $articles->slice(1, 2))
        @php($remaining = $articles->slice(3))

        @if ($featured)
            @php($featuredThumbnailUrl = $featured->thumb_l && Storage::disk('public')->exists($featured->thumb_l) ? Storage::url($featured->thumb_l) : asset('assets/images/article/article-image-placeholder.svg'))
            <div class="article-mosaic-feature">
                @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $featured->uri), 'class' => 'article-mosaic-feature__media', 'mediaUrl' => $featuredThumbnailUrl, 'title' => $featured->title])
                <div class="article-mosaic-feature__body"><span class="article-chip">{{ $featured->category?->name ?? t('Uncategorized') }}</span><h2 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $featured->uri) }}">{{ $featured->title }}</a></h2><p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $featured->content)), 180) }}</p><small>{{ optional($featured->created_at)->format('d M Y') }}</small></div>
            </div>
        @endif

        <div class="article-mosaic-grid">
            @foreach ($supporting->concat($remaining) as $article)
                @php($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                <article class="article-mosaic-card">
                    @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-mosaic-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])
                    <div><span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span><h2 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h2><p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 92) }}</p><small>{{ optional($article->created_at)->format('d M Y') }}</small></div>
                </article>
            @endforeach
        </div>

        @if ($articles->isEmpty())<p class="article-empty">{{ t('No articles found') }}</p>@endif

        @include('article.templates.partials.pagination', ['articles' => $articles])
    </div>
</section>
