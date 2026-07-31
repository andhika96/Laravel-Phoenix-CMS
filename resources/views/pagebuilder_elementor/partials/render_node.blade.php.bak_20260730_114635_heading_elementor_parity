@php
	$type = $node['type'] ?? '';
	$settings = $node['settings'] ?? [];
	$children = $node['children'] ?? [];
	$columns = $node['columns'] ?? [];

	$__pbRequest = request();
	$__pbUser = $__pbRequest->user();
	$__pbDynamicContext = $__pbRequest->attributes->get('pagebuilder_dynamic_context', []);
	$__pbDynamicContext = is_array($__pbDynamicContext) ? $__pbDynamicContext : [];
	if (!array_key_exists('page', $__pbDynamicContext) && isset($pageData)) {
		$__pbDynamicContext['page'] = $pageData;
	}
	$__pbDynamicContext['page_url'] ??= url()->current();
	$__pbDynamicContext['site_title'] ??= config('app.name');
	$__pbDynamicContext['site_url'] ??= config('app.url');
	$__pbDynamicContext['user'] ??= $__pbUser;
	$__pbRequest->attributes->set('pagebuilder_dynamic_context', $__pbDynamicContext);
	$__pbConditionGroups = is_array($settings['displayConditions'] ?? null) ? $settings['displayConditions'] : [];
	$__pbConditionEvaluator = app(\App\Support\PageBuilderElementor\WidgetDisplayConditionEvaluator::class);
	if (!$__pbConditionEvaluator->allows($__pbConditionGroups, $__pbRequest, $__pbUser)) {
		return;
	}

	if (in_array($type, ['accordion', 'image_box'], true) && ($settings['cacheMode'] ?? 'default') === 'active') {
		$__pbRoles = [];
		if ($__pbUser && method_exists($__pbUser, 'getRoleNames')) {
			$__pbRoles = collect($__pbUser->getRoleNames())->map(fn ($role) => (string) $role)->all();
		} elseif ($__pbUser && isset($__pbUser->role)) {
			$__pbRoles = [(string) $__pbUser->role];
		}
		$__pbContext = [
			'page_id' => $__pbRequest->attributes->get('pagebuilder_page_id') ?? $__pbRequest->route('page') ?? $__pbRequest->route('id'),
			'page_slug' => $__pbRequest->attributes->get('pagebuilder_page_slug') ?? $__pbRequest->route('slug') ?? basename(trim($__pbRequest->path(), '/')),
			'auth' => $__pbUser ? 'authenticated' : 'guest',
			'roles' => $__pbRoles,
			'user_id' => $__pbUser && method_exists($__pbUser, 'getAuthIdentifier') ? $__pbUser->getAuthIdentifier() : ($__pbUser->id ?? null),
		];
		$__pbFragmentCache = app(\App\Support\PageBuilderElementor\WidgetFragmentCache::class);
		$__pbFragmentView = $type === 'image_box'
			? 'pagebuilder_elementor.partials.render_image_box'
			: 'pagebuilder_elementor.partials.render_accordion';
		echo $__pbFragmentCache->remember($node, $__pbContext, fn () => view($__pbFragmentView, ['node' => $node])->render());
		return;
	}


	$css_value = function ($value, $fallback = '') {
		if ($value === null || $value === '') {
			return $fallback;
		}

		if (is_numeric($value)) {
			return (float) $value === 0.0 ? '0' : $value . 'px';
		}

		$out = trim((string) $value);

		if ($out === '') {
			return $fallback;
		}

		if (preg_match('/^-?\d+(\.\d+)?$/', $out)) {
			return $out === '0' ? '0' : $out . 'px';
		}

		return $out;
	};

	$css_space = function ($value, $fallback = '0') use ($css_value) {
		$out = trim((string) ($value ?? ''));

		if ($out === '') {
			return $fallback;
		}

		if (strtolower($out) === 'auto') {
			return 'auto';
		}

		return $css_value($out, $fallback);
	};

	$is_truthy = function ($value) {
		return in_array($value, [true, 'true', 1, '1'], true);
	};

	$normalize_class_tokens = function ($value) {
		$raw = trim((string) ($value ?? ''));

		if ($raw === '') {
			return '';
		}

		$tokens = preg_split('/\s+/', $raw) ?: [];
		$tokens = array_values(array_filter(array_map(function ($token) {
			$token = trim((string) $token);
			$token = preg_replace('/^\.+/', '', $token);

			return $token === '' ? null : $token;
		}, $tokens)));

		return implode(' ', $tokens);
	};

	$state_setting = function (array $s, string $base, string $suffix = '') {
		$key = $base . $suffix;

		if (array_key_exists($key, $s) && $s[$key] !== null && $s[$key] !== '') {
			return $s[$key];
		}

		return $s[$base] ?? null;
	};

	$transform_value = function (array $s) use ($css_space) {
		$chunks = [];
		$offsetX = $css_space($s['transformOffsetX'] ?? null, '');
		$offsetY = $css_space($s['transformOffsetY'] ?? null, '');
		$rotate = $css_space($s['transformRotate'] ?? null, '');
		$scaleX = trim((string) ($s['transformScaleX'] ?? ''));
		$scaleY = trim((string) ($s['transformScaleY'] ?? ''));
		$skewX = $css_space($s['transformSkewX'] ?? null, '');
		$skewY = $css_space($s['transformSkewY'] ?? null, '');

		if ($offsetX !== '' || $offsetY !== '') {
			$chunks[] = 'translate(' . ($offsetX !== '' ? $offsetX : '0') . ', ' . ($offsetY !== '' ? $offsetY : '0') . ')';
		}
		if ($rotate !== '') {
			$chunks[] = 'rotate(' . $rotate . ')';
		}
		if ($scaleX !== '' || $scaleY !== '') {
			$chunks[] = 'scale(' . ($scaleX !== '' ? $scaleX : '1') . ', ' . ($scaleY !== '' ? $scaleY : '1') . ')';
		}
		if ($skewX !== '' || $skewY !== '') {
			$chunks[] = 'skew(' . ($skewX !== '' ? $skewX : '0') . ', ' . ($skewY !== '' ? $skewY : '0') . ')';
		}

		return implode(' ', $chunks);
	};

	$position_rules = function (array $s) use ($css_space) {
		$rules = [];
		$sticky = $s['sticky'] ?? 'none';
		$position = $s['position'] ?? 'default';
		$stickyOffset = $css_space($s['stickyOffset'] ?? null, '0');

		if ($sticky !== 'none') {
			$rules[] = 'position:sticky';
			if ($sticky === 'top') $rules[] = 'top:' . ($stickyOffset !== '' ? $stickyOffset : '0');
			if ($sticky === 'bottom') $rules[] = 'bottom:' . ($stickyOffset !== '' ? $stickyOffset : '0');
		} elseif ($position !== 'default') {
			$rules[] = 'position:' . $position;
		}

		if (($s['positionTop'] ?? '') !== '') $rules[] = 'top:' . $css_space($s['positionTop'], 'auto');
		if (($s['positionRight'] ?? '') !== '') $rules[] = 'right:' . $css_space($s['positionRight'], 'auto');
		if (($s['positionBottom'] ?? '') !== '') $rules[] = 'bottom:' . $css_space($s['positionBottom'], 'auto');
		if (($s['positionLeft'] ?? '') !== '') $rules[] = 'left:' . $css_space($s['positionLeft'], 'auto');

		return $rules;
	};

	$layout_effect_classes = function (array $s) use ($is_truthy) {
		$classes = [];
		if ($is_truthy($s['hideDesktop'] ?? false)) $classes[] = 'pb-hide-desktop';
		if ($is_truthy($s['hideTablet'] ?? false)) $classes[] = 'pb-hide-tablet';
		if ($is_truthy($s['hideMobile'] ?? false)) $classes[] = 'pb-hide-mobile';
		if (!empty($s['entranceAnimation'])) {
			$classes[] = 'pb-entrance-anim';
			$classes[] = 'pb-anim-' . trim((string) $s['entranceAnimation']);
		}
		if ($is_truthy($s['scrollingEffects'] ?? false)) {
			$classes[] = 'pb-motion-scroll';
			if (!$is_truthy($s['scrollApplyDesktop'] ?? true)) $classes[] = 'pb-scroll-off-desktop';
			if (!$is_truthy($s['scrollApplyTablet'] ?? true)) $classes[] = 'pb-scroll-off-tablet';
			if (!$is_truthy($s['scrollApplyMobile'] ?? true)) $classes[] = 'pb-scroll-off-mobile';
		}
		if ($is_truthy($s['mouseEffects'] ?? false)) {
			$classes[] = 'pb-motion-mouse';
			if (!$is_truthy($s['mouseApplyDesktop'] ?? true)) $classes[] = 'pb-mouse-off-desktop';
			if (!$is_truthy($s['mouseApplyTablet'] ?? true)) $classes[] = 'pb-mouse-off-tablet';
			if (!$is_truthy($s['mouseApplyMobile'] ?? true)) $classes[] = 'pb-mouse-off-mobile';
		}
		if (($s['sticky'] ?? 'none') !== 'none') {
			if (!$is_truthy($s['stickyOnDesktop'] ?? true)) $classes[] = 'pb-sticky-off-desktop';
			if (!$is_truthy($s['stickyOnTablet'] ?? true)) $classes[] = 'pb-sticky-off-tablet';
			if (!$is_truthy($s['stickyOnMobile'] ?? true)) $classes[] = 'pb-sticky-off-mobile';
		}
		return $classes;
	};

	$color_with_opacity = function ($color, $opacity) {
		$raw = trim((string) ($color ?? ''));
		$alpha = is_numeric($opacity) ? (float) $opacity : null;

		if ($raw === '') {
			return 'transparent';
		}

		if ($alpha === null || $alpha >= 1) {
			return $raw;
		}

		if (str_starts_with($raw, 'rgba(')) {
			return preg_replace('/rgba\((.+),\s*[\d.]+\)/', 'rgba($1, ' . $alpha . ')', $raw) ?: $raw;
		}

		if (str_starts_with($raw, 'rgb(')) {
			return str_replace(['rgb(', ')'], ['rgba(', ', ' . $alpha . ')'], $raw);
		}

		$hex = ltrim($raw, '#');
		$full = strlen($hex) === 3 ? implode('', array_map(fn ($part) => $part . $part, str_split($hex))) : $hex;

		if (preg_match('/^[0-9a-fA-F]{6}$/', $full)) {
			$r = hexdec(substr($full, 0, 2));
			$g = hexdec(substr($full, 2, 2));
			$b = hexdec(substr($full, 4, 2));

			return "rgba($r, $g, $b, $alpha)";
		}

		return $raw;
	};

	$border_radius_value = function (array $s) use ($css_value) {
		if (!empty($s['borderRadius'])) {
			return $css_value($s['borderRadius'], '0');
		}

		return implode(' ', [
			$css_value($s['borderRadiusTL'] ?? null, '0'),
			$css_value($s['borderRadiusTR'] ?? null, '0'),
			$css_value($s['borderRadiusBR'] ?? null, '0'),
			$css_value($s['borderRadiusBL'] ?? null, '0'),
		]);
	};

	$border_style_rules = function (array $s, string $suffix = '', bool $respectHoverInit = false) use ($state_setting, $css_value) {
		if ($respectHoverInit && empty($s['borderHoverInitialized'])) {
			return [];
		}

		$type = strtolower(trim((string) ($state_setting($s, 'borderType', $suffix) ?? 'none')));

		if ($type === '' || $type === 'none') {
			return [
				'border-style:none',
				'border-width:0',
			];
		}

		return [
			'border-style:' . $type,
			'border-width:' . $css_value($state_setting($s, 'borderWidth', $suffix) ?? null, '1px'),
			'border-color:' . ($state_setting($s, 'borderColor', $suffix) ?? '#000000'),
		];
	};

	$shadow_value = function (array $s, string $suffix = '', bool $respectHoverInit = false) use ($css_value, $color_with_opacity, $state_setting, $is_truthy) {
		if ($respectHoverInit && empty($s['shadowHoverInitialized'])) {
			return null;
		}

		$enabled = $suffix !== '' ? $state_setting($s, 'shadowEnabled', $suffix) : ($s['shadowEnabled'] ?? false);

		if ($is_truthy($enabled)) {
			return implode(' ', [
				$css_value($state_setting($s, 'shadowH', $suffix) ?? null, '0'),
				$css_value($state_setting($s, 'shadowV', $suffix) ?? null, '0'),
				$css_value($state_setting($s, 'shadowBlur', $suffix) ?? null, '0'),
				$css_value($state_setting($s, 'shadowSpread', $suffix) ?? null, '0'),
				$color_with_opacity($state_setting($s, 'shadowColor', $suffix) ?? '#000000', $state_setting($s, 'shadowOpacity', $suffix) ?? 0.3),
			]);
		}

		return $suffix !== '' ? 'none' : ($s['boxShadow'] ?? 'none');
	};

	$grid_rows_template = function ($value) {
		$out = trim((string) ($value ?? ''));

		if ($out === '' || strtolower($out) === 'auto') {
			return '';
		}

		if (preg_match('/^\d+$/', $out)) {
			return 'repeat(' . max(1, (int) $out) . ', minmax(0, auto))';
		}

		return $out;
	};
	$container_grid_rows_template = function ($value) {
		$out = trim((string) ($value ?? ''));

		if ($out === '' || strtolower($out) === 'auto') {
			return '';
		}

		if (preg_match('/^\d+$/', $out)) {
			return 'repeat(' . max(1, (int) $out) . ', minmax(68px, auto))';
		}

		if (preg_match('/^(\d+(?:\.\d+)?)fr$/i', $out, $matches)) {
			return 'repeat(' . max(1, (int) $matches[1]) . ', minmax(68px, auto))';
		}

		return $out;
	};
	$container_grid_rows_count = function ($value) {
		$out = trim((string) ($value ?? ''));

		if ($out === '' || strtolower($out) === 'auto') {
			return 1;
		}

		if (preg_match('/^repeat\(\s*(\d+)\s*,/i', $out, $matches)) {
			return max(1, min(12, (int) $matches[1]));
		}

		if (preg_match('/^(\d+(?:\.\d+)?)fr$/i', $out, $matches)) {
			return max(1, min(12, (int) $matches[1]));
		}

		if (preg_match('/^(\d+(?:\.\d+)?)/', $out, $matches)) {
			return max(1, min(12, (int) $matches[1]));
		}

		return 1;
	};

	$grid_columns_template = function ($value) {
		$cols = max(1, min(12, (int) $value));
		return 'repeat(' . $cols . ', minmax(0, 1fr))';
	};

	$background_layer = function (array $s, string $prefix, string $suffix = '') use ($state_setting, $color_with_opacity) {
		$type = strtolower(trim((string) ($state_setting($s, $prefix . 'Type', $suffix) ?? 'none')));

		if ($type === 'none' || $type === '') {
			return null;
		}

		if ($type === 'color') {
			$color = $color_with_opacity($state_setting($s, $prefix . 'Color', $suffix) ?? '#ffffff', $state_setting($s, $prefix . 'Opacity', $suffix));
			return [
				'image' => 'linear-gradient(0deg, ' . $color . ', ' . $color . ')',
				'size' => '100% 100%',
				'position' => 'center center',
				'repeat' => 'no-repeat',
				'attachment' => 'scroll',
				'blendMode' => (string) ($state_setting($s, $prefix . 'BlendMode', $suffix) ?? 'normal'),
			];
		}

		if ($type === 'gradient') {
			$start = (string) ($state_setting($s, $prefix . 'GradientStart', $suffix) ?? '#ffffff');
			$end = (string) ($state_setting($s, $prefix . 'GradientEnd', $suffix) ?? '#000000');
			$position = (float) ($state_setting($s, $prefix . 'GradientPosition', $suffix) ?? 50);
			$angle = (float) ($state_setting($s, $prefix . 'GradientAngle', $suffix) ?? 90);
			$gradientType = strtolower(trim((string) ($state_setting($s, $prefix . 'GradientType', $suffix) ?? 'linear')));
			$image = $gradientType === 'radial'
				? 'radial-gradient(circle, ' . $start . ' 0%, ' . $end . ' ' . $position . '%)'
				: 'linear-gradient(' . $angle . 'deg, ' . $start . ' 0%, ' . $end . ' ' . $position . '%)';

			return [
				'image' => $image,
				'size' => 'cover',
				'position' => 'center center',
				'repeat' => 'no-repeat',
				'attachment' => 'scroll',
				'blendMode' => (string) ($state_setting($s, $prefix . 'BlendMode', $suffix) ?? 'normal'),
			];
		}

		$image = trim((string) ($state_setting($s, $prefix . 'Image', $suffix) ?? ''));
		if ($image === '') {
			return null;
		}

		$size = (string) ($state_setting($s, $prefix . 'Size', $suffix) ?? 'cover');
		if ($size === 'stretch') {
			$size = '100% 100%';
		}

		return [
			'image' => 'url("' . $image . '")',
			'size' => $size,
			'position' => (string) ($state_setting($s, $prefix . 'Position', $suffix) ?? 'center center'),
			'repeat' => (string) ($state_setting($s, $prefix . 'Repeat', $suffix) ?? 'no-repeat'),
			'attachment' => (string) ($state_setting($s, $prefix . 'Attachment', $suffix) ?? 'scroll'),
			'blendMode' => (string) ($state_setting($s, $prefix . 'BlendMode', $suffix) ?? 'normal'),
		];
	};

	$background_styles = function (array $s, string $suffix = '') use ($background_layer) {
		$overlay = $background_layer($s, 'bgOverlay', $suffix);
		$base = $background_layer($s, 'bg', $suffix);
		$layers = [];

		if ($overlay) $layers[] = $overlay;
		if ($base) $layers[] = $base;
		if (!$layers) return [];

		$styles = [
			'background-image:' . implode(', ', array_column($layers, 'image')),
			'background-size:' . implode(', ', array_column($layers, 'size')),
			'background-position:' . implode(', ', array_column($layers, 'position')),
			'background-repeat:' . implode(', ', array_column($layers, 'repeat')),
			'background-attachment:' . implode(', ', array_column($layers, 'attachment')),
		];

		if (count($layers) > 1 && $overlay && ($overlay['blendMode'] ?? 'normal') !== 'normal') {
			$styles[] = 'background-blend-mode:' . $overlay['blendMode'] . ', normal';
		}

		return $styles;
	};

	$shape_divider_type = function (array $s, string $side) {
		$prefix = $side === 'bottom' ? 'shapeDividerBottom' : 'shapeDividerTop';
		return strtolower(trim((string) ($s[$prefix . 'Type'] ?? 'none')));
	};

	$shape_divider_clip_path = function (string $type, string $side, bool $invert = false) {
		$topPaths = [
			'mountains' => 'polygon(0 0, 100% 0, 100% 48%, 88% 28%, 76% 62%, 63% 25%, 50% 68%, 37% 35%, 24% 72%, 12% 40%, 0 64%)',
			'drops' => 'polygon(0 0, 100% 0, 100% 68%, 92% 55%, 85% 74%, 76% 50%, 66% 78%, 56% 52%, 46% 74%, 35% 51%, 24% 70%, 12% 54%, 0 75%)',
			'clouds' => 'polygon(0 0, 100% 0, 100% 62%, 90% 54%, 80% 66%, 70% 54%, 60% 66%, 50% 55%, 40% 66%, 30% 54%, 20% 66%, 10% 55%, 0 62%)',
			'zigzag' => 'polygon(0 0, 100% 0, 100% 75%, 90% 45%, 80% 75%, 70% 45%, 60% 75%, 50% 45%, 40% 75%, 30% 45%, 20% 75%, 10% 45%, 0 75%)',
			'pyramids' => 'polygon(0 0, 100% 0, 100% 78%, 87.5% 45%, 75% 78%, 62.5% 45%, 50% 78%, 37.5% 45%, 25% 78%, 12.5% 45%, 0 78%)',
			'triangle' => 'polygon(0 0, 100% 0, 50% 100%)',
			'triangle-asymmetrical' => 'polygon(0 0, 100% 0, 25% 100%)',
			'tilt' => 'polygon(0 0, 100% 0, 100% 55%, 0 100%)',
			'tilt-opacity' => 'polygon(0 0, 100% 0, 100% 55%, 0 100%)',
			'fan-opacity' => 'polygon(0 0, 100% 0, 100% 82%, 50% 22%, 0 82%)',
			'curve' => 'polygon(0 0, 100% 0, 100% 64%, 84% 78%, 66% 88%, 50% 92%, 34% 88%, 16% 78%, 0 64%)',
			'curve-asymmetrical' => 'polygon(0 0, 100% 0, 100% 45%, 78% 70%, 55% 84%, 32% 88%, 12% 78%, 0 68%)',
			'waves' => 'polygon(0 0, 100% 0, 100% 70%, 88% 60%, 75% 70%, 63% 80%, 50% 70%, 38% 60%, 25% 70%, 13% 80%, 0 70%)',
			'waves-brush' => 'polygon(0 0, 100% 0, 100% 64%, 92% 56%, 84% 70%, 76% 58%, 68% 72%, 60% 58%, 52% 74%, 44% 60%, 36% 76%, 28% 62%, 20% 74%, 10% 58%, 0 70%)',
			'waves-pattern' => 'polygon(0 0, 100% 0, 100% 72%, 93% 58%, 86% 72%, 79% 58%, 72% 72%, 65% 58%, 58% 72%, 51% 58%, 44% 72%, 37% 58%, 30% 72%, 23% 58%, 16% 72%, 8% 58%, 0 72%)',
			'arrow' => 'polygon(0 0, 100% 0, 100% 52%, 60% 52%, 50% 100%, 40% 52%, 0 52%)',
			'split' => 'polygon(0 0, 100% 0, 100% 66%, 50% 42%, 0 66%)',
			'book' => 'polygon(0 0, 100% 0, 100% 62%, 75% 78%, 50% 62%, 25% 78%, 0 62%)',
		];
		$topInvertPaths = [
			'triangle' => 'polygon(0 0, 100% 0, 100% 100%, 50% 0, 0 100%)',
			'triangle-asymmetrical' => 'polygon(0 0, 100% 0, 100% 100%, 25% 0, 0 100%)',
			'arrow' => 'polygon(0 0, 100% 0, 100% 100%, 60% 100%, 50% 52%, 40% 100%, 0 100%)',
			'split' => 'polygon(0 0, 100% 0, 100% 100%, 50% 66%, 0 100%)',
		];
		if ($side === 'top') {
			return ($invert && isset($topInvertPaths[$type])) ? $topInvertPaths[$type] : ($topPaths[$type] ?? $topPaths['tilt']);
		}
		$bottomPaths = [
			'mountains' => 'polygon(0 36%, 12% 60%, 24% 28%, 37% 65%, 50% 32%, 63% 75%, 76% 38%, 88% 72%, 100% 52%, 100% 100%, 0 100%)',
			'drops' => 'polygon(0 25%, 12% 46%, 24% 30%, 35% 49%, 46% 26%, 56% 48%, 66% 22%, 76% 50%, 85% 26%, 92% 45%, 100% 32%, 100% 100%, 0 100%)',
			'clouds' => 'polygon(0 38%, 10% 45%, 20% 34%, 30% 46%, 40% 34%, 50% 45%, 60% 34%, 70% 46%, 80% 34%, 90% 46%, 100% 38%, 100% 100%, 0 100%)',
			'zigzag' => 'polygon(0 25%, 10% 55%, 20% 25%, 30% 55%, 40% 25%, 50% 55%, 60% 25%, 70% 55%, 80% 25%, 90% 55%, 100% 25%, 100% 100%, 0 100%)',
			'pyramids' => 'polygon(0 22%, 12.5% 55%, 25% 22%, 37.5% 55%, 50% 22%, 62.5% 55%, 75% 22%, 87.5% 55%, 100% 22%, 100% 100%, 0 100%)',
			'triangle' => 'polygon(0 100%, 100% 100%, 50% 0)',
			'triangle-asymmetrical' => 'polygon(0 100%, 100% 100%, 75% 0)',
			'tilt' => 'polygon(0 45%, 100% 0, 100% 100%, 0 100%)',
			'tilt-opacity' => 'polygon(0 45%, 100% 0, 100% 100%, 0 100%)',
			'fan-opacity' => 'polygon(0 18%, 50% 78%, 100% 18%, 100% 100%, 0 100%)',
			'curve' => 'polygon(0 36%, 16% 22%, 34% 12%, 50% 8%, 66% 12%, 84% 22%, 100% 36%, 100% 100%, 0 100%)',
			'curve-asymmetrical' => 'polygon(0 32%, 12% 22%, 32% 12%, 55% 16%, 78% 30%, 100% 55%, 100% 100%, 0 100%)',
			'waves' => 'polygon(0 30%, 13% 20%, 25% 30%, 38% 40%, 50% 30%, 63% 20%, 75% 30%, 88% 40%, 100% 30%, 100% 100%, 0 100%)',
			'waves-brush' => 'polygon(0 30%, 10% 42%, 20% 26%, 28% 38%, 36% 24%, 44% 40%, 52% 26%, 60% 42%, 68% 28%, 76% 42%, 84% 30%, 92% 44%, 100% 36%, 100% 100%, 0 100%)',
			'waves-pattern' => 'polygon(0 28%, 8% 42%, 16% 28%, 23% 42%, 30% 28%, 37% 42%, 44% 28%, 51% 42%, 58% 28%, 65% 42%, 72% 28%, 79% 42%, 86% 28%, 93% 42%, 100% 28%, 100% 100%, 0 100%)',
			'arrow' => 'polygon(0 48%, 40% 48%, 50% 0, 60% 48%, 100% 48%, 100% 100%, 0 100%)',
			'split' => 'polygon(0 34%, 50% 58%, 100% 34%, 100% 100%, 0 100%)',
			'book' => 'polygon(0 38%, 25% 22%, 50% 38%, 75% 22%, 100% 38%, 100% 100%, 0 100%)',
		];
		$bottomInvertPaths = [
			'triangle' => 'polygon(0 0, 50% 100%, 100% 0, 100% 100%, 0 100%)',
			'triangle-asymmetrical' => 'polygon(0 0, 75% 100%, 100% 0, 100% 100%, 0 100%)',
			'arrow' => 'polygon(0 0, 40% 0, 50% 48%, 60% 0, 100% 0, 100% 100%, 0 100%)',
			'split' => 'polygon(0 0, 50% 34%, 100% 0, 100% 100%, 0 100%)',
		];
		return ($invert && isset($bottomInvertPaths[$type])) ? $bottomInvertPaths[$type] : ($bottomPaths[$type] ?? $bottomPaths['tilt']);
	};

	$shape_divider_rule = function (array $s, string $side) use ($is_truthy, $shape_divider_type, $shape_divider_clip_path) {
		$prefix = $side === 'bottom' ? 'shapeDividerBottom' : 'shapeDividerTop';
		$type = $shape_divider_type($s, $side);

		if ($type === 'none' || $type === '') {
			return '';
		}

		$color = trim((string) ($s[$prefix . 'Color'] ?? '#ffffff')) ?: '#ffffff';
		$rawWidth = trim((string) ($s[$prefix . 'Width'] ?? ''));
		$rawHeight = trim((string) ($s[$prefix . 'Height'] ?? ''));
		$width = $rawWidth !== '' ? (preg_match('/^-?\d+(\.\d+)?$/', $rawWidth) ? $rawWidth . '%' : $rawWidth) : '100%';
		$height = $rawHeight !== '' ? (preg_match('/^-?\d+(\.\d+)?$/', $rawHeight) ? $rawHeight . 'px' : $rawHeight) : '60px';
		$invert = $is_truthy($s[$prefix . 'Flip'] ?? false);
		$front = $is_truthy($s[$prefix . 'Front'] ?? false);
		$transform = 'translateX(-50%)';
		$clipPath = $shape_divider_clip_path($type, $side, $invert);
		$shapeOpacity = str_contains($type, 'opacity') ? '0.55' : '';

		$rules = [
			'content:""',
			'position:absolute',
			'left:50%',
			($side === 'top' ? 'top:0' : 'bottom:0'),
			'width:' . $width,
			'height:' . $height,
			'background:' . $color,
			'pointer-events:none',
			'transform:' . $transform,
			'transform-origin:center center',
			'clip-path:' . $clipPath,
			'z-index:' . ($front ? '4' : '0'),
		];
		if ($shapeOpacity !== '') $rules[] = 'opacity:' . $shapeOpacity;
		return implode(';', $rules);
	};

	$shape_divider_shapes_path = resource_path('data/pagebuilder_elementor_shapes.json');
	$shape_divider_shapes = is_file($shape_divider_shapes_path)
		? (json_decode(file_get_contents($shape_divider_shapes_path), true) ?: [])
		: [];
	$shape_divider_width_types = ['mountains', 'zigzag', 'pyramids', 'triangle', 'triangle-asymmetrical', 'opacity-tilt', 'opacity-fan', 'curve', 'curve-asymmetrical', 'waves', 'wave-brush', 'waves-pattern', 'arrow', 'split', 'book'];
	$shape_divider_flip_types = ['mountains', 'drops', 'clouds', 'pyramids', 'triangle-asymmetrical', 'tilt', 'opacity-tilt', 'curve-asymmetrical', 'waves', 'wave-brush', 'waves-pattern'];
	$shape_divider_invert_types = ['drops', 'clouds', 'pyramids', 'triangle', 'triangle-asymmetrical', 'curve', 'curve-asymmetrical', 'waves', 'arrow', 'split', 'book'];
	$normalize_shape_divider_type = function ($type) {
		$raw = strtolower(trim((string) ($type ?? 'none')));
		if ($raw === '' || $raw === 'none') return 'none';
		if ($raw === 'tilt-opacity') return 'opacity-tilt';
		if ($raw === 'fan-opacity') return 'opacity-fan';
		if ($raw === 'waves-brush') return 'wave-brush';
		return $raw;
	};
	$shape_divider_type = function (array $s, string $side) use ($normalize_shape_divider_type) {
		$prefix = $side === 'bottom' ? 'shapeDividerBottom' : 'shapeDividerTop';
		return $normalize_shape_divider_type($s[$prefix . 'Type'] ?? 'none');
	};
	$shape_divider_supports_width = fn (string $type) => in_array($normalize_shape_divider_type($type), $shape_divider_width_types, true);
	$shape_divider_supports_flip = fn (string $type) => in_array($normalize_shape_divider_type($type), $shape_divider_flip_types, true);
	$shape_divider_supports_invert = fn (string $type) => in_array($normalize_shape_divider_type($type), $shape_divider_invert_types, true);
	$shape_divider_css_size = function ($value, string $fallback, string $unit) {
		$raw = trim((string) ($value ?? ''));
		if ($raw === '') return $fallback;
		return preg_match('/^-?\d+(\.\d+)?$/', $raw) ? $raw . $unit : $raw;
	};
	$shape_divider_escape_attr = fn ($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8', false);
	$shape_divider_layer_rule = function (array $s, string $side) use ($is_truthy, $shape_divider_type, $shape_divider_supports_invert) {
		$prefix = $side === 'bottom' ? 'shapeDividerBottom' : 'shapeDividerTop';
		$type = $shape_divider_type($s, $side);
		$negative = $shape_divider_supports_invert($type) && $is_truthy($s[$prefix . 'Negative'] ?? false);
		$shouldRotate = ($side === 'top' && $negative) || ($side === 'bottom' && !$negative);
		$rules = [
			'position:absolute',
			'left:0',
			$side === 'top' ? 'top:-1px' : 'bottom:-1px',
			'width:100%',
			'line-height:0',
			'overflow:hidden',
			'pointer-events:none',
			'direction:ltr',
			'z-index:' . ($is_truthy($s[$prefix . 'Front'] ?? false) ? '2' : '0'),
		];
		if ($shouldRotate) $rules[] = 'transform:rotate(180deg)';
		return implode(';', $rules);
	};
	$shape_divider_svg = function (array $s, string $side) use ($shape_divider_shapes, $shape_divider_type, $shape_divider_supports_width, $shape_divider_supports_flip, $shape_divider_supports_invert, $shape_divider_css_size, $shape_divider_escape_attr, $is_truthy) {
		$prefix = $side === 'bottom' ? 'shapeDividerBottom' : 'shapeDividerTop';
		$type = $shape_divider_type($s, $side);
		if ($type === 'none' || !isset($shape_divider_shapes[$type]) || !is_array($shape_divider_shapes[$type])) {
			return '';
		}
		$negative = $shape_divider_supports_invert($type) && $is_truthy($s[$prefix . 'Negative'] ?? false);
		$shape = ($negative && isset($shape_divider_shapes[$type]['negative']) && is_array($shape_divider_shapes[$type]['negative']))
			? $shape_divider_shapes[$type]['negative']
			: $shape_divider_shapes[$type];
		$svgAttrs = is_array($shape['svgAttrs'] ?? null) ? $shape['svgAttrs'] : [];
		$paths = is_array($shape['paths'] ?? null) ? $shape['paths'] : [];
		if (empty($paths)) return '';

		$color = trim((string) ($s[$prefix . 'Color'] ?? '#ffffff')) ?: '#ffffff';
		$width = $shape_divider_supports_width($type) ? $shape_divider_css_size($s[$prefix . 'Width'] ?? '', '100%', '%') : '100%';
		$height = $shape_divider_css_size($s[$prefix . 'Height'] ?? '', '60px', 'px');
		$flip = $shape_divider_supports_flip($type) && $is_truthy($s[$prefix . 'Flip'] ?? false);
		$svgTransform = $flip ? 'translateX(-50%) rotateY(180deg)' : 'translateX(-50%)';
		$attrs = [
			'xmlns="http://www.w3.org/2000/svg"',
			'viewBox="' . $shape_divider_escape_attr($svgAttrs['viewBox'] ?? '0 0 1000 100') . '"',
			'preserveAspectRatio="' . $shape_divider_escape_attr($svgAttrs['preserveAspectRatio'] ?? 'none') . '"',
			'aria-hidden="true"',
			'focusable="false"',
			'style="display:block;left:50%;position:relative;transform:' . $shape_divider_escape_attr($svgTransform) . ';width:calc(' . $shape_divider_escape_attr($width) . ' + 1.3px);height:' . $shape_divider_escape_attr($height) . ';"',
		];
		$pathHtml = '';
		foreach ($paths as $path) {
			if (!is_array($path)) continue;
			$style = 'fill:' . $color . ';' . (string) ($path['style'] ?? '');
			$pathAttrs = [
				'class="elementor-shape-fill"',
				'd="' . $shape_divider_escape_attr($path['d'] ?? '') . '"',
				'style="' . $shape_divider_escape_attr($style) . '"',
			];
			if (($path['opacity'] ?? '') !== '') {
				$pathAttrs[] = 'opacity="' . $shape_divider_escape_attr($path['opacity']) . '"';
			}
			$pathHtml .= '<path ' . implode(' ', $pathAttrs) . '></path>';
		}
		return '<svg ' . implode(' ', $attrs) . '>' . $pathHtml . '</svg>';
	};

	$attribute_pairs = function ($attrs) {
		$out = [];

		if (!is_array($attrs)) {
			return $out;
		}

		foreach ($attrs as $attr) {
			$name = trim((string) ($attr['name'] ?? ''));

			if ($name !== '' && preg_match('/^[A-Za-z_:][A-Za-z0-9:_.-]*$/', $name)) {
				$out[$name] = (string) ($attr['value'] ?? '');
			}
		}

		return $out;
	};

	$scoped_css = function ($nodeId, $css) {
		$nodeId = trim((string) ($nodeId ?? ''));
		$css = trim((string) ($css ?? ''));

		if ($nodeId === '' || $css === '') {
			return '';
		}

		return preg_replace('/\bselector\b/', '#pb-node-' . $nodeId, $css) ?: $css;
	};

	$responsive_side_rules = function (array $s, string $deviceSuffix, string $cssPrefix, callable $formatter) {
		$rules = [];

		foreach (['Top' => 'top', 'Right' => 'right', 'Bottom' => 'bottom', 'Left' => 'left'] as $settingSide => $cssSide) {
			$key = $cssPrefix . $settingSide . $deviceSuffix;
			if (($s[$key] ?? '') === '') {
				continue;
			}

			$rules[] = $cssPrefix . '-' . $cssSide . ':' . $formatter($s[$key]);
		}

		return $rules;
	};

	$responsive_setting_value = function (array $s, string $baseKey, string $deviceSuffix, $default = '') {
		$responsiveKey = $baseKey . $deviceSuffix;
		$value = $s[$responsiveKey] ?? '';
		if ($value === '' || $value === null) {
			$desktopValue = $s[$baseKey] ?? $default;
			return ($desktopValue === '' || $desktopValue === null) ? $default : $desktopValue;
		}

		return $value;
	};

	$resolve_html_tag = function (array $s) {
		$raw = strtolower(trim((string) ($s['htmlTag'] ?? 'default')));
		$allowed = ['div', 'section', 'header', 'main', 'article', 'aside', 'footer', 'nav'];
		if ($raw === '' || $raw === 'default') return 'div';
		return in_array($raw, $allowed, true) ? $raw : 'div';
	};

	$style_with_important = function (array $rules) {
		$items = [];
		foreach ($rules as $rule) {
			if (!is_string($rule) || trim($rule) === '') continue;
			$items[] = $rule . ' !important';
		}
		return implode(';', $items);
	};

	$__pbWidgetModule = config('pagebuilder_elementor_widgets.' . $type);
	$__pbWidgetView = is_array($__pbWidgetModule) ? ($__pbWidgetModule['view'] ?? '') : '';
	if (is_string($__pbWidgetView) && $__pbWidgetView !== '' && view()->exists($__pbWidgetView)) {
		$__pbWidgetData = get_defined_vars();
		$__pbWidgetData['node'] = $node;
		if (isset($pageData)) $__pbWidgetData['pageData'] = $pageData;
		echo view($__pbWidgetView, $__pbWidgetData)->render();
		return;
	}

	echo '<!-- Unsupported Page Builder widget: ' . e($type) . ' -->';
	return;
@endphp
