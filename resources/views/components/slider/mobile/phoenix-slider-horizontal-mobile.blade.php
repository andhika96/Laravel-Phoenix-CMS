<!-- Splide CSS -->
<link href=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css " rel="stylesheet">

<style>
.splide 
{
	.splide__track 
	{
		transition: height 0.3s ease-in-out;
	}
}

.splide 
{
	.splide__track 
	{
		.splide__list 
		{
			align-items: flex-start !important;
		}
	}
}

.splide__pagination 
{
	bottom: 0;
	padding: 2.5rem .5em;
	position: absolute;
	right: 0;
	z-index: 1;
}

.splide__pagination__page 
{
	background: transparent;
	border: 1px #FFFFFF solid;
	border-radius: 50%;
	display: inline-block;
	height: 10px;
	margin: 4px;
	opacity: 1;
	padding: 0;
	position: relative;
	transition: transform .2s linear;
	width: 10px;
}

.splide__pagination__page.is-active
{
	transform: none;
}

/*.splide__slide:not(.is-active) 
{
	height: 0 !important;
}*/
</style>

@php
	$getAutoPlay 			= ($data->cover_autoplay_slideshow == 'active') ? 'true' : 'false';
	$getAutoPlayInterval 	= ($data->cover_autoplay_slideshow == 'active') ? $data->cover_autoplay_slideshow_interval : 3000;
	$getSlideshowLoop 		= ($data->cover_looping_slideshow == 'active') ? 'loop' : 'slide';
@endphp

