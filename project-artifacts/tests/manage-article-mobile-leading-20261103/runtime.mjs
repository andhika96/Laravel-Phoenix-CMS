import assert from 'node:assert/strict';
import {writeFile} from 'node:fs/promises';
import {chromium, firefox} from 'playwright-core';

const engine = process.argv.includes('--firefox') ? 'firefox' : 'chrome';
const browser = await (engine === 'chrome' ? chromium : firefox).launch(engine === 'chrome' ? {channel: 'chrome'} : {});
const origin = 'https://laravel-13-phoenix.aruna';
const output = 'project-artifacts/qa/manage-article-mobile-leading-20261103';
const cases = [], errors = [], writes = [];

try {
    const context = await browser.newContext({ignoreHTTPSErrors: true, viewport: {width: 414, height: 950}});
    await context.route('**/*', route => {
        const request = route.request();
        const url = new URL(request.url());
        if (url.origin === origin && !['GET', 'HEAD', 'OPTIONS'].includes(request.method()) && url.pathname !== '/auth/login') {
            writes.push({method: request.method(), path: url.pathname});
            return route.abort();
        }
        return route.continue();
    });
    const page = await context.newPage();
    page.on('pageerror', error => errors.push(error.message));
    page.on('console', message => {
        if (message.text().includes('[Vue warn]')) errors.push(message.text());
    });
    await page.goto(origin + '/manage_article', {waitUntil: 'domcontentloaded'});
    if (new URL(page.url()).pathname === '/auth/login') {
        const form = page.locator('form').filter({has: page.locator('input[name="password"]')});
        await form.locator('input[name="email"]').fill(process.env.PH_ARTICLE_QA_EMAIL);
        await form.locator('input[name="password"]').fill(process.env.PH_ARTICLE_QA_PASSWORD);
        await form.locator('.btn-submit-login').click();
        await page.waitForURL(url => url.pathname !== '/auth/login');
        if (new URL(page.url()).pathname !== '/manage_article') await page.goto(origin + '/manage_article');
    }
    const app = page.locator('#ph-app-manage-article');
    const rows = app.locator('tr[data-article-id]');
    const first = rows.first(), second = rows.nth(1);
    const title = first.locator('.ph-article-title');
    const expand = first.locator('.ph-article-expand');
    const checkbox = first.locator('.checkids');
    const enter = app.getByRole('button', {name: 'Masuk mode pilih artikel'});
    const exit = app.getByRole('button', {name: 'Keluar dari mode pilih'});
    const detail = app.locator('.ph-dtr-child-row');
    await first.waitFor({timeout: 20000});
    const geometry = () => first.evaluate(row => {
        const bounds = element => element.getBoundingClientRect();
        const box = bounds(row);
        const detail = bounds(row.querySelector('.ph-article-expand'));
        const title = bounds(row.querySelector('.ph-article-title'));
        const stamp = row.querySelector('.ph-article-mobile-status')?.getBoundingClientRect();
        const menu = bounds(row.querySelector('.ph-article-row-actions button'));
        const check = bounds(row.querySelector('.checkids'));
        const header = [...document.querySelectorAll('.ph-article-header-actions > .btn')].map(button => {
            const range = document.createRange();
            range.selectNodeContents(button);
            const content = range.getBoundingClientRect(), rect = bounds(button);
            return {height: rect.height, center: Math.abs(content.top + content.height / 2 - (rect.top + rect.height / 2))};
        });
        return {
            mode: getComputedStyle(row).display,
            left: detail.left - box.left, gap: check.left - detail.right,
            center: Math.abs(detail.top + detail.height / 2 - (box.top + box.height / 2)),
            leading: detail.right <= title.left, checkHidden: check.width === 0,
            menuNextToStamp: !!stamp && menu.left >= stamp.right - 1 && menu.top < stamp.bottom,
            overflow: document.documentElement.scrollWidth > innerWidth + 1, header,
        };
    });
    for (const width of [414, 320, 768, 1440]) {
        await page.setViewportSize({width, height: 950});
        await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
        const g = await geometry();
        const mobile = width <= 414;
        assert.equal(g.mode, mobile ? 'grid' : 'table-row', JSON.stringify(g));
        assert.ok(g.center < 1 && !g.overflow, JSON.stringify(g));
        assert.equal(g.checkHidden, mobile);
        if (mobile) {
            assert.ok(g.leading && g.menuNextToStamp, JSON.stringify(g));
            assert.ok(g.header.every(button => button.height >= 44 && button.center < 2), JSON.stringify(g));
        } else {
            assert.ok(Math.abs(g.left - 20) < 1 && Math.abs(g.gap - 24) < 1, JSON.stringify(g));
        }
        cases.push({name: 'responsive', width, ...g});
    }
    await page.setViewportSize({width: 414, height: 950});
    await enter.waitFor();
    await title.click();
    await detail.waitFor();
    await expand.click();
    await detail.waitFor({state: 'hidden'});
    cases.push({name: 'tap and chevron disclose'});

    await title.hover();
    await page.mouse.down();
    await exit.waitFor({timeout: 3000});
    await page.mouse.up();
    assert.equal(await checkbox.isChecked(), true);
    assert.equal(await detail.count(), 0);
    assert.equal(await title.getAttribute('aria-pressed'), 'true');
    assert.equal(await expand.isVisible(), false);
    await second.locator('.ph-article-title-col .ph-article-meta').click();
    assert.equal(await second.locator('.checkids').isChecked(), true);
    await title.click();
    assert.equal(await checkbox.isChecked(), false);
    await second.locator('.ph-article-title').click();
    assert.equal(await second.locator('.checkids').isChecked(), false);
    assert.equal(await exit.isVisible(), true);
    cases.push({name: 'hold, multi-select, tap again and zero selected'});

    const all = app.locator('.ph-article-select-all-mobile input');
    await all.check();
    assert.equal(await rows.locator('input.checkids:checked').count(), await rows.count());
    const formValues = await app.locator('form').first().evaluate(form => new FormData(form).getAll('getSelected[]'));
    assert.deepEqual(formValues, await rows.evaluateAll(elements => elements.map(row => row.dataset.articleId)));
    await page.screenshot({path: `${output}/${engine}-414-mode-pilih.png`});
    await app.locator('select[name="changeStatus"]').selectOption({index: 1});
    await app.locator('.ph-article-bulk button[type="submit"]').click();
    const confirmation = page.locator('#ph-article-bulk-confirm');
    await confirmation.waitFor();
    await confirmation.locator('.modal-footer [data-bs-dismiss="modal"]').click();
    await confirmation.waitFor({state: 'hidden'});
    await page.setViewportSize({width: 1440, height: 950});
    await expand.waitFor();
    assert.equal(await checkbox.isChecked(), true);
    await page.setViewportSize({width: 414, height: 950});
    await exit.waitFor();
    assert.equal(await checkbox.isChecked(), true);
    await exit.click();
    await enter.waitFor();
    assert.equal(await rows.locator('input.checkids:checked').count(), 0);
    assert.equal(await enter.evaluate(el => el === document.activeElement), true);
    cases.push({name: 'select all, form payload, resize, exit'});

    await enter.focus();
    await page.keyboard.press('Enter');
    assert.equal(await exit.evaluate(el => el === document.activeElement), true);
    await title.focus();
    await page.keyboard.press('Space');
    assert.equal(await checkbox.isChecked(), true);
    await page.keyboard.press('Escape');
    await enter.waitFor();
    assert.equal(await checkbox.isChecked(), false);
    cases.push({name: 'keyboard selection'});

    await title.hover();
    const pos = await title.boundingBox();
    await page.mouse.down();
    await page.mouse.move(pos.x + 3, pos.y + 25);
    await page.waitForTimeout(800);
    await page.mouse.up();
    assert.equal(await exit.isVisible(), false);
    assert.equal(await detail.count(), 0);
    const menu = first.locator('.ph-article-row-actions button[data-bs-toggle="dropdown"]');
    await menu.click();
    await first.locator('.dropdown-menu.show').waitFor();
    assert.equal(await detail.count(), 0);
    await menu.click();
    cases.push({name: 'drag cancels and menu stays independent'});
    await page.screenshot({path: `${output}/${engine}-414-kontrol-kiri.png`});

    if (engine === 'chrome') {
        const cdp = await context.newCDPSession(page);
        await cdp.send('Emulation.setTouchEmulationEnabled', {enabled: true, maxTouchPoints: 2});
        async function titleBox() {
            await title.scrollIntoViewIfNeeded();
            return title.boundingBox();
        }
        let box = await titleBox();
        const touch = (type, offset = 0) => cdp.send('Input.dispatchTouchEvent', {
            type,
            touchPoints: type === 'touchEnd' ? [] : [{x: Math.round(box.x + box.width / 2), y: Math.round(box.y + box.height / 2 + offset)}],
        });
        await touch('touchStart');
        await exit.waitFor({timeout: 3000});
        await touch('touchEnd');
        assert.equal(await checkbox.isChecked(), true);
        assert.equal(await detail.count(), 0);
        await exit.click();
        await enter.waitFor();
        box = await titleBox();
        await touch('touchStart');
        await touch('touchMove', -44);
        await page.waitForTimeout(800);
        await touch('touchEnd');
        assert.equal(await exit.isVisible(), false);
        assert.equal(await detail.count(), 0);
        await cdp.send('Emulation.setTouchEmulationEnabled', {enabled: false});
        cases.push({name: 'Chrome touch hold, release and scroll cancellation'});
    }

    async function testCenteredLoading(name, action) {
        let release;
        const gate = new Promise(resolve => { release = resolve; });
        const pattern = '**/manage_article/listdata?*';
        const hold = async route => {
            await gate;
            await route.continue();
        };
        await page.route(pattern, hold, {times: 1});
        const response = page.waitForResponse(result => new URL(result.url()).pathname === '/manage_article/listdata');
        try {
            await action();
            const loader = app.locator('.ph-article-loading');
            await loader.waitFor({timeout: 5000});
            const offsets = await loader.evaluate(el => {
                const box = el.getBoundingClientRect();
                const content = el.firstElementChild.getBoundingClientRect();
                const header = el.closest('table').tHead.getBoundingClientRect();
                return {x: content.x + content.width / 2 - box.x - box.width / 2, y: content.y + content.height / 2 - box.y - box.height / 2, belowHead: !header.height || box.top >= header.bottom - 1};
            });
            assert.ok(Math.abs(offsets.x) < 1 && Math.abs(offsets.y) < 1 && offsets.belowHead, JSON.stringify(offsets));
            assert.equal(await rows.count(), 0);
            cases.push({name, ...offsets});
        } finally {
            release();
        }
        assert.equal((await response).status(), 200);
        await app.locator('.ph-article-loading').waitFor({state: 'hidden'});
    }
    await testCenteredLoading('mobile next', () => app.locator('.pagination li:last-child a').click());
    await testCenteredLoading('mobile previous', () => app.locator('.pagination li:first-child a').click());
    await testCenteredLoading('mobile search', () => app.locator('input[type="search"]').fill('Load Test'));
    await testCenteredLoading('mobile toolbar sort', () => app.getByLabel('Urutkan artikel').selectOption('title_asc'));
    await page.setViewportSize({width: 1440, height: 950});
    await testCenteredLoading('desktop header sort', () => app.locator('th[data-col-idx="1"] button').click());

    assert.deepEqual(errors, []);
    assert.deepEqual(writes, []);
    await writeFile(`${output}/verification-${engine}.json`, JSON.stringify({cases, errors, writes}, null, 2));
    console.log(`${engine}: ${cases.length} passed, 0 Vue errors and 0 article writes`);
} finally {
    await browser.close();
}
