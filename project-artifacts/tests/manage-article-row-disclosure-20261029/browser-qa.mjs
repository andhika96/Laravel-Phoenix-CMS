import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {chromium,firefox} from 'playwright-core';

const engine=process.argv.includes('--firefox')?'firefox':'chrome';
const out=path.resolve('project-artifacts/qa/manage-article-row-disclosure-20261029');
const browser=await (engine==='firefox'?firefox:chromium).launch(engine==='chrome'?{channel:'chrome',headless:true}:{headless:true});
const cases=[],errors=[],warnings=[],writes=[];
function observe(page) {
  page.on('pageerror',e=>errors.push(e.message));
  page.on('console',m=>{if(m.text().includes('[Vue warn]'))warnings.push(m.text());});
  page.on('request',r=>{if(r.method()!=='GET'&&!r.url().includes('/auth/'))writes.push({method:r.method(),url:r.url()});});
}
try {
  const context=await browser.newContext({ignoreHTTPSErrors:true,viewport:{width:1440,height:1200}});
  const page=await context.newPage();observe(page);
  await page.goto('https://laravel-13-phoenix.aruna/manage_article');
  if(page.url().includes('/auth/login')) {
    await page.getByRole('textbox',{name:'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox',{name:'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button',{name:'Log In',exact:true}).click();
  }
  const app=page.locator('#ph-app-manage-article'),row=app.locator('tr[data-article-id]').first();
  await row.waitFor();
  const id=await row.getAttribute('data-article-id'),expand=row.locator('.ph-article-expand'),title=row.locator('.ph-article-title');
  async function state(expected) {
    await page.waitForFunction(({id,expected})=>document.querySelector(`tr[data-article-id="${id}"] .ph-article-expand`)?.getAttribute('aria-expanded')===String(expected),{id,expected});
    assert.equal(await title.getAttribute('aria-expanded'),String(expected));
    assert.equal(await app.locator('#ph-article-detail-'+id).count(),expected?1:0);
  }
  async function roundTrip(name,action,width) {
    await action();await state(true);await action();await state(false);cases.push({width,name});
  }
  for(const width of [1440,768,390,320]) {
    await page.setViewportSize({width,height:1200});
    await app.evaluate(()=>new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r))));
    const geometry=await row.evaluate(row=>{
      const r=row.getBoundingClientRect(),b=row.querySelector('.ph-article-expand').getBoundingClientRect(),c=row.querySelector('input.checkids').getBoundingClientRect();
      return {inset:b.left-r.left,visibleGap:c.left-b.right,mobile:getComputedStyle(row).display==='grid',centerDelta:Math.abs(b.top+b.height/2-(r.top+r.height/2)),overflow:document.documentElement.scrollWidth>innerWidth+1};
    });
    assert.ok(Math.abs(geometry.inset-20)<.6);assert.ok(Math.abs(geometry.visibleGap-24)<.6);assert.equal(geometry.overflow,false);
    if(!geometry.mobile)assert.ok(geometry.centerDelta<.6);
    assert.equal(await title.evaluate(el=>el.tagName),'BUTTON');
    await roundTrip('row-padding',()=>row.locator('.ph-article-title-col').click({position:{x:2,y:2}}),width);
    await roundTrip('title',()=>title.click(),width);
    await roundTrip('category',()=>row.locator('.ph-article-meta').first().click(),width);
    for(const name of ['author','status','date']) {
      const cell=row.locator('.ph-article-'+name+'-col');
      if(await cell.isVisible())await roundTrip(name,()=>cell.click(),width);
    }
    await roundTrip('chevron-single-toggle',()=>expand.click(),width);
    await title.focus();await page.keyboard.press('Space');await state(true);
    await page.keyboard.press('Enter');await state(false);cases.push({width,name:'title-keyboard'});
    const input=row.locator('input.checkids');
    await input.check();await state(false);assert.equal(await input.isChecked(),true);
    await title.click();await state(true);await input.uncheck();await state(true);
    await app.locator('#ph-article-detail-'+id+' .ph-article-detail-heading').click();await state(true);
    await title.click();await state(false);cases.push({width,name:'checkbox-and-detail-do-not-toggle'});
    await row.locator('.ph-article-title-col').evaluate(el=>{
      const selection=window.getSelection(),range=document.createRange();range.selectNodeContents(el.querySelector('.ph-article-meta'));selection.removeAllRanges();selection.addRange(range);
    });
    await row.locator('.ph-article-title-col').dispatchEvent('click',{button:0,detail:1});await state(false);
    await page.evaluate(()=>window.getSelection().removeAllRanges());cases.push({width,name:'copy-text-selection'});
    const menuButton=row.locator('[data-bs-toggle="dropdown"]');
    await menuButton.click();await state(false);await row.locator('.dropdown-menu.show').waitFor();
    await title.click({position:{x:2,y:2}});await state(true);await row.locator('.dropdown-menu.show').waitFor({state:'hidden'});
    await title.click();await state(false);cases.push({width,name:'menu-excluded-and-title-closes-menu'});
    await menuButton.click();await row.getByRole('button',{name:'Delete',exact:true}).click();
    await page.locator('#ph-submit-data-article-modalDelete.show').waitFor();await state(false);
    await page.locator('#ph-submit-data-article-modalDelete').getByRole('button',{name:'No, keep it',exact:true}).click();
    await page.locator('#ph-submit-data-article-modalDelete.show').waitFor({state:'hidden'});await state(false);
    cases.push({width,name:'delete-modal-cancel-no-row-toggle'});
    await title.click();await state(true);
    if(width!==320)await page.screenshot({path:path.join(out,`row-click-${engine}-${width}.png`)});
    await title.click();await state(false);
  }
  await page.setViewportSize({width:1440,height:1200});
  await page.evaluate(()=>document.documentElement.setAttribute('data-bs-theme','dark'));
  await roundTrip('dark-mode-row',()=>row.locator('.ph-article-meta').first().click(),1440);
  await page.evaluate(()=>document.documentElement.setAttribute('data-bs-theme','light'));
  for(const width of [390,768]) {
    const touch=await browser.newContext({ignoreHTTPSErrors:true,hasTouch:true,viewport:{width,height:1200},storageState:await context.storageState()});
    const p=await touch.newPage();observe(p);await p.goto('https://laravel-13-phoenix.aruna/manage_article');
    const r=p.locator('tr[data-article-id]').first();await r.waitFor();
    await r.locator('.ph-article-meta').first().tap();assert.equal(await r.locator('.ph-article-expand').getAttribute('aria-expanded'),'true');
    await r.locator('input.checkids').tap();assert.equal(await r.locator('.ph-article-expand').getAttribute('aria-expanded'),'true');assert.equal(await r.locator('input.checkids').isChecked(),true);
    await r.locator('.ph-article-meta').first().tap();assert.equal(await r.locator('.ph-article-expand').getAttribute('aria-expanded'),'false');
    cases.push({width,name:'touch-row-and-checkbox'});await touch.close();
  }
  await page.setViewportSize({width:1440,height:1200});
  const edit=row.locator('.ph-article-direct-edit');assert.match(await edit.getAttribute('href'),new RegExp('/manage_article/edit/'+id+'$'));
  await Promise.all([page.waitForURL('**/manage_article/edit/'+id),edit.click()]);
  await page.locator('#ph-app-manage-article input[name="title"]').waitFor();cases.push({width:1440,name:'edit-navigation-retained'});
  assert.deepEqual(errors,[]);assert.deepEqual(warnings,[]);assert.deepEqual(writes,[]);
  await fs.writeFile(path.join(out,`verification-${engine}.json`),JSON.stringify({ok:true,engine,cases,errors,warnings,writes},null,2));
  console.log(JSON.stringify({ok:true,engine,cases:cases.length,errors,warnings,writes}));
} finally {await browser.close();}
