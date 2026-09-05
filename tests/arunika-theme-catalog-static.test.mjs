import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const read = (file) => readFileSync(path.join(root, file), 'utf8');
const activeThemes = ['arunika_prism', 'arunika_aurora', 'arunika_lucent', 'arunika_equinox'];
const controller = read('app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Themes_Controller.php');
const themesSeeder = read('database/seeders_new/ThemesSeeder.php');
const settingsSeeder = read('database/seeders_new/ThemeSettingsSeeder.php');
const lucentCss = read('public/assets/css/themes/arunika_lucent/arunika_lucent.css');

test('CMS theme catalog exposes Prism-first order and removes Mosaic', () => {
    const manageable = controller.match(/MANAGEABLE_THEME_CODES\s*=\s*\[([\s\S]*?)\];/)?.[1] ?? '';
    const listedThemes = [...manageable.matchAll(/'([^']+)'/g)].map((match) => match[1]);

    assert.deepEqual(listedThemes, activeThemes);
    assert.doesNotMatch(controller, /arunika_mosaic/);
    assert.doesNotMatch(themesSeeder, /'theme_code'\s*=>\s*'arunika_mosaic'/);
    assert.match(settingsSeeder, /'theme_id'\s*=>\s*7/);
    assert.match(settingsSeeder, /'theme_code'\s*=>\s*'arunika_prism'/);
    assert.match(settingsSeeder, /'theme_name'\s*=>\s*'Arunika Prism'/);
});

test('Mosaic cleanup migration switches the default to Prism before deletion', () => {
    const migrationPath = 'database/migrations/2026_09_03_213000_remove_arunika_mosaic_theme.php';

    assert.equal(existsSync(path.join(root, migrationPath)), true, 'Missing Mosaic cleanup migration');

    const migration = read(migrationPath);
    assert.match(migration, /arunika_mosaic/);
    assert.match(migration, /arunika_prism/);
    assert.match(migration, /theme_settings/);
    assert.match(migration, /updateOrInsert/);
    assert.match(migration, /->delete\(\)/);
    assert.match(migration, /public function down\(\): void/);
});

test('Mosaic CMS runtime files are removed while article Mosaic templates remain independent', () => {
    const runtimePaths = [
        'resources/views/themes/arunika_mosaic',
        'public/assets/css/themes/arunika_mosaic',
        'public/assets/js/themes/arunika_mosaic',
        'public/assets/images/themes/previews/arunika-mosaic-theme-preview.png',
    ];

    for (const runtimePath of runtimePaths) {
        assert.equal(existsSync(path.join(root, runtimePath)), false, `${runtimePath} should be removed`);
    }

    assert.equal(
        existsSync(path.join(root, 'resources/views/article/templates/archive/mosaic-classic.blade.php')),
        true,
        'Article templates must not be removed with the CMS theme',
    );
});

test('collapsed Lucent toggle uses the same centered rail as menu icons', () => {
    const accountRule = lucentCss.match(
        /\.ph-theme-arunika-lucent \.ph-sidebar:not\(\.ph-expanded\) \.ph-lucent-sidebar-account\s*\{([\s\S]*?)\n\s*\}/,
    )?.[1] ?? '';
    const toggleRule = lucentCss.match(
        /\.ph-theme-arunika-lucent \.ph-sidebar:not\(\.ph-expanded\) \.ph-lucent-sidebar-toggle\s*\{([\s\S]*?)\n\s*\}/,
    )?.[1] ?? '';

    assert.match(accountRule, /grid-template-columns:\s*1fr\s*!important;/);
    assert.match(toggleRule, /grid-column:\s*1\s*!important;/);
    assert.match(toggleRule, /justify-self:\s*center;/);
});
