from pathlib import Path
from PIL import Image, ImageChops, ImageDraw


ROOT = Path(__file__).resolve().parent
V23 = ROOT / "01-v23-desktop.png"
V24 = ROOT / "02-v24-desktop.png"
SIDE_BY_SIDE = ROOT / "03-desktop-side-by-side.png"
DIFF = ROOT / "04-desktop-diff.png"


v23 = Image.open(V23).convert("RGB")
v24 = Image.open(V24).convert("RGB")
if v23.size != v24.size:
    raise SystemExit(f"Viewport mismatch: v2.3={v23.size}, v2.4={v24.size}")

label_height = 32
canvas = Image.new("RGB", (v23.width * 2, v23.height + label_height), "white")
canvas.paste(v23, (0, label_height))
canvas.paste(v24, (v23.width, label_height))
draw = ImageDraw.Draw(canvas)
draw.text((12, 9), "v2.3 source", fill="#172033")
draw.text((v23.width + 12, 9), "v2.4 clone", fill="#172033")
draw.line((v23.width, 0, v23.width, canvas.height), fill="#5b4cf0", width=2)
canvas.save(SIDE_BY_SIDE)

delta = ImageChops.difference(v23, v24)
bbox = delta.getbbox()
gray = delta.convert("L")
differing_pixels = sum(1 for value in gray.getdata() if value != 0)
total_pixels = v23.width * v23.height

highlight = Image.new("RGB", v23.size, "white")
if bbox:
    mask = gray.point(lambda value: 255 if value else 0)
    highlight.paste((220, 38, 38), mask=mask)
highlight.save(DIFF)

print(
    {
        "size": v23.size,
        "difference_bbox": bbox,
        "differing_pixels": differing_pixels,
        "total_pixels": total_pixels,
        "difference_percent": round(differing_pixels / total_pixels * 100, 6),
    }
)
