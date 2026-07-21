@php
	$settings = $node['settings'] ?? [];
	$align = $settings['align'] ?? 'left';
	$wrapStyle = 'display:flex;justify-content:' . ($align === 'center' ? 'center' : ($align === 'right' ? 'flex-end' : 'flex-start'));
@endphp
<div style="{{ $wrapStyle }}">
	<a href="{{ $settings['url'] ?? '#' }}" class="el-widget-button {{ $settings['className'] ?? 'btn btn-primary' }}" @if(!empty($settings['newTab'])) target="_blank" rel="noopener" @endif>{{ $settings['text'] ?? 'Click here' }}</a>
</div>
