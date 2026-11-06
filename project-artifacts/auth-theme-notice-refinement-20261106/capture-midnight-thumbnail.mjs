import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
const browser=await chromium.launch({channel:'chrome',headless:true});
try{
 const page=await browser.newPage({viewport:{width:1440,height:960},reducedMotion:'reduce'});
 await page.goto('http://laravel-13-phoenix.aruna/__auth_notice_qa_20261106/midnight_story-login.html',{waitUntil:'networkidle'});
 await page.evaluate(()=>document.fonts.ready);
 const image=await page.locator('.ph-auth-surface').screenshot({animations:'disabled'});
 await fs.writeFile(path.join(process.cwd(),'public/assets/images/auth/themes/midnight_story.png'),image);
 await fs.writeFile(path.join(process.cwd(),'project-artifacts/auth-theme-notice-refinement-20261106/screenshots/midnight-final-thumbnail.png'),image);
 console.log('Midnight Story thumbnail refreshed from the actual rendered layout and loaded fonts.');
}finally{await browser.close();}
