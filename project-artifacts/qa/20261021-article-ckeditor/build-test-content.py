from pathlib import Path
from html import escape

out = Path(__file__).parent
image = '/storage/ckfinder/articles/qa-ckeditor-20261021-lake.png'
paragraphs = [
    'Perencanaan sistem yang andal dimulai dari pengalaman pembaca. Setiap keputusan mengenai tata letak, gambar, dan struktur informasi harus membantu orang menemukan gagasan utama tanpa kehilangan konteks. Tim perlu mencatat tujuan, mengamati hasil, dan memperbaiki bagian yang masih membingungkan berdasarkan bukti yang dapat diperiksa bersama.',
    'Konten panjang membutuhkan ritme yang terjaga. Paragraf menjelaskan satu gagasan, subjudul memberi arah, sedangkan ilustrasi menyediakan konteks visual yang tidak mudah disampaikan melalui kalimat saja. Jarak yang konsisten dan ukuran yang mengikuti pengaturan situs membuat pengalaman membaca tetap nyaman pada perangkat dengan ruang layar berbeda.',
    'Pengujian dilakukan dari sudut pandang penulis sekaligus pembaca. Format yang terlihat saat menyunting harus tetap bermakna setelah disimpan dan dibuka kembali. Karena itu gambar kecil, teks berwarna, kutipan, daftar bertingkat, serta tabel berisi informasi panjang perlu diperiksa secara bersamaan pada halaman yang benar-benar dirender aplikasi.',
    'Perubahan kecil sebaiknya dapat ditelusuri. Catatan hasil pemeriksaan membantu tim membedakan keadaan yang sudah terbukti dengan asumsi yang masih memerlukan percobaan. Ketika hasil tidak sesuai, telusuri keluaran editor, data tersimpan, dan aturan tampilan sebelum menyimpulkan bahwa sumber masalah berasal dari isi artikel.',
]
parts = ['<h2>Artikel Uji CKEditor — Panduan Membaca dan Merancang Sistem Andal</h2>', '<p>Artikel ini adalah <strong>konten pengujian</strong> bertanggal 21 Oktober 2026. Seluruh ilustrasi memakai satu aset lokal yang diunggah melalui CKEditor. Artikel ini sengaja memuat berbagai bentuk konten untuk pemeriksaan editor, penyimpanan, dan semua template detail.</p>',
'<h3>Format karakter dan tautan</h3><p><strong>Teks tebal</strong>, <i>teks miring</i>, <u>teks bergaris bawah</u>, <s>teks dicoret</s>, dan <code>kode_inline()</code>. <a href="/article">Tautan daftar artikel</a>.</p>',
'<p><span class="text-tiny">Ukuran tiny</span> · <span class="text-small">Ukuran small</span> · ukuran default · <span class="text-big">Ukuran big</span> · <span class="text-huge">Ukuran huge</span>.</p>',
'<p><span style="font-family:Georgia,serif;">Font Georgia sebagai format eksplisit</span> dan <span style="color:#b83232;">teks merah</span>.</p>',
'<p><mark class="marker-yellow">Marker kuning</mark> <mark class="marker-green">Marker hijau</mark> <mark class="marker-pink">Marker merah muda</mark> <mark class="marker-blue">Marker biru</mark> <mark class="pen-red">Pena merah</mark> <mark class="pen-green">Pena hijau</mark>.</p>',
'<h3>Alignment paragraf</h3>']
for align in ['left','center','right','justify']:
    parts.append(f'<p style="text-align:{align};">Alignment {align}: {paragraphs[0]}</p>')
parts += ['<p style="margin-left:40px;">Paragraf berindentasi satu tingkat.</p>',
'<h3>Daftar dan daftar tugas</h3><ul><li>Persiapan<ul><li>Tujuan dan pembaca<ul><li>Prioritas pertama</li></ul></li></ul></li><li>Pengujian</li></ul><ol><li>Rencanakan<ol><li>Ukur kondisi awal<ol><li>Simpan bukti</li></ol></li></ol></li><li>Evaluasi hasil</li></ol>',
'<ul class="todo-list"><li><label class="todo-list__label"><input type="checkbox" disabled="disabled" checked="checked"><span class="todo-list__label__description">Konten disiapkan</span></label></li><li><label class="todo-list__label"><input type="checkbox" disabled="disabled"><span class="todo-list__label__description">Tinjauan pembaca berikutnya</span></label></li></ul>',
'<h3>Kutipan dan kode</h3><blockquote><p>Sistem yang baik membantu orang memahami apa yang terjadi, bukan sekadar menyembunyikan kompleksitas.</p><p>Kutipan kedua tetap berada dalam blok yang sama.</p></blockquote>',
'<pre><code class="language-javascript">function ukurKualitas(hasil) {\n    return hasil.filter(item =&gt; item.terverifikasi);\n}</code></pre><hr>',
'<h3>Tabel dengan heading dan sel gabungan</h3><figure class="table" style="width:90%;"><table style="background-color:#eef7ff;border:2px solid #176b87;"><thead><tr><th>Area</th><th colspan="2">Pemeriksaan lintas perangkat</th></tr></thead><tbody><tr><th rowspan="2">Konten</th><td>Desktop</td><td>Tablet dan mobile</td></tr><tr><td>Struktur heading</td><td>Gambar dan caption</td></tr><tr><td>Hasil</td><td colspan="2">Sel gabungan horizontal</td></tr></tbody></table></figure>']
for index,(style,width) in enumerate([('',None),('image-style-align-center','50%'),('image-style-align-left','25%'),('image-style-align-right','25%'),('image-style-side',None),('image-style-block-align-left','50%'),('image-style-block-align-right','75%'),('image-style-align-center','100%')],1):
    parts.append(f'<h2>Bagian {index}: Menghubungkan konten dengan pengalaman pembaca</h2>')
    classes = 'image '+style+(' image_resized' if width else '')
    attrs = f' style="width:{width};"' if width else ''
    img = f'<img src="{image}" alt="Ilustrasi danau pegunungan — pengujian gambar {index}">'
    if index == 2: img = f'<a href="/article">{img}</a>'
    parts.append(f'<figure class="{classes}"{attrs}>{img}<figcaption>Gambar {index}: {style or "block default"}, ukuran {width or "original"}.</figcaption></figure>')
    for j in range(4): parts.append('<p>'+paragraphs[(index+j)%len(paragraphs)]+'</p>')
    if index == 3: parts.append(f'<p>Gambar inline <img src="{image}" width="80" height="45" alt="Ikon danau inline"> berada di tengah kalimat dan mempertahankan konteks paragraf.</p>')
parts += ['<h3>Pemeriksaan teks panjang dan penutup</h3><p>'+('tautan-panjang-tanpa-spasi-'*18)+'</p><h4>Kesimpulan pengujian</h4><p>Konten ini mencakup format karakter, heading, alignment, indentasi, daftar, checklist, kutipan, kode, tabel, gambar, caption, tautan gambar, dan ukuran gambar. Pemeriksaan akhir harus membandingkan hasil di ketiga template detail.</p>']
html='\n'.join(parts)
(out/'long-article-input.html').write_text(html,encoding='utf-8')
print('Approximate words:',len(html.split()),'HTML bytes:',len(html.encode()))
