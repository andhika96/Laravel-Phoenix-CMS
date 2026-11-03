async page => {
 await page.setViewportSize({width:1920,height:1080});
 const result=await page.evaluate(()=>{
  const link=document.querySelector('.ph-sidebar .list-group-item.active');
  if(!link)throw Error('Active sidebar item not found');
  const original={theme:document.documentElement.dataset.bsTheme,color:document.documentElement.style.getPropertyValue('--ph-theme-primary'),savedColor:localStorage.getItem('theme-color')};
  applyMainColor('#9D00FF');
  const matched=[];
  for(const sheet of document.styleSheets){
   if(!sheet.href?.includes('/assets/css/'))continue;
   try{for(const rule of sheet.cssRules){
    if(rule.selectorText&&link.matches(rule.selectorText)&&rule.style.getPropertyValue('color'))matched.push({source:sheet.href.split('?')[0],selector:rule.selectorText,color:rule.style.getPropertyValue('color'),priority:rule.style.getPropertyPriority('color')});
   }}catch{}
  }
  return {original,body:document.body.className,item:link.outerHTML,foreground:getComputedStyle(link).color,label:getComputedStyle(link.querySelector('.ph-nav-text')).color,background:getComputedStyle(link).backgroundColor,matched,styles:[...document.querySelectorAll('link[rel=stylesheet]')].map(n=>n.href)};
 });
 await page.screenshot({path:'dashboard-before.png',fullPage:false});
 return result;
}
