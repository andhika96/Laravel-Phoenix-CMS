import {readFile, writeFile} from 'node:fs/promises';
import assert from 'node:assert/strict';
const view='resources/views/awesome_admin/header_navigation/awesome_admin_header_navigation.blade.php';
let blade=await readFile(view,'utf8');
const original=blade;
blade=blade.replace(/\{\{ t\('([^']*)'\) \}\}/g,'$1');
const translations={
 'Manage Header Navigation':'Header Navigation',
 'Background mengikuti container':'Background follows container',
 'Transparent sebelum discroll':'Transparent before scrolling',
 'Animasi berubah saat discroll':'Animate on scroll',
 'Ruang untuk ide yang lebih besar.':'Room for bigger ideas.',
 'Header yang tepat membantu pengunjung menemukan apa yang mereka butuhkan. Lihat perubahan navigasi Anda langsung di halaman contoh ini.':'Clear navigation helps visitors find what they need. Preview your header changes on this sample page.',
 'Parent menu dapat langsung dipakai sebagai link header.':'Parent menu items appear as links in your header.',
 'Dropdown/submenu nanti mengambil struktur dari menu frontend.':'Dropdowns use the structure of your frontend menus.',
 'Active, hover, dan focus state bisa dilihat di header preview.':'Explore active, hover, and focus states in the header preview.'
};
for(const [from,to] of Object.entries(translations)){assert(blade.includes(from),from);blade=blade.replaceAll(from,to);}
blade=blade.replace('aria-selected="false">Style</button>','aria-selected="false">Appearance</button>')
 .replace('aria-selected="false">Advanced</button>','aria-selected="false">Behavior</button>')
 .replace('<span>Effects</span>','<span>Link shadow</span>')
 .replace('<span>Config Preview</span>','<span>Configuration</span>')
 .replace('>Header menu position</label>','>Scroll behavior</label>')
 .replace('<strong>Live preview</strong>','<strong>Preview</strong>');
const ids=s=>[...s.matchAll(/\bid="([^"]+)"/g)].map(m=>m[1]).sort();
assert.deepEqual(ids(blade),ids(original),'Every existing DOM id is retained');
await writeFile('project-artifacts/implementations/20261016-header-navigation-refined/staged/awesome_admin_header_navigation.blade.php',blade);
await writeFile('project-artifacts/implementations/20261016-header-navigation-refined/staged/translations.json',JSON.stringify(translations));

const js='public/assets/js/awesome-admin-header-navigation-fullscreen.js';
let shell=await readFile(js,'utf8');
shell=shell.slice(0,shell.indexOf('    const inspector ='));
shell=shell.replace("label.textContent = 'Preview';","label.textContent = 'Scroll position';");
shell+=`
    // Reuse the original controls and tab controller; moving DOM nodes preserves their listeners.
    const inspector = root.querySelector('.inspector-content');
    const previewGroup = device.closest('.header-setting-group');
    previewGroup.hidden = true;
    const options = document.createElement('details');
    options.className = 'header-preview-options';
    const summary = document.createElement('summary');
    summary.textContent = 'Preview options';
    const optionsPanel = document.createElement('div');
    optionsPanel.className = 'header-preview-options-panel';
    options.append(summary, optionsPanel);
    optionsPanel.append(label, root.querySelector('#previewScaleStatus'), root.querySelector('#resetPreview'));
    toolbar.append(options);
    document.addEventListener('click', event => {
        if (!options.contains(event.target)) options.open = false;
    });
    options.addEventListener('keydown', event => {
        if (event.key === 'Escape') { options.open = false; summary.focus(); }
    });

    function primaryGroup(control, heading) {
        const target = root.querySelector('#' + control) || root.querySelector('[data-control="' + control + '"]');
        const group = target.closest('.header-setting-group');
        const section = document.createElement('section');
        section.className = 'header-setting-group header-setting-primary';
        const title = document.createElement('h2');
        title.className = 'header-inspector-heading';
        title.textContent = heading;
        section.append(title, group.querySelector('.header-setting-panel'));
        group.replaceWith(section);
        return section;
    }
    primaryGroup('logoPosition', 'Arrangement');
    const colors = primaryGroup('headerBg', 'Header');
    const behavior = primaryGroup('headerBehavior', 'Scroll behavior');
    const activation = active.closest('label');
    activation.classList.add('header-activation');
    behavior.querySelector('.header-setting-panel').prepend(activation);
    root.querySelectorAll('.box-control').forEach(box => {
        box.querySelector('.box-control-header').append(box.querySelector('.box-link-toggle'));
    });

    const stateGroup = colors.querySelector('.link-color-state-group');
    const stateList = stateGroup.querySelector('.link-color-state-list');
    const normal = root.querySelector('#linkColor').closest('.col-6');
    normal.className = 'header-property-row';
    normal.querySelector('label').textContent = 'Text';
    stateList.prepend(normal);
    const stateTitle = stateGroup.querySelector('.link-color-state-title');
    stateTitle.textContent = 'Menu';
    const stateSelect = document.createElement('select');
    stateSelect.id = 'headerMenuState';
    stateSelect.className = 'form-select form-select-sm';
    stateSelect.setAttribute('aria-label', 'Menu interaction state');
    stateSelect.innerHTML = '<option value="normal">Normal</option><option value="active">Active</option><option value="hover">Hover</option><option value="focus">Focus</option>';
    stateTitle.append(stateSelect);
    const stateHint = document.createElement('p');
    stateHint.className = 'header-setting-help';
    stateHint.setAttribute('aria-live', 'polite');
    stateList.after(stateHint);
    const hints = {
        normal: '', active: 'Style for the current page.',
        hover: 'Style when a pointer is over a menu item.', focus: 'Style when a menu item receives keyboard focus.'
    };
    const selectState = () => {
        normal.hidden = stateSelect.value !== 'normal';
        stateList.querySelectorAll('[data-link-color-state]').forEach(row => { row.hidden = row.dataset.linkColorState !== stateSelect.value; });
        stateHint.textContent = hints[stateSelect.value];
        stateHint.hidden = !stateHint.textContent;
    };
    stateSelect.addEventListener('change', selectState);
    selectState();
    for (const [id, text] of [['headerBg', 'Background'], ['scrolledBg', 'After scroll'], ['headerText', 'Text']]) {
        const input = root.querySelector('#' + id);
        input.closest('.col-6').classList.add('header-property-row');
        root.querySelector('label[for="' + id + '"]').textContent = text;
    }
    stateList.querySelectorAll('[data-link-color-state]').forEach(row => {
        row.querySelectorAll('.link-color-field').forEach((field, index) => {
            field.classList.add('header-property-row');
            const label = field.querySelector('label');
            label.textContent = index === 0 ? 'Background' : 'Border';
            label.classList.remove('visually-hidden');
        });
        const linked = row.querySelector('[data-linked-color]');
        linked.classList.add('header-color-match');
        linked.innerHTML = '<i class="fas fa-link" aria-hidden="true"></i><span>Match background</span>';
        const hint = document.createElement('p');
        hint.className = 'header-setting-help';
        hint.setAttribute('aria-live', 'polite');
        row.querySelector('.link-color-control').append(hint);
        const explainLinked = () => {
            const matches = linked.getAttribute('aria-pressed') === 'true';
            hint.textContent = matches ? 'Border follows the background. Unlink to edit.' : '';
            hint.hidden = !matches;
            linked.title = matches ? 'Unlink border from background' : 'Match border to background';
        };
        new MutationObserver(explainLinked).observe(linked, { attributes: true, attributeFilter: ['aria-pressed'] });
        explainLinked();
    });

    const tabs = [...root.querySelectorAll('[data-inspector-tab]')];
    root.querySelector('.inspector-tabs').addEventListener('keydown', event => {
        const index = tabs.indexOf(document.activeElement);
        if (index < 0 || !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
        event.preventDefault();
        const next = event.key === 'Home' ? 0 : event.key === 'End' ? tabs.length - 1
            : (index + (event.key === 'ArrowRight' ? 1 : -1) + tabs.length) % tabs.length;
        selectInspectorTab(tabs[next]);
        tabs[next].focus();
    });
    root.querySelectorAll('.segmented').forEach(group => {
        group.setAttribute('role', 'group');
        group.setAttribute('aria-label', group.previousElementSibling.textContent.trim());
        const sync = () => group.querySelectorAll('button').forEach(button => button.setAttribute('aria-pressed', String(button.classList.contains('is-active'))));
        group.addEventListener('click', sync);
        sync();
    });
    configOutput.setAttribute('tabindex', '0');
    root.querySelector('#headerNavigationSaveStatus').setAttribute('role', 'status');
    root.classList.add('header-settings-tabbed');
    selectInspectorTab('layout');
})();
`;
await writeFile('project-artifacts/implementations/20261016-header-navigation-refined/staged/awesome-admin-header-navigation-fullscreen.js',shell);

