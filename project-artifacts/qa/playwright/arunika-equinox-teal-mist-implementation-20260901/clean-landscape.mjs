async (page) =>
{
	const sourceUrl = 'http://laravel-13-phoenix.aruna/assets/images/themes/arunika_equinox/arunika-equinox-sidebar-landscape.png';
	await page.goto(sourceUrl, { waitUntil: 'domcontentloaded' });
	await page.evaluate((imageUrl) =>
	{
		document.body.innerHTML = `<img id="source" src="${imageUrl}" alt=""><a id="download" download="arunika-equinox-sidebar-landscape.png">download</a>`;
	}, sourceUrl);
	await page.waitForFunction(() =>
	{
		const image = document.querySelector('#source');
		return image.complete && image.naturalWidth > 0;
	});

	const downloadPromise = page.waitForEvent('download');
	await page.evaluate(() =>
	{
		const image = document.querySelector('#source');
		const canvas = document.createElement('canvas');
		canvas.width = image.naturalWidth;
		canvas.height = image.naturalHeight;
		const context = canvas.getContext('2d', { willReadFrequently: true });
		context.drawImage(image, 0, 0);

		const pixels = context.getImageData(0, 0, canvas.width, canvas.height);
		for (let index = 0; index < pixels.data.length; index += 4)
		{
			const red = pixels.data[index];
			const green = pixels.data[index + 1];
			const blue = pixels.data[index + 2];
			const maximum = Math.max(red, green, blue);
			const minimum = Math.min(red, green, blue);

			if (minimum > 225 && maximum - minimum < 15)
			{
				pixels.data[index + 3] = 0;
			}
		}

		context.putImageData(pixels, 0, 0);
		const link = document.querySelector('#download');
		link.href = canvas.toDataURL('image/png');
		link.click();
	});

	const download = await downloadPromise;
	await download.saveAs('D:/Laragon/www/laravel-13-phoenix/public/assets/images/themes/arunika_equinox/arunika-equinox-sidebar-landscape.png');
}
