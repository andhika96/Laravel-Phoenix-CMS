async page => {
    await page.getByRole('tab',{name:'Layout',exact:true}).click();
    await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-refined/qa/staged-layout.png'});
    await page.locator('summary').filter({hasText:'Sizing'}).click();
    await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-refined/qa/staged-sizing.png'});
    await page.getByRole('tab',{name:'Appearance',exact:true}).click();
    await page.locator('#headerMenuState').selectOption('active');
    await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-refined/qa/staged-active.png'});
    await page.locator('summary').filter({hasText:'Link shadow'}).click();
    await page.locator('#linkShadowEnabled').check();
    await page.locator('#linkShadowColor').scrollIntoViewIfNeeded();
    await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-refined/qa/staged-shadow.png'});
    await page.getByRole('tab',{name:'Behavior',exact:true}).click();
    await page.locator('[data-control=headerBehavior] [data-value=sticky]').click();
    await page.locator('#animateScroll').check();
    await page.locator('#transparentStart').check();
    await page.locator('[data-control=transparentColorMode] [data-value=custom]').click();
    await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-refined/qa/staged-behavior.png'});
    return {screenshotStates:['layout','sizing','active','shadow','behavior'],controls:await page.locator('#headerNavigationEditor input[id], #headerNavigationEditor select[id]').count()};
}
