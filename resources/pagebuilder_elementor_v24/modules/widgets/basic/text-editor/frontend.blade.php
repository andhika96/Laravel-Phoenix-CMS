@php
	$settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$sanitizeHtml = static function ($value): string {
		$raw = trim((string) ($value ?? ''));
		if ($raw === '' || !class_exists('DOMDocument')) return strip_tags($raw);

		$dom = new \DOMDocument('1.0', 'UTF-8');
		$previousErrors = libxml_use_internal_errors(true);
		$dom->loadHTML('<!DOCTYPE html><html><body><div id="pb-text-root">'.$raw.'</div></body></html>', LIBXML_HTML_NODEFDTD | LIBXML_COMPACT);
		libxml_clear_errors();
		libxml_use_internal_errors($previousErrors);
		$root = $dom->getElementById('pb-text-root');
		if (!$root) return strip_tags($raw);

		$allowedTags = ['P', 'BR', 'STRONG', 'EM', 'B', 'I', 'A', 'UL', 'OL', 'LI', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE'];
		$allowedAttributes = ['A' => ['href', 'title', 'target', 'rel'], '*' => ['class']];
		$sanitizeNode = static function (\DOMNode $parent) use (&$sanitizeNode, $allowedTags, $allowedAttributes): void {
			foreach (iterator_to_array($parent->childNodes) as $child) {
				if ($child->nodeType !== XML_ELEMENT_NODE) continue;
				$tag = strtoupper($child->nodeName);
				if (!in_array($tag, $allowedTags, true)) {
					while ($child->firstChild) $parent->insertBefore($child->firstChild, $child);
					$parent->removeChild($child);
					continue;
				}
				$allowed = array_merge($allowedAttributes['*'], $allowedAttributes[$tag] ?? []);
				foreach (iterator_to_array($child->attributes) as $attribute) {
					$name = strtolower($attribute->name);
					$value = (string) $attribute->value;
					if (!in_array($name, $allowed, true) || str_starts_with($name, 'on') || ($name === 'href' && !preg_match('/^(?:https?:|mailto:|tel:|\/|#)/i', $value))) {
						$child->removeAttribute($attribute->name);
					}
				}
				$sanitizeNode($child);
			}
		};
		$sanitizeNode($root);
		$output = '';
		foreach (iterator_to_array($root->childNodes) as $child) $output .= $dom->saveHTML($child);
		return $output;
	};
	$nodeToken = preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) ($node['id'] ?? 'text-editor'))) ?: 'text-editor';
	$advanced = app(\App\Support\PageBuilderElementorV24\WidgetAdvancedStyleResolver::class)->resolve($settings, $nodeToken, request());
	$nodeDomId = $advanced['id'];
	$customClass = implode(' ', array_filter(array_map(fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')), preg_split('/\s+/', trim((string) ($settings['cssClass'] ?? ''))) ?: [])));
	$className = implode(' ', array_values(array_unique(array_merge(['el-widget-text-editor'], $advanced['classes']))));
	$cssLength = fn ($value, string $fallback = '0px') => (($raw = trim((string) ($value ?? ''))) !== '' && preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i', $raw)) ? $raw : $fallback;
	$cssColor = fn ($value, string $fallback = 'inherit') => (($raw = trim((string) ($value ?? ''))) !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw)) ? $raw : $fallback;
	$cssFontFamily = fn ($value) => (($raw = trim((string) ($value ?? 'inherit'))) !== '' && preg_match('/^[A-Za-z0-9 _,\'"-]+$/', $raw)) ? $raw : 'inherit';
	$cssFontWeight = fn ($value, string $fallback = '400') => preg_match('/^(?:normal|bold|[1-9]00)$/', trim((string) $value)) ? trim((string) $value) : $fallback;
	$enum = fn ($value, array $allowed, string $fallback) => in_array(strtolower(trim((string) $value)), $allowed, true) ? strtolower(trim((string) $value)) : $fallback;
	$duration = is_numeric($settings['textEditorTransitionDuration'] ?? null) ? max(0, min(10, (float) $settings['textEditorTransitionDuration'])) : .3;
	$responsive = function (string $base, string $suffix = '', mixed $fallback = '') use ($settings): mixed {
		$keys = $suffix === 'Mobile' ? [$base . 'Mobile', $base . 'Tablet', $base] : ($suffix === 'Tablet' ? [$base . 'Tablet', $base] : [$base]);
		foreach ($keys as $key) { if (array_key_exists($key, $settings) && $settings[$key] !== '' && $settings[$key] !== null) return $settings[$key]; }
		return $fallback;
	};
	$fontStyle = function (string $suffix = '') use ($settings, $responsive, $cssLength, $cssColor, $cssFontFamily, $cssFontWeight, $enum): string {
		return implode(';', [
			'color:' . $cssColor($settings['textEditorTextColor'] ?? '#475467', '#475467'),
			'font-family:' . $cssFontFamily($settings['textEditorFontFamily'] ?? 'inherit'),
			'font-size:' . $cssLength($responsive('textEditorFontSize', $suffix, '16px'), '16px'),
			'font-weight:' . $cssFontWeight($settings['textEditorFontWeight'] ?? '400', '400'),
			'line-height:' . $cssLength($responsive('textEditorLineHeight', $suffix, '1.5em'), '1.5em'),
			'letter-spacing:' . $cssLength($responsive('textEditorLetterSpacing', $suffix, '0px'), '0px'),
			'word-spacing:' . $cssLength($responsive('textEditorWordSpacing', $suffix, '0px'), '0px'),
			'text-transform:' . $enum($settings['textEditorTextTransform'] ?? 'none', ['none', 'uppercase', 'lowercase', 'capitalize'], 'none'),
			'font-style:' . $enum($settings['textEditorFontStyle'] ?? 'normal', ['normal', 'italic', 'oblique'], 'normal'),
			'text-decoration:' . $enum($settings['textEditorTextDecoration'] ?? 'none', ['none', 'underline', 'overline', 'line-through'], 'none'),
			'text-shadow:' . $cssColor($settings['textEditorTextShadow'] ?? 'none', 'none'),
		]);
	};
	$rootStyle = function (string $suffix = '') use ($settings, $responsive, $cssLength, $fontStyle, $cssColor, $duration): string {
		$align = $responsive('align', $suffix, 'left');
		$align = in_array($align, ['left', 'center', 'right', 'justify'], true) ? $align : 'left';
		return implode(';', [
			'box-sizing:border-box', 'text-align:' . $align, $fontStyle($suffix),
			'--pb-text-editor-link-color:' . $cssColor($settings['textEditorLinkColor'] ?? '#4f46e5', '#4f46e5'),
			'--pb-text-editor-hover-color:' . $cssColor($settings['textEditorTextColorHover'] ?? ($settings['textEditorTextColor'] ?? '#475467'), '#475467'),
			'--pb-text-editor-link-hover-color:' . $cssColor($settings['textEditorLinkColorHover'] ?? ($settings['textEditorLinkColor'] ?? '#4f46e5'), '#4f46e5'),
			'--pb-text-editor-paragraph-spacing:' . $cssLength($responsive('paragraphSpacing', $suffix, '1em'), '1em'),
			'--pb-text-editor-transition-duration:' . $duration . 's',
		]);
	};
	$hoverTextColor = $cssColor($settings['textEditorTextColorHover'] ?? null, $settings['textEditorTextColor'] ?? '#475467');
	$hoverLinkColor = $cssColor($settings['textEditorLinkColorHover'] ?? null, $settings['textEditorLinkColor'] ?? '#4f46e5');
	$styleBlocks = [
		'#' . $nodeDomId . ' p{margin:0 0 var(--pb-text-editor-paragraph-spacing,1em)}#' . $nodeDomId . ' p:last-child{margin-bottom:0}',
		'#' . $nodeDomId . ' a{color:var(--pb-text-editor-link-color);transition:color ' . $duration . 's ease}#' . $nodeDomId . ' a:hover{color:var(--pb-text-editor-link-hover-color)}',
		'#' . $nodeDomId . ':hover{color:' . $hoverTextColor . '}',
	];
	foreach (['Tablet' => 1024, 'Mobile' => 767] as $suffix => $breakpoint) {
		$styleBlocks[] = '@media (max-width:' . $breakpoint . 'px){#' . $nodeDomId . '{' . $rootStyle($suffix) . '}#' . $nodeDomId . ' p{margin-bottom:var(--pb-text-editor-paragraph-spacing,1em)}}';
	}
@endphp
<div id="{{ $nodeDomId }}" class="{{ $className }}" data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach style="{{ $rootStyle() }}">{!! $sanitizeHtml($settings['html'] ?? '') !!}</div>
<style>{!! $advanced['css'] !!}{!! implode("\n", $styleBlocks) !!}</style>
