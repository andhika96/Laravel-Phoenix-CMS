import assert from 'node:assert/strict';
import {readFile,writeFile} from 'node:fs/promises';
import {fileURLToPath} from 'node:url';
const out=new URL('./qa/',import.meta.url);
export async function verify(page){
const url='https://laravel-13-phoenix.aruna/awesome_admin/header-navigation';
const errors=[];page.on('pageerror',e=>errors.push(e.message));page.on('dialog',dialog=>dialog.type()==='beforeunload'?dialog.accept():dialog.dismiss());
await page.goto(url);await page.locator('body.header-editor-open').waitFor();await page.evaluate(()=>document.fonts.ready);
await page.waitForFunction(()=>document.querySelectorAll('.header-menu a').length>0);
const previous=JSON.parse(await readFile(new URL('../../mockups/20261015-header-navigation-revamp/qa/authenticated-20261015/comparison.json',import.meta.url),'utf8'));
for(const field of previous.live.fields){assert.equal(await page.locator('#'+field.id).count(),1,field.id);if(field.options)assert.deepEqual(await page.locator('#'+field.id+' option').evaluateAll(a=>a.map(e=>({value:e.value,text:e.textContent.trim()}))),field.options);}
for(const group of previous.live.groups)assert.deepEqual(await page.locator('[data-control="'+group.name+'"] [data-value]').evaluateAll(a=>a.map(b=>b.dataset.value)),group.values);
for(const id of previous.live.linked)assert.equal(await page.locator('#'+id).count(),1);
async function geometry(width,height){await page.setViewportSize({width,height});await page.waitForTimeout(100);const g=await page.evaluate(()=>{const b=s=>{const r=document.querySelector(s).getBoundingClientRect();return {x:r.x,y:r.y,w:r.width,h:r.height,right:r.right,bottom:r.bottom}};return {root:b('#headerNavigationEditor'),left:b('.mock-control-pane'),right:b('.mock-preview-pane'),bodyScroll:document.documentElement.scrollWidth>innerWidth}});assert(Math.abs(g.root.w-width)<2);assert(Math.abs(g.root.h-height)<2);assert(!g.bodyScroll);if(width>700){assert(g.left.x<g.right.x);assert(g.left.right<=g.right.x+2);assert(g.right.bottom<=height+2);}await page.screenshot({path:fileURLToPath(new URL('layout-'+width+'.png',out)),fullPage:true});return g;}
const dimensions=[];for(const size of [[1440,1000],[1024,768],[430,932]])dimensions.push(await geometry(...size));await page.setViewportSize({width:1440,height:1000});
const stage=page.locator('#deviceStage');await stage.hover();await page.mouse.wheel(0,400);await page.waitForTimeout(150);assert(await stage.evaluate(e=>e.scrollTop>0),'preview wheel');await page.locator('#resetPreview').click();await page.waitForTimeout(650);assert.equal(await stage.evaluate(e=>e.scrollTop),0);
await page.locator('[data-preview-device="mobile"]').click();await page.locator('[data-header-editor-exit]').first().click();await page.waitForURL('**/awesome_admin');
await page.goto(url);await page.locator('body.header-editor-open').waitFor();
await page.locator('#headerHeight').evaluate(e=>e.closest('details').open=true);await page.locator('#headerHeight').fill('91');await page.locator('[data-header-editor-exit]').last().click();assert(await page.locator('#headerEditorExitDialog').isVisible());await page.locator('#headerEditorKeepEditing').click();assert.equal(await page.locator('#headerHeight').inputValue(),'91');
// Exercise client success/failure without writing production settings.
await page.route('**/awesome_admin/header-navigation',route=>route.request().method()==='POST'?route.fulfill({status:500,contentType:'application/json',body:JSON.stringify({success:false,message:'QA simulated failure'})}):route.continue());
await page.locator('#saveHeaderNavigation').click();await page.waitForFunction(()=>document.querySelector('#headerNavigationSaveStatus').classList.contains('is-failed'));await page.locator('[data-header-editor-exit]').first().click();assert(await page.locator('#headerEditorExitDialog').isVisible());await page.locator('#headerEditorKeepEditing').click();
await page.unroute('**/awesome_admin/header-navigation');await page.route('**/awesome_admin/header-navigation',route=>route.request().method()==='POST'?route.fulfill({status:200,contentType:'application/json',body:JSON.stringify({success:true})}):route.continue());
await page.locator('#saveHeaderNavigation').click();await page.waitForFunction(()=>document.querySelector('#headerNavigationSaveStatus').classList.contains('is-success'));await page.locator('[data-header-editor-exit]').first().click();await page.waitForURL('**/awesome_admin');
await page.unroute('**/awesome_admin/header-navigation');await page.goto(url);await page.locator('body.header-editor-open').waitFor();await page.locator('#headerNavigationActive').uncheck();await page.locator('[data-header-editor-exit]').last().click();assert(await page.locator('#headerEditorExitDialog').isVisible());await page.locator('#headerEditorDiscard').click();await page.waitForURL('**/awesome_admin');
assert.deepEqual(errors,[]);
await writeFile(new URL('verification.json',out),JSON.stringify({result:'passed',liveControlsCompared:previous.live.fields.length,groupsCompared:previous.live.groups.length,dimensions,checks:['fullscreen viewport','left settings right preview','all source controls preserved','preview wheel and reset','device switch does not mark dirty','cancel prompts for unsaved changes','keep editing preserves edits','failed save remains dirty','successful save clears dirty','discard exits to Awesome Admin'],saveTesting:'Browser responses intercepted; no production settings POST reached server. Backend verified separately by transactional Laravel tests.',errors},null,2));
console.log('PASS: authenticated fullscreen layout, all CMS controls, scrolling and save/cancel lifecycle');
}
