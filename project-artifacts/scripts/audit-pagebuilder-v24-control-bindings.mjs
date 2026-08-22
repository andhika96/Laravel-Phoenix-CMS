import { readdirSync, readFileSync } from 'node:fs';
import { dirname, join, relative, resolve } from 'node:path';

const projectRoot = resolve(import.meta.dirname, '../..');
const moduleRoot = join(projectRoot, 'resources/pagebuilder_elementor_v24/modules');

function filesUnder(directory, predicate = () => true) {
    return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
        const path = join(directory, entry.name);
        if (entry.isDirectory()) return filesUnder(path, predicate);
        return predicate(path) ? [path] : [];
    });
}

function modules() {
    return filesUnder(moduleRoot, (path) => path.endsWith('module.json'))
        .map((path) => ({ directory: dirname(path), manifest: JSON.parse(readFileSync(path, 'utf8')) }))
        .sort((left, right) => left.manifest.type.localeCompare(right.manifest.type));
}

function source(path) {
    return readFileSync(path, 'utf8');
}

function occurrences(value, text) {
    if (!value) return 0;
    return text.split(value).length - 1;
}

function controlTokens(settingsSource) {
    const tokens = new Map();
    const add = (token, origin) => {
        if (!token) return;
        if (!tokens.has(token)) tokens.set(token, new Set());
        tokens.get(token).add(origin);
    };
    const patterns = [
        {
            origin: 'root-v-model',
            regex: /v-model(?:\.[\w-]+)*="(?:node\.settings|settings|s)\.([A-Za-z_$][\w$]*)/g,
        },
        {
            origin: 'nested-v-model',
            regex: /v-model(?:\.[\w-]+)*="(?:item|field|slide|review|button|entry|hotspot|metric)\.([A-Za-z_$][\w$]*)/g,
        },
        {
            origin: 'root-assignment',
            regex: /(?<![\w$])(?:node\.settings|settings|s)\.([A-Za-z_$][\w$]*)\s*=\s*/g,
        },
        {
            origin: 'control-key',
            regex: /\b(?:base|setting-key|url-key|prefix)=["']([A-Za-z_$][\w$]*)["']/g,
        },
        {
            origin: 'responsive-key',
            regex: /(?:activeValue|responsiveValue|setResponsiveSetting|activeResponsiveKey)\(\s*["']([A-Za-z_$][\w$]*)["']/g,
        },
    ];

    for (const { origin, regex } of patterns) {
        for (const match of settingsSource.matchAll(regex)) add(match[1], origin);
    }

    return [...tokens.entries()].map(([token, origins]) => ({ token, origins: [...origins].sort() }));
}

const sharedConsumerSource = [
    source(join(projectRoot, 'public/js/pagebuilder_elementor_v24/app.js')),
    ...filesUnder(join(projectRoot, 'app/Support/PageBuilderElementorV24'), (path) => path.endsWith('.php')).map(source),
    ...filesUnder(join(projectRoot, 'app/Http/Controllers/Web/PageBuilderElementorV24'), (path) => path.endsWith('.php')).map(source),
].join('\n');

function hasDynamicBackendConsumer(token) {
    const match = token.match(/^email2?(To|Subject|Content|From|FromName|ReplyTo|Cc|Bcc|ContentType)$/);
    return Boolean(match && sharedConsumerSource.includes(`$prefix.'${match[1]}'`));
}

const rows = modules().map(({ directory, manifest }) => {
    const settingsPath = join(directory, manifest.assets.settings);
    const settingsSource = source(settingsPath);
    const definitionSource = source(join(directory, manifest.assets.definition));
    const renderSource = [
        source(join(directory, manifest.assets.canvas)),
        source(join(directory, manifest.assets.view)),
        manifest.assets.runtime ? source(join(directory, manifest.assets.runtime)) : '',
    ].join('\n');

    const controls = controlTokens(settingsSource).map((control) => {
        const renderOccurrences = occurrences(control.token, renderSource);
        const settingsOccurrences = occurrences(control.token, settingsSource);
        const sharedOccurrences = occurrences(control.token, sharedConsumerSource);
        const defaultOccurrences = occurrences(control.token, definitionSource);
        const consumers = [];
        if (renderOccurrences > 0) consumers.push('canvas-frontend-runtime');
        if (settingsOccurrences > 1) consumers.push('settings-logic');
        if (sharedOccurrences > 0 || hasDynamicBackendConsumer(control.token)) consumers.push('editor-backend');

        return {
            ...control,
            declaredByDefinition: defaultOccurrences > 0,
            settingsOccurrences,
            renderOccurrences,
            sharedOccurrences,
            consumers,
        };
    });

    return {
        type: manifest.type,
        module: relative(projectRoot, directory).replaceAll('\\', '/'),
        controls,
        undeclared: controls.filter((control) => !control.declaredByDefinition),
        consumerless: controls.filter((control) => control.consumers.length === 0),
    };
});

const report = {
    summary: {
        modules: rows.length,
        controls: rows.reduce((sum, row) => sum + row.controls.length, 0),
        modulesWithUndeclaredControls: rows.filter((row) => row.undeclared.length > 0).length,
        undeclaredControls: rows.reduce((sum, row) => sum + row.undeclared.length, 0),
        modulesWithConsumerlessControls: rows.filter((row) => row.consumerless.length > 0).length,
        consumerlessControls: rows.reduce((sum, row) => sum + row.consumerless.length, 0),
    },
    rows,
};

console.log(JSON.stringify(report, null, 2));
