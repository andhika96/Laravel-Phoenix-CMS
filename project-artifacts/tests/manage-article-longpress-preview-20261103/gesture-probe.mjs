import {chromium,firefox} from 'playwright-core';
import path from 'node:path';
import {pathToFileURL} from 'node:url';
const engine=process.argv.includes('--firefox')?'firefox':'chrome';
const browser=await (engine==='firefox'?firefox:chromium).launch(engine==='chrome'?{channel:'chrome',headless:true}:{headless:true});
try {
  const page=await browser.newPage({viewport:{width:900,height:1500},hasTouch:true});
  await page.goto(pathToFileURL(path.resolve('project-artifacts/qa/manage-article-longpress-preview-20261103/rendered.html')).href);
  const frame=page.frames().find(f=>f.parentFrame()),title=page.frameLocator('iframe').locator('[data-layout="reading"] .mp-title').first();await title.waitFor();
  await frame.evaluate(()=>{
    window.probe=[];
    for(const type of ['pointerdown','pointermove','pointerup','pointercancel','click','scroll','blur','contextmenu'])window.addEventListener(type,e=>{window.probe.push({type:e.type,at:Math.round(performance.now()),pointerId:e.pointerId,pointerType:e.pointerType,primary:e.isPrimary,button:e.button,x:e.clientX,y:e.clientY,target:e.target.className?.baseVal||e.target.className||e.target.nodeName});},true);
  });
  await title.tap();await title.tap();
  async function hold(time) {
    await title.scrollIntoViewIfNeeded();const b=await title.boundingBox();
    await page.mouse.move(b.x+Math.min(b.width/2,70),b.y+Math.min(b.height/2,12));
    await page.mouse.down();await page.waitForTimeout(time);await page.mouse.up();
    console.log(JSON.stringify(await title.evaluate(el=>({expanded:el.getAttribute('aria-expanded'),pressed:el.getAttribute('aria-pressed'),selecting:el.closest('.mp-app').classList.contains('mp-selecting'),events:window.probe,focused:document.activeElement.className}))));
    await frame.evaluate(()=>window.probe=[]);
  }
  await hold(220);await title.click();await frame.evaluate(()=>window.probe=[]);await hold(850);
} finally {await browser.close();}
