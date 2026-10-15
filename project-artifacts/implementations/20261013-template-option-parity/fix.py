from pathlib import Path
import shutil
b=Path('project-artifacts/backups/20261013_101843-template-option-parity')
for f in ['tests/manage-article-template-manager.test.mjs','tests/Feature/Article/ArticleTemplateOptionsTest.php']:
 p=Path(f); q=b/f;q.parent.mkdir(parents=True,exist_ok=True);shutil.copy2(p,q)
p=Path('tests/manage-article-template-manager.test.mjs');s=p.read_text();s=s.replace("'header', 'toolbar'","'header', 'display', 'toolbar'").replace("['header', 'shell']","['header', 'display', 'shell']");p.write_text(s)
for name in ['minimal-reading-list','editorial-journal','mosaic-magazine']:
 p=Path('resources/views/article/templates/archive/'+name+'.blade.php');s=p.read_text().replace('@endif@','@endif\n@');p.write_text(s)
