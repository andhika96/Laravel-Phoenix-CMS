@php
    $templateOptions = app(\App\Support\Article\ArticleTemplateOptions::class)->archive('balanced-card-grid', $templateOptions ?? []);
    $balanced = $templateOptions['balanced_grid'];
    $grid = $templateOptions['grid'];
    $balancedStyle = '--balanced-card-padding:'.$balanced['card_padding'].';--balanced-card-radius:'.$balanced['card_radius'].';--balanced-excerpt-lines:'.$balanced['excerpt_lines'].';';
    foreach (['desktop', 'tablet', 'mobile'] as $device) $balancedStyle .= '--story-gap-'.$device.':'.$balanced['gap'][$device].';';
@endphp
<section class="article-page article-page--balanced-grid" data-card-style="{{ $balanced['card_style'] }}" data-card-align="{{ $balanced['text_align'] }}" style="{{ $balancedStyle }}">
    <div class="article-shell" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        @include('article.templates.partials.archive-header', ['templateKey' => 'balanced-card-grid', 'defaultEyebrow' => t('Explore'), 'defaultTitle' => t('Balanced Card Grid'), 'defaultDescription' => ''])
        <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
        <div data-article-vue-list-content>
            @if (request()->filled('search') || request()->filled('category') || request()->filled('tag'))
                <div class="article-story-results" role="status"><span>{{ number_format($articles->total()) }} {{ t('articles') }}@if(request()->filled('search')) · {{ request('search') }}@endif</span><a href="{{ route('cms.core.article') }}" data-article-category-link>{{ t('Clear filters') }} <i class="fas fa-times" aria-hidden="true"></i></a></div>
            @endif
            <div class="article-card-grid" style="--article-template-grid-desktop:{{ $grid['desktop'] }};--article-template-grid-tablet:{{ $grid['tablet'] }};--article-template-grid-mobile:{{ $grid['mobile'] }}">
                @forelse ($articles as $article)
                    @php
                        $thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg');
                    @endphp
                    <article class="article-card">
                        @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])
                        <div class="article-card__body">
                            @if ($balanced['show_category'])<span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span>@endif
                            @include('article.templates.partials.archive-title')
                            @if ($balanced['show_excerpt'])<p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 300) }}</p>@endif
                            <div class="article-balanced-footer">
                                @if ($balanced['show_date'] && $article->created_at)<time datetime="{{ $article->created_at->toDateString() }}">{{ $article->created_at->format('d M Y') }}</time>@endif
                                @if ($balanced['show_read_more'])<a class="article-text-link" href="{{ route('cms.core.article.detail', $article->uri) }}">{{ t('Read article') }} <i class="fas fa-arrow-right" aria-hidden="true"></i><span class="visually-hidden">: {{ $article->title }}</span></a>@endif
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="article-empty">{{ t('No articles found') }}</p>
                @endforelse
            </div>
        </div>
        <div class="article-vue-control-slot" data-article-vue-control-slot></div>
        @include('article.templates.partials.pagination', ['articles' => $articles])
    </div>
</section>
