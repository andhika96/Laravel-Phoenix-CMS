<section class="article-page article-page--mosaic-classic">
    <div class="article-shell">
        <header class="article-page-heading article-page-heading--inline">
            <div><p class="article-eyebrow">{{ t('Journal') }}</p><h1>{{ t('Mosaic Classic') }}</h1></div>
            <form class="article-search-icon" action="{{ route('cms.core.article') }}" method="get" data-article-filter>
                <label class="visually-hidden" for="article-classic-search">{{ t('Search articles') }}</label>
                <input id="article-classic-search" name="search" type="search" value="{{ request('search') }}" placeholder="{{ t('Search') }}">
                <button type="submit" aria-label="{{ t('Search') }}"><i class="fas fa-search"></i></button>
            </form>
        </header>

        @php($lead = $articles->first())
        @php($sidebarArticles = $articles->slice(1, 3))
        @php($gridArticles = $articles->slice(4))

        @if ($lead)
            @php($leadThumbnailUrl = $lead->thumb_l && Storage::disk('public')->exists($lead->thumb_l) ? Storage::url($lead->thumb_l) : asset('assets/images/article/article-image-placeholder.svg'))
            <div class="article-classic-layout">
                <article class="article-classic-lead">
                    @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $lead->uri), 'class' => 'article-classic-lead__media', 'mediaUrl' => $leadThumbnailUrl, 'title' => $lead->title])
                    <div class="article-classic-lead__body"><span class="article-chip">{{ $lead->category?->name ?? t('Uncategorized') }}</span><h2 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $lead->uri) }}">{{ $lead->title }}</a></h2><p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $lead->content)), 220) }}</p></div>
                </article>

                <aside class="article-classic-sidebar">
                    @foreach ($sidebarArticles as $article)
                        @php($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                        <article class="article-classic-sidebar__item">@include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-classic-sidebar__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])<div><span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span><h2 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h2><p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 76) }}</p><small>{{ optional($article->created_at)->format('d M Y') }}</small></div></article>
                    @endforeach
                </aside>
            </div>
        @endif

        <div class="article-card-grid">
            @foreach ($gridArticles as $article)
                @php($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                <article class="article-card">@include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])<div class="article-card__body"><span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span><h2 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h2><p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 130) }}</p></div></article>
            @endforeach
        </div>

        @if ($articles->isEmpty())<p class="article-empty">{{ t('No articles found') }}</p>@endif
        @include('article.templates.partials.pagination', ['articles' => $articles])
    </div>
</section>
