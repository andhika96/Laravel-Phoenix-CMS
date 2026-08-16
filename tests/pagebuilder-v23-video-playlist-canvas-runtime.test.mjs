import assert from 'node:assert/strict';
import test from 'node:test';
import { readFileSync } from 'node:fs';

const root = 'D:/Laragon/www/laravel-13-phoenix';
const canvas = readFileSync(
    `${root}/public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue`,
    'utf8',
);

test('Video Playlist item title style is a callable method in the shared Canvas', () => {
    assert.match(canvas, /videoPlaylistItemTitleStyle\(videoPlaylistActiveItem\)/);
    assert.match(canvas, /videoPlaylistItemTitleStyle\(entry\)/);
    assert.doesNotMatch(canvas, /:style="videoPlaylistItemTitleStyle"/);

    const computedStart = canvas.indexOf('computed: {');
    const methodsStart = canvas.indexOf('methods: {', computedStart);
    assert.ok(computedStart >= 0 && methodsStart > computedStart, 'Canvas should expose computed and methods sections');

    const computedBlock = canvas.slice(computedStart, methodsStart);
    const methodsBlock = canvas.slice(methodsStart);
    assert.doesNotMatch(computedBlock, /videoPlaylistItemTitleStyle\(/);
    assert.match(methodsBlock, /videoPlaylistItemTitleStyle\(entry = \{\}\)/);
});
