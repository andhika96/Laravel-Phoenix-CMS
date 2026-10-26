const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
module.exports = async function verify(ctx) {
  const manifest = JSON.parse(fs.readFileSync(path.join(__dirname,'navigation-article-manifest.json'),'utf8'));
  const page = await ctx.newPage();
  page.setDefaultTimeout(8000);
  const cases = [];
  const destinations = new Map();
  try {
    await page.goto('http://laravel-13-phoenix.aruna/article/qa-ckeditor-panduan-sistem-andal-uji-seluruh-format-konten');
    for (const entry of manifest) {
      const selector = entry.template === 'editorial-feature' ? '.article-story-navigation' : '.article-reference-navigation';
      for (const width of [390,1440]) {
        await page.setViewportSize({width,height:900});
        await page.setContent(fs.readFileSync(path.join(__dirname,entry.file),'utf8'));
        const nav = page.locator(selector);
        await nav.scrollIntoViewIfNeeded();
        await nav.locator('img').evaluateAll(images => Promise.all(images.map(image=>image.decode())));
        const links = nav.locator('a');
        assert.equal(await links.count(),entry.neighbors);
        const hrefs = await links.evaluateAll(items=>items.map(item=>item.href));
        assert.deepEqual(hrefs,entry.hrefs);
        assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth),false,entry.file+' '+width);
        if (entry.template === 'editorial-feature' && entry.neighbors === 1) assert.equal(await links.evaluate(e=>Math.abs(e.getBoundingClientRect().width-e.parentElement.getBoundingClientRect().width)<1),true);
        for (const href of hrefs) {
          if (destinations.has(href)) continue;
          const target = await ctx.newPage();
          try {
            const response = await target.goto(href);
            assert.equal(response.status(),200,href);
            assert.equal(await target.locator('.article-detail').count(),1);
            destinations.set(href,{status:response.status(),title:await target.locator('.article-detail h1').innerText()});
          } finally {await target.close();}
        }
        cases.push({file:entry.file,template:entry.template,width,neighbors:entry.neighbors,overflow:false,links:hrefs});
      }
    }
    const report = {cases,destinations:[...destinations].map(([url,detail])=>({url,...detail}))};
    fs.writeFileSync(path.join(__dirname,'saved-navigation-verification.json'),JSON.stringify(report,null,2));
    return {cases:cases.length,destinations:destinations.size};
  } finally {await page.close();}
};
