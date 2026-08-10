# Graphify Non-Blocking Pre-Push Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Membuat `git push` normal tidak lagi menunggu Graphify, sambil mempertahankan pemeriksaan pre-push sebagai opt-in eksplisit.

**Architecture:** `.githooks/pre-push` menjadi guard tipis yang default-nya langsung sukses. Jalur Graphify lama tetap dipakai tanpa modifikasi saat `GRAPHIFY_RUN_PRE_PUSH=1`, sehingga hook lain dan updater PowerShell tidak berubah.

**Tech Stack:** POSIX shell (Git for Windows), Node.js built-in test runner, Git CLI.

## Global Constraints

- Pertahankan `core.hooksPath=.githooks`.
- Jangan mengubah hook selain `pre-push`.
- Jangan membuat proses background atau menambah dependency.
- Backup setiap file existing sebelum dimodifikasi.
- Jangan stage atau commit `graphify-out` dan file backup.

---

### Task 1: Jadikan Graphify pre-push sebagai opt-in

**Files:**
- Create: `tests/graphify-pre-push-hook.test.mjs`
- Modify: `.githooks/pre-push`
- Modify: `scripts/graphify/README.md`

**Interfaces:**
- Consumes: environment variable `GRAPHIFY_RUN_PRE_PUSH` dan bridge `.githooks/graphify-sync <event>`.
- Produces: hook yang default-nya exit `0` tanpa bridge; opt-in memanggil bridge dengan event `pre-push`.

- [x] **Step 1: Buat backup file existing**

Salin `.githooks/pre-push` dan `scripts/graphify/README.md` ke file `.bak_YYYYMMDD_HHMMSS_graphify_nonblocking_push` tanpa memasukkannya ke Git.

- [x] **Step 2: Tulis behavior test yang gagal**

```js
import assert from 'node:assert/strict';
import { copyFileSync, existsSync, mkdtempSync, mkdirSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import path from 'node:path';
import { spawnSync } from 'node:child_process';
import test from 'node:test';

const projectRoot = process.cwd();

function createFixture() {
    const root = mkdtempSync(path.join(tmpdir(), 'graphify-pre-push-'));
    const hooks = path.join(root, '.githooks');
    const log = path.join(root, 'graphify-call.log');
    mkdirSync(hooks);
    spawnSync('git', ['init', '--quiet'], { cwd: root });
    copyFileSync(path.join(projectRoot, '.githooks/pre-push'), path.join(hooks, 'pre-push'));
    writeFileSync(path.join(hooks, 'graphify-sync'), '#!/bin/sh\nprintf "%s\\n" "$1" >> "$GRAPHIFY_TEST_LOG"\n');
    return { root, hook: path.join(hooks, 'pre-push'), log };
}

function runHook(fixture, environment = {}) {
    return spawnSync('sh', [fixture.hook], {
        cwd: fixture.root,
        encoding: 'utf8',
        env: { ...process.env, GRAPHIFY_TEST_LOG: fixture.log, ...environment },
    });
}

test('pre-push skips Graphify by default and runs it only when explicitly enabled', (t) => {
    const fixture = createFixture();
    t.after(() => rmSync(fixture.root, { recursive: true, force: true }));

    const normal = runHook(fixture);
    assert.equal(normal.status, 0, normal.stderr);
    assert.equal(existsSync(fixture.log), false);

    const optedIn = runHook(fixture, { GRAPHIFY_RUN_PRE_PUSH: '1' });
    assert.equal(optedIn.status, 0, optedIn.stderr);
    assert.equal(readFileSync(fixture.log, 'utf8'), 'pre-push\n');
});
```

- [x] **Step 3: Jalankan test untuk membuktikan RED**

Run: `node --test tests/graphify-pre-push-hook.test.mjs`

Expected: FAIL karena hook lama membuat `graphify-call.log` pada push normal.

- [x] **Step 4: Implementasikan guard minimal**

Tambahkan setelah shebang di `.githooks/pre-push`:

```sh
# Graphify pre-push is opt-in so a slow local graph update never delays Git.
[ "${GRAPHIFY_RUN_PRE_PUSH:-0}" = "1" ] || exit 0
```

Perbarui `scripts/graphify/README.md` agar `pre-push` disebut opt-in dan contoh PowerShell menggunakan:

```powershell
$env:GRAPHIFY_RUN_PRE_PUSH = '1'
git push origin main
Remove-Item Env:GRAPHIFY_RUN_PRE_PUSH
```

- [x] **Step 5: Jalankan focused verification untuk membuktikan GREEN**

Run: `node --test tests/graphify-pre-push-hook.test.mjs`

Expected: 1 PASS, 0 FAIL.

- [x] **Step 6: Verifikasi syntax dan integritas diff**

Run: `sh -n .githooks/pre-push .githooks/graphify-sync .githooks/post-merge .githooks/post-rewrite`

Run: `git diff --check`

Run: `git status --short`

Expected: seluruh command exit `0`; hanya design, plan, test, `.githooks/pre-push`, dan README yang relevan tampil sebagai perubahan, sedangkan backup serta `graphify-out` tidak di-stage.
