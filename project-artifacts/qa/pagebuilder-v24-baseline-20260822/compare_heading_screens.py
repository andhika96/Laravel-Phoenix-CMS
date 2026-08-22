from pathlib import Path
from PIL import Image, ImageChops, ImageDraw


root = Path(__file__).resolve().parent
v23 = Image.open(root / "05-v23-heading.png").convert("RGB")
v24 = Image.open(root / "06-v24-heading.png").convert("RGB")
if v23.size != v24.size:
    raise SystemExit(f"Viewport mismatch: v2.3={v23.size}, v2.4={v24.size}")

label_height = 32
canvas = Image.new("RGB", (v23.width * 2, v23.height + label_height), "white")
canvas.paste(v23, (0, label_height))
canvas.paste(v24, (v23.width, label_height))
draw = ImageDraw.Draw(canvas)
draw.text((12, 9), "v2.3 Heading", fill="#172033")
draw.text((v23.width + 12, 9), "v2.4 Heading", fill="#172033")
draw.line((v23.width, 0, v23.width, canvas.height), fill="#5b4cf0", width=2)
canvas.save(root / "07-heading-side-by-side.png")

gray = ImageChops.difference(v23, v24).convert("L")
bbox = gray.getbbox()
differing_pixels = sum(1 for value in gray.get_flattened_data() if value != 0)
print({
    "size": v23.size,
    "difference_bbox": bbox,
    "differing_pixels": differing_pixels,
    "difference_percent": round(differing_pixels / (v23.width * v23.height) * 100, 6),
})
