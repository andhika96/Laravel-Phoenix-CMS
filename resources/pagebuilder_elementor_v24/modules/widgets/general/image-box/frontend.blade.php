@php
	$imageBoxSettings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeId = trim((string) ($node['id'] ?? 'image-box')) ?: 'image-box';
	$dynamicBindings = is_array($imageBoxSettings['dynamicBindings'] ?? null) ? $imageBoxSettings['dynamicBindings'] : [];
	$dynamicContext = request()->attributes->get('pagebuilder_dynamic_context', []);
	$dynamicContext = is_array($dynamicContext) ? $dynamicContext : [];
	if (!array_key_exists('page', $dynamicContext) && isset($pageData)) $dynamicContext['page'] = $pageData;
	$dynamicContext['page_url'] ??= url()->current();
	$dynamicContext['site_title'] ??= config('app.name');
	$dynamicContext['site_url'] ??= config('app.url');
	$dynamicContext['user'] ??= request()->user();

	$dynamicResolver = app(\App\Support\PageBuilderElementorV24\DynamicTagResolver::class);
	$resolveDynamic = fn (string $field, mixed $fallback): mixed => $dynamicResolver->resolve($field, $fallback, $dynamicBindings, $dynamicContext);
	$imageUrl = trim((string) $resolveDynamic('imageUrl', $imageBoxSettings['imageUrl'] ?? ''));
	$imageAlt = (string) $resolveDynamic('imageAlt', $imageBoxSettings['imageAlt'] ?? '');
	$title = (string) $resolveDynamic('title', $imageBoxSettings['title'] ?? 'This is the heading');
	$description = (string) $resolveDynamic('description', $imageBoxSettings['description'] ?? '');
	$linkUrl = trim((string) $resolveDynamic('linkUrl', $imageBoxSettings['linkUrl'] ?? ''));

	$safeImageUrl = function (mixed $value): string {
		$url = trim((string) $value);
		if ($url === '' || str_starts_with($url, '//')) return '';
		return preg_match('/^(?:https?:|data:image\/(?:png|gif|jpe?g|webp);base64,|\/)/i', $url) ? $url : '';
	};
	$safeLinkUrl = function (mixed $value): string {
		$url = trim((string) $value);
		if ($url === '' || str_starts_with($url, '//')) return '';
		return preg_match('/^(?:https?:|mailto:|tel:|\/|#)/i', $url) ? $url : '';
	};
	$imageUrl = $safeImageUrl($imageUrl);
	$linkUrl = $safeLinkUrl($linkUrl);
	$imageResolution = strtolower(trim((string) ($imageBoxSettings['imageResolution'] ?? 'full')));
	if (!in_array($imageResolution, ['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048', 'full', 'custom'], true)) $imageResolution = 'full';
	$customImageWidth = max(1, min(4096, (int) ($imageBoxSettings['customImageWidth'] ?? 150)));
	$customImageHeight = max(1, min(4096, (int) ($imageBoxSettings['customImageHeight'] ?? 150)));
	$imageUrl = $imageUrl !== ''
		? app(\App\Support\PageBuilderElementorV24\ImageRenditionResolver::class)->resolve($imageUrl, $imageResolution, $imageResolution === 'custom' ? $customImageWidth : null, $imageResolution === 'custom' ? $customImageHeight : null)
		: '';

	$titleTag = strtolower(trim((string) ($imageBoxSettings['titleTag'] ?? 'h3')));
	if (!in_array($titleTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'], true)) $titleTag = 'h3';
	$titleTagFontSizes = ['h1' => '40px', 'h2' => '34px', 'h3' => '29px', 'h4' => '24px', 'h5' => '20px', 'h6' => '16px', 'div' => '29px', 'span' => '29px', 'p' => '29px'];
	$storedTitleFontSize = trim((string) ($imageBoxSettings['titleFontSize'] ?? ''));
	$titleFontSizeMode = in_array($imageBoxSettings['titleFontSizeMode'] ?? null, ['auto', 'custom'], true)
		? $imageBoxSettings['titleFontSizeMode']
		: ($storedTitleFontSize !== '' && $storedTitleFontSize !== '29px' ? 'custom' : 'auto');
	$linkTarget = ($imageBoxSettings['linkTarget'] ?? '') === '_blank' ? '_blank' : '';
	$relTokens = [];
	if ($linkTarget === '_blank') $relTokens = ['noopener', 'noreferrer'];
	if (in_array($imageBoxSettings['linkNofollow'] ?? false, [true, 1, '1', 'true'], true)) $relTokens[] = 'nofollow';
	$linkRel = implode(' ', array_values(array_unique($relTokens)));
	$linkAttributes = [];
	foreach (($imageBoxSettings['linkCustomAttributes'] ?? []) as $attribute) {
		if (!is_array($attribute)) continue;
		$name = strtolower(trim((string) ($attribute['key'] ?? $attribute['name'] ?? '')));
		if (!preg_match('/^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/', $name)) continue;
		$linkAttributes[$name] = (string) ($attribute['value'] ?? '');
	}

	$responsive = function (string $base, string $suffix = '', mixed $fallback = '') use ($imageBoxSettings): mixed {
		$keys = $suffix === 'Mobile'
			? [$base . 'Mobile', $base . 'Tablet', $base]
			: ($suffix === 'Tablet' ? [$base . 'Tablet', $base] : [$base]);
		foreach ($keys as $key) {
			$value = $imageBoxSettings[$key] ?? null;
			if ($value !== '' && $value !== null) return $value;
		}
		return $fallback;
	};
	$cssLength = function (mixed $value, string $fallback = ''): string {
		$raw = trim((string) $value);
		return preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i', $raw) ? $raw : $fallback;
	};
	$cssColor = function (mixed $value, string $fallback = 'inherit'): string {
		$raw = trim((string) $value);
		return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : $fallback;
	};
	$cssShadow = function (mixed $value): string {
		$raw = trim((string) $value);
		return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : 'none';
	};
	$cssFontFamily = function (mixed $value): string {
		$raw = trim((string) $value);
		return $raw !== '' && preg_match('/^[A-Za-z0-9 _,\'"-]+$/', $raw) ? $raw : 'inherit';
	};
	$enum = function (mixed $value, array $allowed, string $fallback): string {
		$value = strtolower(trim((string) $value));
		return in_array($value, $allowed, true) ? $value : $fallback;
	};
	$opacity = fn (mixed $value, float $fallback = 1): float => is_numeric($value) ? max(0, min(1, (float) $value)) : $fallback;
	$duration = fn (mixed $value): float => is_numeric($value) ? max(0, min(10, (float) $value)) : 0.3;
	$filterCss = function (mixed $filters): string {
		$filters = is_array($filters) ? $filters : [];
		$blur = max(0, min(100, (float) ($filters['blur'] ?? 0)));
		$brightness = max(0, min(200, (float) ($filters['brightness'] ?? 100)));
		$contrast = max(0, min(200, (float) ($filters['contrast'] ?? 100)));
		$saturation = max(0, min(200, (float) ($filters['saturation'] ?? 100)));
		$hue = max(0, min(360, (float) ($filters['hue'] ?? 0)));
		return "blur({$blur}px) brightness({$brightness}%) contrast({$contrast}%) saturate({$saturation}%) hue-rotate({$hue}deg)";
	};
	$position = fn (string $suffix = ''): string => $enum($responsive('imagePosition', $suffix, 'top'), ['top', 'left', 'right'], 'top');
	$alignment = fn (string $suffix = ''): string => $enum($responsive('alignment', $suffix, 'center'), ['left', 'center', 'right', 'justify'], 'center');
	$alignItems = fn (string $value): string => $value === 'left' ? 'flex-start' : ($value === 'right' ? 'flex-end' : ($value === 'justify' ? 'stretch' : 'center'));
	$flexDirection = fn (string $value): string => $value === 'left' ? 'row' : ($value === 'right' ? 'row-reverse' : 'column');

	$imageBorderType = $enum($imageBoxSettings['imageBorderType'] ?? 'none', ['none', 'solid', 'double', 'dotted', 'dashed', 'groove'], 'none');
	$imageTransition = $duration($imageBoxSettings['imageHoverTransition'] ?? 0.3);
	$desktopPosition = $position('');
	$desktopImageWidth = $cssLength($responsive('imageWidth', '', '30%'), '30%');
	$imageStyle = implode(';', [
		'width:' . ($desktopPosition === 'top' ? $desktopImageWidth : '100%'),
		'max-width:100%',
		'display:block',
		'border-style:' . $imageBorderType,
		'border-width:' . ($imageBorderType === 'none' ? '0' : $cssLength($imageBoxSettings['imageBorderWidth'] ?? '1px', '1px')),
		'border-color:' . $cssColor($imageBoxSettings['imageBorderColor'] ?? '#000000', '#000000'),
		'border-radius:' . $cssLength($responsive('imageBorderRadius', '', '0px'), '0px'),
		'filter:' . $filterCss($imageBoxSettings['imageNormalFilter'] ?? []),
		'opacity:' . $opacity($imageBoxSettings['imageNormalOpacity'] ?? 1),
		'transition:filter ' . $imageTransition . 's ease,opacity ' . $imageTransition . 's ease',
	]);

	$typographyStyle = function (string $prefix, string $suffix = '') use ($imageBoxSettings, $responsive, $cssLength, $cssColor, $cssShadow, $cssFontFamily, $enum, $titleTag, $titleTagFontSizes, $titleFontSizeMode): string {
		$isTitle = $prefix === 'title';
		$fontSize = $isTitle && $titleFontSizeMode === 'auto'
			? ($titleTagFontSizes[$titleTag] ?? '29px')
			: $cssLength($responsive($prefix . 'FontSize', $suffix, $isTitle ? '29px' : '16px'), $isTitle ? '29px' : '16px');
		$rules = [
			'font-family:' . $cssFontFamily($imageBoxSettings[$prefix . 'FontFamily'] ?? 'inherit'),
			'font-size:' . $fontSize,
			'font-weight:' . (preg_match('/^(?:normal|bold|[1-9]00)$/', trim((string) ($imageBoxSettings[$prefix . 'FontWeight'] ?? '400'))) ? trim((string) ($imageBoxSettings[$prefix . 'FontWeight'] ?? '400')) : '400'),
			'line-height:' . $cssLength($responsive($prefix . 'LineHeight', $suffix, $isTitle ? '1.2em' : '1.5em'), $isTitle ? '1.2em' : '1.5em'),
			'letter-spacing:' . $cssLength($responsive($prefix . 'LetterSpacing', $suffix, '0px'), '0px'),
			'word-spacing:' . $cssLength($responsive($prefix . 'WordSpacing', $suffix, '0px'), '0px'),
			'text-transform:' . $enum($imageBoxSettings[$prefix . 'TextTransform'] ?? 'none', ['none', 'uppercase', 'lowercase', 'capitalize'], 'none'),
			'font-style:' . $enum($imageBoxSettings[$prefix . 'FontStyle'] ?? 'normal', ['normal', 'italic', 'oblique'], 'normal'),
			'text-decoration:' . $enum($imageBoxSettings[$prefix . 'TextDecoration'] ?? 'none', ['none', 'underline', 'overline', 'line-through'], 'none'),
			'color:' . $cssColor($imageBoxSettings[$prefix . 'Color'] ?? '', 'inherit'),
			'text-shadow:' . $cssShadow($imageBoxSettings[$prefix . 'TextShadow'] ?? 'none'),
		];
		if ($isTitle) {
			$rules[] = '-webkit-text-stroke-width:' . $cssLength($responsive('titleTextStrokeWidth', $suffix, '0px'), '0px');
			$rules[] = '-webkit-text-stroke-color:' . $cssColor($imageBoxSettings['titleTextStrokeColor'] ?? 'currentColor', 'currentColor');
		}
		return implode(';', $rules);
	};

	$desktopAlignment = $alignment('');
	$boxStyle = implode(';', [
		'flex-direction:' . $flexDirection($desktopPosition),
		'align-items:' . ($desktopPosition === 'top' ? $alignItems($desktopAlignment) : 'center'),
		'text-align:' . $desktopAlignment,
		'--pb-image-box-image-spacing:' . $cssLength($responsive('imageSpacing', '', '15px'), '15px'),
		'--pb-image-box-content-spacing:' . $cssLength($responsive('contentSpacing', '', '0px'), '0px'),
		'--pb-image-box-media-justify:' . ($desktopPosition === 'top' ? $alignItems($desktopAlignment) : 'center'),
		'--pb-image-box-hover-filter:' . $filterCss($imageBoxSettings['imageHoverFilter'] ?? []),
		'--pb-image-box-hover-opacity:' . $opacity($imageBoxSettings['imageHoverOpacity'] ?? 1),
		'--pb-image-box-hover-transition:' . $imageTransition . 's',
	]);
	$mediaStyle = implode(';', [
		'width:' . ($desktopPosition === 'top' ? '100%' : $desktopImageWidth),
		'flex:' . ($desktopPosition === 'top' ? '0 0 auto' : '0 0 ' . $desktopImageWidth),
		'max-width:100%',
		'margin:0',
		$desktopPosition === 'top' ? 'margin-bottom:var(--pb-image-box-image-spacing)' : '',
		$desktopPosition === 'left' ? 'margin-right:var(--pb-image-box-image-spacing)' : '',
		$desktopPosition === 'right' ? 'margin-left:var(--pb-image-box-image-spacing)' : '',
	]);

	$advanced = app(\App\Support\PageBuilderElementorV24\WidgetAdvancedStyleResolver::class)->resolve($imageBoxSettings, $nodeId, request());
	$rootClasses = array_values(array_unique(array_merge(
		['el-widget-image-box', 'pb-image-box'],
		$advanced['classes'],
		['pb-image-box--position-' . $desktopPosition],
	)));

	$mediaRules = [];
	foreach (['Tablet' => 1024, 'Mobile' => 767] as $suffix => $breakpoint) {
		$currentPosition = $position($suffix);
		$currentAlignment = $alignment($suffix);
		$rootRules = [
			'flex-direction:' . $flexDirection($currentPosition),
			'align-items:' . ($currentPosition === 'top' ? $alignItems($currentAlignment) : 'center'),
			'text-align:' . $currentAlignment,
			'--pb-image-box-image-spacing:' . $cssLength($responsive('imageSpacing', $suffix, '15px'), '15px'),
			'--pb-image-box-content-spacing:' . $cssLength($responsive('contentSpacing', $suffix, '0px'), '0px'),
			'--pb-image-box-media-justify:' . ($currentPosition === 'top' ? $alignItems($currentAlignment) : 'center'),
		];
		$currentImageWidth = $cssLength($responsive('imageWidth', $suffix, '30%'), '30%');
		$mediaLayoutRules = [
			'width:' . ($currentPosition === 'top' ? '100%' : $currentImageWidth),
			'flex:' . ($currentPosition === 'top' ? '0 0 auto' : '0 0 ' . $currentImageWidth),
			'max-width:100%',
			'margin:0',
		];
		if ($currentPosition === 'top') $mediaLayoutRules[] = 'margin-bottom:var(--pb-image-box-image-spacing)';
		if ($currentPosition === 'left') $mediaLayoutRules[] = 'margin-right:var(--pb-image-box-image-spacing)';
		if ($currentPosition === 'right') $mediaLayoutRules[] = 'margin-left:var(--pb-image-box-image-spacing)';
		$imageRules = [
			'width:' . ($currentPosition === 'top' ? $currentImageWidth : '100%'),
			'border-radius:' . $cssLength($responsive('imageBorderRadius', $suffix, '0px'), '0px'),
		];
		$mediaRules[] = '@media (max-width: ' . $breakpoint . 'px){#' . $advanced['id'] . '{' . implode(';', $rootRules) . '}#' . $advanced['id'] . ' > .pb-image-box__media{' . implode(';', $mediaLayoutRules) . '}#' . $advanced['id'] . ' .pb-image-box__image{' . implode(';', $imageRules) . '}#' . $advanced['id'] . ' .pb-image-box__title{' . $typographyStyle('title', $suffix) . '}#' . $advanced['id'] . ' .pb-image-box__description{' . $typographyStyle('description', $suffix) . '}}';
	}
	$baseCss = '#' . $advanced['id'] . '{display:flex;width:100%;min-width:0}'
		. '#' . $advanced['id'] . '>.pb-image-box__media{display:flex;min-width:0;justify-content:var(--pb-image-box-media-justify,center)}'
		. '#' . $advanced['id'] . ' .pb-image-box__image-link{display:flex;width:100%;max-width:100%;justify-content:var(--pb-image-box-media-justify,center);color:inherit}'
		. '#' . $advanced['id'] . ' .pb-image-box__image{height:auto;object-fit:cover}'
		. '#' . $advanced['id'] . ' .pb-image-box__empty-media{display:grid;width:min(100%,320px);aspect-ratio:16/9;place-items:center;border:1px solid #d8dee8;border-radius:4px;background:#f2f4f7;color:#98a2b3;font-size:44px}'
		. '#' . $advanced['id'] . '>.pb-image-box__content{min-width:0;flex:1 1 auto}'
		. '#' . $advanced['id'] . ' .pb-image-box__title-link{color:inherit;text-decoration:none}'
		. '#' . $advanced['id'] . ' .pb-image-box__title{margin:0 0 var(--pb-image-box-content-spacing,0)}'
		. '#' . $advanced['id'] . ' .pb-image-box__description{margin:0}'
		. '#' . $advanced['id'] . ':hover .pb-image-box__image{filter:var(--pb-image-box-hover-filter);opacity:var(--pb-image-box-hover-opacity,1)}';
@endphp

<div
	id="{{ $advanced['id'] }}"
	class="{{ implode(' ', $rootClasses) }}"
	style="{{ $boxStyle }}"
	data-pb-motion="{{ $advanced['motion'] }}"
	data-entrance-delay="{{ $advanced['entranceDelay'] }}"
	data-entrance-duration="{{ $advanced['entranceDuration'] }}"
	@foreach($advanced['attributes'] as $attributeName => $attributeValue)
		{{ $attributeName }}="{{ e($attributeValue) }}"
	@endforeach
>
	<div class="pb-image-box__media" style="{{ $mediaStyle }}">
		@if($linkUrl !== '')
			<a class="pb-image-box__image-link" href="{{ $linkUrl }}" @if($linkTarget !== '') target="{{ $linkTarget }}" @endif @if($linkRel !== '') rel="{{ $linkRel }}" @endif
				@foreach($linkAttributes as $attributeName => $attributeValue)
					{{ $attributeName }}="{{ e($attributeValue) }}"
				@endforeach
			>
				@if($imageUrl !== '')
					<img class="pb-image-box__image" src="{{ $imageUrl }}" alt="{{ $imageAlt }}" style="{{ $imageStyle }}">
				@else
					<div class="pb-image-box__empty-media" role="img" aria-label="Choose an image"><i class="far fa-image" aria-hidden="true"></i></div>
				@endif
			</a>
		@elseif($imageUrl !== '')
			<img class="pb-image-box__image" src="{{ $imageUrl }}" alt="{{ $imageAlt }}" style="{{ $imageStyle }}">
		@else
			<div class="pb-image-box__empty-media" role="img" aria-label="Choose an image"><i class="far fa-image" aria-hidden="true"></i></div>
		@endif
	</div>

	<div class="pb-image-box__content">
		@if($title !== '' && $linkUrl !== '')
			<a class="pb-image-box__title-link" href="{{ $linkUrl }}" @if($linkTarget !== '') target="{{ $linkTarget }}" @endif @if($linkRel !== '') rel="{{ $linkRel }}" @endif
				@foreach($linkAttributes as $attributeName => $attributeValue)
					{{ $attributeName }}="{{ e($attributeValue) }}"
				@endforeach
			>
				<{{ $titleTag }} class="pb-image-box__title" style="{{ $typographyStyle('title') }}">{{ $title }}</{{ $titleTag }}>
			</a>
		@elseif($title !== '')
			<{{ $titleTag }} class="pb-image-box__title" style="{{ $typographyStyle('title') }}">{{ $title }}</{{ $titleTag }}>
		@endif
		@if($description !== '')
			<p class="pb-image-box__description" style="{{ $typographyStyle('description') }}">{{ $description }}</p>
		@endif
	</div>
</div>
<style>{!! $advanced['css'] . $baseCss . implode('', $mediaRules) !!}</style>
