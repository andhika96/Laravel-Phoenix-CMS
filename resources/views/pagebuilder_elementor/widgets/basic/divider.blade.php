@php
	$settings = $node['settings'] ?? [];
	$nodeId = trim((string) ($node['id'] ?? ''));
	$nodeDomId = $nodeId !== '' ? 'pb-node-' . $nodeId : '';
	$cssValue = fn ($value, $fallback = '') => ($value === null || $value === '') ? $fallback : (is_numeric($value) ? ((float) $value === 0.0 ? '0' : $value . 'px') : trim((string) $value));
	$customClass = implode(' ', array_filter(array_map(fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')), preg_split('/\s+/', trim((string) ($settings['cssClass'] ?? ''))) ?: [])));
	$hrStyle = implode(';', ['border-top:' . ($settings['thickness'] ?? 2) . 'px ' . ($settings['style'] ?? 'solid') . ' ' . ($settings['color'] ?? '#d0d7e6'), 'width:' . ($settings['width'] ?? '100%')]);
	$className = trim(implode(' ', array_filter(['el-widget-divider', $customClass])));
	$tabletRules = ($settings['widthTablet'] ?? '') !== '' ? ['width:' . $cssValue($settings['widthTablet'], '100%')] : [];
	$mobileRules = ($settings['widthMobile'] ?? '') !== '' ? ['width:' . $cssValue($settings['widthMobile'], '100%')] : [];
	$styleBlocks = [];
	if ($nodeDomId !== '' && $tabletRules) $styleBlocks[] = '@media (max-width: 1024px){#' . $nodeDomId . ' > hr{' . implode(';', $tabletRules) . '}}';
	if ($nodeDomId !== '' && $mobileRules) $styleBlocks[] = '@media (max-width: 767px){#' . $nodeDomId . ' > hr{' . implode(';', $mobileRules) . '}}';
@endphp
<div @if($nodeDomId !== '') id="{{ $nodeDomId }}" @endif class="{{ $className }}"><hr style="{{ $hrStyle }}"></div>
@if($styleBlocks)<style>{!! implode("\n", $styleBlocks) !!}</style>@endif
