@if (($previousArticle ?? null) || ($nextArticle ?? null))
    <nav class="article-detail-navigation" aria-label="{{ t('Article navigation') }}">
        @if ($previousArticle ?? null)
            <a class="article-detail-navigation__item article-detail-navigation__item--previous" href="{{ route('cms.core.article.detail', $previousArticle->uri) }}">
                <img src="{{ $previousArticle->thumb_s && Storage::disk('public')->exists($previousArticle->thumb_s) ? Storage::url($previousArticle->thumb_s) : asset('assets/images/article/article-image-placeholder.svg') }}" alt="">
                <span><small><i class="far fa-arrow-left" aria-hidden="true"></i> {{ t('Previous article') }}</small><strong class="article-title-clamp">{{ $previousArticle->title }}</strong></span>
            </a>
        @else
            <span></span>
        @endif

        @if ($nextArticle ?? null)
            <a class="article-detail-navigation__item article-detail-navigation__item--next" href="{{ route('cms.core.article.detail', $nextArticle->uri) }}">
                <span><small>{{ t('Next article') }} <i class="far fa-arrow-right" aria-hidden="true"></i></small><strong class="article-title-clamp">{{ $nextArticle->title }}</strong></span>
                <img src="{{ $nextArticle->thumb_s && Storage::disk('public')->exists($nextArticle->thumb_s) ? Storage::url($nextArticle->thumb_s) : asset('assets/images/article/article-image-placeholder.svg') }}" alt="">
            </a>
        @else
            <span></span>
        @endif
    </nav>
@endif
