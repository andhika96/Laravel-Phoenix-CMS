@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('File Manager') }}
@endsection

@section('content')
	<style>
	.ph-content-filemanager
	{
		border-width: 0 !important;
	}

	.ui-mobile
	{
		display: none;
	}

	.ui-mobile .ui-header
	{
		border-top-left-radius: .375rem;
		border-top-right-radius: .375rem;
	}

	.ui-mobile .ckf-panel-contents
	{
		border-bottom: 1px #d7dcdf solid !important;
	}

	.ui-mobile .ckf-files-info
	{
		border-right: 1px #d7dcdf solid !important;
		border-bottom: 1px #d7dcdf solid !important;
	}
	</style>

	<div>
		<div class="mb-3">		
			{{ Breadcrumbs::render('awesome_admin.file_manager') }}
		</div>

		<div class="ph-content p-3 mb-3 rounded">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('File Manager') }}</h4>
				</div>
			</div>
		</div>

		<div class="ph-content ph-content-filemanager ratio ratio-16x9">
			<iframe src="{{ url('assets/plugins/ckfinder/ckfinder.html') }}" class="rounded border-0 w-100 h-100 embed-responsive-item"></iframe>
		</div>
	</div>
@endsection