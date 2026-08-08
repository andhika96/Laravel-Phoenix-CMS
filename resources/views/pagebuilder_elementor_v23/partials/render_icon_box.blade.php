@php
	$iconBoxSettings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeId = trim((string) ($node['id'] ?? 'icon-box')) ?: 'icon-box';
	$dynamicBindings = is_array($iconBoxSettings['dynamicBindings'] ?? null) ? $iconBoxSettings['dynamicBindings'] : [];
	$dynamicContext = request()->attributes->get('pagebuilder_dynamic_context', []);
	$dynamicContext = is_array($dynamicContext) ? $dynamicContext : [];
	if (!array_key_exists('page', $dynamicContext) && isset($pageData)) $dynamicContext['page'] = $pageData;
	$dynamicContext['page_url'] ??= url()->current();
	$dynamicContext['site_title'] ??= config('app.name');
	$dynamicContext['site_url'] ??= config('app.url');
	$dynamicContext['user'] ??= request()->user();
	$dynamicResolver = app(\App\Support\PageBuilderElementorV23\DynamicTagResolver::class);
	$resolveDynamic = fn (string $field, mixed $fallback): mixed => $dynamicResolver->resolve($field, $fallback, $dynamicBindings, $dynamicContext);
	$title = (string) $resolveDynamic('title', $iconBoxSettings['title'] ?? 'This is the heading');
	$description = (string) $resolveDynamic('description', $iconBoxSettings['description'] ?? '');
	$linkUrl = trim((string) $resolveDynamic('linkUrl', $iconBoxSettings['linkUrl'] ?? ''));
	$safeLinkUrl = function (mixed $value): string {
		$url = trim((string) $value);
		if ($url === '' || str_starts_with($url, '//')) return '';
		return preg_match('/^(?:https?:|mailto:|tel:|\/|#)/i', $url) ? $url : '';
	};
	$linkUrl = $safeLinkUrl($linkUrl);
	$titleTag = strtolower(trim((string) ($iconBoxSettings['titleTag'] ?? 'h3')));
	if (!in_array($titleTag, ['h1','h2','h3','h4','h5','h6','div','span','p'], true)) $titleTag = 'h3';
	$titleTagFontSizes = ['h1'=>'40px','h2'=>'34px','h3'=>'29px','h4'=>'24px','h5'=>'20px','h6'=>'16px','div'=>'29px','span'=>'29px','p'=>'29px'];
	$storedTitleFontSize = trim((string) ($iconBoxSettings['titleFontSize'] ?? ''));
	$titleFontSizeMode = in_array($iconBoxSettings['titleFontSizeMode'] ?? null, ['auto','custom'], true)
		? $iconBoxSettings['titleFontSizeMode']
		: ($storedTitleFontSize !== '' && $storedTitleFontSize !== '29px' ? 'custom' : 'auto');
	$rawIconClass = trim((string) ($iconBoxSettings['iconClass'] ?? 'far fa-star'));
	$iconClass = preg_match('/^(?:fas|far|fab|fal|fad) fa-[a-z0-9-]+$/', $rawIconClass) ? $rawIconClass : 'far fa-star';
	$enum = function (mixed $value, array $allowed, string $fallback): string { $value = strtolower(trim((string) $value)); return in_array($value, $allowed, true) ? $value : $fallback; };
	$view = $enum($iconBoxSettings['view'] ?? 'default', ['default','stacked','framed'], 'default');
	$shape = $enum($iconBoxSettings['shape'] ?? 'circle', ['circle','rounded','square'], 'circle');
	$responsive = function (string $base, string $suffix = '', mixed $fallback = '') use ($iconBoxSettings): mixed {
		$keys = $suffix === 'Mobile' ? [$base.'Mobile',$base.'Tablet',$base] : ($suffix === 'Tablet' ? [$base.'Tablet',$base] : [$base]);
		foreach ($keys as $key) { $value = $iconBoxSettings[$key] ?? null; if ($value !== '' && $value !== null) return $value; }
		return $fallback;
	};
	$cssLength = function (mixed $value, string $fallback = ''): string { $raw = trim((string) $value); return preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh|deg)?$/i', $raw) ? $raw : $fallback; };
	$cssColor = function (mixed $value, string $fallback = 'inherit'): string { $raw = trim((string) $value); return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : $fallback; };
	$cssShadow = function (mixed $value): string { $raw = trim((string) $value); return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : 'none'; };
	$position = fn (string $suffix = ''): string => $enum($responsive('iconPosition', $suffix, 'top'), ['top','left','right'], 'top');
	$alignment = fn (string $suffix = ''): string => $enum($responsive('alignment', $suffix, 'center'), ['left','center','right','justify'], 'center');
	$alignItems = fn (string $value): string => $value === 'left' ? 'flex-start' : ($value === 'right' ? 'flex-end' : ($value === 'justify' ? 'stretch' : 'center'));
	$flexDirection = fn (string $value): string => $value === 'left' ? 'row' : ($value === 'right' ? 'row-reverse' : 'column');
	$iconSides = function (string $base, string $suffix, string $fallback) use ($responsive, $cssLength, $iconBoxSettings): string {
		$legacy = $base === 'iconBorderWidth' ? ($iconBoxSettings['iconBorderWidth'] ?? $fallback) : $fallback;
		return implode(' ', array_map(fn ($side) => $cssLength($responsive($base.$side, $suffix, $legacy), $cssLength($legacy, $fallback)), ['Top','Right','Bottom','Left']));
	};
	$iconRadius = function (string $suffix = '') use ($iconSides, $shape): string {
		$custom = $iconSides('iconBorderRadius', $suffix, '0px');
		if ($custom !== '0px 0px 0px 0px') return $custom;
		return $shape === 'circle' ? '50%' : ($shape === 'rounded' ? '12%' : '0');
	};
	$fontStyle = function (string $prefix, string $suffix = '') use ($iconBoxSettings, $responsive, $cssLength, $cssColor, $cssShadow, $enum, $titleTag, $titleTagFontSizes, $titleFontSizeMode): string {
		$isTitle = $prefix === 'title';
		$fontSize = $isTitle && $titleFontSizeMode === 'auto'
			? ($titleTagFontSizes[$titleTag] ?? '29px')
			: $cssLength($responsive($prefix.'FontSize', $suffix, $isTitle ? '29px' : '16px'), $isTitle ? '29px' : '16px');
		$weight = trim((string) ($iconBoxSettings[$prefix.'FontWeight'] ?? '400'));
		if (!preg_match('/^(?:normal|bold|[1-9]00)$/', $weight)) $weight = '400';
		return implode(';', [
			'font-family:' . (preg_match('/^[A-Za-z0-9 _,\'"-]+$/', trim((string) ($iconBoxSettings[$prefix.'FontFamily'] ?? 'inherit'))) ? trim((string) ($iconBoxSettings[$prefix.'FontFamily'] ?? 'inherit')) : 'inherit'),
			'font-size:' . $fontSize,
			'font-weight:' . $weight,
			'line-height:' . $cssLength($responsive($prefix.'LineHeight', $suffix, $isTitle ? '1.2em' : '1.5em'), $isTitle ? '1.2em' : '1.5em'),
			'letter-spacing:' . $cssLength($responsive($prefix.'LetterSpacing', $suffix, '0px'), '0px'),
			'word-spacing:' . $cssLength($responsive($prefix.'WordSpacing', $suffix, '0px'), '0px'),
			'text-transform:' . $enum($iconBoxSettings[$prefix.'TextTransform'] ?? 'none', ['none','uppercase','lowercase','capitalize'], 'none'),
			'font-style:' . $enum($iconBoxSettings[$prefix.'FontStyle'] ?? 'normal', ['normal','italic','oblique'], 'normal'),
			'text-decoration:' . $enum($iconBoxSettings[$prefix.'TextDecoration'] ?? 'none', ['none','underline','overline','line-through'], 'none'),
			'color:' . $cssColor($iconBoxSettings[$prefix.'Color'] ?? '', 'inherit'),
			'text-shadow:' . $cssShadow($iconBoxSettings[$prefix.'TextShadow'] ?? 'none'),
			...($isTitle ? ['-webkit-text-stroke-width:' . $cssLength($responsive('titleTextStrokeWidth', $suffix, '0px'), '0px'), '-webkit-text-stroke-color:' . $cssColor($iconBoxSettings['titleTextStrokeColor'] ?? 'currentColor', 'currentColor')] : []),
		]);
	};
	$linkTarget = ($iconBoxSettings['linkTarget'] ?? '') === '_blank' ? '_blank' : '';
	$relTokens = $linkTarget === '_blank' ? ['noopener','noreferrer'] : [];
	if (in_array($iconBoxSettings['linkNofollow'] ?? false, [true,1,'1','true'], true)) $relTokens[] = 'nofollow';
	$linkRel = implode(' ', array_values(array_unique($relTokens)));
	$linkAttributes = [];
	foreach (($iconBoxSettings['linkCustomAttributes'] ?? []) as $attribute) {
		if (!is_array($attribute)) continue;
		$name = strtolower(trim((string) ($attribute['key'] ?? $attribute['name'] ?? '')));
		if (!preg_match('/^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/', $name)) continue;
		$linkAttributes[$name] = (string) ($attribute['value'] ?? '');
	}
	$primary = $cssColor($iconBoxSettings['primaryColor'] ?? '#69727d', '#69727d');
	$secondary = $cssColor($iconBoxSettings['secondaryColor'] ?? '#ffffff', '#ffffff');
	$desktopPosition = $position(''); $desktopAlignment = $alignment('');
	$boxStyle = implode(';', ['flex-direction:'.$flexDirection($desktopPosition),'align-items:'.($desktopPosition === 'top' ? $alignItems($desktopAlignment) : 'center'),'text-align:'.$desktopAlignment,'--pb-icon-box-icon-spacing:'.$cssLength($responsive('iconSpacing','','15px'),'15px'),'--pb-icon-box-content-spacing:'.$cssLength($responsive('contentSpacing','','0px'),'0px'),'--pb-icon-box-media-justify:'.($desktopPosition === 'top' ? $alignItems($desktopAlignment) : 'center'),'--pb-icon-primary-hover:'.$cssColor($iconBoxSettings['primaryColorHover'] ?? '', $primary),'--pb-icon-secondary-hover:'.$cssColor($iconBoxSettings['secondaryColorHover'] ?? '', $secondary)]);
	$iconStyle = implode(';', ['font-size:'.$cssLength($responsive('iconSize','','50px'),'50px'),'padding:'.($view === 'default' ? '0' : $cssLength($responsive('iconPadding','','0px'),'0px')),'color:'.($view === 'stacked' ? $secondary : $primary),'background-color:'.($view === 'stacked' ? $primary : 'transparent'),'border-style:'.($view === 'framed' ? 'solid' : 'none'),'border-color:'.($view === 'framed' ? $primary : 'transparent'),'border-width:'.($view === 'framed' ? $iconSides('iconBorderWidth','','1px') : '0'),'border-radius:'.$iconRadius('')]);
	$iconGlyphStyle = 'transform:rotate('.$cssLength($responsive('iconRotate','','0deg'),'0deg').')';
	$advanced = app(\App\Support\PageBuilderElementorV23\WidgetAdvancedStyleResolver::class)->resolve($iconBoxSettings, $nodeId, request());
	$hover = $enum($iconBoxSettings['hoverAnimation'] ?? 'none', ['none','grow','shrink','pulse','pulse-grow','pulse-shrink','push','pop','bounce-in','bounce-out','rotate','grow-rotate','float','sink','bob','hang','skew','skew-forward','skew-backward','wobble-vertical','wobble-horizontal','wobble-to-bottom-right','wobble-to-top-right','wobble-top','wobble-bottom','wobble-skew','buzz','buzz-out'], 'none');
	$rootClasses = array_values(array_unique(array_merge(['el-widget-icon-box','pb-icon-box','pb-icon-box--position-'.$desktopPosition,'is-view-'.$view,'is-shape-'.$shape], $advanced['classes'], $hover === 'none' ? [] : ['pb-icon-box--hover-'.$hover])));
	$mediaRules = [];
	foreach (['Tablet'=>1024,'Mobile'=>767] as $suffix => $breakpoint) {
		$currentPosition=$position($suffix); $currentAlignment=$alignment($suffix);
		$rootRules=['flex-direction:'.$flexDirection($currentPosition),'align-items:'.($currentPosition==='top'?$alignItems($currentAlignment):'center'),'text-align:'.$currentAlignment,'--pb-icon-box-icon-spacing:'.$cssLength($responsive('iconSpacing',$suffix,'15px'),'15px'),'--pb-icon-box-content-spacing:'.$cssLength($responsive('contentSpacing',$suffix,'0px'),'0px'),'--pb-icon-box-media-justify:'.($currentPosition==='top'?$alignItems($currentAlignment):'center')];
		$mediaLayout=['width:'.($currentPosition==='top'?'100%':'auto'),'margin:0'];
		if($currentPosition==='top')$mediaLayout[]='margin-bottom:var(--pb-icon-box-icon-spacing)';if($currentPosition==='left')$mediaLayout[]='margin-right:var(--pb-icon-box-icon-spacing)';if($currentPosition==='right')$mediaLayout[]='margin-left:var(--pb-icon-box-icon-spacing)';
		$iconRules=['font-size:'.$cssLength($responsive('iconSize',$suffix,'50px'),'50px'),'padding:'.($view==='default'?'0':$cssLength($responsive('iconPadding',$suffix,'0px'),'0px')),'border-width:'.($view==='framed'?$iconSides('iconBorderWidth',$suffix,'1px'):'0'),'border-radius:'.$iconRadius($suffix)];
		$glyphRules=['transform:rotate('.$cssLength($responsive('iconRotate',$suffix,'0deg'),'0deg').')'];
		$mediaRules[]='@media (max-width: '.$breakpoint.'px){#'.$advanced['id'].'{'.implode(';',$rootRules).'}#'.$advanced['id'].' > .pb-icon-box__media{'.implode(';',$mediaLayout).'}#'.$advanced['id'].' .pb-icon-box__icon{'.implode(';',$iconRules).'}#'.$advanced['id'].' .pb-icon-box__icon i{'.implode(';',$glyphRules).'}#'.$advanced['id'].' .pb-icon-box__title{'.$fontStyle('title',$suffix).'}#'.$advanced['id'].' .pb-icon-box__description{'.$fontStyle('description',$suffix).'}}';
	}
