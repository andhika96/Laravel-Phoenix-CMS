@php
	$setting = \App\Models\Awesome_Admin\Page_Theme_Setting::where('uri', $authPage)->first();
	$themeOptions = \App\Support\AuthThemeCatalog::options($authTheme, $authPage, $setting);
	$palette = \App\Support\AuthAppearance::palette($themeOptions['primary_color']);
	$backgroundUrl = \App\Support\AuthThemeCatalog::imageUrl($themeOptions['background_image'] ?? null);
	$panelUrl = $authTheme === 'ocean_split'
		? \App\Support\AuthThemeCatalog::imageUrl($themeOptions['same_image'] ? ($themeOptions['background_image'] ?? null) : ($themeOptions['panel_image'] ?? null))
		: \App\Support\AuthThemeCatalog::imageUrl($themeOptions['illustration_image'] ?? null);
	$logoUrl = \App\Support\AuthThemeCatalog::imageUrl($themeOptions['logo_image']);
	$hasPanel = $authTheme !== 'midnight_story' || $themeOptions['supporting_panel'];
	$panelText = $authTheme === 'midnight_story' ? \App\Support\AuthAppearance::palette($themeOptions['panel_color'])['text'] : ($authTheme === 'verdant_split' ? '#F3F7EA' : ($panelUrl ? ($themeOptions['panel_text_color'] ?? '#343029') : '#25342D'));
@endphp
@pushonce('css')
	<link rel="stylesheet" href="{{ asset('assets/css/auth/auth-modern.css?v=').filemtime(public_path('assets/css/auth/auth-modern.css')) }}">
@endpushonce
<main id="ph-app-auth" class="ph-auth-modern-root {{ !$hasPanel ? 'ph-auth-without-panel' : '' }}" data-modern-theme="{{ $authTheme }}"
	data-panel-side="{{ $themeOptions['panel_side'] }}" data-tablet-layout="{{ $themeOptions['tablet_layout'] }}" data-mobile-visual="{{ $themeOptions['mobile_visual'] }}"
	data-label-style="{{ $themeOptions['label_style'] ?? 'above' }}" data-layout-width="{{ $themeOptions['layout_width'] ?? 'bounded' }}" data-card-width="{{ $themeOptions['card_width'] ?? 'normal' }}" data-panel-ratio="{{ $themeOptions['panel_ratio'] ?? '50:50' }}"
	style="--modern-bg:{{ $themeOptions['page_background'] }};--modern-surface:{{ $themeOptions['form_surface'] }};--modern-text:{{ $themeOptions['text_color'] }};--modern-radius:{{ $themeOptions['control_radius'] }}px;--modern-logo-size:{{ $themeOptions['logo_size'] }}px;--modern-illustration-size:{{ $themeOptions['illustration_size'] ?? 100 }}%;--modern-decoration:{{ $themeOptions['decoration_color'] ?? '#F4D5B8' }};--modern-panel-color:{{ $themeOptions['panel_color'] ?? '#123E2F' }};--modern-panel-text:{{ $panelText }};--modern-mobile-height:{{ $themeOptions['mobile_height'] ?? 180 }}px;--modern-glow:{{ ($themeOptions['glow'] ?? false) ? ($themeOptions['glow_intensity']/100) : 0 }};--modern-overlay:{{ $themeOptions['overlay_color'] ?? '#12323D99' }};--auth-primary:{{ $palette['primary'] }};--auth-primary-hover:{{ $palette['hover'] }};--auth-primary-active:{{ $palette['active'] }};--auth-primary-disabled:{{ $palette['disabled'] }};--auth-primary-text:{{ $palette['text'] }};--auth-primary-hover-text:{{ $palette['hover_text'] }};--auth-primary-active-text:{{ $palette['active_text'] }};--auth-primary-rgb:{{ $palette['rgb'] }};">
	@if($authTheme === 'ocean_split')
		<div class="ph-auth-ocean-background" data-image-state="{{ $backgroundUrl ? 'ready' : 'empty' }}" @if($backgroundUrl) style="background-image:url('{{ $backgroundUrl }}');background-position:{{ $themeOptions['background_position'] }};" @endif aria-hidden="true"></div>
	@endif
	<div class="ph-auth-surface">
		@if($hasPanel)
			<aside class="ph-auth-panel" data-image-state="{{ $panelUrl ? 'ready' : 'empty' }}"
				@if($authTheme === 'ocean_split' && $panelUrl) style="background-image:url('{{ $panelUrl }}');background-position:{{ $themeOptions['panel_image_position'] }};" @endif>
				@include('auth.modern.panel')
			</aside>
		@endif
		<section class="ph-auth-form-area">
			<div class="ph-auth-glow" aria-hidden="true"></div>
			<a class="ph-auth-brand" href="{{ url('/') }}">@if($logoUrl)<img src="{{ $logoUrl }}" alt="">@endif<span>{{ site_config()->site_name }}</span></a>
			<div class="ph-auth-form-content">
				<h1>{{ $themeOptions['heading'] }}</h1><p class="ph-auth-form-description">{{ $themeOptions['description'] }}</p>
				@if($authPage === 'signup' && site_config()->signup_closed == 1)
					<p role="status">{{ t('Registration is currently closed.') }}</p>
				@else
					@include('auth.modern.form')
				@endif
				<div class="ph-auth-page-links">
					@if($authPage === 'login' && site_config()->signup_closed == 0)
						<span>{{ t("Don't have an account?") }}</span> <a href="{{ url('auth/signup') }}">{{ t('Create an account') }}</a>
					@elseif($authPage === 'signup')
						<span>{{ t('Already have an account?') }}</span> <a href="{{ url('auth/login') }}">{{ t('Log In') }}</a>
					@elseif($authPage !== 'login')
						<a href="{{ url('auth/login') }}"><i class="fas fa-arrow-left me-2" aria-hidden="true"></i>{{ t('Back to sign in') }}</a>
					@endif
				</div>
			</div>
			<footer class="ph-auth-form-footer">&copy; {{ date('Y') }} {{ site_config()->site_name }}</footer>
		</section>
	</div>
</main>
