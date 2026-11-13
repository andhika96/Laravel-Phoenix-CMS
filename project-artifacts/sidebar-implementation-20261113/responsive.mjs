import assert from 'node:assert/strict';
import {existsSync,writeFileSync} from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';
import {chromium} from 'playwright-core';
const directory=path.dirname(fileURLToPath(import.meta.url));
const publicRoot=path.resolve(directory,'../../public');
const browser=await chromium.launch({channel:'chrome',headless:true});
const results=[];
try {
  for(const theme of ['arunika_lucent','arunika_aurora','arunika_prism','arunika_equinox','default','calm_green']) {
    const context=await browser.newContext({viewport:{width:390,height:844}});
    context.setDefaultTimeout(6000);
    const page=await context.newPage();
    await page.route(/\/(assets|storage)\//,route=>{
      const asset=path.resolve(publicRoot,'.'+decodeURIComponent(new URL(route.request().url()).pathname));
      return asset.startsWith(publicRoot+path.sep)&&existsSync(asset)?route.fulfill({path:asset}):route.fulfill({status:404,body:'Not found'});
    });
    await page.route(/^https?:\/\/(?!laravel-13-phoenix\.aruna)/,route=>route.abort());
    await page.route('**/account?sidebar-qa=*',route=>route.fulfill({path:path.join(directory,'rendered',theme+'.html'),contentType:'text/html'}));
    await page.goto('http://laravel-13-phoenix.aruna/account?sidebar-qa=mobile',{waitUntil:'domcontentloaded'});
    await page.addStyleTag({content:'*,*::before,*::after {transition:none!important;animation:none!important}'});
    const modern=theme.startsWith('arunika_');
    if(modern) {
      await page.evaluate(()=>window.PhoenixMobileNavigation.open());
      await page.waitForFunction(()=>document.querySelector('#sidebar').classList.contains('ph-expanded'));
    } else {
      await page.evaluate(()=>bootstrap.Offcanvas.getOrCreateInstance(document.querySelector('#offcanvasExample')).show());
      await page.waitForFunction(()=>document.querySelector('#offcanvasExample').classList.contains('show'));
    }
    const category=page.locator(modern?'#sidebar .ph-nav-category':'.arv7-menu-mobile .arv7-title').filter({hasText:'All Menus'}).first();
    const checks=[{name:'mobile category is visible',pass:await category.isVisible()},{name:'mobile page has no horizontal overflow',pass:await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)}];
    await page.screenshot({path:path.join(directory,`mobile-${theme}.png`)});
    if(modern) {
      await page.evaluate(()=>window.PhoenixMobileNavigation.close());
      await page.setViewportSize({width:1024,height:768});
      await page.reload({waitUntil:'domcontentloaded'});
      await page.evaluate(()=>document.querySelector('#sidebar').classList.add('ph-expanded'));
      const second=await page.evaluate(()=>{
        const category=document.querySelector('.ph-nav-category');
        const clone=category.cloneNode(true);
        clone.querySelector('.ph-nav-category-label').textContent='Kategori contoh kedua';
        clone.id='qa-second-category';
        const group=document.createElement('div');
        group.className='ph-list-group-wrapper';
        group.append(document.querySelector('#sidebar a[href$="/manage_article"]').cloneNode(true));
        category.parentElement.append(clone,group);
        return {visible:getComputedStyle(clone).display!=='none',gap:getComputedStyle(clone).marginTop};
      });
      checks.push({name:'second category fixture appears with the same group spacing',pass:second.visible&&second.gap==='24px'});
      await page.evaluate(()=>toggleSidebar());
      const separator=await page.locator('#qa-second-category').boundingBox();
      checks.push({name:'second category fixture becomes a divider',pass:separator&&separator.width===24&&separator.height===1});
    }
    results.push({theme,checks});
    await context.close();
  }
} finally {await browser.close()}
writeFileSync(path.join(directory,'responsive-results.json'),JSON.stringify(results,null,2));
const failures=results.flatMap(r=>r.checks.filter(c=>!c.pass).map(c=>r.theme+': '+c.name));
console.log(`${results.reduce((n,r)=>n+r.checks.length,0)} responsive/multiple-category checks; ${failures.length} failed`);
failures.forEach(f=>console.log(f));
assert.equal(failures.length,0);
