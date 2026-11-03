async page => {
 const context=await page.context().browser().newContext({ignoreHTTPSErrors:true,viewport:{width:390,height:844}});
 let theme='';let baseline=false;const results=[];
 await context.route('**/dashboard?qa-theme=*',route=>route.fulfill({path:'rendered/'+theme+'.html',contentType:'text/html'}));
 await context.route('**/assets/css/cms/cms-bootstrap-theme.css*',route=>baseline?route.fulfill({path:'bridge-without-forced-list-color.css',contentType:'text/css'}):route.continue());
 const qa=await context.newPage();
 try{
  for(theme of ['arunika_prism','arunika_aurora','arunika_lucent','arunika_equinox','default','calm_green'])for(baseline of [true,false]){
   await qa.goto('https://laravel-13-phoenix.aruna/dashboard?qa-theme='+theme);
   await qa.addStyleTag({content:'*, *::before, *::after { transition: none !important; animation: none !important; }'});
   await qa.evaluate(()=>{
    const mobile=document.querySelector('.arv7-menu-mobile');
    const root=mobile||document.querySelector('.ph-sidebar');
    const link=[...root.querySelectorAll('a')].find(n=>n.getAttribute('href')?.endsWith('/dashboard'));
    if(!link)throw Error('Mobile Dashboard menu missing');link.classList.add('active');link.id='qa-mobile-menu';
   });
   for(const mode of ['light','dark']){
    await qa.evaluate(mode=>{document.documentElement.dataset.bsTheme=mode;if(typeof applyMainColor==='function')applyMainColor('#9D00FF');},mode);
    await qa.waitForTimeout(100);
    const colors=await qa.evaluate(()=>{
     const item=document.getElementById('qa-mobile-menu');const style=getComputedStyle(item);
     return {color:style.color,background:style.backgroundColor};
    });
    results.push({theme,baseline,mode,...colors});
   }
  }
  for(const result of results.filter(item=>!item.baseline)){
   const expected=results.find(item=>item.theme===result.theme&&item.mode===result.mode&&item.baseline);
   if(result.color!==expected.color||result.background!==expected.background)throw Error('Mobile menu differs from theme: '+JSON.stringify({result,expected}));
  }
  return results;
 }finally{await context.close();}
}
