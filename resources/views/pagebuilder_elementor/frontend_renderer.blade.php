<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $pageData->page_name ?? 'Page' }}</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	{{-- SAMA PERSIS dengan canvas builder — WYSIWYG terjamin --}}
	<link href="{{ asset('assets/css/frontend_elementor.css') }}?v={{ @filemtime(public_path('assets/css/frontend_elementor.css')) }}" rel="stylesheet">

	@if(!empty($pageData->custom_css))
	<style>
		{!! $pageData->custom_css !!}
	</style>
	@endif
</head>
<body>

<div class="el-page-wrapper">
	@foreach($nodes as $node)
		@include('pagebuilder_elementor.partials.render_node', ['node' => $node])
	@endforeach
</div>

</body>
</html>
