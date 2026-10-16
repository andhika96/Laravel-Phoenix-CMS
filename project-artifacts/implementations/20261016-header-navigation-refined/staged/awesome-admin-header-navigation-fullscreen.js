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
    let wasDirty = false;
    function refreshDirtyStatus() {
        const dirty = isDirty();
        if (dirty === wasDirty || saveButton.disabled) return;
        wasDirty = dirty;
        setSaveStatus(dirty ? 'changed' : 'success', dirty ? 'Unsaved changes' : 'All changes saved');
    }
    root.addEventListener('header-navigation:saved', event => { saved = comparable(event.detail); wasDirty = false; });
    new MutationObserver(refreshDirtyStatus).observe(configOutput, { childList: true });
    active.addEventListener('change', refreshDirtyStatus);
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
    label.textContent = 'Scroll position';
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


    // Reuse the original controls and tab controller; moving DOM nodes preserves their listeners.
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
    root.querySelectorAll('[data-preview-device]').forEach(button => {
        button.setAttribute('aria-label', button.textContent.trim() + ' preview');
    });
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
    const animation = root.querySelector('#animateScroll').closest('.form-check');
    const transparency = root.querySelector('#transparentStart').closest('.form-check');
    transparency.before(animation);
    const scrollHelp = document.createElement('p');
    scrollHelp.className = 'header-setting-help';
    scrollHelp.textContent = 'Choose Fixed or Sticky, then enable scroll animation to use transparency and the after-scroll background.';
    behavior.querySelector('.header-setting-panel').append(scrollHelp);
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
    root.setAttribute('lang', 'en');
    root.classList.add('header-settings-tabbed');
    selectInspectorTab('style');
})();
