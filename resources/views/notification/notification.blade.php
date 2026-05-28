@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Notifications') }}
@endsection

@section('content')
	<style>
		.arv7-list-notification .list-group-item
		{
			padding-left: 0 !important;
			padding-top: .75rem;
			padding-right: 0 !important;
			padding-bottom: .75rem;
			border: 0 !important;
		}
	</style>

	<div id="ph-app-notification">
		<div class="mb-4">
			<h4 class="mb-3"><i class="fad fa-bell fa-fw me-1"></i> {{ t('Notifications') }}</h4>
		</div>

		<div class="bg-white rounded p-4">
			<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="pills-general-tab" data-bs-toggle="pill" data-bs-target="#pills-general" type="button" role="tab" aria-controls="pills-general" aria-selected="true">{{ t('General') }}</button>
				</li>
				
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="pills-system-tab" data-bs-toggle="pill" data-bs-target="#pills-system" type="button" role="tab" aria-controls="pills-system" aria-selected="false">{{ t('System') }}</button>
				</li>
			</ul>

			<div>
				<div class="list-group list-group-flush arv7-list-notification">
					<a href="#!" class="list-group-item">
						<div class="d-flex align-items-center">
							<div class="flex-shrink-0">
								<img src="https://placehold.co/55/E01D24/FFF" alt="Default avatar image" class="rounded-circle">
							</div>
							
							<div class="flex-grow-1 ms-3">
								<div><span class="h6">Andhika</span> has assign to new rule</div>
								<div>5 mins ago</div>
							</div>
						</div>
					</a>

					<a href="#!" class="list-group-item">
						<div class="d-flex align-items-center">
							<div class="flex-shrink-0">
								<img src="https://placehold.co/55/E01D24/FFF" alt="Default avatar image" class="rounded-circle">
							</div>
							
							<div class="flex-grow-1 ms-3">
								<div><span class="h6">Aruna Swastamitha</span> registered</div>
								<div>15 mins ago</div>
							</div>
						</div>
					</a>

					<a href="#!" class="list-group-item">
						<div class="d-flex align-items-center">
							<div class="flex-shrink-0">
								<img src="https://placehold.co/55/E01D24/FFF" alt="Default avatar image" class="rounded-circle">
							</div>
							
							<div class="flex-grow-1 ms-3">
								<div><span class="h6">Rajo Langit</span> has assign to new rule</div>
								<div>5 mins ago</div>
							</div>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vueV3-notification-2025.js?v=').time() }}"></script>
@endpushonce