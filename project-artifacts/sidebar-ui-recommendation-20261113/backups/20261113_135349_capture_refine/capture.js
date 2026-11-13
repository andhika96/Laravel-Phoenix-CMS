async (page) => {
  const output = 'D:/Laragon/www/laravel-13-phoenix/project-artifacts/sidebar-ui-recommendation-20261113/';
  await page.reload();
  await page.setViewportSize({width: 1440, height: 800});
  await page.addStyleTag({path: 'D:/Laragon/www/laravel-13-phoenix/project-artifacts/sidebar-category-preview-20261113/proposal.css'});
  await page.evaluate(() => {
    const sidebar = document.querySelector('#sidebar');
    sidebar.style.transition = 'none';
    sidebar.classList.add('ph-expanded');
    document.querySelector('.ph-nav-category').style.transition = 'none';
  });
  await page.waitForFunction(() => document.querySelector('#sidebar').getBoundingClientRect().width >= 287.9);
  await page.mouse.move(900, 700);
  await page.locator('#sidebar .list-group-item[data-bs-target]').click();
  await page.waitForFunction(() => document.querySelector('.ph-submenu-container').classList.contains('show'));
  await page.mouse.move(900, 700);
  await page.screenshot({path: output + '01-previous.png', clip: {x: 0, y: 0, width: 288, height: 800}});
  await page.addStyleTag({path: output + 'refined.css'});
  await page.evaluate(() => {
    // State contoh untuk membandingkan desain; tidak mengubah route atau data.
    document.querySelector('.ph-submenu-link').classList.add('preview-current');
    document.querySelector('.ph-submenu-link').setAttribute('aria-current', 'page');
    document.querySelector('.ph-floating-link').classList.add('preview-current');
    document.querySelector('.ph-floating-link').setAttribute('aria-current', 'page');
    document.querySelector('#sidebar .list-group-item[data-bs-target]').setAttribute('data-preview-active-descendant', 'true');
  });
  await page.screenshot({path: output + '02-recommended.png', clip: {x: 0, y: 0, width: 288, height: 800}});
  const expandedChecks = await page.evaluate(() => {
    const current = document.querySelector('.ph-submenu-link.preview-current');
    const parent = document.querySelector('#sidebar .list-group-item[data-bs-target]');
    const category = document.querySelector('.ph-nav-category');
    return [
      {name: 'expanded parent and selected page use different backgrounds', pass: getComputedStyle(current).backgroundColor !== getComputedStyle(parent).backgroundColor},
      {name: 'active page has a 3px indicator', pass: getComputedStyle(current, '::before').width === '3px'},
      {name: 'category keeps stored capitalization', pass: getComputedStyle(category).textTransform === 'none'},
      {name: 'category has a trailing divider', pass: getComputedStyle(category, '::after').height === '1px'},
      {name: 'submenu has a vertical hierarchy guide', pass: getComputedStyle(document.querySelector('.ph-submenu-container'), '::before').width === '1px'}
    ];
  });
  await page.locator('#ph-lucent-sidebar-toggle').click();
  await page.waitForFunction(() => document.querySelector('#sidebar').getBoundingClientRect().width === 76);
  await page.evaluate(() => document.documentElement.style.setProperty('--preview-sidebar-edge', document.querySelector('#sidebar').getBoundingClientRect().right + 'px'));
  await page.locator('#sidebar .list-group-item[data-bs-target]').hover();
  await page.locator('.ph-floating-link').first().hover();
  await page.screenshot({path: output + '03-recommended-collapsed.png', clip: {x: 0, y: 0, width: 360, height: 800}});
  const collapsedEvidence = await page.evaluate(() => {
    const sidebar = document.querySelector('#sidebar');
    const popover = document.querySelector('.ph-floating-submenu');
    const activeParent = document.querySelector('[data-preview-active-descendant]');
    const icon = popover.querySelector('.ph-floating-link > :first-child').getBoundingClientRect();
    const label = popover.querySelector('.ph-submenu-label').getBoundingClientRect();
    const arrow = getComputedStyle(popover, '::after');
    return {
      checks: [
        {name: 'panel body aligns with sidebar right edge', pass: Math.abs(sidebar.getBoundingClientRect().right - popover.getBoundingClientRect().left) < 1},
        {name: 'arrow remains visible and protrudes to the left', pass: arrow.display !== 'none' && parseFloat(arrow.left) < 0},
        {name: 'selected descendant is marked on the collapsed parent', pass: getComputedStyle(activeParent, '::before').width === '3px'},
        {name: 'icon and label keep a 12px gap', pass: Math.abs(label.left - icon.right - 12) < 1},
        {name: 'collapsed category remains a 1px divider', pass: document.querySelector('.ph-nav-category').getBoundingClientRect().height === 1},
        {name: 'page has no horizontal overflow', pass: document.documentElement.scrollWidth <= innerWidth}
      ],
      sidebarRight: sidebar.getBoundingClientRect().right,
      panelLeft: popover.getBoundingClientRect().left,
      iconTextGap: label.left - icon.right
    };
  });
  const checks = [...expandedChecks, ...collapsedEvidence.checks];
  const failure = checks.find(check => !check.pass);
  if (failure) throw new Error(failure.name);
  await page.evaluate(() => localStorage.removeItem('sidebar-state'));
  await page.reload();
  return {checks, viewport: '1440x800', demoSelection: 'Submenu Test 1', ...collapsedEvidence};
}
