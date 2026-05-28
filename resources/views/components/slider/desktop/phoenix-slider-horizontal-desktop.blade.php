<!-- Splide CSS -->
<link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet">

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

/*.ph-cover-only-image .ph-cover-image-filter,
.ph-cover-background-image .ph-cover-image-filter 
{
	top: 0;
	width: 100%;
	height: 100%;
	position: absolute;
}*/
</style>

@php
	$getAutoPlay 			= ($data->cover_autoplay_slideshow == 'active') ? 'true' : 'false';
	$getAutoPlayInterval 	= ($data->cover_autoplay_slideshow == 'active') ? $data->cover_autoplay_slideshow_interval : 3000;
	$getSlideshowLoop 		= ($data->cover_looping_slideshow == 'active') ? 'loop' : 'slide';
@endphp

<div class="position-relative {{ $slug }}" id="ph-app-coverimage">
	<!-- Slider main container -->
	<div class="splide">
		<!-- Additional required wrapper -->
		<div class="splide__track">
			<!-- Slides -->
			<ul class="splide__list">
				@foreach (json_decode($data['cover_slideshow_vars'], true) as $slideshow)
					@if ($slideshow['cover_is_active'] == 'active')
						<li class="splide__slide" data-splide-interval="{{ $getAutoPlayInterval }}">
							<div class="{{ getClassBackgroundImageSize($slideshow['background_size']) }} position-relative d-none d-md-block">
								<img src="{{ asset('storage/'.$slideshow['desktop_image']) }}" class="img-fluid w-100 d-none">
							
								<div class="ph-background {{ getBackgroundImageSize($slideshow['background_size']) }}" style="background-image: url('{{ asset('storage/'.$slideshow['desktop_image']) }}') {{ getBackgroundImageSizeForBasedOnImageSize($slideshow['background_size'], asset('storage/'.$slideshow['desktop_image'])) }}">
									@if ($slideshow['disable_content'] == 'inactive')
										<div class="ph-container-fluid-coverimage position-absolute top-0 p-5 w-100 h-100 text-white" style="z-index: 3">
											<div class="position-absolute p-5 {{ coverImageContentPositionDesktopH($slideshow['desktop_content_position']) }}">
												<div class="row">
													<div class="{{ coverImageContentGridPosition($slideshow['desktop_content_position'], $slideshow['description'] == '' ? false : true) }}">
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
														 $slideshow['countdown']['desktop_position'] == 'default')
														<div class="bg-white text-dark rounded-pill p-4 mt-3 {{ coverImageContentPositionCountDownDefaultPosition($slideshow['desktop_content_position']) }}" style="width: 350px">
															{{-- FIX: data-countdown-date menyimpan tanggal target --}}
															{{-- countDownVue diinisialisasi dari mounted() bukan inline --}}
															<div data-countdown-idx="{{ $loop->index }}" data-countdown-date="{!! $slideshow['countdown']['content'] !!}"></div>
														</div>
													@endif

													</div>
												</div>
											</div>
										</div>

										@if ($slideshow['countdown']['desktop_position'] !== 'default')
											<div class="ph-container-fluid-coverimage-2 position-absolute top-0 p-5 w-100 h-100 text-white" style="z-index: 2">
												<div class="position-absolute p-5 {{ coverImageContentPositionCountDown($slideshow['countdown']['desktop_position']) }}">
													<div class="row g-3">
														<div class="col-auto">

															@if ($slideshow['countdown']['is_active'] == 'active')
																<div class="bg-white text-dark rounded-pill p-4 mt-3 mx-auto" style="width: 350px">
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
											<div class="ph-container-fluid-coverimage-2 position-absolute top-0 p-5 w-100 h-100 text-white">
												<div class="position-absolute p-5 {{ coverImageContentPositionSecondContent($slideshow['second_content']['desktop_position']) }}" style="z-index: 3">
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

		/*
		 * Parses a datetime string into a timestamp (milliseconds).
		 * Supports formats: "dd/mm/yyyy" or "dd/mm/yyyy HH:MM"
		 *
		 * @param {string} value The datetime string.
		 * @param {boolean} utc If true, use UTC. If false, use local time.
		 * @returns {number} Timestamp in milliseconds.
		 */

		/*
		function getNewTime(value, utc) 
		{
			// Split the date and time parts (if time exists)
			// Example: "23/10/2025 10:30" -> ["23/10/2025", "10:30"]
			let dateTimeParts = value.split(' ');
			let dateString = dateTimeParts[0]; // Date part: "23/10/2025"
			let timeString = dateTimeParts[1]; // Time part: "10:30" (or undefined)

			// 1. Parse Date (dd/mm/yyyy)
			let dateParts = dateString.split('/');

			// dateParts[0] = dd, dateParts[1] = mm, dateParts[2] = yyyy
			let year = parseInt(dateParts[2], 10);
			let month = parseInt(dateParts[1], 10) - 1; // Date() uses zero-based months (0-11)
			let day = parseInt(dateParts[0], 10);

			// 2.
		function countDown(index, data)
		{
			// Set the date we're counting down to
			let countDownDate = new Date(getNewTime2(data, false)).getTime();

			// Update the count down every 1 second
			let x = setInterval(function() 
			{
				// Get today's date and time
				let now = new Date().getTime();

				// Find the distance between now and the count down date
				let distance = countDownDate - now;

				// Time calculations for days, hours, minutes and seconds
				let days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
				let hours = String(MMath.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
				let minutes = String(MMath.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
				let seconds = String(MMath.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');

				// Output the result in an element with id="demo"
				document.getElementById("countdown-"+index).innerHTML = days+"d "+hours+"h "+minutes+"m "+seconds+ "s";

				// If the count down is over, write some text 
				if (distance < 0) 
				{
					clearInterval(x);
					document.getElementById("countdown-"+index).innerHTML = "EXPIRED";
				}

			}, 1000);
		}
		*/

	});
	</script>
</div>

@push('js')
	<script src="{{ url('assets/plugins/vue/plugins/vue-datepicker/js/vue-datepicker-11.0.3.js') }}"></script>
	<script src="{{ url('assets/js/global/coverimage-frontend/vueV3-coverimage-frontend-2026.js?v=').time() }}"></script>
@endpush
