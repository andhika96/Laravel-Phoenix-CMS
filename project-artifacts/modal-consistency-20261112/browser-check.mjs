import { chromium } from 'playwright-core';
import { mkdir, writeFile } from 'node:fs/promises';
import path from 'node:path';

const out = path.dirname(new URL(import.meta.url).pathname.replace(/^\/(\w:)/, '$1'));
const phase = process.argv[2] || 'before';
const browser = await chromium.launch({ executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
const context = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: 1440, height: 1000 } });
const page = await context.newPage();
const errors = [];
page.on('pageerror', error => errors.push({ url: page.url(), message: error.message }));
const base = 'https://laravel-13-phoenix.aruna';
try {
  await page.goto(base + '/login', { waitUntil: 'domcontentloaded' });
  if (await page.locator('input[name=email]').count()) {
    await page.locator('input[name=email]').fill(process.env.CMS_MODAL_EMAIL);
    await page.locator('input[name=password]').fill(process.env.CMS_MODAL_PASSWORD);
    await page.locator('[type=submit]').first().click();
    await page.waitForTimeout(1800);
    if (new URL(page.url()).pathname.includes('login')) {
      await page.screenshot({path:path.join(out, phase + '-login.png')});
      console.log((await page.locator('body').innerText()).slice(-2200));
      throw new Error('Login did not leave login page');
    }
  }
  const rows = [];
  const routes = ['/awesome_admin/user','/awesome_admin/role','/awesome_admin/permission','/awesome_admin/smtp','/awesome_admin/menu/be/categorymenu','/awesome_admin/menu/fe/categorymenu','/awesome_admin/menu/fe/parentmenu','/awesome_admin/appearance','/awesome_admin/themes','/manage_article','/manage_article/templates','/manage_event','/manage_event/add','/manage_coverimage','/awesome_admin/config','/awesome_admin/header-navigation','/workflow/create'];
  for (const route of routes) {
    const response = await page.goto(base + route, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(500);
    rows.push(await page.evaluate(({ route, status }) => ({ route, status, finalPath: location.pathname, bodyClass: document.body.className, modals: [...document.querySelectorAll('.modal-dialog')].map(el => ({ id: el.closest('.modal')?.id, classes: el.className })), dialogs: [...document.querySelectorAll('dialog')].map(el => ({ id: el.id, classes: el.className })) }), {route, status: response.status()}));
  }
  await mkdir(out, { recursive: true });
  await writeFile(path.join(out, phase + '-inventory.json'), JSON.stringify({rows, errors}, null, 2));
  console.log(JSON.stringify({phase, routes:rows.length, statuses: rows.map(r=>[r.route,r.status]), missingStandard: rows.flatMap(r=>r.modals.filter(m=>!m.classes.includes('ph-modal-dialog')).map(m=>({route:r.route,...m}))), errors}, null, 2));
} finally { await browser.close(); }
