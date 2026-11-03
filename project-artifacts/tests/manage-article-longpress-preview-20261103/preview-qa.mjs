import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {pathToFileURL} from 'node:url';
import {chromium,firefox} from 'playwright-core';

const engine=process.argv.includes('--firefox')?'firefox':'chrome';
const out=path.resolve('project-artifacts/qa/manage-article-longpress-preview-20261103');
const browser=await (engine==='firefox'?firefox:chromium).launch(engine==='chrome'?{channel:'chrome',headless:true}:{headless:true});
const errors=[],requests=[],cases=[],interactions=[];
try {
  const context=await browser.newContext({viewport:{width:900,height:1500},ignoreHTTPSErrors:true,hasTouch:engine==='chrome'});
  const page=await context.newPage();
  page.on('pageerror',e=>errors.push(e.message));
  page.on('request',r=>{if(r.url().includes('laravel-13-phoenix.aruna'))requests.push(r.url());});
  await page.goto(pathToFileURL(path.join(out,'rendered.html')).href);
  const frame=page.frames().find(f=>f.parentFrame());
  const f=page.frameLocator('iframe');
  await f.locator('[data-record]').first().waitFor();
  async function configure(variant,width=414,theme='light') {
    await frame.evaluate(({variant,width,theme})=>{
      for(const s of document.querySelectorAll('[data-variant]'))s.hidden=s.dataset.variant!==variant;
      document.documentElement.style.colorScheme=theme;
      const rows={reading:{expanded:[],selected:[],selecting:false},leading:{expanded:[],selected:[],selecting:false}};
      window.dispatchEvent(new CustomEvent('openai:set_globals',{detail:{globals:{widgetState:{modelContent:{width:String(width)},privateContent:{rows}}}}}));
    },{variant,width,theme});
    const stage=f.locator(`[data-variant="${variant}"] .mp-app`);
    await stage.evaluate(()=>new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r))));
    return stage;
  }
  async function geometry(stage,mode) {
    const m=await stage.evaluate(stage=>{
      const b=stage.getBoundingClientRect();
      const overflow=[...stage.querySelectorAll('*')].filter(el=>el.checkVisibility()&&el.getBoundingClientRect().width>0).filter(el=>{const r=el.getBoundingClientRect();return r.left<b.left-1||r.right>b.right+1;}).map(el=>el.className?.baseVal||el.className||el.tagName).slice(0,10);
      const actions=[...stage.querySelectorAll('.mp-header-actions > *')].map(el=>{const b=el.getBoundingClientRect(),t=el.querySelector('span').getBoundingClientRect();return {height:b.height,delta:Math.abs(t.top+t.height/2-(b.top+b.height/2)),clipped:el.scrollWidth>el.clientWidth+1};});
      const checks=[...stage.querySelectorAll('.mp-check')].filter(el=>el.checkVisibility()).map(el=>{const r=el.getBoundingClientRect();return {width:r.width,height:r.height};});
      return {width:b.width,overflow,actions,checks,selected:stage.querySelectorAll('.mp-record.mp-picked').length,count:stage.querySelector('[data-selection-count]').textContent};
    });
    assert.deepEqual(m.overflow,[],JSON.stringify({mode,m}));
    for(const a of m.actions){assert.ok(a.height>=44);assert.ok(a.delta<.6);assert.equal(a.clipped,false);}
    if(mode==='selection'){assert.equal(m.checks.length,4);for(const c of m.checks)assert.ok(c.width>=44&&c.height>=44);}
    else assert.equal(m.checks.length,0);
    return m;
  }
  for(const variant of ['Judul utama','Kontrol di kiri'])for(const width of [320,390,414,720])for(const theme of ['light','dark']) {
    const stage=await configure(variant,width,theme);
    const normal=await geometry(stage,'normal');
    await stage.locator('[data-enter-selection]').click();
    await stage.locator('[data-record]').first().locator('.mp-title').click();
    const selection=await geometry(stage,'selection');assert.equal(selection.selected,1);
    if(width===414||width===320)await stage.screenshot({path:path.join(out,`${engine}-${variant==='Judul utama'?'reading':'leading'}-${width}-${theme}-selected.png`)});
    await stage.locator('[data-exit-selection]').click();
    assert.equal(await stage.locator('.mp-bulk').isVisible(),false);assert.equal(await stage.locator('.mp-record.mp-picked').count(),0);
    cases.push({variant,width,theme,normal,selection});
  }
  async function mouseHold(target,duration=790) {
    await target.scrollIntoViewIfNeeded();
    const b=await target.boundingBox();
    await page.mouse.move(b.x+Math.min(b.width/2,70),b.y+Math.min(b.height/2,12));
    await page.mouse.down();await page.waitForTimeout(duration);await page.mouse.up();
  }
  for(const variant of ['Judul utama','Kontrol di kiri']) {
    const stage=await configure(variant),row=stage.locator('[data-record]').first(),second=stage.locator('[data-record]').nth(1),title=row.locator('.mp-title');
    await title.click();assert.equal(await title.getAttribute('aria-expanded'),'true');assert.equal(await stage.locator('.mp-bulk').isVisible(),false);
    await title.click();assert.equal(await title.getAttribute('aria-expanded'),'false');
    await mouseHold(title,220);assert.equal(await stage.locator('.mp-bulk').isVisible(),false);assert.equal(await title.getAttribute('aria-expanded'),'true');
    await title.click();
    await mouseHold(title);assert.equal(await stage.locator('.mp-bulk').isVisible(),true);
    assert.equal(await row.locator('[data-check]').isChecked(),true);assert.equal(await stage.locator('.mp-details:visible').count(),0);
    assert.equal(await title.getAttribute('aria-pressed'),'true');assert.equal(await title.getAttribute('aria-expanded'),null);
    await title.click();assert.equal(await row.locator('[data-check]').isChecked(),false);assert.equal(await stage.locator('.mp-bulk').isVisible(),true);assert.match(await stage.locator('[data-selection-count]').innerText(),/^0 dipilih$/);
    await second.locator('.mp-byline').click();assert.equal(await second.locator('[data-check]').isChecked(),true);
    await title.click();assert.equal(await row.locator('[data-check]').isChecked(),true);assert.match(await stage.locator('[data-selection-count]').innerText(),/^2 dipilih$/);
    await row.locator('[data-check]').uncheck();assert.equal(await row.locator('[data-check]').isChecked(),false);assert.equal(await second.locator('[data-check]').isChecked(),true);
    await stage.locator('[data-all]').check();assert.equal(await stage.locator('.mp-record.mp-picked').count(),4);
    await stage.locator('[data-all]').uncheck();assert.equal(await stage.locator('.mp-record.mp-picked').count(),0);assert.equal(await stage.locator('.mp-bulk').isVisible(),true);
    assert.equal(await stage.locator('[data-selection-action]').isDisabled(),true);
    await title.focus();await page.keyboard.press('Enter');assert.equal(await title.getAttribute('aria-pressed'),'true');await page.keyboard.press('Space');assert.equal(await title.getAttribute('aria-pressed'),'false');
    await stage.locator('[data-exit-selection]').click();assert.equal(await stage.locator('.mp-bulk').isVisible(),false);assert.equal(await title.getAttribute('aria-pressed'),null);assert.equal(await title.getAttribute('aria-expanded'),'false');
    await title.click();assert.equal(await title.getAttribute('aria-expanded'),'true');await title.click();
    await stage.locator('[data-enter-selection]').focus();await page.keyboard.press('Enter');assert.equal(await stage.locator('.mp-bulk').isVisible(),true);await page.keyboard.press('Escape');assert.equal(await stage.locator('.mp-bulk').isVisible(),false);
    for(const cancel of ['move','cancel','scroll']) {
      await title.dispatchEvent('pointerdown',{button:0,isPrimary:true,pointerId:13,pointerType:'touch',clientX:100,clientY:100});
      if(cancel==='move')await title.dispatchEvent('pointermove',{pointerId:13,pointerType:'touch',clientX:100,clientY:130});
      if(cancel==='cancel')await title.dispatchEvent('pointercancel',{pointerId:13,pointerType:'touch'});
      if(cancel==='scroll')await frame.evaluate(()=>window.dispatchEvent(new Event('scroll')));
      await page.waitForTimeout(780);await title.dispatchEvent('pointerup',{pointerId:13,pointerType:'touch'});
      assert.equal(await stage.locator('.mp-bulk').isVisible(),false,`${cancel} must cancel long press`);
    }
    await mouseHold(row.locator('[data-options]'));assert.equal(await stage.locator('.mp-bulk').isVisible(),false);await page.keyboard.press('Escape');
    await stage.locator('[data-search]').fill('Science');await stage.locator('.mp-loading').waitFor({state:'visible'});await stage.locator('.mp-loading').waitFor({state:'hidden'});assert.equal(await stage.locator('[data-record]').count(),1);
    const science=stage.locator('[data-record]').first();await mouseHold(science.locator('.mp-title'));assert.equal(await science.locator('[data-check]').isChecked(),true);await stage.locator('[data-exit-selection]').click();
    await stage.locator('[data-search]').fill('');await stage.locator('.mp-loading').waitFor({state:'visible'});await stage.locator('.mp-loading').waitFor({state:'hidden'});
    interactions.push({variant,shortTapDetails:true,longPressSelects:true,releaseNotDeselect:true,tapDeselect:true,multiple:true,zeroStaysInMode:true,exitClears:true,selectAll:true,keyboard:true,scrollCancels:true,movementCancels:true,pointerCancel:true,menuExcluded:true,filteredSelection:true});
  }
  if(engine==='chrome') {
    const stage=await configure('Kontrol di kiri'),title=stage.locator('[data-record]').first().locator('.mp-title');
    await title.scrollIntoViewIfNeeded();const b=await title.boundingBox(),x=b.x+45,y=b.y+10;
    const cdp=await context.newCDPSession(page);
    await cdp.send('Input.dispatchTouchEvent',{type:'touchStart',touchPoints:[{x,y,id:1}]});await page.waitForTimeout(800);
    await cdp.send('Input.dispatchTouchEvent',{type:'touchEnd',touchPoints:[]});
    assert.equal(await stage.locator('.mp-bulk').isVisible(),true);assert.equal(await stage.locator('[data-record]').first().locator('[data-check]').isChecked(),true);
    await stage.screenshot({path:path.join(out,'chrome-real-touch-selected.png')});
    await stage.locator('[data-exit-selection]').click();
    interactions.push({actualChromiumTouch:true,selectionAfterRelease:true});
  }
  assert.deepEqual(errors,[]);assert.deepEqual(requests,[]);
  await fs.writeFile(path.join(out,`verification-${engine}.json`),JSON.stringify({ok:true,engine,cases,interactions,errors,requests},null,2));
  console.log(JSON.stringify({ok:true,engine,layoutCases:cases.length,interactionGroups:interactions.length,errors,requests}));
} finally {await browser.close();}
