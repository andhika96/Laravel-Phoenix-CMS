const { chromium } = require('playwright-core');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
(async () => {
  const browser = await chromium.launch({channel:'msedge',headless:true});
  try {
    const page = await browser.newPage();
    const errors=[];
    page.on('pageerror',e=>errors.push(e.message));
    const html=fs.readFileSync(path.join(__dirname,'focused-reader.html'),'utf8');
    const results=[];
    for (const width of [1440,1024,768,390,320]) {
      await page.setViewportSize({width,height:1000});
      await page.setContent('<style>body{margin:0;padding:12px;color-scheme:light}</style>'+html);
      await page.locator('.fr-cover').evaluate(img=>img.decode());
      const metrics=await page.evaluate(()=>({overflow:document.documentElement.scrollWidth>innerWidth,title:getComputedStyle(document.querySelector('h1')).fontSize,columns:getComputedStyle(document.querySelector('.fr-end')).gridTemplateColumns}));
      assert.equal(metrics.overflow,false,'overflow at '+width);
      results.push({width,...metrics});
      if(width===1024||width===390) await page.screenshot({path:path.join(__dirname,`preview-${width}.png`),fullPage:true});
    }
    await page.setViewportSize({width:1024,height:1000});
    await page.getByRole('button',{name:'Mobile',exact:true}).click();
    assert.equal(await page.locator('.fr-page').evaluate(el=>el.getBoundingClientRect().width),390);
    await page.getByRole('button',{name:'Tablet',exact:true}).click();
    assert.equal(await page.locator('.fr-page').evaluate(el=>el.getBoundingClientRect().width),768);
    await page.getByRole('button',{name:'Desktop',exact:true}).click();
    assert.equal(await page.getByRole('button',{name:'Desktop',exact:true}).getAttribute('aria-pressed'),'true');
    await page.emulateMedia({colorScheme:'dark'});
    await page.evaluate(()=>document.body.style.colorScheme='dark');
    assert.equal(await page.locator('.fr-page').evaluate(el=>getComputedStyle(el).backgroundColor),'rgb(23, 27, 32)');
    assert.deepEqual(errors,[]);
    fs.writeFileSync(path.join(__dirname,'verification.json'),JSON.stringify({results,deviceSwitches:'passed',darkSurface:'passed',pageErrors:errors},null,2));
    console.log(JSON.stringify({results,deviceSwitches:'passed',darkSurface:'passed',pageErrors:errors}));
  } finally {await browser.close()}
})().catch(e=>{console.error(e);process.exitCode=1});
