import datetime as dt,shutil
from pathlib import Path
base=Path(__file__).parent
p=base/'prepare_projects.py';src=p.read_text(encoding='utf-8')
shutil.copy2(p,Path(str(p)+'.bak_'+dt.datetime.now().strftime('%Y%m%d_%H%M%S')+'_newmg-vendor-scope'))
src=src.replace("    if rel.startswith('public/assets/plugins/')", "    if rel.startswith('aruna/system/'):return False\n    if any(p.endswith('_old') for p in parts):return False\n    if rel.startswith('assets/plugins/') and not rel.endswith('/ckfinder/config.php'):return False\n    if rel.startswith('public/assets/plugins/')")
src=src.replace("    inventory=[]\n    for label,value in ROOTS:", "    selected={str(Path(x)).lower() for x in sys.argv[1:]}\n    inventory=[]\n    for label,value in ROOTS:\n        if selected and str(Path(value)).lower() not in selected:continue")
src=src.replace("    dump(BASE/'inventory.json',inventory)", "    if selected and (BASE/'inventory.json').exists():\n        old_inventory=json.loads((BASE/'inventory.json').read_text(encoding='utf-8'))\n        updates={r['label']:r for r in inventory}\n        inventory=[updates.get(r['label'],r) for r in old_inventory]\n    dump(BASE/'inventory.json',inventory)")
compile(src,str(p),'exec');p.write_text(src,encoding='utf-8')
print('NewMG dependency/historical exclusions checked.')
