@php
    $heroSettings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
    $heroId = 'pb-hero-' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string) ($node['id'] ?? uniqid()));
    $heroMode = ($heroSettings['positioningMode'] ?? 'grouped') === 'independent' ? 'independent' : 'grouped';
    $heroAllowedOrder = ['title', 'subtitle', 'buttons'];
    $heroOrder = [];
    foreach ((array) ($heroSettings['contentOrder'] ?? $heroAllowedOrder) as $key) {
        if (in_array($key, $heroAllowedOrder, true) && !in_array($key, $heroOrder, true)) $heroOrder[] = $key;
    }
    foreach ($heroAllowedOrder as $key) if (!in_array($key, $heroOrder, true)) $heroOrder[] = $key;
    $heroButtons = array_slice(array_values(array_filter((array) ($heroSettings['buttons'] ?? []), 'is_array')), 0, 3);

    $heroSafeMedia = static function ($value) {
        $raw = trim((string) $value);
        if ($raw === '' || str_starts_with($raw, '//')) return '';
        return preg_match('#^(?:https?://|/)#i', $raw) ? $raw : '';
    };
    $heroSafeLink = static function ($value) {
        $raw = trim((string) $value);
        if ($raw === '' || str_starts_with($raw, '//')) return '';
        return preg_match('#^(?:https?://|mailto:|tel:|/|\#)#i', $raw) ? $raw : '';
    };
    $heroLength = static function ($value, $fallback) {
        $raw = trim((string) $value);
        return preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i', $raw) ? $raw : $fallback;
    };
    $heroColor = static function ($value, $fallback) {
        $raw = trim((string) $value);
        return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : $fallback;
    };
    $heroEnum = static fn ($value, $allowed, $fallback) => in_array($value, $allowed, true) ? $value : $fallback;
    $heroResponsive = static function ($base, $suffix, $fallback = '') use ($heroSettings) {
        $keys = $suffix === 'Mobile' ? [$base . 'Mobile', $base . 'Tablet', $base] : ($suffix === 'Tablet' ? [$base . 'Tablet', $base] : [$base]);
        foreach ($keys as $key) if (array_key_exists($key, $heroSettings) && $heroSettings[$key] !== '' && $heroSettings[$key] !== null) return $heroSettings[$key];
        return $fallback;
    };
    $heroPercent = static function ($value, $fallback) {
        $raw = trim((string) $value);
        $number = is_numeric($raw) ? (float) $raw : $fallback;
        if (!is_numeric($raw) && preg_match('/^(-?\d+(?:\.\d+)?)%$/', $raw, $matches)) $number = (float) $matches[1];
        return max(0, min(100, $number)) . '%';
    };
    $heroPosition = static function ($target, $suffix) use ($heroResponsive, $heroEnum, $heroPercent) {
        $anchor = $heroEnum($heroResponsive($target . 'Anchor', $suffix, 'center-left'), ['top-left','top-center','top-right','center-left','center','center-right','bottom-left','bottom-center','bottom-right'], 'center-left');
        [$vertical, $horizontal] = $anchor === 'center' ? ['center', 'center'] : explode('-', $anchor);
        $translateX = $horizontal === 'center' ? '-50%' : ($horizontal === 'right' ? '-100%' : '0');
        $translateY = $vertical === 'center' ? '-50%' : ($vertical === 'bottom' ? '-100%' : '0');
        $align = $heroEnum($heroResponsive($target . 'Align', $suffix, 'left'), ['left','center','right'], 'left');
        $flex = ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'][$align];
        return 'left:' . $heroPercent($heroResponsive($target . 'X', $suffix, 0), 0) . ';top:' . $heroPercent($heroResponsive($target . 'Y', $suffix, 0), 0) . ';width:' . $heroPercent($heroResponsive($target . 'Width', $suffix, 40), 40) . ';text-align:' . $align . ';transform:translate(' . $translateX . ',' . $translateY . ');--hero-content-align:' . $flex;
    };
    $heroButtonLayout = static function ($suffix) use ($heroResponsive, $heroEnum, $heroLength, $heroMode) {
        $direction = $heroEnum($heroResponsive('buttonDirection', $suffix, 'row'), ['row','column'], 'row');
        $alignMode = $heroEnum($heroResponsive('buttonAlignMode', $suffix, 'inherit'), ['inherit','custom'], 'inherit');
        $alignTarget = $heroMode === 'grouped' ? 'group' : 'buttons';
        $align = $heroEnum($heroResponsive($alignMode === 'inherit' ? $alignTarget . 'Align' : 'buttonAlign', $suffix, 'left'), ['left','center','right'], 'left');
        $flex = ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'][$align];
        $wrap = $heroResponsive('buttonWrap', $suffix, true) ? 'wrap' : 'nowrap';
        return 'flex-direction:' . $direction . ';gap:' . $heroLength($heroResponsive('buttonGap', $suffix, '10px'), '10px') . ';flex-wrap:' . $wrap . ';justify-content:' . ($direction === 'row' ? $flex : 'initial') . ';align-items:' . ($direction === 'column' ? $flex : 'initial');
    };
    $heroObjectPosition = static fn ($suffix, $fallback) => $heroEnum($heroResponsive('objectPosition', $suffix, $fallback), ['left top','left center','left bottom','center top','center center','center bottom','right top','right center','right bottom'], $fallback);
    $heroVideoUrl = static function (array $button) use ($heroSafeMedia) {
        $raw = $heroSafeMedia($button['videoUrl'] ?? '');
        if ($raw === '') return '';
        $source = $button['videoSource'] ?? 'youtube';
        if ($source === 'self_hosted') return $raw;
        $parts = parse_url($raw);
        if (!is_array($parts) || empty($parts['host'])) return '';
        $host = preg_replace('/^www\./i', '', strtolower($parts['host']));
        $pathParts = array_values(array_filter(explode('/', trim($parts['path'] ?? '', '/'))));
        if ($source === 'youtube' && in_array($host, ['youtube.com', 'youtu.be'], true)) {
            parse_str($parts['query'] ?? '', $query);
            $id = $host === 'youtu.be' ? ($pathParts[0] ?? '') : ($query['v'] ?? end($pathParts));
            return preg_match('/^[A-Za-z0-9_-]+$/', (string) $id) ? 'https://www.youtube.com/embed/' . $id : '';
        }
        if ($source === 'vimeo' && $host === 'vimeo.com') {
            $id = end($pathParts);
            return preg_match('/^\d+$/', (string) $id) ? 'https://player.vimeo.com/video/' . $id : '';
        }
        if ($source === 'dailymotion' && in_array($host, ['dailymotion.com', 'dai.ly'], true)) {
            $id = end($pathParts);
            return preg_match('/^[A-Za-z0-9]+$/', (string) $id) ? 'https://www.dailymotion.com/embed/video/' . $id : '';
        }
        return '';
    };
    $heroCustomAttributes = static function (array $button) {
        $output = [];
        foreach ((array) ($button['linkCustomAttributes'] ?? []) as $attribute) {
            if (!is_array($attribute)) continue;
            $key = trim((string) ($attribute['key'] ?? $attribute['name'] ?? ''));
            if (preg_match('/^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/i', $key)) $output[$key] = (string) ($attribute['value'] ?? '');
        }
        return $output;
    };

    $heroDesktopImage = $heroSafeMedia($heroResponsive('imageUrl', '', ''));
    $heroTabletImage = $heroSafeMedia($heroResponsive('imageUrl', 'Tablet', $heroDesktopImage)) ?: $heroDesktopImage;
    $heroMobileImage = $heroSafeMedia($heroResponsive('imageUrl', 'Mobile', $heroTabletImage)) ?: $heroTabletImage;
    $heroImageLayout = static function ($suffix) use ($heroResponsive, $heroEnum) {
        return $heroEnum(strtolower(trim((string) $heroResponsive('imageLayout', $suffix, 'cover'))), ['cover', 'natural'], 'cover');
    };
    $heroImageLayoutDesktop = $heroImageLayout('');
    $heroImageLayoutTablet = $heroImageLayout('Tablet');
    $heroImageLayoutMobile = $heroImageLayout('Mobile');
    $heroNaturalClasses = [];
    if ($heroImageLayoutDesktop === 'natural') $heroNaturalClasses[] = 'is-natural-image is-natural-image-desktop';
    if ($heroImageLayoutTablet === 'natural') $heroNaturalClasses[] = 'is-natural-image-tablet';
    if ($heroImageLayoutMobile === 'natural') $heroNaturalClasses[] = 'is-natural-image-mobile';
    $heroTitleTag = $heroEnum($heroSettings['titleTag'] ?? 'h2', ['h1','h2','h3','h4','h5','h6','div'], 'h2');
    $heroTitleTagFontSizes = ['h1' => '56px', 'h2' => '48px', 'h3' => '40px', 'h4' => '32px', 'h5' => '24px', 'h6' => '18px', 'div' => '48px'];
    $heroTitleTagFontSizesTablet = ['h1' => '46px', 'h2' => '38px', 'h3' => '32px', 'h4' => '28px', 'h5' => '22px', 'h6' => '18px', 'div' => '38px'];
    $heroTitleTagFontSizesMobile = ['h1' => '40px', 'h2' => '34px', 'h3' => '29px', 'h4' => '24px', 'h5' => '20px', 'h6' => '16px', 'div' => '34px'];
    $heroTitleFontSizeMode = ($heroSettings['titleFontSizeMode'] ?? 'auto') === 'custom' ? 'custom' : 'auto';
    $heroSubtitleTag = $heroEnum($heroSettings['subtitleTag'] ?? 'p', ['p','div','span'], 'p');
    $heroTitleWeight = (string) ($heroSettings['titleFontWeight'] ?? '700');
    $heroSubtitleWeight = (string) ($heroSettings['subtitleFontWeight'] ?? '400');
    $heroRootStyle = implode(';', [
        '--hero-overlay:' . $heroColor($heroSettings['overlayColor'] ?? '', 'rgba(255,255,255,0)'),
        '--hero-title-color:' . $heroColor($heroSettings['titleColor'] ?? '', '#292d32'),
        '--hero-title-weight:' . (preg_match('/^(?:normal|bold|[1-9]00)$/', $heroTitleWeight) ? $heroTitleWeight : '700'),
        '--hero-subtitle-color:' . $heroColor($heroSettings['subtitleColor'] ?? '', '#292d32'),
        '--hero-subtitle-weight:' . (preg_match('/^(?:normal|bold|[1-9]00)$/', $heroSubtitleWeight) ? $heroSubtitleWeight : '400'),
        '--hero-button-color:' . $heroColor($heroSettings['buttonTextColor'] ?? '', '#fff'),
        '--hero-button-bg:' . $heroColor($heroSettings['buttonBackground'] ?? '', '#30343a'),
        '--hero-button-hover-color:' . $heroColor($heroSettings['buttonTextColorHover'] ?? '', '#fff'),
        '--hero-button-hover-bg:' . $heroColor($heroSettings['buttonBackgroundHover'] ?? '', '#1f2328'),
        '--hero-button-radius:' . $heroLength($heroSettings['buttonRadius'] ?? '', '999px'),
        '--hero-button-pad-x:' . $heroLength($heroSettings['buttonPaddingX'] ?? '', '18px'),
        '--hero-button-pad-y:' . $heroLength($heroSettings['buttonPaddingY'] ?? '', '10px'),
        '--hero-modal-background:' . $heroColor($heroSettings['modalBackground'] ?? '', 'rgba(0,0,0,.92)'),
        '--hero-modal-ui:' . $heroColor($heroSettings['modalUiColor'] ?? '', '#fff'),
        '--hero-modal-ui-hover:' . $heroColor($heroSettings['modalUiHoverColor'] ?? '', '#6979f8'),
        '--hero-modal-video-width:' . $heroLength($heroSettings['modalVideoWidth'] ?? '', '75%'),
    ]);
