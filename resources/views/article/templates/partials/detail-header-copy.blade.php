@php
    $header = $templateOptions['header'] ?? [];
    $eyebrow = data_get($header, 'eyebrow', ['enabled' => true, 'mode' => 'dynamic', 'text' => '']);
    $title = data_get($header, 'title', ['enabled' => true]);
    $description = data_get($header, 'description', ['enabled' => true, 'mode' => 'dynamic', 'text' => '']);
    $eyebrowText = data_get($eyebrow, 'mode') === 'custom' && data_get($eyebrow, 'text') !== '' ? data_get($eyebrow, 'text') : $defaultEyebrow;
    $descriptionText = data_get($description, 'mode') === 'custom' && data_get($description, 'text') !== '' ? data_get($description, 'text') : $defaultDescription;
@endphp
@if (data_get($eyebrow, 'enabled') && $eyebrowText)<span class="article-chip">{{ $eyebrowText }}</span>@endif
@if ($metaText)<p class="article-detail__meta">{{ $metaText }}</p>@endif
@if (data_get($title, 'enabled'))<h1>{{ $article->title }}</h1>@endif
@if (data_get($description, 'enabled') && $descriptionText)<p class="article-detail__dek">{{ $descriptionText }}</p>@endif
