@php
    $titleOptions = $templateOptions ?? $options ?? [];
    $titleTag = data_get($titleOptions, 'article_title.tag', 'h4');
    $titleTag = in_array($titleTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true) ? $titleTag : 'h4';
@endphp

@switch($titleTag)
    @case('h1')
        <h1 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h1>
        @break
    @case('h2')
        <h2 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h2>
        @break
    @case('h3')
        <h3 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h3>
        @break
    @case('h5')
        <h5 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h5>
        @break
    @case('h6')
        <h6 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h6>
        @break
    @default
        <h4 class="article-title-clamp"><a href="{{ route('cms.core.article.detail', $article->uri) }}">{{ $article->title }}</a></h4>
@endswitch
