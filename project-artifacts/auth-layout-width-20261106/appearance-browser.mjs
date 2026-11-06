import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
const directory=path.resolve('project-artifacts/auth-layout-width-20261106');
const browser=await chromium.launch({channel:'chrome',headless:true});
const checks=[],errors=[];let payload=null;
try {
 const page=await browser.newPage({viewport:{width:1440,height:1000},reducedMotion:'reduce'});
 page.on('pageerror',error=>errors.push(error.message));
 await page.goto('http://laravel-13-phoenix.aruna/auth/login',{waitUntil:'load'});
 await page.locator('[name="email"]').fill(process.env.PH_QA_ADMIN_EMAIL);
 await page.locator('[name="password"]').fill(process.env.PH_QA_ADMIN_PASSWORD);
 await page.locator('input[type="submit"]').click();
 await page.waitForURL(url=>!url.pathname.startsWith('/auth/'));
 await page.goto('http://laravel-13-phoenix.aruna/awesome_admin/appearance',{waitUntil:'networkidle'});
 await page.route('**/appearance/update',route=>{
  const match=route.request().postData().match(/name="auth_theme_options"\r?\n\r?\n([^\r\n]*)/);
  payload=JSON.parse(match[1]);
  return route.fulfill({status:200,contentType:'application/json',body:JSON.stringify({success:true,status:'success',message:'QA save intercepted; settings unchanged.'})});
 });
 async function openLayout(flow,theme) {
  await page.locator(`#ph-auth-tab-${flow}`).click();
  await page.locator(`[data-theme="${theme}"]`).click();
  const groupId=`ph-auth-${flow}-${theme}-group-layout`;
  const button=page.locator(`[data-bs-target="#${groupId}"]`);
  if(await button.getAttribute('aria-expanded')!=='true')await button.click();
  await page.locator(`#${groupId}.show`).waitFor();
  return page.locator(`#${groupId}`);
 }
 for(const flow of ['login','signup','forgotPassword','resetPassword']) for(const theme of ['ocean_split','verdant_split']) {
  const group=await openLayout(flow,theme),width=page.locator(`#ph-auth-${flow}-${theme}-layout_width`),card=page.locator(`#ph-auth-${flow}-${theme}-card_width`);
  assert.deepEqual(await width.locator('option').allTextContents(),['Constrained','Full width (adaptive)']);
  await width.selectOption('bounded');
  if(theme==='ocean_split')await card.selectOption('compact');
  assert.deepEqual(await group.locator('.ph-auth-setting-row > label').allTextContents(),theme==='ocean_split'?['Layout width','Card width','Panel position','Photo / form proportion']:['Layout width','Panel position']);
  await width.selectOption('fluid');
  if(theme==='ocean_split')await card.waitFor({state:'detached'});
  assert.deepEqual(await group.locator('.ph-auth-setting-row > label').allTextContents(),theme==='ocean_split'?['Layout width','Panel position','Photo / form proportion']:['Layout width','Panel position']);
  await width.selectOption('bounded');
  if(theme==='ocean_split')assert.equal(await card.inputValue(),'compact');
  await width.selectOption('fluid');
  checks.push({flow,theme,conditionalCardWidth:true,fieldOrder:true,retainedCardWidth:theme==='ocean_split'?'compact':null});
 }
 for(const theme of ['clean_peach','midnight_story']) {
  await page.locator(`[data-theme="${theme}"]`).click();
  assert.equal(await page.locator('select[id$="-layout_width"]').count(),0);
 }
 let group=await openLayout('login','ocean_split');
 const width=page.locator('#ph-auth-login-ocean_split-layout_width');
 await width.selectOption('bounded');
 await group.locator('..').screenshot({path:path.join(directory,'appearance-layout-bounded.png'),animations:'disabled'});
 await width.selectOption('fluid');
 await group.locator('..').screenshot({path:path.join(directory,'appearance-layout-fluid.png'),animations:'disabled'});
 await page.locator('button[type="submit"]').click();
 await page.locator('body > .ph-notice .ph-notice-toast.show').waitFor();
 assert.ok(payload);
 for(const flow of ['login','signup','forgotPassword','resetPassword']) {
  for(const theme of ['ocean_split','verdant_split'])assert.equal(payload[flow][theme].layout_width,'fluid');
  assert.equal(payload[flow].ocean_split.card_width,'compact');
 }
 for(const viewport of [820,390]) {
  await page.setViewportSize({width:viewport,height:1000});
  await group.scrollIntoViewIfNeeded();
  const box=await group.boundingBox();
  assert.ok(box.x>=0 && box.x+box.width<=viewport+1);
  await group.locator('..').screenshot({path:path.join(directory,`appearance-layout-${viewport}.png`),animations:'disabled'});
 }
 assert.deepEqual(errors,[]);
 console.log(`${checks.length} Appearance page/theme cases passed; field ordering, conditional card width, retained draft, Save payload, native notice and tablet/mobile controls verified. Save intercepted.`);
} finally {
 await fs.writeFile(path.join(directory,'appearance-browser-checks.json'),JSON.stringify({checks,errors,saveIntercepted:payload!==null},null,2));
 await browser.close();
}
