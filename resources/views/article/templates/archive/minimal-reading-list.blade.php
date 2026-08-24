<section class="article-page article-page--reading-list">
    <div class="article-shell">
        <header class="article-page-heading">
            <p class="article-eyebrow">{{ t('Journal') }}</p>
            <h1>{{ t('Minimal Reading List') }}</h1>
            <p>{{ t('Thoughtful reads on design, technology, and the web.') }}</p>
        </header>

        <form class="article-filter-bar" action="{{ route('cms.core.article') }}" method="get" data-article-filter>
            <label class="visually-hidden" for="article-search">{{ t('Search articles') }}</label>
            <input id="article-search" name="search" type="search" value="{{ request('search') }}" placeholder="{{ t('Search articles') }}">
            <button type="submit" class="btn ph-btn-theme">{{ t('Search') }}</button>
        </form>

        <div class="article-reading-list" data-article-list>
            @forelse ($articles as $article)
                @php($readingMinutes = max(1, (int) ceil(str_word_count(strip_tags((string) $article->content)) / 220)))
                @php($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                <article class="article-reading-list__item">
                    @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-reading-list__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])
                    <div class="article-reading-list__body">
                        <div class="article-meta"><span>{{ $article->category?->name ?? t('Uncategorized') }}</span><span>{{ optional($article->created_at)->format('d M Y') }}</span><span>{{ $readingMinutes }} {{ t('min read') }}</span></div>
                        <h2 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h2>
                        <p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 180) }}</p>
                    </div>
                </article>
            @empty
                <p class="article-empty">{{ t('No articles found') }}</p>
            @endforelse
        </div>

        @include('article.templates.partials.pagination', ['articles' => $articles])
    </div>
</section>
