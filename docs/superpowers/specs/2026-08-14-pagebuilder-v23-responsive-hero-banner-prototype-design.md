# Page Builder v2.3 Responsive Hero Banner Prototype Design

**Tanggal:** 2026-08-14

**Project:** Laravel 13 Phoenix — Page Builder Elementor v2.3

## Context

Klien membutuhkan hero banner yang tidak dapat diwakili secara aman oleh background image ditambah beberapa widget dengan absolute positioning manual. Komposisi dapat berubah antar-klien: jumlah button 1–3, urutan Title dan Subtitle berbeda, tindakan button dapat berupa link, video popup, atau image popup, dan aset hero dapat berbeda pada Desktop, Tablet, dan Mobile.

Prototype awal di `project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype` sudah membuktikan dua perilaku positioning: Grouped dan Independent. Perluasan berikutnya tetap berada di prototype terisolasi. Source produksi Page Builder v2.3 tidak diubah pada fase ini.

Runtime MG5 GT telah diverifikasi: tombol **Watch Video** membuka modal berisi YouTube iframe. Source aktif v2.3 juga sudah memiliki `RepeaterList`, `LinkControl`, video URL normalization, CKFinder-backed media selection, serta image/video lightbox. Implementasi produksi nantinya harus memakai ulang seam tersebut dan tidak membuat sistem modal atau URL parser kedua.

## Goal

Memperluas prototype Responsive Hero Banner agar pengguna dapat mengevaluasi:

1. Grouped dan Independent positioning.
2. Content Order pada Grouped mode.
3. Satu sampai tiga button dalam satu Button Group.
4. Action Type per button: Link, Video Popup, atau Image Popup.
5. Desktop base dengan optional Tablet dan Mobile overrides untuk media dan layout.
6. Image Source dari CKFinder/Media Library atau External URL.
7. Popup video dan image yang dapat diuji langsung dari canvas prototype.

## Non-goals

- Tidak menambahkan widget ke registry produksi.
- Tidak mengubah Vue canvas, Blade renderer, frontend runtime, persistence, route, atau Page Builder v2.0.
- Tidak menekan Save pada editor Page Builder yang aktif.
- Tidak mengunggah file sungguhan ke CKFinder dari prototype; picker direpresentasikan sebagai alur pemilihan media yang jelas.
- Tidak mengunduh, mem-proxy, atau menyalin external image/video ke storage lokal.
- Tidak memberi setiap button posisi X/Y terpisah; button diposisikan sebagai satu Button Group.
- Tidak membuat order konten berbeda per-device.
- Tidak mendukung lebih dari tiga button pada prototype.

## UX Architecture

Panel mengikuti struktur Page Builder v2.3: Content, Style, dan Advanced. Perluasan prototype difokuskan pada Content; Style dan Advanced tetap menjadi preview boundary seperti prototype sekarang.

### Content Behavior

- **Grouped:** Title, Subtitle, dan Button Group berada dalam satu flow container.
- **Independent:** Title, Subtitle, dan Button Group mempunyai anchor, X, Y, width, dan alignment masing-masing.
- Content Order hanya tampil pada Grouped mode.
- Independent mode menyembunyikan Content Order karena urutan visual ditentukan oleh posisi tiap blok.

### Content Order

Content Order berisi tiga blok tetap:

1. Title
2. Subtitle
3. Button Group

Pengguna dapat memindahkan blok menggunakan tombol Move Up dan Move Down. Urutan ini bersifat global dan tidak berubah antar-device. Blok yang disembunyikan tidak dirender tetapi tetap mempertahankan posisinya dalam order agar dapat dipulihkan tanpa kehilangan konfigurasi.

### Button Group

- Minimal satu dan maksimal tiga button.
- Setiap row mempunyai drag handle visual, label button, duplicate, dan remove.
- Add Button disabled ketika jumlah mencapai tiga.
- Button Group mempunyai responsive Direction (`Row` atau `Column`), Alignment, Gap, dan Wrap.
- Dalam Independent mode, semua button bergerak sebagai satu Button Group.

