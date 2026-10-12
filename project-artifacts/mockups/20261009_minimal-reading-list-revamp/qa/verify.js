async page => {
  const base='http://127.0.0.1:8774/';
  const output='D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20261009_minimal-reading-list-revamp/qa/';
  const checks=[];
  const check=(name,condition)=>{if(!condition)throw new Error(name);checks.push(name);};
  const measure=()=>page.evaluate(()=>({
    overflow:document.documentElement.scrollWidth>innerWidth,
    searchHeight:document.getElementById('article-search').getBoundingClientRect().height,
    buttonHeight:document.querySelector('.search button').getBoundingClientRect().height,
    buttonWidth:document.querySelector('.search button').getBoundingClientRect().width,
    searchGap:parseFloat(getComputedStyle(document.querySelector('.search')).columnGap),
    smallPages:[...document.querySelectorAll('.page-button')].some(button=>{const r=button.getBoundingClientRect();return r.width<44||r.height<44;}),
    brokenImages:[...document.images].filter(image=>!image.complete||!image.naturalWidth).map(image=>image.src)
  }));
  await page.setViewportSize({width:1440,height:1000});
  await page.goto(base+'index.html');
  await page.evaluate(()=>document.fonts.ready);
  let desktop=await measure();
  if(desktop.brokenImages.length){await page.reload();await page.evaluate(()=>document.fonts.ready);desktop=await measure();}
  check('All local images load',desktop.brokenImages.length===0);
  check('Six fixture articles per page',await page.locator('.article-row').count()===6);
  check('Search input and button are 48px',desktop.searchHeight===48&&desktop.buttonHeight===48);
  check('Desktop search button is 128px wide',desktop.buttonWidth===128);
  check('Input to button gap is 12px',desktop.searchGap===12);
  check('Pagination targets are at least 44px',!desktop.smallPages);
  check('Desktop has no horizontal overflow',!desktop.overflow);
  check('Previous is disabled on first page',await page.getByRole('button',{name:'Previous page',exact:true}).isDisabled());
  await page.screenshot({path:output+'desktop.png'});
  await page.screenshot({path:output+'desktop-full.png',fullPage:true});
  await page.locator('.pagination').screenshot({path:output+'pagination-first.png'});
  await page.getByRole('button',{name:'Page 2',exact:true}).click();
  check('Next page changes stories',await page.locator('.article-row h3').first().innerText()==='The Quiet Value of Everyday Craft');
  check('Pagination summary changes to 7 to 12',(await page.locator('#pagination-summary').innerText()).includes('7 to 12'));
  await page.getByRole('button',{name:'Page 8',exact:true}).click();
  check('Next is disabled on last page',await page.getByRole('button',{name:'Next page',exact:true}).isDisabled());
  await page.getByRole('button',{name:'Previous page',exact:true}).click();
  await page.getByRole('button',{name:'Previous page',exact:true}).click();
  await page.getByRole('button',{name:'Previous page',exact:true}).click();
  await page.getByRole('button',{name:'Previous page',exact:true}).click();
  check('Middle page active state is correct',await page.getByRole('button',{name:'Page 4',exact:true}).getAttribute('aria-current')==='page');
  await page.locator('.pagination').screenshot({path:output+'pagination-middle.png'});
  await page.setViewportSize({width:320,height:900});
  await page.waitForFunction(()=>document.querySelectorAll('#pager [data-page]').length<=5);
  check('Middle-page pagination fits a 320px viewport',!(await measure()).overflow);
  await page.setViewportSize({width:1440,height:1000});
  await page.locator('#article-search').fill('resilient');
  await page.getByRole('button',{name:'Search',exact:true}).click();
  check('Article search filters fixtures',await page.locator('#result-count').innerText()==='4 articles');
  check('Search resets pagination to first page',(await page.locator('#pagination-summary').innerText()).includes('1 to 4'));
  await page.locator('#article-search').fill('no-matching-example-xyz');
  await page.getByRole('button',{name:'Search',exact:true}).click();
  check('Empty result has recovery action',await page.getByRole('heading',{name:'No articles found'}).isVisible()&&await page.locator('[data-clear]').isVisible());
  await page.locator('[data-clear]').click();
  await page.locator('[data-category="Technology"]').click();
  check('Category filter works',await page.locator('#result-count').innerText()==='8 articles');
  await page.getByRole('button',{name:'Next page',exact:true}).click();
  check('Filtered last page has two articles',await page.locator('.article-row').count()===2);
  await page.getByRole('button',{name:'Clear filters',exact:true}).click();
  await page.locator('#category-search').fill('zzzz');
  check('Category search has an empty state',await page.locator('#category-empty').isVisible());
  check('Category search retains All categories',await page.locator('[data-category=""]').isVisible());
  await page.locator('#category-search').fill('');
  await page.locator('.article-row h3 a').first().click();
  check('Sample article opens in a native dialog',await page.locator('#article-dialog').isVisible());
  await page.keyboard.press('Escape');
  check('Escape closes sample article',!await page.locator('#article-dialog').isVisible());
  const sizes=[];
  for(const width of [1440,1024,768,390,375,320]){
    await page.setViewportSize({width,height:900});
    const layout=await measure();
    check(`No horizontal overflow at ${width}px`,!layout.overflow);
    check(`Search remains 48px at ${width}px`,layout.searchHeight===48&&layout.buttonHeight===48);
    check(`Pagination targets remain 44px at ${width}px`,!layout.smallPages);
    sizes.push({width,...layout});
    if(width===768)await page.screenshot({path:output+'tablet.png'});
    if(width===390){await page.screenshot({path:output+'mobile.png'});await page.screenshot({path:output+'mobile-full.png',fullPage:true});await page.locator('.pagination').screenshot({path:output+'pagination-mobile.png'});}
  }
  await page.setViewportSize({width:375,height:900});
  await page.locator('#mobile-category').selectOption('Design');
  check('Mobile native category select works',await page.locator('#result-count').innerText()==='8 articles');
  await page.setViewportSize({width:844,height:390});
  check('Landscape has no horizontal overflow',!(await measure()).overflow);
  await page.emulateMedia({reducedMotion:'reduce'});
  check('Reduced motion disables transitions',await page.locator('.primary').first().evaluate(element=>getComputedStyle(element).transitionDuration==='0s'));
  await page.emulateMedia({reducedMotion:'no-preference'});
  await page.setViewportSize({width:1000,height:900});
  await page.goto(base+'preview.html');
  const frame=page.frameLocator('#prototype');
  await frame.locator('.article-row').first().waitFor();
  check('Preview viewer opens desktop',await page.locator('#prototype').evaluate(element=>element.getBoundingClientRect().width>0));
  await page.getByRole('button',{name:'Mobile',exact:true}).click();
  await frame.locator('#mobile-category').waitFor({state:'visible'});
  check('Viewer switches iframe to 390px',await page.locator('#prototype').evaluate(element=>parseFloat(element.style.width)===390));
  check('Viewer mobile frame fits contents',await frame.locator('body').evaluate(()=>document.documentElement.scrollWidth<=innerWidth));
  await page.getByRole('button',{name:'Desktop',exact:true}).click();
  await frame.locator('.categories-panel').waitFor({state:'visible'});
  check('Viewer switches back to desktop',await page.locator('#prototype').evaluate(element=>parseFloat(element.style.width)===1440));
  await page.goto(base+'index.html');
  await page.evaluate(results=>{window.__prototypeQA=results;},{passed:checks.length,checks,desktop,sizes});
}
