import fs from 'node:fs';
import postcss from 'postcss';
if(Date.now()>=Date.parse('2026-10-30T09:50:00Z')) throw Error('Deadline reached');
const shared=fs.readFileSync('public/assets/css/shared/phoenix-cms.css','utf8');
const rules=postcss.parse(shared.slice(shared.indexOf('.ph-notice-toast\n')>=0?shared.indexOf('.ph-notice-toast\n'):shared.indexOf('.ph-notice-toast\r\n'),shared.indexOf('.ph-modal-create .ph-modal-dialog')));
rules.walkRules(rule=>rule.selectors=rule.selectors.map(selector=>'.ph-auth-page '+selector));
fs.appendFileSync('public/assets/css/auth/auth-base.css','\n/* Shared notice appearance, scoped to authentication. */\n'+rules.toString());
