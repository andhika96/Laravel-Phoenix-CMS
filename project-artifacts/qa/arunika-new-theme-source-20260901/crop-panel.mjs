async (page) =>
{
	const crop = { x: 125, y: 122, width: 1254, height: 884 };
	await page.goto('http://127.0.0.1:8769/original-source.jpg', { waitUntil: 'domcontentloaded' });
	await page.waitForFunction(() => document.images[0]?.complete && document.images[0]?.naturalWidth > 0);

	const downloadPromise = page.waitForEvent('download');
	await page.evaluate((area) =>
	{
		const image = document.images[0];
		const canvas = document.createElement('canvas');
		canvas.width = area.width;
		canvas.height = area.height;
		const context = canvas.getContext('2d');
		context.drawImage(image, area.x, area.y, area.width, area.height, 0, 0, area.width, area.height);

		const link = document.createElement('a');
		link.download = 'cropped-theme-source.png';
		link.href = canvas.toDataURL('image/png');
		document.body.appendChild(link);
		link.click();
	}, crop);

	const download = await downloadPromise;
	await download.saveAs('D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/arunika-new-theme-source-20260901/cropped-theme-source.png');
	await page.evaluate((area) => { document.title = JSON.stringify({ crop: area, output: 'cropped-theme-source.png' }); }, crop);
}
