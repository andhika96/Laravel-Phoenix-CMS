@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Menus') }}
@endsection

@section('content')
	<div>
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.menu') }}
		</div>

		<div class="ph-content rounded p-3 mb-3">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('Manage Menus') }}</h4>
				</div>
			</div>
		</div>

		<div class="ph-content rounded p-4">
			<div class="row g-5 g-lg-4 mt-0 text-center">
				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/menu/be') }}" class="text-decoration-none">
						<div class="fa-layers fa-fw fa-4x d-block mx-auto mb-2">
							<i class="fad fa-send-back"></i>
						</div>

						<span class="lead">{{ t('Backend Menus') }}</span>
					</a>
				</div>

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/menu/fe') }}" class="text-decoration-none">
						<div class="fa-layers fa-fw fa-4x d-block mx-auto mb-2">
							<i class="fad fa-bring-front"></i>
						</div>

						<span class="lead">{{ t('Frontend Menus') }}</span>
					</a>
				</div>
			</div>
		</div>
	</div>
@endsection