@endphp

<style>
#{{ $heroId }}{min-height:{{ $heroLength($heroResponsive('minHeight','','500px'),'500px') }};--hero-content-gap:{{ $heroLength($heroResponsive('contentGap','','14px'),'14px') }};--hero-title-size:{{ $heroLength($heroTitleFontSizeMode === 'custom' ? $heroResponsive('titleFontSize','','48px') : ($heroTitleTagFontSizes[$heroTitleTag] ?? '48px'),'48px') }};--hero-subtitle-size:{{ $heroLength($heroResponsive('subtitleFontSize','','22px'),'22px') }}}
#{{ $heroId }} .pb-hero-banner__media{object-fit:{{ $heroEnum($heroResponsive('objectFit','','cover'),['cover','contain','fill'],'cover') }};object-position:{{ $heroObjectPosition('','center center') }}}
#{{ $heroId }} .pb-hero-banner__buttons{ {{ $heroButtonLayout('') }} }
@if($heroMode==='grouped')#{{ $heroId }} .pb-hero-banner__content{ {{ $heroPosition('group','') }} }@else @foreach(['title','subtitle','buttons'] as $target)#{{ $heroId }} .pb-hero-banner__block--{{ $target }}{ {{ $heroPosition($target,'') }} }@endforeach @endif
@media(max-width:1024px){#{{ $heroId }}{min-height:{{ $heroLength($heroResponsive('minHeight','Tablet','520px'),'520px') }};--hero-content-gap:{{ $heroLength($heroResponsive('contentGap','Tablet','14px'),'14px') }};--hero-title-size:{{ $heroLength($heroTitleFontSizeMode === 'custom' ? $heroResponsive('titleFontSize','Tablet','38px') : ($heroTitleTagFontSizesTablet[$heroTitleTag] ?? '38px'),'38px') }};--hero-subtitle-size:{{ $heroLength($heroResponsive('subtitleFontSize','Tablet','18px'),'18px') }}}#{{ $heroId }} .pb-hero-banner__media{object-fit:{{ $heroEnum($heroResponsive('objectFit','Tablet','cover'),['cover','contain','fill'],'cover') }};object-position:{{ $heroObjectPosition('Tablet','center center') }}}#{{ $heroId }} .pb-hero-banner__buttons{ {{ $heroButtonLayout('Tablet') }} }@if($heroMode==='grouped')#{{ $heroId }} .pb-hero-banner__content{ {{ $heroPosition('group','Tablet') }} }@else @foreach(['title','subtitle','buttons'] as $target)#{{ $heroId }} .pb-hero-banner__block--{{ $target }}{ {{ $heroPosition($target,'Tablet') }} }@endforeach @endif}
@media(max-width:767px){#{{ $heroId }}{min-height:{{ $heroLength($heroResponsive('minHeight','Mobile','680px'),'680px') }};--hero-content-gap:{{ $heroLength($heroResponsive('contentGap','Mobile','10px'),'10px') }};--hero-title-size:{{ $heroLength($heroTitleFontSizeMode === 'custom' ? $heroResponsive('titleFontSize','Mobile','34px') : ($heroTitleTagFontSizesMobile[$heroTitleTag] ?? '34px'),'34px') }};--hero-subtitle-size:{{ $heroLength($heroResponsive('subtitleFontSize','Mobile','17px'),'17px') }}}#{{ $heroId }} .pb-hero-banner__media{object-fit:{{ $heroEnum($heroResponsive('objectFit','Mobile','cover'),['cover','contain','fill'],'cover') }};object-position:{{ $heroObjectPosition('Mobile','center top') }}}#{{ $heroId }} .pb-hero-banner__buttons{ {{ $heroButtonLayout('Mobile') }} }@if($heroMode==='grouped')#{{ $heroId }} .pb-hero-banner__content{ {{ $heroPosition('group','Mobile') }} }@else @foreach(['title','subtitle','buttons'] as $target)#{{ $heroId }} .pb-hero-banner__block--{{ $target }}{ {{ $heroPosition($target,'Mobile') }} }@endforeach @endif}
</style>

<section id="{{ $heroId }}" class="pb-hero-banner is-{{ $heroMode }}{{ $heroNaturalClasses ? ' ' . implode(' ', $heroNaturalClasses) : '' }}" data-hero-banner data-hero-image-layout="{{ $heroImageLayoutDesktop }}" style="{{ $heroRootStyle }}">
    @if($heroDesktopImage)
        <picture class="pb-hero-banner__picture">
            @if($heroMobileImage && $heroMobileImage !== $heroTabletImage)<source media="(max-width:767px)" srcset="{{ $heroMobileImage }}">@endif
            @if($heroTabletImage && $heroTabletImage !== $heroDesktopImage)<source media="(max-width:1024px)" srcset="{{ $heroTabletImage }}">@endif
            <img class="pb-hero-banner__media" src="{{ $heroDesktopImage }}" alt="{{ e($heroResponsive('imageAlt','','')) }}">
        </picture>
    @else
        <div class="pb-hero-banner__empty" role="img" aria-label="Hero image unavailable"><i class="far fa-image" aria-hidden="true"></i></div>
    @endif
    <div class="pb-hero-banner__overlay"></div>
    <div class="pb-hero-banner__content">
        @foreach($heroOrder as $contentKey)
            @if($contentKey === 'title' && ($heroSettings['showTitle'] ?? true))
                <div class="pb-hero-banner__block pb-hero-banner__block--title"><{{ $heroTitleTag }} class="pb-hero-banner__title">{{ $heroSettings['title'] ?? '' }}</{{ $heroTitleTag }}></div>
            @elseif($contentKey === 'subtitle' && ($heroSettings['showSubtitle'] ?? true))
                <div class="pb-hero-banner__block pb-hero-banner__block--subtitle"><{{ $heroSubtitleTag }} class="pb-hero-banner__subtitle">{{ $heroSettings['subtitle'] ?? '' }}</{{ $heroSubtitleTag }}></div>
            @elseif($contentKey === 'buttons' && ($heroSettings['showButtons'] ?? true))
                <div class="pb-hero-banner__block pb-hero-banner__block--buttons"><div class="pb-hero-banner__buttons">
                    @foreach($heroButtons as $button)
                        @php
                            $action = $heroEnum($button['actionType'] ?? 'link', ['link','video_popup','image_popup'], 'link');
                            $text = trim((string) ($button['text'] ?? '')) ?: 'Button';
                            $icon = $action === 'video_popup' ? 'fas fa-play' : ($action === 'image_popup' ? 'far fa-image' : 'fas fa-arrow-right');
                            $link = $action === 'link' ? $heroSafeLink($button['linkUrl'] ?? '') : '';
                            $popup = $action === 'video_popup' ? $heroVideoUrl($button) : ($action === 'image_popup' ? $heroSafeMedia($button['imageUrl'] ?? '') : '');
                            $target = ($button['linkTarget'] ?? '') === '_blank' ? '_blank' : '';
                            $rels = [];
                            if ($target) $rels = ['noopener','noreferrer'];
                            if (!empty($button['linkNofollow'])) $rels[] = 'nofollow';
                            $rel = implode(' ', array_unique($rels));
                            $attributes = $heroCustomAttributes($button);
                        @endphp
                        @if($link)
                            <a class="pb-hero-banner__button" href="{{ $link }}" @if($target) target="_blank" @endif @if($rel) rel="{{ $rel }}" @endif @foreach($attributes as $name=>$value) {{ $name }}="{{ e($value) }}" @endforeach><span>{{ $text }}</span><i class="{{ $icon }}" aria-hidden="true"></i></a>
                        @elseif($popup)
                            <button type="button" class="pb-hero-banner__button" data-hero-media data-media-type="{{ $action === 'video_popup' ? 'video' : 'image' }}" data-media-src="{{ $popup }}" data-media-alt="{{ e($button['imageAlt'] ?? $text) }}"><span>{{ $text }}</span><i class="{{ $icon }}" aria-hidden="true"></i></button>
                        @else
                            <button type="button" class="pb-hero-banner__button is-disabled" disabled><span>{{ $text }}</span><i class="{{ $icon }}" aria-hidden="true"></i></button>
                        @endif
                    @endforeach
                </div></div>
            @endif
        @endforeach
    </div>
</section>
