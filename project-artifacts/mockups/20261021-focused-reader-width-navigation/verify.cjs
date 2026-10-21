const {chromium}=require('playwright-core');
const fs=require('node:fs');
const path=require('node:path');
const assert=require('node:assert/strict');
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try{
  const page=await browser.newPage();const errors=[];page.on('pageerror',e=>errors.push(e.message));
  const markup=fs.readFileSync(path.join(__dirname,'focused-reader-layout.html'),'utf8');
  const checks=[];
  for(const variant of ['Reading Cards','Editorial Flow'])for(const width of [320,390,768,1024,1440]){
   await page.setViewportSize({width,height:1000});
   await page.setContent('<style>body{margin:0;padding:8px;color-scheme:light}</style>'+markup);
   await page.evaluate(name=>document.querySelectorAll('[data-variant]').forEach(s=>s.hidden=s.dataset.variant!==name),variant);
   const state=await page.evaluate(()=>({overflow:document.documentElement.scrollWidth>innerWidth,contentWidth:document.querySelector('[data-variant]:not([hidden]) .fl-reading').getBoundingClientRect().width,columns:getComputedStyle(document.querySelector('[data-variant]:not([hidden]) .fl-nav')).gridTemplateColumns}));
   assert.equal(state.overflow,false,variant+' '+width);
   checks.push({variant,width,...state});
   if(width===1024||width===390)await page.screenshot({path:path.join(__dirname,`${variant.replaceAll(' ','-')}-${width}.png`),fullPage:true});
  }
  await page.setViewportSize({width:1024,height:1000});await page.setContent('<style>body{margin:0;padding:8px;color-scheme:light}</style>'+markup);
  await page.getByRole('button',{name:'Mobile',exact:true}).click();assert.equal(await page.locator('.fl-page').first().evaluate(e=>e.getBoundingClientRect().width),390);
  await page.getByRole('button',{name:'Tablet',exact:true}).click();assert.equal(await page.locator('.fl-page').first().evaluate(e=>e.getBoundingClientRect().width),834);
  await page.getByRole('button',{name:'Desktop',exact:true}).click();
  const next=page.locator('[data-variant="Reading Cards"] .fl-next');
  const normal=await next.evaluate(e=>getComputedStyle(e).backgroundColor);
  await page.getByRole('button',{name:'Contoh hover',exact:true}).click();
  await next.evaluate(e=>e.getAnimations().forEach(a=>a.finish()));
  const hover=await next.evaluate(e=>getComputedStyle(e).backgroundColor);assert.notEqual(normal,hover);
  await page.screenshot({path:path.join(__dirname,'hover-desktop.png'),fullPage:true});
  await next.click();assert.match(await page.locator('[data-variant="Reading Cards"] .fl-selected').textContent(),/Small teams/);
  await next.focus();assert.equal(await next.evaluate(e=>e===document.activeElement),true);
  await page.emulateMedia({reducedMotion:'reduce'});assert.equal(await next.evaluate(e=>getComputedStyle(e).transitionDuration),'0s');
  assert.deepEqual(errors,[]);
  fs.writeFileSync(path.join(__dirname,'verification.json'),JSON.stringify({checks,deviceSwitches:'passed',hover:{normal,hover},localNavigation:'passed',keyboardFocus:'passed',reducedMotion:'passed',errors},null,2));
  console.log(JSON.stringify({responsiveCases:checks.length,hover:'passed',devices:'passed',localNavigation:'passed',errors}));
 }finally{await browser.close()}
})().catch(e=>{console.error(e);process.exitCode=1});
