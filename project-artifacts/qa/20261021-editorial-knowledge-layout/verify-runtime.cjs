const assert = require('node:assert/strict');
const fs = require('node:fs');
module.exports = async function verify(ctx) {
    const page = await ctx.newPage();
    page.setDefaultTimeout(8000);
    const base = 'http://laravel-13-phoenix.aruna';
    const cases = [], interactions = [];
    try {
        for (const template of ['editorial-feature', 'knowledge-toc', 'focused-reader']) {
            for (const width of [320, 390, 576, 834, 1024, 1440]) {
                await page.setViewportSize({width, height: 900});
                const response = await page.goto(`${base}/manage_article/templates/preview/detail/${template}`);
                assert.equal(response.status(), 200);
                await page.evaluate(() => document.fonts.ready);
                const metrics = await page.evaluate(() => {
                    const rect = selector => document.querySelector(selector)?.getBoundingClientRect().toJSON();
                    const toc = document.querySelector('.article-toc');
                    const nav = document.querySelector('.article-reading-navigation__grid');
                    return {overflow: document.documentElement.scrollWidth > innerWidth, content: rect('.article-rich-content'), cover: rect('.article-feature-hero') || rect('.article-detail__cover'), main: rect('.article-detail__main'), toc: rect('.article-toc'), tocPosition: toc && getComputedStyle(toc).position, columns: getComputedStyle(nav).gridTemplateColumns.split(' ').length, cards: nav.children.length, fontSize: getComputedStyle(document.querySelector('h1')).fontSize, loaded: [...document.querySelectorAll('img')].every(i => i.complete && i.naturalWidth > 0)};
                });
                assert.equal(metrics.overflow, false, `${template} ${width} overflow`);
                assert.equal(metrics.cards, 2);
                assert.equal(metrics.columns, width < 576 ? 1 : 2);
                assert.equal(metrics.loaded, true);
                if (width === 1440) assert.equal(metrics.content.width, 880);
                if (template === 'knowledge-toc') {
                    assert.equal(metrics.tocPosition, width >= 1200 ? 'sticky' : 'static');
                    if (width < 1200) assert.ok(metrics.toc.bottom <= metrics.main.top);
                    else assert.ok(metrics.toc.left > metrics.main.right);
                }
                if (template === 'editorial-feature' && width < 576) {
                    assert.equal(await page.locator('.article-feature-hero').evaluate(e => e.querySelector('img').getBoundingClientRect().bottom <= e.querySelector('.article-feature-hero__overlay').getBoundingClientRect().top + 1), true);
                }
                if ([390, 834, 1440].includes(width) && template !== 'focused-reader') {
                    await page.screenshot({path: `${__dirname}/${template}-${width}-top.png`});
                    await page.locator('.article-reading-navigation').scrollIntoViewIfNeeded();
                    await page.screenshot({path: `${__dirname}/${template}-${width}-cards.png`});
                }
                cases.push({template, width, ...metrics});
            }
            await page.setViewportSize({width: 1440, height: 900});
            await page.goto(`${base}/manage_article/templates/preview/detail/${template}`);
            const card = page.locator('.article-reading-navigation__card--next');
            const normal = await card.evaluate(e => getComputedStyle(e).backgroundColor);
            await card.hover(); await page.waitForTimeout(220);
            assert.notEqual(await card.evaluate(e => getComputedStyle(e).backgroundColor), normal);
            assert.equal(await card.evaluate(e => getComputedStyle(e).boxShadow), 'none');
            await page.mouse.move(0, 0); await page.keyboard.press('Tab'); await card.focus();
            assert.equal(await card.evaluate(e => e.matches(':focus-visible')), true);
            assert.equal(await card.evaluate(e => getComputedStyle(e).outlineWidth), '2px');
            await page.emulateMedia({reducedMotion: 'reduce'}); await card.hover();
            assert.equal(await card.locator('.article-reading-navigation__arrow').evaluate(e => getComputedStyle(e).transform), 'none');
            await page.emulateMedia({reducedMotion: 'no-preference'});
            interactions.push({template, hover: true, focus: true, reducedMotion: true});
        }
        await page.setViewportSize({width: 390, height: 900});
        await page.goto(`${base}/manage_article/templates/preview/detail/knowledge-toc`);
        const summary = page.locator('.article-toc summary');
        await summary.click(); assert.equal(await page.locator('.article-toc details').getAttribute('open'), null);
        await summary.focus(); await page.keyboard.press('Enter');
        assert.notEqual(await page.locator('.article-toc details').getAttribute('open'), null);
        const href = await page.locator('.article-toc a').first().getAttribute('href');
        await page.locator('.article-toc a').first().click();
        await page.waitForFunction(hash => location.hash === hash, href);
        await page.waitForFunction(hash => { const rect = document.querySelector(hash).getBoundingClientRect(); return rect.top >= 0 && rect.top < innerHeight; }, href);
        assert.ok(await page.locator(href).evaluate(e => e.getBoundingClientRect().top >= 0 && e.getBoundingClientRect().top < innerHeight));
        const result = {cases, interactions, tocToggleByMouseAndKeyboard: true, previewTocAnchorNavigation: true};
        fs.writeFileSync(`${__dirname}/runtime-verification.json`, JSON.stringify(result, null, 2));
        return {cases: cases.length, interactions, tocToggleByMouseAndKeyboard: true, previewTocAnchorNavigation: true};
    } finally {await page.close();}
};
