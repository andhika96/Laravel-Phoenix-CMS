async page => {
 const context=await page.context().browser().newContext({ignoreHTTPSErrors:true});
 await context.route('**/__auth-qa/*.html',route=>route.fulfill({path:'rendered/'+new URL(route.request().url()).pathname.split('/').pop(),contentType:'text/html'}));
 const results=[];
 try{
  for(const name of ['baseline-signup','split_left-signup']){
   const qa=await context.newPage();const errors=[];qa.on('pageerror',e=>errors.push(e.message));
   await qa.goto('https://laravel-13-phoenix.aruna/__auth-qa/'+name+'.html');
   await qa.waitForTimeout(300);
   results.push({name,errors});await qa.close();
  }
  const qa=await context.newPage();
  await qa.goto('https://laravel-13-phoenix.aruna/auth/login');
  const button=qa.locator('input[type=submit]');await button.focus();
  const focus=await button.evaluate(n=>({outline:getComputedStyle(n).outlineWidth,shadow:getComputedStyle(n).boxShadow}));
  const final=await qa.evaluate(()=>({page:document.body.dataset.authPage,primary:getComputedStyle(document.body).getPropertyValue('--auth-primary').trim(),themes:[...document.querySelectorAll('link[rel=stylesheet]')].filter(n=>n.href.includes('/themes/')).length}));
  await qa.setViewportSize({width:390,height:844});await qa.screenshot({path:'login-final-mobile.png',fullPage:true});
  return {recaptchaBaseline:results,focus,final};
 }finally{await context.close();}
}
