<nav class="article-reference-navigation" aria-label="{{ t('Article navigation') }}">
    <p class="article-reference-navigation__heading">{{ t('Continue exploring') }}</p>
    <div class="article-reference-navigation__list">
        @foreach (['previous' => $previousArticle ?? null, 'next' => $nextArticle ?? null] as $direction => $neighbor)
            @if ($neighbor)
                @php($minutes = max(1, (int) ceil(str_word_count(strip_tags((string) $neighbor->content)) / 220)))
                <a class="article-reference-navigation__item article-reference-navigation__item--{{ $direction }}" href="{{ route('cms.core.article.detail', $neighbor->uri) }}">
                    <span class="article-reference-navigation__direction">
                        @if ($direction === 'previous')<i class="fas fa-arrow-left" aria-hidden="true"></i>@endif
                        <span>{{ $direction === 'previous' ? t('Previous article') : t('Next article') }}</span>
                    </span>
                    <span class="article-reference-navigation__copy"><strong>{{ $neighbor->title }}</strong><small>@if ($neighbor->category?->name){{ $neighbor->category->name }} · @endif{{ $minutes }} {{ t('min read') }}</small></span>
                    @if ($direction === 'next')<i class="fas fa-arrow-right" aria-hidden="true"></i>@else<span aria-hidden="true"></span>@endif
                </a>
            @endif
        @endforeach
    </div>
</nav>
