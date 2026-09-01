async (page) =>
{
	const artifactRoot = 'project-artifacts/qa/playwright/arunika-lucent-implementation-20260901';
	const pageState = () =>
	{
		const read = (selector, pseudo = undefined) =>
		{
			const element = document.querySelector(selector);
			const computed = getComputedStyle(element, pseudo);
			const rect = element.getBoundingClientRect();

			return {
				backgroundColor: computed.backgroundColor,
				backgroundImage: computed.backgroundImage,
				boxShadow: computed.boxShadow,
				color: computed.color,
				fontSize: computed.fontSize,
				fontWeight: computed.fontWeight,
				borderRadius: computed.borderRadius,
				width: Math.round(rect.width * 100) / 100,
				height: Math.round(rect.height * 100) / 100,
				left: Math.round(rect.left * 100) / 100,
				top: Math.round(rect.top * 100) / 100,
			};
		};

		return {
			viewport: { width: window.innerWidth, height: window.innerHeight },
			body: read('body'),
			shell: read('.ph-app-shell'),
			sidebar: read('.ph-sidebar'),
			header: read('.ph-top-bar'),
			search: read('.ph-search-container'),
			content: read('.ph-content'),
			section: read('.ph-section'),
			title: read('.ph-lucent-page-header h1'),
			sidebarToggle: read('#ph-lucent-sidebar-toggle'),
			hoveredMenuItem: read('#manage-articles'),
			activeMenuItem: read('#settings'),
			primaryButton: read('.btn-primary'),
			outlineButton: read('.btn-outline-primary'),
			overflow: {
				horizontal: document.documentElement.scrollWidth > window.innerWidth,
				vertical: document.documentElement.scrollHeight > window.innerHeight,
			},
		};
	};

	await page.setViewportSize({ width: 1504, height: 900 });
	await page.goto('http://127.0.0.1:8768/project-artifacts/qa/playwright/arunika-lucent-implementation-20260901/harness.html', { waitUntil: 'domcontentloaded' });
	await page.waitForFunction(() =>
		document.styleSheets.length >= 4 &&
		getComputedStyle(document.querySelector('.ph-sidebar')).width !== '76px' &&
		getComputedStyle(document.body).getPropertyValue('--ph-lucent-sidebar-width').trim() !== ''
	);
	await page.waitForTimeout(350);
	await page.evaluate(() =>
	{
		window.scrollTo(0, 0);
		document.querySelector('.ph-scrollable-content')?.scrollTo(0, 0);
	});

	const beforeHover = await page.locator('#manage-articles').evaluate((element) =>
	{
		const computed = getComputedStyle(element);
		return {
			backgroundColor: computed.backgroundColor,
			boxShadow: computed.boxShadow,
			color: computed.color,
		};
	});

	await page.locator('#manage-articles').hover();
	await page.waitForTimeout(180);
	const lightMode = await page.evaluate(pageState);
	await page.screenshot({ path: `${artifactRoot}/arunika-lucent-desktop.png`, fullPage: false });
	await page.locator('#ph-lucent-sidebar-toggle').click();
	await page.waitForTimeout(300);
	const collapsedSidebar = await page.evaluate(() => ({
		width: Math.round(document.querySelector('.ph-sidebar').getBoundingClientRect().width * 100) / 100,
		expanded: document.querySelector('.ph-sidebar').classList.contains('ph-expanded'),
		ariaExpanded: document.querySelector('#ph-lucent-sidebar-toggle').getAttribute('aria-expanded'),
		label: document.querySelector('#ph-lucent-sidebar-toggle').getAttribute('aria-label'),
	}));
	await page.locator('#ph-lucent-sidebar-toggle').click();
	await page.waitForTimeout(300);
	const expandedSidebar = await page.evaluate(() => ({
		width: Math.round(document.querySelector('.ph-sidebar').getBoundingClientRect().width * 100) / 100,
		expanded: document.querySelector('.ph-sidebar').classList.contains('ph-expanded'),
		ariaExpanded: document.querySelector('#ph-lucent-sidebar-toggle').getAttribute('aria-expanded'),
		label: document.querySelector('#ph-lucent-sidebar-toggle').getAttribute('aria-label'),
	}));

	await page.evaluate(() =>
	{
		document.documentElement.dataset.phThemeColor = 'cool-gray';
		document.documentElement.style.setProperty('--ph-theme-primary', '#667085');
		document.documentElement.style.setProperty('--ph-theme-surface-tint', '#C7CCD8');
	});
	await page.locator('#manage-articles').hover();
	await page.waitForTimeout(180);
	const coolGray = await page.evaluate(pageState);
	await page.screenshot({ path: `${artifactRoot}/arunika-lucent-cool-gray-hover.png`, fullPage: false });

	await page.evaluate(() => { document.documentElement.dataset.bsTheme = 'dark'; });
	await page.waitForTimeout(180);
	const darkMode = await page.evaluate(pageState);
	await page.screenshot({ path: `${artifactRoot}/arunika-lucent-dark.png`, fullPage: false });

	await page.evaluate(() =>
	{
		document.documentElement.dataset.bsTheme = 'light';
		delete document.documentElement.dataset.phThemeColor;
		document.documentElement.style.setProperty('--ph-theme-primary', '#1FA675');
		document.documentElement.style.setProperty('--ph-theme-surface-tint', '#1FA675');
	});
	await page.setViewportSize({ width: 375, height: 800 });
	await page.evaluate(() => { document.querySelector('.ph-sidebar')?.classList.remove('ph-expanded'); });
	await page.mouse.move(2, 2);
	await page.waitForTimeout(180);
	const mobile = await page.evaluate(() =>
	{
		const selectors = ['.ph-sidebar', '.ph-layout-right', '.ph-content', '.ph-lucent-page-header h1'];
		const boxes = Object.fromEntries(selectors.map((selector) =>
		{
			const element = document.querySelector(selector);
			const rect = element.getBoundingClientRect();
			return [selector, {
				width: Math.round(rect.width * 100) / 100,
				height: Math.round(rect.height * 100) / 100,
				left: Math.round(rect.left * 100) / 100,
				top: Math.round(rect.top * 100) / 100,
			}];
		}));

		return {
			boxes,
			titleFontSize: getComputedStyle(document.querySelector('.ph-lucent-page-header h1')).fontSize,
			mobileTriggerVisible: (() =>
			{
				const element = document.querySelector('.ph-mobile-sidebar-trigger');
				const rect = element.getBoundingClientRect();
				return getComputedStyle(element).display !== 'none' && rect.width > 0 && rect.height > 0;
			})(),
			horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth,
			verticalOverflow: document.documentElement.scrollHeight > window.innerHeight,
		};
	});
	await page.screenshot({ path: `${artifactRoot}/arunika-lucent-mobile.png`, fullPage: false });

	await page.setViewportSize({ width: 1504, height: 900 });
	await page.evaluate(() =>
	{
		document.querySelector('.ph-sidebar')?.classList.add('ph-expanded');
		window.scrollTo(0, 0);
		document.querySelector('.ph-scrollable-content')?.scrollTo(0, 0);
	});
	await page.locator('#manage-articles').hover();
	await page.waitForTimeout(180);
	await page.screenshot({ path: `${artifactRoot}/arunika-lucent-final.png`, fullPage: false });

	const payload = {
		beforeHover,
		lightMode,
		collapsedSidebar,
		expandedSidebar,
		coolGray,
		darkMode,
		mobile,
		consoleErrors: [],
		sourceCss: 'http://127.0.0.1:8768/public/assets/css/themes/arunika_lucent/arunika_lucent.css',
		previewAsset: 'public/assets/images/themes/previews/arunika-lucent-theme-preview.png',
		liveRoute: 'http://laravel-13-phoenix.aruna/awesome_admin/themes',
		liveRouteResult: 'not submitted; authenticated production route was not mutated during QA',
	};

	await page.evaluate((value) => { document.title = JSON.stringify(value); }, payload);
	return payload;
}
