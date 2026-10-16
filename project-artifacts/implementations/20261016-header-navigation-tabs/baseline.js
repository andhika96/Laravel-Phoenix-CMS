async page => {
    await page.locator('body.header-editor-open').waitFor();
    const baseline = await page.evaluate(() => {
        const root = document.querySelector('#headerNavigationEditor');
        return {
            fields: [...root.querySelectorAll('input[id], select[id]')].map(e => ({id:e.id, type:e.type, options:e.tagName === 'SELECT' ? [...e.options].map(o=>o.value) : null})),
            groups: [...root.querySelectorAll('[data-control]')].map(e=>({name:e.dataset.control, values:[...e.querySelectorAll('[data-value]')].map(b=>b.dataset.value)})),
            linked: [...root.querySelectorAll('.box-link-toggle[id]')].map(e=>e.id),
            config: JSON.parse(root.querySelector('#jsonOutput').textContent)
        };
    });
    return baseline;
}

