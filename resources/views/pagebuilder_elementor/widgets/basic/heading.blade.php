@php
	$settings = $node['settings'] ?? [];
	$tag = in_array($settings['tag'] ?? 'h2', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div'], true)
		? $settings['tag']
		: 'h2';
	$customClass = implode(' ', array_filter(array_map(
		fn ($token) => preg_replace('/[^A-Za-z0-9_-]/', '', ltrim((string) $token, '.')),
		preg_split('/\s+/', trim((string) ($settings['cssClass'] ?? ''))) ?: []
	)));
	$style = implode(';', array_filter([
		'text-align:' . ($settings['align'] ?? 'left'),
		'color:' . ($settings['color'] ?? '#101828'),
		!empty($settings['fontSize']) ? 'font-size:' . $settings['fontSize'] : '',
		!empty($settings['fontWeight']) ? 'font-weight:' . $settings['fontWeight'] : '',
	]));
	$className = trim(implode(' ', array_filter(['el-widget-heading', $customClass])));
@endphp
<{{ $tag }} class="{{ $className }}" style="{{ $style }}">
	{!! $settings['text'] ?? '' !!}
</{{ $tag }}>
