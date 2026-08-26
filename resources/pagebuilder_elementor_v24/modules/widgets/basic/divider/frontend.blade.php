@php
	$settings = $node['settings'] ?? [];
	$nodeId = trim((string) ($node['id'] ?? ''));
	$advanced = app(\App\Support\PageBuilderElementorV24\WidgetAdvancedStyleResolver::class)->resolve($settings, $nodeId !== '' ? $nodeId : 'divider', request());
	$nodeDomId = $advanced['id'];
	$cssValue = fn ($value, $fallback = '') => ($value === null || $value === '') ? $fallback : (is_numeric($value) ? ((float) $value === 0.0 ? '0' : $value . 'px') : trim((string) $value));
	$customClass = implode(' ', array_filter(array_map(fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')), preg_split('/\s+/', trim((string) ($settings['cssClass'] ?? ''))) ?: [])));
	$thicknessUnit = in_array(($settings['thicknessUnit'] ?? ''), ['px', 'em', 'rem'], true) ? $settings['thicknessUnit'] : 'px';
	$thicknessCss = function ($value) use ($thicknessUnit) {
		$raw = trim((string) ($value ?? 2));
		preg_match('/^-?\d+(?:\.\d+)?/', $raw, $valueMatches);
		preg_match('/([a-z%]+)$/i', $raw, $unitMatches);
		$valueUnit = strtolower($unitMatches[1] ?? '');
		return ($valueMatches[0] ?? '2') . (in_array($valueUnit, ['px', 'em', 'rem'], true) ? $valueUnit : $thicknessUnit);
	};
	$lineRule = fn ($value) => 'border-top:' . $thicknessCss($value) . ' ' . ($settings['style'] ?? 'solid') . ' ' . ($settings['color'] ?? '#d0d7e6');
	$hrStyle = implode(';', [$lineRule($settings['thickness'] ?? '2px'), 'width:' . ($settings['width'] ?? '100%')]);
	$className = implode(' ', array_values(array_unique(array_merge(['el-widget-divider'], $advanced['classes']))));
	$tabletRules = [];
	if (($settings['widthTablet'] ?? '') !== '') $tabletRules[] = 'width:' . $cssValue($settings['widthTablet'], '100%');
	if (($settings['thicknessTablet'] ?? '') !== '') $tabletRules[] = $lineRule($settings['thicknessTablet']);
	$mobileRules = [];
	if (($settings['widthMobile'] ?? '') !== '') $mobileRules[] = 'width:' . $cssValue($settings['widthMobile'], '100%');
	if (($settings['thicknessMobile'] ?? '') !== '') $mobileRules[] = $lineRule($settings['thicknessMobile']);
	$styleBlocks = [];
	if ($nodeDomId !== '' && $tabletRules) $styleBlocks[] = '@media (max-width: 1024px){#' . $nodeDomId . ' > hr{' . implode(';', $tabletRules) . '}}';
	if ($nodeDomId !== '' && $mobileRules) $styleBlocks[] = '@media (max-width: 767px){#' . $nodeDomId . ' > hr{' . implode(';', $mobileRules) . '}}';
@endphp
<div id="{{ $nodeDomId }}" class="{{ $className }}" data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach><hr style="{{ $hrStyle }}"></div>
<style>{!! $advanced['css'] !!}{!! implode("\n", $styleBlocks) !!}</style>
