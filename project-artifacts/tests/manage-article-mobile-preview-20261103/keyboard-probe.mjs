import {chromium} from 'playwright-core';
import {pathToFileURL} from 'node:url';
import path from 'node:path';
const browser=await chromium.launch({channel:'chrome',headless:true});
try {
  const page=await browser.newPage({viewport:{width:900,height:1300}});
  await page.goto(pathToFileURL(path.resolve('project-artifacts/qa/manage-article-mobile-preview-20261103/rendered.html')).href);
  const frame=page.frameLocator('iframe'),title=frame.locator('[data-layout="reading"] .mp-title').first();
  await title.waitFor();await title.focus();
  const info=label=>title.evaluate((el,label)=>({label,focused:document.activeElement===el,active:document.activeElement.outerHTML.slice(0,220),expanded:el.getAttribute('aria-expanded')}),label);
  console.log(JSON.stringify(await info('focused')));
  await page.keyboard.press('Enter');console.log(JSON.stringify(await info('enter')));
  await page.keyboard.press('Space');console.log(JSON.stringify(await info('space')));
  await title.press('Space');console.log(JSON.stringify(await info('locator-space')));
} finally {await browser.close();}
