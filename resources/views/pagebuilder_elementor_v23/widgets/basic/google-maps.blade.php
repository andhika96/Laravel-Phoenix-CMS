@php
	$settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeId = trim((string) ($node['id'] ?? 'google-maps')) ?: 'google-maps';
	$location = trim((string) ($settings['location'] ?? ''));
	$enumNumber = function (mixed $value, float $min, float $max, float $fallback): float {
		if ($value === '' || $value === null || !is_numeric($value)) return $fallback;
		return max($min, min($max, (float) $value));
	};
	$zoom = (int) round($enumNumber($settings['zoom'] ?? 14, 1, 20, 14));
	$cssLength = function (mixed $value, string $fallback): string {
		$raw = trim((string) ($value ?? ''));
		if (preg_match('/^\d+(?:\.\d+)?$/', $raw)) return $raw . 'px';
		return preg_match('/^(?:\d+(?:\.\d+)?)(?:px|%|em|rem|vh|vw)$/i', $raw) ? $raw : $fallback;
	};
	$cssFilter = function (mixed $filters): string {
		$filters = is_array($filters) ? $filters : [];
		$number = function (string $key, float $min, float $max, float $fallback) use ($filters): string {
			$value = $filters[$key] ?? null;
			if ($value === '' || $value === null || !is_numeric($value)) $value = $fallback;
			return (string) max($min, min($max, (float) $value));
		};
		return 'blur(' . $number('blur', 0, 100, 0) . 'px) brightness(' . $number('brightness', 0, 200, 100) . '%) contrast(' . $number('contrast', 0, 200, 100) . '%) saturate(' . $number('saturation', 0, 200, 100) . '%) hue-rotate(' . $number('hue', 0, 360, 0) . 'deg)';
	};
	$responsive = function (string $base, string $suffix = '', mixed $fallback = '') use ($settings): mixed {
		$keys = $suffix === 'Mobile' ? [$base . 'Mobile', $base . 'Tablet', $base] : ($suffix === 'Tablet' ? [$base . 'Tablet', $base] : [$base]);
		foreach ($keys as $key) {
			$value = $settings[$key] ?? null;
			if ($value !== '' && $value !== null) return $value;
		}
		return $fallback;
	};
	$duration = $enumNumber($settings['transitionDuration'] ?? 0.3, 0, 10, 0.3);
	$mapUrl = $location === '' ? '' : 'https://www.google.com/maps?q=' . rawurlencode($location) . '&z=' . $zoom . '&output=embed';
	$advanced = app(\App\Support\PageBuilderElementorV23\WidgetAdvancedStyleResolver::class)->resolve($settings, $nodeId, request());
	$rootClasses = array_values(array_unique(array_merge(['el-widget-google-maps', 'pb-google-maps'], $advanced['classes'])));
	$rootStyle = implode(';', [
		'--pb-google-maps-hover-filter:' . $cssFilter($settings['mapHoverFilter'] ?? []),
		'--pb-google-maps-transition-duration:' . $duration . 's',
	]);
	$frameStyle = implode(';', [
		'height:' . $cssLength($responsive('height', '', '400px'), '400px'),
		'filter:' . $cssFilter($settings['mapNormalFilter'] ?? []),
	]);
	$mediaRules = [];
	foreach (['Tablet' => 1024, 'Mobile' => 767] as $suffix => $breakpoint) {
		$mediaRules[] = '@media (max-width:' . $breakpoint . 'px){#' . $advanced['id'] . ' .pb-google-maps__frame{height:' . $cssLength($responsive('height', $suffix, '400px'), '400px') . '}}';
	}
	$interactionCss = '#'.$advanced['id'].'{width:100%;min-width:0}#'.$advanced['id'].' .pb-google-maps__frame{overflow:hidden;transition:filter var(--pb-google-maps-transition-duration,.3s) ease}#'.$advanced['id'].' .pb-google-maps__frame:hover{filter:var(--pb-google-maps-hover-filter)}#'.$advanced['id'].' .pb-google-maps__frame iframe{display:block;width:100%;height:100%;border:0}#'.$advanced['id'].' .pb-google-maps__empty{min-height:220px;display:grid;place-items:center;align-content:center;gap:8px;padding:24px;background:#eef1f4;color:#98a2b3;text-align:center}#'.$advanced['id'].' .pb-google-maps__empty i{font-size:36px}#'.$advanced['id'].' .pb-google-maps__empty span{font-size:12px}@media(prefers-reduced-motion:reduce){#'.$advanced['id'].' .pb-google-maps__frame{transition:none}}';
@endphp

<div id="{{ $advanced['id'] }}" class="{{ implode(' ', $rootClasses) }}" style="{{ $rootStyle }}" data-basic-google-maps data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName => $attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach>
	@if($mapUrl !== '')
		<div class="pb-google-maps__frame" style="{{ $frameStyle }}"><iframe src="{{ $mapUrl }}" title="Google Maps" loading="lazy" allowfullscreen></iframe></div>
	@else
		<div class="pb-google-maps__empty" data-google-maps-empty role="img" aria-label="Choose a map location"><i class="fas fa-map-marker-alt" aria-hidden="true"></i><span>Enter a location to display the map.</span></div>
	@endif
</div>
<style>{!! $advanced['css'] . implode('', $mediaRules) . $interactionCss !!}</style>
