import { chromium } from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';

const out = path.dirname(new URL(import.meta.url).pathname.replace(/^\/([A-Za-z]:)/, '$1'));
const browser = await chromium.launch({channel: 'chrome', headless: true});
try {
  const page = await browser.newPage({ignoreHTTPSErrors: true, viewport: {width: 1440, height: 1024}});
  await page.goto('https://laravel-13-phoenix.aruna/manage_article');
  if (page.url().includes('/auth/login')) {
    await page.getByRole('textbox', {name: 'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox', {name: 'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button', {name: 'Log In', exact: true}).click();
  }
  await page.locator('tr[data-article-id]').first().waitFor();
  const result = [];
  for (const width of [1440, 768, 390]) {
    await page.setViewportSize({width, height: 1024});
    await page.screenshot({path: path.join(out, `current-${width}.png`)});
    result.push(await page.locator('#ph-app-manage-article').evaluate(app => {
      const row = app.querySelector('tr[data-article-id]');
      const box = selector => {const r = row.querySelector(selector).getBoundingClientRect(); return {x:r.x, y:r.y, width:r.width, height:r.height};};
      const style = getComputedStyle(app);
      return {width:innerWidth, font:style.fontFamily, fontSize:style.fontSize, accent:style.getPropertyValue('--ph-primary'), detail:box('.ph-article-expand'), check:box('.ph-article-check-target'), title:box('.ph-article-title'), statusLabels:[...app.querySelectorAll('.ph-article-tab')].map(n=>n.textContent.trim())};
    }));
  }
  await fs.writeFile(path.join(out, 'current-runtime.json'), JSON.stringify(result, null, 2));
  console.log(JSON.stringify(result));
} finally {await browser.close();}
