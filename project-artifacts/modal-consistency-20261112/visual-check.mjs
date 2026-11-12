import assert from 'node:assert/strict';
import { chromium } from 'playwright-core';
import { writeFile, mkdir } from 'node:fs/promises';
import path from 'node:path';
const out=path.dirname(new URL(import.meta.url).pathname.replace(/^\/(\w:)/,'$1'));
const browser=await chromium.launch({executablePath:'C:/Program Files/Google/Chrome/Application/chrome.exe',headless:true});
const context=await browser.newContext({ignoreHTTPSErrors:true});
const page=await context.newPage();
page.setDefaultTimeout(10000);
const errors=[], rows=[], failures=[];
page.on('pageerror',e=>errors.push({url:page.url(),message:e.message}));
page.on('dialog',dialog=>dialog.dismiss());
const base='https://laravel-13-phoenix.aruna';
await page.goto(base+'/login',{waitUntil:'domcontentloaded'});
await page.locator('input[name=email]').fill(process.env.CMS_MODAL_EMAIL);
await page.locator('input[name=password]').fill(process.env.CMS_MODAL_PASSWORD);
await page.locator('[type=submit]').click();
await page.waitForURL(url=>!url.pathname.includes('login'),{waitUntil:'domcontentloaded'});
// All application writes are blocked after authentication; checks only open and dismiss UI.
await page.route('**/*',route=>['POST','PUT','PATCH','DELETE'].includes(route.request().method()) ? route.abort() : route.continue());
await mkdir(path.join(out,'screenshots'),{recursive:true});
let samples=[
 ['/awesome_admin/user','modalAddNewUser'],
 ['/awesome_admin/smtp','modalAddNewSMTP'],
 ['/awesome_admin/smtp','modalDeleteSMTP'],
 ['/awesome_admin/menu/be/categorymenu','modalDeleteCategoryMenu'],
 ['/awesome_admin/menu/fe/categorymenu','modalDeleteCategoryMenu'],
 ['/awesome_admin/menu/fe/parentmenu','modalDropdownParentMenu'],
 ['/awesome_admin/appearance','phAuthImagePreview'],
 ['/awesome_admin/themes','themeManagerPreviewModal'],
 ['/manage_event','eventDeleteModal'],
 ['/manage_event','eventCategoryDeleteModal'],
 ['/manage_event/add','eventOccurrenceDeleteModal'],
 ['/manage_event/add','eventOccurrenceCancelModal'],
 ['/awesome_admin/header-navigation','headerEditorExitDialog'],
 ['/manage_article/templates','modalArticleTemplateOptions'],
 ['/manage_article/templates','discardDialog'],
 ['/workflow/create','iconDialog'],
];
if(process.argv[2]) samples=samples.filter(([,id])=>process.argv[2].split(',').includes(id));
try {
 for(const width of [1440,390]) for(const mode of ['light','dark']) {
  await page.setViewportSize({width,height:width===390?844:1000});
  await page.evaluate(mode=>localStorage.setItem('theme',mode),mode);
  for(const [route,id] of samples) {
   try {
    await page.goto(base+route,{waitUntil:'domcontentloaded'});
    await page.waitForTimeout(600);
    await page.waitForFunction(()=>!!window.bootstrap);
    if(route==='/manage_article/templates') {
      await page.waitForFunction(()=>!!window.ManageArticleTemplateVue3);
      await page.locator('#article-template-options-trigger').click();
      await page.waitForTimeout(400);
      if(id==='discardDialog') {
        await page.evaluate(()=>{
          const app=window.ManageArticleTemplateVue3;
          app.optionsModal.value.header.title.text+=' QC';
        });
        await page.waitForTimeout(100);
        await page.evaluate(()=>window.ManageArticleTemplateVue3.requestCloseTemplateOptions());
      }
    } else if(id==='iconDialog') {
      await page.waitForFunction(()=>!!window.WorkflowEditorVue);
      await page.evaluate(async()=>{
        const app=window.WorkflowEditorVue;
        app.addNode('icon');
        await app.openIconPicker(app.selectedNode);
      });
    } else {
      await page.evaluate(id=>{
        const el=document.getElementById(id);
        if(el.tagName==='DIALOG') el.showModal();
        else bootstrap.Modal.getOrCreateInstance(el).show();
      },id);
    }
    await page.waitForTimeout(650);
    const selector=id==='discardDialog'?'.article-template-discard-dialog':id==='iconDialog'?'.workflow-icon-dialog':'#'+id;
    const modal=page.locator(selector);
    const metric=await modal.evaluate(el=>{
      const content=el.querySelector('.modal-content');
      const header=content?.querySelector('.modal-header');
      const footer=content?.querySelector('.modal-footer');
      const title=content?.querySelector('.modal-title');
      const style=getComputedStyle(content),rect=content.getBoundingClientRect();
      return {radius:style.borderRadius,background:style.backgroundColor,color:style.color,width:rect.width,x:rect.x,right:rect.right,height:rect.height,headerBorder:header?getComputedStyle(header).borderBottomWidth:null,footerBorder:footer?getComputedStyle(footer).borderTopWidth:null,titleWeight:title?getComputedStyle(title).fontWeight:null,titleSize:title?getComputedStyle(title).fontSize:null,open:el.tagName==='DIALOG'?el.open:el.classList.contains('show'),focused:el.contains(document.activeElement),scrollWidth:content.scrollWidth};
    });
    rows.push({route,id,viewportWidth:width,mode,...metric});
    assert.equal(metric.open,true);
    assert.equal(metric.radius,'16px');
    assert.notEqual(metric.background,'rgba(0, 0, 0, 0)');
    if(metric.headerBorder!==null) assert.equal(metric.headerBorder,'0px');
    if(metric.footerBorder!==null) assert.equal(metric.footerBorder,'0px');
    if(metric.titleWeight!==null) assert.equal(metric.titleWeight,'700');
    assert.ok(metric.width<=width && metric.x>=-1 && metric.right<=width+1,`horizontal overflow: ${JSON.stringify(metric)}`);
    if(['modalAddNewSMTP','eventDeleteModal','headerEditorExitDialog','discardDialog','iconDialog'].includes(id)) await modal.locator('.modal-content').first().screenshot({path:path.join(out,'screenshots',`${id}-${width}-${mode}.png`)});
    if(id==='discardDialog') {
      await modal.getByRole('button',{name:'Keep editing',exact:true}).click();
      assert.equal(await modal.evaluate(el=>el.open),false);
      assert.equal(await page.locator('#modalArticleTemplateOptions').evaluate(el=>el.classList.contains('show')),true);
      await page.evaluate(()=>window.ManageArticleTemplateVue3.discardTemplateOptions());
    } else if(id==='iconDialog') {
      await modal.getByRole('button',{name:'Cancel',exact:true}).click();
      await page.evaluate(()=>{window.WorkflowEditorVue.dirty=false;});
      assert.equal(await page.locator('.workflow-icon-dialog').count(),0);
    } else if(id==='headerEditorExitDialog') {
      await modal.getByRole('button',{name:'Keep editing',exact:true}).click();
      assert.equal(await modal.evaluate(el=>el.open),false);
    } else if(id!=='modalArticleTemplateOptions') {
      await page.evaluate(id=>bootstrap.Modal.getInstance(document.getElementById(id)).hide(),id);
      await page.waitForTimeout(500);
      assert.equal(await modal.evaluate(el=>el.classList.contains('show')),false);
    }
    console.log(`${width} ${mode} ${id}: PASS`);
   } catch(error) {
    failures.push({route,id,width,mode,error:error.message});
    await page.screenshot({path:path.join(out,'screenshots',`FAILED-${id}-${width}-${mode}.png`)});
    console.log(`${width} ${mode} ${id}: FAIL ${error.message.slice(0,250)}`);
   }
  }
 }
} finally {
 await writeFile(path.join(out,process.argv[2]?'visual-results-selected.json':'visual-results.json'),JSON.stringify({rows,errors,failures},null,2));
 await browser.close();
}
console.log(JSON.stringify({cases:rows.length,failures,errors},null,2));
assert.equal(failures.length,0);
assert.equal(errors.length,0);
