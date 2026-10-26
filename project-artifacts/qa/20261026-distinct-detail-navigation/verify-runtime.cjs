const assert = require('node:assert/strict');
const fs = require('node:fs');
module.exports = async function verify(ctx, base = 'http://laravel-13-phoenix.aruna') {
  const page = await ctx.newPage();
  page.setDefaultTimeout(8000);
  const config = [
    {key:'editorial-feature',nav:'.article-story-navigation',link:'.article-story-navigation__item',grid:'.article-story-navigation__grid'},
    {key:'knowledge-toc',nav:'.article-reference-navigation',link:'.article-reference-navigation__item'},
    {key:'focused-reader',nav:'.article-reading-navigation',link:'.article-reading-navigation__card',grid:'.article-reading-navigation__grid'}
  ];
  const cases = [], interactions = [];
  try {
    for (const item of config) {
      for (const width of [320,390,576,834,1024,1440]) {
        await page.setViewportSize({width,height:900});
        const response = await page.goto(`${base}/manage_article/templates/preview/detail/${item.key}`);
        assert.equal(response.status(),200);
        const nav = page.locator(item.nav);
        await nav.scrollIntoViewIfNeeded();
        await nav.locator('img').evaluateAll(images => Promise.all(images.map(image => image.decode())));
        const metrics = await page.evaluate(({nav,link,grid}) => {
          const root = document.querySelector(nav), links = [...root.querySelectorAll(link)];
          return {overflow:document.documentElement.scrollWidth > innerWidth,width:root.getBoundingClientRect().width,links:links.length,images:root.querySelectorAll('img').length,columns:grid ? getComputedStyle(root.querySelector(grid)).gridTemplateColumns.split(' ').length : 1,rects:links.map(e=>({x:e.getBoundingClientRect().x,y:e.getBoundingClientRect().y,width:e.getBoundingClientRect().width}))};
        },item);
        assert.equal(metrics.overflow,false,`${item.key} ${width}`);
        assert.equal(metrics.links,2);
        assert.equal(metrics.images,item.key==='knowledge-toc' ? 0 : 2);
        const expected = item.key==='knowledge-toc' ? 1 : item.key==='editorial-feature' ? (metrics.width<=560?1:2) : (width<576?1:2);
        assert.equal(metrics.columns,expected);
        if (item.key==='editorial-feature' && expected===2) assert.ok(metrics.rects[1].width > metrics.rects[0].width);
        if (item.key==='knowledge-toc') assert.ok(metrics.rects[1].y > metrics.rects[0].y);
        if ([390,834,1440].includes(width)) await nav.screenshot({path:`${__dirname}/${item.key}-${width}.png`});
        cases.push({template:item.key,viewport:width,...metrics});
      }
      await page.setViewportSize({width:1440,height:900});
      await page.goto(`${base}/manage_article/templates/preview/detail/${item.key}`);
      const next = page.locator(item.link+'--next');
      const normal = await next.evaluate(e=>({background:getComputedStyle(e).backgroundColor,underline:getComputedStyle(e.querySelector('strong')).textDecorationColor}));
      await next.hover(); await page.waitForTimeout(320);
      if (item.key==='editorial-feature') {
        assert.notEqual(await next.locator('img').evaluate(e=>getComputedStyle(e).transform),'none');
        assert.equal(await next.evaluate(e=>getComputedStyle(e,'::after').transform),'matrix(1, 0, 0, 1, 0, 0)');
      } else {
        assert.notEqual(await next.evaluate(e=>getComputedStyle(e).backgroundColor),normal.background);
        if (item.key==='knowledge-toc') assert.notEqual(await next.locator('strong').evaluate(e=>getComputedStyle(e).textDecorationColor),normal.underline);
      }
      assert.equal(await next.evaluate(e=>getComputedStyle(e).boxShadow),'none');
      await page.locator(item.nav).screenshot({path:`${__dirname}/${item.key}-hover.png`});
      await page.mouse.move(0,0); await page.keyboard.press('Tab'); await next.focus();
      assert.equal(await next.evaluate(e=>e.matches(':focus-visible')),true);
      assert.equal(await next.evaluate(e=>getComputedStyle(e).outlineWidth),'2px');
      await page.emulateMedia({reducedMotion:'reduce'}); await next.hover();
      const moving = item.key==='editorial-feature' ? next.locator('img') : item.key==='knowledge-toc' ? next.locator(':scope > i') : next.locator('.article-reading-navigation__arrow');
      assert.equal(await moving.evaluate(e=>getComputedStyle(e).transform),'none');
      assert.equal(await moving.evaluate(e=>getComputedStyle(e).transitionDuration),'0s');
      await page.emulateMedia({reducedMotion:'no-preference'});
      if (item.key!=='focused-reader') {
        for (const direction of ['previous','next']) {
          await page.goto(`${base}/manage_article/templates/preview/detail/${item.key}`);
          await page.locator(item.link+'--'+direction).evaluate(e=>e.remove());
          assert.equal(await page.locator(item.link).count(),1);
          if(item.grid) assert.equal(await page.locator(item.link).evaluate(e=>Math.abs(e.getBoundingClientRect().width-e.parentElement.getBoundingClientRect().width)<1),true);
        }
        await page.setViewportSize({width:320,height:900});
        await page.goto(`${base}/manage_article/templates/preview/detail/${item.key}`);
        await page.locator(item.link+' strong').evaluateAll(links=>links.forEach(e=>e.textContent='ArtikelDenganJudulPanjangTanpaSpasi'.repeat(16)));
        assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth),false);
      }
      interactions.push({template:item.key,hover:true,keyboardFocus:true,reducedMotion:true,oneNeighbor:item.key==='focused-reader'?'PHP covered':true,longTitle:item.key==='focused-reader'?'unchanged':true});
    }
    await page.goto(`${base}/article/qa-ckeditor-panduan-sistem-andal-uji-seluruh-format-konten`);
    assert.equal(await page.locator('.article-detail--focused-reader').count(),1);
    const previous = await page.locator('.article-reading-navigation__card--previous').getAttribute('href');
    await page.locator('.article-reading-navigation__card--previous').click(); assert.equal(page.url(),previous);
    await page.locator('.article-reading-navigation__card--next').click(); assert.ok(page.url().endsWith('/qa-ckeditor-panduan-sistem-andal-uji-seluruh-format-konten'));
    fs.writeFileSync(`${__dirname}/runtime-verification.json`,JSON.stringify({cases,interactions,publicFocusedReaderRoundtrip:true},null,2));
    return {cases:cases.length,interactions:interactions.length,publicFocusedReaderRoundtrip:true};
  } finally {await page.close();}
};
