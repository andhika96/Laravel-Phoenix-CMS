async (page) =>
{
	const state = await page.evaluate(() =>
	{
		const read = (selector) =>
		{
			const element = document.querySelector(selector);
			const rect = element.getBoundingClientRect();
			return {
				className: element.className,
				rect: { left: rect.left, top: rect.top, width: rect.width, height: rect.height },
				scrollTop: element.scrollTop,
				clientHeight: element.clientHeight,
				scrollHeight: element.scrollHeight,
				transform: getComputedStyle(element).transform,
				display: getComputedStyle(element).display,
			};
		};

		return {
			viewport: { width: innerWidth, height: innerHeight },
			body: read('body'),
			shell: read('.ph-app-shell'),
			sidebar: read('.ph-sidebar'),
			layout: read('.ph-layout-right'),
			topbar: read('.ph-top-bar'),
			main: read('.ph-main-panel'),
			scrollable: read('.ph-scrollable-content'),
			hero: read('.ph-equinox-dashboard-hero'),
		};
	});
	await page.evaluate((value) => { document.title = JSON.stringify(value); }, state);
}
