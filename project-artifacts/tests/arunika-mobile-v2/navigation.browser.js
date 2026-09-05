// Browser-run scenario for the Vue 3 CDN mobile drawer.
// The runner supplies `tab` from the supported Codex Browser API.
export async function verifyMobileNavigation(tab)
{
	const assert = (await import('node:assert/strict')).default;
	const opener = tab.playwright.getByRole('button', { name: 'Open navigation', exact: true });

	assert.equal(await opener.count(), 1);
	await opener.click();

	const opened = await tab.playwright.evaluate(() =>
	({
		expanded: document.querySelector('#sidebar')?.classList.contains('ph-expanded'),
		mainInert: Boolean(document.querySelector('.ph-layout-right')?.inert),
		overflow: document.documentElement.scrollWidth > innerWidth + 1,
		visibleClose: [...document.querySelectorAll('.ph-mobile-sidebar-close')].filter((node) => node.offsetWidth > 0).length
	}));

	assert.equal(opened.expanded, true);
	assert.equal(opened.mainInert, true);
	assert.equal(opened.overflow, false);
	assert.equal(opened.visibleClose, 1);

	await tab.playwright.getByRole('button', { name: 'Close navigation', exact: true }).filter({ visible: true }).click();
	await tab.playwright.getByRole('button', { name: 'Open navigation', exact: true }).press('Escape');

	const closed = await tab.playwright.evaluate(() =>
	({
		expanded: document.querySelector('#sidebar')?.classList.contains('ph-expanded'),
		mainInert: Boolean(document.querySelector('.ph-layout-right')?.inert),
		focusReturned: document.activeElement?.classList.contains('ph-mobile-sidebar-trigger')
	}));

	assert.equal(closed.expanded, false);
	assert.equal(closed.mainInert, false);
	assert.equal(closed.focusReturned, true);
}
