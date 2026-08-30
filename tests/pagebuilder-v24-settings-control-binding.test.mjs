import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import { dirname, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');

test('every discovered v2.4 Settings control has a live consumer', () => {
    const result = spawnSync(
        process.execPath,
        ['project-artifacts/scripts/audit-pagebuilder-v24-control-bindings.mjs'],
        { cwd: root, encoding: 'utf8' },
    );

    assert.equal(result.status, 0, result.stderr);
    const report = JSON.parse(result.stdout);
    const gaps = report.rows.flatMap((row) => row.consumerless.map((control) => ({
        type: row.type,
        token: control.token,
        origins: control.origins,
    })));

    assert.equal(report.summary.modules, 53);
    assert.ok(report.summary.controls >= 1600, 'expected the complete Settings control surface');
    assert.deepEqual(gaps, []);
});
