import { readdirSync } from 'node:fs';
import { spawnSync } from 'node:child_process';

const files = readdirSync('tests')
	.filter((file) => file.startsWith('arunika-prism-') && file.endsWith('.test.mjs'))
	.sort()
	.map((file) => `tests/${file}`);

const result = spawnSync(process.execPath, ['--test', ...files], { encoding: 'utf8' });
const output = `${result.stdout ?? ''}${result.stderr ?? ''}`;
const summary = output.match(/ℹ tests[\s\S]*?duration_ms \d+\.?\d*\s*/u)?.[0] ?? output.slice(-4000);
const failures = output.split(/\r?\n/u).filter((line) => line.includes('✖'));

if (failures.length > 0)
{
	console.log(failures.join('\n'));
}
console.log(summary);
process.exit(result.status ?? 1);
