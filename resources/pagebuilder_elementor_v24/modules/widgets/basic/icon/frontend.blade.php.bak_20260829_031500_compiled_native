@php
	$settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeToken = preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) ($node['id'] ?? 'icon'))) ?: 'icon';
	$advanced = app(\App\Support\PageBuilderElementorV24\WidgetAdvancedStyleResolver::class)->resolve($settings, $nodeToken, request());
	$nodeDomId = $advanced['id'];
	$cleanClasses = fn ($value) => implode(' ', array_filter(array_map(fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')), preg_split('/\s+/', trim((string) $value)) ?: [])));
	$customClass = $cleanClasses($settings['cssClass'] ?? '');
	$iconClass = $cleanClasses($settings['iconClass'] ?? '') ?: 'far fa-star';
	$viewCandidate = strtolower(trim((string) ($settings['view'] ?? 'default')));
	$view = in_array($viewCandidate, ['default', 'stacked', 'framed'], true) ? $viewCandidate : 'default';
	$shapeCandidate = strtolower(trim((string) ($settings['shape'] ?? 'circle')));
	$shape = in_array($shapeCandidate, ['circle', 'rounded', 'square'], true) ? $shapeCandidate : 'circle';
	$link = trim((string) ($settings['link'] ?? ''));
	$openInNewWindow = filter_var($settings['openInNewWindow'] ?? false, FILTER_VALIDATE_BOOLEAN);
	$nofollow = filter_var($settings['nofollow'] ?? false, FILTER_VALIDATE_BOOLEAN);
	$relAttr = implode(' ', array_filter([$openInNewWindow ? 'noopener noreferrer' : '', $nofollow ? 'nofollow' : '']));
	$attrBag = [];
	foreach (($settings['attributes'] ?? []) as $attribute) {
		$name = trim((string) ($attribute['name'] ?? ''));
		if (preg_match('/^(data-[A-Za-z0-9_.:-]+|aria-[A-Za-z0-9_.:-]+|title)$/', $name)) $attrBag[$name] = (string) ($attribute['value'] ?? '');
	}
	$cssColor = fn ($value, string $fallback = 'inherit') => (($raw = trim((string) ($value ?? ''))) !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw)) ? $raw : $fallback;
	$cssLength = fn ($value, string $fallback = '0px') => (($raw = trim((string) ($value ?? ''))) !== '' && preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)$/i', $raw)) ? $raw : $fallback;
	$cssAngle = fn ($value, string $fallback = '0deg') => (($raw = trim((string) ($value ?? ''))) !== '' && preg_match('/^-?\d+(?:\.\d+)?(?:deg|grad|rad|turn)$/i', $raw)) ? $raw : $fallback;
	$duration = is_numeric($settings['iconTransitionDuration'] ?? null) ? max(0, min(10, (float) $settings['iconTransitionDuration'])) : .3;
	$responsive = function (string $base, string $suffix = '', mixed $fallback = '') use ($settings): mixed {
		$keys = $suffix === 'Mobile' ? [$base . 'Mobile', $base . 'Tablet', $base] : ($suffix === 'Tablet' ? [$base . 'Tablet', $base] : [$base]);
		foreach ($keys as $key) { if (array_key_exists($key, $settings) && $settings[$key] !== '' && $settings[$key] !== null) return $settings[$key]; }
		return $fallback;
	};
	$alignValue = function (string $suffix = '') use ($responsive): string {
		$value = $responsive('align', $suffix, 'left');
		return in_array($value, ['left', 'center', 'right'], true) ? $value : 'left';
	};
	$justify = fn (string $align) => $align === 'center' ? 'center' : ($align === 'right' ? 'flex-end' : 'flex-start');
	$rootStyle = function (string $suffix = '') use ($settings, $alignValue, $justify, $cssColor, $duration): string {
		$primary = $cssColor($settings['primaryColor'] ?? '#6f7f94', '#6f7f94');
		$primaryHover = $cssColor($settings['primaryColorHover'] ?? $primary, $primary);
		$secondary = $cssColor($settings['secondaryColor'] ?? '#7b8796', '#7b8796');
		$secondaryHover = $cssColor($settings['secondaryColorHover'] ?? $secondary, $secondary);
		return implode(';', ['display:flex', 'justify-content:' . $justify($alignValue($suffix)), '--pb-icon-primary:' . $primary, '--pb-icon-primary-hover:' . $primaryHover, '--pb-icon-secondary:' . $secondary, '--pb-icon-secondary-hover:' . $secondaryHover, '--pb-icon-transition-duration:' . $duration . 's']);
	};
	$glyphStyle = function (string $suffix = '') use ($settings, $responsive, $cssLength, $cssAngle): string {
		return 'font-size:' . $cssLength($responsive('iconSize', $suffix, '52px'), '52px') . ';transform:rotate(' . $cssAngle($responsive('iconRotate', $suffix, '0deg'), '0deg') . ')';
	};
	$className = implode(' ', array_values(array_unique(array_merge(['el-widget-icon', 'is-view-' . $view, $view !== 'default' ? 'is-shape-' . $shape : ''], $advanced['classes']))));
	$styleBlocks = [
		'#' . $nodeDomId . ' .el-widget-icon-link{color:var(--pb-icon-primary)!important;transition:color ' . $duration . 's ease}',
		'#' . $nodeDomId . ' .el-widget-icon-link:hover{color:var(--pb-icon-primary-hover)!important}',
		'#' . $nodeDomId . '.is-view-stacked .el-widget-icon-box{background:var(--pb-icon-secondary)!important;color:var(--pb-icon-primary)!important;transition:background-color ' . $duration . 's ease,color ' . $duration . 's ease}',
		'#' . $nodeDomId . '.is-view-stacked .el-widget-icon-link:hover .el-widget-icon-box{background:var(--pb-icon-secondary-hover)!important}',
		'#' . $nodeDomId . '.is-view-framed .el-widget-icon-box{border-color:var(--pb-icon-secondary)!important;color:var(--pb-icon-primary)!important;transition:border-color ' . $duration . 's ease,color ' . $duration . 's ease}',
		'#' . $nodeDomId . '.is-view-framed .el-widget-icon-link:hover .el-widget-icon-box{border-color:var(--pb-icon-secondary-hover)!important}',
	];
	foreach (['Tablet' => 1024, 'Mobile' => 767] as $suffix => $breakpoint) {
		$styleBlocks[] = '@media (max-width:' . $breakpoint . 'px){#' . $nodeDomId . '{' . $rootStyle($suffix) . '}#' . $nodeDomId . ' .el-widget-icon-link>i,#' . $nodeDomId . ' .el-widget-icon-box>i{' . $glyphStyle($suffix) . '}}';
	}
@endphp
<div id="{{ $nodeDomId }}" class="{{ $className }}" data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach style="{{ $rootStyle() }}">
	@if($link !== '')<a href="{{ $link }}" class="el-widget-icon-link" @if($openInNewWindow) target="_blank" @endif @if($relAttr !== '') rel="{{ $relAttr }}" @endif @foreach($attrBag as $attrName => $attrValue) {{ $attrName }}="{{ e($attrValue) }}" @endforeach>
	@else<span class="el-widget-icon-link" @foreach($attrBag as $attrName => $attrValue) {{ $attrName }}="{{ e($attrValue) }}" @endforeach>@endif
		@if($view !== 'default')<span class="el-widget-icon-box"><i class="{{ $iconClass }}" style="{{ $glyphStyle() }}" aria-hidden="true"></i></span>@else<i class="{{ $iconClass }}" style="{{ $glyphStyle() }}" aria-hidden="true"></i>@endif
	@if($link !== '')</a>@else</span>@endif
</div>
<style>{!! $advanced['css'] !!}{!! implode("\n", $styleBlocks) !!}</style>
