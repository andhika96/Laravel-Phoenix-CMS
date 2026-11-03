async page => {
 const context=await page.context().browser().newContext({ignoreHTTPSErrors:true});
 await context.route('**/__auth-qa/*.html',route=>route.fulfill({path:'rendered/'+new URL(route.request().url()).pathname.split('/').pop(),contentType:'text/html'}));
 const qa=await context.newPage();
 const errors=[];qa.on('pageerror',error=>errors.push(error.message));
 const cases=[];
 try {
  for(const template of ['default','card','split_left','split_right']) for(const name of ['login','signup','forgotpassword','resetpassword']){
   if(Date.now()>=Date.parse('2026-10-30T09:49:30Z'))throw Error('Deadline: stop QA');
   await qa.goto(`https://laravel-13-phoenix.aruna/__auth-qa/${template}-${name}.html`);
   for(const width of [1280,390]){
    await qa.setViewportSize({width,height:width===390?844:720});
    const result=await qa.evaluate(()=>{
     const button=document.querySelector('input[type=submit]');
     return {page:document.body.dataset.authPage,layout:document.body.dataset.authLayout,button:button?getComputedStyle(button).backgroundColor:null,css:[...document.querySelectorAll('link[rel=stylesheet]')].map(n=>n.href),overflow:document.documentElement.scrollWidth>innerWidth,width:innerWidth,formWidth:document.querySelector('.ph-form')?.getBoundingClientRect().width};
    });
    if(result.overflow)throw Error(`Horizontal overflow: ${template}/${name}/${width}`);
    if(result.css.some(url=>url.includes('/themes/')||url.includes('shared/phoenix-cms.css')))throw Error('CMS CSS leak');
    if(result.button!=='rgb(31, 166, 117)')throw Error(`Unexpected button: ${template}/${name} ${result.button}`);
    cases.push(result);
    if(name==='login')await qa.screenshot({path:`${template}-login-${width}.png`,fullPage:true});
   }
  }
  await qa.goto('https://laravel-13-phoenix.aruna/__auth-qa/card-login.html');
  const button=qa.locator('input[type=submit]');
  await button.hover();await qa.waitForTimeout(200);
  const hover=await button.evaluate(n=>({background:getComputedStyle(n).backgroundColor,color:getComputedStyle(n).color}));
  await button.focus();
  const focus=await button.evaluate(n=>({outline:getComputedStyle(n).outlineWidth,background:getComputedStyle(n).backgroundColor}));
  await qa.mouse.down();await qa.waitForTimeout(200);
  const active=await button.evaluate(n=>getComputedStyle(n).backgroundColor);
  await qa.mouse.move(1,1);await qa.mouse.up();
  await button.evaluate(n=>n.disabled=true);await qa.waitForTimeout(200);
  const disabled=await button.evaluate(n=>getComputedStyle(n).backgroundColor);
  return {cases,hover,focus,active,disabled,errors};
 } finally {await context.close();}
}
