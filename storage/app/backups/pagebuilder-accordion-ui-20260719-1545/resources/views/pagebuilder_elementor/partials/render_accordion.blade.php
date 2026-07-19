@php
	$accordionSettings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeId = trim((string) ($node['id'] ?? 'accordion'));
	$safeNodeToken = preg_replace('/[^A-Za-z0-9_-]+/', '-', $nodeId) ?: 'accordion';
	$internalNodeDomId = 'pb-node-' . $safeNodeToken;
	$requestedCssId = trim((string) ($accordionSettings['cssId'] ?? ''));
	$requestedCssId = preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/', $requestedCssId) ? $requestedCssId : '';
	$usedCssIds = request()->attributes->get('_pagebuilder_elementor_css_ids', []);
	if (!is_array($usedCssIds)) $usedCssIds = [];
	$nodeDomId = $internalNodeDomId;
	if ($requestedCssId !== '' && !in_array($requestedCssId, $usedCssIds, true)) {
		$nodeDomId = $requestedCssId;
		$usedCssIds[] = $requestedCssId;
		request()->attributes->set('_pagebuilder_elementor_css_ids', $usedCssIds);
	}
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

	$spaceToken = function ($value, string $fallback = '0') use ($cssToken): string {
		$raw = strtolower(trim((string) ($value ?? '')));
		return $raw === 'auto' ? 'auto' : $cssToken($value, $fallback);
	};
	$safeUrl = function ($value): string {
		$url = trim((string) ($value ?? ''));
		if ($url === '') return '';
		if (preg_match('/^(?:https?:|data:image\/(?:png|gif|jpe?g|webp|svg\+xml);base64,|\/|#)/i', $url)) return str_replace(['"', "'", '\\'], '', $url);
		return '';
	};
	$advancedStyles = [
		'margin-top:' . $spaceToken($responsiveValue('marginTop', '', '0px'), '0'),
		'margin-right:' . $spaceToken($responsiveValue('marginRight', '', '0px'), '0'),
		'margin-bottom:' . $spaceToken($responsiveValue('marginBottom', '', '0px'), '0'),
		'margin-left:' . $spaceToken($responsiveValue('marginLeft', '', '0px'), '0'),
		'padding-top:' . $cssToken($responsiveValue('paddingTop', '', '0px'), '0'),
		'padding-right:' . $cssToken($responsiveValue('paddingRight', '', '0px'), '0'),
		'padding-bottom:' . $cssToken($responsiveValue('paddingBottom', '', '0px'), '0'),
		'padding-left:' . $cssToken($responsiveValue('paddingLeft', '', '0px'), '0'),
	];
	$widthMode = strtolower(trim((string) ($accordionSettings['widthMode'] ?? 'default')));
	if ($widthMode === 'full') $advancedStyles[] = 'width:100%';
	if ($widthMode === 'inline') $advancedStyles[] = 'width:fit-content';
	if ($widthMode === 'custom') $advancedStyles[] = 'width:' . $cssToken($responsiveValue('customWidth', '', ''), 'auto');
	$alignSelf = trim((string) ($responsiveValue('alignSelf', '', 'auto')));
	if (in_array($alignSelf, ['flex-start', 'center', 'flex-end', 'stretch'], true)) $advancedStyles[] = 'align-self:' . $alignSelf;
	$orderMode = strtolower(trim((string) ($responsiveValue('orderMode', '', 'default'))));
	if ($orderMode === 'start') $advancedStyles[] = 'order:-9999';
	if ($orderMode === 'end') $advancedStyles[] = 'order:9999';
	if ($orderMode === 'custom' && is_numeric($responsiveValue('order', '', ''))) $advancedStyles[] = 'order:' . (int) $responsiveValue('order', '', 0);
	$sizeMode = strtolower(trim((string) ($responsiveValue('sizeMode', '', 'none'))));
	if ($sizeMode === 'grow') $advancedStyles[] = 'flex:1 1 0';
	if ($sizeMode === 'shrink') $advancedStyles[] = 'flex:0 1 auto';
	if ($sizeMode === 'custom') $advancedStyles[] = 'flex:' . ((float) $responsiveValue('flexGrow', '', 0)) . ' ' . ((float) $responsiveValue('flexShrink', '', 1)) . ' auto';
	$position = strtolower(trim((string) ($accordionSettings['position'] ?? 'default')));
	if (in_array($position, ['absolute', 'fixed'], true)) {
		$advancedStyles[] = 'position:' . $position;
		$advancedStyles[] = (($accordionSettings['horizontalOrientation'] ?? 'left') === 'right' ? 'right:' : 'left:') . $spaceToken($responsiveValue('positionX', '', '0px'), '0');
		$advancedStyles[] = (($accordionSettings['verticalOrientation'] ?? 'top') === 'bottom' ? 'bottom:' : 'top:') . $spaceToken($responsiveValue('positionY', '', '0px'), '0');
	}
	$sticky = strtolower(trim((string) ($accordionSettings['sticky'] ?? 'none')));
	if (in_array($sticky, ['top', 'bottom'], true)) {
		$advancedStyles[] = 'position:sticky';
		$advancedStyles[] = $sticky . ':' . $spaceToken($responsiveValue('stickyOffset', '', '0px'), '0');
	}
	if ($responsiveValue('zIndex', '', '') !== '' && is_numeric($responsiveValue('zIndex', '', ''))) $advancedStyles[] = 'z-index:' . (int) $responsiveValue('zIndex', '', 0);

	$advancedBackground = function (string $suffix = '') use ($accordionSettings, $gradient, $safeUrl): array {
		$type = strtolower(trim((string) ($accordionSettings['advancedBackgroundType' . $suffix] ?? 'none')));
		if ($type === 'classic') {
			$image = $safeUrl($accordionSettings['advancedBackgroundImage' . $suffix] ?? '');
			return $image !== ''
				? ['background-image:url("' . $image . '")', 'background-position:' . ($accordionSettings['advancedBackgroundPosition' . $suffix] ?? 'center center'), 'background-repeat:' . ($accordionSettings['advancedBackgroundRepeat' . $suffix] ?? 'no-repeat'), 'background-size:' . ($accordionSettings['advancedBackgroundSize' . $suffix] ?? 'cover'), 'background-attachment:' . ($accordionSettings['advancedBackgroundAttachment' . $suffix] ?? 'scroll')]
				: ['background-color:' . (trim((string) ($accordionSettings['advancedBackgroundColor' . $suffix] ?? 'transparent')) ?: 'transparent')];
		}
		if ($type === 'gradient') return ['background-image:' . $gradient('advanced', $suffix)];
		return [];
	};
	$advancedStyles = array_merge($advancedStyles, $advancedBackground());
	$advancedBorderType = strtolower(trim((string) ($accordionSettings['advancedBorderType'] ?? 'none')));
	$advancedBorderType = in_array($advancedBorderType, ['solid', 'double', 'dotted', 'dashed', 'groove'], true) ? $advancedBorderType : 'none';
	$advancedStyles[] = 'border-style:' . $advancedBorderType;
	$advancedStyles[] = 'border-width:' . ($advancedBorderType === 'none' ? '0' : $cssToken($accordionSettings['advancedBorderWidth'] ?? '1px', '1px'));
	$advancedStyles[] = 'border-color:' . (trim((string) ($accordionSettings['advancedBorderColor'] ?? 'transparent')) ?: 'transparent');
	$advancedStyles[] = 'border-radius:' . $cssToken($responsiveValue('advancedBorderRadius', '', '0px'), '0');
	if (!empty($accordionSettings['advancedBoxShadowEnabled'])) {
		$advancedStyles[] = 'box-shadow:' . implode(' ', [
			$cssToken($accordionSettings['advancedBoxShadowX'] ?? '0px', '0'), $cssToken($accordionSettings['advancedBoxShadowY'] ?? '4px', '4px'),
			$cssToken($accordionSettings['advancedBoxShadowBlur'] ?? '16px', '16px'), $cssToken($accordionSettings['advancedBoxShadowSpread'] ?? '0px', '0'),
			trim((string) ($accordionSettings['advancedBoxShadowColor'] ?? 'rgba(0,0,0,.2)')) ?: 'rgba(0,0,0,.2)', !empty($accordionSettings['advancedBoxShadowInset']) ? 'inset' : '',
		]);
	}
	$angleToken = fn ($value, string $fallback = '0deg') => preg_match('/^-?\d+(?:\.\d+)?(?:deg|rad|turn)$/i', trim((string) $value)) ? trim((string) $value) : $fallback;
	$numberToken = fn ($value, float $fallback = 1.0) => is_numeric($value) ? (float) $value : $fallback;
	$buildTransform = function (string $suffix = '', string $responsiveSuffix = '') use ($accordionSettings, $responsiveValue, $cssToken, $spaceToken, $angleToken, $numberToken): string {
		$chunks = [];
		$perspective = $cssToken($accordionSettings['transformPerspective' . $suffix] ?? '0px', '0px');
		if ($perspective !== '0' && $perspective !== '0px') $chunks[] = 'perspective(' . $perspective . ')';
		$chunks[] = 'translate(' . $spaceToken($responsiveValue('transformOffsetX' . $suffix, $responsiveSuffix, '0px'), '0') . ',' . $spaceToken($responsiveValue('transformOffsetY' . $suffix, $responsiveSuffix, '0px'), '0') . ')';
		$chunks[] = 'rotate(' . $angleToken($accordionSettings['transformRotate' . $suffix] ?? '0deg') . ')';
		$chunks[] = 'rotateX(' . $angleToken($accordionSettings['transformRotateX' . $suffix] ?? '0deg') . ')';
		$chunks[] = 'rotateY(' . $angleToken($accordionSettings['transformRotateY' . $suffix] ?? '0deg') . ')';
		$chunks[] = 'scale(' . $numberToken($accordionSettings['transformScale' . $suffix] ?? 1) . ')';
		$chunks[] = 'skew(' . $angleToken($accordionSettings['transformSkewX' . $suffix] ?? '0deg') . ',' . $angleToken($accordionSettings['transformSkewY' . $suffix] ?? '0deg') . ')';
		if (!empty($accordionSettings['transformFlipHorizontal' . $suffix])) $chunks[] = 'scaleX(-1)';
		if (!empty($accordionSettings['transformFlipVertical' . $suffix])) $chunks[] = 'scaleY(-1)';
		return implode(' ', $chunks);
	};
	$advancedStyles[] = '--pb-advanced-transform:' . $buildTransform();
	$advancedStyles[] = 'transform-origin:' . ($accordionSettings['transformOriginX'] ?? 'center') . ' ' . ($accordionSettings['transformOriginY'] ?? 'center');
	$advancedStyles[] = 'transition:background ' . max(0, (float) ($accordionSettings['advancedBackgroundHoverDuration'] ?? .3)) . 's ease,border ' . max(0, (float) ($accordionSettings['advancedBorderHoverDuration'] ?? .3)) . 's ease,box-shadow ' . max(0, (float) ($accordionSettings['advancedBorderHoverDuration'] ?? .3)) . 's ease,transform ' . max(0, (float) ($accordionSettings['transformHoverDuration'] ?? .3)) . 's ease';

	if (!empty($accordionSettings['maskEnabled'])) {
		$maskImage = '';
		if (($accordionSettings['maskShape'] ?? 'circle') === 'custom') $maskImage = $safeUrl($accordionSettings['maskCustomImage'] ?? '');
		if ($maskImage !== '') $maskImage = 'url("' . $maskImage . '")';
		else {
			$shapeSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="black"/></svg>';
			$maskImage = 'url("data:image/svg+xml;base64,' . base64_encode($shapeSvg) . '")';
		}
		$maskSizeMode = strtolower(trim((string) ($responsiveValue('maskSize', '', 'fit'))));
		$maskSize = $maskSizeMode === 'fill' ? 'cover' : ($maskSizeMode === 'custom' ? max(1, (float) $responsiveValue('maskScale', '', 100)) . '%' : 'contain');
		$maskPosition = $responsiveValue('maskPosition', '', 'center center');
		if ($maskPosition === 'custom') $maskPosition = $responsiveValue('maskPositionX', '', '50%') . ' ' . $responsiveValue('maskPositionY', '', '50%');
		$maskRepeat = $responsiveValue('maskRepeat', '', 'no-repeat');
		foreach (['mask-image', '-webkit-mask-image'] as $prop) $advancedStyles[] = $prop . ':' . $maskImage;
		foreach (['mask-size', '-webkit-mask-size'] as $prop) $advancedStyles[] = $prop . ':' . $maskSize;
		foreach (['mask-position', '-webkit-mask-position'] as $prop) $advancedStyles[] = $prop . ':' . $maskPosition;
		foreach (['mask-repeat', '-webkit-mask-repeat'] as $prop) $advancedStyles[] = $prop . ':' . $maskRepeat;
	}
	$styleVars = array_merge($styleVars, $advancedStyles);

	$customAttributes = [];
	foreach (($accordionSettings['attributes'] ?? []) as $attribute) {
		if (!is_array($attribute)) continue;
		$name = strtolower(trim((string) ($attribute['name'] ?? '')));
		$value = trim((string) ($attribute['value'] ?? ''));
		if (!preg_match('/^[a-z][a-z0-9_:.\-]*$/', $name)) continue;
		if (str_starts_with($name, 'on') || in_array($name, ['style', 'id', 'class'], true)) continue;
		if (in_array($name, ['href', 'src', 'action', 'formaction', 'xlink:href'], true) && $safeUrl($value) === '') continue;
		$customAttributes[$name] = $value;
	}
	$motionConfig = [
		'scrollingEffects' => !empty($accordionSettings['scrollingEffects']),
		'verticalScrollEnabled' => !empty($accordionSettings['verticalScrollEnabled']), 'verticalScrollDirection' => $accordionSettings['verticalScrollDirection'] ?? 'up', 'verticalScrollSpeed' => (float) ($accordionSettings['verticalScrollSpeed'] ?? 4),
		'horizontalScrollEnabled' => !empty($accordionSettings['horizontalScrollEnabled']), 'horizontalScrollDirection' => $accordionSettings['horizontalScrollDirection'] ?? 'left', 'horizontalScrollSpeed' => (float) ($accordionSettings['horizontalScrollSpeed'] ?? 4),
		'transparencyEnabled' => !empty($accordionSettings['transparencyEnabled']), 'transparencyDirection' => $accordionSettings['transparencyDirection'] ?? 'fade-in', 'transparencyLevel' => (float) ($accordionSettings['transparencyLevel'] ?? 5),
		'blurEnabled' => !empty($accordionSettings['blurEnabled']), 'blurDirection' => $accordionSettings['blurDirection'] ?? 'fade-in', 'blurLevel' => (float) ($accordionSettings['blurLevel'] ?? 5),
		'rotateEnabled' => !empty($accordionSettings['rotateEnabled']), 'rotateDirection' => $accordionSettings['rotateDirection'] ?? 'left', 'rotateSpeed' => (float) ($accordionSettings['rotateSpeed'] ?? 4),
		'scaleEnabled' => !empty($accordionSettings['scaleEnabled']), 'scaleDirection' => $accordionSettings['scaleDirection'] ?? 'up', 'scaleSpeed' => (float) ($accordionSettings['scaleSpeed'] ?? 4),
		'applyDesktop' => ($accordionSettings['scrollApplyDesktop'] ?? true) !== false, 'applyTablet' => ($accordionSettings['scrollApplyTablet'] ?? true) !== false, 'applyMobile' => ($accordionSettings['scrollApplyMobile'] ?? true) !== false,
		'mouseEffects' => !empty($accordionSettings['mouseEffects']), 'mouseTrackEnabled' => !empty($accordionSettings['mouseTrackEnabled']), 'mouseTrackDirection' => $accordionSettings['mouseTrackDirection'] ?? 'direct', 'mouseTrackSpeed' => (float) ($accordionSettings['mouseTrackSpeed'] ?? 1),
		'tilt3dEnabled' => !empty($accordionSettings['tilt3dEnabled']), 'tilt3dDirection' => $accordionSettings['tilt3dDirection'] ?? 'direct', 'tilt3dSpeed' => (float) ($accordionSettings['tilt3dSpeed'] ?? 1),
	];
	$motionJson = json_encode($motionConfig, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?: '{}';

	$itemPosition = strtolower(trim((string) ($responsiveValue('itemPosition', '', 'stretch'))));
	if (!in_array($itemPosition, ['start', 'center', 'end', 'stretch'], true)) $itemPosition = 'stretch';
	$iconPosition = strtolower(trim((string) ($responsiveValue('iconPosition', '', 'start')))) === 'end' ? 'end' : 'start';
	$customClasses = preg_split('/\s+/', trim((string) ($accordionSettings['cssClass'] ?? ''))) ?: [];
	$customClasses = array_values(array_filter(array_map(fn ($value) => preg_replace('/[^A-Za-z0-9_-]/', '', $value), $customClasses)));
	$rootClasses = array_merge(['el-widget-accordion', 'pb-advanced-widget', 'is-item-position-' . $itemPosition, 'is-icon-position-' . $iconPosition], $customClasses);
	if (!empty($accordionSettings['hideDesktop'])) $rootClasses[] = 'pb-hide-desktop';
	if (!empty($accordionSettings['hideTablet'])) $rootClasses[] = 'pb-hide-tablet';
	if (!empty($accordionSettings['hideMobile'])) $rootClasses[] = 'pb-hide-mobile';
	if (!empty($accordionSettings['scrollingEffects'])) $rootClasses[] = 'pb-motion-scroll';
	if (!empty($accordionSettings['mouseEffects'])) $rootClasses[] = 'pb-motion-mouse';
	if (!empty($accordionSettings['entranceAnimation'])) {
		$rootClasses[] = 'pb-advanced-entrance';
		$rootClasses[] = 'pb-anim-' . preg_replace('/[^A-Za-z0-9_-]/', '', (string) $accordionSettings['entranceAnimation']);
	}

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
		$all = [$root];
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
			'margin-top:' . $spaceToken($responsiveValue('marginTop', $suffix, '0px'), '0'),
			'margin-right:' . $spaceToken($responsiveValue('marginRight', $suffix, '0px'), '0'),
			'margin-bottom:' . $spaceToken($responsiveValue('marginBottom', $suffix, '0px'), '0'),
			'margin-left:' . $spaceToken($responsiveValue('marginLeft', $suffix, '0px'), '0'),
			'padding-top:' . $cssToken($responsiveValue('paddingTop', $suffix, '0px'), '0'),
			'padding-right:' . $cssToken($responsiveValue('paddingRight', $suffix, '0px'), '0'),
			'padding-bottom:' . $cssToken($responsiveValue('paddingBottom', $suffix, '0px'), '0'),
			'padding-left:' . $cssToken($responsiveValue('paddingLeft', $suffix, '0px'), '0'),
			'border-radius:' . $cssToken($responsiveValue('advancedBorderRadius', $suffix, '0px'), '0'),
			'--pb-advanced-transform:' . $buildTransform('', $suffix),
		];
		if ($widthMode === 'custom') $rules[] = 'width:' . $cssToken($responsiveValue('customWidth', $suffix, ''), 'auto');
		if (in_array($responsiveValue('alignSelf', $suffix, 'auto'), ['flex-start', 'center', 'flex-end', 'stretch'], true)) $rules[] = 'align-self:' . $responsiveValue('alignSelf', $suffix, 'auto');
		if ($responsiveValue('zIndex', $suffix, '') !== '' && is_numeric($responsiveValue('zIndex', $suffix, ''))) $rules[] = 'z-index:' . (int) $responsiveValue('zIndex', $suffix, 0);
		$responsiveRules[] = '@media (max-width:' . $breakpoint . 'px){#' . $nodeDomId . '{' . implode(';', $rules) . '}}';
	}
	$hoverRules = array_merge($advancedBackground('Hover'), [
		'border-style:' . (in_array(($accordionSettings['advancedBorderTypeHover'] ?? 'none'), ['solid', 'double', 'dotted', 'dashed', 'groove'], true) ? $accordionSettings['advancedBorderTypeHover'] : 'none'),
		'border-width:' . $cssToken($accordionSettings['advancedBorderWidthHover'] ?? '0px', '0'),
		'border-color:' . (trim((string) ($accordionSettings['advancedBorderColorHover'] ?? 'transparent')) ?: 'transparent'),
		'border-radius:' . $cssToken($responsiveValue('advancedBorderRadiusHover', '', '0px'), '0'),
		'--pb-advanced-transform:' . $buildTransform('Hover'),
	]);
	if (!empty($accordionSettings['advancedBoxShadowEnabledHover'])) {
		$hoverRules[] = 'box-shadow:' . implode(' ', [
			$cssToken($accordionSettings['advancedBoxShadowXHover'] ?? '0px', '0'), $cssToken($accordionSettings['advancedBoxShadowYHover'] ?? '4px', '4px'),
			$cssToken($accordionSettings['advancedBoxShadowBlurHover'] ?? '16px', '16px'), $cssToken($accordionSettings['advancedBoxShadowSpreadHover'] ?? '0px', '0'),
			trim((string) ($accordionSettings['advancedBoxShadowColorHover'] ?? 'rgba(0,0,0,.2)')) ?: 'rgba(0,0,0,.2)', !empty($accordionSettings['advancedBoxShadowInsetHover']) ? 'inset' : '',
		]);
	}
	$responsiveRules[] = '#' . $nodeDomId . ':hover{' . implode(';', array_filter($hoverRules)) . '}';
	$customCssCode = trim((string) ($accordionSettings['customCssCode'] ?? ''));
	if ($customCssCode !== '') {
		$customCssCode = preg_replace('/@import\b[^;]*;?/i', '', $customCssCode) ?? '';
		$customCssCode = preg_replace('/<\/?style\b[^>]*>/i', '', $customCssCode) ?? '';
		$customCssCode = preg_replace('/(?:javascript\s*:|expression\s*\()/i', '', $customCssCode) ?? '';
		$customCssCode = preg_replace('/\bselector\b/', '#' . $nodeDomId, $customCssCode) ?? '';
		if (trim($customCssCode) !== '') $responsiveRules[] = $customCssCode;
	}
@endphp

<div
	id="{{ $nodeDomId }}"
	class="{{ implode(' ', $rootClasses) }}"
	style="{{ implode(';', $styleVars) }}"
	data-accordion-root="1"
	data-max-expanded="{{ $maxExpanded }}"
	data-animation-duration="{{ $animationDuration }}"
	data-pb-motion="{{ $motionJson }}"
	data-entrance-delay="{{ max(0, (int) ($accordionSettings['entranceDelay'] ?? 0)) }}"
	data-entrance-duration="{{ $accordionSettings['entranceDuration'] ?? 'normal' }}"
	@foreach($customAttributes as $attributeName => $attributeValue)
		{{ $attributeName }}="{{ e($attributeValue) }}"
	@endforeach
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
