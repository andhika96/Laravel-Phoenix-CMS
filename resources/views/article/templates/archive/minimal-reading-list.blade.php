<section class="article-page article-page--reading-list">
    <div class="article-shell" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        @include('article.templates.partials.archive-header', ['templateKey' => 'minimal-reading-list', 'defaultEyebrow' => t('Journal'), 'defaultTitle' => t('Minimal Reading List'), 'defaultDescription' => t('Thoughtful reads on design, technology, and the web.')])

        <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
        <div data-article-vue-list-content>
            <div class="article-reading-list" data-article-list>
                @forelse ($articles as $article)
                    @php($readingMinutes = max(1, (int) ceil(str_word_count(strip_tags((string) $article->content)) / 220)))
                    @php($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                    <article class="article-reading-list__item">
                        @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-reading-list__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])
                        <div class="article-reading-list__body">
                            <div class="article-meta"><span>{{ $article->category?->name ?? t('Uncategorized') }}</span><span>{{ optional($article->created_at)->format('d M Y') }}</span><span>{{ $readingMinutes }} {{ t('min read') }}</span></div>
                            @include('article.templates.partials.archive-title')
                            <p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 180) }}</p>
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
