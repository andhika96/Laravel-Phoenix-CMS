@php
    $settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
    $nodeId = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($node['id'] ?? ''));
    $heroId = $nodeId !== '' ? 'pb-node-' . $nodeId : 'pb-hero-slider-' . substr(md5(uniqid('', true)), 0, 8);
    $safeMedia = function ($value) {
        $raw = trim((string) ($value ?? ''));
        return preg_match("~^(?:https?://|/)[^\"'\\s<>\\\\]+$~i", $raw) ? $raw : '';
    };
    $safeClass = function ($value) {
        $raw = preg_replace('/[^A-Za-z0-9_\-\s]/', ' ', (string) ($value ?? ''));
        return trim(preg_replace('/\s+/', ' ', $raw));
    };
    $cssLength = function ($value, $fallback) {
        $raw = trim((string) ($value ?? ''));
        return preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i', $raw) ? $raw : $fallback;
    };
    $safeColor = function ($value, $fallback) {
        $raw = trim((string) ($value ?? ''));
        return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : $fallback;
    };
    $bool = function ($key, $fallback = false) use ($settings) {
        return array_key_exists($key, $settings) ? (bool) $settings[$key] : $fallback;
    };
    $direction = in_array(strtolower(trim((string) ($settings['direction'] ?? 'horizontal'))), ['horizontal', 'vertical'], true) ? strtolower(trim((string) ($settings['direction'] ?? 'horizontal'))) : 'horizontal';
    $directionTablet = in_array(strtolower(trim((string) ($settings['directionTablet'] ?? ''))), ['horizontal', 'vertical'], true) ? strtolower(trim((string) ($settings['directionTablet'] ?? ''))) : '';
    $directionMobile = in_array(strtolower(trim((string) ($settings['directionMobile'] ?? ''))), ['horizontal', 'vertical'], true) ? strtolower(trim((string) ($settings['directionMobile'] ?? ''))) : '';
    $paginationPositions = ['top-left', 'top-center', 'top-right', 'center-left', 'center', 'center-right', 'bottom-left', 'bottom-center', 'bottom-right'];
    $paginationPosition = function ($value, $fallback) use ($paginationPositions) {
        $candidate = strtolower(trim((string) ($value ?? '')));
        return in_array($candidate, $paginationPositions, true) ? $candidate : $fallback;
    };
    $paginationPositionHorizontal = $paginationPosition($settings['paginationPositionHorizontal'] ?? '', 'bottom-center');
    $paginationPositionHorizontalTablet = $paginationPosition($settings['paginationPositionHorizontalTablet'] ?? '', '');
    $paginationPositionHorizontalMobile = $paginationPosition($settings['paginationPositionHorizontalMobile'] ?? '', '');
    $paginationPositionVertical = $paginationPosition($settings['paginationPositionVertical'] ?? '', 'center-right');
    $paginationPositionVerticalTablet = $paginationPosition($settings['paginationPositionVerticalTablet'] ?? '', '');
    $paginationPositionVerticalMobile = $paginationPosition($settings['paginationPositionVerticalMobile'] ?? '', '');
    $activePaginationPosition = $direction === 'vertical' ? $paginationPositionVertical : $paginationPositionHorizontal;
    $transition = strtolower(trim((string) ($settings['transition'] ?? 'slide'))) === 'fade' ? 'fade' : 'slide';
    $heightMode = strtolower(trim((string) ($settings['heightMode'] ?? 'adaptive'))) === 'fixed' ? 'fixed' : 'adaptive';
    $videoDurationMode = strtolower(trim((string) ($settings['videoDurationMode'] ?? 'interval'))) === 'duration' ? 'duration' : 'interval';
    $videoControls = strtolower(trim((string) ($settings['videoControls'] ?? 'custom'))) === 'provider' ? 'provider' : 'custom';
    $videoMutedAutoplay = $bool('videoMutedAutoplay', true);
    $videoResume = $bool('videoResume', true);
    $videoLoop = $bool('videoLoop', false);
    $lazyLoad = $bool('lazyLoad', true);
    $allowedRatios = ['16/9', '4/3', '1/1', '3/2', '21/9', '9/16', '4/5'];
    $ratioPadding = function ($ratio) use ($allowedRatios) {
        $safe = in_array(trim((string) $ratio), $allowedRatios, true) ? trim((string) $ratio) : '16/9';
        [$width, $height] = array_pad(explode('/', $safe), 2, 9);
        return round(((float) $height / max(1, (float) $width)) * 100, 4) . '%';
    };
    $inferProvider = function ($url) {
        $value = strtolower(trim((string) $url));
        if (preg_match('/youtu\.be\/|youtube\.com\//', $value)) return 'youtube';
        if (preg_match('/vimeo\.com\//', $value)) return 'vimeo';
        if (preg_match('/dailymotion\.com\/|dai\.ly\//', $value)) return 'dailymotion';
        return '';
    };
    $normalizeProvider = function ($provider, $url) use ($inferProvider) {
        $raw = strtolower(trim((string) ($provider ?? '')));
        $aliases = ['file' => 'self_hosted', 'html5' => 'self_hosted', 'native' => 'self_hosted', 'direct' => 'self_hosted', 'iframe' => 'embed'];
        $candidate = $aliases[$raw] ?? $raw;
        if (in_array($candidate, ['self_hosted', 'youtube', 'vimeo', 'dailymotion', 'embed'], true)) return $candidate;
        return $candidate === '' || $candidate === 'auto' ? ($inferProvider($url) ?: 'self_hosted') : 'embed';
    };
    $extractId = function ($provider, $url) {
        $value = trim((string) $url);
        if ($provider === 'youtube' && preg_match('/(?:youtu\.be\/|[?&]v=|embed\/)([^?&\/]+)/i', $value, $match)) return $match[1];
        if ($provider === 'vimeo' && preg_match('/(?:video\/|vimeo\.com\/)(\d+)/i', $value, $match)) return $match[1];
        if ($provider === 'dailymotion' && preg_match('/(?:video\/|dai\.ly\/)([^_?&\/]+)/i', $value, $match)) return $match[1];
        return $provider === 'vimeo' ? preg_replace('/\D+/', '', $value) : $value;
    };
    $videoUrl = function ($provider, $url) use ($safeMedia, $extractId, $settings) {
        $raw = $safeMedia($url);
        if ($raw === '' || in_array($provider, ['self_hosted', 'embed'], true)) return $raw;
        $id = $extractId($provider, $raw);
        if ($id === '') return '';
        if ($provider === 'youtube') return 'https://' . (!empty($settings['videoPrivacyMode']) ? 'www.youtube-nocookie.com' : 'www.youtube.com') . '/embed/' . rawurlencode($id) . '?enablejsapi=1&playsinline=1&rel=0';
        if ($provider === 'vimeo') return 'https://player.vimeo.com/video/' . rawurlencode($id) . '?api=1&autopause=0&playsinline=1';
        return 'https://www.dailymotion.com/embed/video/' . rawurlencode($id) . '?api=postMessage';
    };
    $defaultSlide = function ($index) {
        return ['id' => 'hero-slider-slide-' . $index, 'mediaType' => 'image', 'imageUrl' => '', 'imageUrlTablet' => '', 'imageUrlMobile' => '', 'imageAlt' => '', 'videoProvider' => 'self_hosted', 'videoUrl' => '', 'videoPoster' => '', 'videoPosterTablet' => '', 'videoPosterMobile' => '', 'videoAutoplay' => 'inherit', 'videoLoop' => false, 'videoControls' => true, 'videoMuted' => true, 'videoResume' => true, 'videoAspectRatio' => '16/9', 'title' => '', 'subtitle' => ''];
    };
    $slides = is_array($settings['slides'] ?? null) ? array_slice($settings['slides'], 0, 30) : [];
    if (!$slides) $slides = [$defaultSlide(1)];
    $normalizedSlides = [];
    foreach ($slides as $index => $rawSlide) {
        $slide = is_array($rawSlide) ? array_replace($defaultSlide($index + 1), $rawSlide) : $defaultSlide($index + 1);
        $mediaType = strtolower(trim((string) ($slide['mediaType'] ?? 'image')));
        $mediaType = in_array($mediaType, ['image', 'video'], true) ? $mediaType : 'image';
        $rawVideoUrl = $safeMedia($slide['videoUrl'] ?? ($slide['url'] ?? ''));
        $providerInput = is_array($rawSlide) && array_key_exists('videoProvider', $rawSlide) ? ($slide['videoProvider'] ?? '') : (is_array($rawSlide) ? ($rawSlide['provider'] ?? '') : '');
        $provider = $normalizeProvider($providerInput, $rawVideoUrl);
        $videoAutoplay = in_array(strtolower(trim((string) ($slide['videoAutoplay'] ?? 'inherit'))), ['inherit', 'on', 'off'], true) ? strtolower(trim((string) ($slide['videoAutoplay'] ?? 'inherit'))) : 'inherit';
        $poster = $safeMedia($slide['videoPoster'] ?? ($slide['poster'] ?? ''));
        $normalizedSlides[] = [
            ...$slide,
            'id' => trim((string) ($slide['id'] ?? 'hero-slider-slide-' . ($index + 1))) ?: 'hero-slider-slide-' . ($index + 1),
            'mediaType' => $mediaType,
            'imageUrl' => $safeMedia($slide['imageUrl'] ?? ($mediaType === 'image' ? ($slide['url'] ?? '') : '')),
            'imageUrlTablet' => $safeMedia($slide['imageUrlTablet'] ?? ''),
            'imageUrlMobile' => $safeMedia($slide['imageUrlMobile'] ?? ''),
            'imageAlt' => trim((string) ($slide['imageAlt'] ?? '')),
            'videoProvider' => $provider,
            'videoUrl' => $rawVideoUrl,
            'videoPoster' => $poster,
            'videoPosterTablet' => $safeMedia($slide['videoPosterTablet'] ?? ''),
            'videoPosterMobile' => $safeMedia($slide['videoPosterMobile'] ?? ''),
            'videoAutoplay' => $videoAutoplay,
            'videoLoop' => array_key_exists('videoLoop', $slide) ? (bool) $slide['videoLoop'] : $videoLoop,
            'videoMuted' => array_key_exists('videoMuted', $slide) ? (bool) $slide['videoMuted'] : true,
            'videoResume' => array_key_exists('videoResume', $slide) ? (bool) $slide['videoResume'] : $videoResume,
            'videoAspectRatio' => in_array(trim((string) ($slide['videoAspectRatio'] ?? '16/9')), $allowedRatios, true) ? trim((string) ($slide['videoAspectRatio'] ?? '16/9')) : '16/9',
        ];
    }
    $runtimeSlides = array_map(function ($slide) {
        return [
            'id' => $slide['id'], 'mediaType' => $slide['mediaType'], 'videoProvider' => $slide['videoProvider'], 'videoUrl' => $slide['videoUrl'],
            'videoAutoplay' => $slide['videoAutoplay'], 'videoLoop' => $slide['videoLoop'], 'videoMuted' => $slide['videoMuted'], 'videoResume' => $slide['videoResume'], 'videoAspectRatio' => $slide['videoAspectRatio'], 'videoPoster' => $slide['videoPoster'], 'videoPosterTablet' => $slide['videoPosterTablet'], 'videoPosterMobile' => $slide['videoPosterMobile'],
        ];
    }, $normalizedSlides);
    $runtimeConfig = [
        'direction' => $direction, 'directionTablet' => $directionTablet, 'directionMobile' => $directionMobile, 'paginationPositionHorizontal' => $paginationPositionHorizontal, 'paginationPositionHorizontalTablet' => $paginationPositionHorizontalTablet, 'paginationPositionHorizontalMobile' => $paginationPositionHorizontalMobile, 'paginationPositionVertical' => $paginationPositionVertical, 'paginationPositionVerticalTablet' => $paginationPositionVerticalTablet, 'paginationPositionVerticalMobile' => $paginationPositionVerticalMobile, 'transition' => $transition,
        'transitionSpeed' => max(0, (int) ($settings['transitionSpeed'] ?? 600)), 'autoplay' => $bool('autoplay', true), 'autoplaySpeed' => max(100, (int) ($settings['autoplaySpeed'] ?? 5000)),
        'pauseOnHover' => $bool('pauseOnHover', true), 'pauseOnFocus' => $bool('pauseOnFocus', true), 'pauseOnInteraction' => $bool('pauseOnInteraction', false),
        'loop' => $bool('loop', true), 'rewind' => $bool('rewind', false), 'perMove' => max(1, (int) ($settings['perMove'] ?? 1)),
        'arrows' => $bool('arrows', true), 'pagination' => $bool('pagination', true), 'keyboard' => $bool('keyboard', true), 'drag' => $bool('drag', true), 'mouseWheel' => $bool('mouseWheel', false), 'wheelRelease' => $bool('wheelRelease', false), 'progress' => $bool('progress', true), 'lazyLoad' => $bool('lazyLoad', true),
        'videoAutoplay' => $bool('videoAutoplay', false), 'videoDurationMode' => $videoDurationMode, 'videoAutoplayFallback' => 'interval', 'videoMutedAutoplay' => $videoMutedAutoplay, 'videoControls' => $videoControls, 'videoLoop' => $videoLoop, 'videoResume' => $videoResume, 'videoPrivacyMode' => $bool('videoPrivacyMode', false),
        'dailymotionPlayerId' => trim((string) ($settings['dailymotionPlayerId'] ?? '')), 'dailymotionSdkUrl' => $safeMedia($settings['dailymotionSdkUrl'] ?? ''), 'heightMode' => $heightMode, 'fixedHeight' => $cssLength($settings['fixedHeight'] ?? '', '520px'), 'minHeight' => $cssLength($settings['minHeight'] ?? '', '420px'), 'minHeightTablet' => $cssLength($settings['minHeightTablet'] ?? '', '360px'), 'minHeightMobile' => $cssLength($settings['minHeightMobile'] ?? '', '280px'), 'slides' => $runtimeSlides,
    ];
    $customClass = $safeClass($settings['cssClass'] ?? '');
    $rootStyle = '--hero-slider-overlay:' . $safeColor($settings['overlayColor'] ?? '', 'rgba(0,0,0,.2)') . ';--hero-slider-title-color:' . $safeColor($settings['titleColor'] ?? '', '#fff') . ';--hero-slider-subtitle-color:' . $safeColor($settings['subtitleColor'] ?? '', '#fff') . ';--hero-slider-button-color:' . $safeColor($settings['buttonTextColor'] ?? '', '#fff') . ';--hero-slider-button-bg:' . $safeColor($settings['buttonBackground'] ?? '', '#30343a') . ';--hero-slider-button-hover-bg:' . $safeColor($settings['buttonBackgroundHover'] ?? '', '#1f2328') . ';--hero-slider-button-radius:' . $cssLength($settings['buttonRadius'] ?? '', '999px') . ';--hero-slider-button-pad-x:' . $cssLength($settings['buttonPaddingX'] ?? '', '18px') . ';--hero-slider-button-pad-y:' . $cssLength($settings['buttonPaddingY'] ?? '', '10px') . ';min-height:' . $cssLength($settings['minHeight'] ?? '', '420px') . ';';
    if ($heightMode === 'fixed') $rootStyle .= 'height:' . $cssLength($settings['fixedHeight'] ?? '', '520px') . ';';
    $tabletMinHeight = $cssLength($settings['minHeightTablet'] ?? '', '360px');
    $mobileMinHeight = $cssLength($settings['minHeightMobile'] ?? '', '280px');
