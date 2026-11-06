import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
const directory=path.resolve('project-artifacts/auth-layout-width-20261106');
const browser=await chromium.launch({channel:'chrome',headless:true});
const checks=[];
try {
 const page=await browser.newPage({reducedMotion:'reduce'});
 async function verify(theme,mode,width,flow,variant='saved options') {
  const metrics=await page.evaluate(()=>{
   const root=document.querySelector('[data-modern-theme]'),surface=root.querySelector('.ph-auth-surface'),form=root.querySelector('.ph-auth-form-content');
   const style=getComputedStyle(root),available=root.clientWidth-parseFloat(style.paddingLeft)-parseFloat(style.paddingRight);
   const cap=root.dataset.modernTheme==='ocean_split'?{compact:900,normal:1080,wide:1240}[root.dataset.cardWidth]:1140;
   return {available,cap,surface:surface.getBoundingClientRect().width,form:form.getBoundingClientRect().width,maxWidth:getComputedStyle(surface).maxWidth,overflow:document.documentElement.scrollWidth>innerWidth,brand:document.querySelectorAll('.ph-auth-brand').length};
  });
  assert.ok(!metrics.overflow,JSON.stringify({theme,mode,width,flow,variant,metrics}));
  assert.ok(Math.abs(metrics.surface-(mode==='fluid'?metrics.available:Math.min(metrics.cap,metrics.available)))<2,JSON.stringify({theme,mode,width,flow,variant,metrics}));
  assert.ok(metrics.form>0 && metrics.form<=(width<768?390:355)+1);
  assert.equal(metrics.brand,1);
  checks.push({theme,mode,width,flow,variant,...metrics});
 }
 for(const width of [320,390,820,1440,1920,3840]) {
  await page.setViewportSize({width,height:1000});
  for(const theme of ['ocean_split','verdant_split']) for(const mode of ['bounded','fluid']) for(const flow of ['login','signup','forgotpassword','resetpassword']) {
   await page.goto(`http://laravel-13-phoenix.aruna/__auth_layout_qa_20261106/${theme}-${mode}-${flow}.html`,{waitUntil:'load'});
   await page.evaluate(()=>document.fonts.ready);
   await verify(theme,mode,width,flow);
   if(flow==='login' && width===1920) await page.screenshot({path:path.join(directory,`${theme}-${mode}-1920.png`),fullPage:true,animations:'disabled'});
   if(flow==='login' && mode==='fluid' && width===390) await page.screenshot({path:path.join(directory,`${theme}-fluid-mobile.png`),fullPage:true,animations:'disabled'});
   if(flow==='login' && mode==='fluid' && width===3840) await page.screenshot({path:path.join(directory,`${theme}-fluid-3840.png`),fullPage:true,animations:'disabled'});
   if(flow==='login' && mode==='fluid' && width===1920 && theme==='ocean_split') {
    for(const card of ['compact','normal','wide']) for(const side of ['left','right']) for(const ratio of ['40:60','50:50']) {
     await page.locator('[data-modern-theme]').evaluate((el,values)=>{[el.dataset.cardWidth,el.dataset.panelSide,el.dataset.panelRatio]=values;},[card,side,ratio]);
     await verify(theme,mode,width,flow,`${card}/${side}/${ratio}`);
     const proportions=await page.evaluate(()=>{const root=document.querySelector('[data-modern-theme]');return {side:root.dataset.panelSide,ratio:root.dataset.panelRatio,panel:root.querySelector('.ph-auth-panel').getBoundingClientRect().width+14,form:root.querySelector('.ph-auth-form-area').getBoundingClientRect().width};});
     assert.ok(Math.abs(proportions.panel/proportions.form-(ratio==='40:60'?2/3:1))<.01);
    }
   }
   if(flow==='login' && mode==='fluid' && width===820) {
    await page.locator('[data-modern-theme]').evaluate(el=>el.dataset.tabletLayout='stack');
    await verify(theme,mode,width,flow,'tablet stacked');
   }
   if(flow==='login' && mode==='fluid' && width===390) {
    await page.locator('[data-modern-theme]').evaluate(el=>el.dataset.mobileVisual='above');
    await verify(theme,mode,width,flow,'mobile visual above');
   }
  }
 }
 console.log(`${checks.length} layout checks passed, including ultrawide, card widths, both panel sides, ratios and responsive variants.`);
} finally {
 await fs.writeFile(path.join(directory,'layout-browser-checks.json'),JSON.stringify(checks,null,2));
 await browser.close();
}
