import fs from 'node:fs';

const files = process.argv.slice(2);
if (!files.length) throw new Error('Pass at least one Settings.vue path.');

const component = `
            <component
                :is="editor.widgetAdvancedControls"
                :node="node"
                :responsive-device="editor.responsiveDevice"
                :elementor-choices="true"
                @responsive-device="editor.setResponsiveDevice"
                @choose-media="editor.chooseMedia(node.settings,$event)"
                @clear-media="editor.clearMedia(node.settings,$event)"
                @unavailable-ai="editor.showUnsupportedControlNotice('Animate With AI', 'AI service is not connected to this page builder.')"
            />
`;

function matchingDiv(source, start) {
    const tag = /<\/?div\b[^>]*>/gi;
    tag.lastIndex = start;
    let depth = 0;
    let match;

    while ((match = tag.exec(source))) {
        if (match[0].startsWith('</')) {
            depth -= 1;
            if (depth === 0) return { start: match.index, end: tag.lastIndex };
        } else {
            depth += 1;
        }
    }

    throw new Error('Advanced tab div is not balanced.');
}

for (const file of files) {
    const source = fs.readFileSync(file, 'utf8');
    const matches = [...source.matchAll(/<div\s+v-if="editor\.settingsTab\s*={2,3}\s*['"]advanced['"]"[^>]*>/g)];
    if (matches.length !== 1) throw new Error(`${file} has ${matches.length} Advanced tab roots.`);

    const opening = matches[0];
    const closing = matchingDiv(source, opening.index);
    const output = source.slice(0, opening.index + opening[0].length)
        + component
        + source.slice(closing.start);

    if (!output.includes('editor.widgetAdvancedControls')) throw new Error(`${file} did not receive the shared component.`);
    fs.writeFileSync(file, output);
    process.stdout.write(`${file}\n`);
}
