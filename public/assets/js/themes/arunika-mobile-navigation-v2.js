(() =>
{
	'use strict';

	const MOBILE_QUERY = '(max-width: 768px)';
	const FOCUSABLE_SELECTOR = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

	const getFocusable = (container) => Array.from(container?.querySelectorAll(FOCUSABLE_SELECTOR) || [])
		.filter((element) => element.offsetWidth > 0 || element.offsetHeight > 0 || element === document.activeElement);

	const createBackdrop = (sidebar) =>
	{
		let backdrop = document.querySelector('.ph-mobile-sidebar-backdrop');

		if (!backdrop)
		{
			backdrop = document.createElement('button');
			backdrop.type = 'button';
			backdrop.className = 'ph-mobile-sidebar-backdrop';
			backdrop.tabIndex = -1;
			backdrop.hidden = true;
			backdrop.setAttribute('aria-label', 'Dismiss navigation backdrop');
			sidebar.insertAdjacentElement('afterend', backdrop);
		}
		else
		{
			backdrop.setAttribute('aria-label', 'Dismiss navigation backdrop');
		}

		return backdrop;
	};

	const createCloseControl = (sidebar) =>
	{
		let closeControl = sidebar.querySelector('.ph-mobile-sidebar-close');

		if (closeControl)
		{
			return closeControl;
		}

		closeControl = document.createElement('button');
		closeControl.type = 'button';
		closeControl.className = 'ph-mobile-sidebar-close';
		closeControl.setAttribute('aria-label', 'Close navigation');
		closeControl.innerHTML = '<i class="fas fa-times" aria-hidden="true"></i>';

		const header = sidebar.querySelector('.ph-sidebar-logo-container') || sidebar.firstElementChild || sidebar;
		header.appendChild(closeControl);

		return closeControl;
	};

	if (!window.Vue)
	{
		console.warn('Phoenix mobile navigation skipped: Vue 3 CDN is unavailable.');
		return;
	}

	const mountRoot = document.getElementById('ph-mobile-nav-controller');
	const sidebar = document.getElementById('sidebar');

	if (!mountRoot || !sidebar)
	{
		console.warn('Phoenix mobile navigation skipped: required mount root or sidebar is missing.');
		return;
	}

	const backdrop = createBackdrop(sidebar);
	const isLucentTheme = document.body.dataset.phMobileTheme === 'arunika_lucent';
	const closeControl = isLucentTheme
		? sidebar.querySelector('.ph-lucent-sidebar-toggle')
		: createCloseControl(sidebar);
	const mainContent = document.querySelector('.ph-layout-right');
	const mobileNotification = document.querySelector('.ph-header-notification');
	const mobileOpeners = () => Array.from(document.querySelectorAll('.ph-mobile-sidebar-trigger'));

	const app = Vue.createApp(
	{
		setup()
		{
			const isOpen = Vue.ref(false);
			const isMobile = Vue.ref(window.matchMedia(MOBILE_QUERY).matches);
			let previousActiveElement = null;

			const commit = () =>
			{
				if (mobileNotification)
				{
					mobileNotification.setAttribute('aria-hidden', isMobile.value ? 'false' : 'true');
				}

				if (!isMobile.value)
				{
					sidebar.inert = false;
					sidebar.removeAttribute('aria-hidden');
					if (mainContent) mainContent.inert = false;
					backdrop.hidden = true;
					backdrop.setAttribute('aria-hidden', 'true');
					document.body.classList.remove('ph-mobile-sidebar-open');
					return;
				}

				sidebar.classList.toggle('ph-expanded', isOpen.value);
				sidebar.inert = !isOpen.value;
				sidebar.setAttribute('aria-hidden', isOpen.value ? 'false' : 'true');
				if (mainContent) mainContent.inert = isOpen.value;
				backdrop.hidden = !isOpen.value;
				backdrop.setAttribute('aria-hidden', isOpen.value ? 'false' : 'true');
				document.body.classList.toggle('ph-mobile-sidebar-open', isOpen.value);

				mobileOpeners().forEach((opener) =>
				{
					opener.setAttribute('aria-expanded', isOpen.value ? 'true' : 'false');
					opener.setAttribute('aria-label', 'Open navigation');
				});
				if (closeControl)
				{
					closeControl.setAttribute('aria-hidden', isOpen.value ? 'false' : 'true');
				}
			};

			const open = () =>
			{
				if (!isMobile.value || isOpen.value)
				{
					return;
				}

				previousActiveElement = document.activeElement;
				isOpen.value = true;
				Vue.nextTick(() =>
				{
					const focusTarget = closeControl || getFocusable(sidebar)[0];
					focusTarget?.focus();
				});
			};

			const close = () =>
			{
				if (!isOpen.value)
				{
					return;
				}

				isOpen.value = false;
				Vue.nextTick(() =>
				{
					if (previousActiveElement && previousActiveElement.isConnected && !previousActiveElement.hidden)
					{
						previousActiveElement.focus();
					}
				});
			};

			const toggle = () =>
			{
				if (!isMobile.value)
				{
					return;
				}

				isOpen.value ? close() : open();
			};

			const syncViewport = () =>
			{
				const nextIsMobile = window.matchMedia(MOBILE_QUERY).matches;

				if (nextIsMobile !== isMobile.value)
				{
					isMobile.value = nextIsMobile;
					if (nextIsMobile) isOpen.value = false;
				}

				commit();
			};

			const onKeydown = (event) =>
			{
				if (!isMobile.value || !isOpen.value)
				{
					return;
				}

				if (event.key === 'Escape')
				{
					event.preventDefault();
					close();
					return;
				}

				if (event.key !== 'Tab')
				{
					return;
				}

				const focusable = getFocusable(sidebar);

				if (!focusable.length)
				{
					event.preventDefault();
					closeControl.focus();
					return;
				}

				const first = focusable[0];
				const last = focusable[focusable.length - 1];

				if (event.shiftKey && document.activeElement === first)
				{
					event.preventDefault();
					last.focus();
				}
				else if (!event.shiftKey && document.activeElement === last)
				{
					event.preventDefault();
					first.focus();
				}
			};

			const onSidebarClick = (event) =>
			{
				const link = event.target.closest('a[href]');

				if (!link || link.hasAttribute('data-bs-toggle') || link.getAttribute('href')?.startsWith('javascript:'))
				{
					return;
				}

				close();
			};

			const api =
			{
				isMobile: () => isMobile.value,
				open,
				close,
				toggle,
				syncViewport
			};

			window.PhoenixMobileNavigation = api;

			Vue.watch([isOpen, isMobile], commit, { immediate: true });

			Vue.onMounted(() =>
			{
				document.addEventListener('keydown', onKeydown);
				document.addEventListener('click', onSidebarClick);
				backdrop.addEventListener('click', close);
				window.addEventListener('resize', syncViewport);
				if (closeControl)
				{
					closeControl.addEventListener('click', (event) =>
					{
						event.preventDefault();
						event.stopPropagation();
						close();
					});
				}
				syncViewport();
			});

			Vue.onBeforeUnmount(() =>
			{
				document.removeEventListener('keydown', onKeydown);
				document.removeEventListener('click', onSidebarClick);
				backdrop.removeEventListener('click', close);
				window.removeEventListener('resize', syncViewport);
				sidebar.inert = false;
				if (mainContent) mainContent.inert = false;
			});

			return {};
		},
		render()
		{
			return null;
		}
	}
	);

	app.mount(mountRoot);
})();
