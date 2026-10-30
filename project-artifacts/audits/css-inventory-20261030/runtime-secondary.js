async (page) => {
    const out = [];
    for (const path of ['/article', '/manage_event/add', '/manage_coverimage/add', '/pagebuilder', '/pagebuilder-elementor/create', '/pagebuilder-elementor/v2.3/create']) {
        try {
            const response = await page.goto('https://laravel-13-phoenix.aruna' + path, { waitUntil: 'domcontentloaded', timeout: 25000 });
            await page.waitForLoadState('networkidle', { timeout: 6000 }).catch(() => {});
            out.push(await page.evaluate(({ path, status }) => ({
                path, status, url: location.href,
                css: [...document.styleSheets].map(s => s.href).filter(Boolean),
                resources: performance.getEntriesByType('resource').filter(r => /\.css(?:\?|$)/.test(r.name)).map(r => ({ url: r.name, status: r.responseStatus })),
                moduleStyles: [...document.querySelectorAll('style[data-pb-module-style]')].map(s => s.getAttribute('data-pb-module-style')),
            }), { path, status: response.status() }));
        } catch (error) {
            out.push({ path, error: error.message });
        }
    }
    return out;
}
