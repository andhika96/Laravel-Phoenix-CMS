@php
	$pbElementorFontStylesheets = [];
	$pbElementorFontDirectories = \Illuminate\Support\Facades\Storage::exists('public/fonts')
		? \Illuminate\Support\Facades\Storage::directories('public/fonts')
		: [];
	foreach ($pbElementorFontDirectories as $fontDirectory) {
		$fontCode = basename(str_replace('\\', '/', $fontDirectory));
		$fontCssPath = 'public/fonts/' . $fontCode . '/fonts.css';
		if (!preg_match('/^[A-Za-z0-9_-]+$/', $fontCode) || !\Illuminate\Support\Facades\Storage::exists($fontCssPath)) continue;
		$pbElementorFontStylesheets[] = asset('storage/fonts/' . $fontCode . '/fonts.css');
	}
	$pbElementorFontStylesheets = array_values(array_unique($pbElementorFontStylesheets));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $pageData->page_name ?? 'Page' }}</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	@foreach($pbElementorFontStylesheets as $fontStylesheet)
	<link href="{{ $fontStylesheet }}" rel="stylesheet">
	@endforeach
	<link href="{{ asset('assets/plugins/fontawesome/5.15.3/css/all.min.css') }}?v={{ @filemtime(public_path('assets/plugins/fontawesome/5.15.3/css/all.min.css')) }}" rel="stylesheet">
	{{-- SAMA PERSIS dengan canvas builder — WYSIWYG terjamin --}}
	<link href="{{ asset('assets/css/frontend_elementor_v23.css') }}?v={{ @filemtime(public_path('assets/css/frontend_elementor_v23.css')) }}" rel="stylesheet">

	@if(!empty($pageData->custom_css))
	<style>
		{!! $pageData->custom_css !!}
	</style>
	@endif
</head>
<body>

<div class="el-page-wrapper">
	@foreach($nodes as $node)
		@include('pagebuilder_elementor_v23.partials.render_node', ['node' => $node])
	@endforeach
</div>

<script src="{{ asset('js/pagebuilder_elementor_v23/frontend-runtime.js') }}?v={{ @filemtime(public_path('js/pagebuilder_elementor_v23/frontend-runtime.js')) }}"></script>

</body>
</html>
