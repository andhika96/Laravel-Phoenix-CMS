@php
	$accordionSettings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeId = trim((string) ($node['id'] ?? 'accordion'));
	$safeNodeToken = preg_replace('/[^A-Za-z0-9_-]+/', '-', $nodeId) ?: 'accordion';
	$nodeDomId = 'pb-node-' . $safeNodeToken;
	$defaultState = ($accordionSettings['defaultState'] ?? 'first-expanded') === 'all-collapsed' ? 'all-collapsed' : 'first-expanded';
	$maxExpanded = ($accordionSettings['maxExpanded'] ?? 'one') === 'multiple' ? 'multiple' : 'one';
	$animationDuration = max(0, min(5000, (int) ($accordionSettings['animationDuration'] ?? 400)));
	$titleTag = strtolower(trim((string) ($accordionSettings['titleTag'] ?? 'div')));
	if (!in_array($titleTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'], true)) $titleTag = 'div';
	$items = is_array($node['accordionItems'] ?? null) && count($node['accordionItems'])
		? array_values($node['accordionItems'])
		: [['id' => 'item-1', 'title' => 'Item #1', 'cssId' => '', 'children' => []]];

	$cssToken = function ($value, string $fallback = '0px'): string {
		$raw = trim((string) ($value ?? ''));
		if ($raw === '') return $fallback;
		if (preg_match('/^-?\d+(?:\.\d+)?$/', $raw)) return $raw . 'px';
		if (preg_match('/^-?\d+(?:\.\d+)?(?:px|%|em|rem|vw|vh)$/i', $raw)) return $raw;
		if (preg_match('/^calc\([^;{}]+\)$/i', $raw)) return $raw;
		return $fallback;
	};
	$responsiveValue = function (string $base, string $suffix = '', $fallback = '') use ($accordionSettings) {
		$value = $accordionSettings[$base . $suffix] ?? null;
		if ($suffix !== '' && ($value === '' || $value === null)) $value = $accordionSettings[$base] ?? null;
		return ($value === '' || $value === null) ? $fallback : $value;
	};
	$borderStyle = function ($value): string {
		$raw = strtolower(trim((string) ($value ?? '')));
		return in_array($raw, ['solid', 'double', 'dotted', 'dashed', 'groove'], true) ? $raw : 'none';
	};
	$gradient = function (string $prefix, string $suffix = '') use ($accordionSettings): string {
		$first = trim((string) ($accordionSettings[$prefix . 'GradientColorOne' . $suffix] ?? '#ffffff')) ?: '#ffffff';
		$second = trim((string) ($accordionSettings[$prefix . 'GradientColorTwo' . $suffix] ?? '#f4f6f8')) ?: '#f4f6f8';
		$firstLocation = max(0, min(100, (int) ($accordionSettings[$prefix . 'GradientLocationOne' . $suffix] ?? 0)));
		$secondLocation = max(0, min(100, (int) ($accordionSettings[$prefix . 'GradientLocationTwo' . $suffix] ?? 100)));
		$type = strtolower(trim((string) ($accordionSettings[$prefix . 'GradientType' . $suffix] ?? 'linear')));
		if ($type === 'radial') {
			$position = preg_replace('/[^a-z\s-]/i', '', (string) ($accordionSettings[$prefix . 'GradientPosition' . $suffix] ?? 'center center')) ?: 'center center';
			return "radial-gradient(at {$position}, {$first} {$firstLocation}%, {$second} {$secondLocation}%)";
		}
		$angle = max(0, min(360, (int) ($accordionSettings[$prefix . 'GradientAngle' . $suffix] ?? 180)));
		return "linear-gradient({$angle}deg, {$first} {$firstLocation}%, {$second} {$secondLocation}%)";
	};
	$background = function (string $prefix, string $suffix = '') use ($accordionSettings, $gradient): string {
		$typeKey = $prefix . 'BackgroundType' . $suffix;
		if (($accordionSettings[$typeKey] ?? 'classic') === 'gradient') return $gradient($prefix, $suffix);
		return trim((string) ($accordionSettings[$prefix . 'BackgroundColor' . $suffix] ?? 'transparent')) ?: 'transparent';
	};
	$styleVars = [
		'--accordion-animation-duration:' . $animationDuration . 'ms',
		'--accordion-item-gap:' . $cssToken($responsiveValue('accordionItemGap', '', '0px'), '0px'),
		'--accordion-content-distance:' . $cssToken($responsiveValue('accordionContentDistance', '', '0px'), '0px'),
		'--accordion-border-radius:' . $cssToken($responsiveValue('accordionBorderRadius', '', '0px'), '0px'),
		'--accordion-padding:' . $cssToken($responsiveValue('accordionPadding', '', '0px'), '0px'),
		'--accordion-header-font-family:' . (trim((string) ($accordionSettings['headerFontFamily'] ?? 'inherit')) ?: 'inherit'),
		'--accordion-header-font-size:' . $cssToken($responsiveValue('headerFontSize', '', '16px'), '16px'),
		'--accordion-header-font-weight:' . (trim((string) ($accordionSettings['headerFontWeight'] ?? '600')) ?: '600'),
		'--accordion-header-line-height:' . (trim((string) ($accordionSettings['headerLineHeight'] ?? '1.4')) ?: '1.4'),
		'--accordion-header-letter-spacing:' . $cssToken($accordionSettings['headerLetterSpacing'] ?? '0px', '0px'),
		'--accordion-header-text-transform:' . (trim((string) ($accordionSettings['headerTextTransform'] ?? 'none')) ?: 'none'),
		'--accordion-header-font-style:' . (trim((string) ($accordionSettings['headerFontStyle'] ?? 'normal')) ?: 'normal'),
		'--accordion-header-text-decoration:' . (trim((string) ($accordionSettings['headerTextDecoration'] ?? 'none')) ?: 'none'),
		'--accordion-icon-size:' . $cssToken($responsiveValue('headerIconSize', '', '16px'), '16px'),
		'--accordion-icon-spacing:' . $cssToken($responsiveValue('headerIconSpacing', '', '12px'), '12px'),
		'--accordion-content-background:' . $background('content'),
		'--accordion-content-border-style:' . $borderStyle($accordionSettings['contentBorderType'] ?? 'none'),
		'--accordion-content-border-width:' . $cssToken($accordionSettings['contentBorderWidth'] ?? '0px', '0px'),
		'--accordion-content-border-color:' . (trim((string) ($accordionSettings['contentBorderColor'] ?? 'transparent')) ?: 'transparent'),
		'--accordion-content-radius:' . $cssToken($responsiveValue('contentBorderRadius', '', '0px'), '0px'),
		'--accordion-content-padding:' . $cssToken($responsiveValue('contentPadding', '', '20px'), '20px'),
	];
	foreach (['Normal' => 'normal', 'Hover' => 'hover', 'Active' => 'active'] as $suffix => $state) {
		$styleVars[] = '--accordion-background-' . $state . ':' . $background('accordion', $suffix);
		$styleVars[] = '--accordion-border-style-' . $state . ':' . $borderStyle($accordionSettings['accordionBorderType' . $suffix] ?? 'solid');
		$styleVars[] = '--accordion-border-width-' . $state . ':' . $cssToken($accordionSettings['accordionBorderWidth' . $suffix] ?? '1px', '1px');
		$styleVars[] = '--accordion-border-color-' . $state . ':' . (trim((string) ($accordionSettings['accordionBorderColor' . $suffix] ?? '#d5dae3')) ?: '#d5dae3');
		$styleVars[] = '--accordion-header-' . $state . '-title-color:' . (trim((string) ($accordionSettings['headerTitleColor' . $suffix] ?? '#1f2937')) ?: '#1f2937');
		$styleVars[] = '--accordion-header-' . $state . '-text-shadow:' . (trim((string) ($accordionSettings['headerTextShadow' . $suffix] ?? 'none')) ?: 'none');
		$styleVars[] = '--accordion-header-' . $state . '-stroke-width:' . $cssToken($accordionSettings['headerTextStrokeWidth' . $suffix] ?? '0px', '0px');
		$styleVars[] = '--accordion-header-' . $state . '-stroke-color:' . (trim((string) ($accordionSettings['headerTextStrokeColor' . $suffix] ?? 'currentColor')) ?: 'currentColor');
		$styleVars[] = '--accordion-header-' . $state . '-icon-color:' . (trim((string) ($accordionSettings['headerIconColor' . $suffix] ?? 'currentColor')) ?: 'currentColor');
	}

	$itemPosition = strtolower(trim((string) ($responsiveValue('itemPosition', '', 'stretch'))));
	if (!in_array($itemPosition, ['start', 'center', 'end', 'stretch'], true)) $itemPosition = 'stretch';
	$iconPosition = strtolower(trim((string) ($responsiveValue('iconPosition', '', 'start')))) === 'end' ? 'end' : 'start';
	$customClasses = preg_split('/\s+/', trim((string) ($accordionSettings['cssClass'] ?? ''))) ?: [];
	$customClasses = array_values(array_filter(array_map(fn ($value) => preg_replace('/[^A-Za-z0-9_-]/', '', $value), $customClasses)));
	$rootClasses = array_merge(['el-widget-accordion', 'is-item-position-' . $itemPosition, 'is-icon-position-' . $iconPosition], $customClasses);

	$sanitizeSvg = function ($value): string {
		$source = trim((string) ($value ?? ''));
		if ($source === '' || !class_exists(DOMDocument::class)) return '';
		$previous = libxml_use_internal_errors(true);
		$doc = new DOMDocument();
		$loaded = $doc->loadXML($source, LIBXML_NONET | LIBXML_NOBLANKS);
		libxml_clear_errors();
		libxml_use_internal_errors($previous);
		$root = $doc->documentElement;
		if (!$loaded || !$root || strtolower($root->tagName) !== 'svg') return '';
		$allowed = ['svg', 'g', 'path', 'circle', 'ellipse', 'rect', 'line', 'polyline', 'polygon', 'title', 'desc'];
		$all = [];
		foreach ($root->getElementsByTagName('*') as $element) $all[] = $element;
		foreach (array_reverse($all) as $element) {
			if (!in_array(strtolower($element->tagName), $allowed, true)) {
				$element->parentNode?->removeChild($element);
				continue;
			}
			foreach (iterator_to_array($element->attributes ?? []) as $attribute) {
				$name = strtolower($attribute->name);
				if (str_starts_with($name, 'on') || $name === 'style' || str_contains($name, 'href')) $element->removeAttribute($attribute->name);
			}
		}
		return $doc->saveXML($root) ?: '';
	};
	$iconData = function (string $role) use ($accordionSettings, $sanitizeSvg): array {
		$source = strtolower(trim((string) ($accordionSettings[$role . 'IconSource'] ?? 'library')));
		if (!in_array($source, ['none', 'library', 'svg'], true)) $source = 'library';
		if ($source === 'svg') {
			$svg = $sanitizeSvg($accordionSettings[$role . 'IconSvg'] ?? '');
			return ['source' => $svg !== '' ? 'svg' : 'none', 'value' => $svg !== '' ? 'data:image/svg+xml;base64,' . base64_encode($svg) : ''];
		}
		$class = trim((string) ($accordionSettings[$role . 'IconClass'] ?? ($role === 'collapse' ? 'fas fa-minus' : 'fas fa-plus')));
		$class = implode(' ', array_filter(array_map(fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', $token), preg_split('/\s+/', $class) ?: [])));
		return ['source' => $source, 'value' => $class];
	};
	$expandIcon = $iconData('expand');
	$collapseIcon = $iconData('collapse');

	$extractText = function (array $nodes) use (&$extractText): string {
		$parts = [];
		foreach ($nodes as $child) {
			if (!is_array($child)) continue;
			$type = strtolower(trim((string) ($child['type'] ?? '')));
			$childSettings = is_array($child['settings'] ?? null) ? $child['settings'] : [];
			$value = match ($type) {
				'heading' => $childSettings['text'] ?? '',
				'text_editor' => $childSettings['html'] ?? '',
				'button' => $childSettings['text'] ?? '',
				'image' => $childSettings['alt'] ?? '',
				default => '',
			};
			$value = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $value)) ?? '');
			if ($value !== '') $parts[] = $value;
			if (is_array($child['children'] ?? null)) $parts[] = $extractText($child['children']);
			foreach (($child['columns'] ?? []) as $column) {
				if (is_array($column['children'] ?? null)) $parts[] = $extractText($column['children']);
			}
			foreach (($child['tabItems'] ?? []) as $tab) {
				if (is_array($tab['children'] ?? null)) $parts[] = $extractText($tab['children']);
			}
			foreach (($child['accordionItems'] ?? []) as $item) {
				if (is_array($item['children'] ?? null)) $parts[] = $extractText($item['children']);
			}
		}
		return trim(preg_replace('/\s+/u', ' ', implode(' ', array_filter($parts))) ?? '');
	};
	$faqEntities = [];
	if (!empty($accordionSettings['faqSchema'])) {
		foreach ($items as $index => $item) {
			$title = trim(strip_tags((string) ($item['title'] ?? ('Item #' . ($index + 1)))));
			$answer = $extractText(is_array($item['children'] ?? null) ? $item['children'] : []);
			if ($title === '' || $answer === '') continue;
			$faqEntities[] = ['@type' => 'Question', 'name' => $title, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer]];
		}
	}
	$faqJson = $faqEntities ? json_encode(
		['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faqEntities],
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
	) : '';

	$responsiveRules = [];
	foreach (['Tablet' => 1024, 'Mobile' => 767] as $suffix => $breakpoint) {
		$rules = [
			'--accordion-item-gap:' . $cssToken($responsiveValue('accordionItemGap', $suffix, '0px'), '0px'),
			'--accordion-content-distance:' . $cssToken($responsiveValue('accordionContentDistance', $suffix, '0px'), '0px'),
			'--accordion-border-radius:' . $cssToken($responsiveValue('accordionBorderRadius', $suffix, '0px'), '0px'),
			'--accordion-padding:' . $cssToken($responsiveValue('accordionPadding', $suffix, '0px'), '0px'),
			'--accordion-header-font-size:' . $cssToken($responsiveValue('headerFontSize', $suffix, '16px'), '16px'),
			'--accordion-icon-size:' . $cssToken($responsiveValue('headerIconSize', $suffix, '16px'), '16px'),
			'--accordion-icon-spacing:' . $cssToken($responsiveValue('headerIconSpacing', $suffix, '12px'), '12px'),
			'--accordion-content-radius:' . $cssToken($responsiveValue('contentBorderRadius', $suffix, '0px'), '0px'),
			'--accordion-content-padding:' . $cssToken($responsiveValue('contentPadding', $suffix, '20px'), '20px'),
		];
		$responsiveRules[] = '@media (max-width:' . $breakpoint . 'px){#' . $nodeDomId . '{' . implode(';', $rules) . '}}';
	}
@endphp

<div
	id="{{ $nodeDomId }}"
	class="{{ implode(' ', $rootClasses) }}"
	style="{{ implode(';', $styleVars) }}"
	data-accordion-root="1"
	data-max-expanded="{{ $maxExpanded }}"
	data-animation-duration="{{ $animationDuration }}"
>
	@foreach($items as $index => $item)
		@php
			$itemId = trim((string) ($item['id'] ?? ('item-' . ($index + 1)))) ?: ('item-' . ($index + 1));
			$safeItemToken = preg_replace('/[^A-Za-z0-9_-]+/', '-', $itemId) ?: ('item-' . ($index + 1));
			$summaryId = 'pb-accordion-summary-' . $safeNodeToken . '-' . $safeItemToken;
			$panelId = 'pb-accordion-panel-' . $safeNodeToken . '-' . $safeItemToken;
			$isOpen = $defaultState === 'first-expanded' && $index === 0;
			$customItemId = trim((string) ($item['cssId'] ?? ''));
			$safeItemId = preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/', $customItemId) ? $customItemId : '';
			$itemTitle = trim((string) ($item['title'] ?? ('Item #' . ($index + 1)))) ?: ('Item #' . ($index + 1));
			$itemChildren = is_array($item['children'] ?? null) ? $item['children'] : [];
		@endphp
		<details class="el-widget-accordion__item" data-accordion-item="{{ $safeItemToken }}" @if($safeItemId !== '') id="{{ $safeItemId }}" @endif @if($isOpen) open @endif>
			<summary id="{{ $summaryId }}" class="el-widget-accordion__summary" aria-expanded="{{ $isOpen ? 'true' : 'false' }}" aria-controls="{{ $panelId }}">
				@if($iconPosition === 'start')
					@include('pagebuilder_elementor.partials.render_accordion_icon', ['expandIcon' => $expandIcon, 'collapseIcon' => $collapseIcon])
				@endif
				<{{ $titleTag }} class="el-widget-accordion__heading"><span class="el-widget-accordion__title">{{ $itemTitle }}</span></{{ $titleTag }}>
				@if($iconPosition === 'end')
					@include('pagebuilder_elementor.partials.render_accordion_icon', ['expandIcon' => $expandIcon, 'collapseIcon' => $collapseIcon])
				@endif
			</summary>
			<div id="{{ $panelId }}" class="el-widget-accordion__content-wrap" role="region" aria-labelledby="{{ $summaryId }}">
				<div class="el-widget-accordion__content">
					@foreach($itemChildren as $child)
						@include('pagebuilder_elementor.partials.render_node', ['node' => $child])
					@endforeach
				</div>
			</div>
		</details>
	@endforeach
</div>
<style>{!! implode('', $responsiveRules) !!}</style>
@if($faqJson !== '')
	<script type="application/ld+json">{!! $faqJson !!}</script>
@endif
