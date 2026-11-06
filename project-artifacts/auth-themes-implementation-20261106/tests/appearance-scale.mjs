import { chromium } from 'playwright-core';
import { readFile, writeFile } from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
const root = process.cwd();
const output = path.join(root,'project-artifacts/auth-themes-implementation-20261106');
const css = await readFile(path.join(root,'public/assets/css/cms/auth-appearance.css'),'utf8');
const bootstrap = await readFile(path.join(root,'public/assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css'),'utf8');
const browser = await chromium.launch({channel:'chrome',headless:true});
try {
  const page = await browser.newPage({viewport:{width:1280,height:720}});
  await page.setContent('<style>'+bootstrap+'</style><style>body{font-size:14px}h2{font-size:32px}.accordion-button{font-size:inherit}</style><style>'+css+'</style><div class="ph-auth-appearance"><div class="accordion"><h2 class="accordion-header"><button class="accordion-button"><span><strong>Colors & branding</strong><small class="d-block ph-auth-field-help mt-1">Logo, colors and control shape</small></span></button></h2></div><div class="ph-auth-setting-row"><label class="form-label">Supporting panel</label><input class="form-control"></div></div>');
  const metrics = await page.evaluate(()=>({
    body:parseFloat(getComputedStyle(document.body).fontSize),
    title:parseFloat(getComputedStyle(document.querySelector('strong')).fontSize),
    help:parseFloat(getComputedStyle(document.querySelector('small')).fontSize),
    headerHeight:document.querySelector('.accordion-button').getBoundingClientRect().height
  }));
  await writeFile(path.join(output,'appearance-scale-latest.json'),JSON.stringify(metrics,null,2));
  console.log(JSON.stringify(metrics));
  assert.ok(metrics.title<=metrics.body*1.15,'Section titles must follow form typography instead of the CMS h2 size');
  assert.ok(metrics.help<=metrics.body,'Descriptions must not exceed the body font size');
  assert.ok(metrics.headerHeight<95,'Closed sections must remain compact');
} finally { await browser.close(); }
