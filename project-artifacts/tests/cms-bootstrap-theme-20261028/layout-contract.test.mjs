import assert from 'node:assert/strict';
import {readFileSync,readdirSync,existsSync} from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root='resources/views/themes';
const themes=readdirSync(root).filter(theme=>existsSync(path.join(root,theme,'cms/cms_layout.blade.php')));
test('every CMS layout loads the theme bridge after its theme and before page overrides',()=>{
  assert.ok(themes.length>0);
  for(const theme of themes){const source=readFileSync(path.join(root,theme,'cms/cms_layout.blade.php'),'utf8');assert.equal((source.match(/<link\b[^>]*cms-bootstrap-theme\.css[^>]*>/g)||[]).length,1,theme);assert.ok(source.indexOf('cms-bootstrap-theme.css')>source.indexOf('theme-responsive-typography.css'),theme);assert.ok(source.indexOf('cms-bootstrap-theme.css')<source.indexOf("@stack('css')"),theme);}
});
test('frontend and auth layouts do not load the CMS component bridge',()=>{
  for(const theme of themes)for(const section of ['frontend','auth']){const file=path.join(root,theme,section,section+'_layout.blade.php');if(existsSync(file))assert.ok(!readFileSync(file,'utf8').includes('cms-bootstrap-theme.css'),file);}
});
