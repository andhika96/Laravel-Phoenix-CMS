async (page) => {
  const output = 'D:/Laragon/www/laravel-13-phoenix/project-artifacts/sidebar-category-preview-20261113/';
  await page.reload();
  await page.setViewportSize({width: 1440, height: 800});
  await page.addStyleTag({path: output + 'proposal.css'});
  await page.evaluate(() => {
    document.querySelector('#sidebar').classList.add('ph-expanded');
    document.querySelector('#sidebar').style.transition = 'none';
    document.querySelector('#sidebar').style.animation = 'none';
  });
  await page.waitForFunction(() => document.querySelector('#sidebar').getBoundingClientRect().width >= 287.9);
  await page.evaluate(() => {
    window.previewPosition = () => document.documentElement.style.setProperty('--preview-sidebar-edge', document.querySelector('#sidebar').getBoundingClientRect().right + 'px');
    window.previewPosition();
  });
  const testing = page.locator('#sidebar .list-group-item[data-bs-target]');
  await testing.click();
  await page.waitForFunction(() => document.querySelector('.ph-submenu-container').classList.contains('show'));
  await page.screenshot({path: output + '01-expanded.png'});
  await page.screenshot({path: output + '01-expanded-detail.png', clip: {x: 0, y: 0, width: 360, height: 800}});
  await page.locator('#ph-lucent-sidebar-toggle').click();
  await page.waitForFunction(() => Math.abs(document.querySelector('#sidebar').getBoundingClientRect().width - 76) < .01);
  await page.evaluate(() => {
    window.previewPosition();
    document.querySelectorAll('.ph-custom-tooltip,.ph-floating-submenu').forEach(el => el.style.transition = 'none');
  });
  await page.locator('#sidebar a[href$="/dashboard"]').hover();
  await page.screenshot({path: output + '02-collapsed-tooltip.png'});
  await page.screenshot({path: output + '02-collapsed-tooltip-detail.png', clip: {x: 0, y: 0, width: 360, height: 800}});
  await testing.hover();
  await page.locator('.ph-floating-link').first().hover();
  await page.screenshot({path: output + '03-collapsed-submenu.png'});
  await page.screenshot({path: output + '03-collapsed-submenu-detail.png', clip: {x: 0, y: 0, width: 360, height: 800}});
  return await page.evaluate(() => {
    const rail = document.querySelector('#sidebar').getBoundingClientRect();
    const pop = document.querySelector('.ph-show-popover');
    const icon = pop.querySelector('.ph-floating-link > :first-child').getBoundingClientRect();
    const label = pop.querySelector('.ph-submenu-label').getBoundingClientRect();
    if (Math.abs(rail.right - pop.getBoundingClientRect().left) > 1) throw new Error('Panel tidak sejajar dengan batas sidebar');
    if (Math.abs(label.left - icon.right - 12) > 1) throw new Error('Jarak ikon dan label tidak 12px');
    return {category: document.querySelector('.ph-nav-category-label').textContent,
      sidebarRight: rail.right, popoverLeft: pop.getBoundingClientRect().left,
      popoverWidth: pop.getBoundingClientRect().width,
      iconTextGap: label.left - icon.right,
      separatorHeight: document.querySelector('.ph-nav-category').getBoundingClientRect().height};
  });
}
