<nav class="article-story-navigation" aria-label="{{ t('Article navigation') }}">
    <div class="article-story-navigation__heading"><p>{{ t('More stories') }}</p><span>{{ t('Selected for your next read') }}</span></div>
    <div class="article-story-navigation__grid">
        @foreach (['previous' => $previousArticle ?? null, 'next' => $nextArticle ?? null] as $direction => $neighbor)
            @if ($neighbor)
                @php
                    $image = collect([$neighbor->thumb_l, $neighbor->thumb_s])->first(fn ($path) => $path && Storage::disk('public')->exists($path));
                    $thumbnail = $image ? Storage::url($image) : asset('assets/images/article/article-image-placeholder.svg');
                    $minutes = max(1, (int) ceil(str_word_count(strip_tags((string) $neighbor->content)) / 220));
                @endphp
                <a class="article-story-navigation__item article-story-navigation__item--{{ $direction }}" href="{{ route('cms.core.article.detail', $neighbor->uri) }}">
                    <img src="{{ $thumbnail }}" alt="" width="800" height="600" loading="lazy">
                    <span class="article-story-navigation__direction">
                        @if ($direction === 'previous')<i class="fas fa-arrow-left" aria-hidden="true"></i>@endif
                        <span>{{ $direction === 'previous' ? t('Previous story') : t('Next story') }}</span>
                        @if ($direction === 'next')<i class="fas fa-arrow-right" aria-hidden="true"></i>@endif
                    </span>
                    <span class="article-story-navigation__copy">
                        @if ($neighbor->category?->name)<small class="article-story-navigation__category">{{ $neighbor->category->name }}</small>@endif
                        <strong>{{ $neighbor->title }}</strong>
                        <small class="article-story-navigation__time">{{ $minutes }} {{ t('min read') }}</small>
                    </span>
                </a>
            @endif
        @endforeach
    </div>
</nav>
