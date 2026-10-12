async page => {
  await page.setViewportSize({width:1536,height:1100});
  await page.goto('http://127.0.0.1:8774/pagination-audit/index.html');
  await page.evaluate(()=>document.fonts.ready);
  await page.emulateMedia({reducedMotion:'reduce'});
  const measure=()=>page.evaluate(()=>[...document.querySelectorAll('.audit-cell')].map(cell=>{
    const read=element=>{const style=getComputedStyle(element),rect=element.getBoundingClientRect();return {width:rect.width,height:rect.height,border:[style.borderTopWidth,style.borderRightWidth,style.borderBottomWidth,style.borderLeftWidth],radius:style.borderTopLeftRadius,background:style.backgroundColor,color:style.color,padding:[style.paddingTop,style.paddingRight,style.paddingBottom,style.paddingLeft]};};
    return {template:cell.dataset.template,model:cell.dataset.model,normal:read(cell.querySelector('.page-item:not(.active):not(.disabled) .page-link')),active:read(cell.querySelector('.active .page-link')),disabled:read(cell.querySelector('.disabled .page-link')),frame:read(cell.querySelector('nav'))};
  }));
  const ssr=await measure();
  if(ssr.length!==15)throw new Error('Expected 5 templates x 3 models');
  await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20261009_minimal-reading-list-revamp/pagination-audit/comparison.png',fullPage:true});
  await page.evaluate(()=>document.querySelectorAll('nav').forEach(nav=>{nav.classList.remove('article-pagination--ssr');nav.classList.add('article-pagination--vue');}));
  const vue=await measure();
  const comparable=record=>({normal:record.normal,active:record.active,disabled:record.disabled,frame:{...record.frame,width:undefined}});
  const sameAcrossModes=ssr.every((record,index)=>JSON.stringify(comparable(record))===JSON.stringify(comparable(vue[index])));
  await page.evaluate(results=>{window.__paginationAudit=results;},{variants:['Minimal Underline','Classic Boxed','Soft Highlight'],cases:30,sameAcrossModes,ssr,vue});
  if(!sameAcrossModes)throw new Error('SSR and Vue CSS results differ');
}