<div class="position-relative" id="ph-app-coverimage">
	<!-- Mobile View -->
	<!-- Slider main container -->
	<div class="splide">
		<!-- Additional required wrapper -->
		<div class="splide__track">
			<!-- Slides -->
			<ul class="splide__list">
				@foreach (json_decode($data['cover_slideshow_vars'], true) as $slideshow)
					@if ($slideshow['cover_is_active'] == 'active')
						<li class="splide__slide" data-splide-interval="{{ $getAutoPlayInterval }}">
							<div class="{{ getClassBackgroundImageSize($slideshow['background_size']) }} position-relative d-block d-md-none">
								<img src="{{ asset('storage/'.$slideshow['mobile_image']) }}" class="img-fluid w-100 d-none">
							
								<div class="ph-background {{ getBackgroundImageSize($slideshow['background_size']) }}" style="background-image: url('{{ asset('storage/'.$slideshow['mobile_image']) }}') {{ getBackgroundImageSizeForBasedOnImageSize($slideshow['background_size'], asset('storage/'.$slideshow['mobile_image'])) }}">
									@if ($slideshow['disable_content'] == 'inactive')
										<div class="ph-container-fluid-coverimage position-absolute top-0 p-3 w-100 h-100 text-white" style="z-index: 3">
											<div class="position-absolute {{ coverImageContentPositionMobileH($slideshow['desktop_content_position']) }}">
												<div class="row">
													<div class="{{ coverImageContentGridPositionMobileH($slideshow['desktop_content_position']) }}">
														<h2>{{ $slideshow['title'] }}</h2>
														<h4 class="font-weight-light mb-3">{{ $slideshow['description'] }}</h4>

														<div class="ph-coverimage-button-area">							
															@if ($slideshow['button'][0]['is_active'] == 'active' && $slideshow['button'][0]['title'] !== null)
																<a href="@if ($slideshow['button'][0]['link'] !== null) {{ $slideshow['button'][0]['link'] }} @else javascript:void(0) @endif" class="btn btn-outline-light rounded-pill position-relative me-2" style="z-index: 2">{{ $slideshow['button'][0]['title'] }}</a>
															@endif

															@if ($slideshow['button'][1]['is_active'] == 'active' && $slideshow['button'][1]['title'] !== null)
																<a href="@if ($slideshow['button'][1]['link'] !== null) {{ $slideshow['button'][1]['link'] }} @else javascript:void(0) @endif" class="btn btn-outline-light rounded-pill position-relative" style="z-index: 2">{{ $slideshow['button'][1]['title'] }}</a>
															@endif

															@if ($slideshow['link']['is_active'] == 'active')
																<a href="{{ $slideshow['link']['content'] }}" class="text-white text-decoration-none stretched-link"></a>
															@endif
														</div>

													@if ($slideshow['countdown']['is_active'] == 'active' &&
														 $slideshow['countdown']['mobile_position'] == 'default')
														<div class="bg-white text-dark rounded-pill p-3 mt-4 {{ coverImageContentPositionCountDownDefaultPosition($slideshow['desktop_content_position']) }}" style="width: 350px">
															{{-- FIX: data-countdown-date menyimpan tanggal target --}}
															{{-- countDownVue diinisialisasi dari mounted() bukan inline --}}
															<div data-countdown-idx="{{ $loop->index }}" data-countdown-date="{!! $slideshow['countdown']['content'] !!}"></div>
														</div>
													@endif

													</div>
												</div>
											</div>
										</div>

										@if ($slideshow['countdown']['mobile_position'] !== 'default')
											<div class="ph-container-fluid-coverimage-2 position-absolute top-0 p-3 w-100 h-100 text-white" style="z-index: 2">
												<div class="position-absolute pt-3 px-3 pb-5 {{ coverImageContentPositionCountDownMobile($slideshow['countdown']['mobile_position']) }}">
													<div class="row g-3">
														<div class="col-auto">

															@if ($slideshow['countdown']['is_active'] == 'active')
																<div class="bg-white text-dark rounded-pill p-3 mt-3 mx-auto" style="width: 350px">
																	{{-- FIX: data-countdown-date menyimpan tanggal target --}}
																	<div data-countdown-idx="{{ $loop->index }}" data-countdown-date="{!! $slideshow['countdown']['content'] !!}"></div>
																</div>
															@endif

														</div>
													</div>
												</div>
											</div>
										@endif

										@if ($slideshow['second_content']['is_active'] == 'active')
											<div class="ph-container-fluid-coverimage-2 position-absolute top-0 p-3 w-100 h-100 text-white">
												<div class="position-absolute p-3 {{ coverImageContentPositionSecondContent($slideshow['second_content']['mobile_position']) }}" style="z-index: 3">
													<div class="row g-3">
														<div class="col-auto">

															@if ($slideshow['second_content']['type'] == 'text')
																<h4 class="font-weight-light">{{ $slideshow['second_content']['text'] }}</h4>
															@elseif	($slideshow['second_content']['type'] == 'link')
																<a href="{{ $slideshow['second_content']['link'] }}" class="h4 font-weight-light">{{ $slideshow['second_content']['text'] }}</a>
															@elseif	($slideshow['second_content']['type'] == 'button_link')
																<a href="{{ $slideshow['second_content']['link'] }}" class="h4 font-weight-light">{{ $slideshow['second_content']['text'] }}</a>
															@endif

														</div>
													</div>
												</div>
											</div>
										@endif
									@endif

									@if ($slideshow['link']['is_active'] == 'active' && $slideshow['disable_content'] == 'active')
										<a href="{{ $slideshow['link']['content'] }}" class="text-white text-decoration-none stretched-link"></a>
									@endif

									@if ($slideshow['background_overlay'] !== null)
										<div class="ph-cover-image-filter" style="background: {{ $slideshow['background_overlay'] }}"></div>
									@endif
								</div>
							</div>
						</li>
					@endif
				@endforeach
			</ul>

			<ul class="splide__pagination"></ul>
		</div>
	</div>

	<!-- Splide JS -->
	<script src=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js "></script>

	<script>
	document.addEventListener('DOMContentLoaded', function() 
	{
		const splide = new Splide('.splide', 
		{
			type		: '{{ $getSlideshowLoop }}',
			height		: 'auto',
			autoplay	: {!! $getAutoPlay !!},
			arrows		: false,
			wheel		: true,
			autoHeight	: true,
			updateOnMove: true,
			perPage		: 1,
			pagination	: true,
			focus		: 0
		});

		splide.on('active', function(e) 
		{
			if (e.slide.parentElement.offsetParent !== null)
			{
				e.slide.parentElement.offsetParent.style.height = e.slide.clientHeight+'px';

				// console.log(e.slide.parentElement.offsetParent.style.height);

				window.setTimeout(function() 
				{
					const setHeightToContainer = document.getElementById("splide01-track");

					setHeightToContainer.style.height = e.slide.clientHeight+'px';

					// console.log(setHeightToContainer);

				}, 50);
			}
		})

		splide.mount();
	});
	</script>
</div>

@push('js')
	<script src="{{ url('assets/plugins/vue/plugins/vue-datepicker/js/vue-datepicker-11.0.3.js') }}"></script>
	<script src="{{ url('assets/js/global/coverimage-frontend/vueV3-coverimage-frontend-2026.js?v=').time() }}"></script>
@endpush
