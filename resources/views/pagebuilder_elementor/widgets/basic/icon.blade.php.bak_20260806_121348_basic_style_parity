@php
	$settings = $node['settings'] ?? [];
	$cleanClasses = fn ($value) => implode(' ', array_filter(array_map(fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')), preg_split('/\s+/', trim((string) $value)) ?: [])));
	$customClass = $cleanClasses($settings['cssClass'] ?? '');
	$iconClass = $cleanClasses($settings['iconClass'] ?? '') ?: 'far fa-star';
	$view = in_array(($candidate = strtolower(trim((string) ($settings['view'] ?? 'default')))), ['default', 'stacked', 'framed'], true) ? $candidate : 'default';
	$shape = in_array(($candidate = strtolower(trim((string) ($settings['shape'] ?? 'circle')))), ['circle', 'rounded', 'square'], true) ? $candidate : 'circle';
	$link = trim((string) ($settings['link'] ?? ''));
	$openInNewWindow = filter_var($settings['openInNewWindow'] ?? false, FILTER_VALIDATE_BOOLEAN);
	$nofollow = filter_var($settings['nofollow'] ?? false, FILTER_VALIDATE_BOOLEAN);
	$relAttr = implode(' ', array_filter([$openInNewWindow ? 'noopener noreferrer' : '', $nofollow ? 'nofollow' : '']));
	$attrBag = [];
	foreach (($settings['attributes'] ?? []) as $attribute) {
		$name = trim((string) ($attribute['name'] ?? ''));
		if (preg_match('/^(data-[A-Za-z0-9_.:-]+|aria-[A-Za-z0-9_.:-]+|title)$/', $name)) $attrBag[$name] = (string) ($attribute['value'] ?? '');
	}
	$className = trim(implode(' ', array_filter(['el-widget-icon', 'is-view-' . $view, $view !== 'default' ? 'is-shape-' . $shape : '', $customClass])));
@endphp
<div class="{{ $className }}">
	@if($link !== '')<a href="{{ $link }}" class="el-widget-icon-link" @if($openInNewWindow) target="_blank" @endif @if($relAttr !== '') rel="{{ $relAttr }}" @endif @foreach($attrBag as $attrName => $attrValue) {{ $attrName }}="{{ e($attrValue) }}" @endforeach>
	@else<span class="el-widget-icon-link" @foreach($attrBag as $attrName => $attrValue) {{ $attrName }}="{{ e($attrValue) }}" @endforeach>@endif
		@if($view !== 'default')<span class="el-widget-icon-box"><i class="{{ $iconClass }}" aria-hidden="true"></i></span>@else<i class="{{ $iconClass }}" aria-hidden="true"></i>@endif
	@if($link !== '')</a>@else</span>@endif
</div>
