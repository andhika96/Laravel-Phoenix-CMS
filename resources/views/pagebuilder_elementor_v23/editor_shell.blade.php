@php
	$pbElementorCustomFonts = [];
	$pbElementorFontDirectories = \Illuminate\Support\Facades\Storage::exists('public/fonts')
		? \Illuminate\Support\Facades\Storage::directories('public/fonts')
		: [];
	foreach ($pbElementorFontDirectories as $fontDirectory) {
		$fontCode = basename(str_replace('\\', '/', $fontDirectory));
		$fontCssPath = 'public/fonts/' . $fontCode . '/fonts.css';
		if (!preg_match('/^[A-Za-z0-9_-]+$/', $fontCode) || !\Illuminate\Support\Facades\Storage::exists($fontCssPath)) continue;

		$fontCss = \Illuminate\Support\Facades\Storage::get($fontCssPath);
		preg_match("/font-family\\s*:\\s*(['\"])(.*?)\\1\\s*;/i", $fontCss, $fontMatches);
		$fontFamily = trim((string) ($fontMatches[2] ?? \Illuminate\Support\Str::headline($fontCode)));
		if ($fontFamily === '' || !preg_match('/^[A-Za-z0-9 _-]+$/', $fontFamily)) continue;

		$pbElementorCustomFonts[] = [
			'label' => $fontFamily,
			'value' => $fontFamily,
			'group' => 'custom',
			'stylesheet' => asset('storage/fonts/' . $fontCode . '/fonts.css'),
		];
	}
	usort($pbElementorCustomFonts, fn (array $left, array $right): int => strnatcasecmp($left['label'], $right['label']));
	$pbElementorFontFamilies = [['label' => 'Default', 'value' => 'inherit', 'group' => 'default']];
	foreach ($pbElementorCustomFonts as $font) {
		$pbElementorFontFamilies[] = ['label' => $font['label'], 'value' => $font['value'], 'group' => $font['group']];
	}
	foreach (['Arial', 'Tahoma', 'Verdana', 'Georgia', 'Times New Roman', 'Courier New'] as $fontFamily) {
		$pbElementorFontFamilies[] = ['label' => $fontFamily, 'value' => $fontFamily, 'group' => 'system'];
	}
@endphp
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Page Builder Elementor Style</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
	<link href="{{ asset('assets/plugins/fontawesome/5.15.3/css/all.min.css') }}?v={{ @filemtime(public_path('assets/plugins/fontawesome/5.15.3/css/all.min.css')) }}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	@foreach($pbElementorCustomFonts as $font)
	<link href="{{ $font['stylesheet'] }}" rel="stylesheet">
	@endforeach
	<link href="{{ asset('assets/vendor/pb-picker/picker.min.css') }}?v={{ @filemtime(public_path('assets/vendor/pb-picker/picker.min.css')) }}" rel="stylesheet">
	<link href="{{ asset('assets/css/frontend_elementor_v23.css') }}?v={{ @filemtime(public_path('assets/css/frontend_elementor_v23.css')) }}" rel="stylesheet">
	<link href="{{ asset('assets/css/pagebuilder_elementor_v23.css') }}?v={{ @filemtime(public_path('assets/css/pagebuilder_elementor_v23.css')) }}" rel="stylesheet">
</head>
<body>
	<div id="pbElementorV23App" v-cloak></div>

	<script>
		window.PAGE_BUILDER_ELEMENTOR_V23_CONTEXT = {
			mode: @json($mode),
			editorVersion: '2.3',
			saveUrl: @json($saveUrl),
			csrfToken: @json(csrf_token()),
			pageData: @json($pageData),
			imageRenditionUrl: @json(route('cms.core.pagebuilder_elementor_v23.image_rendition')),
			previewUrl: @json($pageData ? route('cms.core.pagebuilder_elementor_v23.preview', $pageData->uri) : ''),
			dynamicPreviewContext: {
				page_excerpt: @json((string) ($pageData->description ?? $pageData->excerpt ?? '')),
				featured_image: @json((string) ($pageData->featured_image ?? $pageData->cover_image ?? '')),
				page_url: @json($pageData ? route('cms.core.pagebuilder_elementor_v23.preview', $pageData->uri) : ''),
				site_title: @json(config('app.name')),
				site_url: @json(config('app.url')),
				user_display_name: @json((string) (auth()->user()->name ?? '')),
			},
		};
		window.PB_ELEMENTOR_V23_SHAPES = @json(json_decode(file_get_contents(resource_path('data/pagebuilder_elementor_v23_shapes.json')), true));
		window.PB_ELEMENTOR_V23_FONT_FAMILIES = @json($pbElementorFontFamilies);
	</script>

	<script src="https://cdn.jsdelivr.net/npm/vue@3.4.38/dist/vue.global.prod.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios@1.7.4/dist/axios.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/vuedraggable@4.1.0/dist/vuedraggable.umd.min.js"></script>
	<script src="{{ asset('assets/vendor/pb-picker/picker.min.js') }}?v={{ @filemtime(public_path('assets/vendor/pb-picker/picker.min.js')) }}"></script>
	<script src="{{ asset('assets/plugins/ckfinder/ckfinder.js') }}"></script>
	<script src="{{ asset('assets/plugins/ckeditor5/build/ckeditor.js') }}?v=0.0.1"></script>
	<script src="https://cdn.jsdelivr.net/npm/vue3-sfc-loader@0.8.4/dist/vue3-sfc-loader.js"></script>
	<script src="{{ asset('js/pagebuilder_elementor_v23/frontend-runtime.js') }}?v={{ @filemtime(public_path('js/pagebuilder_elementor_v23/frontend-runtime.js')) }}"></script>
	<script src="{{ asset('js/pagebuilder_elementor_v23/widget-registry.js') }}?v={{ @filemtime(public_path('js/pagebuilder_elementor_v23/widget-registry.js')) }}"></script>
	@foreach(config('pagebuilder_elementor_v23_widgets', []) as $pbElementorWidget)
	@php($pbElementorDefinition = $pbElementorWidget['definition'])
	<script src="{{ asset($pbElementorDefinition) }}?v={{ @filemtime(public_path($pbElementorDefinition)) }}"></script>
	@endforeach
	<script src="{{ asset('js/pagebuilder_elementor_v23/app.js') }}?v={{ @filemtime(public_path('js/pagebuilder_elementor_v23/app.js')) }}"></script>
</body>
</html>
