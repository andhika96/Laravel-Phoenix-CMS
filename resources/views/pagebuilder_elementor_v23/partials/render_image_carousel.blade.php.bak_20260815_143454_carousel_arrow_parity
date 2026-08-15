@php
	$settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeId = trim((string) ($node['id'] ?? 'image-carousel')) ?: 'image-carousel';
	$safeImageUrl = function (mixed $value): string {
		$url = trim((string) $value);
		if ($url === '' || str_starts_with($url, '//')) return '';
		return preg_match('/^(?:https?:|\/)/i', $url) ? $url : '';
	};
	$safeLinkUrl = function (mixed $value): string {
		$url = trim((string) $value);
		if ($url === '' || str_starts_with($url, '//')) return '';
		return preg_match('/^(?:https?:|mailto:|tel:|\/|#)/i', $url) ? $url : '';
	};
	$cssLength = function (mixed $value, string $fallback = '0px'): string {
		$raw = trim((string) $value);
		return preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i', $raw) ? $raw : $fallback;
	};
	$cssColor = function (mixed $value, string $fallback): string {
		$raw = trim((string) $value);
		return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : $fallback;
	};
	$enum = function (mixed $value, array $allowed, string $fallback): string {
		$raw = strtolower(trim((string) $value));
		return in_array($raw, $allowed, true) ? $raw : $fallback;
	};
	$truthy = fn (mixed $value): bool => in_array($value, [true, 1, '1', 'true', 'yes', 'on'], true);
	$sanitizeSvg = function (mixed $value): string {
		$markup = trim((string) $value);
		if ($markup === '' || !preg_match('/\A<svg\b[\s\S]*<\/svg>\z/i', $markup)) return '';
		$markup = strip_tags($markup, '<svg><g><path><circle><ellipse><rect><line><polyline><polygon><title><desc>');
		$markup = preg_replace("/\\s(?:on[a-z]+|style|(?:xlink:)?href|src)\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^\\s>]+)/i", '', $markup) ?? '';
		return $markup;
	};
	$arrowIcon = function (string $key, string $fallback) use ($settings, $sanitizeSvg): array {
		$source = ($settings[$key.'Source'] ?? '') === 'svg' ? 'svg' : 'library';
		$svg = $source === 'svg' ? $sanitizeSvg($settings[$key.'Svg'] ?? '') : '';
		return ['source' => $source, 'class' => trim((string) ($settings[$key] ?? $fallback)) ?: $fallback, 'svg' => $svg];
	};
	$previousArrow = $arrowIcon('previousArrowIcon', 'fas fa-chevron-left');
	$nextArrow = $arrowIcon('nextArrowIcon', 'fas fa-chevron-right');
	$responsive = function (string $base, string $suffix = '', mixed $fallback = '') use ($settings): mixed {
		$keys = $suffix === 'Mobile' ? [$base.'Mobile', $base.'Tablet', $base] : ($suffix === 'Tablet' ? [$base.'Tablet', $base] : [$base]);
		foreach ($keys as $key) {
			$value = $settings[$key] ?? null;
			if ($value !== '' && $value !== null) return $value;
		}
		return $fallback;
	};
	$slideCount = function (string $suffix = '') use ($responsive): int {
		$value = $responsive('slidesToShow', $suffix, 'default');
		return is_numeric($value) ? max(1, min(10, (int) $value)) : 3;
	};
	$scrollCount = function (string $suffix = '') use ($responsive): int {
		$value = $responsive('slidesToScroll', $suffix, 'default');
		return is_numeric($value) ? max(1, min(10, (int) $value)) : 1;
	};

	$imageResolution = $enum($settings['imageResolution'] ?? 'thumbnail', ['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048', 'full', 'custom'], 'thumbnail');
	$customWidth = max(1, min(4096, (int) ($settings['customImageWidth'] ?? 150)));
	$customHeight = max(1, min(4096, (int) ($settings['customImageHeight'] ?? 150)));
	$resolver = app(\App\Support\PageBuilderElementorV23\ImageRenditionResolver::class);
	$images = [];
	foreach (is_array($settings['images'] ?? null) ? $settings['images'] : [] as $index => $image) {
		if (!is_array($image)) continue;
		$url = $safeImageUrl($image['url'] ?? '');
		if ($url === '') continue;
		$url = $resolver->resolve($url, $imageResolution, $imageResolution === 'custom' ? $customWidth : null, $imageResolution === 'custom' ? $customHeight : null);
		$images[] = [
			'id' => preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($image['id'] ?? 'image-'.$index)) ?: 'image-'.$index,
			'url' => $url,
			'alt' => (string) ($image['alt'] ?? ''),
			'title' => (string) ($image['title'] ?? ''),
			'caption' => (string) ($image['caption'] ?? ''),
			'description' => (string) ($image['description'] ?? ''),
		];
	}

	$navigation = $enum($settings['navigation'] ?? 'arrows_dots', ['arrows_dots', 'arrows', 'dots', 'none'], 'arrows_dots');
	$imageCount = count($images);
	$visibleCount = function (string $suffix = '') use ($imageCount, $slideCount): int {
		return max(1, min(max(1, $imageCount), $slideCount($suffix)));
	};
	$effectiveScrollCount = function (string $suffix = '') use ($scrollCount, $visibleCount): int {
		return max(1, min($visibleCount($suffix), $scrollCount($suffix)));
	};
	$pageCount = function (string $suffix = '') use ($imageCount, $visibleCount, $effectiveScrollCount): int {
		return max(1, (int) ceil(max(0, $imageCount - $visibleCount($suffix)) / $effectiveScrollCount($suffix)) + 1);
	};
	$hasAnyOverflow = collect(['', 'Tablet', 'Mobile'])->contains(fn (string $suffix): bool => $imageCount > $visibleCount($suffix));
	$showArrows = in_array($navigation, ['arrows_dots', 'arrows'], true) && $hasAnyOverflow;
	$showDots = in_array($navigation, ['arrows_dots', 'dots'], true) && $hasAnyOverflow;
	$linkType = $enum($settings['linkType'] ?? 'none', ['none', 'media', 'custom'], 'none');
	$customLinkUrl = $safeLinkUrl($settings['customLinkUrl'] ?? '');
	$linkTarget = $linkType === 'custom' && ($settings['linkTarget'] ?? '') === '_blank' ? '_blank' : '';
	$relTokens = $linkTarget === '_blank' ? ['noopener', 'noreferrer'] : [];
	if ($linkType === 'custom' && $truthy($settings['linkNofollow'] ?? false)) $relTokens[] = 'nofollow';
	$linkRel = implode(' ', array_values(array_unique($relTokens)));
	$linkAttributes = [];
	foreach (($settings['linkCustomAttributes'] ?? []) as $attribute) {
		if (!is_array($attribute)) continue;
		$name = strtolower(trim((string) ($attribute['key'] ?? $attribute['name'] ?? '')));
		if (!preg_match('/^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/', $name)) continue;
		$linkAttributes[$name] = (string) ($attribute['value'] ?? '');
	}
	$captionType = $enum($settings['captionType'] ?? 'none', ['none', 'title', 'caption', 'description'], 'none');
	$borderType = $enum($settings['imageBorderType'] ?? 'default', ['default', 'none', 'solid', 'double', 'dotted', 'dashed', 'groove'], 'default');
	$actualBorderType = in_array($borderType, ['default', 'none'], true) ? 'none' : $borderType;
	$sideValues = function (string $base, string $suffix = '') use ($responsive, $cssLength): string {
		return collect(['Top', 'Right', 'Bottom', 'Left'])
			->map(fn (string $side): string => $cssLength($responsive($base.$side, $suffix, '0px'), '0px'))
			->implode(' ');
	};
	$verticalAlign = function (string $suffix = '') use ($responsive, $enum): string {
		$value = $enum($responsive('imageVerticalAlign', $suffix, 'center'), ['start', 'center', 'end'], 'center');
		return ['start' => 'flex-start', 'end' => 'flex-end'][$value] ?? 'center';
	};
	$imageGap = function (string $suffix = '') use ($settings, $responsive, $cssLength): string {
		return ($settings['imageSpacingMode'] ?? 'default') === 'custom'
			? $cssLength($responsive('imageSpacing', $suffix, '20px'), '20px')
			: '20px';
	};
	$imageStyle = implode(';', [
		'width:'.($truthy($settings['imageStretch'] ?? false) ? '100%' : 'auto'),
		'height:auto',
		'object-fit:contain',
		'border-style:'.$actualBorderType,
		'border-color:'.$cssColor($settings['imageBorderColor'] ?? '', 'transparent'),
	]);
	$captionStyle = implode(';', [
		'color:'.$cssColor($settings['captionColor'] ?? '', 'inherit'),
		'font-family:'.(preg_match('/^[A-Za-z0-9 _,\'"-]+$/', (string) ($settings['captionFontFamily'] ?? 'inherit')) ? (string) ($settings['captionFontFamily'] ?? 'inherit') : 'inherit'),
		'font-weight:'.preg_replace('/[^A-Za-z0-9-]/', '', (string) ($settings['captionFontWeight'] ?? '400')),
		'text-transform:'.$enum($settings['captionTextTransform'] ?? 'none', ['none', 'uppercase', 'lowercase', 'capitalize'], 'none'),
		'font-style:'.$enum($settings['captionFontStyle'] ?? 'normal', ['normal', 'italic', 'oblique'], 'normal'),
		'text-decoration:'.$enum($settings['captionTextDecoration'] ?? 'none', ['none', 'underline', 'overline', 'line-through'], 'none'),
		'text-shadow:'.(preg_match('/^[#a-z0-9(),.%\s-]+$/i', (string) ($settings['captionTextShadow'] ?? 'none')) ? (string) ($settings['captionTextShadow'] ?? 'none') : 'none'),
	]);
	$advanced = app(\App\Support\PageBuilderElementorV23\WidgetAdvancedStyleResolver::class)->resolve($settings, $nodeId, request());
	$rootClasses = array_values(array_unique(array_merge(['el-widget-image-carousel', 'pb-image-carousel', 'is-arrows-'.$enum($settings['arrowPosition'] ?? 'inside', ['inside', 'outside'], 'inside'), 'is-pagination-'.$enum($settings['paginationPosition'] ?? 'outside', ['inside', 'outside'], 'outside')], $advanced['classes'])));
	$rootStyle = implode(';', [
		'--pb-carousel-visible:'.$visibleCount(),
		'--pb-carousel-arrow-size:'.$cssLength($responsive('arrowSize', '', '16px'), '16px'),
		'--pb-carousel-arrow-color:'.$cssColor($settings['arrowColor'] ?? '', '#69727d'),
		'--pb-carousel-dot-size:'.$cssLength($responsive('dotSize', '', '8px'), '8px'),
		'--pb-carousel-dot-gap:'.$cssLength($responsive('dotSpacing', '', '8px'), '8px'),
		'--pb-carousel-dot-color:'.$cssColor($settings['dotColor'] ?? '', '#c4c7cf'),
		'--pb-carousel-dot-active:'.$cssColor($settings['dotActiveColor'] ?? '', '#69727d'),
		'--pb-carousel-image-gap:'.$imageGap(),
		'--pb-carousel-transition:'.max(0, min(10000, (int) ($settings['animationSpeed'] ?? 500))).'ms',
	]);
	$config = [
		'slidesToShow' => $visibleCount(), 'slidesToShowTablet' => $visibleCount('Tablet'), 'slidesToShowMobile' => $visibleCount('Mobile'),
		'slidesToScroll' => $effectiveScrollCount(), 'slidesToScrollTablet' => $effectiveScrollCount('Tablet'), 'slidesToScrollMobile' => $effectiveScrollCount('Mobile'),
		'autoplay' => $truthy($settings['autoplay'] ?? true), 'pauseOnHover' => $truthy($settings['pauseOnHover'] ?? true), 'pauseOnInteraction' => $truthy($settings['pauseOnInteraction'] ?? true),
		'autoplaySpeed' => max(100, min(60000, (int) ($settings['autoplaySpeed'] ?? 5000))), 'infiniteLoop' => $truthy($settings['infiniteLoop'] ?? true),
		'animationSpeed' => max(0, min(10000, (int) ($settings['animationSpeed'] ?? 500))), 'direction' => ($settings['direction'] ?? 'left') === 'right' ? 'right' : 'left',
	];
	$dotCount = max($pageCount(), $pageCount('Tablet'), $pageCount('Mobile'));
	$baseRules = '#'.$advanced['id'].' .pb-image-carousel__slide{align-self:'.$verticalAlign().'}';
	$baseRules .= '#'.$advanced['id'].' .pb-image-carousel__slide img{border-width:'.$sideValues('imageBorderWidth').';border-radius:'.$sideValues('imageBorderRadius').'}';
	$baseRules .= '#'.$advanced['id'].' .pb-image-carousel__caption{font-size:'.$cssLength($responsive('captionFontSize', '', '16px'), '16px').';text-align:'.$enum($responsive('captionAlignment', '', 'center'), ['left','center','right','justify'], 'center').';line-height:'.$cssLength($responsive('captionLineHeight', '', '1.5em'), '1.5em').';letter-spacing:'.$cssLength($responsive('captionLetterSpacing', '', '0px'), '0px').';word-spacing:'.$cssLength($responsive('captionWordSpacing', '', '0px'), '0px').';margin-top:'.$cssLength($responsive('captionSpacing', '', '8px'), '8px').'}';
	$mediaRules = '@media(max-width:1024px){#'.$advanced['id'].'{--pb-carousel-visible:'.$visibleCount('Tablet').';--pb-carousel-arrow-size:'.$cssLength($responsive('arrowSize', 'Tablet', '16px'), '16px').';--pb-carousel-dot-size:'.$cssLength($responsive('dotSize', 'Tablet', '8px'), '8px').';--pb-carousel-dot-gap:'.$cssLength($responsive('dotSpacing', 'Tablet', '8px'), '8px').';--pb-carousel-image-gap:'.$imageGap('Tablet').'}#'.$advanced['id'].' .pb-image-carousel__slide{align-self:'.$verticalAlign('Tablet').'}#'.$advanced['id'].' .pb-image-carousel__slide img{border-width:'.$sideValues('imageBorderWidth', 'Tablet').';border-radius:'.$sideValues('imageBorderRadius', 'Tablet').'}#'.$advanced['id'].' .pb-image-carousel__caption{font-size:'.$cssLength($responsive('captionFontSize', 'Tablet', '16px'), '16px').';text-align:'.$enum($responsive('captionAlignment', 'Tablet', 'center'), ['left','center','right','justify'], 'center').';line-height:'.$cssLength($responsive('captionLineHeight', 'Tablet', '1.5em'), '1.5em').';letter-spacing:'.$cssLength($responsive('captionLetterSpacing', 'Tablet', '0px'), '0px').';word-spacing:'.$cssLength($responsive('captionWordSpacing', 'Tablet', '0px'), '0px').';margin-top:'.$cssLength($responsive('captionSpacing', 'Tablet', '8px'), '8px').'}}';
	$mediaRules .= '@media(max-width:767px){#'.$advanced['id'].'{--pb-carousel-visible:'.$visibleCount('Mobile').';--pb-carousel-arrow-size:'.$cssLength($responsive('arrowSize', 'Mobile', '16px'), '16px').';--pb-carousel-dot-size:'.$cssLength($responsive('dotSize', 'Mobile', '8px'), '8px').';--pb-carousel-dot-gap:'.$cssLength($responsive('dotSpacing', 'Mobile', '8px'), '8px').';--pb-carousel-image-gap:'.$imageGap('Mobile').'}#'.$advanced['id'].' .pb-image-carousel__slide{align-self:'.$verticalAlign('Mobile').'}#'.$advanced['id'].' .pb-image-carousel__slide img{border-width:'.$sideValues('imageBorderWidth', 'Mobile').';border-radius:'.$sideValues('imageBorderRadius', 'Mobile').'}#'.$advanced['id'].' .pb-image-carousel__caption{font-size:'.$cssLength($responsive('captionFontSize', 'Mobile', '16px'), '16px').';text-align:'.$enum($responsive('captionAlignment', 'Mobile', 'center'), ['left','center','right','justify'], 'center').';line-height:'.$cssLength($responsive('captionLineHeight', 'Mobile', '1.5em'), '1.5em').';letter-spacing:'.$cssLength($responsive('captionLetterSpacing', 'Mobile', '0px'), '0px').';word-spacing:'.$cssLength($responsive('captionWordSpacing', 'Mobile', '0px'), '0px').';margin-top:'.$cssLength($responsive('captionSpacing', 'Mobile', '8px'), '8px').'}}';
