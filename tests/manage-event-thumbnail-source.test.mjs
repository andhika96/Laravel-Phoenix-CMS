import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const sourcePath = path.join(process.cwd(), 'public/assets/js/vue3/manage_event/vueV3-manage-event-form-2026.js');
const source = readFileSync(sourcePath, 'utf8');

function loadOptions() {
    const sandbox = {
        Vue: {
            createApp(options) {
                return { mount() { return options; } };
            },
            markRaw(value) { return value; },
        },
        document: { getElementById() { return { dataset: {} }; } },
        window: { VueDatePicker: {} },
        console,
    };

    vm.createContext(sandbox);
    vm.runInContext(`${source}\n;globalThis.__options = ManageEventFormVue3;`, sandbox, { filename: sourcePath });

    return sandbox.__options;
}

test('switching from a selected CKFinder thumbnail to upload clears the stale CKFinder state', () => {
    const options = loadOptions();
    const state = {
        thumbnailSource: 'ckfinder',
        thumbnailCkfinderLabel: 'Events / selected.jpg',
        thumbnailOriginalPreview: '',
        thumbnailFileSelected: false,
        showThumbnailRemove: true,
        form: {
            thumbnailPreview: '/storage/ckfinder/events/selected.jpg',
            thumbnail_ckfinder_url: '/storage/ckfinder/events/selected.jpg',
            remove_thumbnail: false,
        },
        $refs: { thumbnailInput: { value: '' } },
    };

    options.methods.setThumbnailSource.call(state, 'upload');

    assert.equal(state.thumbnailSource, 'upload');
    assert.equal(state.form.thumbnail_ckfinder_url, '');
    assert.equal(state.thumbnailCkfinderLabel, '');
    assert.equal(state.form.thumbnailPreview, '');
    assert.equal(state.showThumbnailRemove, false);
});
