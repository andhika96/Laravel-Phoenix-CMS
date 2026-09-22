@php
    $templateOptions = app(\App\Support\Article\ArticleTemplateOptions::class)->archive('mosaic-magazine', $templateOptions ?? []);
    $mosaic = $templateOptions['mosaic_magazine'];
    $grid = $templateOptions['grid'];
    $isFiltered = trim((string) request('search', '')) !== '' || request()->filled('category') || request()->filled('tag');
    $featured = $isFiltered ? null : $articles->first();
    $supporting = $isFiltered ? collect() : $articles->slice(1, $mosaic['supporting_count']);
    $remaining = $isFiltered ? $articles->getCollection() : $articles->slice(1 + $mosaic['supporting_count']);
@endphp
<section class="article-page article-page--mosaic-magazine">
    <div class="article-shell" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        @include('article.templates.partials.archive-header', ['templateKey' => 'mosaic-magazine', 'defaultEyebrow' => t('Journal'), 'defaultTitle' => t('Mosaic Magazine'), 'defaultDescription' => ''])

        <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
        <div data-article-vue-list-content>
            @if ($isFiltered)
                <div class="article-mosaic-results" role="status">
                    <span>{{ number_format($articles->total()) }} {{ t('articles') }}@if(request()->filled('search')) · {{ request('search') }}@endif @if(request()->filled('category')) · {{ collect($articleCategories ?? [])->firstWhere('id', request('category'))?->name ?? t('Category') }}@endif @if(request()->filled('tag')) · {{ request('tag') }}@endif</span>
                    <a href="{{ route('cms.core.article') }}" data-article-category-link>{{ t('Clear filters') }} <i class="fas fa-times" aria-hidden="true"></i></a>
                </div>
            @endif

            @if ($featured)
                @php
                    $featuredThumbnailUrl = $featured->thumb_l && Storage::disk('public')->exists($featured->thumb_l) ? Storage::url($featured->thumb_l) : asset('assets/images/article/article-image-placeholder.svg');
                    $article = $featured;
                @endphp
                <article class="article-mosaic-feature" data-mosaic-region="hero">
                    @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $featured->uri), 'class' => 'article-mosaic-feature__media', 'mediaUrl' => $featuredThumbnailUrl, 'title' => $featured->title])
                    <div class="article-mosaic-feature__body"><span class="article-chip">{{ $featured->category?->name ?? t('Uncategorized') }}</span>@include('article.templates.partials.archive-title')<p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $featured->content)), 180) }}</p><small>{{ optional($featured->created_at)->format('d M Y') }}</small></div>
                </article>
            @endif

            @foreach (['supporting' => $supporting, 'regular' => $remaining] as $region => $posts)
                @continue($posts->isEmpty())
                @php
                    $columns = $region === 'supporting' && !$mosaic['match_grid'] ? $mosaic['columns'] : $grid;
                    $columnStyle = '';
                    foreach ($columns as $device => $count) {
                        $count = $region === 'supporting' && $posts->count() === 1 ? 1 : $count;
                        $tracks = $region === 'supporting' && $count === 2 && $mosaic['proportion'] === 'asymmetric'
                            ? 'minmax(0, 3fr) minmax(0, 2fr)' : 'repeat('.$count.', minmax(0, 1fr))';
                        $columnStyle .= '--mosaic-columns-'.$device.':'.$tracks.';';
                    }
                @endphp
                <div class="article-mosaic-grid article-mosaic-grid--{{ $region }}" data-mosaic-region="{{ $region }}" style="{{ $columnStyle }}">
                @foreach ($posts as $article)
                    @php
                        $thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg');
                    @endphp
                    <article class="article-mosaic-card">
                        @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-mosaic-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])
                        <div><span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span>@include('article.templates.partials.archive-title')<p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 92) }}</p><small>{{ optional($article->created_at)->format('d M Y') }}</small></div>
                    </article>
                @endforeach
                </div>
            @endforeach

            @if ($articles->isEmpty())<p class="article-empty">{{ t('No articles found') }}</p>@endif
        </div>
        <div class="article-vue-control-slot" data-article-vue-control-slot></div>

        @include('article.templates.partials.pagination', ['articles' => $articles])
    </div>
</section>
