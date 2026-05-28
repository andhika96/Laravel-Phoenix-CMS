@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Language') }}
@endsection

@section('content')
	<div>
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.language') }}
		</div>

		<div class="ph-content rounded p-3 mb-3">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('Manage Languages') }}</h4>
				</div>
			</div>
		</div>

		<div class="ph-content rounded p-4">
			<div class="arv6-header d-lg-flex justify-content-lg-between align-items-lg-center pb-3 mb-2 border-bottom">
				<div class="mt-3 mt-lg-0">
					<div class="row g-3">
						<div class="col-auto">
							{!! $listLanguageHTML !!}
						</div>
					</div>
				</div>
			</div>

			<div class="row g-5 g-lg-4 mt-0 text-center">
				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/language/untranslated') }}" class="text-decoration-none">
						<div class="fa-layers fa-fw fa-4x d-block mx-auto mb-2">
							<i class="fad fa-language"></i>
							<span class="fa-layers-counter fa-layers-top-right bg-danger shadow-sm" style="right: -11px"><i class="fas fa-times"></i></span>
						</div>

						<span class="lead">{{ t('Untranslated') }}</span>
					</a>
				</div>

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/language/translated') }}" class="text-decoration-none">
						<div class="fa-layers fa-fw fa-4x d-block mx-auto mb-2">
							<i class="fad fa-language"></i>
							<span class="fa-layers-counter fa-layers-top-right bg-success shadow-sm" style="right: -11px"><i class="fas fa-check"></i></span>
						</div>

						<span class="lead">{{ t('Translated') }}</span>
					</a>
				</div>
			</div>
		</div>
	</div>
@endsection

@pushonce('js')
	<script src="{{ url('assets/plugins/fontawesome/5.15.3/js/all.min.js') }}"></script>
@endpushonce