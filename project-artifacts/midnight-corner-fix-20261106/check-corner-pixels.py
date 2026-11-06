from pathlib import Path
from PIL import Image
import json,sys
root=Path(__file__).parent
phase=sys.argv[1] if len(sys.argv)>1 else 'after'
results=[]
for width in (320,820,1280):
    image=Image.open(root/phase/f'panel-{width}.png').convert('RGB')
    w,h=image.size
    green=(35,129,91)
    background=(6,11,8)
    checks=[('upper_outer_curve',(w-62,4),background),('lower_outer_curve',(w-6,48),background),('right_edge_below_notch',(w-6,76),green),('left_of_inner_curve',(w-72,28),green)]
    for name,point,expected in checks:
        actual=image.getpixel(point)
        results.append({'viewport':width,'point':name,'actual':actual,'expected':expected,'passed':all(abs(a-b)<=2 for a,b in zip(actual,expected))})
(root/phase/'pixel-checks.json').write_text(json.dumps(results,indent=2))
failed=[r for r in results if not r['passed']]
print(f'{len(results)-len(failed)}/{len(results)} corner contour checks passed ({phase}).')
for failure in failed: print(failure)
assert not failed
