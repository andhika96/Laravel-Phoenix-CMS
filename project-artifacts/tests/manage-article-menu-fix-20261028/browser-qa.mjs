import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {chromium} from 'playwright-core';

const out=path.resolve('project-artifacts/qa/manage-article-menu-fix-20261028');
const inspect=process.argv.includes('--inspect'),errors=[],warnings=[],writes=[],cases=[];
const browser=await chromium.launch({channel:'chrome',headless:true});
try {
  const page=await browser.newPage({ignoreHTTPSErrors:true,viewport:{width:1440,height:1100}});
  page.on('pageerror',e=>errors.push(e.message));
  page.on('console',m=>{if(m.text().includes('[Vue warn]'))warnings.push(m.text());});
  page.on('request',r=>{if(r.method()!=='GET'&&!r.url().includes('/auth/')) writes.push({method:r.method(),url:r.url()});});
  await page.goto('https://laravel-13-phoenix.aruna/manage_article');
  if(page.url().includes('/auth/login')) {
    await page.getByRole('textbox',{name:'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox',{name:'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button',{name:'Log In',exact:true}).click();
  }
  const app=page.locator('#ph-app-manage-article'),header=app.locator('.ph-article-header-actions');
  await app.locator('tr[data-article-id]').first().waitFor();
  async function menuMetrics() {
    return app.locator('.ph-article-row-actions').first().evaluate(actions=>{
      const menu=actions.querySelector('.dropdown-menu'),body=getComputedStyle(document.body),table=getComputedStyle(actions.closest('td')),r=menu.getBoundingClientRect();
      const ancestors=[];for(let el=menu;el&&el.tagName!=='TABLE';el=el.parentElement)ancestors.push({tag:el.tagName,class:el.className,fontSize:getComputedStyle(el).fontSize});
      return {ancestors,rootSize:getComputedStyle(document.documentElement).fontSize,bodySize:body.fontSize,cellSize:table.fontSize,menuSize:getComputedStyle(menu).fontSize,menuFamily:getComputedStyle(menu).fontFamily,bodyFamily:body.fontFamily,items:[...menu.querySelectorAll('.dropdown-item')].filter(el=>el.checkVisibility()).map(el=>({label:el.textContent.trim(),size:getComputedStyle(el).fontSize,family:getComputedStyle(el).fontFamily,lineHeight:getComputedStyle(el).lineHeight})),left:r.left,right:r.right,bottom:r.bottom,viewport:innerWidth};
    });
  }
  if(inspect) {
    await app.locator('.ph-article-row-actions [data-bs-toggle="dropdown"]').first().click();
    const metrics=await menuMetrics();
    const fontRules=await app.locator('.ph-article-row-actions .dropdown-menu').first().evaluate(menu=>{
      const found=[];
      function read(rules,sheet){for(const rule of rules){if(rule.selectorText&&rule.style?.fontSize){try{if(menu.matches(rule.selectorText))found.push({sheet,selector:rule.selectorText,fontSize:rule.style.fontSize,important:rule.style.getPropertyPriority('font-size')});}catch{}}if(rule.cssRules&&(!rule.media||matchMedia(rule.conditionText).matches))read(rule.cssRules,sheet);}}
      for(const sheet of document.styleSheets){try{read(sheet.cssRules,sheet.href||'inline');}catch{}}
      return found;
    });
    await fs.writeFile(path.join(out,'inspection-font.json'),JSON.stringify({metrics,fontRules},null,2));
    console.log(JSON.stringify({metrics,fontRules}));
  } else {
    assert.equal(await header.locator(':scope > a').count(),2,'Add Post and Article Templates must be direct header actions.');
    assert.equal(await header.locator(':scope > button').count(),1,'Category List must be a direct header action.');
    assert.equal(await header.locator('[data-bs-toggle="dropdown"]').count(),0);
    for(const theme of ['light','dark']) for(const width of [1440,1024,768,390,320]) {
      await page.setViewportSize({width,height:1200});
      await page.evaluate(theme=>document.documentElement.setAttribute('data-bs-theme',theme),theme);
      await page.evaluate(async()=>{await Promise.allSettled(document.getAnimations().filter(a=>Number.isFinite(a.effect?.getComputedTiming().endTime)).map(a=>a.finished));});
      const geometry=await header.evaluate(header=>{
        const actions=[...header.children].map(el=>{const r=el.getBoundingClientRect();return {label:el.textContent.trim(),x:r.left,y:r.top,right:r.right,bottom:r.bottom,width:r.width,height:r.height,fontSize:getComputedStyle(el).fontSize,visible:el.checkVisibility(),clipped:el.scrollWidth>el.clientWidth+1};});
        const overlaps=actions.some((a,i)=>actions.slice(i+1).some(b=>a.x<b.right&&a.right>b.x&&a.y<b.bottom&&a.bottom>b.y));
        return {actions,overlaps,overflow:document.documentElement.scrollWidth>innerWidth+1,width:innerWidth};
      });
      assert.equal(geometry.actions.length,3);assert.equal(geometry.overflow,false);assert.equal(geometry.overlaps,false);
      for(const action of geometry.actions){assert.equal(action.visible,true);assert.equal(action.clipped,false,JSON.stringify({width,action}));assert.ok(action.x>=0&&action.right<=width);}
      if(width===1440)assert.ok(geometry.actions.every(a=>Math.abs(a.y-geometry.actions[0].y)<1));
      await app.locator('.ph-article-row-actions [data-bs-toggle="dropdown"]').first().click();
      await app.locator('.ph-article-row-actions .dropdown-menu.show').waitFor();
      await app.evaluate(()=>new Promise(resolve=>requestAnimationFrame(()=>requestAnimationFrame(resolve))));
      const menu=await menuMetrics();
      assert.equal(menu.menuSize,menu.cellSize,JSON.stringify(menu));
      for(const item of menu.items){assert.equal(item.size,menu.cellSize,JSON.stringify(menu));assert.equal(item.family,menu.bodyFamily);}
      assert.ok(menu.left>=0&&menu.right<=width,JSON.stringify(menu));
      cases.push({theme,width,geometry,menu});
      if(width===1440||width===390) await page.screenshot({path:path.join(out,`menu-${width}-${theme}.png`)});
      await page.keyboard.press('Escape');
      if(width===1440||width===390) await page.screenshot({path:path.join(out,`page-${width}-${theme}.png`)});
    }
    await page.setViewportSize({width:390,height:1000});
    await header.getByRole('button',{name:'Category List',exact:true}).click();
    const modal=page.locator('#ph-submit-data-category-modalRead');
    await page.locator('#ph-submit-data-category-modalRead.show').waitFor();
    await modal.locator('[data-bs-dismiss="modal"]').first().click();
    await page.locator('#ph-submit-data-category-modalRead.show').waitFor({state:'hidden'});
    assert.match(await header.getByRole('link',{name:'Article Templates',exact:true}).getAttribute('href'),/\/manage_article\/templates/);
    const row=app.locator('tr[data-article-id]').first();
    await row.locator('.ph-article-expand').click();await row.locator('input.checkids').check();
    assert.equal(await row.locator('.ph-article-expand').getAttribute('aria-expanded'),'true');
    await app.locator('.ph-article-bulk').getByRole('button',{name:'Cancel',exact:true}).click();
    assert.deepEqual(errors,[]);assert.deepEqual(warnings,[]);assert.deepEqual(writes,[]);
    await fs.writeFile(path.join(out,'verification.json'),JSON.stringify({ok:true,cases,errors,warnings,writes},null,2));
    console.log(JSON.stringify({ok:true,cases:cases.length,errors,warnings,writes}));
  }
} finally {await browser.close();}
