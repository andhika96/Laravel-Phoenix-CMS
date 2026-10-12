import datetime as dt,shutil
from pathlib import Path
base=Path(__file__).parent
path=base/'prepare_projects.py'
src=path.read_text(encoding='utf-8')
stamp=dt.datetime.now().strftime('%Y%m%d_%H%M%S')
shutil.copy2(path,Path(str(path)+'.bak_'+stamp+'_source-exclusions'))
src=src.replace("import collections\n","import collections\nimport datetime as dt\nimport shutil\n")
src=src.replace("'uploads','contents','output','outputs'}","'uploads','contents','output','outputs','fontawesome','archived','.history'}")
src=src.replace("    name=path.name.lower()\n", "    if rel.startswith('public/assets/plugins/') and not rel.endswith('/ckfinder/config.php'):return False\n    if rel.startswith('public/assets/master_data/gis'):return False\n    if any(p.startswith(('backup_','backup-','backups_')) for p in parts):return False\n    name=path.name.lower()\n")
src=src.replace("'.bak' in name or name.startswith('.env')","'.bak' in name or '.old.' in name or name.endswith('.old') or name.startswith('.env')")
src=src.replace("def dump(path,value):\n    path.write_text", "def dump(path,value):\n    if path.exists():\n        shutil.copy2(path,Path(str(path)+'.bak_'+dt.datetime.now().strftime('%Y%m%d_%H%M%S')+'_recheck'))\n    path.write_text")
src=src.replace("        art=root/'project-artifacts'/RUN\n        art.mkdir(parents=True,exist_ok=True)", "        art=root/'project-artifacts'/RUN\n        if label=='New MG SSD Portable':\n            row.update(status='filesystem-unreadable',error='Previous Windows error 1392 while creating project-artifacts. No further writes attempted.')\n            inventory.append(row);continue\n        try:art.mkdir(parents=True,exist_ok=True)\n        except OSError as error:\n            row.update(status='artifacts-unavailable',error=str(error));inventory.append(row);continue")
compile(src,str(path),'exec')
path.write_text(src,encoding='utf-8')
print('Scope exclusions refined; unreadable SSD excluded from writes.')
