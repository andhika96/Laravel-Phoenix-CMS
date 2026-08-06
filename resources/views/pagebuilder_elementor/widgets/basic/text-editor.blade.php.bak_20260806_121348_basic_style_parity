@php
	$settings = $node['settings'] ?? [];
	$customClass = implode(' ', array_filter(array_map(
		fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')),
		preg_split('/\s+/', trim((string) ($settings['cssClass'] ?? ''))) ?: []
	)));
	$className = trim(implode(' ', array_filter(['el-widget-text-editor', $customClass])));
@endphp
<div class="{{ $className }}">{!! $settings['html'] ?? '' !!}</div>
