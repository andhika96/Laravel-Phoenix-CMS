import datetime as dt,shutil
from pathlib import Path
base=Path(__file__).parent
p=base/'prepare_projects.py'
src=p.read_text(encoding='utf-8')
backup=Path(str(p)+'.bak_'+dt.datetime.now().strftime('%Y%m%d_%H%M%S')+'_retry-existing-projects')
shutil.copy2(p,backup)
old="""        if label=='New MG SSD Portable':
            row.update(status='filesystem-unreadable',error='Previous Windows error 1392 while creating project-artifacts. No further writes attempted.')
            inventory.append(row);continue
"""
assert old in src
src=src.replace(old,'')
compile(src,str(p),'exec')
p.write_text(src,encoding='utf-8')
print('Resume all currently existing roots; deleted roots remain skipped.')
