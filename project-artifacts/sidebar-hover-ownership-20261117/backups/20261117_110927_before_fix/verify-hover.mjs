import assert from 'node:assert/strict';
import {existsSync,writeFileSync} from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';
import {chromium} from 'playwright-core';

const directory=path.dirname(fileURLToPath(import.meta.url));
const publicRoot=path.resolve(directory,'../../public');
const phase=process.argv[2] || 'after';
const themes=['arunika_aurora','arunika_lucent','arunika_prism','arunika_equinox','default','calm_green'];
const results=[];
const browser=await chromium.launch({channel:'chrome',headless:true});
try {
  for(const theme of themes) {
    const modern=theme.startsWith('arunika_');
    for(const mode of ['light','dark']) {
      const context=await browser.newContext({viewport:{width:1440,height:900}});
      context.setDefaultTimeout(6000);
      await context.addInitScript(mode=>localStorage.setItem('theme',mode),mode);
      const page=await context.newPage();
      await page.route(/\/(assets|storage)\//,route=>{
        const asset=path.resolve(publicRoot,'.'+decodeURIComponent(new URL(route.request().url()).pathname));
        return asset.startsWith(publicRoot+path.sep)&&existsSync(asset)
          ?route.fulfill({path:asset})
          :route.fulfill({status:404,body:'Not found'});
      });
      await page.route('**/account?hover-qa=*',route=>route.fulfill({path:path.join(directory,'rendered',theme+'.html'),contentType:'text/html'}));
      await page.route(/^https?:\/\/(?!laravel-13-phoenix\.aruna)/,route=>route.abort());
      const checks=[];
      const check=(name,pass,actual)=>checks.push({name,pass:!!pass,actual});
      try {
        await page.goto('http://laravel-13-phoenix.aruna/account?hover-qa='+theme,{waitUntil:'domcontentloaded'});
        await page.evaluate(({modern,mode})=>{
          document.documentElement.dataset.bsTheme=mode;
          if(modern) {
            const sidebar=document.querySelector('#sidebar');
            if(sidebar.classList.contains('ph-expanded')) toggleSidebar();
          } else {
            document.querySelector('.arv7-sidebar').classList.add('arv7-sidebar-collapsed');
            document.querySelector('.arv7-container').classList.add('arv7-with-vertical-menu-collapsed');
          }
        },{modern,mode});
        await page.waitForFunction(modern=>modern
          ?Math.abs(document.querySelector('#sidebar').getBoundingClientRect().width-parseFloat(getComputedStyle(document.body).getPropertyValue('--ph-sidebar-width-collapsed')))<.5
          :document.querySelector('.arv7-sidebar').classList.contains('arv7-sidebar-collapsed'),modern);
        const links=await page.evaluate(modern=>{
          const selector=modern?'#sidebar a.list-group-item':'.arv7-sidebar > .arv7-menu a.ph-list-group-item:not(.ph-list-group-subitem)';
          return [...document.querySelectorAll(selector)].map((link,index)=>{
            link.dataset.qaHover=String(index);
            return {index,name:link.querySelector(modern?'.ph-nav-text':'.arv7-parent-menu-name')?.textContent.trim()||link.textContent.trim(),parent:link.hasAttribute('data-bs-target')};
          }).filter(link=>link.name);
        },modern);
        const inspect=async entry=>{
          const state=await page.evaluate(({index,modern})=>{
            const link=document.querySelector(`[data-qa-hover="${index}"]`);
            if(modern) {
              return {
                mapped:getPopover(link)?.querySelector('.ph-floating-header')?.textContent.trim()??null,
                panels:[...document.querySelectorAll('.ph-floating-submenu.ph-show-popover')].map(p=>p.querySelector('.ph-floating-header').textContent.trim()),
                tooltips:[...document.querySelectorAll('.ph-custom-tooltip.ph-show-tooltip')].map(t=>t.textContent.trim())
              };
            }
            const visible=[...document.querySelectorAll('.arv7-sidebar .ph-list-group-submenu,.arv7-sidebar .ph-list-group-submenu-single')].filter(el=>el.getClientRects().length>0&&getComputedStyle(el).visibility!=='hidden');
            return {panels:visible.map(el=>el.querySelector('.ph-list-group-item-title')?.textContent.trim()||el.textContent.trim())};
          },{index:entry.index,modern});
          if(modern) {
            check(`${entry.name}: panel lookup stays with its owner`,state.mapped===(entry.parent?entry.name:null),state.mapped);
            check(`${entry.name}: only its own submenu opens`,JSON.stringify(state.panels)===JSON.stringify(entry.parent?[entry.name]:[]),state.panels);
            check(`${entry.name}: tooltip and submenu are exclusive`,JSON.stringify(state.tooltips)===JSON.stringify(entry.parent?[]:[entry.name]),state.tooltips);
          } else check(`${entry.name}: only its own hover panel opens`,state.panels.length===1&&state.panels[0]===entry.name,state.panels);
          return state;
        };
        for(const entry of links) {
          await page.locator(`[data-qa-hover="${entry.index}"]`).hover();
          await inspect(entry);
          if(entry.name==='Workflow Editor'&&theme==='arunika_aurora'&&mode==='light') {
            await page.screenshot({path:path.join(directory,`${phase}-aurora-workflow.png`),clip:{x:0,y:0,width:440,height:850}});
          }
        }
        for(const name of ['Testing','Workflow Editor','Manage Event','Manage Articles','Testing']) {
          const entry=links.find(link=>link.name===name);
          assert.ok(entry,`Expected menu ${name}`);
          await page.locator(`[data-qa-hover="${entry.index}"]`).hover();
          await inspect(entry);
        }
        const parent=links.find(link=>link.name==='Testing');
        const panel=page.locator(modern?'.ph-floating-submenu':'.arv7-sidebar .ph-list-group-submenu').filter({hasText:'Testing'}).first();
        await panel.locator(modern?'.ph-floating-link':'.ph-list-group-subitem:not(.ph-list-group-item-title)').first().hover();
        await page.waitForTimeout(350);
        check('Testing stays open while pointer is over its submenu',await panel.isVisible());
        await page.locator(`[data-qa-hover="${links.find(link=>link.name==='Manage Event').index}"]`).hover();
        await inspect(links.find(link=>link.name==='Manage Event'));
        if(theme==='arunika_aurora'&&mode==='light') await page.screenshot({path:path.join(directory,`${phase}-aurora-event.png`),clip:{x:0,y:0,width:440,height:850}});
        await page.mouse.move(1000,800);
        if(modern) await page.waitForFunction(()=>!document.querySelector('.ph-show-tooltip,.ph-show-popover'));
        check('all hover panels close after leaving navigation',modern
          ?await page.locator('.ph-show-tooltip,.ph-show-popover').count()===0
          :!await panel.isVisible());
      } catch(error) {check('browser flow completes',false,error.message)}
      results.push({theme,mode,checks});
      console.log(`${theme}/${mode}: ${checks.filter(c=>!c.pass).length} failed of ${checks.length}`);
      await context.close();
    }
  }
} finally {await browser.close()}
writeFileSync(path.join(directory,`${phase}-results.json`),JSON.stringify(results,null,2));
const failures=results.flatMap(row=>row.checks.filter(check=>!check.pass).map(check=>`${row.theme}/${row.mode}: ${check.name}: ${JSON.stringify(check.actual)}`));
console.log(`${results.reduce((total,row)=>total+row.checks.length,0)} checks; ${failures.length} failed`);
failures.forEach(failure=>console.log(failure));
assert.equal(failures.length,0,'Hover owner must match the hovered navigation item');
