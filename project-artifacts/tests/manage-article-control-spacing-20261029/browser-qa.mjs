import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {chromium,firefox} from 'playwright-core';

const engine=process.argv.includes('--firefox')?'firefox':'chrome';
const before=process.argv.includes('--before');
const out=path.resolve('project-artifacts/qa/manage-article-control-spacing-20261029');
const browser=await (engine==='firefox'?firefox:chromium).launch(engine==='chrome'?{channel:'chrome',headless:true}:{headless:true});
const errors=[],warnings=[],writes=[],cases=[];
try {
  const context=await browser.newContext({ignoreHTTPSErrors:true,viewport:{width:1440,height:1050}});
  const page=await context.newPage();
  page.on('pageerror',e=>errors.push(e.message));
  page.on('console',m=>{if(m.text().includes('[Vue warn]'))warnings.push(m.text());});
  page.on('request',r=>{if(r.method()!=='GET'&&!r.url().includes('/auth/'))writes.push({method:r.method(),url:r.url()});});
  await page.goto('https://laravel-13-phoenix.aruna/manage_article');
  if(page.url().includes('/auth/login')) {
    await page.getByRole('textbox',{name:'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox',{name:'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button',{name:'Log In',exact:true}).click();
  }
  const app=page.locator('#ph-app-manage-article');
  await app.locator('tr[data-article-id]').first().waitFor();
  async function metrics(page) {
    return page.locator('#ph-app-manage-article').evaluate(app=>{
      const row=app.querySelector('tr[data-article-id]'),b=row.querySelector('.ph-article-expand'),c=row.querySelector('.ph-article-check-target'),title=row.querySelector('.ph-article-title'),header=app.querySelector('thead .ph-article-check-target');
      const box=el=>{const r=el.getBoundingClientRect();return {left:r.left,right:r.right,top:r.top,bottom:r.bottom,width:r.width,height:r.height};};
      const br=box(b),cr=box(c),tr=box(title),rr=box(row),mobile=getComputedStyle(row).display==='grid',detail=app.querySelector('.ph-article-detail');
      return {width:innerWidth,mobile,button:br,check:cr,title:tr,row:rr,gap:cr.left-br.right,inset:br.left-rr.left,centerDelta:br.top+br.height/2-(tr.top+22/2),headerCheck:header.checkVisibility()?box(header):null,expandCell:{...box(b.parentElement),padding:getComputedStyle(b.parentElement).padding},checkCell:{...box(c.parentElement),padding:getComputedStyle(c.parentElement).padding},detail:detail?box(detail):null,overflow:document.documentElement.scrollWidth>innerWidth+1};
    });
  }
  function verify(m,touch=false) {
    assert.ok(Math.abs(m.inset-8)<.6,`Expected 8px left inset: ${JSON.stringify(m)}`);
    assert.ok(Math.abs(m.gap-24)<.6,`Expected 24px target gap: ${JSON.stringify(m)}`);
    assert.equal(m.overflow,false);
    assert.ok(Math.abs(m.expandCell.width-m.checkCell.width)<.6,'Control columns must have equal widths.');
    if(m.mobile)assert.ok(Math.abs(m.title.left-m.button.left)<.6,'Mobile title must align with the first control.');
    else {
      assert.ok(Math.abs(m.centerDelta)<.6,'Controls must align with the first title line.');
      assert.ok(Math.abs(m.headerCheck.left-m.check.left)<.6,'Select-all must align with row checkboxes.');
    }
    if(touch||m.mobile){assert.ok(m.button.width>=44&&m.button.height>=44);assert.ok(m.check.width>=44&&m.check.height>=44);}
    if(m.detail)assert.ok(Math.abs(m.detail.left-m.title.left)<2,`Expanded detail must align with title: ${JSON.stringify(m)}`);
  }
  for(const theme of (before?['light']:['light','dark'])) for(const width of (before?[1440]:[1440,1024,768,601,600,390,320])) {
    await page.setViewportSize({width,height:1050});
    await page.evaluate(theme=>document.documentElement.setAttribute('data-bs-theme',theme),theme);
    await page.evaluate(async()=>{await Promise.allSettled(document.getAnimations().filter(a=>Number.isFinite(a.effect?.getComputedTiming().endTime)).map(a=>a.finished));await new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r)));});
    const m=await metrics(page);verify(m);
    const row=app.locator('tr[data-article-id]').first();
    await row.locator('.ph-article-expand').click();
    assert.equal(await row.locator('.ph-article-expand').getAttribute('aria-expanded'),'true');
    verify(await metrics(page));
    if([1440,768,390].includes(width))await page.screenshot({path:path.join(out,`after-${engine}-${width}-${theme}.png`)});
    await row.locator('.ph-article-expand').click();
    await row.locator('input.checkids').check();assert.equal(await row.locator('input.checkids').isChecked(),true);
    await row.locator('input.checkids').uncheck();
    cases.push({theme,...m});
  }
  if(!before) for(const width of [320,768,1024]) {
    const touch=await browser.newContext({ignoreHTTPSErrors:true,hasTouch:true,viewport:{width,height:1050},storageState:await context.storageState()});
    const touchPage=await touch.newPage();
    touchPage.on('pageerror',e=>errors.push(e.message));
    touchPage.on('request',r=>{if(r.method()!=='GET'&&!r.url().includes('/auth/'))writes.push({method:r.method(),url:r.url()});});
    await touchPage.goto('https://laravel-13-phoenix.aruna/manage_article');
    await touchPage.locator('tr[data-article-id]').first().waitFor();
    const m=await metrics(touchPage);verify(m,true);cases.push({touch:true,...m});
    await touch.close();
  }
  assert.deepEqual(errors,[]);assert.deepEqual(warnings,[]);assert.deepEqual(writes,[]);
  const result={ok:true,engine,cases,errors,warnings,writes};
  await fs.writeFile(path.join(out,`verification-${engine}.json`),JSON.stringify(result,null,2));
  console.log(JSON.stringify({ok:true,engine,cases:cases.length,errors,warnings,writes}));
} finally {await browser.close();}
