import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import {
    copyFileSync,
    existsSync,
    mkdtempSync,
    mkdirSync,
    readFileSync,
    rmSync,
    writeFileSync,
} from 'node:fs';
import { tmpdir } from 'node:os';
import path from 'node:path';
import test from 'node:test';

const projectRoot = process.cwd();

function createFixture()
{
    const root = mkdtempSync(path.join(tmpdir(), 'graphify-pre-push-'));
    const hooks = path.join(root, '.githooks');
    const log = path.join(root, 'graphify-call.log');

    mkdirSync(hooks);

    const initialized = spawnSync('git', ['init', '--quiet'], { cwd: root, encoding: 'utf8' });

    assert.equal(initialized.status, 0, initialized.stderr);

    copyFileSync(path.join(projectRoot, '.githooks/pre-push'), path.join(hooks, 'pre-push'));
    writeFileSync(
        path.join(hooks, 'graphify-sync'),
        '#!/bin/sh\nprintf "%s\\n" "$1" >> "$GRAPHIFY_TEST_LOG"\n',
    );

    return { root, hook: path.join(hooks, 'pre-push'), log };
}

function runHook(fixture, environment = {})
{
    return spawnSync('sh', [fixture.hook], {
        cwd: fixture.root,
        encoding: 'utf8',
        env: { ...process.env, GRAPHIFY_TEST_LOG: fixture.log, ...environment },
    });
}

test('pre-push skips Graphify by default and runs it only when explicitly enabled', (t) =>
{
    const fixture = createFixture();

    t.after(() => rmSync(fixture.root, { recursive: true, force: true }));

    const normal = runHook(fixture);

    assert.equal(normal.status, 0, normal.stderr);
    assert.equal(existsSync(fixture.log), false);

    const optedIn = runHook(fixture, { GRAPHIFY_RUN_PRE_PUSH: '1' });

    assert.equal(optedIn.status, 0, optedIn.stderr);
    assert.equal(readFileSync(fixture.log, 'utf8'), 'pre-push\n');
});
