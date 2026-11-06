from pathlib import Path
root=Path(__file__).resolve().parents[2]
source=(root/'public/assets/plugins/fontawesome/5.15.3/svgs/solid/user-circle.svg').read_text(encoding='utf-8')
target=root/'public/assets/images/auth/defaults'
for name,background,foreground in [('forest','#E5F4E9','#1B5A3F'),('sage','#E9F0E4','#648563'),('sand','#FFF0DB','#A27447')]:
    destination=target/f'avatar-{name}.svg'
    assert not destination.exists(), str(destination)
    svg=source.replace('<path ',f'<rect width="496" height="512" fill="{background}"/><path fill="{foreground}" ',1)
    assert '<path fill=' in svg and 'Font Awesome Pro 5.15.3' in svg
    destination.write_text(svg,encoding='utf-8')
print('Created three default avatars from the installed Font Awesome user-circle asset.')
