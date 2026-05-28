<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<!-- Bootstrap CSS -->
		<link href="{{ url('assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css') }}" rel="stylesheet">

		<!-- Fontawesome -->
		<link href="{{ url('assets/plugins/fontawesome/5.15.3/css/all.min.css') }}" rel="stylesheet">

		<!-- Nunito Lato CSS -->
		<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap"> -->

		<!-- Custom CSS -->
		<link href="{{ asset('assets/css/extending-css-bootstrap-5.css?v=').time() }}" rel="stylesheet">
		<link href="{{ asset('assets/css/phoenix-cms.css?v=').time() }}" rel="stylesheet">	
		<link href="{{ asset('assets/css/aruna-v4.css?v=').time() }}" rel="stylesheet">	

		<title>LaraPhoenix CMS</title>

		<style>
		:root,
		[data-bs-theme=light] 
		{
			--ph-font-size: 14px;
			--ph-body-color: #212529;

			--ph-body-bg: #FFFFFF;
			--ph-body-color-rgb: rgba(255, 255, 255, 1);

			--ph-content-color: #000000;
			--ph-content-bg: #FFFFFF;

			/* Primary Color */

			--ph-primary: #E01D24;
			--ph-primary-rgb: rgba(224, 29, 36, 1);

			--ph-primary-link: #E01D24;

			--ph-primary-color-subtle: #E01D24;
			--ph-primary-color-subtle-2: #E01D24;

			--ph-primary-bg-subtle: #F9D2D3;
			--ph-primary-bg-subtle-2: #FCE8E9;

			--ph-primary-bg-subtle-color-mix: color-mix(in srgb, var(--ph-primary) 20%, transparent);
			--ph-primary-bg-subtle-2-color-mix: color-mix(in srgb, var(--ph-primary) 10%, transparent);

			--ph-primary-border-subtle: #F9D2D3;
			--ph-primary-border-subtle: #FCE8E9;

			--ph-primary-text-emphasis: #FFFFFF;

			--ph-primary-hover-bg: #B9181E;
			--ph-primary-hover-text: #FFFFFF;
			--ph-primary-hover-border: #B9181E;

			--ph-primary-focus-shadow-rgb: rgba(253, 243, 244, 1);

			--ph-primary-active-bg: #E01D24;
			--ph-primary-active-text: #FFFFFF;
			--ph-primary-active-border: #E01D24;
			--ph-primary-active-shadow: #FDF3F4;

			--ph-primary-disable-bg: #EB6C70;
			--ph-primary-disable-text: #FFFFFF;
			--ph-primary-disable-border: #EB6C70;

			/* Secondary Color  */

			--ph-secondary: #00A4CB;
			--ph-secondary-rgb: rgba(0, 164, 203, 1);

			--ph-secondary-color-subtle: #006B84;
			--ph-secondary-color-subtle-2: #006B84;

			--ph-secondary-bg-subtle: #CCEDF5;
			--ph-secondary-bg-subtle-2: #E5F6FA;

			--ph-secondary-bg-subtle-color-mix: color-mix(in srgb, var(--ph-secondary) 20%, transparent);
			--ph-secondary-bg-subtle-2-color-mix: color-mix(in srgb, var(--ph-secondary) 10%, transparent);
			
			--ph-secondary-border-subtle: #CCEDF5;
			--ph-secondary-border-subtle-2: #E5F6FA;
			
			--ph-secondary-text-emphasis: #FFFFFF;

			--ph-secondary-hover-bg: #0087A7;
			--ph-secondary-hover-text: #FFFFFF;
			--ph-secondary-hover-border: #0087A7;

			--ph-secondary-focus-shadow-rgb: rgba(242, 250, 252, 1);

			--ph-secondary-active-bg: #00A4CB;
			--ph-secondary-active-text: #FFFFFF;
			--ph-secondary-active-border: #00A4CB;
			--ph-secondary-active-shadow: #F2FAFC;

			--ph-secondary-disable-bg: #9BDBEA;
			--ph-secondary-disable-text: #FFFFFF;
			--ph-secondary-disable-border: #9BDBEA;

			/* Callout Toast */

			--ph-callout-bg: #FFFFFF;
			--ph-callout-color: #000000;

			--ph-callout-success-color: #04BB7B;
			--ph-callout-danger-color: #DC3545;
			--ph-callout-info-color: #0DCAF0;
			--ph-callout-warning-color: #FFC107;

			/* Default Modal CRUD  */

			--ph-modal-create-width: 500px;
			--ph-modal-read-width: 500px;
			--ph-modal-update-width: 500px;
			--ph-modal-delete-width: 425px;

			/* Toast */

			--ph-toast-success: #04bb7b;
			--ph-toast-success-rgb: 4, 187, 123;
			--ph-toast-danger: #dc3545;
			--ph-toast-danger-rgb: 220, 53, 69;
			--ph-toast-info: #0dcaf0;
			--ph-toast-info-rgb: 13, 202, 240;
			--ph-toast-warning: #ffc107;
			--ph-toast-warning-rgb: 255, 193, 7;

			/* Default Header Background Color  */

			--page-header-background: #EC777B;

			/* Other Variable CSS */

			--vs-selected-bg: #F0F0F0;
			--vs-controls-color: rgba(60, 60, 60, 0.5);

			--vs-dropdown-bg: #FFFFFF;
			--vs-dropdown-color: #000000;
			--vs-dropdown-option-color: #000000;

			--vs-dropdown-option--active-bg: #E01D24;
			--vs-dropdown-option--active-color: #EEEEEE;
		}

		[data-bs-theme=dark] 
		{
			--ph-body-color: #FFFFFF;

			--ph-body-bg: #212529;
			--ph-body-color-rgb: rgba(33, 37, 41, 1);

			--ph-content-color: #FFFFFF;
			--ph-content-bg: #36393D;

			/* Primary Color  */

			--ph-primary: #E01D24;
			--ph-primary-rgb: rgba(224, 29, 36, 1);

			--ph-primary-link: #EB6C71;

			--ph-primary-color-subtle: #EB6C71;
			--ph-primary-color-subtle-2: #EB6C71;

			--ph-primary-bg-subtle: #472428;
			--ph-primary-bg-subtle-2: #352429;

			--ph-primary-bg-subtle-color-mix: background-color: color-mix(in srgb, var(--ph-primary) 20%, var(--ph-primary) 0%);
			--ph-primary-bg-subtle-2-color-mix: background-color: color-mix(in srgb, var(--ph-primary) 10%, var(--ph-primary) 0%);

			--ph-primary-border-subtle: #472428;
			--ph-primary-border-subtle: #352429;

			--ph-primary-text-emphasis: #FFFFFF;

			--ph-primary-hover-bg: #FF2914;
			--ph-primary-hover-text: #FFFFFF;
			--ph-primary-hover-border: #FF2914;

			--ph-primary-focus-shadow-rgb: rgba(253, 243, 244, 1);

			--ph-primary-active-bg: #E01D24;
			--ph-primary-active-text: #FFFFFF;
			--ph-primary-active-border: #E01D24;
			--ph-primary-active-shadow: #FDF3F4;

			--ph-primary-disable-bg: #EB6C70;
			--ph-primary-disable-text: #FFFFFF;
			--ph-primary-disable-border: #EB6C70;

			/* Secondary Color  */

			--ph-secondary: #00A4CB;
			--ph-secondary-rgb: rgba(0, 164, 203, 1);

			--ph-secondary-color-subtle: #59c4dd;
			--ph-secondary-color-subtle-2: #59c4dd;

			--ph-secondary-bg-subtle: #1A3F4A;
			--ph-secondary-bg-subtle-2: #1E323A;

			--ph-secondary-bg-subtle-color-mix: color-mix(in srgb, var(--ph-secondary) 20%, transparent);
			--ph-secondary-bg-subtle-2-color-mix: color-mix(in srgb, var(--ph-secondary) 10%, transparent);
			
			--ph-secondary-border-subtle: #CCEDF5;
			--ph-secondary-border-subtle-2: #E5F6FA;
			
			--ph-secondary-text-emphasis: #FFFFFF;

			--ph-secondary-hover-bg: #0087A7;
			--ph-secondary-hover-text: #FFFFFF;
			--ph-secondary-hover-border: #0087A7;

			--ph-secondary-focus-shadow-rgb: rgba(242, 250, 252, 1);

			--ph-secondary-active-bg: #00A4CB;
			--ph-secondary-active-text: #FFFFFF;
			--ph-secondary-active-border: #00A4CB;
			--ph-secondary-active-shadow: #F2FAFC;

			--ph-secondary-disable-bg: #9BDBEA;
			--ph-secondary-disable-text: #FFFFFF;
			--ph-secondary-disable-border: #9BDBEA;

			/* Callout Toast */

			--ph-callout-bg: #000000;
			--ph-callout-color: #FFFFFF;

			--ph-callout-success-color: #04BB7B;
			--ph-callout-danger-color: #DC3545;
			--ph-callout-info-color: #0DCAF0;
			--ph-callout-warning-color: #FFC107;

			/* Toast */

			--ph-toast-success: #04bb7b;
			--ph-toast-success-rgb: 4, 187, 123;
			--ph-toast-danger: #dc3545;
			--ph-toast-danger-rgb: 220, 53, 69;
			--ph-toast-info: #0dcaf0;
			--ph-toast-info-rgb: 13, 202, 240;
			--ph-toast-warning: #ffc107;
			--ph-toast-warning-rgb: 255, 193, 7;

			/* Other Variable CSS */

			--vs-selected-bg: #676767;
			--vs-controls-color: rgba(255, 255, 255, 0.5);

			--vs-dropdown-bg: #282C34;
			--vs-dropdown-color: #FFFFFF;
			--vs-dropdown-option-color: #FFFFFF;

			--vs-dropdown-option--active-bg: #E01D24;
			--vs-dropdown-option--active-color: #EEEEEE;
		}

		body 
		{
			background-color: #F9F9F9;
		}

		.fading-effect 
		{
			display: block;
			position: absolute;
			bottom: 12px;
			left: 0;
			width: 100%;
			height: 203px;
			background: linear-gradient(180deg, transparent -30%, white 100%);
		}
		</style>
	</head>

	<body>
		<div id="ph-app-setup" class="container-fluid h-100">
			<div class="d-flex justify-content-center align-items-center my-5">
				<div style="width: 750px">
					<div class="text-center pb-2 mb-4">
						<img src="{{ asset('assets/logos/laraphoenix_colored.png') }}" class="img-fuid pb-1 mb-2" style="width: 225px">
					
						<div>
							<h4 class="fw-semibold">Welcome to LaraPhoenix CMS !</h4>
							<p class="text-secondary d-none">This is the installation page, before you use the CMS, you must install the database.</p>
						</div>
					</div>

					<div class="bg-white border rounded-4 p-4 mb-3">
						<div><i class="fas fa-info-circle fa-lg me-2"></i> This is the installation page, before you use the CMS, you must install the database.</div>
					</div>

					<div class="bg-white border rounded-4 p-4">
						<p class="mb-4">Below, you'll need to enter your database connection details. If you're unsure, contact your hosting provider. <span class="text-danger fw-semibold">The default admin account credentials will be available</span> after a successful installation.</p>

						<div id="ph-form-submit-data">

							<div class="ph-notice" v-cloak>
								<div aria-live="polite" aria-atomic="true" class="position-relative">
									<div class="toast-container position-fixed top-0 end-0 p-3">
										<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
											<div :class="'toast-header '+responseStatusToast+' pe-3 pt-3 pb-1 border-0'">
												<strong class="toast-header-title toast-header-icon me-auto">Notice</strong>
												<small>just now</small>
												<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" style="margin-right: calc(-.1 * var(--bs-toast-padding-x));"></button>
											</div>
										
											<div class="toast-body p-3 text-start">
												<div v-if="isArrayMessageAfterSubmit == 1">
													<ul class="ps-3 m-0">
														<li v-for="(item, index) in responseMessageAfterSubmit">
															@{{ item[0] }}
														</li>
													</ul>
												</div>

												<div v-else>
													@{{ responseMessageAfterSubmit }}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<form action="{{ route('cms.core.auth.setup.process') }}" method="post" @submit="submitData" ref="formHTML" custom-button-class="">
								<!-- Database Type Section -->
								<div class="row g-2 g-lg-3 mb-4 mb-lg-3 align-items-center">
									<div class="col-md-2">
										<label for="inputDatabaseType" class="col-form-label p-0">Database Type</label>
									</div>
		
									<div class="col-md-5">
										<select name="db_type" class="form-select" id="inputDatabaseType" aria-label="databaseTypeHelpInline" disabled>
											<option>Select Database Type</option>
											<option value="mysql" selected>MySQL / MariaDB</option>
										</select>
									</div>
					
									<div class="col">
										<span id="databaseTypeHelpInline" class="form-text">
											Select your database type, for now we only support MySQL.
										</span>
									</div>
								</div>

								<!-- Database Name Section -->
								<div class="row g-2 g-lg-3 mb-4 mb-lg-3 align-items-center">
									<div class="col-md-2">
										<label for="inputDatabaseName" class="col-form-label p-0">Database Name</label>
									</div>
		
									<div class="col-md-5">
										<input type="text" name="db_name" id="inputDatabaseName" class="form-control" aria-describedby="databaseNameHelpInline">
									</div>
					
									<div class="col">
										<span id="databaseNameHelpInline" class="form-text">
											The name of the database you want to use with LaraPhoenix CMS
										</span>
									</div>
								</div>

								<!-- Database Username Section -->
								<div class="row g-2 g-lg-3 mb-4 mb-lg-3 align-items-center">
									<div class="col-md-2">
										<label for="inputUsername" class="col-form-label p-0">Username</label>
									</div>
		
									<div class="col-md-5">
										<input type="text" name="db_username" id="inputUsername" class="form-control" aria-describedby="databaseUsernameHelpInline">
									</div>
					
									<div class="col">
										<span id="passwordHelpInline" class="form-text">
											Your database username
										</span>
									</div>
								</div>

								<!-- Database Password Section -->
								<div class="row g-2 g-lg-3 mb-4 mb-lg-3 align-items-center">
									<div class="col-md-2">
										<label for="inputPassword" class="col-form-label p-0">Password</label>
									</div>
		
									<div class="col-md-5">
										<input type="password" name="db_password" id="inputPassword" class="form-control" aria-describedby="passwordHelpInline">
									</div>
					
									<div class="col">
										<span id="passwordHelpInline" class="form-text">
											Your database password
										</span>
									</div>
								</div>

								<!-- Database Host Section -->
								<div class="row g-2 g-lg-3 mb-4 align-items-center">
									<div class="col-md-2">
										<label for="inputDatabaseHost" class="col-form-label p-0">Database Host</label>
									</div>
		
									<div class="col-md-5">
										<input type="text" name="db_hostname" id="inputDatabaseHost" class="form-control" aria-describedby="databaseHostHelpInline">
									</div>
					
									<div class="col">
										<span id="databaseHostHelpInline" class="form-text">
											You should be able to get this info from your web host, if localhost doesn't work
										</span>
									</div>
								</div>

								<!-- Database Host Port Section -->
								<div class="row g-2 g-lg-3 mb-4 align-items-center">
									<div class="col-md-2">
										<label for="inputDatabaseHostPort" class="col-form-label p-0">Database Port</label>
									</div>
		
									<div class="col-md-5">
										<input type="text" name="db_port" id="inputDatabaseHostPort" class="form-control" aria-describedby="inputDatabaseHostPortHelpInline">
									</div>
					
									<div class="col">
										<span id="databaseHostHelpInline" class="form-text">
											You should be able to get this info from your web host, if port 3306 doesn't work
										</span>
									</div>
								</div>

								<div class="row">
									<div class="col-6 d-flex justify-content-start align-items-center">
										<span v-html="responseMessage"></span>
									</div>

									<div class="col-6 text-end">
										<input type="submit" class="btn btn-larapx btn-submit-data" value="Install">
									</div>
								</div>

								<div v-if="responseMessageError !== ''" class="position-relative border rounded-4 p-4 mt-3" v-cloak>
									<div class="h5 mb-3 text-danger">Error:</div>
									<div v-html="responseMessageError"></div>
									<div v-if="responseMessageError.length > 1150" class="fading-effect"></div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script src="{{ url('assets/plugins/bootstrap/5.3.6/js/bootstrap.bundle.min.js') }}"></script>
		<script src="{{ url('assets/plugins/fontawesome/5.15.3/js/all.min.js') }}"></script>

		<script src="{{ url('assets/plugins/axios/v1/1.7.7.js') }}"></script>
		<script src="{{ url('assets/plugins/lodash/lodash.4.17.21.min.js') }}"></script>

		<script src="{{ url('assets/plugins/vue/core/v3/vue.3.5.17.global.prod.min.js') }}"></script>		
		<script src="{{ url('assets/plugins/vue/plugins/vue-debounce/js/vue-debounce.5.0.0.min.js') }}"></script>	

		<script type="text/javascript">
			const { createApp, ref, reactive, defineModel } = Vue;
		</script>

		@stack('js')

		<script src="{{ url('assets/js/vueV3-setup.js?v=').time() }}"></script>
	</body>
</html>
