@php
    $thumbnailOptions = $templateOptions['thumbnail'] ?? [];
    $thumbnailMode = data_get($thumbnailOptions, 'mode', 'background');
    $thumbnailMode = in_array($thumbnailMode, ['background', 'asset'], true) ? $thumbnailMode : 'background';
    $thumbnailFit = data_get($thumbnailOptions, 'fit', 'cover');
    $thumbnailFit = in_array($thumbnailFit, ['cover', 'contain'], true) ? $thumbnailFit : 'cover';
    $thumbnailFrame = $thumbnailOptions['frame'] ?? [];
    $thumbnailFrameEnabled = !empty($thumbnailFrame['enabled']);
@endphp
<a href="{{ $href }}" class="{{ $class }} article-media-frame {{ $thumbnailMode === 'asset' ? 'article-asset-media' : 'article-background-media' }} {{ $thumbnailFrameEnabled ? 'article-thumbnail-frame--custom' : '' }}" style="--article-media-image:url('{{ $mediaUrl }}');--article-thumbnail-fit:{{ $thumbnailFit }};--article-thumbnail-background:{{ data_get($thumbnailOptions, 'background_color', '#f2f4f7') }};--article-thumbnail-frame-border-color:{{ data_get($thumbnailFrame, 'border_color', '#e1e6ee') }};--article-thumbnail-frame-border-width:{{ data_get($thumbnailFrame, 'border_width', '1px') }};--article-thumbnail-frame-radius:{{ data_get($thumbnailFrame, 'radius', '1rem') }};" aria-label="{{ $title }}">@if($thumbnailMode === 'asset')<img src="{{ $mediaUrl }}" alt="">@else<span class="visually-hidden">{{ $title }}</span>@endif</a>
