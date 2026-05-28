@extends('themes.'.custom_theme('frontend'))

@section('title')
	{{ t('Homepage') }}
@endsection

@section('content')
	<div>
		<div class="d-none">
			<div class="ph-cover-only-image position-relative d-none d-md-block">
				<img src="{{ asset('assets/images/pexels-visually-us-1643402-cropped.png') }}" class="img-fluid w-100">

				<div class="ph-container-fluid-coverimage position-absolute top-0 p-5 w-100 h-100 text-white" style="z-index: 2">
					<div class="position-absolute p-5 {{ coverImageContentPositionDesktopH('bottom-left') }}">
						<div class="row">
							<div class="{{ coverImageContentGridPosition('bottom-left') }}">
								<h2>Hello World!</h2>
								<h4 class="font-weight-light mb-3">Bootstrap v5.3.8 is out with a reversion for a dropdown focus bug, some CSS updates, and several docs updates.</h4>

								<div class="ph-coverimage-button-area">												
									<a href="https://getbootstrap.com/" class="btn btn-outline-light rounded-pill">Link 1</a><a href="https://getbootstrap.com/" class="btn btn-outline-light rounded-pill">Link 2</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="ph-cover-image-filter"></div>
			</div>
		</div>
	</div>

	<x-phoenix-slider uri="homepage2" slug="homepage2"></x-phoenix-slider>
@endsection