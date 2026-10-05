@php
    $classicAuthor = $classic['show_author'] && $article->relationLoaded('author') ? $article->author : null;
    $classicAuthorName = $classicAuthor?->fullname ?: $classicAuthor?->username;
    $classicReadTime = max(1, (int) ceil(str_word_count(strip_tags((string) $article->content)) / 220));
@endphp
@if ($classicAuthorName || ($classic['show_date'] && $article->created_at) || $classic['show_reading_time'])
    <div class="article-classic-meta">
        @if ($classicAuthorName)<span class="article-classic-author"><span class="article-classic-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($classicAuthorName, 0, 1)) }}</span>{{ $classicAuthorName }}</span>@endif
        @if ($classic['show_date'] && $article->created_at)<time datetime="{{ $article->created_at->toDateString() }}">{{ $article->created_at->format('d M Y') }}</time>@endif
        @if ($classic['show_reading_time'])<span>{{ $classicReadTime }} {{ t('min read') }}</span>@endif
    </div>
@endif
