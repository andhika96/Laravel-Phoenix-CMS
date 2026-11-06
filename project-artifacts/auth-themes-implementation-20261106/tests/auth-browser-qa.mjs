import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';

const artifact = path.join(process.cwd(),'project-artifacts/auth-themes-implementation-20261106');
const baseUrl = process.env.PH_AUTH_QA_URL || 'http://127.0.0.1:8976';
const manifest = JSON.parse(await fs.readFile(path.join(artifact,'rendered_20261106_110641/manifest.json'),'utf8')).filter(file=>!process.env.PH_AUTH_QA_FILTER||file.startsWith(process.env.PH_AUTH_QA_FILTER));
const browser = await chromium.launch({channel:'chrome',headless:true});
const context = await browser.newContext();
const page = await context.newPage();
const results = [], errors = [];
page.on('pageerror', error=>errors.push({url:page.url(),message:error.message}));
for (const file of manifest) {
    const modern = /^(clean_peach|ocean_split|midnight_story|verdant_split)/.test(file);
    for (const width of [320,390,768,820,1280]) {
        await page.setViewportSize({width,height:900});
        await page.goto(`${baseUrl}/${file}`,{waitUntil:'load'});
        await page.evaluate(()=>document.fonts.ready);
        const metrics = await page.evaluate(()=>({width:innerWidth,documentWidth:document.documentElement.scrollWidth,forms:document.querySelectorAll('form').length,heading:document.querySelector('h1')?.textContent.trim(),mobilePanel:document.querySelector('.ph-auth-panel') ? getComputedStyle(document.querySelector('.ph-auth-panel')).display : null}));
        results.push({file,width,modern,...metrics});
        assert.equal(metrics.documentWidth,width,`${file} overflows at ${width}`);
        assert.equal(metrics.forms,1,`${file} missing form at ${width}`);
        if (modern && width === 320 && !file.includes('forgotpassword')) {
            const toggle = page.locator('.ph-auth-password-toggle').first();
            await toggle.click();
            assert.equal(await page.locator('#ph-auth-password').getAttribute('type'),'text');
            await toggle.click();
            assert.equal(await page.locator('#ph-auth-password').getAttribute('type'),'password');
        }
        if (modern && (width === 390 || width === 820) && file.endsWith('-login.html')) {
            await page.screenshot({path:path.join(artifact,'screenshots',`${file.replace('.html','')}-${width}.png`),fullPage:true});
        }
    }
}
await browser.close();
await fs.writeFile(path.join(artifact,'auth-browser-qa.json'),JSON.stringify({results,errors},null,2));
assert.equal(errors.length,0,JSON.stringify(errors));
console.log(`${results.length} viewport/page checks passed across all 32 views; no horizontal overflow, missing forms, or browser errors.`);
