import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
import {chromium} from 'playwright-core';
const dir=path.resolve('project-artifacts/shared-css-auth-fixes-20261117'),phase=process.argv[2]||'before';
const browser=await chromium.launch({channel:'chrome',headless:true}),results=[],errors=[],writes=[];
const modern=['clean_peach','ocean_split','midnight_story','verdant_split'];
let mode='success',release;
try {
 const page=await browser.newPage({reducedMotion:'reduce'});
 page.on('pageerror',e=>errors.push({url:page.url(),message:e.message}));
 await page.route('**/*',route=>{
  const r=route.request();
  if(r.url().includes('/auth/checkdata'))return route.fulfill({contentType:'application/json',body:JSON.stringify({status:'success',message:''})});
  if(r.method()==='GET')return route.continue();
  writes.push({method:r.method(),url:r.url(),mocked:true});
  return new Promise(resolve=>release=async()=>{
   if(mode==='network')await route.abort('failed');
   else await route.fulfill({status:mode==='validation'?422:200,contentType:'application/json',body:JSON.stringify(mode==='success'?{status:'success',redirect_url:page.url()+'#qa-success'}:mode==='notice'?{status:'success',message:'QA recovery notice.'}:{status:'failed',message:{email:['QA validation message.']}})});
   resolve();
  });
 });
 async function load(file,width){
  await page.setViewportSize({width,height:900});
  await page.goto(`http://laravel-13-phoenix.aruna/__auth_fix_qa_20261117/${file}`,{waitUntil:'load'});
  await page.addStyleTag({content:'*,*::before,*::after{transition:none!important;animation:none!important}'});
  await page.evaluate(()=>document.fonts.ready);
  for(const [selector,value] of [['[name="email"],[name="account[email]"]','fixture@example.test'],['[name="password"],[name="account[password]"]','fixture-password'],['[name="password_confirmation"]','fixture-password'],['[name="account[fullname]"]','QA Fixture'],['[name="account[username]"]','qa-fixture']]){
   const control=page.locator(selector);if(await control.count())await control.fill(value);
  }
  await page.evaluate(()=>document.querySelectorAll('input[type=checkbox][required]').forEach(e=>e.checked=true));
 }
 async function measure(){return page.evaluate(()=>{
  const e=document.querySelector('input[type=submit],.btn-submit-loading,.btn-logged'),r=e.getBoundingClientRect(),links=document.querySelector('.ph-auth-page-links');
  return{x:r.x+scrollX,y:r.y+scrollY,width:r.width,height:r.height,documentWidth:document.documentElement.scrollWidth,viewport:innerWidth,gap:links?links.getBoundingClientRect().top-r.bottom:null,label:e.value||e.textContent.trim(),tag:e.tagName,disabled:e.disabled??false,busy:e.getAttribute('aria-busy')};
 });}
 const files=(await fs.readdir(path.join(dir,'auth-rendered'))).filter(f=>f.endsWith('.html')).sort();
 for(const file of files)for(const width of [390,820,1440]){
  const theme=file.replace(/-(login|signup|forgotpassword|resetpassword)\.html$/,'');
  mode='success';await load(file,width);
  const idle=await measure(),label=await page.locator('input[type=submit]').inputValue();
  await page.locator('input[type=submit]').click();await page.locator('.btn-submit-loading').waitFor();
  const loading=await measure();await release();await page.locator('.btn-logged').waitFor();
  const success=await measure();
  if(phase!=='before'){
   for(const [state,reading] of [['loading',loading],['success',success]]){
    for(const p of ['x','y','width','height'])assert.ok(Math.abs(reading[p]-idle[p])<=.5,`${file}/${width}/${state}/${p}: ${reading[p]} vs ${idle[p]}`);
    assert.equal(reading.tag,'BUTTON');assert.equal(reading.disabled,true);
   }
   assert.equal(loading.busy,'true');
   if(modern.includes(theme))assert.ok(idle.gap<=16.5,`${file}/${width} excessive gap ${idle.gap}`);
   assert.ok(idle.documentWidth<=width,`${file}/${width} horizontal overflow`);
  }
  if(theme==='midnight_story'&&file.endsWith('-login.html')&&width===1440)await page.screenshot({path:path.join(dir,`auth-success-${phase}.png`),fullPage:true});
  const restored=[];
  for(const state of ['validation','network',...(file.includes('-forgotpassword.')?['notice']:[])]){
   mode=state;await load(file,width);
   await page.locator('input[type=submit]').click();await page.locator('.btn-submit-loading').waitFor();await release();
   await page.locator('input[type=submit]').waitFor();
   const notice=page.locator('body > .ph-notice .ph-notice-toast.show');await notice.waitFor({timeout:3000});
   assert.equal(await page.locator('input[type=submit]').inputValue(),label);
   const after=await measure();
   if(phase!=='before')for(const p of ['x','y','width','height'])assert.ok(Math.abs(after[p]-idle[p])<=.5,`${file}/${width}/${state}/${p}`);
   assert.equal(await notice.locator('.toast-header-title.toast-header-icon').count(),1);
   await notice.locator('[data-bs-dismiss=toast]').click();
   restored.push(state);
  }
  results.push({file,width,idle,loading,success,restored});
 }
 assert.deepEqual(errors,[]);
 console.log(`${phase}: ${results.length} auth theme/flow/viewport cases; initial/loading/success + validation/network/recovery notices; ${writes.length} writes intercepted, zero real POSTs; no page errors.`);
}finally{await fs.writeFile(path.join(dir,`auth-states-${phase}.json`),JSON.stringify({results,errors,writes},null,2));await browser.close();}
