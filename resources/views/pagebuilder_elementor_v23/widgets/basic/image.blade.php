@php
	$settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeId = trim((string) ($node['id'] ?? 'image')) ?: 'image';
	$dynamicBindings = is_array($settings['dynamicBindings'] ?? null) ? $settings['dynamicBindings'] : [];
	$dynamicContext = request()->attributes->get('pagebuilder_dynamic_context', []);
	$dynamicContext = is_array($dynamicContext) ? $dynamicContext : [];
	if (!array_key_exists('page', $dynamicContext) && isset($pageData)) $dynamicContext['page'] = $pageData;
	$dynamicContext['page_url'] ??= url()->current();
	$dynamicContext['site_title'] ??= config('app.name');
	$dynamicContext['site_url'] ??= config('app.url');
	$dynamicContext['user'] ??= request()->user();
	$dynamicResolver = app(\App\Support\PageBuilderElementorV23\DynamicTagResolver::class);
	$resolveDynamic = fn (string $field, mixed $fallback): mixed => $dynamicResolver->resolve($field, $fallback, $dynamicBindings, $dynamicContext);

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
	$enum = function (mixed $value, array $allowed, string $fallback): string {
		$value = strtolower(trim((string) $value));
		return in_array($value, $allowed, true) ? $value : $fallback;
	};
	$cssLength = function (mixed $value, string $fallback = ''): string {
		$raw = trim((string) $value);
		return $raw === 'auto' || preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i', $raw) ? $raw : $fallback;
	};
	$cssColor = function (mixed $value, string $fallback = 'inherit'): string {
		$raw = trim((string) $value);
		return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : $fallback;
	};
	$cssShadow = function (mixed $value): string {
		$raw = trim((string) $value);
		return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : 'none';
	};
	$fontFamily = function (mixed $value): string {
		$raw = trim((string) $value);
		return $raw !== '' && preg_match('/^[A-Za-z0-9 _,\'"-]+$/', $raw) ? $raw : 'inherit';
	};
	$responsive = function (string $base, string $suffix = '', mixed $fallback = '') use ($settings): mixed {
		$keys = $suffix === 'Mobile' ? [$base . 'Mobile', $base . 'Tablet', $base] : ($suffix === 'Tablet' ? [$base . 'Tablet', $base] : [$base]);
		foreach ($keys as $key) {
			$value = $settings[$key] ?? null;
			if ($value !== '' && $value !== null) return $value;
		}
		return $fallback;
	};
	$filterCss = function (mixed $filters): string {
		$filters = is_array($filters) ? $filters : [];
		$blur = max(0, min(100, (float) ($filters['blur'] ?? 0)));
		$brightness = max(0, min(200, (float) ($filters['brightness'] ?? 100)));
		$contrast = max(0, min(200, (float) ($filters['contrast'] ?? 100)));
		$saturation = max(0, min(200, (float) ($filters['saturation'] ?? 100)));
		$hue = max(0, min(360, (float) ($filters['hue'] ?? 0)));
		return "blur({$blur}px) brightness({$brightness}%) contrast({$contrast}%) saturate({$saturation}%) hue-rotate({$hue}deg)";
	};
	$opacity = fn (mixed $value, float $fallback = 1): float => is_numeric($value) ? max(0, min(1, (float) $value)) : $fallback;
	$duration = fn (mixed $value): float => is_numeric($value) ? max(0, min(10, (float) $value)) : 0.3;

	$imageUrl = $safeImageUrl($resolveDynamic('src', $settings['src'] ?? ''));
	$imageResolution = $enum($settings['imageResolution'] ?? 'large', ['thumbnail','medium','medium_large','large','1536x1536','2048x2048','full','custom'], 'large');
	$customDimension = function (string $key) use ($settings): ?int {
		$raw = trim((string) ($settings[$key] ?? ''));
		return $raw === '' ? null : max(1, min(4096, (int) $raw));
	};
	$customImageWidth = $customDimension('customImageWidth');
	$customImageHeight = $customDimension('customImageHeight');
	if ($imageUrl !== '') {
		$imageUrl = app(\App\Support\PageBuilderElementorV23\ImageRenditionResolver::class)->resolve(
			$imageUrl,
			$imageResolution,
			$imageResolution === 'custom' ? $customImageWidth : null,
			$imageResolution === 'custom' ? $customImageHeight : null,
		);
	}
	$imageAlt = (string) ($settings['alt'] ?? '');
	$captionType = $enum($settings['captionType'] ?? 'none', ['none','attachment','custom'], 'none');
	$caption = $captionType === 'custom'
		? (string) $resolveDynamic('customCaption', $settings['customCaption'] ?? '')
		: ($captionType === 'attachment' ? (string) ($settings['attachmentCaption'] ?? '') : '');

	$linkType = $enum($settings['linkType'] ?? 'none', ['none','media','custom'], 'none');
	$usesLightbox = $linkType === 'media' && $enum($settings['lightbox'] ?? 'default', ['default','yes','no'], 'default') !== 'no';
	$linkUrl = $linkType === 'media' ? $imageUrl : ($linkType === 'custom' ? $safeLinkUrl($resolveDynamic('customLinkUrl', $settings['customLinkUrl'] ?? '')) : '');
	$linkTarget = $linkType === 'custom' && ($settings['linkTarget'] ?? '') === '_blank' ? '_blank' : '';
	$relTokens = [];
	if ($linkTarget === '_blank') $relTokens = ['noopener','noreferrer'];
	if ($linkType === 'custom' && in_array($settings['linkNofollow'] ?? false, [true,1,'1','true'], true)) $relTokens[] = 'nofollow';
	$linkRel = implode(' ', array_values(array_unique($relTokens)));
	$linkAttributes = [];
	if ($linkType === 'custom') foreach (($settings['linkCustomAttributes'] ?? []) as $attribute) {
		if (!is_array($attribute)) continue;
		$name = strtolower(trim((string) ($attribute['key'] ?? $attribute['name'] ?? '')));
		if (!preg_match('/^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/', $name)) continue;
		$linkAttributes[$name] = (string) ($attribute['value'] ?? '');
	}

	$alignment = fn (string $suffix = ''): string => $enum($responsive('alignment', $suffix, 'center'), ['left','center','right'], 'center');
	$captionAlignment = fn (string $suffix = ''): string => $enum($responsive('captionAlignment', $suffix, 'center'), ['left','center','right','justify'], 'center');
	$objectFit = fn (string $suffix = ''): string => $enum($responsive('objectFit', $suffix, 'default'), ['default','fill','cover','contain','scale-down'], 'default');
	$objectPositions = ['center center','center left','center right','top center','top left','top right','bottom center','bottom left','bottom right'];
	$objectPosition = fn (string $suffix = ''): string => $enum($responsive('objectPosition', $suffix, 'center center'), $objectPositions, 'center center');
	$borderType = $enum($settings['imageBorderType'] ?? 'default', ['default','none','solid','double','dotted','dashed','groove'], 'default');
	$borderStyle = in_array($borderType, ['solid','double','dotted','dashed','groove'], true) ? $borderType : 'none';
	$boxShadow = 'none';
	if (in_array($settings['imageBoxShadowEnabled'] ?? false, [true,1,'1','true'], true)) {
		$boxShadow = implode(' ', [
			$cssLength($settings['imageBoxShadowX'] ?? '0px', '0px'), $cssLength($settings['imageBoxShadowY'] ?? '0px', '0px'),
			$cssLength($settings['imageBoxShadowBlur'] ?? '10px', '10px'), $cssLength($settings['imageBoxShadowSpread'] ?? '0px', '0px'),
			$cssColor($settings['imageBoxShadowColor'] ?? 'rgba(0,0,0,.25)', 'rgba(0,0,0,.25)'),
		]);
	}
	$imageStyle = implode(';', [
		'width:' . $cssLength($responsive('width', '', '100%'), '100%'),
		'max-width:' . $cssLength($responsive('maxWidth', '', '100%'), '100%'),
		'height:' . $cssLength($responsive('height', '', 'auto'), 'auto'),
		'object-fit:' . ($objectFit() === 'default' ? 'fill' : $objectFit()),
		'object-position:' . $objectPosition(), 'display:block',
		'border-style:' . $borderStyle, 'border-width:' . ($borderStyle === 'none' ? '0' : $cssLength($responsive('imageBorderWidth', '', '1px'), '1px')),
		'border-color:' . $cssColor($settings['imageBorderColor'] ?? '#000000', '#000000'),
		'border-radius:' . $cssLength($responsive('imageBorderRadius', '', '0px'), '0px'),
		'box-shadow:' . $boxShadow, 'filter:' . $filterCss($settings['imageNormalFilter'] ?? []),
		'opacity:' . $opacity($settings['imageNormalOpacity'] ?? 1),
	]);
	$captionStyle = function (string $suffix = '') use ($settings,$responsive,$captionAlignment,$cssLength,$cssColor,$cssShadow,$fontFamily,$enum): string {
		$weight = trim((string) ($settings['captionFontWeight'] ?? '400'));
		if (!preg_match('/^(?:inherit|normal|bold|[1-9]00)$/', $weight)) $weight = '400';
		return implode(';', [
			'color:' . $cssColor($settings['captionColor'] ?? '', 'inherit'), 'background-color:' . $cssColor($settings['captionBackgroundColor'] ?? '', 'transparent'),
			'text-align:' . $captionAlignment($suffix), 'font-family:' . $fontFamily($settings['captionFontFamily'] ?? 'inherit'),
			'font-size:' . $cssLength($responsive('captionFontSize', $suffix, '16px'), '16px'), 'font-weight:' . $weight,
			'line-height:' . $cssLength($responsive('captionLineHeight', $suffix, '1.5em'), '1.5em'),
			'letter-spacing:' . $cssLength($responsive('captionLetterSpacing', $suffix, '0px'), '0px'), 'word-spacing:' . $cssLength($responsive('captionWordSpacing', $suffix, '0px'), '0px'),
			'text-transform:' . $enum($settings['captionTextTransform'] ?? 'none', ['none','uppercase','lowercase','capitalize'], 'none'),
			'font-style:' . $enum($settings['captionFontStyle'] ?? 'normal', ['normal','italic','oblique'], 'normal'),
			'text-decoration:' . $enum($settings['captionTextDecoration'] ?? 'none', ['none','underline','overline','line-through'], 'none'),
			'text-shadow:' . $cssShadow($settings['captionTextShadow'] ?? 'none'), 'margin-top:' . $cssLength($responsive('captionSpacing', $suffix, '8px'), '8px'),
		]);
	};

	$hoverAnimations = ['none','grow','shrink','pulse','pulse-grow','pulse-shrink','push','pop','bounce-in','bounce-out','rotate','grow-rotate','float','sink','bob','hang','skew','skew-forward','skew-backward','wobble-vertical','wobble-horizontal','wobble-to-bottom-right','wobble-to-top-right','wobble-top','wobble-bottom','wobble-skew','buzz','buzz-out'];
	$hoverAnimation = $enum($settings['imageHoverAnimation'] ?? 'none', $hoverAnimations, 'none');
	$advanced = app(\App\Support\PageBuilderElementorV23\WidgetAdvancedStyleResolver::class)->resolve($settings, $nodeId, request());
	$rootClasses = array_values(array_unique(array_merge(['el-widget-image','pb-image'], $advanced['classes'], $hoverAnimation === 'none' ? [] : ['pb-image--hover-' . $hoverAnimation])));
	$rootStyle = implode(';', [
		'text-align:' . $alignment(), '--pb-image-hover-filter:' . $filterCss($settings['imageHoverFilter'] ?? []),
		'--pb-image-hover-opacity:' . $opacity($settings['imageHoverOpacity'] ?? 1), '--pb-image-hover-duration:' . $duration($settings['imageHoverTransition'] ?? 0.3) . 's',
	]);

	$mediaRules = [];
	foreach (['Tablet' => 1024, 'Mobile' => 767] as $suffix => $breakpoint) {
		$fit = $objectFit($suffix);
		$imageRules = [
			'width:' . $cssLength($responsive('width', $suffix, '100%'), '100%'), 'max-width:' . $cssLength($responsive('maxWidth', $suffix, '100%'), '100%'),
			'height:' . $cssLength($responsive('height', $suffix, 'auto'), 'auto'), 'object-fit:' . ($fit === 'default' ? 'fill' : $fit), 'object-position:' . $objectPosition($suffix),
			'border-width:' . ($borderStyle === 'none' ? '0' : $cssLength($responsive('imageBorderWidth', $suffix, '1px'), '1px')),
			'border-radius:' . $cssLength($responsive('imageBorderRadius', $suffix, '0px'), '0px'),
		];
		$mediaRules[] = '@media (max-width: ' . $breakpoint . 'px){#' . $advanced['id'] . '{text-align:' . $alignment($suffix) . '}#' . $advanced['id'] . ' .pb-image__img{' . implode(';', $imageRules) . '}#' . $advanced['id'] . ' .pb-image__caption{' . $captionStyle($suffix) . '}}';
	}
	$interactionCss = '#'.$advanced['id'].'{width:100%;min-width:0}#'.$advanced['id'].' .pb-image__figure{display:inline-flex;max-width:100%;margin:0;flex-direction:column}#'.$advanced['id'].' .pb-image__link{display:inline-block;max-width:100%;color:inherit}#'.$advanced['id'].' .pb-image__caption{max-width:100%;padding:0}#'.$advanced['id'].' .pb-image__empty{width:min(100%,640px);aspect-ratio:16/9;display:grid;place-items:center;background:#eef1f4;color:#98a2b3;font-size:44px}#'.$advanced['id'].' .pb-image__img{transition:filter var(--pb-image-hover-duration,.3s) ease,opacity var(--pb-image-hover-duration,.3s) ease,transform var(--pb-image-hover-duration,.3s) ease}#'.$advanced['id'].':hover .pb-image__img{filter:var(--pb-image-hover-filter);opacity:var(--pb-image-hover-opacity)}'
		.'#'.$advanced['id'].'.pb-image--hover-grow:hover .pb-image__img,#'.$advanced['id'].'.pb-image--hover-pulse-grow:hover .pb-image__img{transform:scale(1.08)}#'.$advanced['id'].'.pb-image--hover-shrink:hover .pb-image__img,#'.$advanced['id'].'.pb-image--hover-pulse-shrink:hover .pb-image__img{transform:scale(.94)}#'.$advanced['id'].'.pb-image--hover-rotate:hover .pb-image__img{transform:rotate(6deg)}#'.$advanced['id'].'.pb-image--hover-grow-rotate:hover .pb-image__img{transform:scale(1.06) rotate(6deg)}#'.$advanced['id'].'.pb-image--hover-float:hover .pb-image__img,#'.$advanced['id'].'.pb-image--hover-bob:hover .pb-image__img{transform:translateY(-7px)}#'.$advanced['id'].'.pb-image--hover-sink:hover .pb-image__img,#'.$advanced['id'].'.pb-image--hover-hang:hover .pb-image__img{transform:translateY(7px)}#'.$advanced['id'].'.pb-image--hover-skew:hover .pb-image__img,#'.$advanced['id'].'.pb-image--hover-skew-forward:hover .pb-image__img{transform:skewX(-8deg)}#'.$advanced['id'].'.pb-image--hover-skew-backward:hover .pb-image__img{transform:skewX(8deg)}#'.$advanced['id'].'.pb-image--hover-pulse:hover .pb-image__img,#'.$advanced['id'].'.pb-image--hover-push:hover .pb-image__img,#'.$advanced['id'].'.pb-image--hover-pop:hover .pb-image__img,#'.$advanced['id'].'[class*="pb-image--hover-bounce"]:hover .pb-image__img,#'.$advanced['id'].'[class*="pb-image--hover-wobble"]:hover .pb-image__img,#'.$advanced['id'].'[class*="pb-image--hover-buzz"]:hover .pb-image__img{animation:pb-image-pulse .48s ease both}@keyframes pb-image-pulse{35%{transform:scale(1.08)}70%{transform:scale(.98)}}@media(prefers-reduced-motion:reduce){#'.$advanced['id'].' .pb-image__img{animation:none!important;transition:none!important}}';
