import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
const directory=path.resolve('project-artifacts/manage-event-table-alignment-20261106');
const browser=await chromium.launch({channel:'chrome',headless:true});
const results=[],errors=[],writes=[];
try{
 const page=await browser.newPage({viewport:{width:1440,height:1000},reducedMotion:'reduce'});
 page.on('pageerror',error=>errors.push(error.message));
 page.on('console',message=>{if(message.type()==='error')errors.push(message.text());});
 await page.goto('http://laravel-13-phoenix.aruna/auth/login');await page.locator('[name="email"]').fill(process.env.PH_QA_ADMIN_EMAIL);await page.locator('[name="password"]').fill(process.env.PH_QA_ADMIN_PASSWORD);await page.locator('input[type="submit"]').click();await page.waitForURL(url=>!url.pathname.startsWith('/auth/'));
 await page.route('**/manage_event/**',route=>{if(route.request().method()==='GET')return route.continue();writes.push(route.request().url());return route.abort();});
 await page.goto('http://laravel-13-phoenix.aruna/manage_event',{waitUntil:'networkidle'});await page.waitForFunction(()=>window.ManageEventVue3&&!ManageEventVue3.loading);
 assert.equal(await page.evaluate(()=>ManageEventVue3.total),100);assert.equal(await page.locator('[data-event-id]').count(),15);
 const ids=[];let occurrenceCount=0;
 for(let current=1;current<=7;current++){
  const response=await page.request.get(`http://laravel-13-phoenix.aruna/manage_event/listdata?page=${current}&per_page=15&sort=title_asc`,{headers:{Accept:'application/json'}});assert.equal(response.status(),200);const data=await response.json();
  assert.equal(data.total,100);assert.equal(data.total_page,7);assert.equal(data.status_counts.draft,100);
  for(const event of data.data){assert.ok(event.uri.startsWith('demo-event-table-20261106-'));assert.equal(event.publication_status,'draft');assert.ok(event.occurrence_count>=1&&event.occurrence_count<=3);assert.ok(event.next_occurrence_at);ids.push(event.id);occurrenceCount+=event.occurrence_count;}
 }
 assert.equal(new Set(ids).size,100);assert.equal(occurrenceCount,199);results.push({case:'all live pages',events:ids.length,uniqueEvents:new Set(ids).size,sessions:occurrenceCount,pages:7});
 for(const perPage of [5,10,15,25,50]){const data=await(await page.request.get(`http://laravel-13-phoenix.aruna/manage_event/listdata?per_page=${perPage}`)).json();assert.equal(data.data.length,perPage);assert.equal(data.total_page,Math.ceil(100/perPage));results.push({case:'live page size',perPage,pages:data.total_page});}
 await page.locator('.ph-article-filter-toggle').click();await page.locator('#ph-event-category').selectOption('1');await page.waitForFunction(()=>ManageEventVue3.total===20&&!ManageEventVue3.loading);results.push({case:'live category filter',total:20});
 await page.locator('#ph-event-category').selectOption('');await page.locator('#ph-event-visibility').selectOption('private');await page.waitForFunction(()=>ManageEventVue3.total===20&&!ManageEventVue3.loading);results.push({case:'live visibility filter',total:20});
 await page.evaluate(()=>ManageEventVue3.resetFilters());await page.waitForFunction(()=>ManageEventVue3.total===100&&!ManageEventVue3.loading);
 await page.locator('.ph-article-search input').fill('#100');await page.waitForFunction(()=>ManageEventVue3.total===1&&!ManageEventVue3.loading);assert.match(await page.locator('[data-event-id]').innerText(),/#100/);results.push({case:'live search',total:1});
 await page.evaluate(()=>{ManageEventVue3.showFilters=false;return ManageEventVue3.resetFilters();});await page.waitForFunction(()=>ManageEventVue3.total===100&&!ManageEventVue3.loading);await page.evaluate(()=>document.fonts.ready);
 await page.screenshot({path:path.join(directory,'live-event-desktop.png'),animations:'disabled'});
 const first=page.locator('[data-event-id]').first();await first.locator('.ph-article-title').click();assert.match(await page.locator('.ph-article-detail').innerText(),/Demo|contoh/);results.push({case:'live event detail'});
 await page.setViewportSize({width:390,height:1000});await page.waitForTimeout(150);await first.locator('.ph-article-title').click();await page.screenshot({path:path.join(directory,'live-event-mobile.png'),fullPage:false,animations:'disabled'});
 assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth),false);assert.deepEqual(errors,[]);assert.deepEqual(writes,[]);
 console.log(`${results.length} live data checks passed: 100 unique Draft Demo events, 199 sessions, 7 pages at 15/page; category/visibility/search/details verified without mutations.`);
}finally{await fs.writeFile(path.join(directory,'live-demo-checks.json'),JSON.stringify({results,errors,writes},null,2));await browser.close();}
