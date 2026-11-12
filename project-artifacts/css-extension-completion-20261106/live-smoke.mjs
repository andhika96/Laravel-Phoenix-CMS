import fs from 'node:fs/promises';
import path from 'node:path';
import {createHash} from 'node:crypto';
import {chromium} from 'playwright-core';
import assert from 'node:assert/strict';
const directory=path.resolve('project-artifacts/css-extension-completion-20261106');
const backup=JSON.parse(await fs.readFile(path.join(directory,'backup.json'),'utf8'));
const oldCss=await fs.readFile(backup.backup,'utf8'),currentCss=await fs.readFile('public/assets/css/shared/extending-css-bootstrap-5.css');
const browser=await chromium.launch({channel:'chrome',headless:true});
const results=[];
try{
 const page=await browser.newPage({viewport:{width:1440,height:1000}});let baseline=false,errors=[],blocked=[];
 page.on('pageerror',error=>errors.push(error.message));
 await page.route('**/*',route=>{if(['GET','HEAD'].includes(route.request().method()))return route.continue();blocked.push(route.request().url());return route.abort();});
 await page.route('**/assets/css/shared/extending-css-bootstrap-5.css?*',route=>baseline?route.fulfill({status:200,contentType:'text/css',body:oldCss}):route.continue());
 for(const pathname of ['/home','/article','/event','/auth/login']){
  const pair=[];
  for(const before of [true,false]){
   baseline=before;errors=[];blocked=[];
   const response=await page.goto('http://laravel-13-phoenix.aruna'+pathname,{waitUntil:'networkidle'});
   pair.push({status:response.status(),finalPath:new URL(page.url()).pathname,errors:[...errors],blocked:[...blocked],extensionLoaded:await page.locator('link[href*="extending-css-bootstrap-5.css"]').count()});
  }
  assert.deepEqual(pair[1],pair[0],pathname);assert.equal(pair[1].status,200);results.push({pathname,...pair[1],baselineUnchanged:true});
 }
 const response=await page.request.get('http://laravel-13-phoenix.aruna/assets/css/shared/extending-css-bootstrap-5.css?qa=20261106');assert.equal(response.status(),200);
 const served=await response.body();assert.equal(createHash('sha256').update(served).digest('hex'),createHash('sha256').update(currentCss).digest('hex'));
 await fs.writeFile(path.join(directory,'live-smoke.json'),JSON.stringify({results,cssHttpStatus:response.status(),cssHashMatchesDisk:true,cssContentEncoding:response.headers()['content-encoding']||'identity'},null,2));
 console.log('4 live GET baseline comparisons passed; application-served CSS matches active source. No write requests permitted.');
}finally{await browser.close();}
