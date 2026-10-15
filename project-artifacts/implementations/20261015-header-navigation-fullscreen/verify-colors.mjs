import assert from 'node:assert/strict';
import {writeFile} from 'node:fs/promises';
export async function verifyColors(page) {
    await page.goto('https://laravel-13-phoenix.aruna/awesome_admin/header-navigation');
    await page.locator('body.header-editor-open').waitFor();
    await page.waitForFunction(()=>document.querySelectorAll('.header-menu a').length>1);
    const backColor=await page.locator('.header-editor-back').evaluate(e=>getComputedStyle(e).color);
    await page.locator('[data-inspector-tab="style"]').click();
    async function setColor(id,value){await page.locator('#'+id).fill(value);await page.locator('#'+id).dispatchEvent('change');await page.locator('#'+id).blur();await page.waitForTimeout(350);}
    async function color(selector){return page.locator(selector).evaluate(e=>getComputedStyle(e).color);}
    await setColor('headerText','#123456');await setColor('linkColor','#654321');
    assert.equal(await color('.header-logo'),'rgb(18, 52, 86)');
    assert.equal(await color('.header-menu a:not(.is-active) >> nth=0'),'rgb(101, 67, 33)');
    await setColor('linkHover','#000000');await page.locator('.header-menu a:not(.is-active)').first().hover();await page.waitForTimeout(250);assert.equal(await color('.header-menu a:not(.is-active) >> nth=0'),'rgb(255, 255, 255)');
    await setColor('linkFocus','#ffffff');await page.mouse.move(10,10);await page.locator('.header-menu a:not(.is-active)').first().focus();await page.waitForTimeout(250);const focus=await color('.header-menu a:not(.is-active) >> nth=0');assert.equal(focus,'rgb(16, 24, 40)');
    await setColor('linkActive','#000000');assert.equal(await color('.header-menu a.is-active'),'rgb(255, 255, 255)');
    await page.locator('[data-inspector-tab="advanced"]').click();await page.locator('#animateScroll').evaluate(e=>e.closest('details').open=true);
    await page.locator('[data-control="headerBehavior"] [data-value="sticky"]').click();await page.locator('#animateScroll').check();await page.locator('#transparentStart').check();await page.locator('[data-control="transparentColorMode"] [data-value="custom"]').click();await setColor('transparentText','#abcdef');
    assert.equal(await color('.header-logo'),'rgb(171, 205, 239)');assert.equal(await color('.header-menu a:not(.is-active) >> nth=0'),'rgb(171, 205, 239)');
    await page.locator('#headerPreviewCondition').selectOption('50');await page.waitForTimeout(300);assert.equal(await color('.header-logo'),'rgb(18, 52, 86)');assert.equal(await color('.header-menu a:not(.is-active) >> nth=0'),'rgb(101, 67, 33)');
    assert.equal(await color('.header-editor-back'),backColor);
    await writeFile(new URL('./qa/colors-result.json',import.meta.url),JSON.stringify({result:'passed',checks:['header text custom color','normal link custom color','hover contrast text','focus contrast text','active contrast text','transparent custom text','scrolled restores normal text','CMS back link unchanged'],productionSettingsSaved:false},null,2));
    console.log('PASS: preview text/link colors and states follow settings; CMS link color unchanged');
}
