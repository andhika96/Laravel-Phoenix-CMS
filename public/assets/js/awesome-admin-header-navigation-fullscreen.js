(() => {
    const root = document.getElementById('headerNavigationEditor');
    if (!root?.classList.contains('header-navigation-fullscreen')) return;

    // Escape the theme's scroll/transform containers while inheriting its font and color tokens.
    document.body.append(root);
    document.body.classList.add('header-editor-open');
    const themeShell = [...document.body.children].filter(element => element !== root && !['SCRIPT', 'STYLE', 'LINK'].includes(element.tagName) && !element.id.startsWith('clr-'));
    themeShell.forEach(element => { element.inert = true; });

    const configOutput = root.querySelector('#jsonOutput');
    const active = root.querySelector('#headerNavigationActive');
    const saveButton = root.querySelector('#saveHeaderNavigation');
    const dialog = root.querySelector('#headerEditorExitDialog');
    let destination = root.querySelector('[data-header-editor-exit]').href;
    let leaving = false;

    function comparable(payload) {
        const config = structuredClone(payload.config_json);
        // Device selection and effective box values are preview metadata, not edits.
        for (const box of Object.values(config.sizing || {})) {
            if (box && typeof box === 'object' && box.responsive) {
                for (const key of ['mode', 'preview_device', 'top', 'right', 'bottom', 'left', 'unit', 'linked']) delete box[key];
                for (const device of ['desktop', 'tablet', 'mobile']) {
                    if (JSON.stringify(box.responsive[device]) === JSON.stringify(box.responsive.all)) box.responsive[device] = null;
                }
            }
        }
        delete config.layout.left_menu_count;
        delete config.layout.right_menu_count;
        return JSON.stringify({ is_active: payload.is_active, config_json: config });
    }
    function snapshot() {
        return comparable({ is_active: active.checked, config_json: JSON.parse(configOutput.textContent) });
    }
    let saved = snapshot();
    const isDirty = () => snapshot() !== saved;
    root.addEventListener('header-navigation:saved', event => { saved = comparable(event.detail); });
    root.querySelectorAll('[data-header-editor-exit]').forEach(link => {
        link.addEventListener('click', event => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            if (saveButton.disabled) return;
            destination = link.href;
            if (isDirty()) dialog.showModal();
            else { leaving = true; location.assign(destination); }
        });
    });
    root.querySelector('#headerEditorKeepEditing').addEventListener('click', () => dialog.close());
    root.querySelector('#headerEditorDiscard').addEventListener('click', () => { leaving = true; location.assign(destination); });
    window.addEventListener('beforeunload', event => {
        if (!leaving && isDirty()) { event.preventDefault(); event.returnValue = ''; }
    });

    const stage = root.querySelector('#deviceStage');
    const frame = root.querySelector('#previewFrame');
    const viewport = root.querySelector('#previewViewport');
    const device = root.querySelector('#deviceMode');
    const toolbar = root.querySelector('.preview-toolbar-actions');
    const label = document.createElement('label');
    label.className = 'header-preview-condition';
    label.textContent = 'Preview';
    const condition = document.createElement('select');
    condition.id = 'headerPreviewCondition';
    condition.className = 'form-select form-select-sm';
    condition.setAttribute('aria-label', 'Preview scroll position');
    condition.innerHTML = '<option value="0">Page top</option><option value="50">After scroll</option>';
    label.append(condition);
    toolbar.append(label);
    condition.addEventListener('change', () => {
        stage.scrollTop = (Number(condition.value) / 100) * (stage.scrollHeight - stage.clientHeight);
    });
    stage.addEventListener('scroll', () => { condition.value = stage.scrollTop > 32 ? '50' : '0'; }, { passive: true });

    // Override the existing scaler: the scrollable stage must fit the visible editor area.
    window.removeEventListener('resize', updatePreviewScale);
    updatePreviewScale = function () {
        const widths = { desktop: 1440, tablet: 820, mobile: 430 };
        const width = widths[device.value] || widths.desktop;
        if (!frame.clientWidth || !frame.clientHeight) return;
        const scale = Math.min(1, frame.clientWidth / width);
        viewport.style.width = `${width}px`;
        viewport.style.height = `${frame.clientHeight / scale}px`;
        viewport.style.left = `${Math.max(0, (frame.clientWidth - width * scale) / 2)}px`;
        viewport.style.top = '0';
        viewport.style.setProperty('--preview-scale', scale.toFixed(4));
        root.querySelector('#previewScaleStatus').textContent = `${device.options[device.selectedIndex].text} · ${width}px · Fit ${Math.round(scale * 100)}%`;
    };
    new ResizeObserver(updatePreviewScale).observe(frame);
    window.addEventListener('resize', updatePreviewScale);
    // The original observer holds the original scaler; keep it from overwriting this shell's viewport.
    previewResizeObserver?.disconnect();
    updatePreviewScale();

    const inspector = root.querySelector('.inspector-content');
    const directory = document.createElement('nav');
    directory.className = 'header-settings-directory';
    directory.setAttribute('aria-label', 'Header settings');
    const detailHeading = document.createElement('div');
    detailHeading.className = 'header-settings-detail-heading';
    detailHeading.hidden = true;
    const back = document.createElement('button');
    back.type = 'button';
    back.className = 'header-settings-detail-back';
    back.innerHTML = '<i class="fas fa-arrow-left" aria-hidden="true"></i>';
    back.setAttribute('aria-label', 'Back to all settings');
    const title = document.createElement('strong');
    detailHeading.append(back, title);
    const deviceLabel = document.createElement('label');
    deviceLabel.className = 'header-settings-device';
    deviceLabel.append(document.createTextNode('Sedang mengatur'), device);
    inspector.before(deviceLabel);
    inspector.prepend(directory, detailHeading);
    const groups = [...root.querySelectorAll('.header-setting-group')];
    const entries = [
        ['Posisi & susunan', 'Logo, menu, dan lebar header', 'fa-columns', 'logoPosition'],
        ['Ukuran & jarak', 'Tinggi, padding, radius, dan margin', 'fa-ruler-combined', 'headerHeight'],
        ['Warna & state', 'Header, link, hover, dan focus', 'fa-palette', 'headerBg'],
        ['Bayangan link', 'Offset, blur, warna, dan inset', 'fa-clone', 'linkShadowEnabled'],
        ['Perilaku scroll', 'Fixed, sticky, dan transparansi', 'fa-arrows-alt-v', 'headerBehavior'],
        ['Konfigurasi', 'Lihat konfigurasi header', 'fa-code', 'jsonOutput']
    ];
    let selectedButton;
    function showDirectory() {
        root.querySelectorAll('[data-inspector-panel]').forEach(panel => { panel.hidden = true; });
        directory.hidden = false;
        detailHeading.hidden = true;
        selectedButton?.focus();
    }
    entries.forEach(([label, description, icon, control]) => {
        const target = root.querySelector(`#${control}`) || root.querySelector(`[data-control="${control}"]`);
        const group = target.closest('.header-setting-group');
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'header-settings-directory-item';
        button.dataset.settingsSection = control;
        button.innerHTML = `<i class="fas ${icon}" aria-hidden="true"></i><span><strong>${label}</strong><small>${description}</small></span><i class="fas fa-angle-right" aria-hidden="true"></i>`;
        button.addEventListener('click', () => {
            selectInspectorTab(group.closest('[data-inspector-panel]').dataset.inspectorPanel);
            groups.forEach(item => { item.hidden = item !== group; item.open = item === group; });
            directory.hidden = true;
            detailHeading.hidden = false;
            title.textContent = label;
            selectedButton = button;
            inspector.scrollTop = 0;
            back.focus();
        });
        directory.append(button);
    });
    back.addEventListener('click', showDirectory);
    root.querySelectorAll('.box-control').forEach(box => {
        const linked = box.querySelector('.box-link-toggle');
        if (linked) box.querySelector('.box-control-header').append(linked);
    });
    const stateList = root.querySelector('.link-color-state-list');
    const stateSwitcher = document.createElement('div');
    stateSwitcher.className = 'header-settings-state-switch';
    stateSwitcher.setAttribute('aria-label', 'Link color state');
    stateSwitcher.setAttribute('role', 'group');
    ['active', 'hover', 'focus'].forEach(state => {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = state.charAt(0).toUpperCase() + state.slice(1);
        button.dataset.colorState = state;
        button.addEventListener('click', () => {
            stateList.querySelectorAll('[data-link-color-state]').forEach(row => { row.hidden = row.dataset.linkColorState !== state; });
            stateSwitcher.querySelectorAll('button').forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        });
        stateSwitcher.append(button);
    });
    stateList.before(stateSwitcher);
    root.querySelector('.link-color-state-title').textContent = 'Warna menu berdasarkan kondisi';
    const stateHelp = document.createElement('p');
    stateHelp.className = 'header-setting-help';
    stateHelp.setAttribute('aria-live', 'polite');
    stateSwitcher.after(stateHelp);
    const descriptions = {
        active: ['Aktif', 'Tampilan menu untuk halaman yang sedang dibuka.'],
        hover: ['Ditunjuk', 'Tampilan menu saat pointer berada di atasnya.'],
        focus: ['Fokus', 'Tampilan menu saat dipilih dengan tombol Tab pada keyboard.']
    };
    stateSwitcher.querySelectorAll('button').forEach(button => {
        const [label, description] = descriptions[button.dataset.colorState];
        button.textContent = label;
        button.addEventListener('click', () => { stateHelp.textContent = description; });
    });
    stateList.querySelectorAll('[data-link-color-state]').forEach(row => {
        const labels = row.querySelectorAll('.link-color-field label');
        labels[0].textContent = 'Latar menu';
        labels[1].textContent = 'Garis tepi';
        labels.forEach(label => label.classList.remove('visually-hidden'));
        const linked = row.querySelector('[data-linked-color]');
        linked.classList.add('header-color-match');
        linked.innerHTML = '<i class="fas fa-link" aria-hidden="true"></i><span>Samakan dengan latar menu</span>';
        const hint = document.createElement('p');
        hint.className = 'header-setting-help';
        hint.setAttribute('aria-live', 'polite');
        row.querySelector('.link-color-control').append(hint);
        const explainLinked = () => {
            const matches = linked.getAttribute('aria-pressed') === 'true';
            hint.textContent = matches
                ? 'Warna garis tepi mengikuti latar menu. Matikan penyamaan untuk memilih warna berbeda.'
                : 'Warna garis tepi dapat dipilih sendiri.';
            linked.title = matches ? 'Matikan penyamaan warna' : 'Samakan warna garis tepi dengan latar menu';
        };
        new MutationObserver(explainLinked).observe(linked, { attributes: true, attributeFilter: ['aria-pressed'] });
        explainLinked();
    });
    stateSwitcher.firstElementChild.click();
    root.classList.add('header-settings-drilldown');
    showDirectory();
})();
