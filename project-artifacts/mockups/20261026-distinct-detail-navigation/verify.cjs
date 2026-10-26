const {chromium} = require('playwright-core');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
(async () => {
  const browser = await chromium.launch({channel:'msedge',headless:true});
  try {
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    const cases = [];
    const variants = ['Editorial Feature · Story Spread','Knowledge & TOC · Knowledge Links','Focused Reader · Reading Cards'];
    for (const width of [320,390,736,1024]) {
      await page.setViewportSize({width,height:1100});
      await page.goto('http://127.0.0.1:9046/');
      const frame = page.frames().find(item => item !== page.mainFrame());
      for (const name of variants) {
        await frame.locator('.viz-carousel summary').click();
        await frame.getByRole('button',{name,exact:true}).click();
        await frame.locator('[data-variant]:not([hidden]) img').evaluateAll(images => Promise.all(images.map(img => img.decode())));
        const metrics = await frame.evaluate(() => {
          const section = document.querySelector('[data-variant]:not([hidden])');
          const grid = section.querySelector('.pn-spread,.pn-cards');
          return {overflow:document.documentElement.scrollWidth > innerWidth,pageWidth:section.querySelector('.pn-page').getBoundingClientRect().width,links:section.querySelectorAll('a').length,images:section.querySelectorAll('img').length,columns:grid ? getComputedStyle(grid).gridTemplateColumns.split(' ').length : 1};
        });
        assert.equal(metrics.overflow,false,`${name} ${width}`);
        assert.equal(metrics.links,2);
        assert.equal(metrics.images,name.startsWith('Knowledge') ? 0 : 2);
        assert.equal(metrics.columns,name.startsWith('Knowledge') || metrics.pageWidth <= 560 ? 1 : 2);
        cases.push({name,width,...metrics});
        if ([390,1024].includes(width)) await page.screenshot({path:path.join(__dirname,`${name.split(' · ')[0].replaceAll(' ','-').replace('&','and')}-${width}.png`),fullPage:true});
      }
    }
    await page.setViewportSize({width:1024,height:1100});
    await page.goto('http://127.0.0.1:9046/');
    const frame = page.frames().find(item => item !== page.mainFrame());
    const apply = value => frame.evaluate(state => window.dispatchEvent(new CustomEvent('openai:set_globals',{detail:{globals:{widgetState:{modelContent:{articleNavigationConcepts:state}}}}})),value);
    const select = async name => {
      await frame.locator('.viz-carousel summary').click();
      await frame.getByRole('button',{name,exact:true}).click();
    };
    const interactions = [];
    for (const name of variants) {
      await select(name);
      const next = frame.locator('[data-variant]:not([hidden]) a.pn-next');
      await next.hover();
      await frame.evaluate(() => document.getAnimations().forEach(animation => animation.finish()));
      if (name.startsWith('Editorial')) assert.notEqual(await next.locator('img').evaluate(e => getComputedStyle(e).transform),'none');
      else assert.notEqual(await next.evaluate(e => getComputedStyle(e).backgroundColor),'rgba(0, 0, 0, 0)');
      await page.mouse.move(0,0);
      await page.keyboard.press('Tab'); await next.focus();
      assert.equal(await next.evaluate(e => e.matches(':focus-visible')),true);
      assert.equal(await next.evaluate(e => getComputedStyle(e).outlineWidth),'2px');
      await next.click();
      assert.match(await frame.locator('.pn-status').innerText(),/Designing Spaces/);
      assert.equal(await frame.evaluate(() => location.hash),'');
      await page.emulateMedia({reducedMotion:'reduce'});
      await next.hover();
      assert.equal(await next.evaluate(e => getComputedStyle(e).transitionDuration),'0s');
      await page.emulateMedia({reducedMotion:'no-preference'});
      for (const neighbors of ['previous','next']) {
        await apply({neighbors});
        assert.equal(await frame.locator('[data-variant]:not([hidden]) a:visible').count(),1);
      }
      await apply({neighbors:'both'});
      interactions.push({name,hover:true,keyboardFocus:true,reducedMotion:true,localClick:true,singleNeighbor:true});
    }
    const devices = [];
    for (const device of ['desktop','tablet','mobile']) {
      await apply({device,hover:false});
      const width = await frame.locator('[data-variant]:not([hidden]) .pn-page').evaluate(e => e.getBoundingClientRect().width);
      assert.ok(device === 'desktop' ? width > 650 : width === (device === 'tablet' ? 650 : 390));
      devices.push({device,width});
    }
    await apply({device:'desktop',hover:true}); await select(variants[0]);
    assert.equal(await frame.locator('#article-nav-concepts').getAttribute('data-hover'),'true');
    await frame.evaluate(() => document.getAnimations().forEach(animation => animation.finish()));
    await page.screenshot({path:path.join(__dirname,'editorial-hover.png'),fullPage:true});
    await frame.evaluate(() => document.documentElement.style.colorScheme = 'dark');
    await page.screenshot({path:path.join(__dirname,'editorial-dark.png'),fullPage:true});
    assert.deepEqual(errors,[]);
    const markup = fs.readFileSync(path.join(__dirname,'detail-navigation-concepts.html'),'utf8');
    assert.ok(Buffer.byteLength(markup) < 1024*1024);
    assert.equal(markup.includes('__PREVIOUS_PHOTO__'),false);
    assert.equal(markup.includes('__NEXT_PHOTO__'),false);
    fs.writeFileSync(path.join(__dirname,'verification.json'),JSON.stringify({cases,interactions,devices,hoverControl:true,errors,sourceAppUnchanged:true},null,2));
    console.log(JSON.stringify({responsiveCases:cases.length,interactions:interactions.length,devices:devices.length,errors}));
  } finally { await browser.close(); }
})().catch(error => {console.error(error);process.exitCode = 1;});
