@php($coverUrl = $article->thumb_l && Storage::disk('public')->exists($article->thumb_l) ? Storage::url($article->thumb_l) : asset('assets/images/article/article-image-placeholder.svg'))
<article class="article-detail article-detail--editorial-feature">
    <div class="article-feature-back"><a href="{{ route('cms.core.article') }}" class="article-back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> {{ t('Back to articles') }}</a></div>
    <header class="article-feature-hero article-media-frame">
        <img src="{{ $coverUrl }}" alt="{{ $article->title }}" fetchpriority="high">
        <div class="article-feature-hero__overlay article-detail__header">
            @include('article.templates.partials.detail-header-copy', ['defaultEyebrow' => $article->category?->name ?? t('Uncategorized'), 'defaultDescription' => \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 180), 'metaText' => optional($article->created_at)->format('d M Y').' · '.t('By').' '.($article->author?->fullname ?: $article->author?->username)])
        </div>
    </header>
    <div class="article-detail__shell" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        <div class="article-rich-content article-rich-content--feature">{!! $article->content !!}</div>
        @include('article.templates.partials.detail-navigation', ['previousArticle' => $previousArticle ?? null, 'nextArticle' => $nextArticle ?? null, 'readingCards' => true])
    </div>
</article>
