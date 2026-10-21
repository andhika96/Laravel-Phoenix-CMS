@if ($focusedReaderHeader ?? false)
            @if ($eyebrow['enabled'] && $eyebrowText)<span class="article-reader-category">{{ $eyebrowText }}</span>@endif
            @if ($templateOptions['header']['title']['enabled'])<h1>{{ $article->title }}</h1>@endif
            @if ($description['enabled'] && $descriptionText)<p class="article-detail__dek">{{ $descriptionText }}</p>@endif
            @if ($hasMeta)
                <div class="article-detail__meta" data-separator="{{ $reader['metadata_separator'] }}">
                    @if ($templateOptions['display']['author'] && $authorName)<span class="article-reader-author">{{ $authorName }}</span>@endif
                    @if ($templateOptions['display']['date'] && $article->created_at)<time datetime="{{ $article->created_at->toDateString() }}">{{ $article->created_at->format('d M Y') }}</time>@endif
                    @if ($templateOptions['display']['reading_time'])<span>{{ $readingMinutes }} {{ t('min read') }}</span>@endif
                </div>
            @endif
@else
@php
    $header = $templateOptions['header'] ?? [];
    $eyebrow = data_get($header, 'eyebrow', ['enabled' => true, 'mode' => 'dynamic', 'text' => '']);
    $title = data_get($header, 'title', ['enabled' => true]);
    $description = data_get($header, 'description', ['enabled' => true, 'mode' => 'dynamic', 'text' => '']);
    $eyebrowText = data_get($eyebrow, 'mode') === 'custom' && data_get($eyebrow, 'text') !== '' ? data_get($eyebrow, 'text') : $defaultEyebrow;
    $descriptionText = data_get($description, 'mode') === 'custom' && data_get($description, 'text') !== '' ? data_get($description, 'text') : $defaultDescription;
@endphp
@if (data_get($eyebrow, 'enabled') && $eyebrowText)<span class="article-chip">{{ $eyebrowText }}</span>@endif
@php
    $metaParts = [];
    if (data_get($templateOptions, 'display.date', true) && $article->created_at) $metaParts[] = $article->created_at->format('d M Y');
    $authorName = $article->author?->fullname ?: $article->author?->username;
    if (data_get($templateOptions, 'display.author', true) && $authorName) $metaParts[] = t('By').' '.$authorName;
    $metaText = implode(' · ', $metaParts);
@endphp
@if ($metaText)<p class="article-detail__meta">{{ $metaText }}</p>@endif
@if (data_get($title, 'enabled'))<h1>{{ $article->title }}</h1>@endif
@if (data_get($description, 'enabled') && $descriptionText)<p class="article-detail__dek">{{ $descriptionText }}</p>@endif

@endif
