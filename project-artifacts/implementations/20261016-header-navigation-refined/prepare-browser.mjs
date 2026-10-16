import {readFile,writeFile} from 'node:fs/promises';
import {resolve} from 'node:path';
const dir='project-artifacts/implementations/20261016-header-navigation-refined';
const translations=JSON.parse(await readFile(`${dir}/staged/translations.json`,'utf8'));
const replacements={...translations,
 'aria-selected="false">Style</button>':'aria-selected="false">Appearance</button>',
 'aria-selected="false">Advanced</button>':'aria-selected="false">Behavior</button>',
 '<span>Effects</span>':'<span>Link shadow</span>',
 '<span>Config Preview</span>':'<span>Configuration</span>',
 '>Header menu position</label>':'>Scroll behavior</label>',
 '<strong>Live preview</strong>':'<strong>Preview</strong>'
};
const paths={};
for(const ext of ['css','js']) paths[ext]=resolve(`${dir}/staged/awesome-admin-header-navigation-fullscreen.${ext}`);
const script=`async page => {
    page.removeAllListeners('dialog');
    page.on('dialog', dialog => dialog.type() === 'beforeunload' ? dialog.accept() : dialog.dismiss());
    await page.unrouteAll({behavior:'wait'});
    await page.route('**/awesome-admin-header-navigation-fullscreen.css?*', route => route.fulfill({path:${JSON.stringify(paths.css)},contentType:'text/css'}));
    await page.route('**/awesome-admin-header-navigation-fullscreen.js?*', route => route.fulfill({path:${JSON.stringify(paths.js)},contentType:'application/javascript'}));
    await page.route('**/awesome_admin/header-navigation', async route => {
        if(route.request().method() !== 'GET') return route.fulfill({status:409,contentType:'application/json',body:JSON.stringify({success:false,message:'Isolated preview: saving is disabled.'})});
        const response=await route.fetch({ignoreHTTPSErrors:true});
        let body=await response.text();
        for(const [from,to] of Object.entries(${JSON.stringify(replacements)})) body=body.split(from).join(to);
        await route.fulfill({response,body});
    });
    await page.setViewportSize({width:1440,height:1000});
    await page.goto('https://laravel-13-phoenix.aruna/awesome_admin/header-navigation');
    await page.locator('.header-settings-tabbed').waitFor();
    await page.getByRole('tab',{name:'Appearance',exact:true}).click();
    await page.evaluate(()=>document.fonts.ready);
    await page.screenshot({path:${JSON.stringify(resolve(`${dir}/qa/staged-appearance.png`))}});
    return {isolated:true,tabs:await page.locator('[role=tab]').allTextContents(),sidebar:await page.locator('.mock-control-pane').boundingBox()};
}`;
await writeFile(`${dir}/preview.js`,script);
