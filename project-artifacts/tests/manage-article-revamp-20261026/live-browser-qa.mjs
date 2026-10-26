import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {chromium} from 'playwright-core';

const origin='https://laravel-13-phoenix.aruna';
const out=path.resolve('project-artifacts/qa/manage-article-revamp-20261026');
const browser=await chromium.launch({channel:'chrome',headless:true});
try {
const context=await browser.newContext({ignoreHTTPSErrors:true,viewport:{width:1440,height:1000}});
const page=await context.newPage();
const runtimeErrors=[],vueWarnings=[],writes=[];
page.on('pageerror',error=>runtimeErrors.push(error.message));
page.on('console',message=>{if(message.type()==='warning'&&message.text().includes('[Vue warn]'))vueWarnings.push(message.text());});
page.on('request',request=>{if(request.method()!=='GET'&&!request.url().includes('/auth/'))writes.push({method:request.method(),url:request.url()});});
await page.goto(origin+'/manage_article');
if(page.url().includes('/auth/login')){
    assert.ok(process.env.PH_ARTICLE_QA_EMAIL&&process.env.PH_ARTICLE_QA_PASSWORD,'Login credentials are required in the process environment.');
    await page.getByRole('textbox',{name:'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox',{name:'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button',{name:'Log In',exact:true}).click();
}
await page.locator('tbody tr[data-article-id]').first().waitFor();
const app=page.locator('#ph-app-manage-article');
const initial=(await (await context.request.get(origin+'/manage_article/listdata')).json());
assert.equal(await app.locator('tbody tr[data-article-id]').count(),15);
assert.ok(initial.total>=1);
async function waitList(action){
    const [response]=await Promise.all([page.waitForResponse(r=>r.url().includes('/manage_article/listdata?')&&r.request().method()==='GET'),action()]);
    assert.equal(response.status(),200);
    await page.waitForFunction(()=>document.querySelector('.ph-article-board')?.getAttribute('aria-busy')==='false');
    return response.json();
}
let row=app.locator('tbody tr[data-article-id]').first();
assert.deepEqual(await row.evaluate(row=>[...row.cells].slice(0,3).map(cell=>cell.querySelector('.ph-article-expand')?'detail':cell.querySelector('input.checkids')?'checkbox':'article')),['detail','checkbox','article']);
const firstId=await row.getAttribute('data-article-id');
await row.locator('.ph-article-expand').click();
await app.locator('#ph-article-detail-'+firstId).waitFor();
await row.locator('input.checkids').check();
assert.equal(await row.locator('.ph-article-expand').getAttribute('aria-expanded'),'true');
await row.locator('.ph-article-expand').focus();await page.keyboard.press('Space');
assert.equal(await row.locator('input.checkids').isChecked(),true);
await page.keyboard.press('Enter');
assert.equal(await row.locator('.ph-article-expand').getAttribute('aria-expanded'),'true');
await app.locator('.ph-article-bulk select').selectOption('pending');
await app.getByRole('button',{name:'Terapkan',exact:true}).click();
await page.locator('#ph-article-bulk-confirm.show').waitFor();
assert.match(await page.locator('#ph-article-bulk-confirm .modal-body').innerText(),/1 artikel.*Pending/s);
await page.locator('#ph-article-bulk-confirm').getByRole('button',{name:'Cancel',exact:true}).click();
await page.locator('#ph-article-bulk-confirm.show').waitFor({state:'hidden'});
await app.locator('.ph-article-bulk').getByRole('button',{name:'Cancel',exact:true}).click();
await page.screenshot({path:path.join(out,'live-desktop-detail.png')});

const pending=await waitList(()=>app.locator('.ph-article-tab').filter({hasText:/^Pending/}).click());
assert.equal(pending.total,pending.status_counts.pending);
assert.equal(await app.locator('tbody tr[data-article-id]').count(),Math.min(15,pending.total));
await waitList(()=>app.locator('.ph-article-tab').filter({hasText:/^All/}).click());
const empty=await waitList(()=>app.getByRole('searchbox').fill('No-Matching-Article-Revamp-QA'));
assert.equal(empty.total,0);await app.getByRole('heading',{name:'Artikel tidak ditemukan'}).waitFor();
await waitList(()=>app.getByRole('button',{name:'Hapus pencarian dan filter',exact:true}).click());
await app.getByRole('button',{name:'Filter',exact:true}).click();
const categories=await app.locator('#ph-article-category option').evaluateAll(options=>options.filter(option=>option.value).map(option=>({id:option.value,label:option.textContent})));
const chosen=categories.find(category=>category.label.includes('Load Test Technology'))||categories[0];
const categoryResult=await waitList(()=>app.locator('#ph-article-category').selectOption(chosen.id));
assert.ok(categoryResult.data.every(info=>String(info.category_id)===chosen.id));
const scheduledResult=await waitList(()=>app.locator('#ph-article-scheduled').selectOption('false'));
assert.ok(scheduledResult.data.every(info=>info.scheduled==='false'||info.scheduled===false));
const searched=await waitList(()=>app.getByRole('searchbox').fill('Load Test'));
assert.ok(searched.data.every(info=>String(info.category_id)===chosen.id&&info.title_full.includes('Load Test')));
const sorted=await waitList(()=>app.getByRole('combobox',{name:'Urutkan artikel'}).selectOption('title_desc'));
assert.ok(sorted.data.every(info=>String(info.category_id)===chosen.id));
const paged=await waitList(()=>app.locator('.ph-article-page-info select').selectOption('5'));
assert.equal(paged.limit,5);assert.equal(await app.locator('tbody tr[data-article-id]').count(),Math.min(5,paged.total));
if(paged.total_page>1){
    const page2=await waitList(()=>app.locator('.ph-article-footer .pagination').getByText('2',{exact:true}).click());
    assert.equal(page2.current_page,2);assert.ok(page2.data.every(info=>String(info.category_id)===chosen.id));
}
await waitList(()=>app.getByRole('button',{name:'Hapus filter',exact:true}).click());
await app.getByRole('button',{name:'Filter',exact:true}).click();
row=app.locator('tbody tr[data-article-id]').first();
await row.locator('.ph-article-expand').click();
const measurements=[];
for(const theme of ['light','dark']){
    await page.evaluate(theme=>document.documentElement.setAttribute('data-bs-theme',theme),theme);
    for(const width of [320,390,768,1024,1440]){
        await page.setViewportSize({width,height:1000});
        await page.evaluate(()=>new Promise(resolve=>requestAnimationFrame(()=>requestAnimationFrame(resolve))));
        await page.waitForTimeout(300);
        const metrics=await app.evaluate(el=>{
            const row=el.querySelector('tr[data-article-id]');
            const detail=row.querySelector('.ph-article-expand').getBoundingClientRect();
            const checkbox=row.querySelector('.ph-article-check-target').getBoundingClientRect();
            const board=el.querySelector('.ph-article-board').getBoundingClientRect();
            const visible=[...row.cells].filter(cell=>getComputedStyle(cell).display!=='none');
            const action=row.querySelector('.ph-article-row-actions').getBoundingClientRect();
            return {viewport:innerWidth,bodyOverflow:document.documentElement.scrollWidth>innerWidth+1,boardWidth:board.width,gap:checkbox.left-detail.right,visibleColumns:visible.length,detailSpan:el.querySelector('.ph-dtr-child-row td')?.colSpan,actionFits:action.right<=board.right+1,unrendered:el.textContent.includes('@{{')};
        });
        measurements.push({theme,width,...metrics});
        assert.equal(metrics.bodyOverflow,false,JSON.stringify(metrics));assert.ok(Math.abs(metrics.gap-24)<1,JSON.stringify(metrics));assert.equal(metrics.visibleColumns,metrics.detailSpan);assert.equal(metrics.actionFits,true);assert.equal(metrics.unrendered,false);
        if(width===390||width===1440)await page.screenshot({path:path.join(out,`live-${width}-${theme}.png`)});
    }
}
await page.setViewportSize({width:390,height:900});
await page.evaluate(()=>document.documentElement.setAttribute('data-bs-theme','light'));
row=app.locator('tbody tr[data-article-id]').first();
await row.locator('[data-bs-toggle="dropdown"]').click();
const deleteButton=row.getByRole('button',{name:'Delete',exact:true});
await deleteButton.waitFor();await deleteButton.click();
await page.locator('#ph-submit-data-article-modalDelete.show').waitFor();
await page.locator('#ph-submit-data-article-modalDelete').getByRole('button',{name:'No, keep it',exact:true}).click();
await page.locator('#ph-submit-data-article-modalDelete.show').waitFor({state:'hidden'});
await app.getByRole('button',{name:'Menu pengelolaan artikel'}).click();
await app.getByRole('button',{name:'Category List',exact:true}).click();
await page.locator('#ph-submit-data-category-modalRead.show').waitFor();
assert.ok(await page.locator('#ph-submit-data-category-modalRead').isVisible());
await page.locator('#ph-submit-data-category-modalRead [data-bs-dismiss="modal"]').first().click();
await page.locator('#ph-submit-data-category-modalRead.show').waitFor({state:'hidden'});
await page.goto(origin+'/manage_article/add');await page.locator('#ph-app-manage-article form').waitFor();
assert.ok(await page.locator('input[name="thumbnail_source"]').count());
await page.goto(origin+'/manage_article/edit/'+firstId);await page.locator('#ph-app-manage-article form').waitFor();
await page.locator('#ph-app-manage-article input[name="title"]').waitFor();
await page.goto(origin+'/manage_article');await page.locator('tbody tr[data-article-id]').first().waitFor();
await page.route('**/manage_article/listdata?*',route=>route.fulfill({status:503,contentType:'application/json',body:JSON.stringify({message:'QA: koneksi sementara gagal'})}),{times:1});
await app.getByRole('combobox',{name:'Urutkan artikel'}).selectOption('oldest');
await app.getByRole('alert').filter({hasText:'QA: koneksi sementara gagal'}).waitFor();
await waitList(()=>app.getByRole('button',{name:'Coba lagi',exact:true}).click());
await app.locator('tbody tr[data-article-id]').first().waitFor();
const touchContext=await browser.newContext({ignoreHTTPSErrors:true,viewport:{width:320,height:900},isMobile:true,hasTouch:true,storageState:await context.storageState()});
const touchPage=await touchContext.newPage();touchPage.on('pageerror',error=>runtimeErrors.push(error.message));
await touchPage.goto(origin+'/manage_article');
const touchRow=touchPage.locator('tbody tr[data-article-id]').first();await touchRow.waitFor();
await touchRow.locator('.ph-article-expand').tap();await touchRow.locator('input.checkids').tap();
assert.equal(await touchRow.locator('.ph-article-expand').getAttribute('aria-expanded'),'true');assert.equal(await touchRow.locator('input.checkids').isChecked(),true);
const touchMetrics=await touchRow.evaluate(row=>{const detail=row.querySelector('.ph-article-expand').getBoundingClientRect();const checkbox=row.querySelector('.ph-article-check-target').getBoundingClientRect();return {gap:checkbox.left-detail.right,detailWidth:detail.width,detailHeight:detail.height,checkboxWidth:checkbox.width,checkboxHeight:checkbox.height,bodyOverflow:document.documentElement.scrollWidth>innerWidth+1};});
assert.ok(Math.abs(touchMetrics.gap-24)<1,JSON.stringify(touchMetrics));assert.ok(touchMetrics.detailWidth>=44&&touchMetrics.detailHeight>=44);assert.ok(touchMetrics.checkboxWidth>=44&&touchMetrics.checkboxHeight>=44);assert.equal(touchMetrics.bodyOverflow,false);
await touchPage.screenshot({path:path.join(out,'live-320-touch.png')});await touchContext.close();
assert.deepEqual(runtimeErrors,[]);assert.deepEqual(vueWarnings,[]);assert.deepEqual(writes,[]);
await fs.writeFile(path.join(out,'live-verification.json'),JSON.stringify({checkedAt:new Date().toISOString(),initialTotal:initial.total,initialStatusCounts:initial.status_counts,measurements,touchMetrics,runtimeErrors,vueWarnings,writes,functional:'detail/checkbox/keyboard, cancel bulk confirmation, empty search, combined search/category/schedule/sort/pagination/page size, cancel delete, category modal, add/edit form smoke, retry after simulated network failure, touch 44px controls and 24px gap'},null,2));
console.log(JSON.stringify({total:initial.total,responsiveCases:measurements.length,runtimeErrors,vueWarnings,writes},null,2));
} finally {
    await browser.close();
}
