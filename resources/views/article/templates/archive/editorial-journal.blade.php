<section class="article-page article-page--editorial-journal">
    <div class="article-shell">
        <header class="article-page-heading">
            <p class="article-eyebrow">{{ t('Stories') }}</p>
            <h1>{{ t('Editorial Journal') }}</h1>
            <p>{{ t('Featured ideas and useful perspectives.') }}</p>
        </header>

        @php($lead = $articles->first())
        @if ($lead)
            @php($leadThumbnailUrl = $lead->thumb_l && Storage::disk('public')->exists($lead->thumb_l) ? Storage::url($lead->thumb_l) : asset('assets/images/article/article-image-placeholder.svg'))
            <article class="article-editorial-lead">
                <div class="article-editorial-lead__body"><span class="article-chip">{{ $lead->category?->name ?? t('Uncategorized') }}</span><h2 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $lead->uri) }}">{{ $lead->title }}</a></h2><p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $lead->content)), 220) }}</p><a class="article-text-link" href="{{ route('cms.core.article.detail', $lead->uri) }}">{{ t('Read article') }} <i class="fas fa-arrow-right"></i></a></div>
                @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $lead->uri), 'class' => 'article-editorial-lead__media', 'mediaUrl' => $leadThumbnailUrl, 'title' => $lead->title])
            </article>
        @endif

        <div class="article-editorial-grid">
            @foreach ($articles->slice(1) as $article)
                @php($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                <article class="article-editorial-card">@include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-editorial-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])<span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span><h2 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h2><p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 120) }}</p><small>{{ optional($article->created_at)->format('d M Y') }}</small></article>
            @endforeach
        </div>

        @if ($articles->isEmpty())<p class="article-empty">{{ t('No articles found') }}</p>@endif
        @include('article.templates.partials.pagination', ['articles' => $articles])
    </div>
</section>
