import fs from 'node:fs/promises';
import {chromium} from 'playwright-core';
const dir='project-artifacts/shared-css-auth-fixes-20261117';
const browser=await chromium.launch({channel:'chrome',headless:true});
const page=await browser.newPage({viewport:{width:1440,height:900}});
const errors=[];page.on('pageerror',e=>errors.push(e.message));
let pending;
await page.route('**/*',r=>{
 if(r.request().method()!=='GET')return new Promise(resolve=>pending=async()=>{await r.fulfill({contentType:'application/json',body:JSON.stringify({status:'success',redirect_url:page.url()+'#qa-success'})});resolve();});
 if(r.request().url().includes('/auth/checkdata'))return r.fulfill({contentType:'application/json',body:JSON.stringify({status:'success',message:''})});
 return r.continue();
});
const results=[];
await page.goto('http://laravel-13-phoenix.aruna/__auth_fix_qa_20261117/midnight_story-login.html');
await page.evaluate(()=>document.fonts.ready);
await page.locator('[name=email]').fill('fixture@example.test');
await page.locator('[name=password]').fill('fixture-password');
async function measure(state){
 const value=await page.evaluate(()=>{
  const rect=s=>{const e=document.querySelector(s);return e?{...e.getBoundingClientRect().toJSON(),margin:getComputedStyle(e).margin,heightCSS:getComputedStyle(e).height,display:getComputedStyle(e).display}:null;};
  return{button:rect('.btn-submit-login,.btn-submit-loading,.btn-logged'),fields:rect('.ph-auth-form-fields'),links:rect('.ph-auth-page-links'),content:rect('.ph-auth-form-content'),panel:rect('.ph-auth-panel'),children:[...document.querySelector('.ph-auth-form-fields').children].map(e=>({tag:e.tagName,cls:e.className,type:e.type,display:getComputedStyle(e).display,text:e.textContent.trim().slice(0,40)})),html:document.querySelector('.ph-auth-form-fields').innerHTML};
 });
 results.push({state,...value});
 await page.screenshot({path:`${dir}/auth-${state}-before.png`,fullPage:true});
}
await measure('idle');
await page.locator('input[type=submit]').click();
await page.locator('.btn-submit-loading').waitFor();
await measure('loading');
await pending();
await page.locator('.btn-logged').waitFor();
await measure('success');
await fs.writeFile(`${dir}/auth-probe-before.json`,JSON.stringify({results,errors},null,2));
console.log(results.map(({state,button,links,fields,children})=>({state,button,links,fields,children})),errors);
await browser.close();
