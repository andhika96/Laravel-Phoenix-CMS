import { existsSync, readFileSync, readdirSync } from 'node:fs';
import path from 'node:path';

const root = path.resolve(import.meta.dirname, '../../..');
const pairs = [];

function versionedPath(value) {
  return value
    .replaceAll('PageBuilderElementorV23', 'PageBuilderElementorV24')
    .replaceAll('pagebuilder_elementor_v23', 'pagebuilder_elementor_v24')
    .replaceAll('pagebuilder-elementor-v23', 'pagebuilder-elementor-v24')
    .replaceAll('pagebuilder-editor-redesign-v23', 'pagebuilder-editor-redesign-v24')
    .replaceAll('pagebuilder-editor-v23', 'pagebuilder-editor-v24')
    .replaceAll('pagebuilder-v23', 'pagebuilder-v24');
}

function versionedContent(value, replaceNumber = true) {
  let converted = value
    .replaceAll('Page_Builder_Elementor_V23', 'Page_Builder_Elementor_V24')
    .replaceAll('PageBuilderElementorV23', 'PageBuilderElementorV24')
    .replaceAll('pagebuilder_elementor_v23', 'pagebuilder_elementor_v24')
    .replaceAll('pagebuilder-elementor-v23', 'pagebuilder-elementor-v24')
    .replaceAll('pagebuilder-editor-redesign-v23', 'pagebuilder-editor-redesign-v24')
    .replaceAll('pagebuilder-editor-v23', 'pagebuilder-editor-v24')
    .replaceAll('pagebuilder-v23', 'pagebuilder-v24')
    .replaceAll('V23', 'V24')
    .replaceAll('v23', 'v24');

  if (replaceNumber) {
    converted = converted.replaceAll('2\\.3', '2\\.4').replaceAll('2.3', '2.4');
  }
  return converted;
}

function filesUnder(relative) {
  const absolute = path.join(root, relative);
  return readdirSync(absolute, { withFileTypes: true }).flatMap((entry) => {
    if (entry.name.includes('.bak')) return [];
    const child = path.join(relative, entry.name);
    return entry.isDirectory() ? filesUnder(child) : [child.replaceAll('\\', '/')];
  });
}

for (const [sourceDir, targetDir] of [
  ['app/Http/Controllers/Web/PageBuilderElementorV23', 'app/Http/Controllers/Web/PageBuilderElementorV24'],
  ['app/Http/Requests/Page_Builder_Elementor_V23', 'app/Http/Requests/Page_Builder_Elementor_V24'],
  ['app/Models/PageBuilderElementorV23', 'app/Models/PageBuilderElementorV24'],
  ['app/Support/PageBuilderElementorV23', 'app/Support/PageBuilderElementorV24'],
  ['public/js/pagebuilder_elementor_v23', 'public/js/pagebuilder_elementor_v24'],
  ['resources/views/pagebuilder_elementor_v23', 'resources/views/pagebuilder_elementor_v24'],
]) {
  for (const source of filesUnder(sourceDir)) {
    const relative = source.slice(sourceDir.length + 1);
    pairs.push({ source, target: `${targetDir}/${versionedPath(relative)}`, replaceNumber: true });
  }
}

pairs.push(
  { source: 'app/Mail/PageBuilderElementorV23FormMail.php', target: 'app/Mail/PageBuilderElementorV24FormMail.php', replaceNumber: true },
  { source: 'config/pagebuilder_elementor_v23_widgets.php', target: 'config/pagebuilder_elementor_v24_widgets.php', replaceNumber: true },
  { source: 'database/migrations/2026_08_19_120000_create_pagebuilder_elementor_v23_form_datasets_table.php', target: 'database/migrations/2026_08_22_210800_create_pagebuilder_elementor_v24_form_datasets_table.php', replaceNumber: true },
  { source: 'public/assets/css/frontend_elementor_v23.css', target: 'public/assets/css/frontend_elementor_v24.css', replaceNumber: true },
  { source: 'public/assets/css/pagebuilder_elementor_v23.css', target: 'public/assets/css/pagebuilder_elementor_v24.css', replaceNumber: true },
  { source: 'public/mockups/pagebuilder-editor-redesign-prototype-v2.3.html', target: 'public/mockups/pagebuilder-editor-redesign-prototype-v2.4.html', replaceNumber: true },
  { source: 'resources/data/pagebuilder_elementor_v23_shapes.json', target: 'resources/data/pagebuilder_elementor_v24_shapes.json', replaceNumber: false },
  { source: 'resources/views/emails/pagebuilder-elementor-v23-form-text.blade.php', target: 'resources/views/emails/pagebuilder-elementor-v24-form-text.blade.php', replaceNumber: true },
);

const missing = [];
const mismatched = [];
for (const pair of pairs) {
  const target = path.join(root, pair.target);
  if (!existsSync(target)) {
    missing.push(pair.target);
    continue;
  }
  const source = readFileSync(path.join(root, pair.source), 'utf8');
  const actual = readFileSync(target, 'utf8');
  if (actual !== versionedContent(source, pair.replaceNumber)) {
    mismatched.push(pair.target);
  }
}

const expectedTargets = new Set(pairs.map(({ target }) => target.replaceAll('\\', '/')));
const targetRoots = [
  'app/Http/Controllers/Web/PageBuilderElementorV24',
  'app/Http/Requests/Page_Builder_Elementor_V24',
  'app/Models/PageBuilderElementorV24',
  'app/Support/PageBuilderElementorV24',
  'public/js/pagebuilder_elementor_v24',
  'resources/views/pagebuilder_elementor_v24',
];
const extras = targetRoots.flatMap(filesUnder).filter((file) => !expectedTargets.has(file));

const result = {
  mappedFiles: pairs.length,
  missing,
  mismatched,
  unexpectedVersionOwnedFiles: extras,
  passed: missing.length === 0 && mismatched.length === 0 && extras.length === 0,
};

console.log(JSON.stringify(result, null, 2));
if (!result.passed) process.exitCode = 1;