@endphp

<div id="{{ $advanced['id'] }}" class="{{ implode(' ', $rootClasses) }}" style="{{ $rootStyle }}" data-basic-image data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName => $attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach>
	<figure class="pb-image__figure">
		@if($linkUrl !== '')
			<a class="pb-image__link" href="{{ $linkUrl }}" @if($linkTarget !== '') target="{{ $linkTarget }}" @endif @if($linkRel !== '') rel="{{ $linkRel }}" @endif @if($usesLightbox) data-basic-image-lightbox="true" @endif @foreach($linkAttributes as $attributeName => $attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach>
				@if($imageUrl !== '')<img class="pb-image__img" src="{{ $imageUrl }}" alt="{{ $imageAlt }}" style="{{ $imageStyle }}">@else<div class="pb-image__empty" role="img" aria-label="Choose an image"><i class="far fa-image" aria-hidden="true"></i></div>@endif
			</a>
		@elseif($imageUrl !== '')
			<img class="pb-image__img" src="{{ $imageUrl }}" alt="{{ $imageAlt }}" style="{{ $imageStyle }}">
		@else
			<div class="pb-image__empty" role="img" aria-label="Choose an image"><i class="far fa-image" aria-hidden="true"></i></div>
		@endif
		@if($caption !== '')<figcaption class="pb-image__caption" style="{{ $captionStyle() }}">{{ $caption }}</figcaption>@endif
	</figure>
</div>
<style>{!! $advanced['css'] . implode('', $mediaRules) . $interactionCss !!}</style>
