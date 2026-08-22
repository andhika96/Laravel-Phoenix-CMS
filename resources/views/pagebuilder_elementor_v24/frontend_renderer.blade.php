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
	$pbModuleCatalog = app(\App\Support\PageBuilderElementorV24\ModuleCatalog::class);
	$pbUsedModuleTypes = app(\App\Support\PageBuilderElementorV24\ModuleUsageCollector::class)->types(is_array($nodes ?? null) ? $nodes : []);
	$pbUsedModuleAssets = [];
	foreach ($pbUsedModuleTypes as $pbUsedModuleType) {
		$pbUsedModule = $pbModuleCatalog->find($pbUsedModuleType);
		if (!is_array($pbUsedModule)) continue;
		$pbUsedModuleAssets[$pbUsedModuleType] = $pbUsedModule['assets'];
	}
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
	<link href="{{ asset('assets/css/frontend_elementor_v24.css') }}?v={{ @filemtime(public_path('assets/css/frontend_elementor_v24.css')) }}" rel="stylesheet">
	@foreach($pbUsedModuleAssets as $pbUsedModuleType => $pbUsedAssets)
	@if(isset($pbUsedAssets['styles']) && is_file($pbUsedAssets['styles']))
	<style data-pb-module-style="{{ $pbUsedModuleType }}">{!! file_get_contents($pbUsedAssets['styles']) !!}</style>
	@endif
	@endforeach

	@if(!empty($pageData->custom_css))
	<style>
		{!! $pageData->custom_css !!}
	</style>
	@endif
</head>
<body>

<div class="el-page-wrapper">
	@foreach($nodes as $node)
		@include('pagebuilder_elementor_v24.partials.render_node', ['node' => $node])
	@endforeach
</div>

<script src="{{ asset('js/pagebuilder_elementor_v24/frontend-runtime.js') }}?v={{ @filemtime(public_path('js/pagebuilder_elementor_v24/frontend-runtime.js')) }}"></script>
@foreach($pbUsedModuleAssets as $pbUsedModuleType => $pbUsedAssets)
@if(isset($pbUsedAssets['runtime']) && is_file($pbUsedAssets['runtime']))
<script data-pb-module-runtime="{{ $pbUsedModuleType }}">{!! file_get_contents($pbUsedAssets['runtime']) !!}</script>
@endif
@endforeach

</body>
</html>
