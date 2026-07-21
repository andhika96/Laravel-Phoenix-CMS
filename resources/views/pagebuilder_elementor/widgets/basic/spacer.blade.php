@php
	$settings = $node['settings'] ?? [];
	$nodeId = trim((string) ($node['id'] ?? ''));
	$nodeDomId = $nodeId !== '' ? 'pb-node-' . $nodeId : '';
	$cssValue = fn ($value, $fallback = '') => ($value === null || $value === '') ? $fallback : (is_numeric($value) ? ((float) $value === 0.0 ? '0' : $value . 'px') : trim((string) $value));
	$customClass = implode(' ', array_filter(array_map(fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')), preg_split('/\s+/', trim((string) ($settings['cssClass'] ?? ''))) ?: [])));
	$className = trim(implode(' ', array_filter(['el-widget-spacer', $customClass])));
	$tabletRules = ($settings['heightTablet'] ?? '') !== '' ? ['height:' . $cssValue($settings['heightTablet'], '32px')] : [];
	$mobileRules = ($settings['heightMobile'] ?? '') !== '' ? ['height:' . $cssValue($settings['heightMobile'], '32px')] : [];
	$styleBlocks = [];
	if ($nodeDomId !== '' && $tabletRules) $styleBlocks[] = '@media (max-width: 1024px){#' . $nodeDomId . '{' . implode(';', $tabletRules) . '}}';
	if ($nodeDomId !== '' && $mobileRules) $styleBlocks[] = '@media (max-width: 767px){#' . $nodeDomId . '{' . implode(';', $mobileRules) . '}}';
@endphp
<div @if($nodeDomId !== '') id="{{ $nodeDomId }}" @endif class="{{ $className }}" style="height:{{ $settings['height'] ?? '32px' }}"></div>
@if($styleBlocks)<style>{!! implode("\n", $styleBlocks) !!}</style>@endif
