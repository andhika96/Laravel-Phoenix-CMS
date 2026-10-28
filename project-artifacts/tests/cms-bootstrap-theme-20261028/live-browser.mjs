import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {chromium} from 'playwright-core';

const origin='https://laravel-13-phoenix.aruna',out=path.resolve('project-artifacts/qa/cms-bootstrap-theme-20261028');
const browser=await chromium.launch({channel:'chrome',headless:true}),errors=[],warnings=[],writes=[],cases=[];
try {
  const context=await browser.newContext({ignoreHTTPSErrors:true,viewport:{width:1440,height:1100}}),page=await context.newPage();
  page.on('pageerror',e=>errors.push(e.message));
  page.on('console',m=>{if(m.text().includes('[Vue warn]'))warnings.push(m.text());});
  page.on('request',r=>{if(r.method()!=='GET'&&!r.url().includes('/auth/'))writes.push({method:r.method(),url:r.url()});});
  await page.goto(origin+'/manage_article');
  if(page.url().includes('/auth/login')){
    await page.getByRole('textbox',{name:'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox',{name:'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button',{name:'Log In',exact:true}).click();
  }
  await page.locator('tr[data-article-id]').first().waitFor();
  assert.equal(await page.locator('link[href*="cms-bootstrap-theme.css"]').count(),1);
  const asset=await context.request.get(origin+'/assets/css/cms-bootstrap-theme.css');assert.equal(asset.status(),200);
  const app=page.locator('#ph-app-manage-article');
  async function reference() {
    return page.evaluate(()=>{
      const swatch=document.createElement('span');swatch.style.background='var(--ph-theme-primary-subtle, color-mix(in srgb, var(--ph-primary) 10%, var(--ph-content-bg, var(--bs-body-bg))))';swatch.style.color='var(--ph-theme-text-accessible, var(--ph-text-main, var(--bs-body-color)))';document.body.append(swatch);
      const style=getComputedStyle(swatch),result={background:style.backgroundColor,color:style.color};swatch.remove();return result;
    });
  }
  async function inspectItem(item,label,expected,{semantic=false}={}) {
    const result=await item.evaluate(el=>({background:getComputedStyle(el).backgroundColor,color:getComputedStyle(el).color,active:el.matches(':active'),focus:el.matches(':focus'),disabled:el.matches('.disabled,:disabled')}));
    assert.equal(result.background,expected.background,JSON.stringify({label,result,expected}));
    if(!semantic)assert.equal(result.color,expected.color,JSON.stringify({label,result,expected}));
    assert.notEqual(result.background,'rgb(13, 110, 253)');return result;
  }
  for(const width of [1440,390]) {
    await page.setViewportSize({width,height:1100});
    for(const mode of ['light','dark']) for(const accent of ['native','#9D00FF','#667085']) {
      await page.evaluate(({mode,accent})=>{document.documentElement.setAttribute('data-bs-theme',mode);if(accent==='native')document.documentElement.style.removeProperty('--ph-theme-primary');else document.documentElement.style.setProperty('--ph-theme-primary',accent);},{mode,accent});
      const expected=await reference();
      const row=app.locator('tr[data-article-id]').first(),trigger=row.locator('[data-bs-toggle="dropdown"]');
      await trigger.click();await row.locator('.dropdown-menu.show').waitFor();
      const view=row.getByRole('link',{name:'View article',exact:true});
      await view.hover();const hover=await inspectItem(view,'hover',expected);
      await view.focus();const focus=await inspectItem(view,'focus',expected);assert.equal(focus.focus,true);
      await view.evaluate(el=>el.addEventListener('click',event=>event.preventDefault(),{once:true}));
      const rect=await view.boundingBox();await page.mouse.move(rect.x+rect.width/2,rect.y+rect.height/2);await page.mouse.down();
      const pressed=await inspectItem(view,'pressed',expected);assert.equal(pressed.active,true);
      if(accent==='native'&&mode==='light')await page.screenshot({path:path.join(out,`dropdown-pressed-${width}.png`)});
      await page.mouse.up();
      if(!await row.locator('.dropdown-menu').isVisible())await trigger.click();
      await view.evaluate(el=>el.classList.add('active'));
      const active=await inspectItem(view,'active class',expected);
      await view.evaluate(el=>el.classList.remove('active'));
      const danger=row.getByRole('button',{name:'Delete',exact:true});await danger.focus();
      const dangerColor=await danger.evaluate(el=>({color:getComputedStyle(el).color,danger:getComputedStyle(el).getPropertyValue('--bs-danger').trim()}));
      assert.ok(dangerColor.color.includes('220, 53, 69'),JSON.stringify(dangerColor));
      await page.keyboard.press('Escape');assert.equal(await trigger.getAttribute('aria-expanded'),'false');
      cases.push({page:'manage_article',width,mode,accent,hover,focus,pressed,active,dangerColor});
    }
  }
  const otherPages=[];
  for(const route of ['/dashboard','/account','/manage_coverimage','/manage_event']) {
    await page.setViewportSize({width:1440,height:1100});await page.goto(origin+route);
    await page.locator('.ph-scrollable-content').waitFor();
    assert.equal(await page.locator('link[href*="cms-bootstrap-theme.css"]').count(),1);
    await page.locator('.ph-lucent-account-menu-button').click();
    const menu=page.locator('.ph-sidebar-profile-menu');await menu.waitFor({state:'visible'});
    const item=menu.locator('.ph-profile-menu-section a.dropdown-item').first();
    await item.focus();
    const value=await item.evaluate(el=>({color:getComputedStyle(el).color,background:getComputedStyle(el).backgroundColor,activeBg:getComputedStyle(el.closest('.dropdown-menu')).getPropertyValue('--bs-dropdown-link-active-bg')}));
    assert.notEqual(value.background,'rgb(13, 110, 253)');
    await item.evaluate(el=>el.classList.add('active'));
    assert.notEqual(await item.evaluate(el=>getComputedStyle(el).backgroundColor),'rgb(13, 110, 253)');
    await page.keyboard.press('Escape');otherPages.push({route,...value});
  }
  const publicResponse=await context.request.get(origin+'/article');
  const publicCheck={status:publicResponse.status(),bridgeLoaded:(await publicResponse.text()).includes('cms-bootstrap-theme.css')};
  assert.equal(publicCheck.bridgeLoaded,false);
  assert.deepEqual(errors,[]);assert.deepEqual(warnings,[]);assert.deepEqual(writes,[]);
  await fs.writeFile(path.join(out,'live-verification.json'),JSON.stringify({ok:true,checkedAt:new Date().toISOString(),cases,otherPages,publicCheck,errors,warnings,writes},null,2));
  console.log(JSON.stringify({ok:true,articleCases:cases.length,otherPages:otherPages.length,publicCheck,errors,warnings,writes}));
} finally {await browser.close();}
