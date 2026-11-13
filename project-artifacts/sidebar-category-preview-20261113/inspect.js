async (page) => {
  await page.screenshot({ path: 'D:/Laragon/www/laravel-13-phoenix/project-artifacts/sidebar-category-preview-20261113/before.png' });
  return await page.evaluate(() => ({
    theme: document.body.className,
    font: getComputedStyle(document.body).fontFamily,
    fontSize: getComputedStyle(document.body).fontSize,
    sidebarWidth: document.querySelector('#sidebar').getBoundingClientRect().width,
    collapsedWidth: getComputedStyle(document.body).getPropertyValue('--ph-sidebar-width-collapsed'),
    categories: [...document.querySelectorAll('.ph-nav-category')].map(el => ({html: el.outerHTML, display: getComputedStyle(el).display})),
    wrapper: document.querySelector('#sidebar-scroll-content').innerHTML,
    scripts: [...document.scripts].map(el => el.src).filter(Boolean)
  }));
}
