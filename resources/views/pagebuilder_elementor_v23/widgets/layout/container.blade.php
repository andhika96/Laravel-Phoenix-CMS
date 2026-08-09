	@php
		$s = $settings;
		$display = $s['displayType'] ?? 'flex';
		$fullMode = $type === 'container_fluid' || in_array(($s['contentWidth'] ?? ''), ['full', 'fluid'], true);
		$backgroundType = strtolower(trim((string) ($s['bgType'] ?? 'none')));
		$safeBackgroundUrl = static function ($value): string {
			return str_replace(['"', "'", '(', ')', '\\'], '', trim((string) $value));
		};
		$videoLink = trim((string) ($s['bgVideoLink'] ?? ''));
		$videoFallback = $safeBackgroundUrl($s['bgVideoFallback'] ?? '');
		$slideshowImages = array_values(array_filter(is_array($s['bgSlideshowImages'] ?? null) ? $s['bgSlideshowImages'] : [], static fn ($image) => is_array($image) && trim((string) ($image['url'] ?? '')) !== ''));
		$slideshowImages = array_map(static function ($image) use ($safeBackgroundUrl) {
			$image['url'] = $safeBackgroundUrl($image['url'] ?? '');
			return $image;
		}, $slideshowImages);
		$videoEmbedUrl = '';
		$videoNativeUrl = '';
		$videoStart = max(0, (float) ($s['bgVideoStart'] ?? 0));
		$videoEnd = max(0, (float) ($s['bgVideoEnd'] ?? 0));
		$videoPlayOnce = filter_var($s['bgVideoPlayOnce'] ?? false, FILTER_VALIDATE_BOOLEAN);
		if ($backgroundType === 'video' && $videoLink !== '') {
			if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~i', $videoLink, $match)) {
				$params = ['autoplay' => '1', 'mute' => '1', 'controls' => '0', 'playsinline' => '1', 'rel' => '0'];
				if ($videoStart > 0) $params['start'] = (string) $videoStart;
				if ($videoEnd > $videoStart) $params['end'] = (string) $videoEnd;
				if (!$videoPlayOnce) { $params['loop'] = '1'; $params['playlist'] = $match[1]; }
				$videoEmbedUrl = 'https://www.' . (filter_var($s['bgVideoPrivacy'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'youtube-nocookie.com' : 'youtube.com') . '/embed/' . $match[1] . '?' . http_build_query($params);
			} elseif (preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $videoLink, $match)) {
				$params = ['autoplay' => '1', 'muted' => '1', 'background' => '1', 'loop' => $videoPlayOnce ? '0' : '1', 'title' => '0', 'byline' => '0', 'portrait' => '0'];
				if (filter_var($s['bgVideoPrivacy'] ?? false, FILTER_VALIDATE_BOOLEAN)) $params['dnt'] = '1';
				$videoEmbedUrl = 'https://player.vimeo.com/video/' . $match[1] . '?' . http_build_query($params) . ($videoStart > 0 ? '#t=' . $videoStart . 's' : '');
			} elseif (preg_match('~^(?:https?:)?//|^/~i', $videoLink)) {
				$videoNativeUrl = $safeBackgroundUrl($videoLink);
			}
		}
		$hasVideoBackground = $backgroundType === 'video' && ($videoEmbedUrl !== '' || $videoNativeUrl !== '');
		$hasSlideshowBackground = $backgroundType === 'slideshow' && count($slideshowImages) > 0;
		$containerWidth = $css_value($s['containerWidth'] ?? null, '100%');
		$styles = [
			'box-sizing:border-box',
			'display:block',
			'width:' . ($fullMode || array_key_exists('containerWidth', $s) ? $containerWidth : '100%'),
		];

		$styles = array_merge($styles, $background_styles($s));
		if ($backgroundType === 'video') {
			$styles[] = 'background-color:' . ($s['bgColor'] ?? '#ffffff');
		}
		if ($backgroundType === 'video' && $videoFallback !== '') {
			$styles[] = 'background-image:url("' . $videoFallback . '")';
			$styles[] = 'background-size:cover';
			$styles[] = 'background-position:center center';
			$styles[] = 'background-repeat:no-repeat';
		}
		if ($backgroundType === 'slideshow' && $hasSlideshowBackground) {
			$styles[] = 'background-image:url("' . $slideshowImages[0]['url'] . '")';
			$styles[] = 'background-size:' . (($s['bgSlideshowSize'] ?? 'cover') === 'default' ? 'cover' : ($s['bgSlideshowSize'] ?? 'cover'));
			$styles[] = 'background-position:' . ($s['bgSlideshowPosition'] ?? 'center center');
			$styles[] = 'background-repeat:no-repeat';
		}
		$styles = array_merge($styles, $border_style_rules($s));

		$styles[] = 'border-radius:' . $border_radius_value($s);
		$styles[] = 'box-shadow:' . $shadow_value($s);
		$styles[] = 'padding-top:' . $css_value($s['paddingTop'] ?? null, '0');
		$styles[] = 'padding-right:' . $css_value($s['paddingRight'] ?? null, '0');
		$styles[] = 'padding-bottom:' . $css_value($s['paddingBottom'] ?? null, '0');
		$styles[] = 'padding-left:' . $css_value($s['paddingLeft'] ?? null, '0');
		$styles[] = 'margin-top:' . $css_space($s['marginTop'] ?? null, '0');
		$styles[] = 'margin-right:' . $css_space($s['marginRight'] ?? null, '0');
		$styles[] = 'margin-bottom:' . $css_space($s['marginBottom'] ?? null, '0');
		$styles[] = 'margin-left:' . $css_space($s['marginLeft'] ?? null, '0');
		$styles[] = 'min-height:' . $css_value($s['minHeight'] ?? null, 'auto');

		if ($fullMode) {
			$styles[] = 'max-width:100%';
		} else {
			$maxWidthCss = $css_value($s['maxWidth'] ?? null, 'auto');
			$styles[] = 'max-width:' . ($maxWidthCss === 'auto' ? 'auto' : 'min(' . $maxWidthCss . ', 100%)');
		}

		if (!empty($s['alignSelf']) && $s['alignSelf'] !== 'auto') {
			$styles[] = 'align-self:' . $s['alignSelf'];
		}
		if (($s['order'] ?? '') !== '' && is_numeric($s['order'])) {
			$styles[] = 'order:' . (int) $s['order'];
		}
		if (($s['sizeMode'] ?? 'default') === 'grow') {
			$styles[] = 'flex:1 1 0';
		} elseif (($s['sizeMode'] ?? 'default') === 'shrink') {
			$styles[] = 'flex:0 1 auto';
		} elseif (($s['sizeMode'] ?? 'default') === 'custom') {
			$customBasis = $css_value(($s['containerWidth'] ?? '') !== '' ? $s['containerWidth'] : ($s['maxWidth'] ?? ''), 'auto');
			$styles[] = 'flex:0 0 ' . $customBasis;
		}
		if (($s['overflow'] ?? '') !== '' && ($s['overflow'] ?? '') !== 'default') {
			$styles[] = 'overflow:' . $s['overflow'];
		}

		$styles = array_merge($styles, $position_rules($s));

		$transform = $transform_value($s);
		if ($transform !== '') {
			$styles[] = 'transform:' . $transform;
		}

		if (($s['zIndex'] ?? '') !== '') {
			if (($s['sticky'] ?? 'none') === 'none' && (($s['position'] ?? 'default') === 'default')) {
				$styles[] = 'position:relative';
			}
			$styles[] = 'z-index:' . $s['zIndex'];
		}

		$hasShapeDivider = $shape_divider_type($s, 'top') !== 'none' || $shape_divider_type($s, 'bottom') !== 'none';
		if (($hasShapeDivider || $hasVideoBackground || $hasSlideshowBackground) && ($s['sticky'] ?? 'none') === 'none' && (($s['position'] ?? 'default') === 'default') && ($s['zIndex'] ?? '') === '') {
			$styles[] = 'position:relative';
		}

		$style = implode(';', array_filter($styles));
		$baseClass = $type === 'container_fluid' ? 'el-layout-container-fluid' : 'el-layout-container';
		$classTokens = array_filter(array_merge([$baseClass, ($hasVideoBackground || $hasSlideshowBackground) ? 'pb-has-background-media' : '', ($hasVideoBackground && !filter_var($s['bgVideoPlayOnMobile'] ?? false, FILTER_VALIDATE_BOOLEAN)) ? 'pb-background-video-mobile-disabled' : '', $normalize_class_tokens($s['cssClass'] ?? '')], $layout_effect_classes($s)));
		$classes = trim(implode(' ', $classTokens));
		$attrBag = $attribute_pairs($s['attributes'] ?? []);
		if (!empty($s['cssId'])) $attrBag['data-css-id'] = (string) $s['cssId'];
		$nodeDomId = 'pb-node-' . ($node['id'] ?? '');
		$rootTag = $resolve_html_tag($s);

		$hasLegacyColumns = array_key_exists('columns', $node);
		$hasCanonicalChildren = array_key_exists('children', $node) && !$hasLegacyColumns;
		$canonicalChildren = $hasCanonicalChildren && is_array($children) ? $children : [];
		$legacyLooseChildren = $hasLegacyColumns && is_array($children) ? $children : [];
		$legacyColumns = $hasLegacyColumns && is_array($columns) ? $columns : [];
		$containerColumns = $legacyColumns;
		if ($legacyLooseChildren) {
			array_unshift($containerColumns, ['id' => 'legacy-loose', 'children' => $legacyLooseChildren]);
		}

		$normalizedColumns = [];
		foreach ($containerColumns as $idx => $col) {
			$c = is_array($col) ? $col : [];
			$colChildren = $c['children'] ?? [];
			$normalizedColumns[] = [
				'id' => $c['id'] ?? ('col-' . ($idx + 1)),
				'flexBasis' => $c['flexBasis'] ?? null,
				'children' => is_array($colChildren) ? $colChildren : [],
			];
		}

		if ($display === 'grid') {
			$gridCols = $s['gridTemplateColumns'] ?? $grid_columns_template($s['gridColumns'] ?? 3);
			$contColumnsStyles = [
				'display:grid',
				'grid-template-columns:' . $gridCols,
				'column-gap:' . $css_value($s['gridColumnGap'] ?? null, '20px'),
				'row-gap:' . $css_value($s['gridRowGap'] ?? null, '20px'),
				'grid-auto-flow:' . ($s['autoFlow'] ?? 'row'),
				'justify-items:' . ($s['gridJustifyItems'] ?? 'stretch'),
				'align-items:' . ($s['gridAlignItems'] ?? 'start'),
				'width:100%',
			];
			$rows = $container_grid_rows_template($s['gridRows'] ?? null);
			if ($rows !== '') {
				$contColumnsStyles[] = 'grid-template-rows:' . $rows;
			}
		} elseif ($display === 'flex') {
			$gap = $css_value($s['gap'] ?? null, '0');
			$direction = $s['direction'] ?? 'row';
			$isRowDir = in_array($direction, ['row', 'row-reverse'], true);
			$requestedAlignItems = $s['alignItems'] ?? 'stretch';
			$contColumnsStyles = [
				'display:flex',
				'flex-direction:' . $direction,
				'flex-wrap:' . ($s['flexWrap'] ?? 'nowrap'),
				'justify-content:' . ($s['justifyContent'] ?? 'flex-start'),
				'align-items:' . ($isRowDir ? 'stretch' : $requestedAlignItems),
				'align-content:' . ($s['alignContent'] ?? 'stretch'),
				'gap:' . $gap,
				'row-gap:' . $css_value($s['flexRowGap'] ?? null, $gap),
				'column-gap:' . $css_value($s['flexColumnGap'] ?? null, $gap),
				'width:100%',
				'min-height:inherit',
				'height:100%',
			];
		} else {
			$contColumnsStyles = ['display:block', 'width:100%'];
		}
		$contColumnsStyle = implode(';', array_filter($contColumnsStyles));

		$tabletRules = array_merge(
			$responsive_side_rules($s, 'Tablet', 'padding', fn ($value) => $css_value($value, '0')),
			$responsive_side_rules($s, 'Tablet', 'margin', fn ($value) => $css_space($value, '0'))
		);
		$mobileRules = array_merge(
			$responsive_side_rules($s, 'Mobile', 'padding', fn ($value) => $css_value($value, '0')),
			$responsive_side_rules($s, 'Mobile', 'margin', fn ($value) => $css_space($value, '0'))
		);
		if (($tabletBorderRadius = $responsive_border_radius_value($s, 'Tablet')) !== null) $tabletRules[] = 'border-radius:' . $tabletBorderRadius;
		if (($mobileBorderRadius = $responsive_border_radius_value($s, 'Mobile')) !== null) $mobileRules[] = 'border-radius:' . $mobileBorderRadius;
		if (($s['minHeightTablet'] ?? '') !== '') $tabletRules[] = 'min-height:' . $css_value($s['minHeightTablet'], 'auto');
		if (($s['minHeightMobile'] ?? '') !== '') $mobileRules[] = 'min-height:' . $css_value($s['minHeightMobile'], 'auto');
		if ($fullMode) {
			if (($s['containerWidthTablet'] ?? '') !== '') $tabletRules[] = 'width:' . $css_value($s['containerWidthTablet'], '100%');
			if (($s['containerWidthMobile'] ?? '') !== '') $mobileRules[] = 'width:' . $css_value($s['containerWidthMobile'], '100%');
		} else {
			if (($s['maxWidthTablet'] ?? '') !== '') $tabletRules[] = 'max-width:' . $css_value($s['maxWidthTablet'], 'auto');
			if (($s['maxWidthMobile'] ?? '') !== '') $mobileRules[] = 'max-width:' . $css_value($s['maxWidthMobile'], 'auto');
		}
		if (($s['alignSelfTablet'] ?? '') !== '') $tabletRules[] = 'align-self:' . $s['alignSelfTablet'];
		if (($s['alignSelfMobile'] ?? '') !== '') $mobileRules[] = 'align-self:' . $s['alignSelfMobile'];
		if (($s['orderTablet'] ?? '') !== '' && is_numeric($s['orderTablet'])) $tabletRules[] = 'order:' . (int) $s['orderTablet'];
		if (($s['orderMobile'] ?? '') !== '' && is_numeric($s['orderMobile'])) $mobileRules[] = 'order:' . (int) $s['orderMobile'];
		if (($s['sizeModeTablet'] ?? '') !== '') {
			if ($s['sizeModeTablet'] === 'grow') {
				$tabletRules[] = 'flex:1 1 0';
			} elseif ($s['sizeModeTablet'] === 'shrink') {
				$tabletRules[] = 'flex:0 1 auto';
			} elseif ($s['sizeModeTablet'] === 'custom') {
				$tabletCustomBasis = $css_value(
					$responsive_setting_value($s, 'containerWidth', 'Tablet', $s['maxWidth'] ?? 'auto')
						?: $responsive_setting_value($s, 'maxWidth', 'Tablet', 'auto'),
					'auto'
				);
				$tabletRules[] = 'flex:0 0 ' . $tabletCustomBasis;
			}
		}
		if (($s['sizeModeMobile'] ?? '') !== '') {
			if ($s['sizeModeMobile'] === 'grow') {
				$mobileRules[] = 'flex:1 1 0';
			} elseif ($s['sizeModeMobile'] === 'shrink') {
				$mobileRules[] = 'flex:0 1 auto';
			} elseif ($s['sizeModeMobile'] === 'custom') {
				$mobileCustomBasis = $css_value(
					$responsive_setting_value($s, 'containerWidth', 'Mobile', $s['maxWidth'] ?? 'auto')
						?: $responsive_setting_value($s, 'maxWidth', 'Mobile', 'auto'),
					'auto'
				);
				$mobileRules[] = 'flex:0 0 ' . $mobileCustomBasis;
			}
		}

		$tabletColsRules = [];
		$mobileColsRules = [];
		if ($display === 'flex') {
			if (($s['directionTablet'] ?? '') !== '') $tabletColsRules[] = 'flex-direction:' . $s['directionTablet'];
			if (($s['directionMobile'] ?? '') !== '') $mobileColsRules[] = 'flex-direction:' . $s['directionMobile'];
			if (($s['justifyContentTablet'] ?? '') !== '') $tabletColsRules[] = 'justify-content:' . $s['justifyContentTablet'];
			if (($s['justifyContentMobile'] ?? '') !== '') $mobileColsRules[] = 'justify-content:' . $s['justifyContentMobile'];
			if (($s['alignItemsTablet'] ?? '') !== '') $tabletColsRules[] = 'align-items:' . $s['alignItemsTablet'];
			if (($s['alignItemsMobile'] ?? '') !== '') $mobileColsRules[] = 'align-items:' . $s['alignItemsMobile'];
			if (($s['alignContentTablet'] ?? '') !== '') $tabletColsRules[] = 'align-content:' . $s['alignContentTablet'];
			if (($s['alignContentMobile'] ?? '') !== '') $mobileColsRules[] = 'align-content:' . $s['alignContentMobile'];
			if (($s['flexWrapTablet'] ?? '') !== '') $tabletColsRules[] = 'flex-wrap:' . $s['flexWrapTablet'];
			if (($s['flexWrapMobile'] ?? '') !== '') $mobileColsRules[] = 'flex-wrap:' . $s['flexWrapMobile'];
			if (($s['flexColumnGapTablet'] ?? '') !== '') $tabletColsRules[] = 'column-gap:' . $css_value($s['flexColumnGapTablet'], '0');
			if (($s['flexColumnGapMobile'] ?? '') !== '') $mobileColsRules[] = 'column-gap:' . $css_value($s['flexColumnGapMobile'], '0');
			if (($s['flexRowGapTablet'] ?? '') !== '') $tabletColsRules[] = 'row-gap:' . $css_value($s['flexRowGapTablet'], '0');
			if (($s['flexRowGapMobile'] ?? '') !== '') $mobileColsRules[] = 'row-gap:' . $css_value($s['flexRowGapMobile'], '0');
		} elseif ($display === 'grid') {
			if (($s['gridColumnGapTablet'] ?? '') !== '') $tabletColsRules[] = 'column-gap:' . $css_value($s['gridColumnGapTablet'], '20px');
			if (($s['gridColumnGapMobile'] ?? '') !== '') $mobileColsRules[] = 'column-gap:' . $css_value($s['gridColumnGapMobile'], '20px');
			if (($s['gridRowGapTablet'] ?? '') !== '') $tabletColsRules[] = 'row-gap:' . $css_value($s['gridRowGapTablet'], '20px');
			if (($s['gridRowGapMobile'] ?? '') !== '') $mobileColsRules[] = 'row-gap:' . $css_value($s['gridRowGapMobile'], '20px');
			if (($s['autoFlowTablet'] ?? '') !== '') $tabletColsRules[] = 'grid-auto-flow:' . $s['autoFlowTablet'];
			if (($s['autoFlowMobile'] ?? '') !== '') $mobileColsRules[] = 'grid-auto-flow:' . $s['autoFlowMobile'];
			if (($s['gridJustifyItemsTablet'] ?? '') !== '') $tabletColsRules[] = 'justify-items:' . $s['gridJustifyItemsTablet'];
			if (($s['gridJustifyItemsMobile'] ?? '') !== '') $mobileColsRules[] = 'justify-items:' . $s['gridJustifyItemsMobile'];
			if (($s['gridAlignItemsTablet'] ?? '') !== '') $tabletColsRules[] = 'align-items:' . $s['gridAlignItemsTablet'];
			if (($s['gridAlignItemsMobile'] ?? '') !== '') $mobileColsRules[] = 'align-items:' . $s['gridAlignItemsMobile'];
			if (($s['gridColumnsTablet'] ?? '') !== '') $tabletColsRules[] = 'grid-template-columns:' . $grid_columns_template($s['gridColumnsTablet']);
			if (($s['gridColumnsMobile'] ?? '') !== '') $mobileColsRules[] = 'grid-template-columns:' . $grid_columns_template($s['gridColumnsMobile']);
			if (($s['gridRowsTablet'] ?? '') !== '') $tabletColsRules[] = 'grid-template-rows:' . ($container_grid_rows_template($s['gridRowsTablet']) ?: 'none');
			if (($s['gridRowsMobile'] ?? '') !== '') $mobileColsRules[] = 'grid-template-rows:' . ($container_grid_rows_template($s['gridRowsMobile']) ?: 'none');
		}
		$styleBlocks = [];
		if ($tabletRules || $tabletColsRules) {
			$css = [];
			if ($tabletRules) $css[] = '#' . $nodeDomId . '{' . implode(';', $tabletRules) . '}';
			if ($tabletColsRules) $css[] = '#' . $nodeDomId . ' > .el-cont-columns{' . implode(';', $tabletColsRules) . '}';
			$styleBlocks[] = '@media (max-width: 1024px){' . implode('', $css) . '}';
		}
		if ($mobileRules || $mobileColsRules) {
			$css = [];
			if ($mobileRules) $css[] = '#' . $nodeDomId . '{' . implode(';', $mobileRules) . '}';
			if ($mobileColsRules) $css[] = '#' . $nodeDomId . ' > .el-cont-columns{' . implode(';', $mobileColsRules) . '}';
			$styleBlocks[] = '@media (max-width: 767px){' . implode('', $css) . '}';
		}

		$hoverStyles = array_merge(
			$background_styles($s, 'Hover'),
			$border_style_rules($s, 'Hover', true)
		);
		$hoverShadowValue = $shadow_value($s, 'Hover', true);
		if ($hoverShadowValue !== null) {
			$hoverStyles[] = 'box-shadow:' . $hoverShadowValue;
		}
		if ($hoverStyles) {
			$transitionDuration = max(0, (int) ($s['bgTransitionDuration'] ?? 300));
			$styleBlocks[] = '#' . $nodeDomId . '{transition:background-color ' . $transitionDuration . 'ms ease, opacity ' . $transitionDuration . 'ms ease, border-color ' . $transitionDuration . 'ms ease, border-width ' . $transitionDuration . 'ms ease, box-shadow ' . $transitionDuration . 'ms ease;}';
			$styleBlocks[] = '#' . $nodeDomId . ':hover{' . $style_with_important($hoverStyles) . '}';
		}

		$topShapeSvg = $shape_divider_svg($s, 'top');
		if ($topShapeSvg !== '') {
			$styleBlocks[] = '#' . $nodeDomId . ' > .pb-shape-divider-top{' . $shape_divider_layer_rule($s, 'top') . '}';
		}
		$bottomShapeSvg = $shape_divider_svg($s, 'bottom');
		if ($bottomShapeSvg !== '') {
			$styleBlocks[] = '#' . $nodeDomId . ' > .pb-shape-divider-bottom{' . $shape_divider_layer_rule($s, 'bottom') . '}';
		}
		if ($hasSlideshowBackground) {
			$slideDuration = max(1000, (int) ($s['bgSlideshowDuration'] ?? 5000));
			$slideTransitionDuration = max(100, (int) ($s['bgSlideshowTransitionDuration'] ?? 500));
			$slideCount = count($slideshowImages);
			$animationName = 'pb-container-slideshow-' . preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($node['id'] ?? 'item'));
			$cycleDuration = $slideDuration * $slideCount;
			$fadeEnd = min(49, max(1, ($slideTransitionDuration / $slideDuration) * 100));
			$visibleEnd = min(99, max($fadeEnd + 1, 100 - $fadeEnd));
			$transition = $s['bgSlideshowTransition'] ?? 'fade';
			$kenBurns = filter_var($s['bgSlideshowKenBurns'] ?? false, FILTER_VALIDATE_BOOLEAN);
			$fromTransform = $transition === 'slide-right' ? 'translate3d(100%,0,0)' : ($transition === 'slide-left' ? 'translate3d(-100%,0,0)' : ($transition === 'slide-up' ? 'translate3d(0,100%,0)' : ($transition === 'slide-down' ? 'translate3d(0,-100%,0)' : 'translate3d(0,0,0)')));
			$toTransform = $transition === 'slide-right' ? 'translate3d(-100%,0,0)' : ($transition === 'slide-left' ? 'translate3d(100%,0,0)' : ($transition === 'slide-up' ? 'translate3d(0,-100%,0)' : ($transition === 'slide-down' ? 'translate3d(0,100%,0)' : 'translate3d(0,0,0)')));
			$centerTransform = $kenBurns ? 'translate3d(0,0,0) scale(1.08)' : 'translate3d(0,0,0)';
			$styleBlocks[] = '@keyframes ' . $animationName . '{0%{opacity:0;transform:' . $fromTransform . '} ' . $fadeEnd . '%{opacity:1;transform:' . $centerTransform . '} ' . $visibleEnd . '%{opacity:1;transform:' . $centerTransform . '} 100%{opacity:0;transform:' . $toTransform . '}}';
			foreach (['Tablet' => 1024, 'Mobile' => 767] as $suffix => $breakpoint) {
				$size = $s['bgSlideshowSize' . $suffix] ?? '';
				$position = $s['bgSlideshowPosition' . $suffix] ?? '';
				$rules = [];
				if ($size !== '') $rules[] = 'background-size:' . ($size === 'default' ? 'cover' : $size);
				if ($position !== '') $rules[] = 'background-position:' . $position;
				if ($rules) $styleBlocks[] = '@media (max-width: ' . $breakpoint . 'px){#' . $nodeDomId . ' .pb-container-background-slideshow__slide{' . implode(';', $rules) . '}}';
			}
		}

		$customScopedCss = $scoped_css($node['id'] ?? null, $s['customCssCode'] ?? '');
		if ($customScopedCss !== '') $styleBlocks[] = $customScopedCss;
	@endphp
	<{{ $rootTag }} id="{{ $nodeDomId }}" class="{{ $classes }}" style="{{ $style }}"
		@foreach($attrBag as $attrName => $attrValue)
			{{ $attrName }}="{{ e($attrValue) }}"
		@endforeach
	>
		@if($hasVideoBackground)
			<div class="pb-container-background-media pb-container-background-video" aria-hidden="true">
				@if($videoEmbedUrl !== '')
					<iframe src="{{ e($videoEmbedUrl) }}" title="Background video" tabindex="-1" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
				@else
					<video src="{{ e($videoNativeUrl) }}" @if($videoFallback !== '') poster="{{ e($videoFallback) }}" @endif autoplay muted playsinline @if(!$videoPlayOnce) loop @endif data-pb-video-start="{{ $videoStart }}" data-pb-video-end="{{ $videoEnd }}" onloadedmetadata="if(this.dataset.pbVideoStart){this.currentTime=Number(this.dataset.pbVideoStart)||0}" ontimeupdate="var e=Number(this.dataset.pbVideoEnd||0);if(e&&this.currentTime>=e){if(this.loop){this.currentTime=Number(this.dataset.pbVideoStart)||0}else{this.pause()}}"></video>
				@endif
			</div>
		@endif
		@if($hasSlideshowBackground)
			@php $slideshowLazyload = filter_var($s['bgSlideshowLazyload'] ?? false, FILTER_VALIDATE_BOOLEAN); @endphp
			<div class="pb-container-background-media pb-container-background-slideshow pb-container-background-slideshow--{{ e($s['bgSlideshowTransition'] ?? 'fade') }}" aria-hidden="true">
				@foreach($slideshowImages as $index => $image)
					<span class="pb-container-background-slideshow__slide" @if($slideshowLazyload) data-pb-slideshow-image="{{ e($image['url']) }}" @endif style="@unless($slideshowLazyload)background-image:url(&quot;{{ e($image['url']) }}&quot;);@endunless background-size:{{ e(($s['bgSlideshowSize'] ?? 'cover') === 'default' ? 'cover' : ($s['bgSlideshowSize'] ?? 'cover')) }};background-position:{{ e($s['bgSlideshowPosition'] ?? 'center center') }};animation:{{ $animationName }} {{ $cycleDuration }}ms linear {{ filter_var($s['bgSlideshowInfiniteLoop'] ?? true, FILTER_VALIDATE_BOOLEAN) ? 'infinite' : '1' }};animation-delay:-{{ $index * $slideDuration }}ms;animation-fill-mode:both"></span>
				@endforeach
			</div>
			@if($slideshowLazyload)
				<script>(function(){var root=document.getElementById(@json($nodeDomId));if(!root)return;var slides=Array.prototype.slice.call(root.querySelectorAll('.pb-container-background-slideshow__slide'));if(!slides.length)return;var load=function(index){var slide=slides[index];if(slide&&slide.dataset.pbSlideshowImage&&!slide.style.backgroundImage)slide.style.backgroundImage='url("'+slide.dataset.pbSlideshowImage.replace(/"/g,'\\"')+'")';};var current=0;load(current);load((current+1)%slides.length);window.setInterval(function(){current=(current+1)%slides.length;load((current+1)%slides.length);},{{ $slideDuration }});}());</script>
			@endif
		@endif
		@if($topShapeSvg !== '')
			<div class="pb-shape-divider-layer pb-shape-divider-top" aria-hidden="true">{!! $topShapeSvg !!}</div>
		@endif
		@if($bottomShapeSvg !== '')
			<div class="pb-shape-divider-layer pb-shape-divider-bottom" aria-hidden="true">{!! $bottomShapeSvg !!}</div>
		@endif
		<div class="el-cont-columns" style="{{ $contColumnsStyle }}">
			@if($hasCanonicalChildren)
				@foreach($canonicalChildren as $child)
					@include('pagebuilder_elementor_v23.partials.render_node', ['node' => $child])
				@endforeach
			@else
			@foreach($normalizedColumns as $col)
				@php
					$colStyles = [];
					$colChildren = is_array($col['children'] ?? null) ? $col['children'] : [];
					$childCount = count($colChildren);
					$childWrapperStyles = [];
					if ($display === 'flex') {
						$rawBasis = trim((string) ($col['flexBasis'] ?? ''));
						$hasBasis = $rawBasis !== '';
						$dir = $s['direction'] ?? 'row';
						$isColumnDir = in_array($dir, ['column', 'column-reverse'], true);
						$isRowDir = in_array($dir, ['row', 'row-reverse'], true);
						$wrapMode = $s['flexWrap'] ?? 'nowrap';
						$isWrapMode = in_array($wrapMode, ['wrap', 'wrap-reverse'], true);
						$flexAlignItems = strtolower(trim((string) ($s['alignItems'] ?? 'flex-start')));
						$flexJustifyMap = [
							'flex-start' => 'flex-start',
							'center' => 'center',
							'flex-end' => 'flex-end',
							'stretch' => 'flex-start',
						];

						if ($hasBasis) {
							if ($isColumnDir) {
								$colStyles = [
									'flex:0 0 ' . $rawBasis,
									'height:' . $rawBasis,
									'min-height:' . $rawBasis,
									'width:100%',
									'min-width:0',
									'box-sizing:border-box',
								];
							} else {
								$percentBasis = null;
								$basisToken = $rawBasis;
								if (preg_match('/^(\d+(?:\.\d+)?)%$/', $rawBasis, $basisMatches)) {
									$numericPercent = (float) $basisMatches[1];
									if ($numericPercent > 0) {
										$percentBasis = $numericPercent;
										$percentText = rtrim(rtrim(sprintf('%.4F', $numericPercent), '0'), '.');
										$basisToken = $percentText . '%';
										$baseGapToken = $css_value($s['gap'] ?? null, '0');
										$columnGapToken = $css_value($s['flexColumnGap'] ?? null, $baseGapToken);
										if (preg_match('/^(\d+(?:\.\d+)?)px$/i', $columnGapToken, $gapMatches)) {
											$totalGapPx = (float) $gapMatches[1] * max(0, count($normalizedColumns) - 1);
											$gapSharePx = round($totalGapPx * ($numericPercent / 100), 3);
											if ($gapSharePx > 0) {
												$gapShareText = rtrim(rtrim(sprintf('%.3F', $gapSharePx), '0'), '.');
												$basisToken = 'calc(' . $percentText . '% - ' . $gapShareText . 'px)';
											}
										}
									}
								}

								if ($percentBasis !== null) {
									$colStyles = [
										'flex:0 0 ' . $basisToken,
										'flex-basis:' . $basisToken,
										'width:' . $basisToken,
										'min-width:' . ($isWrapMode ? '220px' : '0'),
										'max-width:100%',
										'height:auto',
										'align-self:stretch',
										'display:flex',
										'flex-direction:column',
										'box-sizing:border-box',
									];
								} else {
									$colStyles = [
										'flex:0 0 ' . $rawBasis,
										'width:' . $rawBasis,
										'min-width:' . $rawBasis,
										'max-width:' . $rawBasis,
										'height:auto',
										'align-self:stretch',
										'display:flex',
										'flex-direction:column',
										'box-sizing:border-box',
									];
								}
							}
						} elseif ($isColumnDir) {
							$colStyles = [
								'flex:0 0 auto',
								'width:100%',
								'min-width:0',
								'min-height:88px',
								'box-sizing:border-box',
							];
						} else {
							$colStyles = [
								'flex:1 1 0',
								'width:auto',
								'min-width:' . ($isWrapMode ? '220px' : '0'),
								'height:auto',
								'align-self:stretch',
								'display:flex',
								'flex-direction:column',
								'box-sizing:border-box',
							];
						}
						if ($isRowDir && $childCount > 0) {
							$colStyles[] = 'justify-content:' . ($flexJustifyMap[$flexAlignItems] ?? 'flex-start');
						}
					} elseif ($display === 'grid') {
						$colStyles = [
							'display:flex',
							'flex-direction:column',
							'min-width:0',
							'max-width:100%',
							'box-sizing:border-box',
						];
						if ($childCount > 0) {
							$alignItems = strtolower(trim((string) ($s['gridAlignItems'] ?? 'start')));
							$mainAxisMap = [
								'start' => 'flex-start',
								'center' => 'center',
								'end' => 'flex-end',
								'stretch' => 'flex-start',
							];
							$colStyles[] = 'justify-content:' . ($mainAxisMap[$alignItems] ?? 'flex-start');
							$colStyles[] = 'min-height:68px';
						}
					}
					$colStyle = implode(';', array_filter($colStyles));
				@endphp
				<div class="el-grid-col" style="{{ $colStyle }}">
					@foreach($colChildren as $child)
						@if($display === 'grid')
							@php
								$childType = strtolower(trim((string) ($child['type'] ?? '')));
								$isLayoutChild = in_array($childType, ['container', 'container_fluid', 'grid', 'row_grid'], true);
								$isWidgetChild = $childType !== '' && !$isLayoutChild;
								$justifyItems = strtolower(trim((string) ($s['gridJustifyItems'] ?? 'stretch')));
								$alignItems = strtolower(trim((string) ($s['gridAlignItems'] ?? 'start')));
								$childWrapperStyles = [
									'max-width:100%',
									'min-width:0',
									'margin-top:0',
									'margin-bottom:0',
								];

								if ($isWidgetChild) {
									$childWrapperStyles[] = 'width:100%';
									$childWrapperStyles[] = 'margin-left:0';
									$childWrapperStyles[] = 'margin-right:0';

									if (in_array($childType, ['heading', 'button'], true)) {
										$justifyMap = [
											'start' => 'flex-start',
											'center' => 'center',
											'end' => 'flex-end',
											'stretch' => 'flex-start',
										];
										$childWrapperStyles[] = 'display:flex';
										$childWrapperStyles[] = 'justify-content:' . ($justifyMap[$justifyItems] ?? 'flex-start');
									}
								} else {
									if ($justifyItems === 'center') {
										$childWrapperStyles[] = 'width:fit-content';
										$childWrapperStyles[] = 'margin-left:auto';
										$childWrapperStyles[] = 'margin-right:auto';
									} elseif ($justifyItems === 'end') {
										$childWrapperStyles[] = 'width:fit-content';
										$childWrapperStyles[] = 'margin-left:auto';
										$childWrapperStyles[] = 'margin-right:0';
									} elseif ($justifyItems === 'start') {
										$childWrapperStyles[] = 'width:fit-content';
										$childWrapperStyles[] = 'margin-left:0';
										$childWrapperStyles[] = 'margin-right:auto';
									} else {
										$childWrapperStyles[] = 'width:100%';
										$childWrapperStyles[] = 'margin-left:0';
										$childWrapperStyles[] = 'margin-right:0';
									}
								}

								if ($childCount <= 1) {
									if ($alignItems === 'center') {
										$childWrapperStyles[] = 'margin-top:auto';
										$childWrapperStyles[] = 'margin-bottom:auto';
									} elseif ($alignItems === 'end') {
										$childWrapperStyles[] = 'margin-top:auto';
										$childWrapperStyles[] = 'margin-bottom:0';
									}
								}

								$childWrapperStyle = implode(';', array_filter($childWrapperStyles));
							@endphp
							<div style="{{ $childWrapperStyle }}">
								@include('pagebuilder_elementor_v23.partials.render_node', ['node' => $child])
							</div>
						@elseif($display === 'flex' && $isRowDir)
							@php
								$childType = strtolower(trim((string) ($child['type'] ?? '')));
								$isLayoutChild = in_array($childType, ['container', 'container_fluid', 'grid', 'row_grid'], true);
								$isWidgetChild = $childType !== '' && !$isLayoutChild;
								$justifyContent = strtolower(trim((string) ($s['justifyContent'] ?? 'flex-start')));
								$childWrapperClasses = ['el-flex-row-justify-item'];
								$childWrapperStyles = [
									'width:100%',
									'max-width:100%',
									'min-width:0',
									'margin-top:0',
									'margin-bottom:0',
								];

								if ($isWidgetChild) {
									$childWrapperStyles[] = 'margin-left:0';
									$childWrapperStyles[] = 'margin-right:0';

									if (in_array($justifyContent, ['flex-start', 'center', 'flex-end'], true)) {
										$childWrapperStyles[] = 'display:flex';
										$childWrapperStyles[] = 'justify-content:' . $justifyContent;
										if (in_array($childType, ['heading', 'button'], true)) {
											$childWrapperClasses[] = 'is-shrink';
										}
									}
								}

								$childWrapperStyle = implode(';', array_filter($childWrapperStyles));
							@endphp
							<div class="{{ implode(' ', array_filter($childWrapperClasses)) }}" style="{{ $childWrapperStyle }}">
								@include('pagebuilder_elementor_v23.partials.render_node', ['node' => $child])
							</div>
						@else
							@include('pagebuilder_elementor_v23.partials.render_node', ['node' => $child])
						@endif
					@endforeach
				</div>
			@endforeach
			@endif
		</div>
	</{{ $rootTag }}>
	@if($styleBlocks)
		<style>{!! implode("\n", $styleBlocks) !!}</style>
	@endif
