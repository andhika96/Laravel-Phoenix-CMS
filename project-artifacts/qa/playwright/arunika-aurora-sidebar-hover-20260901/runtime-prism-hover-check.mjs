async (page) =>
{
	await page.setContent(`
		<!doctype html>
		<html data-bs-theme="light" data-ph-theme-color="cool-gray" style="--ph-theme-primary: #667085; --ph-theme-surface-tint: #C7CCD8;">
			<head>
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<link rel="stylesheet" href="http://laravel-13-phoenix.aruna/assets/css/themes/arunika_prism/arunika_prism.css">
			</head>
			<body class="ph-theme-arunika-prism">
				<div class="ph-app-shell d-flex w-100 h-100">
					<aside class="ph-sidebar ph-expanded" id="sidebar">
						<div class="ph-sidebar-logo-container">
							<span class="ph-app-logo-text">LaraPhoenix CMS</span>
						</div>
						<div id="sidebar-scroll-content">
							<div class="ph-list-group-wrapper">
								<a id="manage-event" class="list-group-item list-group-item-action" href="#">
									<div class="ph-nav-icon">▣</div>
									<span class="ph-nav-text">Manage Event</span>
								</a>
							</div>
						</div>
					</aside>
				</div>
			</body>
		</html>
	`);

	await page.waitForTimeout(300);
	const item = page.locator('#manage-event');
	await page.mouse.move(1000, 700);
	await page.waitForTimeout(250);
	const before = await item.evaluate((element) => getComputedStyle(element).backgroundColor);
	await item.hover();
	await page.waitForTimeout(250);
	const styles = await item.evaluate((element) =>
	{
		const computed = getComputedStyle(element);
		return {
			background: computed.backgroundColor,
			boxShadow: computed.boxShadow,
			prismHover: computed.getPropertyValue('--ph-prism-sidebar-hover').trim(),
		};
	});
	await page.evaluate(({ before, styles }) =>
	{
		document.title = JSON.stringify({ before, ...styles });
	}, { before, styles });
}
