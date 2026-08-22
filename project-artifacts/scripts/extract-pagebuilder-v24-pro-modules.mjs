import { execFileSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import { parse, compileTemplate } from '@vue/compiler-sfc';
import { baseParse, NodeTypes } from '@vue/compiler-dom';

const projectRoot = path.resolve(import.meta.dirname, '..', '..');
const requestedTypes = process.argv.slice(2);
if (!requestedTypes.length) {
    throw new Error('Pass at least one Pro module type.');
}

const configJson = execFileSync('php', [
    '-r',
    '$modules = include "config/pagebuilder_elementor_v24_widgets.php"; echo json_encode($modules, JSON_THROW_ON_ERROR);',
], { cwd: projectRoot, encoding: 'utf8' });
const legacyModules = JSON.parse(configJson);
const sharedSettingsPath = path.join(projectRoot, 'public', 'js', 'pagebuilder_elementor_v24', 'widgets', 'pro', 'shared', 'Settings.vue');
const sharedCanvasPath = path.join(projectRoot, 'public', 'js', 'pagebuilder_elementor_v24', 'widgets', 'pro', 'shared', 'Canvas.vue');
const sharedFrontendPath = path.join(projectRoot, 'resources', 'views', 'pagebuilder_elementor_v24', 'partials', 'render_pro_widget.blade.php');
const sharedSettings = fs.readFileSync(sharedSettingsPath, 'utf8');
const sharedCanvas = fs.readFileSync(sharedCanvasPath, 'utf8');
const sharedFrontend = fs.readFileSync(sharedFrontendPath, 'utf8');

function branchType(expression) {
    const match = /^type\s*={2,3}\s*['"]([a-z_]+)['"]$/.exec(expression.trim());
    return match?.[1] ?? null;
}

function transformSfc(source, selectedType, filename) {
    const parsed = parse(source, { filename });
    if (parsed.errors.length || !parsed.descriptor.template) {
        throw new Error(`Cannot parse shared ${filename}: ${parsed.errors.join(', ')}`);
    }

    const block = parsed.descriptor.template;
    const ast = baseParse(block.content);
    const edits = [];
    const seen = new Set();

    function visit(node) {
        if (node.type !== NodeTypes.ELEMENT) return;

        const branch = node.props.find((prop) => {
            if (prop.type !== NodeTypes.DIRECTIVE || !['if', 'else-if'].includes(prop.name) || !prop.exp) return false;
            return branchType(prop.exp.content) !== null;
        });

        if (branch) {
            const type = branchType(branch.exp.content);
            seen.add(type);
            if (type === selectedType) {
                if (node.tag === 'template') {
                    const openEnd = node.loc.source.indexOf('>') + 1;
                    const closeStart = node.loc.source.lastIndexOf('</template>');
                    if (openEnd <= 0 || closeStart < 0) {
                        throw new Error(`Cannot unwrap ${selectedType} template branch in ${filename}`);
                    }
                    edits.push({ start: node.loc.start.offset, end: node.loc.start.offset + openEnd, value: '' });
                    edits.push({ start: node.loc.start.offset + closeStart, end: node.loc.end.offset, value: '' });
                } else {
                    edits.push({ start: branch.loc.start.offset, end: branch.loc.end.offset, value: '' });
                }
            } else {
                edits.push({ start: node.loc.start.offset, end: node.loc.end.offset, value: '' });
                return;
            }
        }

        for (const child of node.children ?? []) visit(child);
    }

    for (const child of ast.children) visit(child);
    if (!seen.has(selectedType)) {
        throw new Error(`${filename} has no template branch for ${selectedType}`);
    }

    let template = block.content;
    edits.sort((left, right) => right.start - left.start || right.end - left.end);
    for (const edit of edits) {
        template = template.slice(0, edit.start) + edit.value + template.slice(edit.end);
    }

    const result = source.slice(0, block.loc.start.offset) + template + source.slice(block.loc.end.offset);
    const verification = parse(result, { filename });
    if (verification.errors.length || !verification.descriptor.template) {
        throw new Error(`Extracted ${selectedType} ${filename} is invalid: ${verification.errors.join(', ')}`);
    }
    const compiled = compileTemplate({
        id: `v24-${selectedType}-${filename}`,
        filename,
        source: verification.descriptor.template.content,
    });
    if (compiled.errors.length) {
        throw new Error(`Extracted ${selectedType} ${filename} does not compile: ${compiled.errors.join(', ')}`);
    }
    const inertTemplate = baseParse(verification.descriptor.template.content).children.some(function containsInertTemplate(node) {
        if (node.type !== NodeTypes.ELEMENT) return false;
        if (node.tag === 'template' && node.props.length === 0) return true;
        return (node.children ?? []).some(containsInertTemplate);
    });
    if (inertTemplate) {
        throw new Error(`Extracted ${selectedType} ${filename} contains an inert plain template wrapper`);
    }

    return result;
}

function extractFrontend(source, selectedType) {
    const switchToken = '@switch($type)';
    const switchStart = source.indexOf(switchToken);
    const switchEnd = source.indexOf('@endswitch', switchStart);
    if (switchStart < 0 || switchEnd < 0) throw new Error('Shared Pro frontend switch is missing.');

    const switchBody = source.slice(switchStart + switchToken.length, switchEnd);
    const cases = [...switchBody.matchAll(/@case\('([a-z_]+)'\)/g)];
    const index = cases.findIndex((entry) => entry[1] === selectedType);
    if (index < 0) throw new Error(`Shared Pro frontend has no case for ${selectedType}`);

    const bodyStart = cases[index].index + cases[index][0].length;
    const bodyEnd = index + 1 < cases.length ? cases[index + 1].index : switchBody.length;
    const body = switchBody.slice(bodyStart, bodyEnd).replace(/\s*@break\s*$/, '\n');
    const result = source.slice(0, switchStart) + body + source.slice(switchEnd + '@endswitch'.length);
    if (/@switch\s*\(\$type\)|@case\s*\(/.test(result)) {
        throw new Error(`Extracted ${selectedType} frontend still contains multi-type switch branches.`);
    }
    return result;
}

for (const type of requestedTypes) {
    const module = legacyModules[type];
    if (!module || module.category !== 'pro') throw new Error(`Unknown legacy Pro module type: ${type}`);

    const definitionSource = path.join(projectRoot, 'public', module.definition);
    const slug = path.basename(path.dirname(module.definition.replaceAll('\\', '/')));
    const destination = path.join(projectRoot, 'resources', 'pagebuilder_elementor_v24', 'modules', 'widgets', 'pro', slug);
    if (fs.existsSync(path.join(destination, 'module.json'))) throw new Error(`Canonical module already exists: ${type}`);
    fs.mkdirSync(destination, { recursive: true });

    const definition = fs.readFileSync(definitionSource, 'utf8')
        .replace(/canvas:\s*["'][^"']*widgets\/pro\/shared\/Canvas\.vue["']/, `canvas: "/pagebuilder-elementor/v2.4/module-assets/${type}/canvas"`)
        .replace(/settings:\s*["'][^"']*widgets\/pro\/shared\/Settings\.vue["']/, `settings: "/pagebuilder-elementor/v2.4/module-assets/${type}/settings"`);

    fs.writeFileSync(path.join(destination, 'definition.js'), definition);
    fs.writeFileSync(path.join(destination, 'Canvas.vue'), transformSfc(sharedCanvas, type, 'Canvas.vue'));
    fs.writeFileSync(path.join(destination, 'Settings.vue'), transformSfc(sharedSettings, type, 'Settings.vue'));
    fs.writeFileSync(path.join(destination, 'frontend.blade.php'), extractFrontend(sharedFrontend, type));

    const manifest = {
        schemaVersion: 1,
        type,
        label: module.label,
        category: 'pro',
        icon: module.icon,
        order: Number(module.order),
        toolbox: module.toolbox !== false,
        assets: {
            definition: 'definition.js',
            canvas: 'Canvas.vue',
            settings: 'Settings.vue',
            view: 'frontend.blade.php',
        },
        advanced: {
            profile: 'widget',
            capabilities: [],
        },
        capabilities: type === 'form' ? ['form-submission'] : [],
    };
    fs.writeFileSync(path.join(destination, 'module.json'), `${JSON.stringify(manifest, null, 2)}\n`);
    process.stdout.write(`${type} -> ${path.relative(projectRoot, destination).replaceAll('\\', '/')}\n`);
}
