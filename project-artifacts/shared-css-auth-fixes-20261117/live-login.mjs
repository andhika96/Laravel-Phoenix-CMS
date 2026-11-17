import fs from 'node:fs/promises';
import assert from 'node:assert/strict';
import {chromium} from 'playwright-core';
const dir='project-artifacts/shared-css-auth-fixes-20261117';
assert.ok(process.env.PHOENIX_QA_USER&&process.env.PHOENIX_QA_PASSWORD,'Use ephemeral QA credentials.');
const browser=await chromium.launch({channel:'chrome',headless:true}),errors=[],writes=[];
const result={};
try {
 const page=await browser.newPage({viewport:{width:1422,height:740},deviceScaleFactor:1.35});
 page.on('pageerror',e=>errors.push({url:page.url(),message:e.message}));
 await page.route('**/*',r=>{
  const q=r.request();
  if(q.method()==='GET')return r.continue();
  const login=q.method()==='POST'&&new URL(q.url()).pathname==='/auth/login';
  writes.push({method:q.method(),path:new URL(q.url()).pathname,allowed:login});
  return login?r.continue():r.abort();
 });
 const response=await page.goto('http://laravel-13-phoenix.aruna/auth/login');
 assert.equal(response.status(),200);
 result.authSharedCSS=await page.locator('link[href*="assets/css/shared"]').count();
 assert.equal(result.authSharedCSS,0);
 await page.locator('[name=email]').fill(process.env.PHOENIX_QA_USER);
 await page.locator('[name=password]').fill(process.env.PHOENIX_QA_PASSWORD);
 const [loginResponse]=await Promise.all([page.waitForResponse(r=>r.request().method()==='POST'&&new URL(r.url()).pathname==='/auth/login'),page.locator('input[type=submit]').click()]);
 const payload=await loginResponse.json();
 result.loginHTTP=loginResponse.status();result.loginStatus=payload.status;
 assert.equal(payload.status,'success', 'Real login failed; inspect existing captcha/account configuration without changing it.');
 await page.waitForURL('**/dashboard',{timeout:15000});
 await page.evaluate(()=>document.fonts.ready);
 result.destination=new URL(page.url()).pathname;
 result.cmsSharedCSS=await page.locator('link[href*="assets/css/shared"]').count();
 assert.ok(result.cmsSharedCSS>0);
 assert.deepEqual(errors,[]);
 console.log('Real local admin login succeeded and reached dashboard with shared CSS; isolated browser session, no settings/CRUD saves.');
}finally{await fs.writeFile(`${dir}/live-login.json`,JSON.stringify({result,errors,writes},null,2));await browser.close();}
