@php
	$settings = $node['settings'] ?? [];
	$nodeId = trim((string) ($node['id'] ?? ''));
	$advanced = app(\App\Support\PageBuilderElementorV24\WidgetAdvancedStyleResolver::class)->resolve($settings, $nodeId !== '' ? $nodeId : 'spacer', request());
	$nodeDomId = $advanced['id'];
	$cssValue = fn ($value, $fallback = '') => ($value === null || $value === '') ? $fallback : (is_numeric($value) ? ((float) $value === 0.0 ? '0' : $value . 'px') : trim((string) $value));
	$customClass = implode(' ', array_filter(array_map(fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')), preg_split('/\s+/', trim((string) ($settings['cssClass'] ?? ''))) ?: [])));
	$className = implode(' ', array_values(array_unique(array_merge(['el-widget-spacer'], $advanced['classes']))));
	$tabletRules = ($settings['heightTablet'] ?? '') !== '' ? ['height:' . $cssValue($settings['heightTablet'], '32px')] : [];
	$mobileRules = ($settings['heightMobile'] ?? '') !== '' ? ['height:' . $cssValue($settings['heightMobile'], '32px')] : [];
	$styleBlocks = [];
	if ($nodeDomId !== '' && $tabletRules) $styleBlocks[] = '@media (max-width: 1024px){#' . $nodeDomId . '{' . implode(';', $tabletRules) . '}}';
	if ($nodeDomId !== '' && $mobileRules) $styleBlocks[] = '@media (max-width: 767px){#' . $nodeDomId . '{' . implode(';', $mobileRules) . '}}';
@endphp
<div id="{{ $nodeDomId }}" class="{{ $className }}" data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach style="height:{{ $settings['height'] ?? '32px' }}"></div>
<style>{!! $advanced['css'] !!}{!! implode("\n", $styleBlocks) !!}</style>
