@if($authTheme === 'clean_peach')
	<div class="ph-auth-illustration-stage">
		@if($panelUrl)
			@if($themeOptions['decoration'])<div class="ph-auth-illustration-arch" aria-hidden="true"></div>@endif
			<img src="{{ $panelUrl }}" alt="" class="ph-auth-illustration-image">
		@endif
	</div>
@elseif($authTheme === 'midnight_story')
	<h2 class="ph-auth-panel-title">{{ $themeOptions['panel_heading'] }}</h2>
	@if($themeOptions['decoration'])<i class="fas fa-asterisk ph-auth-panel-ornament" aria-hidden="true"></i>@endif
	@php $testimonials = array_values(array_filter($themeOptions['testimonials'], fn($item) => trim((string)($item['quote'] ?? '')) !== '')); @endphp
	@if(count($testimonials))
		<div id="phAuthTestimonials" class="carousel slide ph-auth-testimonials" data-bs-interval="false">
			<div class="carousel-inner">
				@foreach($testimonials as $index => $testimonial)
					<div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
						<blockquote><i class="fas fa-quote-left mb-3" aria-hidden="true"></i><p>{{ $testimonial['quote'] }}</p>
							@if(!empty($testimonial['author']))<footer><strong>{{ $testimonial['author'] }}</strong><span>{{ $testimonial['role'] ?? '' }}</span></footer>@endif
						</blockquote>
					</div>
				@endforeach
			</div>
			@if(count($testimonials)>1)
				<div class="d-flex gap-2">
					<button class="ph-auth-story-arrow" type="button" data-bs-target="#phAuthTestimonials" data-bs-slide="prev" aria-label="{{ t('Previous testimonial') }}"><i class="fas fa-arrow-left" aria-hidden="true"></i></button>
					<button class="ph-auth-story-arrow" type="button" data-bs-target="#phAuthTestimonials" data-bs-slide="next" aria-label="{{ t('Next testimonial') }}"><i class="fas fa-arrow-right" aria-hidden="true"></i></button>
				</div>
			@endif
		</div>
	@endif
	@if($themeOptions['info_card'])
		<div class="ph-auth-info-card">
			@if($themeOptions['decoration'])<span class="ph-auth-info-ornament" aria-hidden="true"><i class="fas fa-star"></i></span>@endif
			<h3>{{ $themeOptions['info_heading'] }}</h3><p>{{ $themeOptions['info_text'] }}</p>
			@if(count($themeOptions['avatars']))
				<div class="ph-auth-avatar-list">@foreach($themeOptions['avatars'] as $avatar)<img src="{{ \App\Support\AuthThemeCatalog::imageUrl($avatar) }}" alt="">@endforeach</div>
			@endif
		</div>
	@endif
@else
	<div class="ph-auth-panel-copy">
		<h2 class="ph-auth-panel-title">{{ $themeOptions['panel_heading'] }}</h2>
		<p class="ph-auth-panel-description">{{ $themeOptions['panel_description'] }}</p>
	</div>
	@if($authTheme === 'verdant_split' && $themeOptions['decoration'])
		<div class="ph-auth-botanical" aria-hidden="true"><span class="ph-auth-orbit"></span>@for($leaf=0;$leaf<6;$leaf++)<span class="ph-auth-leaf ph-auth-leaf-{{ $leaf }}"></span>@endfor<span class="ph-auth-stem"></span></div>
	@endif
@endif
