async page => {
 const errors=[];
 page.on('pageerror',error=>errors.push(error.message));
 await page.reload();
 await page.locator('.ph-sidebar .list-group-item.active').waitFor();
 const original=await page.evaluate(()=>({theme:document.documentElement.dataset.bsTheme,style:document.documentElement.getAttribute('style'),sidebar:document.getElementById('sidebar').className,colorAttribute:document.documentElement.dataset.phThemeColor}));
 const results=[];
 try{
  await page.evaluate(()=>{
   const group=document.createElement('div');group.id='qa-regular-list';group.className='list-group';
   group.innerHTML='<a class="list-group-item active">Regular Bootstrap list</a>';
   group.style.cssText='position:fixed;top:0;left:-9999px';document.body.append(group);
  });
  for(const mode of ['light','dark']){
   await page.evaluate(mode=>document.documentElement.dataset.bsTheme=mode,mode);
   for(const color of ['#1FA675','#9D00FF','#1DA1F2','#FF5733','#FFC107','#E91E63','#6C5CE7','#C7CCD8']){
    await page.evaluate(color=>applyMainColor(color),color);await page.waitForTimeout(200);
    const result=await page.evaluate(()=>{
     const link=document.querySelector('.ph-sidebar .list-group-item.active');
     const rgb=color=>{const canvas=document.createElement('canvas');canvas.width=1;canvas.height=1;const ctx=canvas.getContext('2d');ctx.fillStyle=color;ctx.fillRect(0,0,1,1);return [...ctx.getImageData(0,0,1,1).data].slice(0,3);};
     const luminance=color=>rgb(color).map(n=>n/255).map(n=>n<=.04045?n/12.92:((n+.055)/1.055)**2.4).reduce((total,n,index)=>total+n*[.2126,.7152,.0722][index],0);
     const style=getComputedStyle(link);const label=getComputedStyle(link.querySelector('.ph-nav-text'));
     const bg=luminance(style.backgroundColor);const fg=luminance(label.color);const ratio=(Math.max(bg,fg)+.05)/(Math.min(bg,fg)+.05);
     const regular=getComputedStyle(document.querySelector('#qa-regular-list a'));
     return {mode:document.documentElement.dataset.bsTheme,primary:getComputedStyle(document.documentElement).getPropertyValue('--ph-theme-primary').trim(),color:label.color,background:style.backgroundColor,contrast:Number(ratio.toFixed(2)),regularColor:regular.color,regularBackground:regular.backgroundColor};
    });
    if(result.contrast<4.5)throw Error('Sidebar contrast too low: '+JSON.stringify(result));
    if(result.color!==(mode==='light'?'rgb(23, 23, 23)':'rgb(244, 247, 245)'))throw Error('Sidebar did not use Lucent text: '+JSON.stringify(result));
    if(result.regularColor!=='rgb(255, 255, 255)')throw Error('Regular Bootstrap list changed: '+JSON.stringify(result));
    results.push(result);
   }
  }
  await page.evaluate(()=>{document.documentElement.dataset.bsTheme='light';applyMainColor('#9D00FF');});
  await page.setViewportSize({width:1920,height:1080});await page.waitForTimeout(200);
  await page.screenshot({path:'dashboard-after.png',fullPage:false});
  const active=page.locator('.ph-sidebar .list-group-item.active').first();await active.hover();await page.waitForTimeout(200);
  const hover=await active.evaluate(n=>({color:getComputedStyle(n).color,background:getComputedStyle(n).backgroundColor}));
  await active.focus();
  const focus=await active.evaluate(n=>({color:getComputedStyle(n).color,background:getComputedStyle(n).backgroundColor}));
  await page.setViewportSize({width:390,height:844});
  await page.locator('.ph-mobile-sidebar-trigger').click();
  await page.waitForTimeout(250);
  const mobile=await active.evaluate(n=>({color:getComputedStyle(n).color,background:getComputedStyle(n).backgroundColor,width:document.documentElement.scrollWidth,viewport:innerWidth}));
  if(mobile.color!=='rgb(157, 0, 255)'||mobile.width>mobile.viewport)throw Error('Mobile sidebar did not preserve its theme color: '+JSON.stringify(mobile));
  await page.screenshot({path:'sidebar-mobile-after.png',fullPage:false});
  return {palette:results,hover,focus,mobile,errors};
 }finally{
  await page.evaluate(original=>{
   document.getElementById('qa-regular-list')?.remove();
   document.documentElement.dataset.bsTheme=original.theme;
   if(original.style===null)document.documentElement.removeAttribute('style');else document.documentElement.setAttribute('style',original.style);
   if(original.colorAttribute===undefined)delete document.documentElement.dataset.phThemeColor;else document.documentElement.dataset.phThemeColor=original.colorAttribute;
  },original);
  await page.setViewportSize({width:1920,height:1080});
  await page.reload();
 }
}
