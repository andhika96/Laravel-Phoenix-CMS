export async function verifyThemeMobile(tab, setViewport, theme, widths = [300, 400, 500])
{
	const assert = (await import('node:assert/strict')).default;

	for (const width of widths)
	{
		await setViewport(width, 844);
		await tab.reload();
		await tab.playwright.waitForTimeout(350);

		const baseline = await tab.playwright.evaluate(() =>
		({
			marker: document.body.dataset.phMobileTheme,
			viewport: { width: innerWidth, height: innerHeight },
			overflow: document.documentElement.scrollWidth > innerWidth + 1,
			expanded: document.querySelector('#sidebar')?.classList.contains('ph-expanded'),
			statCount: document.querySelectorAll('.ph-dashboard-stat').length,
			chartCount: document.querySelectorAll('#echartSeriesSimpleBar_ProjectionActual').length
		}));

		assert.equal(baseline.marker, theme);
		assert.equal(baseline.viewport.width, width);
		assert.equal(baseline.overflow, false);
		assert.equal(baseline.expanded, false);
		assert.equal(baseline.statCount, 4);
		assert.equal(baseline.chartCount, 1);

		const opener = tab.playwright.getByRole('button', { name: 'Open navigation', exact: true }).filter({ visible: true });
		assert.equal(await opener.count(), 1);
		await opener.click();
		await tab.playwright.waitForTimeout(250);

		const opened = await tab.playwright.evaluate(() =>
		({
			expanded: document.querySelector('#sidebar')?.classList.contains('ph-expanded'),
			mainInert: Boolean(document.querySelector('.ph-layout-right')?.inert),
			overflow: document.documentElement.scrollWidth > innerWidth + 1,
			closeCount: [...document.querySelectorAll('.ph-mobile-sidebar-close')].filter((node) => node.offsetWidth > 0).length,
			menuCount: document.querySelectorAll('#sidebar a[href]').length
		}));

		assert.equal(opened.expanded, true);
		assert.equal(opened.mainInert, true);
		assert.equal(opened.overflow, false);
		assert.equal(opened.closeCount, 1);
		assert.ok(opened.menuCount >= 2);

		await tab.playwright.getByRole('button', { name: 'Close navigation', exact: true }).filter({ visible: true }).click();
		await tab.playwright.waitForTimeout(250);
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
}
