@php
	$settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$advanced = app(\App\Support\PageBuilderElementorV24\WidgetAdvancedStyleResolver::class)->resolve($settings, (string) ($node['id'] ?? 'static-html'), request());
	$srcdoc = (string) ($settings['srcdoc'] ?? '');
	$title = trim((string) ($settings['title'] ?? 'Imported page exact preview')) ?: 'Imported page exact preview';
	$height = preg_match('/^\d{2,5}px$/', trim((string) ($settings['height'] ?? ''))) ? trim((string) $settings['height']) : '1200px';
	$customJavaScript = app(\App\Support\PageBuilderElementorV24\CustomJavaScriptPolicy::class)->normalize(
		data_get($pageData ?? null, 'custom_js', ''),
		data_get($pageData ?? null, 'custom_js_mode', 'disabled'),
	);
	$renderSandboxJavaScript = $customJavaScript['mode'] === 'exact_sandbox'
		&& $customJavaScript['blocked'] === []
		&& $customJavaScript['code'] !== '';
	if ($renderSandboxJavaScript) {
		$sandboxCode = preg_replace('/<\/script/i', '<\\/script', $customJavaScript['code']) ?? '';
		$sandboxScript = '<script data-pb-custom-javascript="sandbox">'.$sandboxCode.'</script>';
		$srcdoc = str_contains(strtolower($srcdoc), '</body>')
			? preg_replace('/<\/body>/i', $sandboxScript.'</body>', $srcdoc, 1)
			: $srcdoc.$sandboxScript;
	}
@endphp
<div id="{{ $advanced['id'] }}" class="pb-static-html-preview {{ implode(' ', $advanced['classes']) }}" data-pb-static-html="true" data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}">
	<iframe srcdoc="{{ $srcdoc }}" title="{{ $title }}" sandbox="allow-scripts" @if($renderSandboxJavaScript) data-pb-custom-javascript="sandbox" @endif style="height:{{ $height }}"></iframe>
</div>
<style>{!! $advanced['css'] !!}</style>