Setiap button memiliki:

```js
{
  id: 'button-1',
  text: 'Watch Video',
  actionType: 'link',
  url: '',
  target: '',
  nofollow: false,
  customAttributes: [],
  videoSource: 'youtube',
  videoUrl: '',
  imageSource: 'ckfinder',
  imageUrl: '',
  imageAlt: '',
}
```

Conditional controls:

- **Link:** URL, Open in New Window, Nofollow, dan Custom Attributes.
- **Video Popup:** Video Source dan Video URL.
- **Image Popup:** Image Source, media selector atau External URL, dan Alt Text.

Changing Action Type tidak langsung menghapus nilai action lain. Nilai disimpan agar pengguna dapat kembali ke tipe sebelumnya tanpa mengetik ulang, tetapi renderer hanya memakai field yang sesuai dengan Action Type aktif.

## Responsive Model

Desktop menjadi base. Tablet dan Mobile memakai optional override dengan inheritance berikut:

```text
Mobile override → Tablet override → Desktop base
Tablet override → Desktop base
```

Setting responsive:

- Anchor, X, Y, Content Width, dan Alignment untuk Grouped content atau independent block.
- Button Group Direction, Alignment, Gap, dan Wrap.
- Hero image source, Object Fit, dan Object Position.

Setting global:

- Title dan Subtitle text.
- Content Order.
- Daftar button dan action masing-masing.
- Modal content source.

Device control harus menampilkan apakah nilai berasal dari override lokal atau inherited. Reset pada Tablet/Mobile menghapus override dan kembali ke inheritance; Reset Desktop mengembalikan default prototype.

## Responsive Media

Setiap device menyediakan **Image Source**:

- `ckfinder`: memakai Media Library/CKFinder flow.
- `url`: menerima direct External URL.

Data media dinormalisasi sebagai:

```js
{
  source: 'ckfinder',
  url: '/assets/mg5gt-hero-desktop.webp',
  alt: 'Yellow MG5 GT in a white studio',
  objectFit: 'cover',
  objectPosition: 'center center',
}
```

Desktop media menjadi base. Tablet dan Mobile boleh kosong untuk memakai fallback. Prototype harus memperlihatkan status seperti `Inherited from Desktop`, `Inherited from Tablet`, atau `Custom override`.

External URL hanya menerima absolute `http://` atau `https://` URL. Protocol-relative URL ditolak. UI menampilkan helper bahwa remote host dapat menolak hotlinking atau menghapus aset. Kesalahan load menampilkan empty/error state tanpa merusak panel.

CKFinder pada prototype tidak membuka CKFinder produksi. Tombol **Choose from Media Library** menampilkan simulated media-choice dialog dengan aset lokal yang sudah tersedia. Ini cukup untuk menguji conditional controls dan state model tanpa menyentuh storage atau session produksi.

## Modal Behavior

Hanya satu shared modal instance digunakan oleh seluruh button.

### Video Popup

- YouTube watch, short, dan embed URLs dinormalisasi menjadi embed URL.
- Vimeo dan Dailymotion memakai embed URL yang telah dinormalisasi.
- Self-hosted HTTP/HTTPS media memakai native `<video controls>`.
- Unknown third-party embed tidak otomatis diizinkan dalam prototype.

### Image Popup

- Menampilkan image dengan `max-width` dan `max-height` terhadap viewport serta `object-fit: contain`.
- Alt text diteruskan ke image modal.

### Shared interaction

- Close button dengan accessible name.
- `Escape` menutup modal.
- Backdrop click menutup modal.
- Focus dipindahkan ke close button ketika modal terbuka dan dikembalikan ke trigger setelah modal ditutup.
- Background tidak dapat discroll selama modal terbuka.
- Video/iframe dilepas ketika modal ditutup sehingga playback berhenti.
- `prefers-reduced-motion` meniadakan modal animation.

## State and Data Flow

Prototype tetap menggunakan React local state dan tidak menambah state library.

