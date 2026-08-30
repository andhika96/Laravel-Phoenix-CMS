	@php
	$s = $settings;
	$__pbLayoutAdvanced = app(\App\Support\PageBuilderElementorV24\WidgetAdvancedStyleResolver::class)->resolve($s, (string) ($node['id'] ?? ''));
		$wrapStyles = ['width:100%', 'box-sizing:border-box'];
		if ($type === 'grid') {
			$wrapStyles[] = 'flex:1 1 100%';
			$wrapStyles[] = 'min-width:0';
		}
		$wrapStyles = array_merge($wrapStyles, $background_styles($s));

		if (!empty($s['borderType']) && $s['borderType'] !== 'none') {
			$wrapStyles[] = 'border:' . $css_value($s['borderWidth'] ?? null, '1px') . ' ' . $s['borderType'] . ' ' . ($s['borderColor'] ?? '#000000');
		}

		$wrapStyles[] = 'border-radius:' . $border_radius_value($s);
		$wrapStyles[] = 'box-shadow:' . $shadow_value($s);
		$wrapStyles[] = 'padding-top:' . $css_value($s['paddingTop'] ?? null, '0');
		$wrapStyles[] = 'padding-right:' . $css_value($s['paddingRight'] ?? null, '0');
		$wrapStyles[] = 'padding-bottom:' . $css_value($s['paddingBottom'] ?? null, '0');
		$wrapStyles[] = 'padding-left:' . $css_value($s['paddingLeft'] ?? null, '0');
		$wrapStyles[] = 'margin-top:' . $css_space($s['marginTop'] ?? null, '0');
		$wrapStyles[] = 'margin-right:' . $css_space($s['marginRight'] ?? null, '0');
		$wrapStyles[] = 'margin-bottom:' . $css_space($s['marginBottom'] ?? null, '0');
		$wrapStyles[] = 'margin-left:' . $css_space($s['marginLeft'] ?? null, '0');
		if (($s['overflow'] ?? '') !== '') {
			$wrapStyles[] = 'overflow:' . ($s['overflow'] ?? 'visible');
		}
		$wrapStyles = array_merge($wrapStyles, $position_rules($s));

		$transform = $transform_value($s);
		if ($transform !== '') {
			$wrapStyles[] = 'transform:' . $transform;
		}

		if (($s['zIndex'] ?? '') !== '') {
			if (($s['sticky'] ?? 'none') === 'none' && (($s['position'] ?? 'default') === 'default')) {
				$wrapStyles[] = 'position:relative';
			}
			$wrapStyles[] = 'z-index:' . $s['zIndex'];
		}

		$colCount = max(1, min(12, (int) ($s['columns'] ?? 3)));
		$columns = is_array($columns ?? null) ? $columns : [];
		$columnStyles = is_array($node['columnStyles'] ?? null) ? $node['columnStyles'] : [];
		$gridCols = $s['gridTemplateColumns'] ?? $grid_columns_template($colCount);
		$gridStyles = [
			'display:grid',
			'grid-template-columns:' . $gridCols,
			'column-gap:' . $css_value($s['columnGap'] ?? null, '20px'),
			'row-gap:' . $css_value($s['rowGap'] ?? null, '20px'),
			'grid-auto-flow:' . ($s['autoFlow'] ?? 'row'),
			'width:100%',
			'box-sizing:border-box',
			'align-items:stretch',
		];

		$rows = $grid_rows_template($s['gridRows'] ?? null);
		if ($rows !== '') {
			$gridStyles[] = 'grid-template-rows:' . $rows;
		}

		if (array_key_exists('gridAutoHeight', $s) && empty($s['gridAutoHeight'])) {
			$gridStyles[] = 'grid-auto-rows:1fr';
		}

		$baseGridClass = ($type === 'grid' ? 'el-layout-grid' : 'el-layout-row-grid');
		$classTokens = array_filter(array_merge([$baseGridClass, $normalize_class_tokens($s['cssClass'] ?? '')], $layout_effect_classes($s)));
		$wrapClass = trim(implode(' ', $classTokens));
		$wrapStyle = implode(';', array_filter($wrapStyles));
		$gridStyle = implode(';', array_filter($gridStyles));
	$attrBag = $attribute_pairs($s['attributes'] ?? []);
	if (!empty($s['cssId'])) $attrBag['data-css-id'] = (string) $s['cssId'];
	if (isset($__pbLayoutAdvanced['attributes']['data-pb-import-node'])) $attrBag['data-pb-import-node'] = $__pbLayoutAdvanced['attributes']['data-pb-import-node'];
		$nodeDomId = 'pb-node-' . ($node['id'] ?? '');
		$cellRenderStyles = [];
		$columnMediaRules = ['tablet' => [], 'mobile' => []];
		foreach ($columns as $cellIndex => $col) {
			$trackIndex = ((int) $cellIndex) % $colCount;
			$trackStyle = $columnStyles[$trackIndex] ?? [];
			$cellOverrides = is_array($col['styleOverrides'] ?? null) ? $col['styleOverrides'] : [];
			$cellRenderStyles[$cellIndex] = $grid_column_style_css($grid_column_style_resolve($trackStyle, $cellOverrides));
			if (!$grid_column_style_has_responsive_values($trackStyle) && !$grid_column_style_has_responsive_values($cellOverrides)) continue;
			$cellId = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($col['id'] ?? '')) ?: ('cell-' . $cellIndex);
			$selector = '#' . $nodeDomId . ' > .el-grid-columns > .el-grid-col[data-pb-grid-cell="' . $cellId . '"]';
			$columnMediaRules['tablet'][] = $selector . '{' . $grid_column_style_css($grid_column_style_resolve($trackStyle, $cellOverrides, 'Tablet')) . '}';
			$columnMediaRules['mobile'][] = $selector . '{' . $grid_column_style_css($grid_column_style_resolve($trackStyle, $cellOverrides, 'Mobile')) . '}';
		}

		$tabletRootRules = array_merge(
			$responsive_side_rules($s, 'Tablet', 'padding', fn ($value) => $css_value($value, '0')),
			$responsive_side_rules($s, 'Tablet', 'margin', fn ($value) => $css_space($value, '0'))
		);
		$mobileRootRules = array_merge(
			$responsive_side_rules($s, 'Mobile', 'padding', fn ($value) => $css_value($value, '0')),
			$responsive_side_rules($s, 'Mobile', 'margin', fn ($value) => $css_space($value, '0'))
		);
		if (($tabletBorderRadius = $responsive_border_radius_value($s, 'Tablet')) !== null) $tabletRootRules[] = 'border-radius:' . $tabletBorderRadius;
		if (($mobileBorderRadius = $responsive_border_radius_value($s, 'Mobile')) !== null) $mobileRootRules[] = 'border-radius:' . $mobileBorderRadius;
		$tabletGridRules = [];
		$mobileGridRules = [];

		if (($s['columnsTablet'] ?? '') !== '') $tabletGridRules[] = 'grid-template-columns:' . $grid_columns_template($s['columnsTablet']);
		if (($s['gridRowsTablet'] ?? '') !== '') $tabletGridRules[] = 'grid-template-rows:' . ($grid_rows_template($s['gridRowsTablet']) ?: 'none');
		if (($s['columnGapTablet'] ?? '') !== '') $tabletGridRules[] = 'column-gap:' . $css_value($s['columnGapTablet'], '20px');
		if (($s['rowGapTablet'] ?? '') !== '') $tabletGridRules[] = 'row-gap:' . $css_value($s['rowGapTablet'], '20px');

		if (($s['columnsMobile'] ?? '') !== '') $mobileGridRules[] = 'grid-template-columns:' . $grid_columns_template($s['columnsMobile']);
		if (($s['gridRowsMobile'] ?? '') !== '') $mobileGridRules[] = 'grid-template-rows:' . ($grid_rows_template($s['gridRowsMobile']) ?: 'none');
		if (($s['columnGapMobile'] ?? '') !== '') $mobileGridRules[] = 'column-gap:' . $css_value($s['columnGapMobile'], '20px');
		if (($s['rowGapMobile'] ?? '') !== '') $mobileGridRules[] = 'row-gap:' . $css_value($s['rowGapMobile'], '20px');

		$styleBlocks = [];
		if ($tabletRootRules || $tabletGridRules) {
			$css = [];
			if ($tabletRootRules) $css[] = '#' . $nodeDomId . '{' . implode(';', $tabletRootRules) . '}';
			if ($tabletGridRules) $css[] = '#' . $nodeDomId . ' > .el-grid-columns{' . implode(';', $tabletGridRules) . '}';
			$styleBlocks[] = '@media (max-width: 1024px){' . implode('', $css) . '}';
		}
		if ($mobileRootRules || $mobileGridRules) {
			$css = [];
			if ($mobileRootRules) $css[] = '#' . $nodeDomId . '{' . implode(';', $mobileRootRules) . '}';
			if ($mobileGridRules) $css[] = '#' . $nodeDomId . ' > .el-grid-columns{' . implode(';', $mobileGridRules) . '}';
			$styleBlocks[] = '@media (max-width: 767px){' . implode('', $css) . '}';
		}
		if ($columnMediaRules['tablet']) $styleBlocks[] = '@media (max-width: 1024px){' . implode('', $columnMediaRules['tablet']) . '}';
		if ($columnMediaRules['mobile']) $styleBlocks[] = '@media (max-width: 767px){' . implode('', $columnMediaRules['mobile']) . '}';
		$customScopedCss = $scoped_css($node['id'] ?? null, $s['customCssCode'] ?? '');
		if ($customScopedCss !== '') $styleBlocks[] = $customScopedCss;
	@endphp
	<div id="{{ $nodeDomId }}" class="{{ $wrapClass }}" style="{{ $wrapStyle }}"
		data-pb-motion="{{ $__pbLayoutAdvanced['motion'] }}" data-entrance-delay="{{ $__pbLayoutAdvanced['entranceDelay'] }}" data-entrance-duration="{{ $__pbLayoutAdvanced['entranceDuration'] }}"
		@foreach($attrBag as $attrName => $attrValue)
			{{ $attrName }}="{{ e($attrValue) }}"
		@endforeach
	>
		<div class="el-grid-columns" style="{{ $gridStyle }}">
			@foreach($columns as $cellIndex => $col)
				@php $cellId = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($col['id'] ?? '')) ?: ('cell-' . $cellIndex); @endphp
				<div class="el-grid-col" data-pb-grid-track="{{ $cellIndex % $colCount }}" data-pb-grid-cell="{{ $cellId }}" style="{{ $cellRenderStyles[$cellIndex] ?? '' }}">
					@foreach(($col['children'] ?? []) as $child)
						@include('pagebuilder_elementor_v24.partials.render_node', ['node' => $child])
					@endforeach
				</div>
			@endforeach
		</div>
	</div>
	@if($styleBlocks)
		<style>{!! implode("\n", $styleBlocks) !!}</style>
	@endif
