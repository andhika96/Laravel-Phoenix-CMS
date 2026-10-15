import {chromium} from 'playwright-core';
import {readFile,writeFile} from 'node:fs/promises';
import assert from 'node:assert/strict';
const dir=new URL('./',import.meta.url);
const browser=await chromium.launch({channel:'chrome',headless:true});
const page=await browser.newPage({viewport:{width:1440,height:1000},ignoreHTTPSErrors:true});
const errors=[];page.on('pageerror',e=>errors.push(e.message));
let liveResult;
try{await page.goto('https://laravel-13-phoenix.aruna/awesome_admin/header-navigation',{timeout:10000});liveResult={url:page.url(),headerEditorPresent:await page.locator('#headerNavigationEditor').count()};}catch(e){liveResult={unavailable:e.message.split('\n')[0]};}
errors.length=0;
await page.goto(new URL('header-navigation.html',dir).href);
await page.evaluate(()=>document.fonts.ready);
const stage=page.locator('#deviceStage');
for(const device of ['desktop','tablet','mobile']){
await page.locator('[data-preview-device="'+device+'"]').click();await stage.hover();const outer=await page.evaluate(()=>scrollY);await page.mouse.wheel(0,350);await page.waitForTimeout(180);assert(await stage.evaluate(e=>e.scrollTop>0),device+' wheel scroll');assert.equal(await page.evaluate(()=>scrollY),outer,device+' outer page fixed');
await stage.evaluate(e=>e.scrollTop=e.scrollHeight);await page.waitForTimeout(60);assert(await stage.evaluate(e=>Math.abs(e.scrollHeight-e.clientHeight-e.scrollTop)<2),device+' reaches bottom');
await page.locator('#resetPreview').click();await page.waitForTimeout(650);assert.equal(await stage.evaluate(e=>e.scrollTop),0,device+' reset');
}
await page.locator('[data-preview-device="desktop"]').click();
await page.locator('[data-inspector-tab="advanced"]').click();await page.locator('[data-settings-category="Behavior"]').click();await page.locator('[data-control="headerBehavior"] [data-value="sticky"]').click();await page.locator('#animateScroll').check();await page.locator('#transparentStart').check();await page.locator('[data-control="transparentColorMode"] [data-value="custom"]').click();
await page.locator('#previewFrame').scrollIntoViewIfNeeded();await stage.hover();await page.mouse.wheel(0,450);await page.waitForTimeout(180);assert(await page.locator('#siteHeader').evaluate(e=>e.classList.contains('is-scrolled')));assert.equal(await page.locator('#previewCondition').inputValue(),'50');
await page.locator('#previewCondition').selectOption('0');await page.waitForTimeout(100);assert.equal(await stage.evaluate(e=>e.scrollTop),0);
const manifest=JSON.parse(await readFile(new URL('cms-parity.json',dir),'utf8'));
const reached=[];
for(const panel of ['layout','style','advanced']){
await page.locator('[data-inspector-tab="'+panel+'"]').click();const buttons=page.locator('[data-inspector-panel="'+panel+'"] [data-settings-category]');
for(let i=0;i<await buttons.count();i++){await buttons.nth(i).click();if(await page.locator('#leafDirectionControl').isVisible()===false&&await page.locator('[data-control="linkShape"]').isVisible())await page.locator('[data-control="linkShape"] [data-value="leaf"]').click();
for(const id of manifest.controlIds){const el=page.locator('#'+id);if(await el.isVisible())reached.push(id);}}
}
const unique=[...new Set(reached)];assert.deepEqual(manifest.controlIds.filter(id=>id!=='scrollSim'&&!unique.includes(id)),[],'Every CMS field reachable via actual navigation');
await page.locator('.show-all-settings').click();for(const panel of ['layout','style','advanced']){await page.locator('[data-inspector-tab="'+panel+'"]').click();assert.equal(await page.locator('[data-inspector-panel="'+panel+'"] details:not([open])').count(),0);}
assert.deepEqual(errors,[]);
await writeFile(new URL('qa/scroll-access-result.json',dir),JSON.stringify({result:'passed',liveCMS:liveResult,visibleCMSFields:unique.length,hiddenPreviewRangeReplacedByNativeScroll:true,checks:['wheel desktop/tablet/mobile','outer page does not scroll','reach true bottom','reset actual scroll','sticky reacts to native scrolling','condition sync','every setting reachable through real category buttons','show all groups'],errors},null,2));
await browser.close();console.log(JSON.stringify({result:'passed',liveCMS:liveResult,reachable:unique.length}));
