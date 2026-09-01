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
		const isPanel = ([red, green, blue]) => red > 247 && green > 247 && blue > 247;
		const scanX = (y, direction) =>
		{
			const start = direction === 'left' ? 0 : canvas.width - 1;
			const end = direction === 'left' ? canvas.width : -1;
			const step = direction === 'left' ? 1 : -1;
			for (let x = start; x !== end; x += step)
			{
				if (isPanel(pixel(x, y))) return x;
			}
			return null;
		};
		const scanY = (x, direction) =>
		{
			const start = direction === 'top' ? 0 : canvas.height - 1;
			const end = direction === 'top' ? canvas.height : -1;
			const step = direction === 'top' ? 1 : -1;
			for (let y = start; y !== end; y += step)
			{
				if (isPanel(pixel(x, y))) return y;
			}
			return null;
		};

		const centerX = Math.floor(canvas.width / 2);
		const centerY = Math.floor(canvas.height / 2);
		return {
			dimensions: { width: canvas.width, height: canvas.height },
			background: pixel(0, 0),
			center: pixel(centerX, centerY),
			left: scanX(centerY, 'left'),
			right: scanX(centerY, 'right'),
			top: scanY(centerX, 'top'),
			bottom: scanY(centerX, 'bottom'),
		};
	});

	await page.evaluate((value) => { document.title = JSON.stringify(value); }, result);
}
