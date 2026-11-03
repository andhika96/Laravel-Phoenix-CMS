async page => {
 const context=await page.context().browser().newContext({ignoreHTTPSErrors:true});
 await context.route('**/dashboard?qa-theme=*',route=>route.fulfill({path:'rendered/'+new URL(route.request().url()).searchParams.get('qa-theme')+'.html',contentType:'text/html'}));
 await context.route('**/__auth-isolation/*.html',route=>route.fulfill({path:'rendered/'+new URL(route.request().url()).pathname.split('/').pop(),contentType:'text/html'}));
 const qa=await context.newPage();const cms=[];const auth=[];const errors=[];
 qa.on('pageerror',e=>errors.push(e.message));
 const snapshot=()=>{
  const menu=document.querySelector('#qa-active-menu');const button=document.querySelector('#qa-primary-button');
  return {bodyColor:getComputedStyle(document.body).color,menuColor:getComputedStyle(menu).color,menuBg:getComputedStyle(menu).backgroundColor,buttonColor:getComputedStyle(button).color,buttonBg:getComputedStyle(button).backgroundColor,primary:getComputedStyle(document.body).getPropertyValue('--ph-primary')};
 };
 try{
  for(const theme of ['arunika_prism','arunika_aurora','arunika_lucent','arunika_equinox','default','calm_green']){
   await qa.goto('https://laravel-13-phoenix.aruna/dashboard?qa-theme='+theme);
   await qa.evaluate(()=>{
    const link=[...document.querySelectorAll('.ph-sidebar a, .arv7-sidebar a')].find(n=>n.getAttribute('href')?.endsWith('/dashboard'));
    link.classList.add('active');link.id='qa-active-menu';
    const button=document.createElement('button');button.id='qa-primary-button';button.className='btn btn-primary';button.textContent='QA';document.body.append(button);
    if([...document.querySelectorAll('link[rel=stylesheet]')].some(n=>n.href.includes('assets/css/auth/')))throw Error('CMS loaded auth CSS');
    if(getComputedStyle(document.body).getPropertyValue('--auth-primary').trim()!=='')throw Error('Auth primary leaked into CMS');
   });
   await qa.waitForTimeout(200);
   const before=await qa.evaluate(snapshot);
   for(const file of ['auth-base','login','signup','forgot-password','reset-password'])await qa.addStyleTag({url:'https://laravel-13-phoenix.aruna/assets/css/auth/'+file+'.css'});
   await qa.evaluate(()=>document.body.style.setProperty('--auth-primary','#FFFF00'));
   await qa.waitForTimeout(200);
   const after=await qa.evaluate(snapshot);
   if(JSON.stringify(before)!==JSON.stringify(after))throw Error('Auth styles affected CMS: '+theme+' '+JSON.stringify({before,after}));
   cms.push({theme,loadedAuthCss:false,authInjectionChangedCms:false,...before});
  }
  for(const template of ['default','card','split_left','split_right'])for(const name of ['login','signup','forgotpassword','resetpassword']){
   await qa.goto(`https://laravel-13-phoenix.aruna/__auth-isolation/auth-${template}-${name}.html`);
   const result=await qa.evaluate(()=>({page:document.body.dataset.authPage,template:document.body.dataset.authLayout,styles:[...document.querySelectorAll('link[rel=stylesheet]')].map(n=>n.href.split('?')[0]),cmsVariable:getComputedStyle(document.body).getPropertyValue('--cms-bs-primary').trim(),button:getComputedStyle(document.querySelector('input[type=submit]')).backgroundColor}));
   if(result.styles.some(url=>url.includes('/assets/css/themes/')||url.includes('/assets/css/cms/')||url.includes('shared/phoenix-cms')))throw Error('CMS CSS leaked into auth');
   if(result.cmsVariable!=='')throw Error('CMS variable leaked into auth');
   if(result.button!=='rgb(31, 166, 117)')throw Error('Auth primary changed: '+JSON.stringify(result));
   auth.push(result);
  }
  return {cms,auth,errors};
 }finally{await context.close();}
}
