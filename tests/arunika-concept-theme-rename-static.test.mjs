import assert from 'node:assert/strict';
import { existsSync, readFileSync, readdirSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const concepts = [
    {
        code: 'arunika_mosaic',
        displayName: 'Arunika Mosaic',
        preview: 'arunika-mosaic-theme-preview.png',
    },
    {
        code: 'arunika_aurora',
        displayName: 'Arunika Aurora',
        preview: 'arunika-aurora-theme-preview.png',
    },
    {
        code: 'arunika_prism',
        displayName: 'Arunika Prism',
        preview: 'arunika-prism-theme-preview.png',
    },
    {
        code: 'arunika_equinox',
        displayName: 'Arunika Equinox',
        preview: 'arunika-equinox-theme-preview.png',
    },
    {
        code: 'arunika_lucent',
        displayName: 'Arunika Lucent',
        preview: 'arunika-lucent-theme-preview.png',
    },
];

const activeRoots = [
    'app',
    'database/seeders_new',
    'public/assets/css/themes',
    'public/assets/images/themes/previews',
    'public/assets/js/themes',
    'public/mockups',
    'resources/views',
    'tests',
];

const versionTokens = [1, 2, 3].flatMap((version) => {
    const suffix = ['v', String(version)].join('');

    return [
        `arunika_${suffix}`,
        `arunika-${suffix}`,
        `Arunika ${suffix.toUpperCase()}`,
        `Arunika ${suffix}`,
    ];
});

const walkFiles = (relativeDirectory) => {
    const absoluteDirectory = path.join(root, relativeDirectory);

    return readdirSync(absoluteDirectory, { withFileTypes: true }).flatMap((entry) => {
        const relativePath = path.join(relativeDirectory, entry.name);

        return entry.isDirectory() ? walkFiles(relativePath) : [relativePath];
    });
};

test('concept-based Arunika runtime entry points exist', () => {
    for (const concept of concepts) {
        const paths = [
            `resources/views/themes/${concept.code}/cms/cms_layout.blade.php`,
            `resources/views/themes/${concept.code}/auth/auth_layout.blade.php`,
            `resources/views/themes/${concept.code}/frontend/frontend_layout.blade.php`,
            `resources/views/themes/${concept.code}/components/menu.blade.php`,
            `public/assets/css/themes/${concept.code}/${concept.code}.css`,
            `public/assets/js/themes/${concept.code}/${concept.code}.js`,
            `public/assets/images/themes/previews/${concept.preview}`,
        ];

        for (const relativePath of paths) {
            assert.equal(existsSync(path.join(root, relativePath)), true, `Missing ${relativePath}`);
        }
    }
});

test('Theme Manager and seeders use concept identities', () => {
    const controller = readFileSync(
        path.join(root, 'app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Themes_Controller.php'),
        'utf8',
    );
    const themesSeeder = readFileSync(path.join(root, 'database/seeders_new/ThemesSeeder.php'), 'utf8');

    for (const concept of concepts) {
        assert.match(controller, new RegExp(`['\"]${concept.code}['\"]`));
        assert.match(controller, new RegExp(concept.preview.replaceAll('.', '\\.')));
        assert.match(controller, new RegExp(concept.displayName));
        assert.match(themesSeeder, new RegExp(`'theme_code'\\s*=>\\s*'${concept.code}'`));
        assert.match(themesSeeder, new RegExp(`'theme_foldername'\\s*=>\\s*'${concept.code}'`));
        assert.match(themesSeeder, new RegExp(`'theme_name'\\s*=>\\s*'${concept.displayName}'`));
    }
});

test('the database identity migration is reversible and collision-safe', () => {
    const migrationPath = path.join(
        root,
        'database/migrations/2026_07_18_105900_rename_arunika_canvas_to_prism.php',
    );

    assert.equal(existsSync(migrationPath), true, 'Missing the Arunika Canvas-to-Prism migration');

    const migration = readFileSync(migrationPath, 'utf8');

    assert.match(migration, /public function up\(\): void/);
    assert.match(migration, /public function down\(\): void/);
    assert.match(migration, /DB::transaction/);
    assert.match(migration, /already belongs to another theme row/);
    assert.match(migration, /'from_code'\s*=>\s*'arunika_canvas'/);
    assert.match(migration, /'from_name'\s*=>\s*'Arunika Canvas'/);
    assert.match(migration, /'to_code'\s*=>\s*'arunika_prism'/);
    assert.match(migration, /'to_name'\s*=>\s*'Arunika Prism'/);
    assert.match(migration, /Schema::hasTable\('theme_settings'\)/);
});

test('active implementation no longer exposes version-based Arunika identities', () => {
    const violations = [];

    for (const activeRoot of activeRoots) {
        for (const relativePath of walkFiles(activeRoot)) {
            const normalizedPath = relativePath.replaceAll('\\\\', '/');
            const pathToken = versionTokens.find((token) => normalizedPath.toLowerCase().includes(token.toLowerCase()));

            if (pathToken) {
                violations.push(`${normalizedPath} (path contains ${pathToken})`);
                continue;
            }

            const absolutePath = path.join(root, relativePath);
            const extension = path.extname(relativePath).toLowerCase();
            const textExtensions = new Set(['.blade.php', '.css', '.html', '.js', '.json', '.mjs', '.php']);

            if (![...textExtensions].some((candidate) => normalizedPath.toLowerCase().endsWith(candidate))) {
                continue;
            }

            const contents = readFileSync(absolutePath, 'utf8');
            const contentToken = versionTokens.find((token) => contents.toLowerCase().includes(token.toLowerCase()));

            if (contentToken) {
                violations.push(`${normalizedPath} (content contains ${contentToken})`);
            }
        }
    }

    assert.deepEqual(violations, []);
});
