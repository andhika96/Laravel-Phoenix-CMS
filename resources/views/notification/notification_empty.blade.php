@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Notifications') }}
@endsection

@section('content')
	<div id="ph-app-notification">
		<div class="mb-4">
			<h4 class="mb-3"><i class="fad fa-bell fa-fw me-1"></i> {{ t('Notifications') }}</h4>
		</div>

		<div class="bg-white rounded p-4 d-flex justify-content-center align-self-center">
			<div class="d-flex justify-content-center align-items-center p-2 vh-100">
				<div class="text-center">
					<i class="fad fa-bell-exclamation fa-8x mb-3"></i>
					<div class="h4">{{ t("It's quite for now") }}</div>
					<div class="fs-6 mb-0">{{ t("You notifications will appear here once there's something new to review.") }}</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vueV3-notification-2025.js?v=').time() }}"></script>
@endpushonce