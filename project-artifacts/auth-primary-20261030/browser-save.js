async page => {
 const keys=['login','signup','forgotPassword','resetPassword'];
 const before=await page.evaluate(async keys=>Object.fromEntries(await Promise.all(keys.map(async key=>[key,await(await fetch('/awesome_admin/appearance/pagethemesetting/'+key)).json()]))),keys);
 let result;
 try {
  await page.locator('#authPrimaryColor-login').fill('#6633CC');
  await page.locator('#authPrimaryColor-login').click();
  const picker=await page.evaluate(()=>({visible:getComputedStyle(document.querySelector('#clr-picker')).visibility,alphaVisible:document.querySelector('.clr-alpha')?getComputedStyle(document.querySelector('.clr-alpha')).display!=='none':false}));
  await page.keyboard.press('Escape');
  const responsePromise=page.waitForResponse(response=>response.url().includes('/appearance/update')&&response.request().method()==='POST');
  await page.locator('.btn-submit-data').click();
  const response=await responsePromise;
  const payload=await response.json();
  if(response.status()!==200||payload.success!==true)throw Error('Appearance save failed: '+JSON.stringify(payload));
  await page.reload();
  await page.locator('#authPrimaryColor-login').waitFor();
  if(await page.locator('#authPrimaryColor-login').inputValue()!=='#6633CC')throw Error('Saved color not restored into field');
  const context=await page.context().browser().newContext({ignoreHTTPSErrors:true});
  try {
   const auth=await context.newPage();const errors=[];auth.on('pageerror',e=>errors.push(e.message));
   await auth.goto('https://laravel-13-phoenix.aruna/auth/login');
   const login=await auth.evaluate(()=>({primary:getComputedStyle(document.body).getPropertyValue('--auth-primary').trim(),background:getComputedStyle(document.querySelector('input[type=submit]')).backgroundColor,text:getComputedStyle(document.querySelector('input[type=submit]')).color}));
   if(login.primary!=='#6633CC'||login.background!=='rgb(102, 51, 204)')throw Error('Saved color not applied to login');
   await auth.screenshot({path:'login-custom-primary.png',fullPage:true});
   const routes=[];
   for(const route of ['signup','forgotpassword']){
    const response=await auth.goto('https://laravel-13-phoenix.aruna/auth/'+route);
    routes.push(await auth.evaluate(status=>({status,page:document.body.dataset.authPage,primary:getComputedStyle(document.body).getPropertyValue('--auth-primary').trim(),styles:[...document.querySelectorAll('link[rel=stylesheet]')].map(n=>n.href)}),response.status()));
   }
   result={picker,saveStatus:response.status(),login,routes,errors};
  }finally{await context.close();}
 }finally{
  const restore=await page.evaluate(async colors=>{
   const data=new FormData();
   data.append('_token',document.querySelector('meta[name=csrf-token]').content);
   data.append('interface_theme','{}');data.append('interface_theme_options','{}');data.append('interface_primary_colors',JSON.stringify(colors));
   const response=await fetch('/awesome_admin/appearance/update',{method:'POST',body:data,headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
   return {status:response.status,body:await response.json()};
  },Object.fromEntries(keys.map(key=>[key,before[key].page_primary_color])));
  if(restore.status!==200||restore.body.success!==true)throw Error('RESTORE FAILED: '+JSON.stringify(restore));
  const after=await page.evaluate(async keys=>Object.fromEntries(await Promise.all(keys.map(async key=>[key,await(await fetch('/awesome_admin/appearance/pagethemesetting/'+key)).json()]))),keys);
  if(JSON.stringify(before)!==JSON.stringify(after))throw Error('Settings differ after restore');
  await page.reload();
 }
 return {...result,restored:true};
}
