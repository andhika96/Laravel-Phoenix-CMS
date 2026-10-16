async page => {
    page.removeAllListeners('dialog');
    page.on('dialog', dialog => dialog.type() === 'beforeunload' ? dialog.accept() : dialog.dismiss());
    await page.unrouteAll({behavior:'wait'});
    await page.route('**/awesome-admin-header-navigation-fullscreen.css?*', route => route.fulfill({path:"D:\\Laragon\\www\\laravel-13-phoenix\\project-artifacts\\implementations\\20261016-header-navigation-refined\\staged\\awesome-admin-header-navigation-fullscreen.css",contentType:'text/css'}));
    await page.route('**/awesome-admin-header-navigation-fullscreen.js?*', route => route.fulfill({path:"D:\\Laragon\\www\\laravel-13-phoenix\\project-artifacts\\implementations\\20261016-header-navigation-refined\\staged\\awesome-admin-header-navigation-fullscreen.js",contentType:'application/javascript'}));
    await page.route('**/awesome_admin/header-navigation', async route => {
        if(route.request().method() !== 'GET') return route.fulfill({status:409,contentType:'application/json',body:JSON.stringify({success:false,message:'Isolated preview: saving is disabled.'})});
        const response=await route.fetch({ignoreHTTPSErrors:true});
        let body=await response.text();
        for(const [from,to] of Object.entries({"Manage Header Navigation":"Header Navigation","Background mengikuti container":"Background follows container","Transparent sebelum discroll":"Transparent before scrolling","Animasi berubah saat discroll":"Animate on scroll","Ruang untuk ide yang lebih besar.":"Room for bigger ideas.","Header yang tepat membantu pengunjung menemukan apa yang mereka butuhkan. Lihat perubahan navigasi Anda langsung di halaman contoh ini.":"Clear navigation helps visitors find what they need. Preview your header changes on this sample page.","Parent menu dapat langsung dipakai sebagai link header.":"Parent menu items appear as links in your header.","Dropdown/submenu nanti mengambil struktur dari menu frontend.":"Dropdowns use the structure of your frontend menus.","Active, hover, dan focus state bisa dilihat di header preview.":"Explore active, hover, and focus states in the header preview.","aria-selected=\"false\">Style</button>":"aria-selected=\"false\">Appearance</button>","aria-selected=\"false\">Advanced</button>":"aria-selected=\"false\">Behavior</button>","<span>Effects</span>":"<span>Link shadow</span>","<span>Config Preview</span>":"<span>Configuration</span>",">Header menu position</label>":">Scroll behavior</label>","<strong>Live preview</strong>":"<strong>Preview</strong>"})) body=body.split(from).join(to);
        await route.fulfill({response,body});
    });
    await page.setViewportSize({width:1440,height:1000});
    await page.goto('https://laravel-13-phoenix.aruna/awesome_admin/header-navigation');
    await page.locator('.header-settings-tabbed').waitFor();
    await page.getByRole('tab',{name:'Appearance',exact:true}).click();
    await page.evaluate(()=>document.fonts.ready);
    await page.screenshot({path:"D:\\Laragon\\www\\laravel-13-phoenix\\project-artifacts\\implementations\\20261016-header-navigation-refined\\qa\\staged-appearance.png"});
    return {isolated:true,tabs:await page.locator('[role=tab]').allTextContents(),sidebar:await page.locator('.mock-control-pane').boundingBox()};
}