async (page) =>
{
	await page.goto('http://127.0.0.1:8769/original-source.jpg', { waitUntil: 'domcontentloaded' });
	await page.waitForFunction(() => document.images[0]?.complete && document.images[0]?.naturalWidth > 0);

	const result = await page.evaluate(() =>
	{
		const image = document.images[0];
		const canvas = document.createElement('canvas');
		canvas.width = image.naturalWidth;
		canvas.height = image.naturalHeight;
		const context = canvas.getContext('2d', { willReadFrequently: true });
		context.drawImage(image, 0, 0);
		const pixels = context.getImageData(0, 0, canvas.width, canvas.height).data;
		const pixel = (x, y) =>
		{
			const offset = (y * canvas.width + x) * 4;
			return [pixels[offset], pixels[offset + 1], pixels[offset + 2]];
		};
		const range = (start, end) => Array.from({ length: end - start + 1 }, (_, index) => start + index);
		return {
			top: Object.fromEntries(range(119, 126).map((y) => [y, pixel(752, y)])),
			bottom: Object.fromEntries(range(1001, 1008).map((y) => [y, pixel(752, y)])),
			left: Object.fromEntries(range(122, 129).map((x) => [x, pixel(x, 564)])),
			right: Object.fromEntries(range(1375, 1382).map((x) => [x, pixel(x, 564)])),
		};
	});

	await page.evaluate((value) => { document.title = JSON.stringify(value); }, result);
}
