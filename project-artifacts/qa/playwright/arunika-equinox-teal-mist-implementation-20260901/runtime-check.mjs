async (page) =>
{
	const harnessUrl = 'http://127.0.0.1:8768/project-artifacts/qa/playwright/arunika-equinox-teal-mist-implementation-20260901/harness.html';
	await page.setViewportSize({ width: 1717, height: 916 });
	await page.goto(harnessUrl, { waitUntil: 'domcontentloaded' });
	await page.waitForFunction(() => document.styleSheets.length >= 3);
	await page.waitForTimeout(750);
	await page.evaluate(() =>
	{
		window.scrollTo(0, 0);
		document.querySelector('.ph-scrollable-content')?.scrollTo(0, 0);
	});
	await page.waitForTimeout(500);

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
	await page.waitForTimeout(250);

	const metrics = await page.evaluate(() =>
	{
		const style = (selector) =>
		{
			const element = document.querySelector(selector);
			const computed = getComputedStyle(element);
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
				top: Math.round(rect.top * 100) / 100,
				left: Math.round(rect.left * 100) / 100,
			};
		};

		const landscape = getComputedStyle(document.querySelector('.ph-sidebar'), '::after');

		return {
			viewport: { width: window.innerWidth, height: window.innerHeight },
			body: style('body'),
			sidebar: style('.ph-sidebar'),
			toggle: style('.ph-sidebar-toggle'),
			title: style('.ph-equinox-dashboard-hero h4'),
			subtitle: style('.ph-equinox-dashboard-hero-subtitle'),
			hero: style('.ph-equinox-dashboard-hero'),
			grid: style('.ph-equinox-dashboard-grid'),
			gridLabel: style('.ph-equinox-dashboard-grid .lead'),
			hoveredMenuItem: style('#manage-articles'),
			activeMenuItem: style('#dashboard'),
			sidebarLandscape: {
				backgroundImage: landscape.backgroundImage,
				height: landscape.height,
				opacity: landscape.opacity,
			},
			horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth,
			verticalOverflow: document.documentElement.scrollHeight > window.innerHeight,
		};
	});

	await page.evaluate(() => { document.documentElement.dataset.bsTheme = 'dark'; });
	await page.locator('#manage-articles').hover();
	await page.waitForTimeout(250);
	const darkMode = await page.evaluate(() =>
	{
		const read = (selector) =>
		{
			const element = document.querySelector(selector);
			const computed = getComputedStyle(element);
			return {
				backgroundColor: computed.backgroundColor,
				boxShadow: computed.boxShadow,
				color: computed.color,
			};
		};

		return {
			body: read('body'),
			sidebar: read('.ph-sidebar'),
			hoveredMenuItem: read('#manage-articles'),
			activeMenuItem: read('#dashboard'),
			sidebarLandscape: (() =>
			{
				const computed = getComputedStyle(document.querySelector('.ph-sidebar'), '::after');
				return { backgroundImage: computed.backgroundImage, opacity: computed.opacity };
			})(),
		};
	});
	await page.screenshot({ path: 'project-artifacts/qa/playwright/arunika-equinox-teal-mist-implementation-20260901/equinox-teal-mist-dark.png', fullPage: false });

	await page.evaluate(() => { document.documentElement.dataset.bsTheme = 'light'; });
	await page.setViewportSize({ width: 375, height: 800 });
	await page.evaluate(() => { document.querySelector('.ph-sidebar').classList.remove('ph-expanded'); });
	await page.mouse.move(2, 2);
	await page.waitForTimeout(250);
	const mobile = await page.evaluate(() =>
	{
		const selectors = ['.ph-sidebar', '.ph-layout-right', '.ph-equinox-dashboard-hero', '.ph-equinox-dashboard-grid'];
		const boxes = Object.fromEntries(selectors.map((selector) =>
		{
			const rect = document.querySelector(selector).getBoundingClientRect();
			return [selector, {
				width: Math.round(rect.width * 100) / 100,
				height: Math.round(rect.height * 100) / 100,
				left: Math.round(rect.left * 100) / 100,
				top: Math.round(rect.top * 100) / 100,
			}];
		}));

		return {
			boxes,
			titleFontSize: getComputedStyle(document.querySelector('.ph-equinox-dashboard-hero h4')).fontSize,
			horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth,
			verticalOverflow: document.documentElement.scrollHeight > window.innerHeight,
		};
	});
	await page.screenshot({ path: 'project-artifacts/qa/playwright/arunika-equinox-teal-mist-implementation-20260901/equinox-teal-mist-mobile.png', fullPage: false });

	const payload = {
		beforeHover,
		...metrics,
		darkMode,
		mobile,
		sourceCss: 'http://127.0.0.1:8768/public/assets/css/themes/arunika_equinox/arunika_equinox.css',
		liveRoute: 'http://laravel-13-phoenix.aruna/awesome_admin',
		liveRouteResult: 'redirected to /auth/login during read-only QA',
	};

	await page.setViewportSize({ width: 1717, height: 916 });
	await page.evaluate(() =>
	{
		document.documentElement.dataset.bsTheme = 'light';
		document.querySelector('.ph-sidebar').classList.add('ph-expanded');
		window.scrollTo(0, 0);
		document.querySelector('.ph-scrollable-content')?.scrollTo(0, 0);
	});
	await page.locator('#manage-articles').hover();
	await page.waitForTimeout(250);
	await page.screenshot({ path: 'project-artifacts/qa/playwright/arunika-equinox-teal-mist-implementation-20260901/equinox-teal-mist-implementation.png', fullPage: false });
	await page.evaluate((value) => { document.title = JSON.stringify(value); }, payload);
}
