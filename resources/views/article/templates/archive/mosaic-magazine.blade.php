<section class="article-page article-page--mosaic-magazine">
    <div class="article-shell" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        @include('article.templates.partials.archive-header', ['templateKey' => 'mosaic-magazine', 'defaultEyebrow' => t('Journal'), 'defaultTitle' => t('Mosaic Magazine'), 'defaultDescription' => ''])

        <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
        <div data-article-vue-list-content>
            @php($featured = $articles->first())
            @php($supporting = $articles->slice(1, 2))
            @php($remaining = $articles->slice(3))

            @if ($featured)
                @php($featuredThumbnailUrl = $featured->thumb_l && Storage::disk('public')->exists($featured->thumb_l) ? Storage::url($featured->thumb_l) : asset('assets/images/article/article-image-placeholder.svg'))
                <div class="article-mosaic-feature">
                    @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $featured->uri), 'class' => 'article-mosaic-feature__media', 'mediaUrl' => $featuredThumbnailUrl, 'title' => $featured->title])
                    <div class="article-mosaic-feature__body"><span class="article-chip">{{ $featured->category?->name ?? t('Uncategorized') }}</span>@php($article = $featured)@include('article.templates.partials.archive-title')<p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $featured->content)), 180) }}</p><small>{{ optional($featured->created_at)->format('d M Y') }}</small></div>
                </div>
            @endif

            @php($grid = $templateOptions['grid'] ?? ['desktop' => 3, 'tablet' => 2, 'mobile' => 1])
            <div class="article-mosaic-grid" style="--article-template-grid-desktop:{{ $grid['desktop'] }};--article-template-grid-tablet:{{ $grid['tablet'] }};--article-template-grid-mobile:{{ $grid['mobile'] }}">
                @foreach ($supporting->concat($remaining) as $article)
                    @php($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                    <article class="article-mosaic-card">
                        @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-mosaic-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])
                        <div><span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span>@include('article.templates.partials.archive-title')<p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 92) }}</p><small>{{ optional($article->created_at)->format('d M Y') }}</small></div>
                    </article>
                @endforeach
            </div>

            @if ($articles->isEmpty())<p class="article-empty">{{ t('No articles found') }}</p>@endif
        </div>
        <div class="article-vue-control-slot" data-article-vue-control-slot></div>

        @include('article.templates.partials.pagination', ['articles' => $articles])
    </div>
</section>
