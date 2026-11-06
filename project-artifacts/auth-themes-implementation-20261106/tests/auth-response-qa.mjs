import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';

const artifact=path.join(process.cwd(),'project-artifacts/auth-themes-implementation-20261106');
const baseUrl=process.env.PH_AUTH_QA_URL || 'http://127.0.0.1:8976';
const browser=await chromium.launch({channel:'chrome',headless:true});
const page=await browser.newPage({viewport:{width:390,height:844}});
const errors=[],results=[];
page.on('pageerror',e=>errors.push(e.message));
let responseMode='success';
await page.route('**/auth/**',route=>{
	if(!['xhr','fetch'].includes(route.request().resourceType())) return route.continue();
    if(route.request().method()==='OPTIONS') return route.fulfill({status:204,headers:{'Access-Control-Allow-Origin':'*','Access-Control-Allow-Headers':'*'}});
    if(responseMode==='network') return route.abort('failed');
    return route.fulfill({status:200,contentType:'application/json',headers:{'Access-Control-Allow-Origin':'*'},body:JSON.stringify({status:responseMode,message:'QA response: reset instructions available.'})});
});
try {
    for(const theme of ['clean_peach','ocean_split','midnight_story','verdant_split']) {
        for(const flow of ['forgotpassword','login','signup','resetpassword']) {
            for(const mode of flow==='forgotpassword' ? ['success','failed','network'] : ['failed','network']) {
                responseMode=mode;
                await page.goto(`${baseUrl}/${theme}-${flow}.html`,{waitUntil:'load'});
                const label='Continue "<img src=x onerror=alert(1)>"';
                await page.locator('input[type="submit"]').evaluate((e,value)=>e.value=value,label);
                const email=page.locator('input[name="email"],input[name="account[email]"]');
                const password=page.locator('input[name="password"],input[name="account[password]"]');
                if(await email.count()) await email.fill('fixture@example.test');
                if(await password.count()) await password.fill('fixture-password');
                if(flow==='signup') {
                    await page.locator('[name="account[fullname]"]').fill('QA fixture');
                    await page.locator('[name="account[username]"]').fill('qa-fixture');
                }
                if(flow==='resetpassword') await page.locator('[name="password_confirmation"]').fill('fixture-password');
                await page.locator('input[type="submit"]').click();
                await page.locator('.ph-notice-toast.show').waitFor({timeout:2000});
                await page.locator('input[type="submit"]').waitFor({timeout:2000});
                assert.equal(await page.locator('input[type="submit"]').inputValue(),label);
                assert.equal(await page.locator('img[src="x"]').count(),0);
                results.push({theme,flow,mode,toast:true,labelPreserved:true});
            }
        }
    }
    assert.equal(errors.length,0,JSON.stringify(errors));
    console.log(`${results.length} response/label checks passed.`);
} finally {
    await fs.writeFile(path.join(artifact,'auth-response-qa-latest.json'),JSON.stringify({results,errors},null,2));
    await browser.close();
}
