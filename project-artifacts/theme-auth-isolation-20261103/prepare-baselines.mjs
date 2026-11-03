import fs from 'node:fs';
import postcss from 'postcss';
const css=fs.readFileSync('public/assets/css/cms/cms-bootstrap-theme.css','utf8');
const dir='project-artifacts/theme-auth-isolation-20261103';
const baseline=postcss.parse(css);
baseline.walkRules(rule=>{if(rule.selector.startsWith('.list-group .list-group-item.active'))rule.remove();});
fs.writeFileSync(`${dir}/bridge-without-forced-list-color.css`,baseline.toString());
const backup=JSON.parse(fs.readFileSync('project-artifacts/sidebar-active-contrast-20261103/backup-manifest.json','utf8').replace(/^\uFEFF/,''));
fs.copyFileSync(backup.backup,`${dir}/bridge-original.css`);
