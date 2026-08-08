@php
	$settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeId = trim((string) ($node['id'] ?? 'icon-list')) ?: 'icon-list';
	$safeUrl = function (mixed $value): string {
		$url = trim((string) $value);
		if ($url === '' || str_starts_with($url, '//')) return '';
		return preg_match('/^(?:https?:|mailto:|tel:|\/|#)/i', $url) ? $url : '';
	};
	$cssLength = function (mixed $value, string $fallback = '0px'): string {
		$raw = trim((string) $value);
		return preg_match('/^-?\d+(?:\.\d+)?(?:px|%|em|rem|vw|vh)?$/i', $raw) ? $raw : $fallback;
	};
	$cssColor = function (mixed $value, string $fallback = 'inherit'): string {
		$raw = trim((string) $value);
		return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : $fallback;
	};
	$enum = function (mixed $value, array $allowed, string $fallback): string {
		$raw = strtolower(trim((string) $value));
		return in_array($raw, $allowed, true) ? $raw : $fallback;
	};
	$responsive = function (string $base, string $suffix = '', mixed $fallback = '') use ($settings): mixed {
		$keys = $suffix === 'Mobile' ? [$base.'Mobile', $base.'Tablet', $base] : ($suffix === 'Tablet' ? [$base.'Tablet', $base] : [$base]);
		foreach ($keys as $key) {
			$value = $settings[$key] ?? null;
			if ($value !== '' && $value !== null) return $value;
		}
		return $fallback;
	};
	$layout = $enum($settings['layout'] ?? 'traditional', ['traditional', 'inline'], 'traditional');
	$applyLinkOn = $enum($settings['applyLinkOn'] ?? 'full_width', ['full_width', 'inline'], 'full_width');
	$items = [];
	foreach (is_array($settings['items'] ?? null) ? $settings['items'] : [] as $index => $item) {
		if (!is_array($item)) continue;
		$iconClass = trim(preg_replace('/[^A-Za-z0-9 _-]/', '', (string) ($item['iconClass'] ?? 'fas fa-check')));
		if (!preg_match('/\bfa-[A-Za-z0-9-]+\b/', $iconClass)) $iconClass = 'fas fa-check';
		$linkUrl = $safeUrl($item['linkUrl'] ?? '');
		$linkTarget = ($item['linkTarget'] ?? '') === '_blank' ? '_blank' : '';
		$linkRel = [];
		if (!empty($item['linkNofollow'])) $linkRel[] = 'nofollow';
		if ($linkTarget === '_blank') array_push($linkRel, 'noopener', 'noreferrer');
		$attributes = [];
		foreach (is_array($item['linkCustomAttributes'] ?? null) ? $item['linkCustomAttributes'] : [] as $attribute) {
			if (!is_array($attribute)) continue;
			$name = trim((string) ($attribute['name'] ?? $attribute['key'] ?? ''));
			if (!preg_match('/^(?:data-|aria-)[A-Za-z0-9_.:-]+$/', $name)) continue;
			$attributes[$name] = (string) ($attribute['value'] ?? '');
		}
		$items[] = ['id' => preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($item['id'] ?? 'icon-list-item-'.$index)), 'text' => (string) ($item['text'] ?? ''), 'iconClass' => $iconClass, 'linkUrl' => $linkUrl, 'linkTarget' => $linkTarget, 'linkRel' => implode(' ', array_unique($linkRel)), 'attributes' => $attributes];
	}
	$alignValue = fn (string $suffix = ''): string => ['start' => 'flex-start', 'center' => 'center', 'end' => 'flex-end'][$enum($responsive('alignment', $suffix, 'start'), ['start','center','end'], 'start')];
	$iconAlignValue = fn (string $suffix = ''): string => ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'][$enum($responsive('iconHorizontalAlignment', $suffix, 'left'), ['left','center','right'], 'left')];
	$fontFamily = trim((string) ($settings['textFontFamily'] ?? 'inherit'));
	if ($fontFamily !== 'inherit' && !preg_match('/^[A-Za-z0-9 _,\'"-]+$/', $fontFamily)) $fontFamily = 'inherit';
	$fontStyle = function (string $suffix = '') use ($settings, $responsive, $cssLength, $enum, $fontFamily): string {
		return implode(';', [
			'font-family:'.$fontFamily,
			'font-size:'.$cssLength($responsive('textFontSize', $suffix, '16px'), '16px'),
			'font-weight:'.preg_replace('/[^A-Za-z0-9-]/', '', (string) ($settings['textFontWeight'] ?? '400')),
			'line-height:'.$cssLength($responsive('textLineHeight', $suffix, '1.5em'), '1.5em'),
			'letter-spacing:'.$cssLength($responsive('textLetterSpacing', $suffix, '0px'), '0px'),
			'word-spacing:'.$cssLength($responsive('textWordSpacing', $suffix, '0px'), '0px'),
			'text-transform:'.$enum($settings['textTextTransform'] ?? 'none', ['none','uppercase','lowercase','capitalize'], 'none'),
			'font-style:'.$enum($settings['textFontStyle'] ?? 'normal', ['normal','italic','oblique'], 'normal'),
			'text-decoration:'.$enum($settings['textTextDecoration'] ?? 'none', ['none','underline','overline','line-through'], 'none'),
			'text-shadow:'.(preg_match('/^[#a-z0-9(),.%\s-]+$/i', (string) ($settings['textTextShadow'] ?? 'none')) ? (string) ($settings['textTextShadow'] ?? 'none') : 'none'),
		]);
	};
	$dividerStyle = $enum($settings['dividerStyle'] ?? 'solid', ['solid','double','dotted','dashed'], 'solid');
	$duration = fn (mixed $value): string => max(0, min(10, (float) $value)).'s';
	$rootVariables = function (string $suffix = '') use ($layout, $settings, $responsive, $cssLength, $cssColor, $enum, $alignValue, $iconAlignValue, $dividerStyle, $duration): string {
		return implode(';', [
			'--pb-icon-list-space-between:'.$cssLength($responsive('spaceBetween', $suffix, '0px'), '0px'),
			'--pb-icon-list-align:'.$alignValue($suffix),
			'--pb-icon-list-divider-style:'.$dividerStyle,
			'--pb-icon-list-divider-weight:'.$cssLength($settings['dividerWeight'] ?? '1px', '1px'),
			'--pb-icon-list-divider-size:'.$cssLength($layout === 'inline' ? ($settings['dividerHeight'] ?? '100%') : ($settings['dividerWidth'] ?? '100%'), '100%'),
			'--pb-icon-list-divider-color:'.$cssColor($settings['dividerColor'] ?? '#dddddd', '#dddddd'),
			'--pb-icon-list-icon-size:'.$cssLength($responsive('iconSize', $suffix, '14px'), '14px'),
			'--pb-icon-list-icon-gap:'.$cssLength($responsive('iconGap', $suffix, '8px'), '8px'),
			'--pb-icon-list-icon-offset:'.$cssLength($responsive('iconVerticalOffset', $suffix, '0px'), '0px'),
			'--pb-icon-list-icon-align:'.$iconAlignValue($suffix),
			'--pb-icon-list-icon-vertical:'.$enum($responsive('iconVerticalAlignment', $suffix, 'center'), ['flex-start','center','flex-end'], 'center'),
			'--pb-icon-list-icon-color:'.$cssColor($settings['iconColor'] ?? '', 'inherit'),
			'--pb-icon-list-icon-hover:'.$cssColor($settings['iconColorHover'] ?? '', $cssColor($settings['iconColor'] ?? '', 'inherit')),
			'--pb-icon-list-icon-transition:'.$duration($settings['iconTransitionDuration'] ?? .3),
			'--pb-icon-list-text-color:'.$cssColor($settings['textColor'] ?? '', 'inherit'),
			'--pb-icon-list-text-hover:'.$cssColor($settings['textColorHover'] ?? '', $cssColor($settings['textColor'] ?? '', 'inherit')),
			'--pb-icon-list-text-transition:'.$duration($settings['textTransitionDuration'] ?? .3),
		]);
	};
	$advanced = app(\App\Support\PageBuilderElementorV23\WidgetAdvancedStyleResolver::class)->resolve($settings, $nodeId, request());
	$rootClasses = array_values(array_unique(array_merge(['el-widget-icon-list','pb-icon-list', $layout === 'inline' ? 'is-inline' : 'is-traditional', !empty($settings['divider']) ? 'has-divider' : '', 'pb-icon-list--apply-'.$applyLinkOn], $advanced['classes'])));
	$rootClasses = array_values(array_filter($rootClasses));
	$mediaRules = '@media(max-width:1024px){#'.$advanced['id'].'{'.$rootVariables('Tablet').'}#'.$advanced['id'].' .pb-icon-list__text{'.$fontStyle('Tablet').'}}';
	$mediaRules .= '@media(max-width:767px){#'.$advanced['id'].'{'.$rootVariables('Mobile').'}#'.$advanced['id'].' .pb-icon-list__text{'.$fontStyle('Mobile').'}}';
