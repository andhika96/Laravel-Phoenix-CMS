@php
    $templateOptions = app(\App\Support\Article\ArticleTemplateOptions::class)->archive('mosaic-classic', $templateOptions ?? []);
    $classic = $templateOptions['mosaic_classic'];
    $grid = $templateOptions['grid'];
    $filtered = trim((string) request('search', '')) !== '' || request()->filled('category') || request()->filled('tag');
    $lead = $filtered ? null : $articles->first();
    $sidebarArticles = $filtered ? collect() : $articles->slice(1, $classic['side_count']);
    $gridArticles = $filtered ? $articles->getCollection() : $articles->slice(1 + $classic['side_count']);
    $classicStyle = '';
    foreach (['desktop', 'tablet', 'mobile'] as $device) {
        $classicStyle .= '--classic-lead-height-'.$device.':'.$classic['lead_height'][$device].';';
        $classicStyle .= '--classic-side-height-'.$device.':'.$classic['side_height'][$device].';';
        $classicStyle .= '--story-gap-'.$device.':'.$classic['gap'][$device].';';
    }
@endphp
<section class="article-page article-page--mosaic-classic" data-classic-divider="{{ $classic['divider'] ? 'true' : 'false' }}" style="{{ $classicStyle }}">
    <div class="article-shell" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        @include('article.templates.partials.archive-header', ['templateKey' => 'mosaic-classic', 'defaultEyebrow' => t('Journal'), 'defaultTitle' => t('Mosaic Classic'), 'defaultDescription' => ''])

        <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
        <div data-article-vue-list-content>
            @if ($filtered)
                <div class="article-story-results" role="status"><span>{{ number_format($articles->total()) }} {{ t('articles') }}@if(request()->filled('search')) · {{ request('search') }}@endif</span><a href="{{ route('cms.core.article') }}" data-article-category-link>{{ t('Clear filters') }} <i class="fas fa-times" aria-hidden="true"></i></a></div>
            @endif

            @if ($lead)
                @php
                    $leadThumbnailUrl = $lead->thumb_l && Storage::disk('public')->exists($lead->thumb_l) ? Storage::url($lead->thumb_l) : asset('assets/images/article/article-image-placeholder.svg');
                    $article = $lead;
                @endphp
                <div class="article-classic-layout" data-classic-side="{{ $classic['side_position'] }}" data-classic-ratio="{{ $classic['lead_ratio'] }}" data-classic-has-side="{{ $sidebarArticles->isNotEmpty() ? 'true' : 'false' }}">
                    <article class="article-classic-lead" data-classic-region="lead">
                        @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $lead->uri), 'class' => 'article-classic-lead__media', 'mediaUrl' => $leadThumbnailUrl, 'title' => $lead->title])
                        <div class="article-classic-lead__body">
                            @if ($classic['show_category'])<span class="article-chip">{{ $lead->category?->name ?? t('Uncategorized') }}</span>@endif
                            @include('article.templates.partials.archive-title')
                            @if ($classic['show_excerpt'])<p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $lead->content)), 220) }}</p>@endif
                            @if ($classic['show_date'] && $lead->created_at)<time datetime="{{ $lead->created_at->toDateString() }}">{{ $lead->created_at->format('d M Y') }}</time>@endif
                            @if ($classic['show_read_more'])<a class="article-text-link" href="{{ route('cms.core.article.detail', $lead->uri) }}">{{ t('Read article') }} <i class="fas fa-arrow-right" aria-hidden="true"></i><span class="visually-hidden">: {{ $lead->title }}</span></a>@endif
                        </div>
                    </article>

                    @if ($sidebarArticles->isNotEmpty())
                    <aside class="article-classic-sidebar" data-classic-region="side" data-classic-side-layout="{{ $classic['side_layout'] }}" aria-label="{{ t('More stories') }}">
                        @foreach ($sidebarArticles as $article)
                            @php
                                $thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg');
                            @endphp
                            <article class="article-classic-sidebar__item">
                                @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-classic-sidebar__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])
                                <div>
                                    @if ($classic['side_numbers'])<span class="article-classic-number" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>@endif
                                    @if ($classic['show_category'])<span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span>@endif
                                    @include('article.templates.partials.archive-title')
                                    @if ($classic['show_excerpt'])<p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 100) }}</p>@endif
                                    @if ($classic['show_date'] && $article->created_at)<time datetime="{{ $article->created_at->toDateString() }}">{{ $article->created_at->format('d M Y') }}</time>@endif
                                    @if ($classic['show_read_more'])<a class="article-text-link" href="{{ route('cms.core.article.detail', $article->uri) }}">{{ t('Read article') }} <i class="fas fa-arrow-right" aria-hidden="true"></i><span class="visually-hidden">: {{ $article->title }}</span></a>@endif
                                </div>
                            </article>
                        @endforeach
                    </aside>
                    @endif
                </div>
            @endif

            @if ($gridArticles->isNotEmpty())
            <div class="article-card-grid article-classic-grid" data-classic-region="grid" style="--article-template-grid-desktop:{{ $grid['desktop'] }};--article-template-grid-tablet:{{ $grid['tablet'] }};--article-template-grid-mobile:{{ $grid['mobile'] }}">
                @foreach ($gridArticles as $article)
                    @php
                        $thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg');
                    @endphp
                    <article class="article-card">
                        @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])
                        <div class="article-card__body">
                            @if ($classic['show_category'])<span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span>@endif
                            @include('article.templates.partials.archive-title')
                            @if ($classic['show_excerpt'])<p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 150) }}</p>@endif
                            @if ($classic['show_date'] && $article->created_at)<time datetime="{{ $article->created_at->toDateString() }}">{{ $article->created_at->format('d M Y') }}</time>@endif
                            @if ($classic['show_read_more'])<a class="article-text-link" href="{{ route('cms.core.article.detail', $article->uri) }}">{{ t('Read article') }} <i class="fas fa-arrow-right" aria-hidden="true"></i><span class="visually-hidden">: {{ $article->title }}</span></a>@endif
                        </div>
                    </article>
                @endforeach
            </div>
            @endif

            @if ($articles->isEmpty())<p class="article-empty">{{ t('No articles found') }}</p>@endif
        </div>
        <div class="article-vue-control-slot" data-article-vue-control-slot></div>
        @include('article.templates.partials.pagination', ['articles' => $articles])
    </div>
</section>
