import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {chromium} from 'playwright-core';

const origin='https://laravel-13-phoenix.aruna';
const out=path.resolve('project-artifacts/qa/manage-article-reference-alignment-20261029/loading-regression');
const browser=await chromium.launch({channel:'chrome',headless:true});
const runtimeErrors=[],vueWarnings=[],writes=[],loadings=[],responsive=[];
try {
  const context=await browser.newContext({ignoreHTTPSErrors:true,viewport:{width:1440,height:1000}});
  const page=await context.newPage();
  page.on('pageerror',error=>runtimeErrors.push(error.message));
  page.on('console',message=>{if(message.text().includes('[Vue warn]')) vueWarnings.push(message.text());});
  page.on('request',request=>{if(request.method()!=='GET'&&!request.url().includes('/auth/')) writes.push({method:request.method(),url:request.url()});});
  await page.goto(origin+'/manage_article');
  if(page.url().includes('/auth/login')) {
    assert.ok(process.env.PH_ARTICLE_QA_EMAIL&&process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('textbox',{name:'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox',{name:'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button',{name:'Log In',exact:true}).click();
  }
  const app=page.locator('#ph-app-manage-article');
  await app.locator('tbody tr[data-article-id]').first().waitFor();
  assert.equal(await app.locator('.ph-article-status-trigger').count(),1,'Approved status dropdown must be present in the toolbar.');
  assert.equal(await app.locator('.ph-article-tabs').count(),0);
  const initial=await (await context.request.get(origin+'/manage_article/listdata')).json();
  let nextHold=null;
  await page.route('**/manage_article/listdata?*',async route=>{
    if(nextHold) {const hold=nextHold;nextHold=null;hold.arrive(route.request());await hold.gate;}
    await route.continue();
  });
  async function heldList(name,action,{screenshot=false}={}) {
    let release,arrive;
    const gate=new Promise(resolve=>release=resolve),started=new Promise(resolve=>arrive=resolve);
    nextHold={gate,arrive};
    let request;
    try {
      await action();request=await started;
      await app.locator('.ph-article-loading').waitFor({state:'visible'});
      const metrics=await app.locator('.ph-article-loading').evaluate(loader=>{
        const box=loader.getBoundingClientRect(),content=loader.querySelector('.ph-article-loading-content').getBoundingClientRect(),head=loader.closest('table').querySelector('thead').getBoundingClientRect();
        return {width:innerWidth,centerX:Math.abs(content.x+content.width/2-(box.x+box.width/2)),centerY:Math.abs(content.y+content.height/2-(box.y+box.height/2)),height:box.height,belowHeader:head.height===0||box.top>=head.bottom-1,visibleRows:[...document.querySelectorAll('tr[data-article-id]')].some(row=>row.checkVisibility()),overflow:document.documentElement.scrollWidth>innerWidth+1,reducedMotion:matchMedia('(prefers-reduced-motion: reduce)').matches,spinnerAnimation:getComputedStyle(loader.querySelector('.spinner-border')).animationName};
      });
      assert.ok(metrics.centerX<1&&metrics.centerY<1,JSON.stringify({name,...metrics}));
      assert.ok(metrics.height>=300);assert.equal(metrics.belowHeader,true);assert.equal(metrics.visibleRows,false);assert.equal(metrics.overflow,false);
      if(metrics.reducedMotion) assert.equal(metrics.spinnerAnimation,'none');
      const params=Object.fromEntries(new URL(request.url()).searchParams);
      loadings.push({name,params,...metrics});
      if(screenshot) await page.screenshot({path:path.join(out,`loading-${name.replace(/[^a-z0-9]/gi,'-')}-${metrics.width}.png`)});
    } finally {release();}
    const response=await page.waitForResponse(response=>response.request()===request);
    assert.equal(response.status(),200);
    await page.waitForFunction(()=>document.querySelector('.ph-article-board')?.getAttribute('aria-busy')==='false');
    assert.equal(await app.locator('.ph-article-loading').count(),0);
    return response.json();
  }
  await heldList('initial',()=>page.reload(),{screenshot:true});
  await heldList('next',()=>app.locator('.pagination li:last-child a').click(),{screenshot:true});
  assert.equal(loadings.at(-1).params.page,'2');
  await heldList('previous',()=>app.locator('.pagination li:first-child a').click());
  assert.equal(loadings.at(-1).params.page,'1');
  await heldList('search',()=>app.getByRole('searchbox').fill('Load Test'));
  assert.equal(loadings.at(-1).params.search,'Load Test');
  await heldList('clear-search',()=>app.getByRole('searchbox').fill(''));
  await heldList('title-header',()=>app.locator('th[data-col-idx="1"] button').click());
  assert.equal(loadings.at(-1).params.sort,'title_asc');
  await heldList('date-header',()=>app.locator('th[data-col-idx="5"] button').click());
  await heldList('sort-select',()=>app.getByRole('combobox',{name:'Urutkan artikel'}).selectOption('title_desc'));
  await app.locator('.ph-article-status-trigger').click();
  const dropdown=app.locator('.ph-article-status-menu');
  await dropdown.waitFor({state:'visible'});
  assert.equal(await dropdown.locator('.ph-article-status-option').count(),4);
  const pending=await heldList('status-pending',()=>dropdown.locator('[data-status-code="pending"]').click());
  assert.equal(pending.total,pending.status_counts.pending);
  assert.match(await app.locator('.ph-article-status-trigger').innerText(),/Pending/);
  await app.locator('.ph-article-status-trigger').click();
  await heldList('status-draft',()=>dropdown.locator('[data-status-code="draft"]').click());
  await app.getByRole('heading',{name:'Artikel tidak ditemukan'}).waitFor();
  await heldList('empty-reset',()=>app.getByRole('button',{name:'Hapus pencarian dan filter',exact:true}).click());
  await app.getByRole('button',{name:'Filter',exact:true}).click();
  const category=await app.locator('#ph-article-category option').evaluateAll(options=>options.find(o=>o.textContent.includes('Load Test Technology'))?.value||options.find(o=>o.value)?.value);
  await heldList('category',()=>app.locator('#ph-article-category').selectOption(category));
  await heldList('schedule',()=>app.locator('#ph-article-scheduled').selectOption('false'));
  await heldList('combined-search',()=>app.getByRole('searchbox').fill('Load Test'));
  await heldList('page-size',()=>app.locator('.ph-article-page-info select').selectOption('5'));
  assert.equal(loadings.at(-1).params.filter_by_category,category);
  assert.equal(loadings.at(-1).params.filter_by_scheduled,'false');assert.equal(loadings.at(-1).params.search,'Load Test');assert.equal(loadings.at(-1).params.per_page,'5');
  await heldList('filter-reset',()=>app.getByRole('button',{name:'Hapus filter',exact:true}).click());
  await app.getByRole('button',{name:'Filter',exact:true}).click();
  const row=app.locator('tbody tr[data-article-id]').first();
  const firstId=await row.getAttribute('data-article-id');
  const trigger=row.locator('.ph-article-expand');
  await trigger.focus();await page.keyboard.press('Enter');
  await row.locator('input.checkids').check();
  assert.equal(await trigger.getAttribute('aria-expanded'),'true');
  await trigger.focus();await page.keyboard.press('Space');
  assert.equal(await trigger.getAttribute('aria-expanded'),'false');assert.equal(await row.locator('input.checkids').isChecked(),true);
  await trigger.click();
  await app.locator('.ph-article-bulk select').selectOption('pending');
  await app.getByRole('button',{name:'Terapkan',exact:true}).click();
  await page.locator('#ph-article-bulk-confirm.show').waitFor();
  await page.locator('#ph-article-bulk-confirm').getByRole('button',{name:'Cancel',exact:true}).click();
  await page.locator('#ph-article-bulk-confirm.show').waitFor({state:'hidden'});
  await app.locator('.ph-article-bulk').getByRole('button',{name:'Cancel',exact:true}).click();
  for(const theme of ['light','dark']) {
    await page.evaluate(theme=>document.documentElement.setAttribute('data-bs-theme',theme),theme);
    for(const width of [1440,1024,768,390,320]) {
      await page.setViewportSize({width,height:1400});
      await app.evaluate(()=>new Promise(resolve=>requestAnimationFrame(()=>requestAnimationFrame(resolve))));
      const metrics=await row.evaluate(row=>{
        const d=row.querySelector('.ph-article-expand').getBoundingClientRect(),c=row.querySelector('.ph-article-check-target').getBoundingClientRect(),t=row.querySelector('.ph-article-title').getBoundingClientRect(),line=parseFloat(getComputedStyle(row.querySelector('.ph-article-title')).lineHeight);
        const board=row.closest('.ph-article-board'),b=board.getBoundingClientRect(),mobile=getComputedStyle(row).display==='grid';
        const bad=[...board.querySelectorAll('*')].filter(el=>el.checkVisibility()&&el.getBoundingClientRect().width>0).filter(el=>{const r=el.getBoundingClientRect();return r.right>b.right+1||r.left<b.left-1;}).map(el=>typeof el.className==='string'?el.className:el.tagName).slice(0,5);
        return {width:innerWidth,mobile,overflow:document.documentElement.scrollWidth>innerWidth+1,bad,gap:row.querySelector('input.checkids').getBoundingClientRect().left-d.right,sameRow:Math.abs(d.top-c.top)<1,alignment:Math.abs(d.top+d.height/2-(row.getBoundingClientRect().top+row.getBoundingClientRect().height/2)),titleBelow:t.top>=d.bottom,detailWidth:d.width,detailHeight:d.height,checkWidth:c.width,checkHeight:c.height,visibleColumns:[...row.cells].filter(c=>c.checkVisibility()).length,detailSpan:row.nextElementSibling?.querySelector('td')?.colSpan};
      });
      responsive.push({theme,...metrics});
      assert.equal(metrics.overflow,false,JSON.stringify(metrics));assert.deepEqual(metrics.bad,[],JSON.stringify(metrics));assert.ok(Math.abs(metrics.gap-24)<1,JSON.stringify(metrics));assert.equal(metrics.sameRow,true);
      if(metrics.mobile) {assert.equal(metrics.titleBelow,true);assert.ok(metrics.detailWidth>=44&&metrics.detailHeight>=44&&metrics.checkWidth>=44&&metrics.checkHeight>=44);}
      else assert.ok(metrics.alignment<1,JSON.stringify(metrics));
      assert.equal(metrics.visibleColumns,metrics.detailSpan);
      if(width===1440||width===390||width===768) await page.screenshot({path:path.join(out,`live-${width}-${theme}.png`)});
      if(theme==='light'&&(width===768||width===390||width===320)) {
        await heldList(`responsive-search-${width}`,()=>app.getByRole('searchbox').fill('Science'),{screenshot:width===390});
        await heldList(`responsive-clear-${width}`,()=>app.getByRole('searchbox').fill(''));
        if(await trigger.getAttribute('aria-expanded')!=='true') await trigger.click();
      }
    }
  }
  await page.setViewportSize({width:390,height:1000});
  const mobileAll=app.locator('.ph-article-select-all-mobile input');
  await mobileAll.check();assert.equal(await app.locator('thead input[type="checkbox"]').isChecked(),true);
  await mobileAll.uncheck();assert.equal(await row.locator('input.checkids').isChecked(),false);
  await row.locator('[data-bs-toggle="dropdown"]').click();
  await row.getByRole('button',{name:'Delete',exact:true}).click();
  await page.locator('#ph-submit-data-article-modalDelete.show').waitFor();
  await page.locator('#ph-submit-data-article-modalDelete').getByRole('button',{name:'No, keep it',exact:true}).click();
  await page.locator('#ph-submit-data-article-modalDelete.show').waitFor({state:'hidden'});
  await page.route('**/manage_article/listdata?*',route=>route.fulfill({status:503,contentType:'application/json',body:JSON.stringify({message:'QA: koneksi sementara gagal'})}),{times:1});
  await app.getByRole('combobox',{name:'Urutkan artikel'}).selectOption('oldest');
  await app.getByRole('alert').filter({hasText:'QA: koneksi sementara gagal'}).waitFor();
  await heldList('retry',()=>app.getByRole('button',{name:'Coba lagi',exact:true}).click());
  await page.emulateMedia({reducedMotion:'reduce'});
  await heldList('reduced-motion',()=>app.getByRole('searchbox').fill('Science'));
  await page.emulateMedia({reducedMotion:'no-preference'});
  const touch=await browser.newContext({ignoreHTTPSErrors:true,viewport:{width:320,height:1000},isMobile:true,hasTouch:true,storageState:await context.storageState()});
  const touchPage=await touch.newPage();touchPage.on('pageerror',e=>runtimeErrors.push(e.message));
  await touchPage.goto(origin+'/manage_article');
  const touchRow=touchPage.locator('tbody tr[data-article-id]').first();await touchRow.waitFor();
  await touchRow.locator('.ph-article-expand').tap();await touchRow.locator('input.checkids').tap();
  assert.equal(await touchRow.locator('.ph-article-expand').getAttribute('aria-expanded'),'true');assert.equal(await touchRow.locator('input.checkids').isChecked(),true);
  await touchPage.screenshot({path:path.join(out,'live-320-touch.png')});
  const touchMetrics=[];
  for(const width of [320,768]) {
    await touchPage.setViewportSize({width,height:1100});
    await touchRow.evaluate(()=>new Promise(resolve=>requestAnimationFrame(()=>requestAnimationFrame(resolve))));
    const metrics=await touchRow.evaluate(row=>{
      const d=row.querySelector('.ph-article-expand').getBoundingClientRect(),c=row.querySelector('.ph-article-check-target').getBoundingClientRect(),t=row.querySelector('.ph-article-title').getBoundingClientRect(),line=parseFloat(getComputedStyle(row.querySelector('.ph-article-title')).lineHeight);
      return {width:innerWidth,gap:row.querySelector('input.checkids').getBoundingClientRect().left-d.right,detailWidth:d.width,detailHeight:d.height,checkWidth:c.width,checkHeight:c.height,mobile:getComputedStyle(row).display==='grid',alignment:Math.abs(d.top+d.height/2-(row.getBoundingClientRect().top+row.getBoundingClientRect().height/2)),overflow:document.documentElement.scrollWidth>innerWidth+1};
    });
    assert.ok(Math.abs(metrics.gap-24)<1,JSON.stringify(metrics));assert.ok(metrics.detailWidth>=44&&metrics.detailHeight>=44&&metrics.checkWidth>=44&&metrics.checkHeight>=44);assert.equal(metrics.overflow,false);
    if(!metrics.mobile) assert.ok(metrics.alignment<1,JSON.stringify(metrics));
    touchMetrics.push(metrics);
  }
  await touch.close();
  await page.goto(origin+'/manage_article/add');await page.locator('#ph-app-manage-article input[name="title"]').waitFor();
  await page.goto(origin+'/manage_article/edit/'+firstId);await page.locator('#ph-app-manage-article input[name="title"]').waitFor();
  assert.deepEqual(runtimeErrors,[]);assert.deepEqual(vueWarnings,[]);assert.deepEqual(writes,[]);
  const vueVersion=await page.evaluate(()=>window.Vue?.version||null);
  await fs.writeFile(path.join(out,'live-verification.json'),JSON.stringify({checkedAt:new Date().toISOString(),vueVersion,initialTotal:initial.total,statusCounts:initial.status_counts,loadingCases:loadings.length,responsiveCases:responsive.length,loadings,responsive,touchMetrics,runtimeErrors,vueWarnings,writes},null,2));
  console.log(JSON.stringify({ok:true,vueVersion,loadingCases:loadings.length,responsiveCases:responsive.length,touchCases:touchMetrics.length,runtimeErrors,vueWarnings,writes}));
} finally {await browser.close();}
