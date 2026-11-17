<?php echo $__env->make('themes.arunika_prism.components.menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
	$siteConfig = site_config();
	$siteName = $siteConfig->site_name;
	$siteTypography = app(\App\Support\SiteTypography::class)->resolve($siteConfig);
	$hasCustomSiteLogo = filled($siteConfig->site_logo);
	$siteLogoWidthUnit = in_array($siteConfig->site_logo_width_unit ?? '%', ['%', 'px', 'em', 'rem', 'pt'], true)
		? $siteConfig->site_logo_width_unit
		: '%';
	$siteLogoWidthMax = $siteLogoWidthUnit === '%' ? 100 : 500;
	$siteLogoWidthValue = min($siteLogoWidthMax, max(1, (float) ($siteConfig->site_logo_width_value ?? 100)));
	$siteLogoCssWidth = $siteLogoWidthUnit === '%'
		? round(34 * ($siteLogoWidthValue / 100), 2).'px'
		: rtrim(rtrim(number_format($siteLogoWidthValue, 2, '.', ''), '0'), '.').$siteLogoWidthUnit;
	$siteLogoUrl = $hasCustomSiteLogo
		? route('cms.admin.awesome_admin.config.logo', ['fileName' => $siteConfig->site_logo])
		: '';
	$currentUserRole = auth()->user()->getRoleNames()->first() ?: t('Account');
?>

<!DOCTYPE html>
<html lang="en" style="--ph-font-family: '<?php echo e($siteTypography['fontFamilyName']); ?>', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; --ph-font-size: <?php echo e($siteTypography['fontSize']); ?>;">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

		<?php echo $__env->yieldPushContent('meta'); ?>

		<!-- Bootstrap -->
		<link href="<?php echo e(url('assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css')); ?>" rel="stylesheet">

		<!-- Fontawesome -->
		<link href="<?php echo e(url('assets/plugins/fontawesome/5.15.3/css/all.min.css')); ?>" rel="stylesheet">

		<!-- Font -->
		<link id="arunikaActiveFontStylesheet" data-font-base-url="<?php echo e(asset('storage/fonts')); ?>" href="<?php echo e(asset('storage/fonts/'.$siteTypography['fontFamilyCode'].'/fonts.css?v=').time()); ?>" rel="stylesheet">

		<!-- Vue Select CSS --->
		<link rel="stylesheet" href="<?php echo e(url('assets/plugins/vue/plugins/vue-select/css/vue-select.3.20.3.css')); ?>">

		<!-- Simplebar -->
		<link href="<?php echo e(url('assets/plugins/simplebar/6.3.1/css/simplebar.css')); ?>" rel="stylesheet">

		<!-- Custom CSS -->
		<link href="<?php echo e(asset('assets/css/shared/phoenix-cms.css?v=').time()); ?>" rel="stylesheet">
		<link href="<?php echo e(asset('assets/css/themes/arunika_prism/arunika_prism.css?v=').time()); ?>" rel="stylesheet">
		<link href="<?php echo e(asset('assets/css/shared/theme-responsive-typography.css?v=').time()); ?>" rel="stylesheet">
		<link href="<?php echo e(asset('assets/css/themes/arunika_prism/mobile-v2.css?v=').time()); ?>" rel="stylesheet">

		<link href="<?php echo e(asset('assets/css/cms/cms-bootstrap-theme.css')); ?>?v=<?php echo e(filemtime(public_path('assets/css/cms/cms-bootstrap-theme.css'))); ?>" rel="stylesheet">
		<?php echo $__env->yieldPushContent('css'); ?>

		<title><?php echo $__env->yieldContent('title', site_config()->site_name); ?> - <?php echo e(site_config()->site_name); ?></title>

		<script>
		(function() 
		{
			// 1. Ambil Tema
			const savedTheme = localStorage.getItem('theme') || 'light';
			// Kita pasang di documentElement (tag <html>) karena body belum ready
			document.documentElement.setAttribute('data-bs-theme', savedTheme);

			// 2. Ambil Warna
			const savedColor = localStorage.getItem('theme-color');
			
			if (savedColor) 
			{
				const isCoolGray = savedColor.toUpperCase() === '#C7CCD8';
				const colorRoot = document.documentElement;

				colorRoot.style.setProperty('--ph-theme-primary', isCoolGray ? '#667085' : savedColor);
				colorRoot.style.setProperty('--ph-theme-surface-tint', savedColor);

				if (isCoolGray)
				{
					colorRoot.dataset.phThemeColor = 'cool-gray';
				}
				else
				{
					delete colorRoot.dataset.phThemeColor;
				}
			}
		})();
		</script>
	</head>

	<body class="ph-theme-arunika-prism" data-ph-mobile-theme="arunika_prism">
		<div class="ph-app-shell d-flex w-100 h-100">
			<div class="ph-sidebar ph-no-transition" id="sidebar">

				<script>
				(function()
				{
					const savedSidebarState = localStorage.getItem('sidebar-state');
					const shouldExpand = window.innerWidth > 768 && savedSidebarState !== 'collapsed';

					if (shouldExpand)
					{
						document.getElementById('sidebar').classList.add('ph-expanded');
					}
				})();
				</script>
				
				<div class="ph-sidebar-logo-container<?php echo e($hasCustomSiteLogo ? ' has-logo' : ''); ?>" aria-label="<?php echo e($siteName); ?>" style="--ph-site-logo-width: <?php echo e($siteLogoCssWidth); ?>">
					<div class="ph-app-logo-icon">
						<img <?php if($hasCustomSiteLogo): ?> src="<?php echo e($siteLogoUrl); ?>" <?php endif; ?> alt="<?php echo e($siteName); ?>">
					</div>

					<span class="ph-app-logo-initial" aria-hidden="true"><?php echo e(mb_strtoupper(mb_substr(trim($siteName), 0, 1))); ?></span>
					<span class="ph-app-logo-text"><?php echo e($siteName); ?></span>
					<button class="ph-mobile-sidebar-close" type="button" onclick="toggleSidebar()" aria-label="<?php echo e(t('Close navigation')); ?>">
						<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
							<rect x="2.75" y="2.75" width="18.5" height="18.5" rx="4"></rect>
							<path d="M8.25 3.25V20.75"></path>
							<path class="ph-sidebar-toggle-chevron" d="M16 8.75L12.75 12L16 15.25"></path>
						</svg>
					</button>
				</div>

				<div id="sidebar-scroll-content">
					
					<div class="list-group list-group-flush w-100">
						<div class="ph-list-group-wrapper">
							<a href="<?php echo e(url('/')); ?>" class="list-group-item list-group-item-action" target="_blank">
								<div class="ph-nav-icon"><i class="fal fa-link fa-fw"></i></div>
								<span class="ph-nav-text"><?php echo e(t('Visit Site')); ?></span>

								<div class="ph-custom-tooltip"><span><?php echo e(t('Visit Site')); ?></span></div>
							</a>

							<a href="<?php echo e(url('dashboard')); ?>" class="list-group-item list-group-item-action">
								<div class="ph-nav-icon"><i class="fal fa-home fa-fw"></i></i></div>
								<span class="ph-nav-text"><?php echo e(t('Dashboard')); ?></span>

								<div class="ph-custom-tooltip"><span><?php echo e(t('Dashboard')); ?></span></div>
							</a>

						</div>

						<?php echo menu_versioning(); ?>

					</div>

				</div>

				<div class="ph-sidebar-footer">
					<div class="ph-sidebar-user-panel">
						<div class="dropdown ph-sidebar-profile">
							<button class="ph-sidebar-user-card" type="button" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false" aria-label="<?php echo e(t('Open profile menu')); ?>">
								<span class="ph-sidebar-user-avatar"><?php echo get_avatar('frame', 'rounded-circle', 38); ?></span>
								<span class="ph-sidebar-user-meta">
									<strong><?php echo e(auth()->user()->fullname); ?></strong>
									<span><?php echo e($currentUserRole); ?></span>
								</span>
								<i class="fal fa-chevron-right ph-sidebar-user-chevron"></i>
							</button>

							<div class="dropdown-menu ph-header-profile-menu ph-sidebar-profile-menu">
								<div class="ph-profile-menu-user">
									<span class="ph-profile-menu-avatar"><?php echo get_avatar('frame', 'rounded-circle', 36); ?></span>
									<span class="ph-profile-menu-identity">
										<strong><?php echo e(auth()->user()->fullname); ?></strong>
										<span><?php echo e($currentUserRole); ?></span>
									</span>
								</div>

								<div class="ph-profile-menu-section">
									<a class="dropdown-item" href="<?php echo e(url('profile')); ?>">
										<i class="fal fa-user-circle fa-fw"></i>
										<span><?php echo e(t('Profile')); ?></span>
									</a>

									<a class="dropdown-item" href="<?php echo e(url('account')); ?>">
										<i class="fal fa-cog fa-fw"></i>
										<span><?php echo e(t('Settings')); ?></span>
									</a>

									<button class="dropdown-item ph-profile-theme-toggle ph-theme-toggle" type="button" onclick="toggleTheme()" aria-label="<?php echo e(t('Dark Mode')); ?>" aria-pressed="false">
										<i class="fas fa-sun fa-fw ph-theme-icon"></i>
										<span><?php echo e(t('Dark Mode')); ?></span>
										<span class="ph-profile-switch" aria-hidden="true"><span></span></span>
									</button>

									<button class="dropdown-item ph-profile-color-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ph-profile-theme-colors" aria-expanded="false" aria-controls="ph-profile-theme-colors">
										<i class="fal fa-palette fa-fw"></i>
										<span><?php echo e(t('Theme Color')); ?></span>
										<i class="fal fa-chevron-right ph-profile-color-chevron" aria-hidden="true"></i>
									</button>

									<div class="collapse ph-profile-color-collapse" id="ph-profile-theme-colors">
										<div class="ph-profile-color-section">
											<span class="ph-profile-color-label"><?php echo e(t('Choose Theme Color')); ?></span>
											<div class="row g-2" id="color-picker-container" data-ph-theme-color-picker></div>
										</div>
									</div>
								</div>

								<div class="ph-profile-menu-section ph-profile-menu-footer">
									<a class="dropdown-item text-danger" href="<?php echo e(url('auth/logout')); ?>">
										<i class="fal fa-sign-out-alt fa-fw"></i>
										<span><?php echo e(t('Logout')); ?></span>
									</a>
								</div>
							</div>
						</div>
					</div>

					<?php if(checkIsAdmin()): ?>
						<a href="<?php echo e(url('awesome_admin')); ?>" class="ph-prism-sidebar-admin" aria-label="<?php echo e(t('Open Awesome Admin')); ?>">
							<i class="fal fa-user-secret fa-fw" aria-hidden="true"></i>
							<span><?php echo e(t('Awesome Admin')); ?></span>
						</a>
					<?php endif; ?>
				</div>

			</div>

			<div class="ph-layout-right" id="ph-layout-right">
				<div class="ph-top-bar" id="ph-top-bar">
					<button class="ph-mobile-sidebar-trigger" type="button" onclick="toggleSidebar()" aria-label="Open navigation" aria-expanded="false">
						<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
							<rect x="2.75" y="2.75" width="18.5" height="18.5" rx="4"></rect>
							<path d="M8.25 3.25V20.75"></path>
							<path class="ph-sidebar-toggle-chevron" d="M16 8.75L12.75 12L16 15.25"></path>
						</svg>
					</button>

					<div class="ph-header-nav-control">
						<button class="ph-sidebar-toggle" id="sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Toggle sidebar" aria-expanded="true">
							<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
								<rect x="2.75" y="2.75" width="18.5" height="18.5" rx="4"></rect>
								<path d="M8.25 3.25V20.75"></path>
								<path class="ph-sidebar-toggle-chevron" d="M16 8.75L12.75 12L16 15.25"></path>
							</svg>
						</button>
						<span class="ph-header-divider" aria-hidden="true"></span>
					</div>

					<div class="ph-header-welcome">
						<span><?php echo e(t('Welcome')); ?>,</span>
						<strong><?php echo e(auth()->user()->fullname); ?></strong>
					</div>

					<label class="ph-search-container" for="ph-global-search">
						<i class="fal fa-search"></i>
						<input type="search" class="ph-search-input" id="ph-global-search" placeholder="<?php echo e(t('Find something')); ?>" autocomplete="off">
						<span class="ph-search-shortcut"><i class="fal fa-command"></i> K</span>
					</label>

					<div class="ph-header-actions">
						<div class="ph-header-notification is-hidden" aria-hidden="true">
							<?php echo $__env->make('components.cms-realtime-notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
						</div>

						<div class="dropdown ph-header-profile ph-prism-mobile-profile">
							<button class="ph-header-profile-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false" aria-haspopup="true" aria-label="<?php echo e(t('Open profile menu')); ?>">
								<span class="ph-header-profile-avatar"><?php echo get_avatar('frame', 'rounded-circle', 32); ?></span>
								<span class="ph-header-profile-meta">
									<strong><?php echo e(auth()->user()->fullname); ?></strong>
									<span><?php echo e($currentUserRole); ?></span>
								</span>
								<i class="fal fa-chevron-down ph-header-profile-chevron" aria-hidden="true"></i>
							</button>

							<div class="dropdown-menu ph-header-profile-menu ph-prism-mobile-profile-menu">
								<div class="ph-profile-menu-user">
									<span class="ph-profile-menu-avatar"><?php echo get_avatar('frame', 'rounded-circle', 36); ?></span>
									<span class="ph-profile-menu-identity">
										<strong><?php echo e(auth()->user()->fullname); ?></strong>
										<span><?php echo e($currentUserRole); ?></span>
									</span>
								</div>

								<div class="ph-profile-menu-section">
									<a class="dropdown-item" href="<?php echo e(url('profile')); ?>">
										<i class="fal fa-user-circle fa-fw"></i>
										<span><?php echo e(t('Profile')); ?></span>
									</a>

									<a class="dropdown-item" href="<?php echo e(url('account')); ?>">
										<i class="fal fa-cog fa-fw"></i>
										<span><?php echo e(t('Settings')); ?></span>
									</a>

									<button class="dropdown-item ph-profile-theme-toggle ph-theme-toggle" type="button" onclick="toggleTheme()" aria-label="<?php echo e(t('Dark Mode')); ?>" aria-pressed="false">
										<i class="fas fa-sun fa-fw ph-theme-icon"></i>
										<span><?php echo e(t('Dark Mode')); ?></span>
										<span class="ph-profile-switch" aria-hidden="true"><span></span></span>
									</button>

									<button class="dropdown-item ph-profile-color-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ph-prism-mobile-theme-colors" aria-expanded="false" aria-controls="ph-prism-mobile-theme-colors">
										<i class="fal fa-palette fa-fw"></i>
										<span><?php echo e(t('Theme Color')); ?></span>
										<i class="fal fa-chevron-right ph-profile-color-chevron" aria-hidden="true"></i>
									</button>

									<div class="collapse ph-profile-color-collapse" id="ph-prism-mobile-theme-colors">
										<div class="ph-profile-color-section">
											<span class="ph-profile-color-label"><?php echo e(t('Choose Theme Color')); ?></span>
											<div class="row g-2" data-ph-theme-color-picker></div>
										</div>
									</div>
								</div>

								<div class="ph-profile-menu-section ph-profile-menu-footer">
									<a class="dropdown-item text-danger" href="<?php echo e(url('auth/logout')); ?>">
										<i class="fal fa-sign-out-alt fa-fw"></i>
										<span><?php echo e(t('Logout')); ?></span>
									</a>
								</div>
							</div>
						</div>

						<?php if(checkIsAdmin()): ?>
							<a href="<?php echo e(url('awesome_admin')); ?>" class="ph-btn-action-icon ph-header-awesome-admin" title="<?php echo e(t('Awesome Admin')); ?>" aria-label="<?php echo e(t('Open Awesome Admin')); ?>">
								<i class="fal fa-user-secret"></i>
							</a>
						<?php endif; ?>
					</div>
				</div>

				<div class="ph-main-panel">
					<div class="ph-scrollable-content">
						<?php echo $__env->yieldContent('content'); ?>
					</div>
				</div>
			</div>
		</div>

		<div id="ph-mobile-nav-controller" hidden aria-hidden="true"></div>

		<script type="text/javascript">const site_url = '<?php echo e(url('/')); ?>';</script>

		<?php echo $__env->yieldPushContent('js-priority'); ?>

		<script src="<?php echo e(url('assets/plugins/bootstrap/5.3.6/js/bootstrap.bundle.min.js')); ?>"></script>
		<script src="<?php echo e(url('assets/plugins/simplebar/6.3.1/js/simplebar.min.js')); ?>"></script>
		<script src="<?php echo e(url('assets/plugins/axios/v1/1.7.7.js')); ?>"></script>
		<script src="<?php echo e(url('assets/plugins/lodash/lodash.4.17.21.min.js')); ?>"></script>
		<script src="<?php echo e(url('assets/plugins/sortable/sortable.1.10.2.min.js')); ?>"></script>

		<script src="<?php echo e(url('assets/plugins/vue/core/v3/vue.3.5.21.global.prod.js')); ?>"></script>
		<script src="<?php echo e(url('assets/plugins/vue/plugins/vuejs-paginate-next/js/vuejs-paginate-next.1.0.2.umd.js')); ?>"></script>
		<script src="<?php echo e(url('assets/plugins/vue/plugins/vue-debounce/js/vue-debounce.5.0.0.min.js')); ?>"></script>
		<script src="<?php echo e(url('assets/plugins/vue/plugins/vue-draggable/js/vuedraggable.4.0.1.umd.min.js')); ?>"></script>
		<script src="<?php echo e(url('assets/plugins/vue/plugins/vue-select/js/vue-select.4.0.0.beta6.umd.js')); ?>"></script>

		<script type="text/javascript">
			const { createApp, ref, reactive, defineModel, h } = Vue;
		</script>

		<?php echo $__env->yieldPushContent('js'); ?>

		<script src="<?php echo e(url('assets/js/themes/arunika_prism/arunika_prism.js?v=').time()); ?>"></script>
		<script src="<?php echo e(asset('assets/js/themes/arunika-mobile-navigation-v2.js?v=').time()); ?>"></script>
	</body>
</html>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/themes/arunika_prism/cms/cms_layout.blade.php ENDPATH**/ ?>