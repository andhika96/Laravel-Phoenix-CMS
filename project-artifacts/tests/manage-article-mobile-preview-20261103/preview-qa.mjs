import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {pathToFileURL} from 'node:url';
import {chromium,firefox} from 'playwright-core';

const out=path.resolve('project-artifacts/qa/manage-article-mobile-preview-20261103');
const engine=process.argv.includes('--firefox')?'firefox':'chrome';
const browser=await (engine==='firefox'?firefox:chromium).launch(engine==='chrome'?{channel:'chrome',headless:true}:{headless:true});
const errors=[],appRequests=[],cases=[];
try {
  const page=await browser.newPage({viewport:{width:900,height:1300},ignoreHTTPSErrors:true});
  page.on('pageerror',e=>errors.push(e.message));
  page.on('request',r=>{if(r.url().includes('laravel-13-phoenix.aruna'))appRequests.push(r.url());});
  await page.goto(pathToFileURL(path.join(out,'rendered.html')).href);
  const f=page.frameLocator('iframe');
  await f.locator('#phoenix-mobile-article-preview .mp-record').first().waitFor();
  const frame=page.frames().find(fr=>fr.parentFrame());
  for(const variant of ['Judul utama','Kontrol di kiri']) for(const width of [320,390,414,720]) for(const theme of ['light','dark']) {
    await frame.evaluate(({variant,width,theme})=>{
      for(const s of document.querySelectorAll('[data-variant]'))s.hidden=s.dataset.variant!==variant;
      document.documentElement.style.colorScheme=theme;
      window.dispatchEvent(new CustomEvent('openai:set_globals',{detail:{globals:{widgetState:{modelContent:{width:String(width)}}}}}));
    },{variant,width,theme});
    const stage=f.locator(`[data-variant="${variant}"] .mp-app`);
    await stage.evaluate(()=>new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r))));
    const metrics=await stage.evaluate(stage=>{
      const b=stage.getBoundingClientRect();
      const actions=[...stage.querySelectorAll('.mp-header-actions > *')].map(el=>{
        const r=el.getBoundingClientRect(),s=el.querySelector('span').getBoundingClientRect(),i=el.querySelector('svg')?.getBoundingClientRect();
        return {label:el.textContent.trim(),height:r.height,labelDelta:Math.abs(s.top+s.height/2-(r.top+r.height/2)),iconDelta:i?Math.abs(i.top+i.height/2-(r.top+r.height/2)):null,left:r.left,right:r.right,clipped:el.scrollWidth>el.clientWidth+1};
      });
      const row=stage.querySelector('[data-record]'),d=row.querySelector('.mp-detail-trigger').getBoundingClientRect(),c=row.querySelector('[data-check]').getBoundingClientRect();
      const overflow=[...stage.querySelectorAll('*')].filter(el=>el.checkVisibility()&&el.getBoundingClientRect().width>0).filter(el=>{const r=el.getBoundingClientRect();return r.left<b.left-1||r.right>b.right+1;}).map(el=>el.className?.baseVal||el.className||el.tagName).slice(0,8);
      return {width:b.width,actions,overflow,visibleGap:c.left-d.right,icons:stage.querySelectorAll('svg').length,titleWeight:getComputedStyle(row.querySelector('.mp-title')).fontWeight};
    });
    assert.ok(Math.abs(metrics.width-width)<1,JSON.stringify({variant,width,theme,metrics}));
    assert.deepEqual(metrics.overflow,[],JSON.stringify({variant,width,theme,metrics}));
    assert.ok(metrics.icons>10,'Host icons must render.');
    assert.equal(metrics.titleWeight,'600');
    assert.ok(Math.abs(metrics.visibleGap-24)<.6);
    for(const action of metrics.actions){assert.ok(action.height>=44);assert.ok(action.labelDelta<.6,JSON.stringify(action));assert.ok(action.iconDelta<.6,JSON.stringify(action));assert.equal(action.clipped,false);}
    if([320,414].includes(width))await stage.screenshot({path:path.join(out,`${engine==='firefox'?'firefox-':''}${variant==='Judul utama'?'reading':'leading'}-${width}-${theme}.png`)});
    cases.push({variant,width,theme,...metrics});
  }
  const interactions=[];
  for(const variant of ['Judul utama','Kontrol di kiri']) {
    await frame.evaluate(variant=>{for(const s of document.querySelectorAll('[data-variant]'))s.hidden=s.dataset.variant!==variant;document.documentElement.style.colorScheme='light';window.dispatchEvent(new CustomEvent('openai:set_globals',{detail:{globals:{widgetState:{modelContent:{width:'414'}}}}}));},variant);
    const stage=f.locator(`[data-variant="${variant}"] .mp-app`),row=stage.locator('[data-record]').first(),title=row.locator('.mp-title');
    await title.click();assert.equal(await title.getAttribute('aria-expanded'),'true');assert.equal(await row.locator('.mp-details').isVisible(),true);
    await row.locator('.mp-byline').click();assert.equal(await title.getAttribute('aria-expanded'),'false');
    await row.locator('[data-check]').check();assert.equal(await title.getAttribute('aria-expanded'),'false');assert.equal(await stage.locator('.mp-bulk').isVisible(),true);
    await stage.getByRole('button',{name:'Batal',exact:true}).click();assert.equal(await row.locator('[data-check]').isChecked(),false);
    await title.focus();await page.keyboard.press('Enter');assert.equal(await title.getAttribute('aria-expanded'),'true');await page.keyboard.press('Space');assert.equal(await title.getAttribute('aria-expanded'),'false');
    await stage.locator('[data-search]').fill('Science');await stage.locator('.mp-loading').waitFor({state:'visible'});
    const center=await stage.locator('.mp-loading').evaluate(el=>{const r=el.getBoundingClientRect(),children=[...el.children].map(x=>x.getBoundingClientRect());return {x:Math.max(...children.map(c=>Math.abs(c.left+c.width/2-(r.left+r.width/2)))),belowToolbar:r.top>=el.closest('.mp-board').querySelector('.mp-toolbar').getBoundingClientRect().bottom};});
    assert.ok(center.x<1);assert.equal(center.belowToolbar,true);
    await stage.locator('.mp-loading').waitFor({state:'hidden'});assert.equal(await stage.locator('[data-record]').count(),1);
    await stage.locator('[data-search]').fill('');await stage.locator('.mp-loading').waitFor({state:'visible'});await stage.locator('.mp-loading').waitFor({state:'hidden'});
    await stage.locator('[data-status-toggle]').click();await stage.locator('[data-status="pending"][type="button"]').click();await stage.locator('.mp-loading').waitFor({state:'hidden'});assert.equal(await stage.locator('[data-record]').count(),1);
    await stage.locator('[data-status-toggle]').click();await stage.locator('[data-status="draft"][type="button"]').click();await stage.locator('.mp-loading').waitFor({state:'hidden'});assert.equal(await stage.locator('.mp-empty').isVisible(),true);
    await stage.getByRole('button',{name:'Hapus filter',exact:true}).click();await stage.locator('.mp-loading').waitFor({state:'hidden'});assert.equal(await stage.locator('[data-record]').count(),4);
    await stage.locator('[data-filter-toggle]').click();await stage.locator('[data-category]').selectOption('Load Test Science');await stage.locator('.mp-loading').waitFor({state:'hidden'});assert.equal(await stage.locator('[data-record]').count(),1);
    await stage.locator('[data-category]').selectOption('');await stage.locator('.mp-loading').waitFor({state:'hidden'});
    await stage.locator('[data-sort]').selectOption('oldest');await stage.locator('.mp-loading').waitFor({state:'hidden'});assert.equal(await stage.locator('[data-record]').first().getAttribute('data-record'),'5011');
    await stage.locator('[data-sort]').selectOption('newest');await stage.locator('.mp-loading').waitFor({state:'hidden'});
    const first=stage.locator('[data-record]').first();await first.locator('[data-options]').click();assert.equal(await first.locator('[data-row-menu]').isVisible(),true);await page.keyboard.press('Escape');assert.equal(await first.locator('[data-row-menu]').isVisible(),false);
    await stage.getByRole('link',{name:'Add Post',exact:true}).click();assert.match(await stage.locator('.mp-notice').innerText(),/Add Post/);
    interactions.push({variant,detail:true,checkboxIndependent:true,keyboard:true,search:true,status:true,empty:true,category:true,sort:true,menu:true,headerAction:true,loadingCentered:true});
  }
  assert.deepEqual(errors,[]);assert.deepEqual(appRequests,[]);
  await fs.writeFile(path.join(out,engine==='firefox'?'verification-firefox.json':'verification.json'),JSON.stringify({ok:true,engine,cases,interactions,errors,appRequests},null,2));
  console.log(JSON.stringify({ok:true,engine,layoutCases:cases.length,interactiveVariants:interactions.length,errors,appRequests}));
} finally {await browser.close();}
