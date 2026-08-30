@php
	$settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeToken = preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) ($node['id'] ?? 'button'))) ?: 'button';
	$advanced = app(\App\Support\PageBuilderElementorV24\WidgetAdvancedStyleResolver::class)->resolve($settings, $nodeToken, request());
	$nodeDomId = $advanced['id'];
	$cleanClasses = fn ($value) => implode(' ', array_filter(array_map(fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')), preg_split('/\s+/', trim((string) $value)) ?: [])));
	$cssLength = function ($value, string $fallback = '0px'): string {
		$raw = trim((string) ($value ?? ''));
		$tokens = preg_split('/\s+/', $raw) ?: [];
		if (count($tokens) < 1 || count($tokens) > 4) return $fallback;
		foreach ($tokens as $token) if (!preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i', $token)) return $fallback;
		return implode(' ', $tokens);
	};
	$cssColor = fn ($value, string $fallback = 'inherit') => (($raw = trim((string) ($value ?? ''))) !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw)) ? $raw : $fallback;
	$sanitizeSvg = function (mixed $value): string {
		$source = trim((string) $value);
		if ($source === '' || !class_exists(\DOMDocument::class)) return '';
		$previous = libxml_use_internal_errors(true);
		$dom = new \DOMDocument();
		$loaded = $dom->loadXML($source, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
		libxml_clear_errors();
		libxml_use_internal_errors($previous);
		$root = $dom->documentElement;
		if (!$loaded || !$root || strtolower($root->tagName) !== 'svg') return '';
		$allowed = ['svg','g','path','circle','ellipse','rect','line','polyline','polygon','title','desc'];
		foreach (iterator_to_array($dom->getElementsByTagName('*')) as $element) {
			if (!in_array(strtolower($element->tagName), $allowed, true)) { $element->parentNode?->removeChild($element); continue; }
			foreach (iterator_to_array($element->attributes ?? []) as $attribute) {
				$name = strtolower($attribute->name);
				if (str_starts_with($name, 'on') || $name === 'style' || str_contains($name, 'href')) $element->removeAttribute($attribute->name);
			}
		}
		return (string) $dom->saveXML($root);
	};
	$cssFontFamily = fn ($value) => (($raw = trim((string) ($value ?? 'inherit'))) !== '' && preg_match('/^[A-Za-z0-9 _,\'"-]+$/', $raw)) ? $raw : 'inherit';
	$cssFontWeight = fn ($value, string $fallback = '400') => preg_match('/^(?:normal|bold|[1-9]00)$/', trim((string) $value)) ? trim((string) $value) : $fallback;
	$enum = fn ($value, array $allowed, string $fallback) => in_array(strtolower(trim((string) $value)), $allowed, true) ? strtolower(trim((string) $value)) : $fallback;
	$duration = is_numeric($settings['buttonTransitionDuration'] ?? null) ? max(0, min(10, (float) $settings['buttonTransitionDuration'])) : .3;
	$responsive = function (string $base, string $suffix = '', mixed $fallback = '') use ($settings): mixed {
		$keys = $suffix === 'Mobile' ? [$base . 'Mobile', $base . 'Tablet', $base] : ($suffix === 'Tablet' ? [$base . 'Tablet', $base] : [$base]);
		foreach ($keys as $key) { if (array_key_exists($key, $settings) && $settings[$key] !== '' && $settings[$key] !== null) return $settings[$key]; }
		return $fallback;
	};
	$state = function (string $base, string $suffix = '') use ($settings) {
		$value = $settings[$base . $suffix] ?? null;
		return ($suffix !== 'Hover' && ($value === '' || $value === null)) ? ($settings[$base] ?? null) : (($value === '' || $value === null) ? ($settings[$base] ?? null) : $value);
	};
	$background = function (string $suffix = '') use ($state, $cssColor): string {
		$type = ($state('buttonBackgroundType', $suffix) ?? 'classic') === 'gradient' ? 'gradient' : 'classic';
		if ($type === 'gradient') {
			$first = $cssColor($state('buttonGradientColorOne', $suffix), '#0d6efd');
			$second = $cssColor($state('buttonGradientColorTwo', $suffix), '#6f42c1');
			$angle = is_numeric($state('buttonGradientAngle', $suffix)) ? max(0, min(360, (float) $state('buttonGradientAngle', $suffix))) : 90;
			return 'linear-gradient(' . $angle . 'deg, ' . $first . ', ' . $second . ')';
		}
		return $cssColor($state('buttonBackgroundColor', $suffix), $suffix === 'Hover' ? '#0b5ed7' : '#0d6efd');
	};
	$border = function (string $suffix = '') use ($state, $enum, $cssLength, $cssColor): array {
		$type = $enum($state('buttonBorderType', $suffix), ['none', 'solid', 'double', 'dotted', 'dashed', 'groove'], 'none');
		return ['style' => $type, 'width' => $type === 'none' ? '0' : $cssLength($state('buttonBorderWidth', $suffix), '1px'), 'color' => $cssColor($state('buttonBorderColor', $suffix), '#0d6efd')];
	};
	$iconSource = $enum($settings['iconSource'] ?? 'none', ['none', 'library', 'svg'], 'none');
	$iconClass = trim((string) ($settings['iconClass'] ?? ''));
	$iconSvg = $sanitizeSvg($settings['iconSvg'] ?? '');
	$iconSource = $iconSource === 'library' && preg_match('/^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i', $iconClass) ? 'library' : ($iconSource === 'svg' && $iconSvg !== '' ? 'svg' : 'none');
	$buttonHasIcon = $iconSource !== 'none';
	$sourceClassStyles = ($settings['sourceClassStyles'] ?? false) === true;
	$iconImportNodeKey = trim((string) ($settings['iconImportNodeKey'] ?? ''));
	$iconImportNodeKey = preg_match('/^import-node-[A-Za-z0-9_-]+$/', $iconImportNodeKey) ? $iconImportNodeKey : '';
	$buttonIconPosition = $enum($settings['buttonIconPosition'] ?? 'row', ['row', 'row-reverse'], 'row');
	$shadow = function (string $suffix = '') use ($state, $cssLength, $cssColor): string {
		if (!in_array($state('buttonBoxShadowEnabled', $suffix), [true, 1, '1', 'true'], true)) return 'none';
		$inset = in_array($state('buttonBoxShadowInset', $suffix), [true, 1, '1', 'true'], true) ? ' inset' : '';
		return implode(' ', [$cssLength($state('buttonBoxShadowX', $suffix), '0px'), $cssLength($state('buttonBoxShadowY', $suffix), '4px'), $cssLength($state('buttonBoxShadowBlur', $suffix), '12px'), $cssLength($state('buttonBoxShadowSpread', $suffix), '0px'), $cssColor($state('buttonBoxShadowColor', $suffix), 'rgba(0,0,0,.16)')]) . $inset;
	};
	$fontStyle = function (string $suffix = '') use ($settings, $responsive, $cssLength, $cssColor, $cssFontFamily, $cssFontWeight, $enum): string {
		return implode(';', [
			'font-family:' . $cssFontFamily($settings['buttonFontFamily'] ?? 'inherit'),
			'font-size:' . $cssLength($responsive('buttonFontSize', $suffix, '16px'), '16px'),
			'font-weight:' . $cssFontWeight($settings['buttonFontWeight'] ?? '600', '600'),
			'line-height:' . $cssLength($responsive('buttonLineHeight', $suffix, '1.2em'), '1.2em'),
			'letter-spacing:' . $cssLength($responsive('buttonLetterSpacing', $suffix, '0px'), '0px'),
			'word-spacing:' . $cssLength($responsive('buttonWordSpacing', $suffix, '0px'), '0px'),
			'text-transform:' . ($enum($settings['buttonTextTransform'] ?? 'none', ['none', 'uppercase', 'lowercase', 'capitalize'], 'none')),
			'font-style:' . ($enum($settings['buttonFontStyle'] ?? 'normal', ['normal', 'italic', 'oblique'], 'normal')),
			'text-decoration:' . ($enum($settings['buttonTextDecoration'] ?? 'none', ['none', 'underline', 'overline', 'line-through'], 'none')),
			'text-shadow:' . $cssColor($settings['buttonTextShadow'] ?? 'none', 'none'),
		]);
	};
	$buttonStyle = function (string $suffix = '') use ($settings, $responsive, $cssLength, $cssColor, $background, $border, $shadow, $fontStyle, $duration, $buttonHasIcon, $buttonIconPosition, $sourceClassStyles): string {
		if ($sourceClassStyles) return 'box-sizing:border-box';
		$borderStyle = $border($suffix);
		return implode(';', [
			'box-sizing:border-box', 'display:inline-flex', 'align-items:center', 'justify-content:center', 'text-decoration:none',
			'flex-direction:' . ($buttonHasIcon ? $buttonIconPosition : 'row'), 'gap:' . ($buttonHasIcon ? $cssLength($responsive('buttonIconSpacing', $suffix, '8px'), '8px') : '0px'),
			'padding:' . $cssLength($responsive('buttonPadding', $suffix, '12px 24px'), '12px 24px'),
			'border-radius:' . $cssLength($responsive('buttonBorderRadius', $suffix, '5px'), '5px'),
			'border-style:' . $borderStyle['style'], 'border-width:' . $borderStyle['width'], 'border-color:' . $borderStyle['color'],
			'color:' . $cssColor($settings['buttonTextColor' . $suffix] ?? null, $suffix === 'Hover' ? ($settings['buttonTextColor'] ?? '#ffffff') : '#ffffff'),
			'background:' . $background($suffix), 'box-shadow:' . $shadow($suffix), $fontStyle($suffix), 'transition:color ' . $duration . 's ease,background ' . $duration . 's ease,border ' . $duration . 's ease,box-shadow ' . $duration . 's ease',
		]);
	};
	$align = $responsive('align', '', 'left');
	$wrapStyle = 'display:flex;justify-content:' . ($align === 'center' ? 'center' : ($align === 'right' ? 'flex-end' : 'flex-start'));
	$buttonClass = $sourceClassStyles
		? implode(' ', array_filter(preg_split('/\s+/', trim((string) ($settings['className'] ?? ''))) ?: [], fn ($token) => preg_match('/^[A-Za-z0-9_:\/\[\]\.\-%]+$/', (string) $token)))
		: $cleanClasses($settings['className'] ?? 'btn btn-primary');
	$buttonClass = $buttonClass ?: 'btn btn-primary';
	if ($sourceClassStyles) $buttonClass .= ' pb-import-source-button';
	$rootClasses = array_values(array_unique(array_merge(['pb-basic-button-canvas'], $advanced['classes'])));
	$normalBorder = $border(); $hoverBorder = $border('Hover');
	$hoverRules = implode(';', [
		'color:' . $cssColor($settings['buttonTextColorHover'] ?? null, $settings['buttonTextColor'] ?? '#ffffff'), 'background:' . $background('Hover'),
		'border-style:' . $hoverBorder['style'], 'border-width:' . $hoverBorder['width'], 'border-color:' . $hoverBorder['color'], 'box-shadow:' . $shadow('Hover'),
	]);
	$styleBlocks = ['#' . $nodeDomId . ' .el-widget-button__icon{display:inline-flex;align-items:center;justify-content:center;line-height:1}', '#' . $nodeDomId . ' .el-widget-button__icon-svg{width:1em;height:1em;object-fit:contain}', '#' . $nodeDomId . ' .el-widget-button__text{white-space:nowrap}'];
	if (!$sourceClassStyles) array_unshift($styleBlocks, '#' . $nodeDomId . ' .el-widget-button:hover{' . $hoverRules . '}');
	foreach (['Tablet' => 1024, 'Mobile' => 767] as $suffix => $breakpoint) {
		$currentAlign = $responsive('align', $suffix, 'left');
		$mediaWrap = 'display:flex;justify-content:' . ($currentAlign === 'center' ? 'center' : ($currentAlign === 'right' ? 'flex-end' : 'flex-start'));
		$mediaButton = $buttonStyle($suffix);
		if ($currentAlign === 'stretch') $mediaButton .= ';width:100%';
		$styleBlocks[] = '@media (max-width:' . $breakpoint . 'px){#' . $nodeDomId . '{' . $mediaWrap . '}#' . $nodeDomId . ' .el-widget-button{' . $mediaButton . '}}';
	}
@endphp
<div id="{{ $nodeDomId }}" class="{{ implode(' ', $rootClasses) }}" data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach style="{{ $wrapStyle }}">
	<a href="{{ $settings['url'] ?? '#' }}" class="el-widget-button {{ $buttonClass }}" @if(!empty($settings['newTab'])) target="_blank" rel="noopener" @endif @if(isset($advanced['attributes']['data-pb-import-node'])) data-pb-import-node="{{ e($advanced['attributes']['data-pb-import-node']) }}" @endif style="{{ $buttonStyle() }}@if($align === 'stretch');width:100%@endif">@if($buttonHasIcon)<span class="el-widget-button__icon" aria-hidden="true">@if($iconSource === 'svg')<img class="el-widget-button__icon-svg" src="{{ 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($iconSvg) }}" alt="">@else<i class="{{ $iconClass }}" @if($iconImportNodeKey !== '') data-pb-import-node="{{ e($iconImportNodeKey) }}" @endif></i>@endif</span>@endif<span class="el-widget-button__text">{{ $settings['text'] ?? 'Click here' }}</span></a>
</div>
<style>{!! $advanced['css'] !!}{!! implode("\n", $styleBlocks) !!}</style>