@endphp

<div id="{{ $advanced['id'] }}" class="{{ implode(' ', $rootClasses) }}" style="{{ $boxStyle }}" data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach>
	<div class="pb-icon-box__media">
		@if($linkUrl !== '')<a class="pb-icon-box__icon-link" href="{{ $linkUrl }}" @if($linkTarget !== '') target="{{ $linkTarget }}" @endif @if($linkRel !== '') rel="{{ $linkRel }}" @endif @foreach($linkAttributes as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach><span class="pb-icon-box__icon" style="{{ $iconStyle }}"><i class="{{ $iconClass }}" style="{{ $iconGlyphStyle }}" aria-hidden="true"></i></span></a>
		@else<span class="pb-icon-box__icon-link"><span class="pb-icon-box__icon" style="{{ $iconStyle }}"><i class="{{ $iconClass }}" style="{{ $iconGlyphStyle }}" aria-hidden="true"></i></span></span>@endif
	</div>
	<div class="pb-icon-box__content">
		@if($title !== '')
			@if($linkUrl !== '')<a class="pb-icon-box__title-link" href="{{ $linkUrl }}" @if($linkTarget !== '') target="{{ $linkTarget }}" @endif @if($linkRel !== '') rel="{{ $linkRel }}" @endif @foreach($linkAttributes as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach><{{ $titleTag }} class="pb-icon-box__title" style="{{ $fontStyle('title') }}">{{ $title }}</{{ $titleTag }}></a>
			@else<{{ $titleTag }} class="pb-icon-box__title" style="{{ $fontStyle('title') }}">{{ $title }}</{{ $titleTag }}>@endif
		@endif
		@if($description !== '')<p class="pb-icon-box__description" style="{{ $fontStyle('description') }}">{{ $description }}</p>@endif
	</div>
</div>
<style>{!! $advanced['css'] . implode('', $mediaRules) !!}</style>
