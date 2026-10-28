import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {chromium} from 'playwright-core';

const out=path.resolve('project-artifacts/qa/cms-bootstrap-theme-20261028');
const inspect=process.argv.includes('--inspect');
const themes=['arunika_lucent','arunika_prism','arunika_equinox','arunika_aurora','default','calm_green'];
const read=filename=>fs.readFile(filename,'utf8');
const bootstrap=await read('public/assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css');
const common=await read('public/assets/css/phoenix-cms.css');
let bridge='';try{bridge=await read('public/assets/css/cms-bootstrap-theme.css');}catch(error){if(error.code!=='ENOENT')throw error;}
const browser=await chromium.launch({channel:'chrome',headless:true}),results=[],errors=[];
try {
  const page=await browser.newPage({viewport:{width:1100,height:1100}});
  page.on('pageerror',e=>errors.push(e.message));
  for(const theme of (inspect?['arunika_lucent']:themes)) {
    const legacy=['default','calm_green'].includes(theme);
    const themeCss=legacy
      ? await read(`public/assets/css/themes/${theme}/phoenix-cms-${theme.replace('_','-')}.css`)+await read(`public/assets/css/themes/${theme}/aruna-admin-v7-${theme.replace('_','-')}.css`)
      : await read(`public/assets/css/themes/${theme}/${theme}.css`)+await read(`public/assets/css/themes/${theme}/mobile-v2.css`);
    for(const mode of (inspect?['light']:['light','dark'])) for(const accent of (inspect?['native']:['native','#9D00FF'])) {
      await page.setContent(`<!doctype html><html data-bs-theme="${mode}" style="${accent==='native'?'':`${legacy?'--ph-primary':'--ph-theme-primary'}:${accent};`}"><head><meta charset="utf-8"><style>${bootstrap}\n${common}\n${themeCss}\n${bridge}</style><style>
        body{--probe-primary:var(--ph-theme-primary,var(--ph-primary));--probe-bg:var(--ph-bg-popover,var(--ph-content-bg,var(--bs-body-bg)));--probe-soft:var(--ph-theme-primary-subtle,color-mix(in srgb,var(--probe-primary) 10%,var(--probe-bg)));--probe-ink:var(--ph-theme-text-accessible,var(--ph-text-main,var(--bs-body-color)));}
        body{height:auto!important;overflow:auto!important;padding:20px;font-size:14px;}#probe{width:900px;display:grid;gap:12px;padding:20px;}#probe .dropdown-menu{position:static;}#references{position:absolute;left:-2000px;}#ref-primary{background:var(--probe-primary);}#ref-soft{background:var(--probe-soft);color:var(--probe-ink);}#ref-danger{color:var(--bs-danger);}#ref-bg{background:var(--probe-bg);}
      </style></head><body class="${legacy?'':`ph-theme-${theme.replaceAll('_','-')}`}" ${legacy?'':`data-ph-mobile-theme="${theme}"`}><main class="ph-scrollable-content"><section id="probe" class="ph-content">
        <div class="dropdown"><ul class="dropdown-menu show"><li><a href="#" class="dropdown-item" id="menu">Menu artikel</a></li><li><button type="button" class="dropdown-item text-danger" id="delete">Delete</button></li><li><a href="#" class="dropdown-item disabled" id="disabled" aria-disabled="true">Disabled</a></li></ul></div>
        <div><button class="btn btn-primary" id="primary">Primary</button> <button class="btn btn-outline-primary" id="outline">Outline primary</button> <button class="btn btn-danger" id="danger">Danger</button> <button class="btn btn-success" id="success">Success</button> <button class="btn btn-warning" id="warning">Warning</button> <button class="btn btn-info" id="info">Info</button></div>
        <div class="nav nav-pills"><a href="#" class="nav-link active" id="pills">Active tab</a></div>
        <ul class="pagination"><li class="page-item active"><a href="#" class="page-link" id="pagination">1</a></li></ul>
        <div class="list-group"><a href="#" class="list-group-item active" id="list">Active list item</a></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" checked id="check"><label class="form-check-label" for="check">Checked</label></div>
        <div class="form-check"><input class="form-check-input" type="radio" checked id="radio"><label class="form-check-label" for="radio">Radio</label></div>
        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="switch"><label class="form-check-label" for="switch">Switch</label></div>
        <div class="progress"><div class="progress-bar" id="progress" style="width:60%">60%</div></div>
        <div class="accordion"><button class="accordion-button" id="accordion" type="button">Open accordion</button></div>
        <div class="alert alert-primary" id="alert">Primary alert</div>
        <div><span class="text-primary" id="text">Primary text</span> <span class="badge text-bg-primary" id="badge">Primary badge</span><span class="bg-primary bg-opacity-50" id="opacity">Opacity</span></div>
        <input class="form-control" id="input" aria-label="Input"><input class="form-control is-invalid" id="invalid" aria-label="Invalid input">
      </section></main><div id="references"><span id="ref-primary">ref</span><span id="ref-soft">ref</span><span id="ref-danger">ref</span><span id="ref-bg">ref</span></div></body></html>`);
      await page.evaluate(()=>document.addEventListener('click',e=>e.preventDefault()));
      const values=await page.evaluate(()=>{
        const canvas=document.createElement('canvas');canvas.width=canvas.height=1;const ctx=canvas.getContext('2d',{willReadFrequently:true});
        const rgba=color=>{ctx.clearRect(0,0,1,1);ctx.fillStyle=color;ctx.fillRect(0,0,1,1);return [...ctx.getImageData(0,0,1,1).data];};
        const color=(id,property='backgroundColor')=>rgba(getComputedStyle(document.getElementById(id))[property]);
        document.getElementById('menu').classList.add('active');
        const semantic=Object.fromEntries(['success','warning','info'].map(id=>[id,{actual:color(id),expected:rgba(getComputedStyle(document.body).getPropertyValue('--bs-'+id))}]));
        const disabled=document.getElementById('disabled'),disabledStyle=getComputedStyle(disabled);
        return {primary:color('ref-primary'),soft:color('ref-soft'),ink:color('ref-soft','color'),danger:color('ref-danger','color'),menu:color('menu'),menuText:color('menu','color'),deleteText:color('delete','color'),primaryButton:color('primary'),outline:color('outline','color'),pills:color('pills'),pagination:color('pagination'),list:color('list'),listText:color('list','color'),pillsText:color('pills','color'),paginationText:color('pagination','color'),check:color('check'),radio:color('radio'),progress:color('progress'),accordion:color('accordion'),accordionIcon:getComputedStyle(document.getElementById('accordion'),'::after').backgroundImage,alert:color('alert'),text:color('text','color'),badge:color('badge'),dangerButton:color('danger'),semantic,opacity:color('opacity'),disabled:{actual:color('disabled','color'),expected:rgba(disabledStyle.getPropertyValue('--bs-dropdown-link-disabled-color')),pointer:disabledStyle.pointerEvents}};
      });
      if(inspect){await fs.writeFile(path.join(out,'before-components.json'),JSON.stringify(values,null,2));console.log(JSON.stringify(values));break;}
      const same=(actual,expected,label)=>assert.ok(actual.every((value,i)=>Math.abs(value-expected[i])<=1),JSON.stringify({theme,mode,accent,label,actual,expected}));
      same(values.menu,values.soft,'dropdown active background');same(values.menuText,values.ink,'dropdown active foreground');same(values.deleteText,values.danger,'danger remains danger');
      for(const key of ['primaryButton','outline','pills','pagination','list','check','radio','progress','text','badge']) same(values[key],values.primary,key);
      for(const key of ['listText','pillsText','paginationText']) same(values[key],[255,255,255,255],key);
      assert.ok(!/6ea8fe|0d6efd/i.test(values.accordionIcon),values.accordionIcon);
      same(values.accordion,values.soft,'accordion');same(values.alert,values.soft,'alert');same(values.dangerButton,values.danger,'danger button');
      for(const [name,role] of Object.entries(values.semantic))same(role.actual,role.expected,name);
      same(values.opacity,[...values.primary.slice(0,3),128],'opacity utility');same(values.disabled.actual,values.disabled.expected,'disabled text');assert.equal(values.disabled.pointer,'none');
      await page.locator('#menu').evaluate(el=>el.classList.remove('active'));
      await page.locator('#menu').focus();
      const focus=await page.locator('#menu').evaluate(el=>({background:getComputedStyle(el).backgroundColor,color:getComputedStyle(el).color,outline:getComputedStyle(el).outlineStyle}));
      const refs=await page.locator('#ref-soft').evaluate(el=>({background:getComputedStyle(el).backgroundColor,color:getComputedStyle(el).color}));
      assert.equal(focus.background,refs.background);assert.equal(focus.color,refs.color);
      await page.locator('#invalid').focus();
      const invalid=await page.locator('#invalid').evaluate(el=>getComputedStyle(el).borderColor);
      assert.notEqual(invalid,'rgb(157, 0, 255)');
      await page.locator('#switch').focus();
      const switchFocus=await page.locator('#switch').evaluate(el=>({image:getComputedStyle(el).backgroundImage,primary:getComputedStyle(document.getElementById('ref-primary')).backgroundColor}));
      assert.ok(switchFocus.image.includes('gradient(')&&switchFocus.image.includes(switchFocus.primary),JSON.stringify(switchFocus));
      results.push({theme,mode,accent,values,focus,invalid,switchFocus});
      if(theme==='arunika_lucent'&&accent==='native')await page.screenshot({path:path.join(out,`components-${mode}.png`)});
    }
  }
  if(!inspect){assert.deepEqual(errors,[]);await fs.writeFile(path.join(out,'components-verification.json'),JSON.stringify({ok:true,cases:results.length,errors,results},null,2));console.log(JSON.stringify({ok:true,cases:results.length,errors}));}
} finally {await browser.close();}