@endphp
<section id="{{ $heroId }}" class="pb-hero-slider{{ $customClass !== '' ? ' ' . $customClass : '' }}" data-hero-slider data-arrow-icons="horizontal" data-direction="{{ $direction }}" data-pagination-position-horizontal="{{ $paginationPositionHorizontal }}" data-pagination-position-vertical="{{ $paginationPositionVertical }}" data-height-mode="{{ $heightMode }}" data-hero-slider-config="{{ e(json_encode($runtimeConfig, JSON_UNESCAPED_SLASHES)) }}" style="{{ $rootStyle }}">
    <div class="pb-hero-slider__viewport">
        <div class="pb-hero-slider__track{{ $transition === 'fade' ? ' is-fade' : '' }}" data-hero-slider-track>
            @foreach($normalizedSlides as $index => $slide)
                @php
                    $isVideo = $slide['mediaType'] === 'video';
                    $provider = $slide['videoProvider'];
                    $mediaSrc = $isVideo ? $videoUrl($provider, $slide['videoUrl']) : '';
                    $isIframe = $isVideo && in_array($provider, ['youtube', 'vimeo', 'dailymotion', 'embed'], true);
                    $durationSupported = $isVideo && $provider === 'self_hosted' && $mediaSrc !== '';
                    $poster = $slide['videoPoster'];
                    $autoplaySlide = $slide['videoAutoplay'] === 'on' || ($slide['videoAutoplay'] === 'inherit' && $bool('videoAutoplay', false));
                    $muted = $videoMutedAutoplay || $slide['videoMuted'];
                @endphp
                <article class="pb-hero-slider__slide{{ $index === 0 ? ' is-active' : '' }}" data-hero-slide data-index="{{ $index }}" data-video-autoplay="{{ $autoplaySlide ? 'true' : 'false' }}" data-video-provider="{{ $isVideo ? $provider : '' }}" data-video-duration-supported="{{ $durationSupported ? 'true' : 'false' }}" data-video-resume="{{ ($slide['videoResume'] && $videoResume) ? 'true' : 'false' }}" data-video-loop="{{ ($slide['videoLoop'] || $videoLoop) ? 'true' : 'false' }}" data-video-muted="{{ $muted ? 'true' : 'false' }}" data-video-poster="{{ e($slide['videoPoster']) }}" data-video-poster-tablet="{{ e($slide['videoPosterTablet']) }}" data-video-poster-mobile="{{ e($slide['videoPosterMobile']) }}" aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                    <div class="pb-hero-slider__media">
                        @if(!$isVideo)
                            @if($slide['imageUrl'] !== '')
                                <picture>
                                    @if($slide['imageUrlMobile'] !== '')<source media="(max-width: 767px)" srcset="{{ e($slide['imageUrlMobile']) }}">@endif
                                    @if($slide['imageUrlTablet'] !== '')<source media="(max-width: 1024px)" srcset="{{ e($slide['imageUrlTablet']) }}">@endif
                                    <img src="{{ e($slide['imageUrl']) }}" alt="{{ e($slide['imageAlt']) }}" loading="{{ !$lazyLoad || $index === 0 ? 'eager' : 'lazy' }}">
                                </picture>
                            @else
                                <div class="pb-hero-slider__empty" role="img" aria-label="Hero slider image unavailable"><i class="far fa-image" aria-hidden="true"></i></div>
                            @endif
                        @elseif($mediaSrc !== '')
                            @if($isIframe)
                                <iframe src="{{ e($mediaSrc) }}" title="{{ e($slide['title'] ?: 'Hero slider video') }}" loading="{{ !$lazyLoad || $index === 0 ? 'eager' : 'lazy' }}" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen data-hero-video data-video-provider="{{ $provider }}" data-video-id="{{ e($extractId($provider, $slide['videoUrl'])) }}"></iframe>
                            @else
                                <video src="{{ e($mediaSrc) }}" @if($poster !== '') poster="{{ e($poster) }}" @endif playsinline preload="metadata" @if($muted) muted @endif @if($slide['videoLoop'] || $videoLoop) loop @endif @if($videoControls === 'provider') controls @endif data-hero-video data-video-provider="self_hosted"></video>
                            @endif
                            @if($poster !== '' && $isIframe)<div class="pb-hero-slider__poster" data-hero-slider-poster style="background-image:url('{{ e($poster) }}')" aria-hidden="true"></div>@endif
                            @if($videoControls === 'custom' && $provider !== 'embed')
                                <div class="pb-hero-slider__video-controls" data-hero-video-controls>
                                    <button type="button" data-hero-video-control="play" aria-label="Play video"><i class="fas fa-play" aria-hidden="true"></i></button>
                                    <button type="button" data-hero-video-control="mute" aria-label="Mute video"><i class="fas fa-volume-mute" aria-hidden="true"></i></button>
                                </div>
                            @endif
                        @else
                            <div class="pb-hero-slider__empty" role="img" aria-label="Hero slider video unavailable"><i class="fas fa-video-slash" aria-hidden="true"></i></div>
                        @endif
                    </div>
                    <div class="pb-hero-slider__overlay"></div>
                    @if(trim((string) ($slide['title'] ?? '')) !== '' || trim((string) ($slide['subtitle'] ?? '')) !== '' || (is_array($slide['buttons'] ?? null) && count($slide['buttons'])))
                        <div class="pb-hero-slider__content">
                            @if(trim((string) ($slide['title'] ?? '')) !== '')<h2 class="pb-hero-slider__title">{{ $slide['title'] }}</h2>@endif
                            @if(trim((string) ($slide['subtitle'] ?? '')) !== '')<p class="pb-hero-slider__subtitle">{{ $slide['subtitle'] }}</p>@endif
                            @if(is_array($slide['buttons'] ?? null) && count($slide['buttons']))
                                <div class="pb-hero-slider__buttons">
                                    @foreach(array_slice($slide['buttons'], 0, 3) as $button)
                                        @php $buttonUrl = $safeMedia($button['linkUrl'] ?? ($button['url'] ?? '')); $buttonText = trim((string) ($button['text'] ?? 'Learn More')); $buttonTarget = ($button['linkTarget'] ?? '') === '_blank'; $buttonRel = !empty($button['linkNofollow']) ? 'noopener noreferrer nofollow' : ($buttonTarget ? 'noopener noreferrer' : ''); @endphp
                                        @if($buttonUrl !== '')<a class="pb-hero-slider__button" href="{{ e($buttonUrl) }}" @if($buttonTarget) target="_blank" @endif @if($buttonRel !== '') rel="{{ $buttonRel }}" @endif data-pb-interactive="true">{{ $buttonText }}</a>@else<button type="button" class="pb-hero-slider__button is-disabled" disabled>{{ $buttonText !== '' ? $buttonText : 'Learn More' }}</button>@endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
    @if($bool('arrows', true) && count($normalizedSlides) > 1)
        <button type="button" class="pb-hero-slider__arrow pb-hero-slider__arrow--prev" data-hero-prev aria-label="Previous slide"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
        <button type="button" class="pb-hero-slider__arrow pb-hero-slider__arrow--next" data-hero-next aria-label="Next slide"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
    @endif
    @if($bool('pagination', true) && count($normalizedSlides) > 1)
        <div class="pb-hero-slider__pagination" data-hero-pagination data-orientation="{{ $direction === 'vertical' ? 'vertical' : 'horizontal' }}" data-position="{{ $activePaginationPosition }}" data-position-horizontal="{{ $paginationPositionHorizontal }}" data-position-vertical="{{ $paginationPositionVertical }}">
            @foreach($normalizedSlides as $index => $slide)<button type="button" data-hero-index data-index="{{ $index }}" class="{{ $index === 0 ? 'is-active' : '' }}" aria-label="Go to slide {{ $index + 1 }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"></button>@endforeach
        </div>
    @endif
    @if($bool('progress', true))<div class="pb-hero-slider__progress" data-hero-slider-progress><span></span></div>@endif
</section>
<style>
#{{ $heroId }}{--hero-slider-gap:{{ $cssLength($settings['gap'] ?? '', '0px') }};--hero-slider-padding:{{ $cssLength($settings['padding'] ?? '', '0px') }}}
@media(max-width:1024px){#{{ $heroId }}{min-height:{{ $tabletMinHeight }}}}
@media(max-width:767px){#{{ $heroId }}{min-height:{{ $mobileMinHeight }}}}
</style>
