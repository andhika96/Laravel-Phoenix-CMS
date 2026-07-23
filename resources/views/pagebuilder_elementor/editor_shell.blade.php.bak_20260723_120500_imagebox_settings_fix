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
	<link href="{{ asset('assets/plugins/fontawesome/5.15.3/css/all.min.css') }}?v={{ @filemtime(public_path('assets/plugins/fontawesome/5.15.3/css/all.min.css')) }}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	@foreach($pbElementorCustomFonts as $font)
	<link href="{{ $font['stylesheet'] }}" rel="stylesheet">
	@endforeach
	<link href="{{ asset('assets/vendor/pb-picker/picker.min.css') }}?v={{ @filemtime(public_path('assets/vendor/pb-picker/picker.min.css')) }}" rel="stylesheet">
	<link href="{{ asset('assets/css/frontend_elementor.css') }}?v={{ @filemtime(public_path('assets/css/frontend_elementor.css')) }}" rel="stylesheet">
	<link href="{{ asset('assets/css/pagebuilder_elementor.css') }}?v={{ @filemtime(public_path('assets/css/pagebuilder_elementor.css')) }}" rel="stylesheet">
</head>
<body>
	<div id="pbElementorApp" v-cloak></div>

	<script>
		window.PAGE_BUILDER_ELEMENTOR_CONTEXT = {
			mode: @json($mode),
			saveUrl: @json($saveUrl),
			csrfToken: @json(csrf_token()),
			pageData: @json($pageData),
		};
		window.PB_ELEMENTOR_SHAPES = @json(json_decode(file_get_contents(resource_path('data/pagebuilder_elementor_shapes.json')), true));
		window.PB_ELEMENTOR_FONT_FAMILIES = @json($pbElementorFontFamilies);
	</script>

	<script src="https://cdn.jsdelivr.net/npm/vue@3.4.38/dist/vue.global.prod.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios@1.7.4/dist/axios.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/vuedraggable@4.1.0/dist/vuedraggable.umd.min.js"></script>
	<script src="{{ asset('assets/vendor/pb-picker/picker.min.js') }}?v={{ @filemtime(public_path('assets/vendor/pb-picker/picker.min.js')) }}"></script>
	<script src="{{ asset('assets/plugins/ckfinder/ckfinder.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@41.4.2/build/ckeditor.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/vue3-sfc-loader@0.8.4/dist/vue3-sfc-loader.js"></script>
	<script src="{{ asset('js/pagebuilder_elementor/frontend-runtime.js') }}?v={{ @filemtime(public_path('js/pagebuilder_elementor/frontend-runtime.js')) }}"></script>
	<script src="{{ asset('js/pagebuilder_elementor/widget-registry.js') }}?v={{ @filemtime(public_path('js/pagebuilder_elementor/widget-registry.js')) }}"></script>
	@foreach(config('pagebuilder_elementor_widgets', []) as $pbElementorWidget)
	<script src="{{ asset($pbElementorWidget['definition']) }}?v={{ @filemtime(public_path($pbElementorWidget['definition'])) }}"></script>
	@endforeach
	<script src="{{ asset('js/pagebuilder_elementor/app.js') }}?v={{ @filemtime(public_path('js/pagebuilder_elementor/app.js')) }}"></script>
</body>
</html>
