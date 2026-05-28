<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Page Builder Elementor Style</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
	</script>

	<script src="https://cdn.jsdelivr.net/npm/vue@3.4.38/dist/vue.global.prod.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios@1.7.4/dist/axios.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/vuedraggable@4.1.0/dist/vuedraggable.umd.min.js"></script>
	<script src="{{ asset('assets/plugins/ckfinder/ckfinder.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@41.4.2/build/ckeditor.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/vue3-sfc-loader@0.8.4/dist/vue3-sfc-loader.js"></script>
	<script src="{{ asset('js/pagebuilder_elementor/app.js') }}?v={{ @filemtime(public_path('js/pagebuilder_elementor/app.js')) }}"></script>
</body>
</html>
