const fs = require('node:fs');
const path = require('node:path');
const {execFileSync} = require('node:child_process');
const assert = require('node:assert/strict');
const root = path.resolve(__dirname, '../../..');
const prefix = 'storage/framework/testing/disks/public/articles/load-test/test-load-articles/';
const files = execFileSync('git', ['diff','--name-only','--diff-filter=D','--',prefix], {cwd:root,encoding:'utf8'}).trim().split(/\r?\n/).filter(Boolean);
const restored = [];
for (const file of files) {
  assert.ok(file.startsWith(prefix) && file.endsWith('.svg'));
  const target = path.resolve(root,file);
  assert.ok(target.startsWith(path.resolve(root,prefix)+path.sep));
  assert.equal(fs.existsSync(target),false);
  fs.mkdirSync(path.dirname(target),{recursive:true});
  const bytes = execFileSync('git',['show','HEAD:'+file],{cwd:root});
  fs.writeFileSync(target,bytes);
  restored.push(file);
}
fs.writeFileSync(path.join(__dirname,'restored-testing-assets.json'),JSON.stringify(restored,null,2));
console.log(`${restored.length} tracked test assets restored after Storage::fake cleared the shared testing disk.`);
