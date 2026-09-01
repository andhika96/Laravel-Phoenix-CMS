async (page) =>
{
	await page.setViewportSize({ width: 1800, height: 1000 });
	await page.goto('file:///D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/playwright/arunika-equinox-teal-mist-implementation-20260901/comparison.html');
	await page.waitForLoadState('networkidle');
	await page.waitForFunction(() => [...document.images].every((image) => image.complete && image.naturalWidth > 0));
	await page.waitForTimeout(300);
	await page.screenshot({ path: 'project-artifacts/qa/playwright/arunika-equinox-teal-mist-implementation-20260901/preview-vs-implementation.png', fullPage: true });
}