@endphp

<div id="{{ $advanced['id'] }}" class="{{ implode(' ', $rootClasses) }}" style="{{ $rootStyle }}" role="region" aria-label="{{ trim((string) ($settings['carouselName'] ?? '')) ?: 'Image Carousel' }}" data-image-carousel data-carousel-config="{{ json_encode($config, JSON_UNESCAPED_SLASHES) }}" data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName => $attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach>
	@if(empty($images))
		<div class="pb-image-carousel__empty"><i class="far fa-images" aria-hidden="true"></i><span>No images selected</span></div>
	@else
		<div class="pb-image-carousel__viewport">
			<div class="pb-image-carousel__track">
				@foreach($images as $image)
					@php
						$linkUrl = $linkType === 'media' ? $image['url'] : ($linkType === 'custom' ? $customLinkUrl : '');
						$caption = $captionType !== 'none' ? (string) ($image[$captionType] ?? '') : '';
					@endphp
					<figure class="pb-image-carousel__slide">
						@if($linkUrl !== '')<a href="{{ $linkUrl }}" @if($linkType === 'custom' && $linkTarget !== '') target="{{ $linkTarget }}" @endif @if($linkType === 'custom' && $linkRel !== '') rel="{{ $linkRel }}" @endif @if($linkType === 'custom') @foreach($linkAttributes as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach @endif @if($linkType === 'media' && ($settings['lightbox'] ?? 'default') !== 'no') data-carousel-lightbox @endif>@endif
						<img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" style="{{ $imageStyle }}" @if($truthy($settings['lazyload'] ?? false)) loading="lazy" @endif>
						@if($linkUrl !== '')</a>@endif
						@if($caption !== '')<figcaption class="pb-image-carousel__caption" style="{{ $captionStyle }}">{{ $caption }}</figcaption>@endif
					</figure>
				@endforeach
			</div>
		</div>
		@if($showArrows)<button type="button" class="pb-image-carousel__arrow pb-image-carousel__arrow--previous" aria-label="Previous slide">@if($previousArrow['source'] === 'svg' && $previousArrow['svg'] !== '')<span class="pb-image-carousel__arrow-svg" aria-hidden="true">{!! $previousArrow['svg'] !!}</span>@else<i class="{{ e($previousArrow['class']) }}" aria-hidden="true"></i>@endif</button><button type="button" class="pb-image-carousel__arrow pb-image-carousel__arrow--next" aria-label="Next slide">@if($nextArrow['source'] === 'svg' && $nextArrow['svg'] !== '')<span class="pb-image-carousel__arrow-svg" aria-hidden="true">{!! $nextArrow['svg'] !!}</span>@else<i class="{{ e($nextArrow['class']) }}" aria-hidden="true"></i>@endif</button>@endif
		@if($showDots)<div class="pb-image-carousel__pagination" role="tablist" aria-label="Carousel pagination">@for($dot=0;$dot<$dotCount;$dot++)<button type="button" class="pb-image-carousel__dot{{ $dot === 0 ? ' is-active' : '' }}" data-carousel-index="{{ $dot }}" aria-label="Go to slide {{ $dot + 1 }}" aria-selected="{{ $dot === 0 ? 'true' : 'false' }}"></button>@endfor</div>@endif
	@endif
</div>
<style>{!! $advanced['css'].$baseRules.$mediaRules !!}</style>
