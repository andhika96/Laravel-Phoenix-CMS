<section class="article-page article-page--balanced-grid">
    <div class="article-shell" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        @include('article.templates.partials.archive-header', ['templateKey' => 'balanced-card-grid', 'defaultEyebrow' => t('Explore'), 'defaultTitle' => t('Balanced Card Grid'), 'defaultDescription' => ''])
        <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
        @php($grid = $templateOptions['grid'] ?? ['desktop' => 3, 'tablet' => 2, 'mobile' => 1])
        <div data-article-vue-list-content>
            <div class="article-card-grid" style="--article-template-grid-desktop:{{ $grid['desktop'] }};--article-template-grid-tablet:{{ $grid['tablet'] }};--article-template-grid-mobile:{{ $grid['mobile'] }}">
                @forelse ($articles as $article)
                    @php($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                    <article class="article-card">@include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])<div class="article-card__body"><span class="article-chip">{{ $article->category?->name ?? t('Uncategorized') }}</span>@include('article.templates.partials.archive-title')<p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 130) }}</p><small>{{ optional($article->created_at)->format('d M Y') }}</small></div></article>
                @empty
                    <p class="article-empty">{{ t('No articles found') }}</p>
                @endforelse
            </div>
        </div>
        <div class="article-vue-control-slot" data-article-vue-control-slot></div>
        @include('article.templates.partials.pagination', ['articles' => $articles])
    </div>
</section>
