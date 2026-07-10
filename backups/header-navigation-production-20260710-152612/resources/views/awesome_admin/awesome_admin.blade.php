@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Awesome Admin Panel') }}
@endsection

@section('content')
	<div>
		<div class="ph-content p-3 mb-3 rounded">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('Awesome Admin Panel') }}</h4>
				</div>
			</div>
		</div>

		<div class="ph-content p-4 rounded">
			<div class="row g-5 g-lg-4 mt-0 text-center">
				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/config') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-cogs fa-3x"></i>
						</div>

						<span class="lead">{{ t('Site Settings') }}</span>
					</a>
				</div>

				<div class="col-6 col-md-3 col-xl-2 d-none">
					<a href="{{ url('awesome_admin/modules') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-puzzle-piece fa-3x"></i>
						</div>

						<span class="lead">Modules</span>
					</a>
				</div>

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/menu') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-users-class fa-3x"></i>
						</div>

						<span class="lead">{{ t('Manage Menu Access') }}</span>
					</a>
				</div>

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/user') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-users fa-3x"></i>
						</div>

						<span class="lead">{{ t('Manage Users') }}</span>
					</a>
				</div>

			@if (site_config()->management_menu == 'v1')

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/role') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-project-diagram fa-3x"></i>
						</div>

						<span class="lead">{{ t('Manage Roles') }}</span>
					</a>
				</div>

			@elseif (site_config()->management_menu == 'v2')

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/role') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-project-diagram fa-3x"></i>
						</div>

						<span class="lead">{{ t('Manage Roles') }}</span>
					</a>
				</div>

			@endif

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/permission') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-chart-network fa-3x"></i>
						</div>

						<span class="lead">{{ t('Manage Permissions') }}</span>
					</a>
				</div>

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/smtp') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-envelope fa-3x"></i>
						</div>

						<span class="lead">{{ t('SMTP Settings') }}</span>
					</a>
				</div>

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/language') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-language fa-3x"></i>
						</div>

						<span class="lead">{{ t('Manage Languages') }}</span>
					</a>
				</div>

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/appearance') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-puzzle-piece fa-3x"></i>
						</div>

						<span class="lead">{{ t('Manage Appearance') }}</span>
					</a>
				</div>

				<div class="col-6 col-md-3 col-xl-2">
					<a href="{{ url('awesome_admin/filemanager') }}" class="text-decoration-none">
						<div class="d-block mb-2">
							<i class="fad fa-file-alt fa-3x"></i>
						</div>

						<span class="lead">{{ t('File Manager') }}</span>
					</a>
				</div>
			</div>
		</div>
	</div>
@endsection