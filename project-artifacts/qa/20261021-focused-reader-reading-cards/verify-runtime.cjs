const assert = require('node:assert/strict');
const fs = require('node:fs');
module.exports = async function verify(ctx) {
    const page = await ctx.newPage();
    const base = 'http://laravel-13-phoenix.aruna';
    const results = [];
    try {
        for (const key of ['focused-reader', 'knowledge-toc', 'editorial-feature']) {
            for (const width of [320, 390, 834, 1440]) {
                await page.setViewportSize({width, height: 900});
                const response = await page.goto(`${base}/manage_article/templates/preview/detail/${key}`);
                assert.equal(response.status(), 200);
                await page.evaluate(() => document.fonts.ready);
                const result = await page.evaluate(() => {
                    const rect = s => document.querySelector(s)?.getBoundingClientRect().width;
                    const nav = document.querySelector('.article-reading-navigation__grid');
                    return {overflow: document.documentElement.scrollWidth > innerWidth, content: rect('.article-rich-content'), cover: rect('.article-reader-cover'), ratio: document.querySelector('.article-reader-cover img') && getComputedStyle(document.querySelector('.article-reader-cover img')).aspectRatio, cards: document.querySelectorAll('.article-reading-navigation__card').length, columns: nav ? getComputedStyle(nav).gridTemplateColumns.split(' ').length : 0, legacy: document.querySelectorAll('.article-detail-navigation').length};
                });
                assert.equal(result.overflow, false, `${key} ${width} overflow`);
                if (key === 'focused-reader') {
                    assert.equal(result.cards, 2);
                    assert.equal(result.columns, width < 576 ? 1 : 2);
                    if (width === 1440) {assert.equal(result.content, 880); assert.equal(result.cover, 1040);}
                    assert.equal(result.ratio, width < 576 ? '16 / 9' : '5 / 2');
                    assert.equal(await page.locator('.article-reading-navigation__body img').evaluateAll(imgs => imgs.every(img => img.complete && img.naturalWidth > 0)), true);
                    await page.locator('.article-reading-navigation').scrollIntoViewIfNeeded();
                    await page.screenshot({path: `${__dirname}/focused-reader-${width}.png`});
                } else {assert.equal(result.cards, 0); assert.equal(result.legacy, 1);}
                results.push({template: key, width, ...result});
            }
        }
        await page.setViewportSize({width: 1440, height: 900});
        await page.goto(`${base}/manage_article/templates/preview/detail/focused-reader`);
        const card = page.locator('.article-reading-navigation__card--next');
        const state = () => card.evaluate(e => ({background: getComputedStyle(e).backgroundColor, border: getComputedStyle(e).borderColor, shadow: getComputedStyle(e).boxShadow, title: getComputedStyle(e.querySelector('strong')).color, arrow: getComputedStyle(e.querySelector('.article-reading-navigation__arrow')).backgroundColor, transform: getComputedStyle(e.querySelector('.article-reading-navigation__arrow')).transform, outline: getComputedStyle(e).outlineWidth}));
        const normal = await state();
        await card.hover(); await page.waitForTimeout(220);
        const hover = await state();
        assert.notEqual(hover.background, normal.background); assert.notEqual(hover.border, normal.border); assert.notEqual(hover.title, normal.title); assert.notEqual(hover.arrow, normal.arrow); assert.equal(hover.shadow, 'none'); assert.equal(hover.transform, 'matrix(1, 0, 0, 1, 3, 0)');
        await page.screenshot({path: `${__dirname}/focused-reader-hover.png`});
        await page.mouse.move(0, 0); await page.keyboard.press('Tab'); await card.focus();
        assert.equal(await card.evaluate(e => e.matches(':focus-visible')), true);
        assert.equal((await state()).outline, '2px');
        await page.emulateMedia({reducedMotion: 'reduce'}); await card.hover();
        assert.equal(await card.locator('.article-reading-navigation__arrow').evaluate(e => getComputedStyle(e).transitionDuration), '0s');
        assert.equal((await state()).transform, 'none');
        await page.goto(`${base}/article/qa-ckeditor-panduan-sistem-andal-uji-seluruh-format-konten`);
        assert.equal(await page.locator('.article-reading-navigation__card').count(), 1);
        const nav = page.locator('.article-reading-navigation__grid');
        assert.equal(await nav.evaluate(e => e.firstElementChild.getBoundingClientRect().width === e.getBoundingClientRect().width), true);
        const previous = await page.locator('.article-reading-navigation__card').getAttribute('href');
        await page.locator('.article-reading-navigation__card').click();
        assert.equal(page.url(), previous);
        assert.equal(await page.locator('.article-reading-navigation__card').count(), 2);
        await page.locator('.article-reading-navigation__card--next').click();
        assert.ok(page.url().endsWith('/qa-ckeditor-panduan-sistem-andal-uji-seluruh-format-konten'));
        const report = {cases: results, interaction: {normal, hover, keyboardFocus: true, reducedMotion: true}, publicNavigationRoundtrip: true};
        fs.writeFileSync(`${__dirname}/runtime-verification.json`, JSON.stringify(report, null, 2));
        return report;
    } finally {await page.close();}
};
