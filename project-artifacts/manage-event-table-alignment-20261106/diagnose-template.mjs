import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
const browser=await chromium.launch({channel:'chrome',headless:true});
try{
 const page=await browser.newPage();
 await page.goto('http://laravel-13-phoenix.aruna/auth/login');
 await page.locator('[name="email"]').fill(process.env.PH_QA_ADMIN_EMAIL);await page.locator('[name="password"]').fill(process.env.PH_QA_ADMIN_PASSWORD);await page.locator('input[type="submit"]').click();await page.waitForURL(url=>!url.pathname.startsWith('/auth/'));
 await page.addInitScript(()=>{
  const original=window.Function;
  const capture=(args,construct)=>{if(String(args.at(-1)).includes('ph-event-bulk-confirm'))window.__eventRenderCode=args.at(-1);try{return construct?Reflect.construct(original,args):Reflect.apply(original,window,args);}catch(error){window.__badRenderCode=args.at(-1);throw error;}};
  window.Function=new Proxy(original,{construct(target,args){return capture(args,true);},apply(target,receiver,args){return capture(args,false);}});
 });
 page.on('pageerror',error=>console.log(error.stack));
 await page.goto('http://laravel-13-phoenix.aruna/manage_event',{waitUntil:'networkidle'});
 const generated=await page.evaluate(()=>{
  let generated='';const original=window.Function;
  window.Function=new Proxy(original,{construct(target,args){generated=args.at(-1);return Reflect.construct(target,args);}});
  try{Vue.compile(document.getElementById('ph-app-manage-event').innerHTML);}catch(error){console.log(error.message);}finally{window.Function=original;}
  return (window.__badRenderCode||window.__eventRenderCode||generated).replace(/"value": "[a-zA-Z0-9]{40}"/g,'"value": "REDACTED"');
 });
 await fs.writeFile('project-artifacts/manage-event-table-alignment-20261106/template-render-function.js',generated);
}finally{await browser.close();}
