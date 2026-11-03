async page => {
 const context=await page.context().browser().newContext({ignoreHTTPSErrors:true});
 let theme='';let variant='current';
 await context.route('**/dashboard?qa-theme=*',route=>route.fulfill({path:'rendered/'+theme+'.html',contentType:'text/html'}));
 await context.route('**/assets/css/cms/cms-bootstrap-theme.css*',route=>variant==='current'?route.continue():route.fulfill({path:variant==='original'?'bridge-original.css':'bridge-without-forced-list-color.css',contentType:'text/css'}));
 const qa=await context.newPage();const results=[];
 try{
  for(theme of ['arunika_prism','arunika_aurora','arunika_lucent','arunika_equinox','default','calm_green']){
   for(variant of ['original','theme-color','current']){
    await qa.setViewportSize({width:1440,height:900});
    await qa.goto('https://laravel-13-phoenix.aruna/dashboard?qa-theme='+theme,{waitUntil:'domcontentloaded'});
    await qa.locator('.ph-sidebar, .arv7-sidebar').first().waitFor({state:'attached'});
    await qa.addStyleTag({content:'*, *::before, *::after { transition: none !important; animation: none !important; }'});
    await qa.evaluate(()=>{
     const link=[...document.querySelectorAll('.ph-sidebar a, .arv7-sidebar a')].find(n=>n.getAttribute('href')?.endsWith('/dashboard'));
     if(!link)throw Error('Dashboard menu missing');link.classList.add('active');link.id='qa-active-menu';
     const styles=[...document.querySelectorAll('link[rel=stylesheet]')].map(n=>n.href);
     if(styles.some(n=>n.includes('/assets/css/auth/')))throw Error('Auth CSS leaked into CMS');
    });
    for(const mode of ['light','dark']){
     await qa.evaluate(mode=>{
      document.documentElement.dataset.bsTheme=mode;
      if(typeof applyMainColor==='function')applyMainColor('#9D00FF');else document.documentElement.style.setProperty('--ph-theme-primary','#9D00FF');
     },mode);await qa.waitForTimeout(200);
     const result=await qa.evaluate(()=>{
      const active=document.getElementById('qa-active-menu');const cs=getComputedStyle(active);
      const label=active.querySelector('.ph-nav-text,.arv7-parent-menu-name,span');
      return {body:document.body.className,color:cs.color,label:label?getComputedStyle(label).color:cs.color,background:cs.backgroundColor,authPrimary:getComputedStyle(document.body).getPropertyValue('--auth-primary').trim(),width:innerWidth};
     });
     results.push({theme,variant,mode,...result});
     if(variant==='current'&&mode==='light')await qa.screenshot({path:theme+'-desktop.png',fullPage:false});
    }
   }
  }
  return results;
 }finally{await context.close();}
}
