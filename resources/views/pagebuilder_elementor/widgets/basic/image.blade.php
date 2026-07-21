@php
	$settings = $node['settings'] ?? [];
	$nodeId = trim((string) ($node['id'] ?? ''));
	$nodeDomId = $nodeId !== '' ? 'pb-node-' . $nodeId : '';
	$cleanClasses = fn ($value) => implode(' ', array_filter(array_map(
		fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')),
		preg_split('/\s+/', trim((string) $value)) ?: []
	)));
	$cssValue = function ($value, $fallback = '') {
		if ($value === null || $value === '') return $fallback;
		return is_numeric($value) ? ((float) $value === 0.0 ? '0' : $value . 'px') : trim((string) $value);
	};
	$customClass = $cleanClasses($settings['cssClass'] ?? '');
	$imgStyle = implode(';', array_filter(['width:' . ($settings['width'] ?? '100%'), 'height:' . ($settings['height'] ?? 'auto')]));
	$className = trim(implode(' ', array_filter(['el-widget-image', $customClass])));
	$tabletRules = [];
	$mobileRules = [];
	if (($settings['widthTablet'] ?? '') !== '') $tabletRules[] = 'width:' . $cssValue($settings['widthTablet'], '100%');
	if (($settings['widthMobile'] ?? '') !== '') $mobileRules[] = 'width:' . $cssValue($settings['widthMobile'], '100%');
	if (($settings['heightTablet'] ?? '') !== '') $tabletRules[] = 'height:' . $cssValue($settings['heightTablet'], 'auto');
	if (($settings['heightMobile'] ?? '') !== '') $mobileRules[] = 'height:' . $cssValue($settings['heightMobile'], 'auto');
	$styleBlocks = [];
	if ($nodeDomId !== '' && $tabletRules) $styleBlocks[] = '@media (max-width: 1024px){#' . $nodeDomId . ' > img{' . implode(';', $tabletRules) . '}}';
	if ($nodeDomId !== '' && $mobileRules) $styleBlocks[] = '@media (max-width: 767px){#' . $nodeDomId . ' > img{' . implode(';', $mobileRules) . '}}';
@endphp
<div @if($nodeDomId !== '') id="{{ $nodeDomId }}" @endif class="{{ $className }}">
	<img src="{{ $settings['src'] ?? '' }}" alt="{{ $settings['alt'] ?? '' }}" @if($customClass !== '') class="{{ $customClass }}" @endif style="{{ $imgStyle }}">
</div>
@if($styleBlocks)<style>{!! implode("\n", $styleBlocks) !!}</style>@endif
