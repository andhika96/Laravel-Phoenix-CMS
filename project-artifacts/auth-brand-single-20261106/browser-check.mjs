import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
const directory=path.resolve('project-artifacts/auth-brand-single-20261106');
const themes=['clean_peach','ocean_split','midnight_story','verdant_split'];
const pages=['login','signup','forgotpassword','resetpassword'];
const origin='http://laravel-13-phoenix.aruna';
const browser=await chromium.launch({channel:'chrome',headless:true});
const checks=[];
try {
 const page=await browser.newPage({reducedMotion:'reduce'});
 for(const width of [320,820,1440]) {
  await page.setViewportSize({width,height:960});
  for(const theme of themes) for(const flow of pages) {
   const response=await page.goto(`${origin}/__auth_brand_qa_20261106/${theme}-${flow}.html`,{waitUntil:'load'});
   await page.evaluate(()=>document.fonts.ready);
   const check={width,theme,flow,status:response.status(),brand:await page.locator('.ph-auth-brand:visible').count(),panelBrand:await page.locator('.ph-auth-panel-brand').count(),footer:await page.locator('.ph-auth-form-footer:visible').count(),overflow:await page.evaluate(()=>document.documentElement.scrollWidth>window.innerWidth)};
   checks.push(check);
   if(check.status!==200 || check.brand!==1 || check.panelBrand!==0 || check.footer!==1 || check.overflow) throw new Error(JSON.stringify(check));
   if(flow==='login' && ['ocean_split','verdant_split'].includes(theme) && width===1440) {
    const image=await page.locator('.ph-auth-surface').screenshot({animations:'disabled'});
    await fs.writeFile(path.join(directory,`${theme}-desktop.png`),image);
    await fs.writeFile(path.resolve(`public/assets/images/auth/themes/${theme}.png`),image);
   }
   if(flow==='login' && theme==='verdant_split' && width===320) await page.screenshot({path:path.join(directory,'verdant_split-mobile.png'),fullPage:true,animations:'disabled'});
  }
 }
 await page.setViewportSize({width:1440,height:960});
 const response=await page.goto(`${origin}/auth/login`,{waitUntil:'load'});
 const live={status:response.status(),theme:await page.locator('[data-modern-theme]').getAttribute('data-modern-theme').catch(()=>null),brand:await page.locator('.ph-auth-brand:visible').count(),panelBrand:await page.locator('.ph-auth-panel-brand').count()};
 await fs.writeFile(path.join(directory,'live-login.json'),JSON.stringify(live,null,2));
 if(live.theme && (live.brand!==1 || live.panelBrand!==0)) throw new Error(JSON.stringify(live));
 console.log(`${checks.length} browser checks passed at 320, 820, 1440px; live login status ${live.status}, theme ${live.theme}.`);
} finally {
 await fs.writeFile(path.join(directory,'browser-checks.json'),JSON.stringify(checks,null,2));
 await browser.close();
}
