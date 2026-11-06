import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
const artifact=path.join(process.cwd(),'project-artifacts/auth-theme-notice-refinement-20261106');
const browser=await chromium.launch({channel:'chrome',headless:true});
try{
 const page=await browser.newPage({viewport:{width:390,height:844},reducedMotion:'reduce'});
 await page.goto('http://laravel-13-phoenix.aruna/auth/login',{waitUntil:'networkidle'});
 await page.evaluate(async()=>{
  AuthVue3.responseStatusToast='ph-callout-danger';AuthVue3.isArrayMessageAfterSubmit=1;AuthVue3.responseMessageAfterSubmit={email:['Enter a valid email.'],password:['Check your password.']};
  await AuthVue3.$nextTick();bootstrap.Toast.getOrCreateInstance(document.querySelector('.ph-notice-toast'),{autohide:false}).show();
  await document.fonts.ready;
 });
 const metrics=await page.locator('.ph-notice-toast').evaluate(el=>{let s=getComputedStyle(el),p=getComputedStyle(el.querySelector('.toast-header-icon'),'::before');return{color:s.color,bg:s.backgroundColor,fontSize:s.fontSize,radius:s.borderRadius,iconFont:p.fontFamily,iconWeight:p.fontWeight,iconLoaded:document.fonts.check('900 20px "Font Awesome 5 Duotone"'),bodyNotice:!!document.querySelector('body > .ph-notice')};});
 assert.equal(metrics.bg,'rgb(255, 255, 255)');assert.equal(metrics.color,'rgb(0, 0, 0)');assert.equal(metrics.iconLoaded,true);assert.equal(metrics.bodyNotice,true);
 await page.screenshot({path:path.join(artifact,'screenshots/auth-cms-notice-actual.png'),animations:'disabled'});
 await page.locator('.ph-notice-toast .btn-close').click();await page.locator('.ph-notice-toast').waitFor({state:'hidden'});
 await fs.writeFile(path.join(artifact,'live-auth-notice-metrics.json'),JSON.stringify(metrics,null,2));
 console.log(JSON.stringify(metrics));
}finally{await browser.close();}
