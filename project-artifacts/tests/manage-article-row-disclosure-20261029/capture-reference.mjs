import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {firefox} from 'playwright-core';

const out=path.resolve('project-artifacts/qa/manage-article-row-disclosure-20261029');
const browser=await firefox.launch({headless:true});
try {
  const page=await browser.newPage({ignoreHTTPSErrors:true,viewport:{width:1920,height:1080}});
  await page.goto('https://laravel-13-phoenix.aruna/manage_article');
  if(page.url().includes('/auth/login')) {
    await page.getByRole('textbox',{name:'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox',{name:'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button',{name:'Log In',exact:true}).click();
  }
  const row=page.locator('#ph-app-manage-article tr[data-article-id]').first();
  await row.waitFor();await row.locator('.ph-article-title').click();assert.equal(await row.locator('.ph-article-expand').getAttribute('aria-expanded'),'true');
  const metrics=await row.evaluate(row=>{
    const r=row.getBoundingClientRect(),b=row.querySelector('.ph-article-expand').getBoundingClientRect(),c=row.querySelector('input.checkids').getBoundingClientRect(),t=row.querySelector('.ph-article-title').getBoundingClientRect(),table=row.closest('table').getBoundingClientRect();
    return {inset:b.left-r.left,visibleGap:c.left-b.right,buttonCenter:b.top+b.height/2,checkboxCenter:c.top+c.height/2,rowCenter:r.top+r.height/2,titleHeight:t.height,clip:{x:table.left-1,y:table.top-15,width:650,height:r.bottom-table.top+Math.min(row.nextElementSibling.getBoundingClientRect().height,180)+16}};
  });
  assert.ok(Math.abs(metrics.inset-20)<.6);assert.ok(Math.abs(metrics.visibleGap-24)<.6);
  assert.ok(Math.abs(metrics.buttonCenter-metrics.rowCenter)<.6);assert.ok(Math.abs(metrics.checkboxCenter-metrics.rowCenter)<.6);
  assert.equal(metrics.titleHeight,22);
  await page.screenshot({path:path.join(out,'reference-alignment-firefox.png'),clip:metrics.clip});
  await fs.writeFile(path.join(out,'reference-capture.json'),JSON.stringify(metrics,null,2));
  console.log(JSON.stringify({ok:true,...metrics}));
} finally {await browser.close();}