@endphp

<ul id="{{ $advanced['id'] }}" class="{{ implode(' ', $rootClasses) }}" style="{{ $rootVariables() }}" data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach>
	@foreach($items as $item)
		<li class="pb-icon-list__item">
			@if($item['linkUrl'] !== '')<a class="pb-icon-list__content" href="{{ $item['linkUrl'] }}" @if($item['linkTarget'] !== '') target="{{ $item['linkTarget'] }}" @endif @if($item['linkRel'] !== '') rel="{{ $item['linkRel'] }}" @endif @foreach($item['attributes'] as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach>@else<span class="pb-icon-list__content">@endif
				<span class="pb-icon-list__icon"><i class="{{ $item['iconClass'] }}" aria-hidden="true"></i></span><span class="pb-icon-list__text" style="{{ $fontStyle() }}">{{ $item['text'] }}</span>
			@if($item['linkUrl'] !== '')</a>@else</span>@endif
		</li>
	@endforeach
</ul>
<style>
#{{ $advanced['id'] }}.pb-icon-list{display:flex;flex-direction:column;align-items:var(--pb-icon-list-align);gap:var(--pb-icon-list-space-between);width:100%;margin:0;padding:0;list-style:none}#{{ $advanced['id'] }}.pb-icon-list.is-inline{flex-flow:row wrap;justify-content:var(--pb-icon-list-align);align-items:center}#{{ $advanced['id'] }} .pb-icon-list__item{position:relative;display:flex;align-items:var(--pb-icon-list-icon-vertical);justify-content:var(--pb-icon-list-align);min-width:0;max-width:100%}#{{ $advanced['id'] }}.pb-icon-list:not(.is-inline) .pb-icon-list__item{width:100%}#{{ $advanced['id'] }} .pb-icon-list__content{display:flex;align-items:inherit;justify-content:var(--pb-icon-list-align);min-width:0;color:inherit;text-decoration:none}#{{ $advanced['id'] }}.pb-icon-list:not(.pb-icon-list--apply-inline) .pb-icon-list__content{width:100%}#{{ $advanced['id'] }}.pb-icon-list.pb-icon-list--apply-inline .pb-icon-list__content{width:auto}#{{ $advanced['id'] }} .pb-icon-list__icon{display:inline-flex;flex:0 0 var(--pb-icon-list-icon-size);justify-content:var(--pb-icon-list-icon-align);align-self:var(--pb-icon-list-icon-vertical);width:var(--pb-icon-list-icon-size);padding-inline-end:var(--pb-icon-list-icon-gap);font-size:var(--pb-icon-list-icon-size);line-height:1;color:var(--pb-icon-list-icon-color);transform:translateY(var(--pb-icon-list-icon-offset));transition:color var(--pb-icon-list-icon-transition) ease}#{{ $advanced['id'] }} .pb-icon-list__text{min-width:0;color:var(--pb-icon-list-text-color);transition:color var(--pb-icon-list-text-transition) ease}#{{ $advanced['id'] }} .pb-icon-list__item:hover .pb-icon-list__icon{color:var(--pb-icon-list-icon-hover)}#{{ $advanced['id'] }} .pb-icon-list__item:hover .pb-icon-list__text{color:var(--pb-icon-list-text-hover)}#{{ $advanced['id'] }}.pb-icon-list.has-divider:not(.is-inline) .pb-icon-list__item:not(:last-child)::after{content:"";position:absolute;inset-inline-start:0;bottom:calc(var(--pb-icon-list-space-between) / -2);width:var(--pb-icon-list-divider-size);border-block-start:var(--pb-icon-list-divider-weight) var(--pb-icon-list-divider-style) var(--pb-icon-list-divider-color)}#{{ $advanced['id'] }}.pb-icon-list.has-divider.is-inline .pb-icon-list__item:not(:last-child)::after{content:"";position:absolute;top:50%;inset-inline-end:calc(var(--pb-icon-list-space-between) / -2);height:var(--pb-icon-list-divider-size);transform:translateY(-50%);border-inline-start:var(--pb-icon-list-divider-weight) var(--pb-icon-list-divider-style) var(--pb-icon-list-divider-color)}{!! $mediaRules.$advanced['css'] !!}
</style>