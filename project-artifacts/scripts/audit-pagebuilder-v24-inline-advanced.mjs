import fs from 'node:fs';
import { parse } from '@vue/compiler-sfc';
import { baseParse, NodeTypes } from '@vue/compiler-dom';

for (const file of process.argv.slice(2)) {
    const source = fs.readFileSync(file, 'utf8');
    const descriptor = parse(source, { filename: file }).descriptor;
    const ast = baseParse(descriptor.template.content);
    const candidates = [];

    function visit(node) {
        if (node.type !== NodeTypes.ELEMENT) return;
        const branch = node.props.find((prop) => (
            prop.type === NodeTypes.DIRECTIVE
            && prop.name === 'if'
            && prop.exp
            && /settingsTab.*advanced/.test(prop.exp.content)
        ));
        if (branch) candidates.push(node.loc.source);
        for (const child of node.children ?? []) visit(child);
    }

    for (const child of ast.children) visit(child);
    const advanced = candidates.sort((left, right) => right.length - left.length)[0] ?? '';
    const keyMatches = [
        ...advanced.matchAll(/node\.settings\.([A-Za-z_$][\w$]*)/g),
        ...advanced.matchAll(/(?:activeResponsiveKey|responsiveKey|sizeControlDisplayValue|sizeControlUnit|setResponsiveSetting)\([^\n]*?['"]([A-Za-z_$][\w$]*)['"]/g),
    ];
    const keys = [...new Set(keyMatches.map((match) => match[1]))].sort();
    const labels = [...new Set(
        [...advanced.matchAll(/<(?:summary|label|span)[^>]*>\s*([^<{\n][^<\n]{0,60})</g)]
            .map((match) => match[1].trim())
            .filter(Boolean),
    )];

    process.stdout.write(`\n${file} len=${advanced.length}\n`);
    process.stdout.write(`keys=${keys.join(',')}\n`);
    process.stdout.write(`labels=${labels.join(' | ')}\n`);
}
