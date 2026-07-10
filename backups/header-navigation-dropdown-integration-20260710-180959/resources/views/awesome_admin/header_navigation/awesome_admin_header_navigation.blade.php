@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Header Navigation') }}
@endsection

@push('css')
	<link href="{{ asset('assets/vendor/coloris/coloris.min.css?v=').time() }}" rel="stylesheet">
	<link href="{{ asset('assets/css/awesome-admin-header-navigation.css?v=').time() }}" rel="stylesheet">
@endpush

@section('content')
	<div class="mb-3">
		{{ Breadcrumbs::render('awesome_admin.header_navigation') }}
	</div>

<div id="headerNavigationEditor" class="header-navigation-editor">
	<main class="mock-shell">
		<div class="ph-content rounded p-3 mb-3">
				<div class="mock-topbar">
					<div>
						<h1 class="mock-title">{{ t('Manage Header Navigation') }}</h1>
						<p class="mock-subtitle">{{ t('Configure the frontend header layout, colors, responsive sizing, and scroll behavior with live preview.') }}</p>
					</div>

					<div class="mock-topbar-actions">
						<label class="form-check form-switch mock-active-control" for="headerNavigationActive">
							<input id="headerNavigationActive" class="form-check-input" type="checkbox" role="switch" @checked($is_active)>
							<span class="form-check-label">{{ t('Active on frontend') }}</span>
						</label>

						<div id="headerNavigationSaveStatus" class="mock-status"><i class="fas fa-database"></i> {{ t('Ready') }}</div>

						<button id="saveHeaderNavigation" type="button" class="btn btn-danger btn-sm save-header-navigation">
							<i class="fas fa-save me-1"></i> {{ t('Save Settings') }}
						</button>
					</div>
				</div>
			</div>

			<div class="mock-workspace-grid">
			<div class="mock-control-pane">
				<div class="ph-content rounded p-3 control-scroll">
					<div class="control-section">
						<div class="section-title">Source</div>
						<div class="mb-3">
							<label class="form-label" for="menuSource">Frontend menu source</label>
							<div class="input-group input-group-sm">
								<input id="menuSource" class="form-control" value="/awesome_admin/menu/fe/listdata/parentmenu">
								<button id="loadMenu" class="btn btn-outline-danger" type="button"><i class="fas fa-sync-alt"></i></button>
							</div>
						</div>
						<div class="row g-2">
							<div class="col-6">
								<label class="form-label" for="menuCount">Preview items</label>
								<input id="menuCount" type="number" min="2" max="9" value="5" class="form-control form-control-sm">
							</div>
							<div class="col-6">
								<label class="form-label" for="deviceMode">Device</label>
								<select id="deviceMode" class="form-select form-select-sm">
									<option value="desktop">Desktop</option>
									<option value="tablet">Tablet</option>
									<option value="mobile">Mobile</option>
								</select>
							</div>
						</div>
					</div>

					<div class="control-section">
						<div class="section-title">Colors</div>
						<div class="row g-2">
							<div class="col-6">
								<label class="form-label" for="headerBg">Header background</label>
								<div class="coloris-control">
									<input id="headerBg" data-coloris value="#ffffff" class="form-control form-control-sm coloris-field">
								</div>
							</div>
							<div class="col-6">
								<label class="form-label" for="scrolledBg">Scrolled background</label>
								<div class="coloris-control">
									<input id="scrolledBg" data-coloris value="#ffffff" class="form-control form-control-sm coloris-field">
								</div>
							</div>
							<div class="col-6">
								<label class="form-label" for="headerText">Header text</label>
								<div class="coloris-control">
									<input id="headerText" data-coloris value="#101828" class="form-control form-control-sm coloris-field">
								</div>
							</div>
							<div class="col-6">
								<label class="form-label" for="linkColor">Link</label>
								<div class="coloris-control">
									<input id="linkColor" data-coloris value="#273142" class="form-control form-control-sm coloris-field">
								</div>
							</div>
							<div class="col-12">
								<label class="form-label">Active link & border</label>
								<div class="link-color-control">
									<div class="link-color-field">
										<label class="link-color-label" for="linkActive">Active</label>
										<div class="coloris-control">
											<input id="linkActive" data-coloris value="#e01d24" class="form-control form-control-sm coloris-field">
										</div>
									</div>
									<div class="link-color-field">
										<label class="link-color-label" for="linkActiveBorder">Border</label>
										<div class="coloris-control">
											<input id="linkActiveBorder" data-coloris value="#e01d24" class="form-control form-control-sm coloris-field">
										</div>
									</div>
									<button id="linkActiveBorderLinked" type="button" class="box-link-toggle is-active" data-linked-color="active" aria-label="Unlink active border color from active link color" aria-pressed="true"><i class="fas fa-link"></i></button>
								</div>
							</div>
							<div class="col-12">
								<label class="form-label">Hover link & border</label>
								<div class="link-color-control">
									<div class="link-color-field">
										<label class="link-color-label" for="linkHover">Hover</label>
										<div class="coloris-control">
											<input id="linkHover" data-coloris value="#e01d24" class="form-control form-control-sm coloris-field">
										</div>
									</div>
									<div class="link-color-field">
										<label class="link-color-label" for="linkHoverBorder">Border</label>
										<div class="coloris-control">
											<input id="linkHoverBorder" data-coloris value="#e01d24" class="form-control form-control-sm coloris-field">
										</div>
									</div>
									<button id="linkHoverBorderLinked" type="button" class="box-link-toggle is-active" data-linked-color="hover" aria-label="Unlink hover border color from hover link color" aria-pressed="true"><i class="fas fa-link"></i></button>
								</div>
							</div>
							<div class="col-12">
								<label class="form-label">Focus link & border</label>
								<div class="link-color-control">
									<div class="link-color-field">
										<label class="link-color-label" for="linkFocus">Focus</label>
										<div class="coloris-control">
											<input id="linkFocus" data-coloris value="#c4121a" class="form-control form-control-sm coloris-field">
										</div>
									</div>
									<div class="link-color-field">
										<label class="link-color-label" for="linkFocusBorder">Border</label>
										<div class="coloris-control">
											<input id="linkFocusBorder" data-coloris value="#c4121a" class="form-control form-control-sm coloris-field">
										</div>
									</div>
									<button id="linkFocusBorderLinked" type="button" class="box-link-toggle is-active" data-linked-color="focus" aria-label="Unlink focus border color from focus link color" aria-pressed="true"><i class="fas fa-link"></i></button>
								</div>
							</div>
							<div class="col-12">
								<div class="shadow-control">
									<div class="shadow-control-header">
										<div class="form-check form-switch mb-0">
											<input id="linkShadowEnabled" class="form-check-input" type="checkbox">
											<label class="form-check-label" for="linkShadowEnabled">Link shadow</label>
										</div>
										<select id="linkShadowUnit" class="form-select form-select-sm box-unit-select">
											<option value="px" selected>px</option>
											<option value="em">em</option>
											<option value="rem">rem</option>
											<option value="pt">pt</option>
										</select>
									</div>
									<div class="box-control-row shadow-control-row">
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="linkShadowX" data-box-stepper-delta="-1" aria-label="Decrease link shadow x"><i class="fas fa-minus"></i></button>
												<input id="linkShadowX" type="number" value="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="linkShadowX" data-box-stepper-delta="1" aria-label="Increase link shadow x"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="linkShadowX">X</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="linkShadowY" data-box-stepper-delta="-1" aria-label="Decrease link shadow y"><i class="fas fa-minus"></i></button>
												<input id="linkShadowY" type="number" value="8" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="linkShadowY" data-box-stepper-delta="1" aria-label="Increase link shadow y"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="linkShadowY">Y</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="linkShadowBlur" data-box-stepper-delta="-1" aria-label="Decrease link shadow blur"><i class="fas fa-minus"></i></button>
												<input id="linkShadowBlur" type="number" value="18" min="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="linkShadowBlur" data-box-stepper-delta="1" aria-label="Increase link shadow blur"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="linkShadowBlur">Blur</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="linkShadowSpread" data-box-stepper-delta="-1" aria-label="Decrease link shadow spread"><i class="fas fa-minus"></i></button>
												<input id="linkShadowSpread" type="number" value="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="linkShadowSpread" data-box-stepper-delta="1" aria-label="Increase link shadow spread"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="linkShadowSpread">Spread</label>
										</div>
									</div>
									<div class="shadow-control-meta">
										<div>
											<label class="form-label" for="linkShadowColor">Shadow color</label>
											<div class="coloris-control">
												<input id="linkShadowColor" data-coloris value="#e01d242e" class="form-control form-control-sm coloris-field">
											</div>
										</div>
										<div class="form-check form-switch shadow-inset-check">
											<input id="linkShadowInset" class="form-check-input" type="checkbox">
											<label class="form-check-label" for="linkShadowInset">Inset</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="control-section">
						<div class="section-title">Layout</div>
						<div class="mb-3">
							<label class="form-label">Logo position</label>
							<div class="segmented" data-control="logoPosition">
								<button type="button" data-value="left" class="is-active">Left</button>
								<button type="button" data-value="center">Center</button>
								<button type="button" data-value="right">Right</button>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label">Menu position</label>
							<div class="segmented" data-control="menuPosition">
								<button type="button" data-value="left" class="is-active">Left</button>
								<button type="button" data-value="center">Center</button>
								<button type="button" data-value="right">Right</button>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label">Header width</label>
							<div class="segmented two" data-control="containerMode">
								<button type="button" data-value="container" class="is-active">Container</button>
								<button type="button" data-value="fluid">Fluid</button>
							</div>
						</div>
						<div class="form-check form-switch mb-2">
							<input id="innerBg" class="form-check-input" type="checkbox">
							<label class="form-check-label" for="innerBg">Background mengikuti container</label>
						</div>
					</div>

					<div class="control-section">
						<div class="section-title">Behavior</div>
						<div class="mb-3">
							<label class="form-label">Header menu position</label>
							<div class="segmented" data-control="headerBehavior">
								<button type="button" data-value="fixed">Fixed</button>
								<button type="button" data-value="sticky">Sticky</button>
								<button type="button" data-value="stay" class="is-active">Stay</button>
							</div>
						</div>
						<div class="form-check form-switch mb-2">
							<input id="transparentStart" class="form-check-input" type="checkbox">
							<label class="form-check-label" for="transparentStart">Transparent sebelum discroll</label>
						</div>
						<div id="transparentColorPanel" class="transparent-color-panel" hidden>
							<div>
								<label class="form-label">Transparent color mode</label>
								<div class="segmented two" data-control="transparentColorMode">
									<button type="button" data-value="auto" class="is-active">Auto</button>
									<button type="button" data-value="custom">Custom</button>
								</div>
							</div>
							<div id="transparentCustomFields" class="transparent-custom-fields" hidden>
								<div class="row g-2">
									<div class="col-6">
										<label class="form-label" for="transparentText">Text/link</label>
										<div class="coloris-control">
											<input id="transparentText" data-coloris value="#ffffff" class="form-control form-control-sm coloris-field">
										</div>
									</div>
									<div class="col-6">
										<label class="form-label" for="transparentHover">Hover</label>
										<div class="coloris-control">
											<input id="transparentHover" data-coloris value="#e01d24" class="form-control form-control-sm coloris-field">
										</div>
									</div>
									<div class="col-6">
										<label class="form-label" for="transparentFocus">Focus</label>
										<div class="coloris-control">
											<input id="transparentFocus" data-coloris value="#c4121a" class="form-control form-control-sm coloris-field">
										</div>
									</div>
									<div class="col-6">
										<label class="form-label" for="transparentActive">Active</label>
										<div class="coloris-control">
											<input id="transparentActive" data-coloris value="#e01d24" class="form-control form-control-sm coloris-field">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-check form-switch mb-3">
							<input id="animateScroll" class="form-check-input" type="checkbox">
							<label class="form-check-label" for="animateScroll">Animasi berubah saat discroll</label>
						</div>
						<label class="form-label" for="scrollSim">Simulate scroll</label>
						<input id="scrollSim" type="range" min="0" max="100" value="0" class="form-range">
					</div>

					<div class="control-section">
						<div class="section-title">Sizing</div>
						<div class="row g-3 gy-lg-4">
							<div class="col-12">
								<label class="form-label" for="headerHeight">Height</label>
								<div class="unit-control">
									<div class="number-stepper">
										<button type="button" class="stepper-btn" data-stepper-target="headerHeight" data-stepper-delta="-1" aria-label="Decrease height"><i class="fas fa-minus"></i></button>
										<input id="headerHeight" type="number" value="76" step="1" class="form-control form-control-sm" inputmode="decimal">
										<button type="button" class="stepper-btn" data-stepper-target="headerHeight" data-stepper-delta="1" aria-label="Increase height"><i class="fas fa-plus"></i></button>
									</div>
									<select id="headerHeightUnit" class="form-select form-select-sm">
										<option value="px" selected>px</option>
										<option value="em">em</option>
										<option value="rem">rem</option>
										<option value="pt">pt</option>
										<option value="%">%</option>
									</select>
								</div>
							</div>

							<div class="col-12">
								<div class="box-control" data-box-control="headerRadius">
									<div class="box-control-header">
										<label class="form-label">Header radius</label>
										<div class="responsive-mode-control" data-responsive-control="headerRadius">
											<button type="button" class="responsive-mode-trigger" data-responsive-trigger aria-haspopup="listbox" aria-expanded="false" aria-label="Responsive device: Desktop" title="Responsive: Desktop"><i class="fas fa-desktop"></i></button>
											<div class="responsive-mode-menu" role="listbox" hidden>
												<button type="button" class="responsive-mode-option is-active" data-responsive-mode="desktop" role="option" aria-selected="true"><i class="fas fa-desktop"></i><span>Desktop</span></button>
												<button type="button" class="responsive-mode-option" data-responsive-mode="tablet" role="option" aria-selected="false"><i class="fas fa-tablet-alt"></i><span>Tablet Portrait</span></button>
												<button type="button" class="responsive-mode-option" data-responsive-mode="mobile" role="option" aria-selected="false"><i class="fas fa-mobile-alt"></i><span>Mobile Portrait</span></button>
											</div>
										</div>
										<select id="headerRadiusUnit" class="form-select form-select-sm box-unit-select">
											<option value="px" selected>px</option>
											<option value="%">%</option>
											<option value="em">em</option>
											<option value="rem">rem</option>
											<option value="pt">pt</option>
										</select>
									</div>
									<div class="box-control-row">
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerRadiusTop" data-box-stepper-delta="-1" aria-label="Decrease header radius top"><i class="fas fa-minus"></i></button>
												<input id="headerRadiusTop" type="number" value="18" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerRadiusTop" data-box-stepper-delta="1" aria-label="Increase header radius top"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="headerRadiusTop">Top</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerRadiusRight" data-box-stepper-delta="-1" aria-label="Decrease header radius right"><i class="fas fa-minus"></i></button>
												<input id="headerRadiusRight" type="number" value="18" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerRadiusRight" data-box-stepper-delta="1" aria-label="Increase header radius right"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="headerRadiusRight">Right</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerRadiusBottom" data-box-stepper-delta="-1" aria-label="Decrease header radius bottom"><i class="fas fa-minus"></i></button>
												<input id="headerRadiusBottom" type="number" value="18" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerRadiusBottom" data-box-stepper-delta="1" aria-label="Increase header radius bottom"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="headerRadiusBottom">Bottom</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerRadiusLeft" data-box-stepper-delta="-1" aria-label="Decrease header radius left"><i class="fas fa-minus"></i></button>
												<input id="headerRadiusLeft" type="number" value="18" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerRadiusLeft" data-box-stepper-delta="1" aria-label="Increase header radius left"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="headerRadiusLeft">Left</label>
										</div>
										<button id="headerRadiusLinked" type="button" class="box-link-toggle is-active" aria-label="Link header radius values" aria-pressed="true"><i class="fas fa-link"></i></button>
									</div>
								</div>
							</div>

							<div class="col-12">
								<div class="box-control" data-box-control="headerPadding">
									<div class="box-control-header">
										<label class="form-label">Header padding</label>
										<div class="responsive-mode-control" data-responsive-control="headerPadding">
											<button type="button" class="responsive-mode-trigger" data-responsive-trigger aria-haspopup="listbox" aria-expanded="false" aria-label="Responsive device: Desktop" title="Responsive: Desktop"><i class="fas fa-desktop"></i></button>
											<div class="responsive-mode-menu" role="listbox" hidden>
												<button type="button" class="responsive-mode-option is-active" data-responsive-mode="desktop" role="option" aria-selected="true"><i class="fas fa-desktop"></i><span>Desktop</span></button>
												<button type="button" class="responsive-mode-option" data-responsive-mode="tablet" role="option" aria-selected="false"><i class="fas fa-tablet-alt"></i><span>Tablet Portrait</span></button>
												<button type="button" class="responsive-mode-option" data-responsive-mode="mobile" role="option" aria-selected="false"><i class="fas fa-mobile-alt"></i><span>Mobile Portrait</span></button>
											</div>
										</div>
										<select id="headerPaddingUnit" class="form-select form-select-sm box-unit-select">
											<option value="px" selected>px</option>
											<option value="%">%</option>
											<option value="em">em</option>
											<option value="rem">rem</option>
											<option value="pt">pt</option>
										</select>
									</div>
									<div class="box-control-row">
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerPaddingTop" data-box-stepper-delta="-1" aria-label="Decrease header padding top"><i class="fas fa-minus"></i></button>
												<input id="headerPaddingTop" type="number" value="10" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerPaddingTop" data-box-stepper-delta="1" aria-label="Increase header padding top"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="headerPaddingTop">Top</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerPaddingRight" data-box-stepper-delta="-1" aria-label="Decrease header padding right"><i class="fas fa-minus"></i></button>
												<input id="headerPaddingRight" type="number" value="24" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerPaddingRight" data-box-stepper-delta="1" aria-label="Increase header padding right"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="headerPaddingRight">Right</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerPaddingBottom" data-box-stepper-delta="-1" aria-label="Decrease header padding bottom"><i class="fas fa-minus"></i></button>
												<input id="headerPaddingBottom" type="number" value="10" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerPaddingBottom" data-box-stepper-delta="1" aria-label="Increase header padding bottom"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="headerPaddingBottom">Bottom</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerPaddingLeft" data-box-stepper-delta="-1" aria-label="Decrease header padding left"><i class="fas fa-minus"></i></button>
												<input id="headerPaddingLeft" type="number" value="24" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="headerPaddingLeft" data-box-stepper-delta="1" aria-label="Increase header padding left"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="headerPaddingLeft">Left</label>
										</div>
										<button id="headerPaddingLinked" type="button" class="box-link-toggle" aria-label="Link header padding values" aria-pressed="false"><i class="fas fa-link"></i></button>
									</div>
								</div>
							</div>

							<div class="col-12">
								<label class="form-label">Link shape</label>
								<div class="segmented two" data-control="linkShape">
									<button type="button" data-value="default" class="is-active">Default</button>
									<button type="button" data-value="leaf">Leaf</button>
								</div>
							</div>

							<div id="leafDirectionControl" class="col-12" hidden>
								<label class="form-label">Leaf direction</label>
								<div class="segmented two" data-control="leafDirection">
									<button type="button" data-value="forward" class="is-active">Forward</button>
									<button type="button" data-value="reverse">Reverse</button>
								</div>
							</div>

							<div class="col-12">
								<div class="box-control" data-box-control="navRadius">
									<div class="box-control-header">
										<label class="form-label">Link radius</label>
										<div class="responsive-mode-control" data-responsive-control="navRadius">
											<button type="button" class="responsive-mode-trigger" data-responsive-trigger aria-haspopup="listbox" aria-expanded="false" aria-label="Responsive device: Desktop" title="Responsive: Desktop"><i class="fas fa-desktop"></i></button>
											<div class="responsive-mode-menu" role="listbox" hidden>
												<button type="button" class="responsive-mode-option is-active" data-responsive-mode="desktop" role="option" aria-selected="true"><i class="fas fa-desktop"></i><span>Desktop</span></button>
												<button type="button" class="responsive-mode-option" data-responsive-mode="tablet" role="option" aria-selected="false"><i class="fas fa-tablet-alt"></i><span>Tablet Portrait</span></button>
												<button type="button" class="responsive-mode-option" data-responsive-mode="mobile" role="option" aria-selected="false"><i class="fas fa-mobile-alt"></i><span>Mobile Portrait</span></button>
											</div>
										</div>
										<select id="navRadiusUnit" class="form-select form-select-sm box-unit-select">
											<option value="px" selected>px</option>
											<option value="%">%</option>
											<option value="em">em</option>
											<option value="rem">rem</option>
											<option value="pt">pt</option>
										</select>
									</div>
									<div class="box-control-row">
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="navRadiusTop" data-box-stepper-delta="-1" aria-label="Decrease link radius top left"><i class="fas fa-minus"></i></button>
												<input id="navRadiusTop" type="number" value="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="navRadiusTop" data-box-stepper-delta="1" aria-label="Increase link radius top left"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="navRadiusTop">Top Left</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="navRadiusRight" data-box-stepper-delta="-1" aria-label="Decrease link radius top right"><i class="fas fa-minus"></i></button>
												<input id="navRadiusRight" type="number" value="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="navRadiusRight" data-box-stepper-delta="1" aria-label="Increase link radius top right"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="navRadiusRight">Top Right</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="navRadiusBottom" data-box-stepper-delta="-1" aria-label="Decrease link radius bottom right"><i class="fas fa-minus"></i></button>
												<input id="navRadiusBottom" type="number" value="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="navRadiusBottom" data-box-stepper-delta="1" aria-label="Increase link radius bottom right"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="navRadiusBottom">Bottom Right</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="navRadiusLeft" data-box-stepper-delta="-1" aria-label="Decrease link radius bottom left"><i class="fas fa-minus"></i></button>
												<input id="navRadiusLeft" type="number" value="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="navRadiusLeft" data-box-stepper-delta="1" aria-label="Increase link radius bottom left"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="navRadiusLeft">Bottom Left</label>
										</div>
										<button id="navRadiusLinked" type="button" class="box-link-toggle is-active" aria-label="Link nav radius values" aria-pressed="true"><i class="fas fa-link"></i></button>
									</div>
								</div>
							</div>

							<div class="col-12">
								<div class="box-control" data-box-control="containerMargin">
									<div class="box-control-header">
										<label class="form-label">Container margin</label>
										<div class="responsive-mode-control" data-responsive-control="containerMargin">
											<button type="button" class="responsive-mode-trigger" data-responsive-trigger aria-haspopup="listbox" aria-expanded="false" aria-label="Responsive device: Desktop" title="Responsive: Desktop"><i class="fas fa-desktop"></i></button>
											<div class="responsive-mode-menu" role="listbox" hidden>
												<button type="button" class="responsive-mode-option is-active" data-responsive-mode="desktop" role="option" aria-selected="true"><i class="fas fa-desktop"></i><span>Desktop</span></button>
												<button type="button" class="responsive-mode-option" data-responsive-mode="tablet" role="option" aria-selected="false"><i class="fas fa-tablet-alt"></i><span>Tablet Portrait</span></button>
												<button type="button" class="responsive-mode-option" data-responsive-mode="mobile" role="option" aria-selected="false"><i class="fas fa-mobile-alt"></i><span>Mobile Portrait</span></button>
											</div>
										</div>
										<select id="containerMarginUnit" class="form-select form-select-sm box-unit-select">
											<option value="px" selected>px</option>
											<option value="%">%</option>
											<option value="em">em</option>
											<option value="rem">rem</option>
											<option value="pt">pt</option>
										</select>
									</div>
									<div class="box-control-row">
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="containerMarginTop" data-box-stepper-delta="-1" aria-label="Decrease container margin top"><i class="fas fa-minus"></i></button>
												<input id="containerMarginTop" type="number" value="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="containerMarginTop" data-box-stepper-delta="1" aria-label="Increase container margin top"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="containerMarginTop">Top</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="containerMarginRight" data-box-stepper-delta="-1" aria-label="Decrease container margin right"><i class="fas fa-minus"></i></button>
												<input id="containerMarginRight" type="number" value="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="containerMarginRight" data-box-stepper-delta="1" aria-label="Increase container margin right"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="containerMarginRight">Right</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="containerMarginBottom" data-box-stepper-delta="-1" aria-label="Decrease container margin bottom"><i class="fas fa-minus"></i></button>
												<input id="containerMarginBottom" type="number" value="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="containerMarginBottom" data-box-stepper-delta="1" aria-label="Increase container margin bottom"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="containerMarginBottom">Bottom</label>
										</div>
										<div class="box-side">
											<div class="box-side-stepper">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="containerMarginLeft" data-box-stepper-delta="-1" aria-label="Decrease container margin left"><i class="fas fa-minus"></i></button>
												<input id="containerMarginLeft" type="number" value="0" step="1" inputmode="decimal">
												<button type="button" class="box-stepper-btn" data-box-stepper-target="containerMarginLeft" data-box-stepper-delta="1" aria-label="Increase container margin left"><i class="fas fa-plus"></i></button>
											</div>
											<label class="box-side-label" for="containerMarginLeft">Left</label>
										</div>
										<button id="containerMarginLinked" type="button" class="box-link-toggle is-active" aria-label="Link container margin values" aria-pressed="true"><i class="fas fa-link"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div>
						<div class="section-title">Config Preview</div>
						<pre id="jsonOutput" class="json-output mb-0"></pre>
					</div>
				</div>
			</div>

			<div class="mock-preview-pane">
				<div class="ph-content rounded preview-card">
					<div class="preview-toolbar">
						<div>
							<strong>Live preview</strong>
							<div class="text-muted small">Scroll preview panel atau pakai slider untuk lihat state header.</div>
						</div>
						<button id="resetPreview" type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-undo-alt me-1"></i> Reset</button>
					</div>
					<div class="preview-frame">
						<div id="deviceStage" class="device-stage">
							<header id="siteHeader" class="site-header is-stay full-bg">
								<div id="headerContainer" class="header-container">
									<div id="headerInner" class="header-inner layout-left"></div>
								</div>
							</header>
							<section class="hero-copy">
								<h2>Build a frontend header that follows your CMS settings.</h2>
								<p>Preview ini menunjukkan perubahan warna, posisi menu, posisi logo, radius, container, dan efek scroll sebelum fitur masuk ke halaman Laravel asli.</p>
							</section>
							<section class="page-band">
								<div class="page-grid">
									<div class="page-tile"><strong>Homepage</strong><span>Parent menu dapat langsung dipakai sebagai link header.</span></div>
									<div class="page-tile"><strong>Products</strong><span>Dropdown/submenu nanti mengambil struktur dari menu frontend.</span></div>
									<div class="page-tile"><strong>Contact</strong><span>Active, hover, dan focus state bisa dilihat di header preview.</span></div>
								</div>
							</section>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
</div>
@endsection

@push('js')
	<script>
		window.headerNavigationEditorOptions = {
			config: {{ Illuminate\Support\Js::from($config) }},
			isActive: {{ $is_active ? 'true' : 'false' }},
			updateUrl: {{ Illuminate\Support\Js::from(route('cms.admin.awesome_admin.header_navigation.update')) }},
			csrfToken: {{ Illuminate\Support\Js::from(csrf_token()) }}
		};
	</script>
	<script src="{{ asset('assets/vendor/coloris/coloris.min.js?v=').time() }}"></script>
	<script src="{{ asset('assets/js/awesome-admin-header-navigation.js?v=').time() }}"></script>
@endpush
