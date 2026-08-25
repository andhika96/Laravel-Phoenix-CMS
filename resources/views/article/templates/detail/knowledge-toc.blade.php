@php
    $outline = [];
    $outlinedContent = preg_replace_callback('/<h([2-3])([^>]*)>(.*?)<\/h\1>/is', function (array $match) use (&$outline) {
        $title = trim(strip_tags($match[3]));
        $id = 'article-section-'.(count($outline) + 1).'-'.\Illuminate\Support\Str::slug($title);
        $outline[] = ['id' => $id, 'title' => $title, 'level' => (int) $match[1]];

        return '<h'.$match[1].$match[2].' id="'.$id.'">'.$match[3].'</h'.$match[1].'>';
    }, (string) $article->content) ?? (string) $article->content;
    $coverUrl = $article->thumb_l && Storage::disk('public')->exists($article->thumb_l) ? Storage::url($article->thumb_l) : asset('assets/images/article/article-image-placeholder.svg');
@endphp
<article class="article-detail article-detail--knowledge">
    <div class="article-detail__shell article-detail__shell--knowledge" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        <div class="article-detail__main">
            <a href="{{ route('cms.core.article') }}" class="article-back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> {{ t('Back to articles') }}</a>
            <header class="article-detail__header">
                @include('article.templates.partials.detail-header-copy', ['defaultEyebrow' => $article->category?->name ?? t('Uncategorized'), 'defaultDescription' => \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 180), 'metaText' => optional($article->created_at)->format('d M Y').' · '.t('By').' '.($article->author?->fullname ?: $article->author?->username)])
            </header>
            <img src="{{ $coverUrl }}" alt="{{ $article->title }}" class="article-detail__cover article-media-frame">
            <div class="article-rich-content">{!! $outlinedContent !!}</div>
            @include('article.templates.partials.detail-navigation', ['previousArticle' => $previousArticle ?? null, 'nextArticle' => $nextArticle ?? null])
        </div>
        @if (count($outline))
            <aside class="article-toc"><strong>{{ t('On this page') }}</strong><ol>@foreach ($outline as $item)<li class="level-{{ $item['level'] }}"><a href="#{{ $item['id'] }}">{{ $item['title'] }}</a></li>@endforeach</ol></aside>
        @endif
    </div>
</article>
