async page => {
  const base='http://127.0.0.1:8775/implementation/';
  const checks=[];
  const check=(name,value)=>{if(!value)throw new Error(name);checks.push(name);};
  await page.setViewportSize({width:1440,height:1000});
  await page.goto(base+'boxed.html');
  await page.evaluate(()=>document.fonts.ready);
  const measure=()=>page.evaluate(()=>{
    const input=document.querySelector('[name=search]'),button=input.parentElement.querySelector('button'),i=input.getBoundingClientRect(),b=button.getBoundingClientRect(),lead=document.querySelector('.article-editorial-lead');
    return {overflow:document.documentElement.scrollWidth>innerWidth,gap:b.left-i.right,inputHeight:i.height,buttonHeight:b.height,font:parseFloat(getComputedStyle(input).fontSize),bodyFont:parseFloat(getComputedStyle(document.querySelector('.article-page')).fontSize),image:lead.querySelector('.article-editorial-lead__media').getBoundingClientRect().toJSON(),body:lead.querySelector('.article-editorial-lead__body').getBoundingClientRect().toJSON(),columns:getComputedStyle(document.querySelector('.article-editorial-grid')).gridTemplateColumns.split(' ').length};
  });
  let data=await measure();
  check('Source search has a visible configurable gap',data.gap>=8);
  check('Search controls align at 48px or above',data.inputHeight>=48&&Math.abs(data.inputHeight-data.buttonHeight)<1);
  check('Search font follows CMS body scale',Math.abs(data.font-data.bodyFont)<.1);
  check('Category select opts into existing asynchronous filter behavior',await page.locator('[name=category][data-article-category-select]').count()===1);
  check('More stories heading is inside the AJAX replacement slot',await page.locator('[data-article-vue-list-content] .article-editorial-section-heading').count()===1);
  const oldFont=data.font;
  await page.evaluate(()=>document.documentElement.style.setProperty('--ph-font-size','20px'));
  data=await measure();
  check('Changing CMS size updates controls',data.font>oldFont&&Math.abs(data.font-data.bodyFont)<.1);
  await page.evaluate(()=>document.documentElement.style.setProperty('--ph-font-size','14px'));
  for(const [width,columns] of [[1440,3],[1024,3],[768,2],[390,1],[375,1],[320,1]]){
    await page.setViewportSize({width,height:1000});data=await measure();
    check(`No horizontal overflow at ${width}px`,!data.overflow);
    check(`Grid has ${columns} columns at ${width}px`,data.columns===columns);
    check(`Lead arrangement is responsive at ${width}px`,width<=991?data.body.top>=data.image.bottom:data.image.left>=data.body.right);
  }
  await page.setViewportSize({width:1440,height:1000});
  for(const model of ['boxed','underline','soft']){
    await page.goto(base+model+'.html');
    check(`${model} keeps default footer frame OFF`,await page.locator('.article-pagination--without-frame').count()===1);
    check(`${model} has 44px page controls`,await page.locator('.page-item.active .page-link').evaluate(element=>element.getBoundingClientRect().height)>=44);
  }
  await page.goto(base+'default.html');
  check('Read more remains opt-in on grid cards',await page.locator('.article-editorial-card .article-text-link').count()===0);
  check('Search and category stay option-driven',await page.locator('[data-article-filter]').count()===0);
  await page.goto(base+'settings.html');
  const settings=await page.evaluate(()=>{
    const card=document.querySelector('.article-editorial-card'),media=card.querySelector('.article-editorial-card__media'),frame=document.querySelector('.article-pagination'),input=document.querySelector('[name=search]'),button=input.parentElement.querySelector('button');
    const c=getComputedStyle(card),m=getComputedStyle(media),f=getComputedStyle(frame),lead=getComputedStyle(document.querySelector('.article-editorial-lead'));
    return {gap:button.getBoundingClientRect().left-input.getBoundingClientRect().right,searchRadius:parseFloat(getComputedStyle(input).borderTopLeftRadius),cardPadding:parseFloat(c.paddingLeft),cardBorder:parseFloat(c.borderTopWidth),cardBackground:c.backgroundColor,cardMinimum:parseFloat(c.minHeight),mediaHeight:media.getBoundingClientRect().height,mediaMargin:parseFloat(m.marginBottom),leadRadius:parseFloat(getComputedStyle(document.querySelector('.article-editorial-lead__media')).borderTopLeftRadius),divider:parseFloat(lead.borderBottomWidth),spacing:parseFloat(getComputedStyle(document.querySelector('.article-editorial-grid')).paddingTop),frameBorder:parseFloat(f.borderTopWidth),frameRadius:parseFloat(f.borderTopLeftRadius)};
  });
  check('Search gap/radius stay configurable',settings.gap===18&&settings.searchRadius===16);
  check('Edge-to-edge thumbnail removes outer padding and media gap',settings.cardPadding===0&&settings.mediaMargin===0);
  check('Card border/background settings remain active',settings.cardBorder===0&&settings.cardBackground==='rgb(246, 249, 252)');
  check('Desktop fixed height follows its setting',settings.cardMinimum===360);
  check('Thumbnail height and custom lead radius are honored',settings.mediaHeight===240&&settings.leadRadius===18);
  check('Lead divider and spacing follow settings',settings.divider===0&&settings.spacing===36);
  check('Footer frame remains explicitly configurable',settings.frameBorder===3&&settings.frameRadius===18);
  check('Read more position/icon follow settings',await page.locator('.article-editorial-read-more--right .fa-chevron-right').count()===6);
  for(const [width,height] of [[768,340],[390,320]]){
    await page.setViewportSize({width,height:1000});
    check(`Fixed card height is ${height}px at ${width}px`,await page.locator('.article-editorial-card').first().evaluate(element=>parseFloat(getComputedStyle(element).minHeight))===height);
    check(`Custom media radius remains 18px at ${width}px`,await page.locator('.article-editorial-lead__media').evaluate(element=>parseFloat(getComputedStyle(element).borderTopLeftRadius))===18);
  }
  await page.goto(base+'boxed.html');
  await page.evaluate(results=>{window.__editorialSourceResults=results;},{passed:checks.length,checks});
}
