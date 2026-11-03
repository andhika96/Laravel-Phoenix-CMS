async page => {
 const errors=[];
 page.on('pageerror',error=>errors.push(error.message));
 const login=await page.evaluate(()=>({page:document.body.dataset.authPage,layout:document.body.dataset.authLayout,styles:[...document.querySelectorAll('link[rel=stylesheet]')].map(n=>n.href),primary:getComputedStyle(document.body).getPropertyValue('--auth-primary'),button:getComputedStyle(document.querySelector('.btn-submit-login')).backgroundColor,scroll:document.documentElement.scrollWidth>innerWidth}));
 await page.screenshot({path:'login-desktop.png',fullPage:true});
 await page.locator('[name=email]').fill('admin@aruna-dev.com');
 await page.locator('[name=password]').fill(process.env.PHOENIX_QA_PASSWORD);
 await page.locator('.btn-submit-login').click();
 await page.waitForURL(url=>!url.pathname.startsWith('/auth/'),{timeout:20000});
 await page.goto('https://laravel-13-phoenix.aruna/awesome_admin/appearance');
 await page.locator('#authPrimaryColor-login').waitFor();
 await page.waitForFunction(()=>document.querySelectorAll('[id^=authPrimaryColor-]').length===4);
 const appearance=await page.evaluate(()=>[...document.querySelectorAll('[id^=authPrimaryColor-]')].map(el=>({id:el.id,value:el.value,label:el.labels[0]?.textContent,visible:el.getBoundingClientRect().height>0,nuancesBelow:el.closest('.row').nextElementSibling.textContent.includes('Color Nuances')})));
 await page.screenshot({path:'appearance-primary-colors.png',fullPage:true});
 return {login,appearance,errors};
}
