<nav class="article-reading-navigation" aria-label="{{ t('Article navigation') }}">
    <p class="article-reading-navigation__heading">{{ t('Continue reading') }}</p>
    <div class="article-reading-navigation__grid">
        @foreach (['previous' => $previousArticle ?? null, 'next' => $nextArticle ?? null] as $direction => $neighbor)
            @if ($neighbor)
                @php
                    $thumbnail = $neighbor->thumb_s && Storage::disk('public')->exists($neighbor->thumb_s) ? Storage::url($neighbor->thumb_s) : asset('assets/images/article/article-image-placeholder.svg');
                    $category = $neighbor->category?->name;
                    $minutes = max(1, (int) ceil(str_word_count(strip_tags((string) $neighbor->content)) / 220));
                @endphp
                <a class="article-reading-navigation__card article-reading-navigation__card--{{ $direction }}" href="{{ route('cms.core.article.detail', $neighbor->uri) }}">
                    <span class="article-reading-navigation__direction">
                        @if ($direction === 'previous')<span class="article-reading-navigation__arrow"><i class="fas fa-arrow-left" aria-hidden="true"></i></span>@endif
                        <span>{{ $direction === 'previous' ? t('Previous article') : t('Next article') }}</span>
                        @if ($direction === 'next')<span class="article-reading-navigation__arrow"><i class="fas fa-arrow-right" aria-hidden="true"></i></span>@endif
                    </span>
                    <span class="article-reading-navigation__body">
                        <img src="{{ $thumbnail }}" alt="" width="76" height="72" loading="lazy">
                        <span class="article-reading-navigation__copy">
                            <small>@if ($category){{ $category }} · @endif{{ $minutes }} {{ t('min read') }}</small>
                            <strong>{{ $neighbor->title }}</strong>
                        </span>
                    </span>
                </a>
            @endif
        @endforeach
    </div>
</nav>
