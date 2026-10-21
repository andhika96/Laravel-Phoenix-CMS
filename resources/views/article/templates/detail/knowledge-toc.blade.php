@php
    $outline = [];
    $outlinedContent = preg_replace_callback('/<h([2-3])([^>]*)>(.*?)<\/h\1>/is', function (array $match) use (&$outline) {
        $title = trim(strip_tags($match[3]));
        if ($title === '') return $match[0];
        $hasId = preg_match('/(?:^|\s)id\s*=\s*([\'"])(.*?)\1/i', $match[2], $existingId);
        $id = $hasId ? html_entity_decode($existingId[2], ENT_QUOTES | ENT_HTML5, 'UTF-8') : 'article-section-'.(count($outline) + 1).'-'.\Illuminate\Support\Str::slug($title);
        $outline[] = ['id' => $id, 'title' => $title, 'level' => (int) $match[1]];

        return $hasId ? $match[0] : '<h'.$match[1].$match[2].' id="'.$id.'">'.$match[3].'</h'.$match[1].'>';
    }, (string) $article->content) ?? (string) $article->content;
    $coverUrl = $article->thumb_l && Storage::disk('public')->exists($article->thumb_l) ? Storage::url($article->thumb_l) : asset('assets/images/article/article-image-placeholder.svg');
@endphp
<article class="article-detail article-detail--knowledge">
    <div class="article-detail__shell article-detail__shell--knowledge" data-has-outline="{{ count($outline) ? 'true' : 'false' }}" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        <div class="article-detail__main">
            <a href="{{ route('cms.core.article') }}" class="article-back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> {{ t('Back to articles') }}</a>
            <header class="article-detail__header">
                @include('article.templates.partials.detail-header-copy', ['defaultEyebrow' => $article->category?->name ?? t('Uncategorized'), 'defaultDescription' => \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 180), 'metaText' => optional($article->created_at)->format('d M Y').' · '.t('By').' '.($article->author?->fullname ?: $article->author?->username)])
            </header>
            <img src="{{ $coverUrl }}" alt="{{ $article->title }}" class="article-detail__cover article-media-frame" fetchpriority="high">
            <div class="article-rich-content">{!! $outlinedContent !!}</div>
            @include('article.templates.partials.detail-navigation', ['previousArticle' => $previousArticle ?? null, 'nextArticle' => $nextArticle ?? null, 'readingCards' => true])
        </div>
        @if (count($outline))
            <aside class="article-toc" aria-label="{{ t('Table of contents') }}">
                <details open>
                    <summary>{{ t('On this page') }}<i class="fas fa-chevron-down" aria-hidden="true"></i></summary>
                    <ol>@foreach ($outline as $item)<li class="level-{{ $item['level'] }}"><a href="#{{ $item['id'] }}">{{ $item['title'] }}</a></li>@endforeach</ol>
                </details>
            </aside>
        @endif
    </div>
</article>
