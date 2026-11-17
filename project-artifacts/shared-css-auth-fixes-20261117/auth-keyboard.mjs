import fs from 'node:fs/promises';
import assert from 'node:assert/strict';
import {chromium} from 'playwright-core';
const phase=process.argv[2]||'before',dir='project-artifacts/shared-css-auth-fixes-20261117';
const browser=await chromium.launch({channel:'chrome',headless:true}),errors=[],results=[];
try {
 const page=await browser.newPage();page.on('pageerror',e=>errors.push(e.message));
 let count=0,release;
 await page.route('**/*',r=>r.request().method()==='GET'?r.continue():new Promise(resolve=>{count++;release=async()=>{await r.fulfill({contentType:'application/json',body:JSON.stringify({status:'failed',message:'QA retry.'})});resolve();};}));
 for(const theme of ['default','clean_peach','ocean_split','midnight_story','verdant_split']){
  count=0;
  await page.goto(`http://laravel-13-phoenix.aruna/__auth_fix_qa_20261117/${theme}-forgotpassword.html`);
  await page.locator('[name=email]').fill('fixture@example.test');
  await page.locator('input[type=submit]').click();await page.locator('.btn-submit-loading').waitFor();
  await page.locator('[name=email]').press('Enter');
  await release();await page.locator('input[type=submit]').waitFor();
  results.push({theme,requests:count});assert.equal(count,1);
 }
 assert.deepEqual(errors,[]);
 console.log('5/5 pending-submit keyboard checks passed; one request each, no JavaScript errors.');
}finally{await fs.writeFile(`${dir}/auth-keyboard-${phase}.json`,JSON.stringify({results,errors},null,2));await browser.close();}