```js
{
  mode: 'grouped',
  device: 'desktop',
  contentOrder: ['title', 'subtitle', 'buttons'],
  contentVisibility: { title: true, subtitle: true, buttons: true },
  buttons: [{
    id: 'button-1',
    text: 'Watch Video',
    actionType: 'video_popup',
    videoSource: 'youtube',
    videoUrl: 'https://www.youtube.com/watch?v=h529sg3pEV4',
  }],
  positioning: {
    desktop: {},
    tablet: {},
    mobile: {},
  },
  buttonLayout: {
    desktop: {},
    tablet: {},
    mobile: {},
  },
  media: {
    desktop: {},
    tablet: null,
    mobile: null,
  },
  modal: null,
}
```

Pure helper functions menangani responsive fallback, reorder, button normalization, URL classification, dan video embed normalization. React components hanya mengubah state dan merender hasil helper tersebut.

## Error Handling and Safety

- Minimal satu button harus tersisa jika Button Group visible.
- Add Button berhenti pada tiga item.
- Button tanpa text tetap memiliki fallback accessible name di preview.
- Link dengan URL kosong tidak melakukan navigasi.
- Popup dengan source kosong tidak membuka modal dan menampilkan helper state di panel.
- Hanya URL HTTP/HTTPS atau absolute local path yang diterima untuk media.
- External link menggunakan `noopener noreferrer` ketika membuka tab baru.
- Custom attributes prototype hanya ditampilkan sebagai data; production implementation wajib memakai sanitizer/whitelist v2.3 yang sudah ada.

## Testing Strategy

Pure behavior helpers dikembangkan test-first dengan red-green self-check menggunakan Node `assert`. Setiap helper check harus terlihat gagal karena behavior belum tersedia sebelum implementasi minimal ditambahkan. React wiring baru ditambahkan setelah helper contract lulus; presentation dan focus behavior diverifikasi melalui browser QA.

Self-check mencakup:

1. Content reorder tidak kehilangan atau menggandakan blok.
2. Responsive fallback mengikuti Mobile → Tablet → Desktop.
3. Button normalization mempertahankan 1–3 button.
4. Action Type memilih field yang tepat.
5. YouTube, Vimeo, Dailymotion, dan self-hosted URL classification.
6. URL invalid dan protocol-relative ditolak.

Browser QA mencakup:

1. Grouped Content Order dapat menempatkan Subtitle di atas Title dan flow tetap tidak overlap.
2. Independent positioning tetap bekerja untuk Title, Subtitle, dan Button Group.
3. Menambah, duplicate, remove, dan batas tiga button.
4. Link button tidak dinavigasikan ketika URL kosong.
5. YouTube modal dan image modal terbuka serta menutup melalui close, backdrop, dan Escape.
6. Desktop, Tablet, dan Mobile inheritance serta reset override.
7. CKFinder simulation dan External URL conditional controls.
8. Viewport 1440, 1024, dan 720 tanpa horizontal overflow.
9. Browser console tidak memiliki error atau warning dari prototype.

Design QA diperbarui dengan source truth, full view, focused modal/button states, findings, patches, dan `final result: passed` sebelum handoff.

## Expected Prototype File Scope

- `project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype/src/App.jsx`
- `project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype/src/styles.css`
- `project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype/src/positioning.js`
- `project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype/scripts/self-check.mjs`
- `project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype/design-qa.md`
- Aset QA baru hanya jika diperlukan untuk membuktikan modal dan responsive media states.

File yang sudah ada harus dibackup dengan timestamp sebelum dimodifikasi. Source Page Builder produksi, Graphify output, dan Page Builder saved data tidak masuk scope perubahan.

## Production Mapping Boundary

Jika prototype disetujui untuk implementasi produksi, pekerjaan berikutnya harus memiliki spec dan implementation plan terpisah. Production implementation wajib menyelaraskan:

1. widget definition dan normalization;
2. settings panel;
3. editor canvas;
4. save/persistence payload;
5. Blade frontend renderer;
6. shared frontend runtime;
7. regression tests dan browser QA.

Prototype ini tidak dianggap sebagai bukti bahwa production widget sudah selesai.
